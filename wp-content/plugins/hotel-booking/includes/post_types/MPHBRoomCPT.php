<?php

class MPHBRoomCPT extends MPHBCustomPostType {

	protected $postTypeName = 'mphb_room';

	protected function addActions(){
		parent::addActions();
		add_action( 'parse_query', array($this, 'parseQuery') );
		add_filter( 'request', array($this, 'filterCustomOrderBy') );
		add_action( 'admin_footer', array($this, 'outputScript') );
		add_action( 'restrict_manage_posts', array($this, 'editPostsFilters') );
	}

	public function setManagePageCustomColumns( $columns ){
		$customColumns	 = array(
			'room_type' => __( 'Room Type', 'motopress-hotel-booking' )
		);
		$offset			 = array_search( 'date', array_keys( $columns ) ); // Set custom columns position before "DATE" column
		$columns		 = array_slice( $columns, 0, $offset, true ) + $customColumns + array_slice( $columns, $offset, count( $columns ) - 1, true );

		return $columns;
	}

	public function setManagePageCustomColumnsSortable( $columns ){
		$columns['room_type'] = 'mphb_room_type_id';

		return $columns;
	}

	public function renderManagePageCustomColumns( $column, $postId ){
		$room = new MPHBRoom( $postId );
		switch ( $column ) {
			case 'room_type' :
				$roomTypeId = $room->getRoomTypeId();
				if ( empty( $roomTypeId ) ) {
					echo '<span aria-hidden="true">&#8212;</span>';
				} else {
					$roomType = new MPHBRoomType( $roomTypeId );
					printf( '<a href="%s">%s</a>', add_query_arg( 'mphb_room_type_id', $roomTypeId ), $roomType->getTitle() );
				}
				break;
		}
	}

	public function register(){
		register_post_type( $this->postTypeName, array(
			'labels'				 => array(
				'name'					 => __( 'Rooms', 'motopress-hotel-booking' ),
				'singular_name'			 => __( 'Room', 'motopress-hotel-booking' ),
				'add_new'				 => _x( 'Add New', 'Add New Room', 'motopress-hotel-booking' ),
				'add_new_item'			 => __( 'Add New Room', 'motopress-hotel-booking' ),
				'edit_item'				 => __( 'Edit Room', 'motopress-hotel-booking' ),
				'new_item'				 => __( 'New Room', 'motopress-hotel-booking' ),
				'view_item'				 => __( 'View Room', 'motopress-hotel-booking' ),
				'search_items'			 => __( 'Search Room', 'motopress-hotel-booking' ),
				'not_found'				 => __( 'No rooms found', 'motopress-hotel-booking' ),
				'not_found_in_trash'	 => __( 'No rooms found in Trash', 'motopress-hotel-booking' ),
				'all_items'				 => __( 'Rooms', 'motopress-hotel-booking' ),
				'insert_into_item'		 => __( 'Insert into room description', 'motopress-hotel-booking' ),
				'uploaded_to_this_item'	 => __( 'Uploaded to this room', 'motopress-hotel-booking' )
			),
			'description'			 => __( 'This is where you can add new rooms to your hotel.', 'motopress-hotel-booking' ),
			'public'				 => false,
			'publicly_queryable'	 => false,
			'show_ui'				 => true,
			'show_in_menu'			 => true,
			'query_var'				 => false,
			'capability_type'		 => 'post',
			'has_archive'			 => false,
			'hierarchical'			 => false,
			'show_in_menu'			 => MPHB()->getRoomTypeCPT()->getMenuSlug(),
			'supports'				 => array('title', 'excerpt', 'page-attributes'),
			'hierarchical'			 => false,
			'register_meta_box_cb'	 => array($this, 'registerMetaBoxes'),
		) );
	}

	public function initMetaBoxes(){
		$generalGroup	 = new MPHBMetaBoxGroup( 'General', __( 'Room', 'motopress-hotel-booking' ), $this->postTypeName );
		$roomTypeIdField = MPHBFieldFactory::create(
			'mphb_room_type_id',
			array(
				'type'	 => 'select',
				'list'	 => array('' => __( '— Select —', 'motopress-hotel-booking' )) + MPHB()->getRoomTypeCPT()->getRoomTypesList(),
				'label'	 => __( 'Room Type', 'motopress-hotel-booking' )
				)
		);
		$generalGroup->addField( $roomTypeIdField );

		$this->fieldGroups = array($generalGroup);
	}

