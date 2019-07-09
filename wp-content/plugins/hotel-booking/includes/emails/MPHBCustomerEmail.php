<?php

class MPHBCustomerEmail extends MPHBAbstractEmail {

	/**
	 *
	 * @param array $args
	 * @param string $args['id'] ID of Email.
	 * @param string $args['label'] Label.
	 * @param string $args['description'] Optional. Email description.
	 * @param string $args['default_subject'] Optional. Default subject of email.
	 * @param string $args['default_header_text'] Optional. Default text in header.
	 */
	public function __construct( $args ){
		parent::__construct( $args );
		add_action( 'mphb_generate_settings_customer_emails', array( $this, 'generateSettingsFields' ) );
	}

	/**
	 *
	 * @return string
	 */
	protected function getReceiver(){
		return $this->booking->getCustomer()->getEmail();
	}

	/**
	 *
	 * @param bool $isSended
	 */
	protected function log( $isSended ){

		if ( $isSended ) {
			$this->booking->addLog( sprintf( __( '"%s" mail was sent to customer.', 'motopress-hotel-booking' ), $this->label ) );
		} else {
			$this->booking->addLog( sprintf( __( '"%s" mail sending is failed.', 'motopress-hotel-booking' ), $this->label ) );
		}
	}

}
