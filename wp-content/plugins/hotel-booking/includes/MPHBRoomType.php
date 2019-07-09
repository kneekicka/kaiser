<?php

class MPHBRoomType {

	private $id;
	private $post;

	/**
	 *
	 * @param int|WP_POST $id
	 */
	public function __construct($post) {
		if (is_a($post, 'WP_Post')) {
			$this->post = $post;
			$this->id = $post->ID;
		} else {
			$this->id   = absint( $post );
			$this->post = get_post( $this->id );
		}
	}

	/**
	 *
	 * @return boolean
	 */
	public function isCorrect(){
		return !is_null($this->post) &&
			$this->post->post_type === MPHB()->getRoomTypeCPT()->getPostType() &&
			$this->getRates()->hasActiveRates();
	}

	/**
	 *
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 *
	 * @return string
	 */
	public function getTitle(){
		return get_the_title($this->id);
	}

	/**
	 * Check is room type has gallery
	 *
	 * @return boolean
	 */
	public function hasGallery() {
		$idsString = get_post_meta($this->id, 'mphb_gallery', true);
		return !empty($idsString);
	}

	/**
	 * Retrieve ids of gallery's attachments
	 *
	 * @return array
	 */
	public function getGalleryIds(){
		$idsString = get_post_meta($this->id, 'mphb_gallery', true);
		return explode(',', $idsString);
	}

	/**
	 * Check is room type has featured image
	 *
	 * @return bool
	 */
	public function hasFeaturedImage(){
		return has_post_thumbnail($this->id);
	}

	/**
	 * Retrieve room type featured image id.
	 *
	 * @return string | int Room type featured image ID or empty string.
	 */
	public function getFeaturedImageId(){
		return get_post_thumbnail_id( $this->id );
	}

	/**
	 *
	 * @return MPHBRoomRates
	 */
	public function getRates(){
		$rates = get_post_meta($this->id, 'mphb_rates', true);
		$items = isset($rates['items']) ? $rates['items'] : array();
		$default = isset($rates['default']) ? $rates['default'] : 0;
		return new MPHBRoomRates($items, $default);
	}

	/**
	 * Retrieve regular (per night) price.
	 *
	 * @return float
	 */
	public function getPrice(){
		$price = get_post_meta($this->id, 'mphb_price', true);
		return !empty($price) ? floatval($price) : 0;
	}

	public function getPriceHTML(){
		$price = $this->getPrice();
		return mphb_format_price($price);
	}

	/**
	 * Retrieve room type categories
	 *
	 * @return string
	 */
	public function getCategories(){
		$categories = $this->getCategoriesArray();
		return implode(', ', $categories);
	}

	/**
	 *
	 * @return array
	 */
	public function getCategoriesArray(){
		return wp_get_post_terms( $this->id, MPHB()->getRoomTypeCPT()->getCategoryTaxName(), array('fields'=>'names') );
	}

	/**
	 *
	 * @return string
	 */
	public function getFacilities(){
		$facilities = $this->getFacilitiesArray();
		return implode(', ', $facilities);
	}

	/**
	 *
	 * @return array
	 */
	public function getFacilitiesArray(){
		return wp_get_post_terms( $this->id, MPHB()->getRoomTypeCPT()->getFacilityTaxName(), array('fields'=>'names') );
	}

	/**
	 *
	 * @return string
	 */
	public function getView(){
		return get_post_meta($this->id, 'mphb_view', true);
	}

	/**
	 *
	 * @param bool $withUnits Optional. Whether to append units to size. Default FALSE.
	 * @return string
	 */
	public function getSize($withUnits = false){
		$size = get_post_meta($this->id, 'mphb_size', true);
		return !empty($size) ? ( $withUnits ? $size . MPHB()->getSettings()->getSquareUnit() : $size ) : '';
	}

	/**
	 *
	 * @return string
	 */
	public function getBedType(){
		return get_post_meta($this->id, 'mphb_bed', true);
	}

