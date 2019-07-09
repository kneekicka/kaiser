<?php

class MPHBMailer {

	/**
	 *
	 * @var MPHBEmailTemplater
	 */
	private $templater;

	public function __construct() {
		$this->templater = new MPHBEmailTemplater();
	}

	/**
	 * Send an email.
	 * @param string $to
	 * @param string $subject
	 * @param string $message
	 * @param string $headers Optional.
	 * @param string $attachments Optional.
	 * @return bool success
	 */
	public function send( $to, $subject, $message, $headers = null, $attachments = null ) {

		add_filter( 'wp_mail_from', array( $this, 'filterFromEmail' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'filterFromName' ) );
		add_filter( 'wp_mail_content_type', array( $this, 'filterContentType' ) );

		$result  = wp_mail( $to, $subject, $message, $headers, $attachments );

		remove_filter( 'wp_mail_from', array( $this, 'filterFromEmail' ) );
		remove_filter( 'wp_mail_from_name', array( $this, 'filterFromName' ) );
		remove_filter( 'wp_mail_content_type', array( $this, 'filterContentType' ) );

		return $result;
	}

	/**
	 * Filter the from name for outgoing emails.
	 * @return string
	 */
	public function filterFromName( $fromName ){
		return wp_specialchars_decode( esc_html( MPHB()->getEmailSettings()->getFromName() ), ENT_QUOTES );
	}

	/**
	 * Filter the from address for outgoing emails.
	 * @return string
	 */
	public function filterFromEmail( $fromAddress ){
		return sanitize_email( MPHB()->getEmailSettings()->getFromEmail() );
	}

	/**
	 * Filter email content type.
	 *
	 * @return string
	 */
	public function filterContentType( $contentType ){
		return 'text/html';
	}

	public function getTemplater(){
		return $this->templater;
	}

}