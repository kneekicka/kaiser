<?php
/**
 * Controller class
 *
 * @package classes
 */

namespace tm_photo_gallery\classes;

use tm_photo_gallery\classes\Core as Core;
use tm_photo_gallery\classes\lib\FB as FB;

/**
 * Controller class
 */
class Controller extends Core {

	/**
	 * Instance
	 *
	 * @var type
	 */
	protected static $instance;

	/**
	 * Get_instance
	 *
	 * @return type
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Install controllers
	 */
	public function install() {
		// include all core controllers
		Core::include_all( TM_PG_CONTROLLERS_PATH );
	}

	/**
	 * Send json data
	 *
	 * @param array $data - data
	 */
	public function send_json( $data ) {
		if ( is_array( $data ) && isset( $data['success'] ) && ! $data['success'] ) {
			FB::error( $data, 'controler error' );
			wp_send_json_error( $data );
		} else {
			wp_send_json_success( $data['data'] );
		}
	}
}
