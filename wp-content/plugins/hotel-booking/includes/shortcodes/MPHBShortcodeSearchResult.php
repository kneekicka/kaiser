<?php
class MPHBShortcodeSearchResult extends MPHBShortcode {

	protected $shortcodeName = 'mphb_search_results';

	const NONCE_NAME = 'mphb-search-available-room-nonce';
	const NONCE_ACTION = 'mphb-search-available-room';

	/**
	 *
	 * @var int
	 */
	private $adults;
	/**
	 *
	 * @var int
	 */
	private $childs;
	/**
	 *
	 * @var DateTime
	 */
	private $checkInDate;
	/**
	 *
	 * @var DateTime
	 */
	private $checkOutDate;
	/**
	 *
	 * @var array
	 */
	private $errors = array();

	private $isCorrectInputData = false;
	private $isCorrectPage = false;

	private $isShowTitle;
	private $isShowFeaturedImage;
	private $isShowGallery;
	private $isShowExcerpt;
	private $isShowDetails;
	private $sortingMode;

	public function addActions() {
		parent::addActions();
		add_action('wp', array($this, 'setup'));

		// Room Template
		add_action('mphb_sc_search_results_room_form', array( $this, 'renderGallery'), 10);
		add_action('mphb_sc_search_results_room_form', array( $this, 'renderFeaturedImage'), 10);
		add_action('mphb_sc_search_results_room_form', array( $this, 'renderTitle'), 20);
		add_action('mphb_sc_search_results_room_form', array( $this, 'renderExcerpt'), 30);
		add_action('mphb_sc_search_results_room_form', array( $this, 'renderDetails'), 40);
		add_action('mphb_sc_search_results_room_form', array( 'MPHBLoopRoomTypeView', 'renderPrice'), 50);
		add_action('mphb_sc_search_results_room_form', array('MPHBLoopRoomTypeView', 'renderViewDetailsButton'), 60);
		add_action('mphb_sc_search_results_room_form', array( 'MPHBLoopRoomTypeView', 'renderBookButton'), 70);

		// Errors
		add_action('mphb_sc_search_results_errors_content', array($this, 'showErrorsContent'));
		add_filter('mphb_sc_search_results_error', array($this, 'filterErrorOutput'));
	}

	public function render($atts, $content = '', $shortcodeName){
		$atts = shortcode_atts(array(
			'title' => 'true',
			'featured_image' => 'false',
			'gallery' => 'true',
			'excerpt' => 'true',
			'details' => 'true',
			'default_sorting' => 'order'
		), $atts, $shortcodeName);

		$this->isShowTitle = $this->convertParameterToBoolean( $atts['title'] );
		$this->isShowFeaturedImage = $this->convertParameterToBoolean( $atts['featured_image'] );
		$this->isShowGallery = $this->convertParameterToBoolean( $atts['gallery'] );
		$this->isShowExcerpt = $this->convertParameterToBoolean( $atts['excerpt'] );
		$this->isShowDetails = $this->convertParameterToBoolean( $atts['details'] );
		$this->sortingMode = $atts['default_sorting'];

		ob_start();

		$this->renderSearchShortcode();

		if ( $this->isCorrectPage && $this->isCorrectInputData ) {

			$roomTypes = $this->getRoomTypesMatched();

			if ($roomTypes->have_posts()) {

				echo '<p class="mphb_sc_search-results-count">' . sprintf( __( '%s room(s) found', 'motopress-hotel-booking' ), $roomTypes->post_count ) . '</p>';

				while ($roomTypes->have_posts()) : $roomTypes->the_post();
					$this->renderRoomType();
				endwhile;

				wp_reset_postdata();
			} else {
				$this->showNotMatchedMessage();

			}
		} else {
			$this->showErrorsMessage();
		}
		return '<div class="mphb_sc_search_results-wrapper">' . ob_get_clean() . '</div>';
	}

	private function renderSearchShortcode(){
		$shortcodeAttrs = array();
		if (isset( $this->checkInDate ) ) {
			$shortcodeAttrs['check_in_date'] = $this->checkInDate->format(MPHB()->getSettings()->getDateFormat());
		}
		if ( isset( $this->checkOutDate ) ){
			$shortcodeAttrs['check_out_date'] = $this->checkOutDate->format(MPHB()->getSettings()->getDateFormat());
		}
		if ( isset( $this->adults ) ) {
			$shortcodeAttrs['adults'] = $this->adults;
		}
		if ( isset( $this->childs ) ) {
			$shortcodeAttrs['childs'] = $this->childs;
		}
		$searchShortcode = MPHB()->getShortcodeSearch()->generateShortcode($shortcodeAttrs);
		echo do_shortcode( $searchShortcode );
	}

