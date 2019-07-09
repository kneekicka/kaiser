<?php

class MPHBWizard{

	const NONCE_NAME = 'mphb-wizard-nonce';
	const NONCE_ACTION_START = 'mphb-start';
	const NONCE_ACTION_SKIP = 'mphb-skip';

	public function __construct() {
		add_action( 'admin_notices', array( $this, 'wizardNotice' ) );
		add_action( 'init', array($this, 'checkUserAction'));
	}

	/**
	 * Display the admin notice to users
	 *
	 */
	public function wizardNotice() {		

		if( $this->isWizardPassed() ) {
			return;
		}		
		
		if( ! $this->checkCapabilities() ) {
			return;
		}

		$startWizardUrl		= wp_nonce_url(add_query_arg( 'mphb_wizard_action', 'start'), self::NONCE_ACTION_START, self::NONCE_NAME);
		$skipUrl			= wp_nonce_url(add_query_arg( 'mphb_wizard_action', 'skip' ), self::NONCE_ACTION_SKIP, self::NONCE_NAME);

		echo '<div class="updated"><p>';		
		echo '<p><strong>' . __( 'Hotel Booking plugin', 'motopress-hotel-booking' ) . '</strong></p>';
		echo '<p>' . __( 'Checkout and Search Results pages are required to handle bookings. Press "Install Pages" button to create and setup these pages. Dismiss this notice if you already have them installed.', 'motopress-hotel-booking' ) . '</p>';
		echo '&nbsp;<a href="' . esc_url( $startWizardUrl ) . '" class="button-primary">' . __( 'Install Pages & Settings', 'motopress-hotel-booking' ) . '</a>';
		echo '&nbsp;<a href="' . esc_url( $skipUrl ) . '" class="button-secondary">' . __( 'Skip', 'motopress-hotel-booking' ) . '</a>';
		echo '</p></div>';
	}

	private function checkCapabilities(){
		return current_user_can( 'manage_options' ) && current_user_can( 'publish_pages' );
	}

	private function isWizardPassed(){
		return get_option('mphb_wizard_passed', false);
	}

	public function checkUserAction(){		
		if ( isset($_GET['mphb_wizard_action']) && !$this->isWizardPassed() ) {			
			switch ($_GET['mphb_wizard_action']) {
				case 'start':
					if (wp_verify_nonce($_GET[self::NONCE_NAME], self::NONCE_ACTION_START)) {						
						$this->start();
					}					
					break;
				case 'skip' :
					if (wp_verify_nonce($_GET[self::NONCE_NAME], self::NONCE_ACTION_SKIP)) {
						$this->pass();
					}
					break;
			}
		}
	}

	public function start(){
		$this->addRoomsPage();
		$this->addSearchPage();
		$this->addResultsPage();
		$this->addCheckoutPage();
		$this->addRoomTypeCategories();
		$this->pass();
	}
	
	public function addSearchPage(){
		$title = __('Search Availability', 'motopress-hotel-booking');
		$content = MPHB()->getShortcodeSearch()->generateShortcode();
		$this->createPage($title, $content);
	}

	public function addResultsPage(){
		$title = __('Search Results', 'motopress-hotel-booking');
		$content = MPHB()->getShortcodeSearchResults()->generateShortcode();
		$id = $this->createPage($title, $content);
		if (!is_wp_error($id)) {
			MPHB()->getSettings()->setSearchResultsPage($id);
		}	
	}
	
	public function addRoomsPage(){
		$title = __('Rooms', 'motopress-hotel-booking');
		$content = MPHB()->getShortcodeRooms()->generateShortcode();
		$this->createPage($title, $content);
	}

	public function addCheckoutPage(){
		$title = __('Checkout', 'motopress-hotel-booking');
		$content = MPHB()->getShortcodeCheckout()->generateShortcode();
		$id = $this->createPage( $title, $content);
		if (!is_wp_error($id)) {
			MPHB()->getSettings()->setCheckoutPage($id);
		}		
	}

	public function addRoomTypeCategories(){

		$terms = array(
			'single' => array(
				'name'			=> __('Single', 'motopress-hotel-booking'),
			),
			'double' => array(
				'name'			=> __('Double', 'motopress-hotel-booking')
			),
			'triple' => array(
				'name'			=> __('Triple', 'motopress-hotel-booking')
			),
			'deluxe' => array(
				'name'			=> __('Deluxe', 'motopress-hotel-booking')
			),
			'honeymoon' => array(
				'name'			=> __('Honeymoon', 'motopress-hotel-booking')
			)
		);
		$terms = apply_filters('mphb_predefined_room_type_categories', $terms);

		foreach ($terms as $slug => $term) {
			wp_insert_term($term['name'], MPHB()->getRoomTypeCPT()->getCategoryTaxName(), array(
				'description' => isset($term['description']) ? $term['description'] : '',
				'slug' => $slug
			));
		}

	}

	public function createPage($title, $content){
		global $user_ID;
		$page = array(
			'post_type'		=> 'page',
			'post_parent'	=> 0,
			'post_author'	=> $user_ID,
			'post_status'	=> 'publish',
			'post_content'	=> $content,
			'post_title'	=> $title
		);
	
		$pageid = wp_insert_post($page);
		return $pageid;
	}

	public function pass(){
		update_option('mphb_wizard_passed', true);
	}

}