	/**
	 *
	 * @return int
	 */
	public function getAdultsCapacity(){
		$capacity = get_post_meta($this->id, 'mphb_adults_capacity', true);
		return (int) ( !empty($capacity) ? $capacity : MPHB()->getSettings()->getMinAdults() );
	}

	/**
	 *
	 * @return int
	 */
	public function getChildsCapacity(){
		$capacity = get_post_meta($this->id, 'mphb_childs_capacity', true);
		return (int) ( !empty($capacity) ? $capacity : 0 );
	}

	public function getLink(){
		return get_permalink($this->id);
	}

	/**
	 *
	 * @return boolean
	 */
	public function hasServices(){
		$services = $this->getServices();
		return !empty($services);
	}

	/**
	 * Retrieve services available for this room type
	 *
	 * @return array
	 */
	public function getServices(){
		$services = get_post_meta($this->id, 'mphb_services', true);
		return $services !== '' ? $services : array();
	}

	/**
	 *
	 * @return array
	 */
	public function getServicesPriceList(){
		$prices = array();
		$services = $this->getServices();
		foreach ( $services as $serviceId ) {
			$service = new MPHBService($serviceId);
			$prices[$service->getId()] = $service->getPrice();
		}
		return $prices;
	}

	/**
	 *
	 * @param bool $fromToday
	 * @param bool|string status of bookings
	 * @return array dates in format Y-m-d
	 */
	public function getBookingsCountByDay($fromToday = true, $status = false){
		$dates = array();
		$postStatus = $status ? $status : MPHB()->getBookingCPT()->getLockedRoomStatuses();
		$roomsIds = $this->getActiveRooms();

		if ( $fromToday ) {
			$metaQuery = array(
				'relation' => 'AND',
				array(
					array(
						'key' => 'mphb_room_id',
						'value' => $roomsIds,
						'compare' => 'IN'
					),
					// prevent retrieving bookings that have already finished
					array(
						'key' => 'mphb_check_out_date',
						'value' => mphb_current_time('Y-m-d'),
						'compare' => '>=',
						'type' => 'DATE'
					)
				)
			);
		} else {
			$metaQuery = array(
				'key' => 'mphb_room_id',
				'value' => $roomsIds,
				'compare' => 'IN'
			);
		}

		$bookings = MPHB()->getBookingCPT()->getPosts(array(
			'post_status' => $postStatus,
			'meta_query' => $metaQuery,
			'fields' => 'ids'
		));

		$dates = array();
		foreach ( $bookings as $bookingId ) {
			$booking = new MPHBBooking( $bookingId );
			foreach ( $booking->getDates( $fromToday ) as $dateYmd => $date ) {
				if ( isset( $dates[$dateYmd] ) ) {
					$dates[$dateYmd] ++;
				} else {
					$dates[$dateYmd] = 1;
				}
			}
		}
		ksort( $dates );
		return $dates;
	}

	public function getBookedDates( $fromToday = true ){
		return $this->getBookingsCountByDay($fromToday, MPHBBookingCPT::STATUS_CONFIRMED);
	}

	public function getScriptLocalizeData(){
		return array(
			'_data' => array(
				'ajaxUrl' => MPHB()->getAjaxUrl(),
				'dates' => array(
					'bookedDates' => $this->getBookingsCountByDay(),
				),
				'activeRoomsCount' => count($this->getActiveRooms()),
				'today' => mphb_current_time(MPHB()->getSettings()->getDateFormat()),
				'numberOfMonthCalendar' => 2,
				'numberOfMonthDatepicker' => 2,
				'firstDay' => MPHB()->getSettings()->getFirstDay(),
				'roomTypeID' => get_the_ID(),
				'nonces' => array(
					'mphb_check_room_availability' => wp_create_nonce('mphb_check_room_availability')
				),
				'translations' => array(
					'errorHasOccured' => __('An error has occurred, please try again later.', 'motopress-hotel-booking'),
					'booked' => __('Booked', 'motopress-hotel-booking'),
					'pending' => __('Pending', 'motopress-hotel-booking'),
					'available' => __('Available', 'motopress-hotel-booking'),
					'past'		=> __('Past', 'motopress-hotel-booking'),
					'checkInDate' => __('Check In Date', 'motopress-hotel-booking')
				)
			)
		);
	}

