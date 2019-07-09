<?php

/**
 * Description of MPHBBookingCPT
 *
 */
class MPHBBookingCPT extends MPHBCustomPostType {

	const STATUS_CONFIRMED	 = 'confirmed';
	const STATUS_PENDING	 = 'pending';
	const STATUS_CANCELLED	 = 'cancelled';

	const PAYMENT_STATUS_PAID = 'paid';
	const PAYMENT_STATUS_UNPAID = 'unpaid';

	protected $postTypeName	 = 'mphb_booking';
	protected $logTypeName	 = 'mphb_booking_log';
	private $statuses		 = array();

	public function __construct(){
		parent::__construct();
		$this->initStatuses();
		add_action( 'init', array( $this, 'registerStatuses' ) );
	}

	public function addActions(){
		parent::addActions();
		add_action( 'save_post', array(	$this, 'fireUpdatePost' ), 12, 3 );
		add_action( 'admin_menu', array( $this, 'customizeMetaBoxes' ) );
		add_action( 'admin_menu', array( $this, 'addSubMenus' ), 11 );
		add_action( 'post_row_actions', array( $this, 'editRowActions' ) );
		add_action( 'transition_post_status', array( $this, 'transitionBookingStatus' ), 10, 3 );
		add_action( 'restrict_manage_posts', array( $this, 'editPostsFilters' ) );

		// Bulk actions
		add_filter( 'bulk_actions-edit-' . $this->postTypeName, array( $this, 'filterBulkActions' ) );
		add_action( 'admin_notices', array( $this, 'bulkAdminNotices' ) );
		add_action( 'admin_footer', array( $this, 'bulkAdminScript' ), 10 );
		add_action( 'load-edit.php', array( $this, 'bulkAction' ) );

		add_filter( 'request', array( $this, 'filterCustomOrderBy' ) );
		$this->extendBookingListingSearch();

		// Hide Logs
		$this->addHideLogsActions();
		add_action( 'mphb_booking_before_get_logs', array( $this, 'removeHideLogsActions' ) );
		add_action( 'mphb_booking_after_get_logs', array( $this, 'addHideLogsActions' ) );
		add_filter( 'comment_feed_where', array( $this, 'hideLogsFromFeed' ), 10, 2 );
		add_filter( 'wp_count_comments', array( $this, 'fixCommentsCount' ), 10, 2 );

		// Send Mails
		add_action( 'mphb_create_booking_by_user', array( $this, 'sendMails' ) );
	}

	public function addSubMenus(){
		add_submenu_page( MPHB()->getMainMenuSlug(), get_post_type_object( $this->postTypeName )->labels->add_new_item, get_post_type_object( $this->postTypeName )->labels->add_new, 'edit_posts', 'post-new.php?post_type=' . $this->postTypeName );
	}

	public function extendBookingListingSearch(){
		if ( is_admin() ) {
			add_action( 'parse_query', array( $this, 'setQueryVarsSearchEmail' ) );
			add_filter( 'posts_join', array( $this, 'extendSearchPostsJoin' ), 10, 2 );
			add_filter( 'posts_search', array( $this, 'extendPostsSearch' ), 10, 2 );
			add_filter( 'posts_search_orderby', array( $this, 'extendPostsSearchOrderBy' ), 10, 2 );
		}
	}

	private function initStatuses(){
		$this->statuses[self::STATUS_PENDING] = array(
			'args'		 => array(
				'label'						 => _x( 'Pending', 'Booking status', 'motopress-hotel-booking' ),
				'public'					 => true,
				'exclude_from_search'		 => false,
				'show_in_admin_all_list'	 => true,
				'show_in_admin_status_list'	 => true,
				'label_count'				 => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>', 'motopress-hotel-booking' )
			),
			'lock_room'	 => true
		);
		$this->statuses[self::STATUS_CONFIRMED]	 = array(
			'args'		 => array(
				'label'						 => _x( 'Confirmed', 'Booking status', 'motopress-hotel-booking' ),
				'public'					 => true,
				'exclude_from_search'		 => false,
				'show_in_admin_all_list'	 => true,
				'show_in_admin_status_list'	 => true,
				'label_count'				 => _n_noop( 'Confirmed <span class="count">(%s)</span>', 'Confirmed <span class="count">(%s)</span>', 'motopress-hotel-booking' )
			),
			'lock_room'	 => true
		);
		$this->statuses[self::STATUS_CANCELLED]	 = array(
			'args'		 => array(
				'label'						 => _x( 'Cancelled', 'Booking status', 'motopress-hotel-booking' ),
				'public'					 => true,
				'exclude_from_search'		 => false,
				'show_in_admin_all_list'	 => true,
				'show_in_admin_status_list'	 => true,
				'label_count'				 => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'motopress-hotel-booking' )
			),
			'lock_room'	 => false
		);

