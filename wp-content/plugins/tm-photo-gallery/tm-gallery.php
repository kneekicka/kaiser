<?php

/**
 * Plugin Name: TM Photo Gallery
 * Plugin URI: http://www.templatemonster.com/
 * Description: TM Photo Gallery plugin organizes your images and allows to add responsive galleries to your site in 1 click.
 * Version: 1.1.0
 * Author: Template Monster
 * Author URI: http://www.templatemonster.com/
 * License: GPL3
 * Text Domain: tm_gallery
 * Domain Path: /languages
 */
use tm_photo_gallery\classes\Core;

defined( 'ABSPATH' ) or exit;
define( 'TM_PG_PHP_MIN_VERSION', '5.3.27' );
define( 'TM_PG_PHP_REC_VERSION', '5.6' );
define( 'TM_PG_MYSQL_MIN_VERSION', '5.0' );
define( 'TM_PG_MYSQL_REC_VERSION', '5.5' );
define( 'TM_PG_WORDPRESS_MIN_VERSION', '4.0' );
define( 'TM_PG_WORDPRESS_REC_VERSION', '4.3.1' );

define( 'TM_PG_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'TM_PG_CLASSES_PATH', TM_PG_PLUGIN_PATH . 'classes/' );
define( 'TM_PG_MODELS_PATH', TM_PG_CLASSES_PATH . 'models/' );
define( 'TM_PG_STRUCTURE_PATH', TM_PG_CLASSES_PATH . 'structure/' );
define( 'TM_PG_CONTROLLERS_PATH', TM_PG_CLASSES_PATH . 'controllers/' );
define( 'TM_PG_SHORTCODES_PATH', TM_PG_CLASSES_PATH . 'shortcodes/' );
define( 'TM_PG_PREPROCESSORS_PATH', TM_PG_CLASSES_PATH . 'preprocessors/' );
define( 'TM_PG_LIBS_PATH', TM_PG_CLASSES_PATH . 'lib/' );
define( 'TM_PG_CONFIGS_PATH', TM_PG_PLUGIN_PATH . 'configs/' );
define( 'TM_PG_MODULES_PATH', TM_PG_CLASSES_PATH . 'modules/' );
define( 'TM_PG_WIDGETS_PATH', TM_PG_CLASSES_PATH . 'widgets/' );
define( 'TM_PG_LANG_PATH', TM_PG_PLUGIN_PATH . 'languages/' );
define( 'TM_PG_TEMPLATES_PATH', TM_PG_PLUGIN_PATH . 'templates/' );

define( 'TM_PG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'TM_PG_MEDIA_URL', TM_PG_PLUGIN_URL . 'media/' );
define( 'TM_PG_JS_URL', TM_PG_MEDIA_URL . 'js/' );
define( 'TM_PG_CSS_URL', TM_PG_MEDIA_URL . 'css/' );
define( 'TM_PG_IMG_URL', TM_PG_MEDIA_URL . 'img/' );

register_activation_hook( __FILE__, array( 'TM_Photo_Gallery', 'on_activation' ) );
register_deactivation_hook( __FILE__, array( 'TM_Photo_Gallery', 'on_deactivation' ) );
register_uninstall_hook( __FILE__, array( 'TM_Photo_Gallery', 'on_uninstall' ) );
add_action( 'plugins_loaded', array( 'TM_Photo_Gallery', 'plugins_loaded' ) );

/**
 * TM_Photo_Gallery
 */
class TM_Photo_Gallery {

	/**
	 * instance
	 *
	 * @var type
	 */
	protected static $instance;

	/**
	 * init
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
	 * On load plugin
	 */
	public static function plugins_loaded() {
		self::include_all();
		Core::get_instance()->init();
	}

	/**
	 * On activation plugin
	 */
	public static function on_activation() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		// do_action('admin_notices');
		self::include_all();
		Core::get_instance()->activate_plugin();
	}

	/**
	 * On deactivation plugin
	 */
	public static function on_deactivation() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		Core::get_instance()->deactivate_plugin();
		flush_rewrite_rules();
	}

	/**
	 * On uninstall
	 */
	public static function on_uninstall() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		self::include_all();
		Core::get_instance()->delete_plugin();
		flush_rewrite_rules();
	}

	/**
	 * Inclde all
	 */
	static function include_all() {
		/**
		 * Install Fire bug
		 */
		require_once TM_PG_LIBS_PATH . 'FirePHPCore/fb.php';

		/**
		 * Include Gump Validator
		 */
		require_once TM_PG_LIBS_PATH . 'gump.class.php';

		/**
		 * Include state
		 */
		require_once TM_PG_CLASSES_PATH . 'class-state-factory.php';

		/**
		 * Include Core class
		 */
		require_once TM_PG_CLASSES_PATH . 'class-core.php';

		/**
		 * Include Model
		 */
		require_once TM_PG_CLASSES_PATH . 'class-model.php';

		/**
		 * Include Controller
		 */
		require_once TM_PG_CLASSES_PATH . 'class-controller.php';

		/**
		 * Include Preprocessor
		 */
		require_once TM_PG_CLASSES_PATH . 'class-preprocessor.php';

		/**
		 * Include Module
		 */
		require_once TM_PG_CLASSES_PATH . 'class-module.php';

		/**
		 * Include view
		 */
		require_once TM_PG_CLASSES_PATH . 'class-view.php';

		/**
		 * include shortcodes
		 */
		require_once TM_PG_CLASSES_PATH . 'class-shortcode.php';

		/**
		 * Include media
		 */
		require_once TM_PG_CLASSES_PATH . 'class-media.php';

		/**
		 * Include hooks
		 */
		require_once TM_PG_CLASSES_PATH . 'class-hooks.php';

		/**
		 * Include structure
		 */
		require_once TM_PG_CLASSES_PATH . 'class-structure.php';
	}
}