	/**
	 *
	 * @global WPDB $wpdb
	 * @param string|DateTime $checkInDate date in format 'Y-m-d'
	 * @param string|DateTime $checkOutDate date in format 'Y-m-d
	 * @param bool|int $excludeBooking
	 * @return array
	 */
	public function getAvailableRooms($checkInDate, $checkOutDate, $excludeBooking = false){
		global $wpdb;
		if (is_a($checkInDate, 'DateTime')) {
			$checkInDate = $checkInDate->format('Y-m-d');
		}
		if (is_a($checkOutDate, 'DateTime')) {
			$checkOutDate = $checkOutDate->format('Y-m-d');
		}
		$checkOutPrevDayDate = date('Y-m-d', strtotime($checkOutDate . ' -1 day'));
		$checkInNextDayDate = date( 'Y-m-d', strToTime($checkInDate . ' +1 day'));

		$whereThisRoom = "( mt0.meta_key = 'mphb_room_id' AND CAST(mt0.meta_value AS CHAR) LIKE $wpdb->posts.ID )";

		$whereDatesIntersect = "( "
			. "( mt1.meta_key = 'mphb_check_in_date' AND CAST(mt1.meta_value AS DATE) BETWEEN '%s' AND '%s' ) "
			. "OR ( mt1.meta_key = 'mphb_check_out_date' AND CAST(mt1.meta_value AS DATE) BETWEEN '%s' AND '%s' ) "
			. "OR ( "
				. "( mt2.meta_key = 'mphb_check_in_date' AND CAST(mt2.meta_value AS DATE) <= '%s' ) "
				. "AND ( mt3.meta_key = 'mphb_check_out_date' AND CAST(mt3.meta_value AS DATE) >= '%s' ) "
			. ") "
			. ")";

		$lockRoomStatuses = MPHB()->getBookingCPT()->getLockedRoomStatuses();
		$whereBookingStatusLockRoom = "p0.post_type = 'mphb_booking' AND ";
		foreach ($lockRoomStatuses as &$status) {
			$status = "p0.post_status = '$status'";
		}
		$whereBookingStatusLockRoom .= "((" . implode(' OR ', $lockRoomStatuses) ."))";

		$bookingsRequest = "SELECT p0.ID "
			. "FROM $wpdb->posts AS p0"
			. " INNER JOIN $wpdb->postmeta AS mt0 ON ( p0.ID = mt0.post_id )"
			. " INNER JOIN $wpdb->postmeta AS mt1 ON ( p0.ID = mt1.post_id )"
			. " INNER JOIN $wpdb->postmeta AS mt2 ON ( p0.ID = mt2.post_id )"
			. " INNER JOIN $wpdb->postmeta AS mt3 ON ( p0.ID = mt3.post_id )"
			. " WHERE 1=1 AND ( $whereThisRoom AND $whereDatesIntersect ) AND $whereBookingStatusLockRoom"
			. ( ($excludeBooking) ? " AND $wpdb->posts.ID != " . $excludeBooking : "" )
			. " GROUP BY $wpdb->posts.ID ORDER BY $wpdb->posts.post_date DESC LIMIT 0, 1";

		$request = "SELECT wp_posts.ID
					FROM wp_posts
					INNER JOIN wp_postmeta ON (wp_posts.ID = wp_postmeta.post_id )
					WHERE 1=1 AND wp_postmeta.meta_key = 'mphb_room_type_id' AND CAST( wp_postmeta.meta_value AS CHAR ) LIKE '" . $this->getId() . "'
						AND wp_posts.post_type = '" . MPHB()->getRoomCPT()->getPostType() . "'
						AND wp_posts.post_status = 'publish'
						AND NOT EXISTS ( $bookingsRequest )
					GROUP BY wp_posts.ID
					ORDER BY wp_posts.post_date
					DESC
		";

		$request = $wpdb->prepare($request, $checkInDate, $checkOutPrevDayDate, $checkInNextDayDate, $checkOutDate, $checkInDate, $checkOutDate);
		return $wpdb->get_col($request);
	}

