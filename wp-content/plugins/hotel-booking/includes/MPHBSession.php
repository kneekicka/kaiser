<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MPHBSession {

	private $session = null;
	
	public function __construct() {
		define( 'MPHB_WP_SESSION_COOKIE', 'mphb_session' );
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}
	
	public function init() {
		$this->session = MPHB_WP_Session::get_instance();
		return $this->session;
	}

	/**
	 * Retrieve session ID
	 *
	 * @return string Session ID
	 */
	public function get_id() {
		return $this->session->session_id;
	}

	/**
	 * Retrieve a session variable
	 * 
	 * @param string $key Session key
	 * @return string|null Session variable if setted or null other way.
	 */
	public function get( $key ) {
		$key = sanitize_key( $key );
		return isset( $this->session[ $key ] ) ? maybe_unserialize( $this->session[ $key ] ) : null;
	}

	/**
	 * Set a session variable
	 *
	 * @param string $key Session key
	 * @param string $value Session variable
	 */
	public function set( $key, $value ) {
		$key = sanitize_key( $key );

		if ( is_array( $value ) ) {
			$this->session[ $key ] = serialize( $value );
		} else {
			$this->session[ $key ] = $value;
		}
		
	}
}