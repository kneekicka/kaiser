<?php

class MPHBAjax {

	private $nonceName = 'mphb_nonce';

	private $ajaxEvents = array(
		'check_room_availability' => array(
			'method' => 'GET',
			'nopriv' => true
		),
		'recalculate_total' => array(
			'method' => 'GET',
			'nopriv' => false
		),
		'recalculate_checkout_prices' => array(
			'method' => 'GET',
			'nopriv' => true
		),
		'get_rates_for_room' => array(
			'method' => 'GET',
			'nopriv' => false
		)
	);

	public function __construct(){

		foreach ( $this->ajaxEvents as $action => $details ) {
			$noPriv = isset( $details['nopriv'] ) ? $details['nopriv'] : false;
			$this->addAjaxAction( $action, $noPriv );
		}

	}

	/**
	 *
	 * @param string $action
	 * @param boolean $noPriv
	 */
	public function addAjaxAction( $action, $noPriv ){

		add_action( 'wp_ajax_mphb_' . $action, array( $this, $action ) );

		if ( $noPriv ) {
			add_action( 'wp_ajax_nopriv_mphb_' . $action, array( $this, $action ) );
		}
	}

	/**
	 *
	 * @param string $action
	 * @return boolean
	 */
	public function checkNonce( $action ){

		if ( ! isset( $this->ajaxEvents[$action] ) ) {
			return false;
		}

		$method = isset( $this->ajaxEvents[$action]['method'] ) ? $this->ajaxEvents[$action]['method'] : '';

		switch($method) {
			case 'GET':
				$nonce = isset( $_GET[$this->nonceName] ) ? $_GET[$this->nonceName] : '';
				break;
			case 'POST':
				$nonce = isset( $_POST[$this->nonceName] ) ? $_POST[$this->nonceName] : '';
				break;
			default:
				$nonce = isset( $_REQUEST[$this->nonceName] ) ? $_REQUEST[$this->nonceName] : '';
		}

		return wp_verify_nonce($nonce, 'mphb_' . $action);
	}

	/**
	 *
	 * @return array
	 */
	public function getAdminNonces(){
		$nonces = array();
		foreach ($this->ajaxEvents as $evtName => $evtDetails) {
			$nonces['mphb_' . $evtName] = wp_create_nonce('mphb_' . $evtName);
		}
		return $nonces;
	}

	/**
	 *
	 * @return arrray
	 */
	public function getFrontNonces(){
		$nonces = array();
		foreach ($this->ajaxEvents as $evtName => $evtDetails) {
			if ( isset($evtDetails['nopriv']) && $evtDetails['nopriv'] ) {
				$nonces['mphb_' . $evtName] = wp_create_nonce('mphb_' . $evtName);
			}
		}
		return $nonces;
	}

	public function check_room_availability(){

		// Check Nonce
		if ( !$this->checkNonce( __FUNCTION__ ) ) {
			wp_send_json_error(array(
				'message' => __('Request do not pass security check! Please refresh the page and try one more time.', 'motopress-hotel-booking')
			));
		}

		// Check is request parameters setted
		if ( ! ( isset($_GET['roomTypeID']) && isset($_GET['checkInDate']) && isset($_GET['checkOutDate']) ) ) {
			wp_send_json_error(array(
				'message' => __('Please complete all required fields and try again.', 'motopress-hotel-booking'),
			));
		}

		$checkInDate = DateTime::createFromFormat(MPHB()->getSettings()->getDateFormat(), $_GET['checkInDate']);
		$checkOutDate = DateTime::createFromFormat(MPHB()->getSettings()->getDateFormat(), $_GET['checkOutDate']);
		$roomTypeID = absint($_GET['roomTypeID']);

		// Check is correct request parameters
		if ( ! ( $roomTypeID && $checkInDate && $checkOutDate ) ) {
			wp_send_json_error(array(
				'message' => __('An error has occurred, please try again later.', 'motopress-hotel-booking'),
			));
		}

		$roomType = new MPHBRoomType($roomTypeID);

		if ( $roomType->hasAvailableRoom( $checkInDate, $checkOutDate ) ) {
			wp_send_json_success();
		} else {
			wp_send_json_error(array(
				'message' => __('Room is unavailable for requested dates.', 'motopress-hotel-booking'),
				'updatedData' =>  array(
					'dates' => array(
						'booked' => $roomType->getBookingsCountByDay()
					),
					'activeRoomsCount' => count($roomType->getActiveRooms())
				)
			));
		}

	}