	/**
	 *
	 * @param string $checkInDate date in Y-m-d format
	 * @param string $checkOutDate date in Y-m-d format
	 * @return bool
	 */
	public function hasAvailableRoom( $checkInDate, $checkOutDate ){
		// @todo make separate db request
		$availableRooms = $this->getAvailableRooms($checkInDate, $checkOutDate);
		return !empty($availableRooms);
	}

	public function getNextAvailableRoom( $checkInDate, $checkOutDate ){
		// @todo make separate db request
		$availableRooms = $this->getAvailableRooms($checkInDate, $checkOutDate);
		return (int) array_shift($availableRooms);
	}

	/**
	 *
	 * @param array $parameters
	 * @return array
	 */
	public function sanitizeSearchParameters($parameters){

		$resultParameters = array(
			'mphb_check_in_date' => '',
			'mphb_check_out_date' => '',
			'mphb_adults' => (string) MPHB()->getSettings()->getMinAdults(),
			'mphb_childs' => (string) MPHB()->getSettings()->getMinChilds()
		);

		if ( !empty( $parameters['mphb_check_in_date'] ) && !empty( $parameters['mphb_check_out_date'] ) ) {

			$checkInDateObj = DateTime::createFromFormat(MPHB()->getSettings()->getDateFormat(), $parameters['mphb_check_in_date']);
			$checkOutDateObj = DateTime::createFromFormat(MPHB()->getSettings()->getDateFormat(), $parameters['mphb_check_out_date']);
			$todayDateObj = DateTime::createFromFormat('Y-m-d', mphb_current_time('Y-m-d'));

			if ( $checkInDateObj && $checkOutDateObj && $checkInDateObj >= $todayDateObj && MPHBUtils::calcNights( $checkInDateObj, $checkOutDateObj ) >= 1 && $this->hasAvailableRoom( $checkInDateObj, $checkOutDateObj ) ) {
				$resultParameters['mphb_check_in_date'] = $checkInDateObj->format( MPHB()->getSettings()->getDateFormat() );
				$resultParameters['mphb_check_out_date'] = $checkOutDateObj->format( MPHB()->getSettings()->getDateFormat() );
			}
		}

		if ( !empty( $parameters['mphb_adults'] ) ) {
			$adults = intval($parameters['mphb_adults']);
			if ( $adults >= MPHB()->getSettings()->getMinAdults() && $adults <= $this->getAdultsCapacity() ) {
				$resultParameters['mphb_adults'] = (string) $adults;
			}
		}

		if ( !empty( $parameters['mphb_childs'] ) ) {
			$childs = intval($parameters['mphb_childs']);
			if ( $childs >= MPHB()->getSettings()->getMinChilds() && $childs <= $this->getChildsCapacity() ) {
				$resultParameters['mphb_childs'] = (string) $childs;
			}
		}

		return $resultParameters;
	}

	/**
	 *
	 * @param type $statuses
	 * @return type
	 */
	public function getRooms( $statuses = array('publish') ){
		$rooms = MPHB()->getRoomCPT()->getPosts( array(
			'post_status'	 => $statuses,
			'fields'		 => 'ids',
			'mphb_room_type' => $this->getId(),
			'orderby'		 => 'menu_order',
			'order'			 => 'ASC',
		) );
		return $rooms;
	}

	public function getActiveRooms(){
		return $this->getRooms();
	}

	public function getAllRooms(){
		return $this->getRooms(array(
			'publish',
			'pending',
			'draft',
			'future',
			'private'
		));
	}

}
