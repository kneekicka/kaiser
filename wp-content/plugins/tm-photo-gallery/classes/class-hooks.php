<?php
/**
 * Hooks
 *
 * @package classes
 */

namespace tm_photo_gallery\classes;

use tm_photo_gallery\classes\Core;
use tm_photo_gallery\classes\Media;
use tm_photo_gallery\classes\frontend\Grid;
use tm_photo_gallery\classes\View;
use tm_photo_gallery\classes\lib\FB;

/**
 * Class Hooks
 */
class Hooks extends Core {

	/**
	 * instance
	 *
	 * @var type
	 */
	protected static $instance;

	/**
	 * get_instance
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
	 * Init all hooks in projects
	 */
	public function install_hooks() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );

		// add ajax request
		add_action( 'wp_ajax_tm_pg', array( Core::get_instance(), 'wp_admin_ajax_route_url' ) );
		add_action( 'wp_ajax_nopriv_tm_pg', array( Core::get_instance(), 'wp_admin_ajax_route_url' ) );
		add_action( 'wp_ajax_tm_pg_f', array( Core::get_instance(), 'wp_frontend_ajax_route_url' ) );
		add_action( 'wp_ajax_nopriv_tm_pg_f', array( Core::get_instance(), 'wp_frontend_ajax_route_url' ) );
		// chery breadcrumbs
		add_filter( 'cherry_breadcrumbs_custom_trail', array( Grid::get_instance(), 'cherry_breadcrumbs' ), 10, 2 );
		// add ajax callback
		if ( ! $this->is_ajax() ) {
			// in load theme
			add_action( 'wp_head', array( Media::get_instance(), 'wp_head' ) );
			// add styles in load theme
			add_action( 'wp_enqueue_scripts', array( Media::get_instance(), 'add_shortcode_css' ) );
			// Load current admin screen
			add_action( 'admin_enqueue_scripts', array( Media::get_instance(), 'admin_enqueue_scripts' ) );
			// Menu init
			add_action( 'admin_menu', array( Media::get_instance(), 'admin_menu' ) );
			// widgets init
			// add_action('widgets_init', array(Widget::get_instance(), 'register'));
			// get archives by custom post type
			add_action( 'admin_notices', array( $this, 'install_plugin_notice' ) );
		}
	}

	/**
	 * Hooks for admin panel
	 */
	public function admin_init() {
		if ( ! $this->is_ajax() ) {
			// add buttons to mce
			add_filter( 'mce_external_plugins', array( Media::get_instance(), 'mce_external_plugins' ) );
			add_filter( 'mce_buttons', array( Media::get_instance(), 'mce_buttons' ) );
			add_action( 'admin_print_footer_scripts', array( View::get_instance(), 'admin_print_footer_scripts' ) );
		}
	}

	/**
	 * Init hook
	 */
	public function init() {

		// register attachmet sizes
		$this( 'image' )->add_image_sizes();
		// get sizes
		add_filter( 'tm_pg_get_sizes', array( $this( 'image' ), 'get_image_sizes' ), 10, 1 );
		add_filter( 'image_downsize', array( $this( 'image' ), 'image_downsize' ), 10, 3 );
		add_filter( 'get_object_terms', array( $this( 'term' ), 'get_object_terms' ), 10, 4 );
		// register custom post type and taxonomyes
		Media::get_instance()->register_all_post_type();
		Media::get_instance()->register_all_taxonomies();
		// Init sort codes
		Shortcode::get_instance()->init();
		// init grid actions
		Grid::init_actions();
		// include template
		add_filter( 'template_include', array( Media::get_instance(), 'template_include' ) );
		add_filter( 'template_redirect', array( Media::get_instance(), 'template_redirect' ), 5 );
		add_action( 'wp_footer', array( Media::get_instance(), 'output_into_footer' ), 999 );
		add_action( 'wp_footer', array( Grid::get_instance(), 'output_grid_style' ), 999 );

		add_filter( 'query_vars', array( $this, 'set_query_var' ) );
		add_filter( self::$post_types['album'] . '_rewrite_rules', array( $this, 'add_galery_parent' ) );
		add_filter( self::$post_types['set'] . '_rewrite_rules', array( $this, 'add_galery_parent' ) );

		//$this->set_rewrite();
	}

	/**
	 * Set additional rewrite rules
	 */
	public function set_rewrite() {

		// revrite rules for breadcrumbs
		add_rewrite_tag( '%post_parent%', '([0-9]+)' );
		add_rewrite_tag( '%set_parent%', '([0-9]+)' );
		add_rewrite_rule( 'tm_pg_set/([^/]+)/([0-9]+)/?$', 'index.php?post_type=tm_pg_set&name=$matches[1]&post_parent=$matches[2]', 'top' );
		add_rewrite_rule( 'tm_pg_album/([^/]+)/([0-9]+)/?$', 'index.php?post_type=tm_pg_album&name=$matches[1]&post_parent=$matches[2]', 'top' );
		add_rewrite_rule( 'tm_pg_album/([^/]+)/([0-9]+)/([0-9]+)/?$', 'index.php?post_type=tm_pg_album&name=$matches[1]&post_parent=$matches[2]&set_parent=$matches[3]', 'top' );
	}

	/**
	 * Add gallery parameter to allowed query variables
	 *
	 * @param array $vars  Default query parameters.
	 */
	public function set_query_var( $vars ){
		$vars[] = 'parent_gallery';
		return $vars;
	}

	/**
	 * Add gallery parent rewrite to rewrite rules
	 *
	 * @param array $rules Current rules array.
	 */
	public function add_galery_parent( $rules ) {

		$new_rules  = array();
		$shift_from = array( '$matches[3]', '$matches[2]', '$matches[1]' );
		$shift_to   = array( '$matches[4]', '$matches[3]', '$matches[2]' );

		foreach ( $rules as $regex => $replace ) {
			$new_regex   = 'gallery/([^/]+)/' . $regex;
			$new_replace = str_replace( $shift_from, $shift_to, $replace ) . '&parent_gallery=$matches[1]';
			$new_rules[ $new_regex ] = $new_replace;
		}

		return array_merge( $new_rules, $rules );

	}

	/**
	 * Install plugin notice
	 */
	public static function install_plugin_notice() {
		global $wp_version;
		global $wpdb;
		$error	 = false;
		$message = '';

		$check_array				 = array(
			'php_min'	 => true,
			'mysql_min'	 => true,
			'wp_min'	 => true,
		);
		// get min versions
		$check_array['php_min']		 = version_compare( TM_PG_PHP_MIN_VERSION, phpversion(), '<=' );
		$check_array['mysql_min']	 = version_compare( TM_PG_MYSQL_MIN_VERSION, $wpdb->db_version(), '<=' );
		$check_array['wp_min']		 = version_compare( TM_PG_WORDPRESS_MIN_VERSION, $wp_version, '<=' );
		$status						 = get_option( 'tm_pg_plugin_notice' );
		// Only for activate
		if ( empty( $status ) ) {
			foreach ( $check_array as $key => $value ) {
				if ( ! $value ) {
					switch ( $key ) {
						case'php_min':
							$error	 = true;
							$message = esc_attr__( 'Minimal version of PHP ', 'tm_gallery' ) . TM_PG_PHP_MIN_VERSION . '. ';
							View::get_instance()->render_html( 'notice/admin_error', array( 'message' => $message ), true );
							break;
						case'mysql_min':
							$error	 = true;
							$message = esc_attr__( 'Minimal version of MySQL ', 'tm_gallery' ) . TM_PG_MYSQL_MIN_VERSION . '. ';
							View::get_instance()->render_html( 'notice/admin_error', array( 'message' => $message ), true );
							break;
						case'wp_min':
							$error	 = true;
							$message = esc_attr__( 'Minimal version of WordPress ', 'tm_gallery' ) . TM_PG_WORDPRESS_MIN_VERSION . '. ';
							View::get_instance()->render_html( 'notice/admin_error', array( 'message' => $message ), true );
							break;
						default:
							break;
					}
				}
			}
		}
		if ( $error ) {
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
			update_option( 'tm_pg_plugin_notice', false );
			deactivate_plugins( TM_PG_PLUGIN_PATH . 'tm-photo-gallery.php', true );
		} else {
			update_option( 'tm_pg_plugin_notice', true );
		}
	}
}