	private function renderRoomType(){
		$roomType = MPHB()->getCurrentRoomType();
		do_action('mphb_sc_search_results_before_room');
		?>
		<div class="mphb-room-type <?php echo apply_filters('mphb_sc_search_results_room_type_class', ''); ?> <?php echo join(' ', mphb_tmpl_get_filtered_post_class()); ?>">
			<?php do_action('mphb_sc_search_results_room_form', $roomType, array(
				'check_in_date' => $this->checkInDate->format(MPHB()->getSettings()->getDateFormat()),
				'check_out_date' => $this->checkOutDate->format(MPHB()->getSettings()->getDateFormat()),
				'adults' => $this->adults,
				'childs' => $this->childs
			)); ?>
		</div>
		<?php
		do_action('mphb_sc_search_results_after_room');
	}

	public function renderGallery(){
		if ( $this->isShowGallery ) {
			MPHBLoopRoomTypeView::renderGalleryOrFeaturedImage();
		}
	}

	public function renderFeaturedImage(){
		if ( $this->isShowFeaturedImage ){
			MPHBLoopRoomTypeView::renderFeaturedImage();
		}
	}

	public function renderTitle(){
		if ( $this->isShowTitle ) {
			MPHBLoopRoomTypeView::renderTitle();
		}
	}

	public function renderExcerpt(){
		if ( $this->isShowExcerpt ) {
			MPHBLoopRoomTypeView::renderExcerpt();
		}
	}

	public function renderDetails(){
		if ( $this->isShowDetails ) {
			MPHBLoopRoomTypeView::renderAttributes();
		}
	}

