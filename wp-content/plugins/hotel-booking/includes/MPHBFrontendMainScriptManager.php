<?php

class MPHBFrontendMainScriptManager {

	private $roomTypeIds = array();

	public function enqueue(){

		if ( !wp_script_is('mphb') ) {
			add_action('wp_print_footer_scripts', array($this, 'localize'), 5);
		}

		wp_enqueue_script( 'mphb' );

		// Styles
		wp_enqueue_style( 'mphb-kbwood-datepick-css' );
		wp_enqueue_style( 'mphb' );
	}

	public function addRoomTypeData($roomTypeId){
		if (!in_array( $roomTypeId, $this->roomTypeIds )) {
			$this->roomTypeIds[] = $roomTypeId;
		}
	}

	public function localize(){
		wp_localize_script( 'mphb', 'MPHB', $this->getLocalizeData());
	}

	public function getLocalizeData(){
		$data = array(
			'_data' => array(
				'settings' => array(
					'firstDay' => MPHB()->getSettings()->getFirstDay(),
					'numberOfMonthCalendar' => 2, // @todo apply_filter
					'numberOfMonthDatepicker' => 2,
				),
				'today' => mphb_current_time(MPHB()->getSettings()->getDateFormat()),
				'ajaxUrl' => MPHB()->getAjaxUrl(),
				'nonces' => MPHB()->getAjax()->getFrontNonces(),
				'room_types_data' => array(),
				'translations' => array(
					'errorHasOccured' => __('An error has occurred, please try again later.', 'motopress-hotel-booking'),
					'booked' => __('Booked', 'motopress-hotel-booking'),
					'pending' => __('Pending', 'motopress-hotel-booking'),
					'available' => __('Available', 'motopress-hotel-booking'),
					'past'		=> __('Past', 'motopress-hotel-booking'),
					'checkInDate' => __('Check In Date', 'motopress-hotel-booking')
				),
				'page' => array(
					'isCheckoutPage' => mphb_is_checkout_page(),
					'isSingleRoomTypePage' => mphb_is_single_room_type_page(),
					'isSearchResultsPage' => mphb_is_search_results_page()
				)
			)
		);

		if ( mphb_is_single_room_type_page() ) {
			$this->addRoomTypeData(get_the_ID());
		}

		foreach($this->roomTypeIds as $roomTypeId) {
			$roomType = new MPHBRoomType($roomTypeId);
			$data['_data']['room_types_data'][$roomType->getId()] = array(
				'dates' => array(
					'booked' => $roomType->getBookingsCountByDay(),
				),
				'activeRoomsCount' => count($roomType->getActiveRooms())
			);
		}

		return $data;
	}
}