		// Cron unreserve statuses
//		$this->statuses['mphb-notfullfillment'] = array(
//			'args' => array(
//				'label'                     => _x( 'Awaiting Fulfilment', 'Booking status', 'motopress-hotel-booking' ),
//				'public'                    => true,
//				'exclude_from_search'       => false,
//				'show_in_admin_all_list'    => true,
//				'show_in_admin_status_list' => true,
//				'label_count'               => _n_noop( 'Awaiting Fulfilment <span class="count">(%s)</span>', 'Awaiting Fulfilment <span class="count">(%s)</span>', 'motopress-hotel-booking' )
//			),
//			'lock_room' => true
//		);
//		$this->statuses['mphb-failed'] = array(
//			'args' => array(
//				'label'                     => _x( 'Failed', 'Booking status', 'motopress-hotel-booking' ),
//				'public'                    => true,
//				'exclude_from_search'       => false,
//				'show_in_admin_all_list'    => true,
//				'show_in_admin_status_list' => true,
//				'label_count'               => _n_noop( 'Failed <span class="count">(%s)</span>', 'Failed <span class="count">(%s)</span>', 'motopress-hotel-booking' )
//			),
//			'lock_room' => false
//		);
		// Payment statuses
//		$this->statuses['mphb-refunded'] = array(
//			'args' => array(
//				'label'                     => _x( 'Refunded', 'Booking status', 'motopress-hotel-booking' ),
//				'public'                    => true,
//				'exclude_from_search'       => false,
//				'show_in_admin_all_list'    => true,
//				'show_in_admin_status_list' => true,
//				'label_count'               => _n_noop( 'Refunded <span class="count">(%s)</span>', 'Refunded <span class="count">(%s)</span>', 'motopress-hotel-booking' )
//			),
//			'lock_room' => false
//		);
//		$this->statuses['mphb-pending-payment'] = array(
//			'args' => array(
//				'label'                     => _x( 'Pending Payment', 'Booking status', 'motopress-hotel-booking' ),
//				'public'                    => true,
//				'exclude_from_search'       => false,
//				'show_in_admin_all_list'    => true,
//				'show_in_admin_status_list' => true,
//				'label_count'               => _n_noop( 'Pending Payment <span class="count">(%s)</span>', 'Pending Payment <span class="count">(%s)</span>', 'motopress-hotel-booking' )
//			),
//			'lock_room' => true
//		);
	}

	/**
	 *
	 * @return array
	 */
	public function getStatuses(){
		return $this->statuses;
	}

	/**
	 *
	 * @return array
	 */
	public function getPaymentStatuses(){
		return array(
			self::PAYMENT_STATUS_UNPAID => __( 'Unpaid', 'motopress-hotel-booking' ),
			self::PAYMENT_STATUS_PAID	 => __( 'Paid', 'motopress-hotel-booking' )
		);
	}

	public function registerStatuses(){
		foreach ( $this->statuses as $statusName => $details ) {
			register_post_status( $statusName, $details['args'] );
		}
	}

	public function editRowActions( $actions ){
		// Prevent Quick Edit
		if ( $this->isAdminListingPage() ) {
			if ( isset( $actions['inline hide-if-no-js'] ) ) {
				unset( $actions['inline hide-if-no-js'] );
			}
		}

		return $actions;
	}

	public function setManagePageCustomColumnsSortable( $columns ){

		$columns['id']				 = 'ID';
		$columns['room']			 = 'mphb_room_id';
		$columns['check_in_date']	 = 'mphb_check_in_date';
		$columns['check_out_date']	 = 'mphb_check_out_date';

		return $columns;
	}

	public function setManagePageCustomColumns( $columns ){
		if ( isset( $columns['title'] ) ) {
			unset( $columns['title'] );
		}

		$customColumns	 = array(
			'id'				 => __( 'ID', 'motopress-hotel-booking' ),
			'status'			 => __( 'Status', 'motopress-hotel-booking' ),
			'room'				 => __( 'Room', 'motopress-hotel-booking' ),
			'rate'				 => __( 'Rate Variation', 'motopress-hotel-booking' ),
			'check_in_out_date'	 => __( 'Check-In / Check-out', 'motopress-hotel-booking' ),
			'guests'			 => __( 'Guests', 'motopress-hotel-booking' ),
			'services'			 => __( 'Services', ',motopress-hotel-booking' ),
			'customer_info'		 => __( 'Customer Information', 'motopress-hotel-booking' ),
			'price'				 => __( 'Price', 'motopress-hotel-booking' ),
			'mphb_date'			 => __( 'Date', 'motopress-hotel-booking' )
		);
		$offset			 = array_search( 'date', array_keys( $columns ) ); // Set custom columns position before "DATE" column
		$columns		 = array_slice( $columns, 0, $offset, true ) + $customColumns + array_slice( $columns, $offset, count( $columns ) - 1, true );

		unset( $columns['date'] );
		return $columns;
	}

	public function renderManagePageCustomColumns( $column, $postId ){
		$booking = new MPHBBooking( $postId );
		switch ( $column ) {
			case 'id':
				printf( '<a href="%s"><strong>#%s</strong></a>', get_edit_post_link( $postId ), $postId );
				break;
			case 'status':
				echo mphb_get_status_label( $booking->getStatus() );
				break;
			case 'room' :
				$room	 = $booking->getRoom();
				echo (!is_null( $room )) ? '<a href="' . $room->getEditLink() . '">' . $room->getTitle() . '</a>' : '<span aria-hidden="true">&#8212;</span>';
				break;
			case 'rate':
				$rate	 = $booking->getRoomRate();
				echo (!is_null( $rate )) ? '<span title="' . esc_attr( $rate->getDescription() ) . '">' . $rate->getTitle() . '</span>' : '<span aria-hidden="true">&#8212;</span>';
				break;
			case 'check_in_out_date' :

				$checkInDate	 = $booking->getCheckInDate();
				$checkOutDate	 = $booking->getCheckOutDate();

				echo (!is_null( $checkInDate )) ? MPHBUtils::convertDateToWPFront( $checkInDate ) : '<span aria-hidden="true">&#8212;</span>';
				echo '<br/>';
				echo (!is_null( $checkOutDate )) ? MPHBUtils::convertDateToWPFront( $checkOutDate ) : '<span aria-hidden="true">&#8212;</span>';
				echo '<br/>';

				if ( !is_null( $checkInDate ) && !is_null( $checkOutDate ) ) {
					printf( __( '%s nights', 'motopress-hotel-booking' ), MPHBUtils::calcNights( $checkInDate, $checkOutDate ) );
				}

				break;
			case 'guests':
				_e( 'Adults: ', 'motopress-hotel-booking' );
				echo $booking->getAdults();
				_e( ', Childs: ', 'motopress-hotel-booking' );
				echo $booking->getChilds();
				break;
			case 'services':
				MPHBBookingView::renderServicesList($booking);
				break;
			case 'customer_info':
				$customer = $booking->getCustomer();
				?>
				<p>
					<?php echo esc_html( $customer->getFirstName() . ' ' . $customer->getLastName() ); ?>
					<br>
					<a href="mailto:<?php echo esc_html( $customer->getEmail() ); ?>">
						<?php echo esc_html( $customer->getEmail() ); ?>
					</a>
					<br>
					<a href="tel:<?php echo esc_html( $customer->getPhone() ); ?>">
						<?php echo esc_html( $customer->getPhone() ); ?>
					</a>
					<?php if ( $booking->getNote() && 1 == 2 ) { ?>
						<br>
						<span><?php _e( 'Note: ', 'motopress-hotel-booking' ); ?></span><strong><?php echo esc_html( $booking->getNote() ); ?></strong>
					<?php } ?>
				</p>
				<?php
				break;
			case 'price':
				echo MPHBBookingView::renderTotalPriceHTML($booking);
				echo '<br/>';
				echo $booking->getPaymentStatusLabel();
				break;
			case 'mphb_date':
				echo get_the_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $postId );
				break;
		}
	}

	public function editPostsFilters(){
		global $typenow;
		if ( $typenow === $this->postTypeName ) {
			$email = isset( $_GET['mphb_email'] ) ? $_GET['mphb_email'] : '';
			echo '<input type="text" name="mphb_email" placeholder="' . esc_attr__( 'Email', 'motopress-hotel-booking' ) . '" value="' . esc_attr( $email ) . '" />';
		}
	}

	public function filterBulkActions( $bulkActions ){
		if ( isset( $bulkActions['edit'] ) ) {
			unset( $bulkActions['edit'] );
		}
		return $bulkActions;
	}

	/**
	 * Add extra bulk action options to change booking status.
	 *
	 * Using Javascript until WordPress core fixes: http://core.trac.wordpress.org/ticket/16031.
	 */
	public function bulkAdminScript(){
		if ( $this->isAdminListingPage() ) {
			$optionText = __('Set to %s', 'motopress-hotel-booking');
			?>
			<script type="text/javascript">
				(function($) {
					$(function() {
						var toPendingOption = $('<option />', {
							value: 'set-status-<?php echo self::STATUS_PENDING; ?>',
							text: '<?php printf($optionText, mphb_get_status_label( self::STATUS_PENDING ) ); ?>'
						});
						var toConfirmedOption = $('<option />', {
							value: 'set-status-<?php echo self::STATUS_CONFIRMED; ?>',
							text: '<?php printf($optionText, mphb_get_status_label( self::STATUS_CONFIRMED ) ); ?>'
						});
						var toCanceledOption = $('<option />', {
							value: 'set-status-<?php echo self::STATUS_CANCELLED; ?>',
							text: '<?php printf($optionText, mphb_get_status_label( self::STATUS_CANCELLED ) ); ?>'
						});

						$('select[name="action"]').append(toPendingOption.clone(), toConfirmedOption.clone(), toCanceledOption.clone());
						$('select[name="action2"]').append(toPendingOption.clone(), toConfirmedOption.clone(), toCanceledOption.clone());
					});
				})(jQuery)
			</script>
			<?php
		}
	}

	/**
	 * Process the new bulk actions for changing booking status.
	 */
	public function bulkAction(){
		$wp_list_table	 = _get_list_table( 'WP_Posts_List_Table' );
		$action			 = $wp_list_table->current_action();

		if ( strpos( $action, 'set-status-' ) === false ) {
			return;
		}

		$allowedStatuses = $this->getStatuses();

		$newStatus		 = substr( $action, 11 );
		$reportAction	 = 'setted-status-' . $newStatus;

		if ( !isset( $allowedStatuses[$newStatus] ) ) {
			return;
		}

		check_admin_referer( 'bulk-posts' );

		$postIDs = isset( $_REQUEST['post'] ) ? array_map( 'absint', (array) $_REQUEST['post'] ) : array();

		if ( empty( $postIDs ) ) {
			return;
		}

		$changed = 0;
		foreach ( $postIDs as $postId ) {
			$booking = new MPHBBooking( $postId );
			$booking->setStatus( $newStatus );
			if ( $booking->save() ) {
				do_action( 'mphb_update_booking_by_admin', $booking );
				$changed++;
			}
		}

		$queryArgs	 = array(
			'post_type'		 => $this->getPostType(),
			$reportAction	 => true,
			'changed'		 => $changed,
			'ids'			 => join( ',', $postIDs ),
			'paged'			 => $wp_list_table->get_pagenum()
		);
		$sendback	 = add_query_arg( $queryArgs, admin_url( 'edit.php' ) );

		if ( isset( $_GET['post_status'] ) ) {
			$sendback = add_query_arg( 'post_status', sanitize_text_field( $_GET['post_status'] ), $sendback );
		}

		wp_redirect( esc_url_raw( $sendback ) );
		exit();
	}

	/**
	 * Show message that booking status changed for number of bookings.
	 */
	public function bulkAdminNotices(){
		if ( $this->isAdminListingPage() ) {
			// Check if any status changes happened
			foreach ( $this->getStatuses() as $slug => $details ) {

				if ( isset( $_REQUEST['setted-status-' . $slug] ) ) {

					$number	 = isset( $_REQUEST['changed'] ) ? absint( $_REQUEST['changed'] ) : 0;
					$message = sprintf( _n( 'Booking status changed.', '%s booking statuses changed.', $number, 'motopress-hotel-booking' ), number_format_i18n( $number ) );
					echo '<div class="updated"><p>' . $message . '</p></div>';

					break;
				}
			}
		}
	}

	public function transitionBookingStatus( $newStatus, $oldStatus, $post ){
		if ( $post->post_type === $this->postTypeName && $newStatus !== $oldStatus ) {

			// Prevent logging status change while importing
			if ( MPHB()->getImporter()->isImportProcess() ) {
				return;
			}

			$booking = new MPHBBooking( $post );
			$booking->addLog( sprintf( __( 'Status changed from %s to %s.', 'motopress-hotel-booking' ), mphb_get_status_label( $oldStatus ), mphb_get_status_label( $newStatus ) ) );
			add_action( 'mphb_update_booking_by_admin', array( $this, 'sendMails' ) );
		}
	}

	/**
	 *
	 * @param type $postId
	 * @param type $post
	 * @param type $update
	 * @return boolean
	 */
	public function fireUpdatePost( $postId, $post, $update ){

		if ( $post->post_type != $this->getPostType() ) {
			return false;
		}

		if ( !$this->isAdminSingleEditPage() ) {
			return false;
		}

		if ( !$this->isCanSave( $postId ) ) {
			return false;
		}

		$booking = new MPHBBooking( $postId );
		do_action( 'mphb_update_booking_by_admin', $booking );

	}

	public function retrieveRequestValues(){
		$values = array();
		foreach ( $this->fieldGroups as $group ) {
			$values = array_merge( $values, $group->retrieveRequestValues() );
		}
	}

	/**
	 *
	 * @return array
	 */
	public function getLockedRoomStatuses(){
		return array_keys( array_filter( $this->statuses, array( $this, 'filterLockedRoomStatuses' ) ) );
	}

	/**
	 *
	 * @return array
	 */
	public function getBookedRoomStatuses(){
		return (array) self::STATUS_CONFIRMED;
	}

	/**
	 *
	 * @return array
	 */
	public function getPendingRoomStatuses(){
		return (array) self::STATUS_PENDING;
	}

	/**
	 *
	 * @return array
	 */
	public function getAvailableRoomStatuses(){
		return array_merge( 'trash', array_diff( array_keys( $this->statuses ), $this->getLockedRoomStatuses() ) );
	}

	public function filterLockedRoomStatuses( $status ){
		return isset( $status['lock_room'] ) && $status['lock_room'];
	}

	/**
	 *
	 * @param MPHBBooking $booking
	 */
	public function sendMails( $booking ){
		switch ( $booking->getStatus() ) {
			case self::STATUS_PENDING:
				MPHB()->getEmails()->getAdminPending()->trigger( $booking );
				MPHB()->getEmails()->getCustomerPending()->trigger( $booking );
				break;
			case self::STATUS_CONFIRMED:
				MPHB()->getEmails()->getCustomerApproved()->trigger( $booking );
				break;
			case self::STATUS_CANCELLED:
				MPHB()->getEmails()->getCustomerCancelled()->trigger( $booking );
				break;
		}

		remove_action( 'mphb_update_booking_by_admin', array( $this,'sendMails' ) );
	}

	public function register(){
		register_post_type( $this->postTypeName, array(
			'labels'				 => array(
				'name'					 => __( 'Booking', 'motopress-hotel-booking' ),
				'singular_name'			 => __( 'Booking', 'motopress-hotel-booking' ),
				'add_new'				 => _x( 'Add New', 'Add New Booking', 'motopress-hotel-booking' ),
				'add_new_item'			 => __( 'Add New Booking', 'motopress-hotel-booking' ),
				'edit_item'				 => __( 'Edit Booking', 'motopress-hotel-booking' ),
				'new_item'				 => __( 'New Booking', 'motopress-hotel-booking' ),
				'view_item'				 => __( 'View Booking', 'motopress-hotel-booking' ),
				'search_items'			 => __( 'Search Booking', 'motopress-hotel-booking' ),
				'not_found'				 => __( 'No bookings found', 'motopress-hotel-booking' ),
				'not_found_in_trash'	 => __( 'No bookings found in Trash', 'motopress-hotel-booking' ),
				'all_items'				 => __( 'All Bookings', 'motopress-hotel-booking' ),
				'insert_into_item'		 => __( 'Insert into booking description', 'motopress-hotel-booking' ),
				'uploaded_to_this_item'	 => __( 'Uploaded to this booking', 'motopress-hotel-booking' )
			),
			'map_meta_cap'			 => true,
			'public'				 => false,
			'exclude_from_search'	 => true,
			'publicly_queryable'	 => false,
			'show_ui'				 => true,
			'show_in_menu'			 => false,
			'query_var'				 => false,
			'capability_type'		 => 'post',
			'has_archive'			 => false,
			'hierarchical'			 => false,
			'show_in_menu'			 => MPHB()->getMainMenuSlug(),
			'supports'				 => false,
			'register_meta_box_cb'	 => array(
				$this,
				'registerMetaBoxes' ),
		) );
	}

	public function initMetaBoxes(){
		$roomGroup	 = new MPHBMetaBoxGroup( 'mphb_room', __( 'Room Information', 'motopress-hotel-booking' ), $this->postTypeName );
		$roomIdField = MPHBFieldFactory::create(
				'mphb_room_id', array(
				'type'		 => 'select',
				'label'		 => __( 'Room ID', 'motopress-hotel-booking' ),
				'list'		 => array(
				'' => __( '— Select —', 'motopress-hotel-booking' ) ) + MPHB()->getRoomCPT()->getRoomsTitles(),
				'default'	 => '',
				'required'	 => true
				)
		);
		$roomGroup->addField( $roomIdField );

		$roomIdField = MPHBFieldFactory::create(
				'mphb_room_rate_id', array(
				'type'				 => 'dynamic-select',
				'dependency_input'	 => 'mphb_room_id',
				'ajax_action'		 => 'mphb_get_rates_for_room',
				'label'				 => __( 'Room Rate Variation', 'motopress-hotel-booking' ),
				'list_callback'		 => array(
					'MPHBRoom',
					'getRatesIdTitleList' ),
				'default'			 => '',
				'required'			 => true
				)
		);
		$roomGroup->addField( $roomIdField );

		$checkInDateField = MPHBFieldFactory::create(
				'mphb_check_in_date', array(
				'type'		 => 'datepicker',
				'label'		 => __( 'Check-in date', 'motopress-hotel-booking' ),
				'required'	 => true,
				'readonly'	 => false
				)
		);
		$roomGroup->addField( $checkInDateField );

		$checkOutDateField = MPHBFieldFactory::create(
				'mphb_check_out_date', array(
				'type'		 => 'datepicker',
				'label'		 => __( 'Check-out date', 'motopress-hotel-booking' ),
				'required'	 => true,
				'readonly'	 => false
				)
		);
		$roomGroup->addField( $checkOutDateField );

		$adultsField = MPHBFieldFactory::create(
				'mphb_adults', array(
				'type'		 => 'select',
				'label'		 => __( 'Adults', 'motopress-hotel-booking' ),
				'list'		 => MPHB()->getSettings()->getAdultsList(),
				'required'	 => true
				)
		);
		$roomGroup->addField( $adultsField );

		$childsField = MPHBFieldFactory::create(
				'mphb_childs', array(
				'type'	 => 'select',
				'label'	 => __( 'Childs', 'motopress-hotel-booking' ),
				'list'	 => MPHB()->getSettings()->getChildsList()
				)
		);
		$roomGroup->addField( $childsField );

		$servicesField = MPHBFieldFactory::create(
				'mphb_services', array(
				'label'		 => __( 'Services', 'motopress-hotel-booking' ),
				'type'		 => 'complex',
				'fields'	 => array(
					MPHBFieldFactory::create( 'id', array(
						'type'		 => 'select',
						'label'		 => __( 'Service', 'motopress-hotel-booking' ),
						'list'		 => MPHB()->getServiceCPT()->getServicesTitles(),
						'required'	 => true
					) ),
					MPHBFieldFactory::create( 'count', array(
						'type'		 => 'number',
						'label'		 => __( 'Count', 'motopress-hotel-booking' ),
						'default'	 => 1,
						'min'		 => 1,
						'step'		 => 1,
						'size'		 => 'small',
						'required'	 => true
					) )
				),
				'add_label'	 => __( 'Add service', 'motopress-hotel-booking' )
				)
		);
		$roomGroup->addField( $servicesField );

		$customerGroup = new MPHBMetaBoxGroup( 'mphb_customer', __( 'Customer Information', 'motopress-hotel-booking' ), $this->postTypeName );

		$nameField = MPHBFieldFactory::create(
				'mphb_first_name', array(
				'type'		 => 'text',
				'label'		 => __( 'First Name', 'motopress-hotel-booking' ),
				'default'	 => '',
				'required'	 => true
				)
		);
		$customerGroup->addField( $nameField );

		$lastNameField = MPHBFieldFactory::create(
				'mphb_last_name', array(
				'type'		 => 'text',
				'label'		 => __( 'Last Name', 'motopress-hotel-booking' ),
				'default'	 => ''
				)
		);
		$customerGroup->addField( $lastNameField );

		$emailField = MPHBFieldFactory::create(
				'mphb_email', array(
				'type'		 => 'email',
				'label'		 => __( 'Email', 'motopress-hotel-booking' ),
				'default'	 => '',
				'required'	 => true
				)
		);
		$customerGroup->addField( $emailField );

		$phoneField = MPHBFieldFactory::create(
				'mphb_phone', array(
				'type'		 => 'text',
				'label'		 => __( 'Phone', 'motopress-hotel-booking' ),
				'default'	 => '',
				'required'	 => true
				)
		);
		$customerGroup->addField( $phoneField );

		$noteField = MPHBFieldFactory::create(
				'mphb_note', array(
				'type'	 => 'textarea',
				'rows'	 => 8,
				'label'	 => __( 'Customer Note', 'motopress-hotel-booking' ),
				)
		);
		$customerGroup->addField( $noteField );

		$miscGroup		 = new MPHBMetaBoxGroup( 'mphb_other', __( 'Additional Information', 'motopress-hotel-booking' ), $this->postTypeName );
		$totalPriceField = MPHBFieldFactory::create(
				'mphb_total_price', array(
				'type'	 => 'total-price',
				'size'	 => 'long-price',
				'label'	 => __( 'Total Booking Price', 'motopress-hotel-booking' )
				)
		);
		$miscGroup->addField( $totalPriceField );

		$totalPriceField = MPHBFieldFactory::create(
				'mphb_payment_status', array(
				'type'	 => 'select',
				'list'	 => $this->getPaymentStatuses(),
				'label'	 => __( 'Payment Status', 'motopress-hotel-booking' )
				)
		);
		$miscGroup->addField( $totalPriceField );

		$this->fieldGroups = array(
			$roomGroup,
			$customerGroup,
			$miscGroup );
	}

	public function customizeMetaBoxes(){
		remove_meta_box( 'submitdiv', $this->postTypeName, 'side' );
		remove_meta_box( 'commentsdiv', $this->postTypeName, 'normal' );
		remove_meta_box( 'commentstatusdiv', $this->postTypeName, 'normal' );

		add_meta_box( 'submitdiv', __( 'Update Booking', 'motopress-hotel-booking' ), array(
			$this,
			'renderSubmitMetaBox' ), $this->postTypeName, 'side' );
		add_meta_box( 'logs', __( 'Logs', 'motopress-hotel-booking' ), array(
			$this,
			'renderLogMetaBox' ), $this->postTypeName, 'side' );
	}

	public function renderSubmitMetaBox( $post, $metabox ){
		$postTypeObject	 = get_post_type_object( $this->postTypeName );
		$can_publish	 = current_user_can( $postTypeObject->cap->publish_posts );
		$postStatus		 = get_post_status( $post->ID );
		?>
		<div class="submitbox" id="submitpost">
			<div id="minor-publishing">
				<div id="minor-publishing-actions">
				</div>
				<div id="misc-publishing-actions">
					<div class="misc-pub-section">
						<label for="mphb_post_status">Status:</label>
						<select name="post_status" id="mphb_post_status">
							<?php foreach ( $this->statuses as $statusName => $statusDetails ) { ?>
								<option value="<?php echo $statusName; ?>" <?php selected( $statusName, $postStatus ); ?>><?php echo $statusDetails['args']['label']; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="misc-pub-section">
						<span><?php _e( 'Created on:', 'motopress-hotel-booking' ); ?></span> <strong><?php echo date_i18n( get_option( 'date_format' ) . ' @ ' . get_option( 'time_format' ), strtotime( $post->post_date ) ); ?></strong>
					</div>
				</div>
			</div>
			<div id="major-publishing-actions">
				<div id="delete-action">
					<?php
					if ( current_user_can( "delete_post", $post->ID ) ) {
						if ( !EMPTY_TRASH_DAYS ) {
							$delete_text = __( 'Delete Permanently', 'motopress-hotel-booking' );
						} else {
							$delete_text = __( 'Move to Trash', 'motopress-hotel-booking' );
						}
						?>
						<a class="submitdelete deletion" href="<?php echo get_delete_post_link( $post->ID ); ?>"><?php echo $delete_text; ?></a>
					<?php } ?>
				</div>
				<div id="publishing-action">
					<span class="spinner"></span>
					<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Update Booking', 'motopress-hotel-booking' ); ?>" />
					<input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="<?php in_array( $post->post_status, array( 'new', 'auto-draft' ) ) ? esc_attr_e( 'Create Booking', 'motopress-hotel-booking' ) : esc_attr_e( 'Update Booking', 'motopress-hotel-booking' ); ?>" />
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<?php
	}

	public function renderLogMetaBox( $post, $metabox ){
		$booking = new MPHBBooking( $post );
		foreach ( array_reverse( $booking->getLogs() ) as $log ) {
			?>
			<strong> <?php _e( 'Date:', 'motopress-hotel-booking' ); ?></strong>
			<span>
				<?php
				comment_date( get_option( 'date_format' ) . ' @ ' . get_option( 'time_format' ), $log->comment_ID );
				?>
			</span>
			<br/>
			<strong><?php _e( 'Author:', 'motopress-hotel-booking' ); ?></strong>
			<?php
			if ( !empty( $log->user_id ) ) {
				$userInfo	 = get_userdata( $log->user_id );
				$authorName	 = sprintf( '<a target="_blank" href="%s">%s</a>', $userInfo->user_url, $userInfo->display_name );
			} else {
				$authorName = '<i>' . __( 'Auto', 'motopress-hotel-booking' ) . '</i>';
			}
			?>
			<span><?php echo $authorName; ?></span>
			<br/>
			<strong><?php _e( 'Message:', 'motopress-hotel-booking' ); ?></strong>
			<span> <?php echo $log->comment_content; ?></span>
			<hr/>
			<?php
		}
	}

	public function setQueryVarsSearchEmail( $query ){
		if ( $this->isAdminListingPage() ) {
			if ( isset( $_GET['mphb_email'] ) && $_GET['mphb_email'] != '' ) {
				$query->query_vars['meta_key']		 = 'mphb_email';
				$query->query_vars['meta_value']	 = sanitize_text_field( $_GET['mphb_email'] );
				$query->query_vars['meta_compare']	 = 'LIKE';
			}
		}
	}

	public function extendSearchPostsJoin( $join, $wp_query ){
		global $wpdb;
		if ( $this->isAdminListingPage() && !empty( $wp_query->query['s'] ) ) {
			for ( $i = 0; $i < $wp_query->query_vars['search_terms_count']; $i++ ) {
				$join .= " LEFT JOIN $wpdb->postmeta AS mphb_postmeta_{$i} ON $wpdb->posts.ID = mphb_postmeta_{$i}.post_id ";
			}
		}
		return $join;
	}

	public function extendPostsSearch( $where, $wp_query ){
		global $wpdb;

		if ( $this->isAdminListingPage() && !empty( $wp_query->query['s'] ) ) {

			preg_match( '/Booking #(?<id>[\d]*)/', trim( $wp_query->query['s'] ), $booking );

			if ( isset( $booking['id'] ) && is_numeric( $booking['id'] ) ) {
				$where = $wpdb->prepare( " AND ($wpdb->posts.ID = %d)", absint( $booking['id'] ) );
				unset( $wp_query->query['s'] );
			} else {
				$searchFields = array(
					'mphb_email',
					'mphb_phone',
					'mphb_first_name',
					'mphb_last_name'
				);

				$extendedSearchStr	 = '';
				$n					 = !empty( $q['exact'] ) ? '' : '%';
				$searchand			 = '';
				foreach ( $wp_query->query_vars['search_terms'] as $index => $term ) {
					// Terms prefixed with '-' should be excluded.
					$include = '-' !== substr( $term, 0, 1 );
					if ( $include ) {
						$like_op	 = 'LIKE';
						$andor_op	 = 'OR';
					} else {
						$like_op	 = 'NOT LIKE';
						$andor_op	 = 'AND';
						$term		 = substr( $term, 1 );
					}

					$like			 = $n . $wpdb->esc_like( $term ) . $n;
					$fieldSearches	 = array();
					foreach ( $searchFields as $field ) {
						$fieldSearches[] = $wpdb->prepare( "( mphb_postmeta_{$index}.meta_key = %s AND CAST( mphb_postmeta_{$index}.meta_value as CHAR ) {$like_op} %s )", $field, $like );
					}

					$fieldSearchesStr	 = join( ' ' . $andor_op . ' ', $fieldSearches );
					$extendedSearchStr .= "{$searchand} ( {$fieldSearchesStr} )";
					$searchand			 = ' AND ';
				}

				if ( !empty( $extendedSearchStr ) ) {
					$extendedSearchStr = " AND ({$extendedSearchStr}) ";
				}

				$where = $extendedSearchStr;
			}
		}

		return $where;
	}

	public function extendPostsSearchOrderBy( $orderBy, $wp_query ){
		// Prevent OrderBy Search terms
		return '';
	}

	public function filterCustomOrderBy( $vars ){
		if ( $this->isAdminListingPage() && isset( $vars['orderby'] ) ) {
			switch ( $vars['orderby'] ) {
				case 'mphb_check_in_date':
					$vars	 = array_merge( $vars, array(
						'meta_key'	 => 'mphb_check_in_date',
						'orderby'	 => 'meta_value',
						'meta_type'	 => 'DATE'
						) );
					break;
				case 'mphb_check_out_date':
					$vars	 = array_merge( $vars, array(
						'meta_key'	 => 'mphb_check_out_date',
						'orderby'	 => 'meta_value',
						'meta_type'	 => 'DATE'
						) );
					break;
				case 'mphb_room_id':
					$vars	 = array_merge( $vars, array(
						'meta_key'	 => '',
						'orderby'	 => 'mphb_room_id'
						) );
					break;
			}
		}
		return $vars;
	}

	public function addHideLogsActions(){
		add_action( 'pre_get_comments', array( $this, 'hideLogsFromComments' ), 10 );
		add_filter( 'comments_clauses', array( $this, 'hideLogsFromComments_pre41' ), 10, 2 );
	}

	public function removeHideLogsActions(){
		remove_action( 'pre_get_comments', array( $this, 'hideLogsFromComments' ), 10 );
		remove_filter( 'comments_clauses', array( $this, 'hideLogsFromComments_pre41' ), 10, 2 );
	}

	/**
	 * Exclude logs from comments
	 *
	 * @param WP_Comment_Query $query
	 */
	function hideLogsFromComments( $query ){
		global $wp_version;

		if ( version_compare( floatval( $wp_version ), '4.1', '>=' ) ) {
			$types = isset( $query->query_vars['type__not_in'] ) ? $query->query_vars['type__not_in'] : array();
			if ( !is_array( $types ) ) {
				$types = array( $types );
			}
			$types[] = $this->logTypeName;

			$query->query_vars['type__not_in']	 = $types;
		}
	}

	/**
	 * Exclude logs from comments
	 *
	 * @param array $clauses Comment clauses for comment query
	 * @param WP_Comment_Query $wp_comment_query
	 * @return array $clauses Updated comment clauses
	 */
	function hideLogsFromComments_pre41( $clauses, $wp_comment_query ){
		global $wp_version;
		if ( version_compare( floatval( $wp_version ), '4.1', '<' ) ) {
			$clauses['where'] .= sprintf( ' AND comment_type != "%s"', $this->logTypeName );
		}
		return $clauses;
	}

	/**
	 * Exclude logs from comment feeds
	 *
	 * @param array $where
	 * @param WP_Comment_Query $wp_comment_query
	 * @return array $where
	 */
	function hideLogsFromFeed( $where, $wp_comment_query ){
		global $wpdb;

		$where .= $wpdb->prepare( " AND comment_type != %s", $this->postTypeName );
		return $where;
	}

	/**
	 * Remove logs from the wp_count_comments function
	 *
	 * @param array $stats
	 * @param int $postId Post ID
	 * @return array Array of comment counts
	 */
	function fixCommentsCount( $stats, $postId ){
		global $wpdb, $pagenow;

//		if( 'index.php' === $pagenow || 'edit-comments.php' === $pagenow ) {
		if ( 0 === $postId ) {

			$postId = (int) $postId;

			$stats = wp_cache_get( "comments-{$postId}", 'counts' );

			if ( $stats === false ) {

				$where = sprintf( 'WHERE comment_type != "%s"', $this->logTypeName );

//				if ( $postId > 0 ) {
//					$where .= $wpdb->prepare( " AND comment_post_ID = %d", $postId );
//				}

				$count = $wpdb->get_results( "SELECT comment_approved, COUNT( * ) AS total FROM {$wpdb->comments} {$where} GROUP BY comment_approved", ARRAY_A );

				$stats = array(
					'approved'		 => 0,
					'moderated'		 => 0,
					'spam'			 => 0,
					'trash'			 => 0,
					'post-trashed'	 => 0,
					'total_comments' => 0,
					'all'			 => 0
				);

				foreach ( (array) $count as $row ) {
					switch ( $row['comment_approved'] ) {
						case 'trash':
							$stats['trash']			 = $row['total'];
							break;
						case 'post-trashed':
							$stats['post-trashed']	 = $row['total'];
							break;
						case 'spam':
							$stats['spam']			 = $row['total'];
							$stats['total_comments'] += $row['total'];
							break;
						case '1':
							$stats['approved']		 = $row['total'];
							$stats['total_comments'] += $row['total'];
							$stats['all'] += $row['total'];
							break;
						case '0':
							$stats['moderated']		 = $row['total'];
							$stats['total_comments'] += $row['total'];
							$stats['all'] += $row['total'];
							break;
						default:
							break;
					}
				}

				$stats = (object) $stats;
				wp_cache_set( "comments-{$postId}", $stats, 'counts' );
			}
		}
		return $stats;
	}

	public function enqueueAdminScripts(){
		parent::enqueueAdminScripts();
		if ( $this->isAdminSingleEditPage() ) {
			wp_enqueue_script( 'mphb-jquery-serialize-json' );
		}
	}

	public function getAttsFromRequest( $request = null ){
		if ( is_null( $request ) ) {
			$request = $_REQUEST;
		}
		$atts = array();
		foreach ( $this->fieldGroups as $group ) {
			$atts = array_merge( $atts, $group->getAttsFromRequest( $request ) );
		}
		return $atts;
	}

	/**
	 * @param array $atts Optional.
	 * @param boolean $atts['room_locked'] Optional. Whether get only bookings that locked room.
	 * @param string $atts['date_from'] Optional. Date in 'Y-m-d' format. Retrieve only bookings that consist dates from period begins at this date.
	 * @param string $atts['date_to'] Optional. Date in 'Y-m-d' format. Retrieve only bookings that consist dates from period ends at this date.
	 * @param array $atts['rooms'] Optional. Room Ids.
	 *
	 * @return array
	 */
	public function getPosts( $atts = array() ){
		$atts = wp_parse_args( $atts, array(
			'post_status' => array_keys( $this->getStatuses() ),
			) );
		if ( isset( $atts['room_locked'] ) && $atts['room_locked'] ) {
			$atts['post_status'] = $this->getLockedRoomStatuses();
			unset( $atts['room_locked'] );
		}
		if ( isset( $atts['date_from'] ) && isset( $atts['date_to'] ) ) {
			$atts['meta_query'] = array(
				'relation' => 'OR',
				array(
					'key'		 => 'mphb_check_in_date',
					'value'		 => array(
						$atts['date_from'],
						$atts['date_to'] ),
					'compare'	 => 'BETWEEN'
				),
				array(
					'key'		 => 'mphb_check_out_date',
					'value'		 => array(
						$atts['date_from'],
						$atts['date_to'] ),
					'compare'	 => 'BETWEEN'
				),
				array(
					'relation' => 'AND',
					array(
						'key'		 => 'mphb_check_in_date',
						'value'		 => $atts['date_from'],
						'compare'	 => '<='
					),
					array(
						'key'		 => 'mphb_check_out_date',
						'value'		 => $atts['date_to'],
						'compare'	 => '>='
					)
				)
			);
			unset( $atts['date_from'] );
			unset( $atts['date_to'] );
		}
		if ( isset( $atts['rooms'] ) ) {
			$metaQuery = array(
				'key'		 => 'mphb_room_id',
				'value'		 => (array) $atts['rooms'],
				'compare'	 => 'IN'
			);
			if ( isset( $atts['meta_query'] ) ) {
				$atts['meta_query'] = array(
					'relation' => 'AND',
					$atts['meta_query'],
					$metaQuery
				);
			} else {
				$atts['meta_query'] = array(
					$metaQuery );
			}
			unset( $atts['rooms'] );
		}
		return parent::getPosts( $atts );
	}

	public function getLockRoomBookings(){
		$items = $this->getPosts( array(
			'post_status' => $this->getLockedRoomStatuses()
			) );
		return $items;
	}

}