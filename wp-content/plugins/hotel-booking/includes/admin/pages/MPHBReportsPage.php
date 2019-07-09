<?php

class MPHBReportsPage extends MPHBAdminPage {

	protected $name = 'mphb_reports';
	private $hookSuffix;
	private $calendar;

	public function createMenu(){
		$this->hookSuffix = add_submenu_page( MPHB()->getMainMenuSlug()
			, __( 'Booking Calendar', 'motopress-hotel-booking' )
			, __( 'Calendar', 'motopress-hotel-booking' )
			, 'manage_options'
			, $this->name
			, array($this, 'renderPage')
		);
	}

	public function addActions(){
		parent::addActions();
		add_action( 'admin_enqueue_scripts', array($this, 'enqueueAdminScripts'), 15 );
	}

	public function setupCalendar(){
		MPHB()->requireOnce( 'includes/MPHBBookingsCalendar.php' );

		$this->calendar = new MPHBBookingsCalendar();
	}

	public function enqueueAdminScripts(){
		$screen = get_current_screen();
		if ( !is_null( $screen ) && $screen->id === $this->hookSuffix ) {
			MPHB()->getAdminMainScriptManager()->enqueue();
		}
	}

	public function renderPage(){
		$this->setupCalendar();
		?>
		<div class="wrap">
		<h1><?php _e( 'Booking Calendar', 'motopress-hotel-booking' ); ?></h1>
		<?php
		$this->calendar->render();
		?>
		</div>
		<?php
	}

}