	private function getRoomTypesMatched(){
		$query = new WP_Query(array(
			'post_type' => MPHB()->getRoomTypeCPT()->getPostType(),
			'post_status' => 'publish',
			'meta_key' => 'mphb_price', // allow sorting by price.
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'mphb_adults_capacity',
					'value' => $this->adults,
					'compare' => '>=',
					'type' => 'NUMERIC'
				),
				array(
					'key' => 'mphb_childs_capacity',
					'value' => $this->childs,
					'compare' => '>=',
					'type' => 'NUMERIC'
				)
			),
			'orderby' => ( $this->sortingMode === 'price' ? 'meta_value_num' : 'menu_order' ),
			'order' => 'ASC',
			'post__in' => $this->getAvailableRoomTypes($this->checkInDate->format('Y-m-d'), $this->checkOutDate->format('Y-m-d')),
			'posts_per_page' => -1,
			'ignore_sticky_posts' => true
		));
		return $query;
	}

	/**
	 *
	 * @global WPDB $wpdb
	 * @param string $checkInDate date in 'Y-m-d' format
	 * @param string $checkOutDate date in 'Y-m-d' format
	 * @return array
	 */
	private function getAvailableRoomTypes($checkInDate, $checkOutDate){
		//@todo replace this method with MPHBRoomTypeCPT->searchRoomTypes()
		global $wpdb;

		$checkInNextDayDate = date('Y-m-d', strToTime($checkInDate . ' +1 day'));
		$checkOutPrevDayDate = date('Y-m-d', strtotime($checkOutDate . ' -1 day'));

		$whereDatesIntersect = "( ( ( $wpdb->postmeta.meta_key = 'mphb_check_in_date' AND CAST($wpdb->postmeta.meta_value AS DATE) BETWEEN '%s' AND '%s' ) OR ( $wpdb->postmeta.meta_key = 'mphb_check_out_date' AND CAST($wpdb->postmeta.meta_value AS DATE) BETWEEN '%s' AND '%s' ) OR ( ( mt1.meta_key = 'mphb_check_in_date' AND CAST(mt1.meta_value AS DATE) <= '%s' ) AND ( mt2.meta_key = 'mphb_check_out_date' AND CAST(mt2.meta_value AS DATE) >= '%s' ) ) ) )";

		$lockRoomStatuses = MPHB()->getBookingCPT()->getLockedRoomStatuses();
		foreach ($lockRoomStatuses as &$status) {
			$status = "$wpdb->posts.post_status = '$status'";
		}
		$whereBookingStatusLockRoom = "(( " . implode(' OR ', $lockRoomStatuses) ." )) ";

		$bookedRooms = "SELECT mt3.meta_value "
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

		$bookedRooms = sprintf($bookedRooms, $checkInDate, $checkOutPrevDayDate, $checkInNextDayDate, $checkOutDate, $checkInDate, $checkOutDate);

		$query = "SELECT room_meta.meta_value
					FROM $wpdb->posts as rooms
						INNER JOIN $wpdb->postmeta as room_meta ON (rooms.id = room_meta.post_id)
					WHERE 1=1
						AND rooms.post_type = '" . MPHB()->getRoomCPT()->getPostType() . "'
						AND rooms.post_status = 'publish'
						AND room_meta.meta_key = 'mphb_room_type_id'
						AND rooms.ID NOT IN ( $bookedRooms )
					GROUP BY room_meta.meta_value
					ORDER BY rooms.post_date
					DESC";

		$roomTypes = $wpdb->get_col($query);
		return !empty($roomTypes) ? $roomTypes : array(0);
	}

	private function setupSearchData(){
		$this->adults = null;
		$this->childs = null;
		$this->checkInDate = null;
		$this->checkOutDate = null;
		$this->isCorrectInputData = false;

		if ( isset($_GET['mphb_adults']) && isset($_GET['mphb_childs']) && isset($_GET['mphb_check_in_date']) && isset($_GET['mphb_check_out_date']) ) {

			$input = $_GET;
			$this->parseInputData($input);

			if ( $this->isCorrectInputData ) {
				$this->storeSearchParameters();
			}

		} else if ( MPHB()->hasStoredSearchParameters() ) {
			$input = MPHB()->getStoredSearchParameters();
			$this->parseInputData($input);
		}

	}

	/**
	 *
	 * @return bool
	 */
	private function parseInputData( $input ){
		$isCorrectAdults	= $this->parseAdults($input['mphb_adults']);
		$isCorrectChilds	= $this->parseChilds($input['mphb_childs']);
		$isCorrectCheckInDate = $this->parseCheckInDate($input['mphb_check_in_date']);
		$isCorrectCheckOutDate	= $this->parseCheckOutDate($input['mphb_check_out_date']);

		$this->isCorrectInputData = ( $isCorrectAdults && $isCorrectChilds && $isCorrectCheckInDate && $isCorrectCheckOutDate );

		return $this->isCorrectInputData;
	}

	public function setup(){
		if ( mphb_is_search_results_page() ) {
			$this->isCorrectPage = true;
			$this->setupSearchData();
		}
	}

	private function storeSearchParameters(){
		MPHB()->storeSearchParameters(array(
			'mphb_check_in_date' => $this->checkInDate->format(MPHB()->getSettings()->getDateFormat()),
			'mphb_check_out_date' => $this->checkOutDate->format(MPHB()->getSettings()->getDateFormat()),
			'mphb_adults' => $this->adults,
			'mphb_childs' => $this->childs
		));
	}

	/**
	 *
	 * @param string|int $adults
	 * @return boolean
	 */
	private function parseAdults( $adults ){
		$adults = intval($adults);
		if ( $adults >= MPHB()->getSettings()->getMinAdults() && $adults <= MPHB()->getSettings()->getMaxAdults() ) {
			$this->adults = $adults;
			return true;
		} else {
			$this->errors[] = __('Number of adults is incorrect.', 'motopress-hotel-booking');
			return false;
		}
	}

	private function parseChilds( $childs ){
		$childs = intval($childs);
		if ( $childs >= MPHB()->getSettings()->getMinChilds() && $childs <= MPHB()->getSettings()->getMaxChilds() ) {
			$this->childs = $childs;
			return true;
		} else {
			$this->errors[] = __('Number of childs is incorrect.', 'motopress-hotel-booking');
			return false;
		}
	}

	private function parseCheckInDate( $date ){
		$checkInDateObj = DateTime::createFromFormat( MPHB()->getSettings()->getDateFormat(), $date );
		$todayDate = DateTime::createFromFormat('Y-m-d', mphb_current_time('Y-m-d'));

		if ( !$checkInDateObj ) {
			$this->errors[] = __('Check-in date is incorrect.', 'motopress-hotel-booking');
			return false;
		} else if ( MPHBUtils::calcNights ( $todayDate, $checkInDateObj ) < 0 ) {
			$this->errors[] = __('Check-in date cannot be earlier than today.', 'motopress-hotel-booking');
			return false;
		}

		$this->checkInDate = $checkInDateObj;
		return true;
	}

	private function parseCheckOutDate( $date ){

		$checkOutDateObj = MPHBUtils::createCheckOutDate( MPHB()->getSettings()->getDateFormat(), $date );

		if ( !$checkOutDateObj ) {
			$this->errors[] = __('Check-out date is incorrect.', 'motopress-hotel-booking');
			return false;
		} else if ( isset($this->checkInDate) && MPHBUtils::calcNights( $this->checkInDate, $checkOutDateObj ) < 1 ){
			$this->errors[] = __('Check-out date must be later than check-in date.', 'motopress-hotel-booking');
			return false;
		}
		$this->checkOutDate = $checkOutDateObj;
		return true;
	}


	public function showErrorsMessage(){
		?>
			<div class="mphb-errors-wrapper">
				<?php
				do_action('mphb_sc_search_results_errors_content', $this->errors);
				?>
			</div>
		<?php
	}

	public function showErrorsContent($errors){
		foreach ($errors as $error) {
			echo apply_filters('mphb_sc_search_results_error', $error);
		}
	}

	public function filterErrorOutput($error){
		return '<br/>' . $error;
	}

	public function showNotMatchedMessage(){
		echo apply_filters( 'mphb_sc_search_results_not_matched', '<p class="mphb_sc_search_results-not-found">' . __('No rooms matched criteria.', 'motopress-hotel-booking') . '</p>');
	}
}
