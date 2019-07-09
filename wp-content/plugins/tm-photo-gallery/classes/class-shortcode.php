<?php
/**
 * Shortcode class
 *
 * @package classes
 */

namespace tm_photo_gallery\classes;

use tm_photo_gallery\classes\Core;
use tm_photo_gallery\classes\shortcodes\Shortcode_Grid;
use tm_photo_gallery\classes\Media;

/**
 * Shortcode class
 */
class Shortcode extends Core {

	/**
	 * Version
	 *
	 * @var type
	 */
	private $version;

	/**
	 * Plugin URL
	 *
	 * @var type
	 */
	private $pluginURL;

	/**
	 * Plugin path
	 *
	 * @var type
	 */
	private $pluginPath;

	/**
	 * Instance
	 *
	 * @var type
	 */
	protected static $instance;

	/**
	 * Construct
	 */
	public function __construct() {
		parent::__construct();
		$this->version		 = $this->get_version();
		$this->pluginURL	 = TM_PG_PLUGIN_URL . 'shortcodes/';
		$this->pluginPath	 = TM_PG_PLUGIN_PATH . 'shortcodes/';
	}

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
	 * Install shortcodes
	 */
	public static function install() {
		self::include_all( TM_PG_SHORTCODES_PATH );
	}

	/**
	 * Init shortcode
	 */
	public function init() {
		add_shortcode( 'tm-pg-gallery', array( $this, 'init_shortcode' ) );
	}

	/**
	 * Show shortcode
	 *
	 * @param array $params
	 *
	 * @return type
	 */
	public function init_shortcode( $params ) {
		Media::get_instance()->init_shortcode( $params );
		return Shortcode_Grid::get_instance()->show_shortcode( $params );
	}
}
