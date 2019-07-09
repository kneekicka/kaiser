<?php

class MPHBAdminMainScriptManager {

	private $roomIds = array();

	public function enqueue(){
		if ( !wp_script_is( 'mphb-admin' ) ) {
			add_action( 'admin_print_footer_scripts', array($this, 'localize'), 5 );
		}
		wp_enqueue_script( 'mphb-admin' );

		// Styles
		wp_enqueue_style( 'mphb-kbwood-datepick-css' );
		wp_enqueue_style( 'mphb-admin-css' );
	}

	public function addRoomData( $roomId ){
		if ( !in_array( $roomId, $this->roomIds ) ) {
			$this->roomIds[] = $roomId;
		}
	}

	public function localize(){
		wp_localize_script( 'mphb-admin', 'MPHB', $this->getLocalizeData() );
	}

	public function getLocalizeData(){
		$data = array(
			'_data' => array(
				'version'		 => MPHB()->getVersion(),
				'prefix'		 => MPHB()->getPrefix(),
				'ajaxUrl'		 => MPHB()->getAjaxUrl(),
				'today'			 => mphb_current_time( MPHB()->getSettings()->getDateFormat() ),
				'nonces'		 => MPHB()->getAjax()->getAdminNonces(),
				'translations'	 => array(
					'roomTypeGalleryTitle'	 => __( 'Room Type Gallery', 'motopress-hotel-booking' ),
					'addGalleryToRoomType'	 => __( 'Add Gallery To Room Type', 'motopress-hotel-booking' ),
					'errorHasOccured'		 => __( '', 'motopress-hotel-booking' )
				),
			),
		);

		return $data;
	}

}
