<?php
/**
 * Widget module
 *
 * @package classes/modules
 */

namespace tm_photo_gallery\classes\modules;

use tm_photo_gallery\classes\Module;

/**
 * Widget module
 */
class Widget extends Module {

	/**
	 * Instance
	 *
	 * @var type
	 */
	protected static $instance;

	/**
	 * Get instance
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
	 * Include all widgets
	 */
	public static function install() {
		self::include_all( TM_PG_WIDGETS_PATH );
	}

	public function register() {
		register_widget( 'tm_photo_gallery\classes\widgets\Gallery_widget' );
	}
}