	public function parseQuery( $query ){
		if ( $this->isAdminListingPage() ) {
			if ( isset( $_GET['mphb_room_type_id'] ) && $_GET['mphb_room_type_id'] != '' ) {
				$query->query_vars['meta_key']		 = 'mphb_room_type_id';
				$query->query_vars['meta_value']	 = sanitize_text_field( $_GET['mphb_room_type_id'] );
				$query->query_vars['meta_compare']	 = 'LIKE';
			}
			remove_action( 'parse_query', array($this, 'parseQuery') );
		}
	}

	public function filterCustomOrderBy( $vars ){
		if ( $this->isAdminListingPage() ) {
			if ( isset( $vars['orderby'] ) ) {
				switch ( $vars['orderby'] ) {
					case 'mphb_room_type_id':
						$vars = array_merge( $vars, array(
							'meta_key'	 => 'mphb_room_type_id',
							'orderby'	 => 'meta_value_num'
							) );
						break;
				}
			}
		}
		return $vars;
	}

	function _parsePostData( $atts, $defaults = array() ){
		$defaults	 = array(
			'post_status'	 => 'publish',
			'room_type_id'	 => ''
		);
		$postData	 = parent::_parsePostData( $atts, $defaults );

		// Post Meta
		$postData['post_meta']['mphb_room_type_id'] = $atts['room_type_id'];

		return $postData;
	}

	/**
	 *
	 * @return array
	 */
	public function getRoomsTitles(){
		$roomsList	 = array();

		$rooms = $this->getPosts();
		foreach ( $rooms as $room ) {
			$roomsList[$room->ID] = $room->post_title;
		}

		return $roomsList;
	}

	/**
	 *
	 * @return string
	 */
	public function getMenuSlug(){
		return 'edit.php?post_type=' . $this->getPostType();
	}

	/**
	 *
	 * @param int $count Optional. Number of rooms to generate. Default 1.
	 * @param int|string $roomTypeId Optional. Default ''.
	 * @param string $customPrefix Optional. Default ''
	 * @return boolean
	 */
	public function generateRooms( $count = 1, $roomTypeId = '', $customPrefix = '' ){
		$titlePrefix = '';
		if ( !empty( $roomTypeId ) ) {
			$roomType	 = new MPHBRoomType( $roomTypeId );
			$titlePrefix = $roomType->getTitle() . ' ';
		}

		if ( !empty($customPrefix) ) {
			$titlePrefix = $customPrefix . ' ';
		}

		for ( $i = 1; $i <= $count; $i++ ) {
			$this->insertPost( array(
				'room_type_id'	 => $roomTypeId,
				'post_title'	 => $titlePrefix . $i
			) );
		}
		return true;
	}

	function outputScript(){
		if ( $this->isAdminListingPage() ) {
			?>
			<script type="text/javascript">
				(function($) {
					$(function() {
						var generateRoomsButtons = $('<a />', {
							'class': 'page-title-action',
							'text': '<?php _e( 'Generate Rooms', 'motopress-hotel-booking' ); ?>',
							'href': '<?php echo MPHB()->getRoomsGeneratorPage()->getUrl(); ?>'
						});
						$('.page-title-action').after(generateRoomsButtons.clone());
					});
				})(jQuery);
			</script>
			<?php
		}
	}

	public function editPostsFilters(){
		global $typenow;
		if ( $typenow === $this->postTypeName ) {
			$selectedId	 = isset( $_GET['mphb_room_type_id'] ) ? $_GET['mphb_room_type_id'] : '';
			$roomTypes	 = MPHB()->getRoomTypeCPT()->getRoomTypesList();
			echo '<select name="mphb_room_type_id">';
			echo '<option value="">' . __( 'All Room Types', 'motopress-hotel-booking' ) . '</option>';
			foreach ( $roomTypes as $id => $title ) {
				echo '<option value="' . $id . '" ' . selected( $selectedId, $id, false ) . '>' . $title . '</option>';
			}
			echo '</select>';
		}
	}

	/**
	 *
	 * @param array $atts
	 * @param int|string|array $atts['mphb_room_type'] Id(s) of room type
	 *
	 */
	public function getPosts( $atts = array() ){
		if ( isset( $atts['mphb_room_type'] ) ) {
			$roomType = (array) $atts['mphb_room_type'];
			unset( $atts['mphb_room_type'] );

			// @todo merge meta query if exists
			$atts['meta_query'] = array(
				array(
					'key'		 => 'mphb_room_type_id',
					'value'		 => $roomType,
					'compare'	 => 'IN'
				)
			);
		}

		return parent::getPosts( $atts );
	}