	public function recalculate_total(){
		if ( !$this->checkNonce( __FUNCTION__ ) ) {
			wp_send_json_error(array(
				'message' => __('Request do not pass security check! Please refresh the page and try one more time.', 'motopress-hotel-booking')
			));
		}

		if ( ! ( isset($_GET['formValues']) && is_array($_GET['formValues']) ) ) {
			wp_send_json_error(array(
				'message' => __('An error has occurred, please try again later.', 'motopress-hotel-booking'),
			));
		}

		$atts = MPHB()->getBookingCPT()->getAttsFromRequest($_GET['formValues']);

		// Check Required Fields
		if ( !isset( $atts['mphb_room_id'] ) || empty( $atts['mphb_room_id'] ) ||
			!isset( $atts['mphb_room_rate_id'] ) || $atts['mphb_room_rate_id'] === '' ||
			!isset( $atts['mphb_check_in_date'] ) || empty( $atts['mphb_check_in_date'] ) ||
			!isset( $atts['mphb_check_out_date'] ) || empty( $atts['mphb_check_out_date'] )
		) {
			wp_send_json_error( array(
				'message' => __( 'Please complete all required fields and try again.', 'motopress-hotel-booking' )
			) );
		}
		$room = new MPHBRoom($atts['mphb_room_id']);
		$atts['mphb_room_type_id'] = $room->getRoomTypeId();

		$roomType = new MPHBRoomType($atts['mphb_room_type_id']);
		$roomRate = $roomType->getRates()->getRate($atts['mphb_room_rate_id']);
		$adults = absint($atts['mphb_adults']);
		$childs = absint($atts['mphb_childs']);
		$checkInDate = DateTime::createFromFormat('Y-m-d', $atts['mphb_check_in_date']);
		$checkOutDate = DateTime::createFromFormat('Y-m-d', $atts['mphb_check_out_date']);

		$services = array();
		if (isset($atts['mphb_services']) && is_array($atts['mphb_services'])) {
			foreach ( $atts['mphb_services'] as $serviceDetails ) {
				$service = new MPHBService($serviceDetails['id']);
				$service->setAdults($serviceDetails['count']);
				$services[] = $service;
			}
		}

		$booking = new MPHBBooking();
		$booking->setupParameters( array(
			'room'			 => $room,
			'room_rate'		 => $roomRate,
			'adults'		 => $adults,
			'childs'		 => $childs,
			'check_in_date'	 => $checkInDate,
			'check_out_date' => $checkOutDate,
			'services'		 => $services
		) );

		wp_send_json_success(array(
			'total' => $booking->calculateTotalPrice()
		));

	}

	public function recalculate_checkout_prices(){
		if ( !$this->checkNonce( __FUNCTION__ ) ) {
			wp_send_json_error(array(
				'message' => __('Total price reaclculate do not pass security check! Please refresh the page and try one more time.', 'motopress-hotel-booking')
			));
		}

		if ( ! (
			isset($_GET['formValues']) && is_array($_GET['formValues'])
			&& isset( $_GET['formValues']['mphb_room_type_id'] ) && $_GET['formValues']['mphb_room_type_id'] !== ''
			&& isset($_GET['formValues']['mphb_room_rate_id']) && $_GET['formValues']['mphb_room_rate_id'] !== ''
			&& isset($_GET['formValues']['mphb_check_in_date']) && $_GET['formValues']['mphb_check_in_date'] !== ''
			&& isset($_GET['formValues']['mphb_check_out_date']) && $_GET['formValues']['mphb_check_out_date'] !== ''
		) ) {
			wp_send_json_error(array(
				'message' => __('An error has occurred while recalculating the price, please try again later.', 'motopress-hotel-booking'),
			));
		}

		$atts = MPHB()->getBookingCPT()->getAttsFromRequest( $_GET['formValues'] );

		$atts['mphb_room_type_id']	 = absint( $_GET['formValues']['mphb_room_type_id'] );
		$atts['mphb_room_rate_id']	 = absint( $atts['mphb_room_rate_id'] );

		$services = array();
		if ( isset( $atts['mphb_services'] ) ) {
			foreach ( $atts['mphb_services'] as $key => $serviceDetails ) {
				if ( !isset( $serviceDetails['id'] ) || empty( $serviceDetails['id'] ) ) {
					unset( $atts['mphb_services'][$key] );
				} else {
					$service	 = new MPHBService( absint( $serviceDetails['id'] ) );
					$service->setAdults( absint( $serviceDetails['count'] ) );
					$services[]	 = $service;
				}
			}
		}

		$roomType		 = new MPHBRoomType( $atts['mphb_room_type_id'] );
		$roomRate		 = $roomType->getRates()->getRate( absint( $atts['mphb_room_rate_id'] ) );
		$checkInDate	 = DateTime::createFromFormat( 'Y-m-d', $atts['mphb_check_in_date'] );
		$checkOutDate	 = DateTime::createFromFormat( 'Y-m-d', $atts['mphb_check_out_date'] );
		$adults			 = absint( $atts['mphb_adults'] );
		$childs			 = absint( $atts['mphb_childs'] );

		$roomType = new MPHBRoomType( $atts['mphb_room_type_id'] );
		if ( !$roomType->getRates()->hasActiveRate( $atts['mphb_room_rate_id'] ) ) {
			wp_send_json_error( array(
				'message' => __( 'An error has occurred while recalculating the price, please try again later.', 'motopress-hotel-booking' ),
			) );
		}

		$booking = new MPHBBooking();
		$booking->setupParameters( array(
			'room_rate'		 => $roomRate,
			'adults'		 => $adults,
			'childs'		 => $childs,
			'check_in_date'	 => $checkInDate,
			'check_out_date' => $checkOutDate,
			'services'		 => $services
		) );

		wp_send_json_success( array(
			'total'			 => mphb_format_price( $booking->calculateTotalPrice() ),
			'priceBreakdown' => MPHBBookingView::generatePriceBreakdown( $booking )
		) );
	}

	public function get_rates_for_room(){
		if ( !$this->checkNonce( __FUNCTION__ ) ) {
			wp_send_json_error(array(
				'message' => __('Total price reaclculate do not pass security check! Please refresh the page and try one more time.', 'motopress-hotel-booking')
			));
		}

		$list = array();
		if ( isset($_GET['formValues']) && is_array($_GET['formValues']) && isset( $_GET['formValues']['mphb_room_id'] ) && !empty($_GET['formValues']['mphb_room_id']) ) {
			$roomId = absint($_GET['formValues']['mphb_room_id']);
			$list = MPHBRoom::getRatesIdTitleList( $roomId );
		}

		wp_send_json_success(array(
			'options' => $list
		));
	}

}