	/**
	 *
	 * @global WPDB $wpdb
	 * @param array $atts
	 * @param string $atts['availability'] Optional. Accepts 'free', 'locked', 'booked', 'pending'. Default 'free'
	 *                                     free - has no bookings with status complete or pending for this days x room
	 *                                     locked - has bookings with status complete or pending for this days x room
	 *                                     booked - has bookings with status complete for this days x room
	 *                                     pending - has bookings with status pending for this days x rooms
	 * @param DateTime $atts['from_date] Optional. Default today.
	 * @param DateTime $atts['to_date'] Optional.Default today.
	 * @return array Array of Ids.
	 */
	public function searchRooms( $atts = array() ){
		global $wpdb;

		$atts = array_merge( array(
			'availability'	 => 'free',
			'from_date'		 => new DateTime( current_time( 'mysql' ) ),
			'to_date'		 => new DateTime( current_time( 'mysql' ) )
			), $atts );

		$fromDate	 = clone $atts['from_date'];
		$toDate		 = clone $atts['to_date'];

		$fromDateNextDay = clone $fromDate;
		$fromDateNextDay->modify( '+1 day' );
		$toDatePrevDay	 = clone $toDate;
		$toDatePrevDay->modify( '-1 day' );

		$whereDatesIntersect = "( ( ( $wpdb->postmeta.meta_key = 'mphb_check_in_date' AND CAST($wpdb->postmeta.meta_value AS DATE) BETWEEN '%s' AND '%s' ) OR ( $wpdb->postmeta.meta_key = 'mphb_check_out_date' AND CAST($wpdb->postmeta.meta_value AS DATE) BETWEEN '%s' AND '%s' ) OR ( ( mt1.meta_key = 'mphb_check_in_date' AND CAST(mt1.meta_value AS DATE) <= '%s' ) AND ( mt2.meta_key = 'mphb_check_out_date' AND CAST(mt2.meta_value AS DATE) >= '%s' ) ) ) )";

		switch ( $atts['availability'] ) {
			case 'free':
				$bookingStatuses = MPHB()->getBookingCPT()->getLockedRoomStatuses();
				break;
			case 'booked':
				$bookingStatuses = MPHB()->getBookingCPT()->getBookedRoomStatuses();
				break;
			case 'pending':
				$bookingStatuses = MPHB()->getBookingCPT()->getPendingRoomStatuses();
				break;
			case 'locked':
				$bookingStatuses = MPHB()->getBookingCPT()->getLockedRoomStatuses();
				break;
		}

		foreach ( $bookingStatuses as &$status ) {
			$status = "$wpdb->posts.post_status = '$status'";
		}
		$whereBookingStatusLockRoom = "(( " . implode( ' OR ', $bookingStatuses ) . " )) ";

		$query = "SELECT mt3.meta_value "
			. "FROM $wpdb->posts "
			. "INNER JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id ) "
			. "INNER JOIN $wpdb->postmeta AS mt1 ON ( $wpdb->posts.ID = mt1.post_id ) "
			. "INNER JOIN $wpdb->postmeta AS mt2 ON ( $wpdb->posts.ID = mt2.post_id ) "
			. "INNER JOIN $wpdb->postmeta AS mt3 ON ( $wpdb->posts.ID = mt3.post_id ) "
			. "WHERE 1=1 "
			. "AND $wpdb->posts.post_type = '" . MPHB()->getBookingCPT()->getPostType() . "' "
			. "AND $whereDatesIntersect AND $whereBookingStatusLockRoom"
			. "AND mt3.meta_key = 'mphb_room_id' "
			. "AND mt3.meta_value IS NOT NULL "
			. "AND mt3.meta_value <> ''"
			. "GROUP BY mt3.meta_value ";

		$query = sprintf( $query, $fromDate->format( 'Y-m-d' ), $toDatePrevDay->format( 'Y-m-d' ), $fromDateNextDay->format( 'Y-m-d' ), $toDate->format( 'Y-m-d' ), $fromDate->format( 'Y-m-d' ), $toDate->format( 'Y-m-d' ) );

		if ( $atts['availability'] === 'free' ) {
			$bookedRooms = $query;
			$query		 = "SELECT rooms.ID "
				. "FROM $wpdb->posts as rooms "
				. "WHERE 1=1 "
				. "AND rooms.post_type = '" . MPHB()->getRoomCPT()->getPostType() . "' "
				. "AND rooms.post_status = 'publish' "
				. "AND rooms.ID NOT IN ( $bookedRooms ) "
				. "ORDER BY rooms.ID "
				. "DESC";
		}

		return $wpdb->get_col( $query );
	}

}
