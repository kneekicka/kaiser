<?php
/**
 * File core
 *
 * @package classes
 */

namespace tm_photo_gallery\classes;

use tm_photo_gallery\classes\Model;
use tm_photo_gallery\classes\Controller;
use tm_photo_gallery\classes\Preprocessor;
use tm_photo_gallery\classes\State_Factory;
use tm_photo_gallery\classes\Module;
use tm_photo_gallery\classes\Hooks;
use tm_photo_gallery\classes\Media;
use tm_photo_gallery\classes\Shortcode;
use tm_photo_gallery\classes\lib\FB;

/**
 * Core class
 */
class Core {

	private $type;
	private $version;

	const PREFIX     = 'tm_pg_';
	const CSS_PREFIX = 'tm-pg-';

	// post types names
	public static $post_types;
	// Taxonome names
	public static $tax_names;

	const ACTION = 'tm_pg_action';

	/**
	 * Instance
	 *
	 * @var type
	 */
	protected static $instance;

	/**
	 * Current state
	 */
	private $state;

	/**
	 * Construct
	 */
	public function __construct() {

		$this->init_plugin_version();

		// set post types
		self::$post_types = array(
			'album'   => self::PREFIX . 'album',
			'set'     => self::PREFIX . 'set',
			'gallery' => self::PREFIX . 'gallery',
			'image'   => 'attachment',
		);

		// set tax names
		self::$tax_names = array(
			'category' => self::PREFIX . 'category',
			'tag'      => self::PREFIX . 'post_tag',
		);
	}

	/**
	 * @return Core
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Get version
	 *
	 * @return type
	 */
	public function get_version() {

		return $this->version;
	}

	/**
	 * Call magic invoke method
	 *
	 * @param bool|false $type - type.
	 * @param bool|false $call_preprocessor - call_preprocessor.
	 *
	 * @return bool|mixed
	 */
	public function __invoke( $type = false, $call_preprocessor = false ) {

		$state = false;
		if ( $type ) {
			if ( $call_preprocessor ) {
				$state = $this->get_preprocessor( $type );
			} else {
				$state = $this->get_model( $type );
			}
		}
		return $state;
	}

	/**
	 * Is gallery
	 */
	public function is_gallery() {
		$post	 = get_post();
		$status	 = true;
		if ( empty( $post ) ) {
			$status = false;
		} else {
			if ( in_array( $post->post_type, self::$post_types ) ) {
				$status = true;
			} else {
				$status = false;
			}
		}
		return $status;
	}

	/**
	 * Get State
	 *
	 * @return State
	 */
	public function get_state() {

		if ( $this->state ) {
			return $this->state;
		}
	}

	/**
	 * Get controller
	 *
	 * @param type $type
	 *
	 * @return boolean
	 */
	public function get_controller( $type ) {
		return Core::get_instance()->get_state()->get_controller( $type );
	}

	/**
	 * Get view
	 *
	 * @return type
	 */
	public function get_view() {
		return View::get_instance();
	}

	/**
	 * Check and return current state
	 *
	 * @param string $type
	 *
	 * @return boolean
	 */
	public function get_model( $type = null ) {
		return Core::get_instance()->get_state()->get_model( $type );
	}

	/**
	 * Get preprocessor
	 *
	 * @param $type
	 *
	 * @return mixed
	 */
	public function get_preprocessor( $type = null ) {
		return Core::get_instance()->get_state()->get_preprocessor( $type );
	}

	/**
	 * Set state
	 *
	 * @param Core $state
	 */
	public function set_state( $state ) {
		$this->state = $state;
	}

	/**
	 * Include all files from folder
	 *
	 * @param string  $folder
	 * @param boolean $inFolder
	 */
	static function include_all( $folder, $inFolder = true ) {
		if ( file_exists( $folder ) ) {
			$includeArr = scandir( $folder );
			foreach ( $includeArr as $include ) {
				if ( ! is_dir( $folder . '/' . $include ) ) {
					include_once $folder . '/' . $include;
				} else {
					if ( $include != '.' && $include != '..' && $inFolder ) {
						Core::include_all( $folder . '/' . $include );
					}
				}
			}
		}
	}

	/**
	 * Get plugin version
	 *
	 * @param $name
	 *
	 * @return array
	 */
	public function init_plugin_version() {
		$filePath = TM_PG_PLUGIN_PATH . 'tm-photo-gallery.php';
		if ( ! $this->version && file_exists( $filePath ) && function_exists( 'get_plugin_data' ) ) {
			$pluginObject	 = get_plugin_data( $filePath );
			$this->version	 = $pluginObject['Version'];
		}
	}

	/**
	 * Get plugin type
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Escape attributes with input value ttype checking
	 *
	 * @param  mixed $value Input value.
	 * @return mixed
	 */
	public function esc_attr( $value ) {

		if ( is_array( $value ) ) {
			return array_map( array( $this, 'esc_attr' ), $value );
		} else {
			return esc_attr( $value );
		}

	}

	/**
	 * Init current plugin
	 */
	public function init() {
		// init error handler
		ob_start( 'tm_photo_gallery\classes\Preprocessor::fatal_error_handler' );
		// load text domain
		load_plugin_textdomain( 'tm-gallery', false, TM_PG_PLUGIN_PATH . 'languages/' );
		// run session
		if ( ! session_id() ) {
			session_start();
		}
		// Include structure
		self::include_all( TM_PG_STRUCTURE_PATH );
		// include plugin models files
		Model::install();
		// include plugin controllers files
		Controller::get_instance()->install();
		// include plugin Preprocessors files
		Preprocessor::install();
		// include plugin Modules files
		Module::install();
		// inclide all widgets
		// Widget::install();
		// include all shortcodes
		Shortcode::install();
		// install state
		$this->install_state();
		// init all hooks
		Hooks::get_instance()->install_hooks();
	}

	/**
	 * Get post meta
	 *
	 * @param  type $id
	 * @param  type $key
	 * @return type
	 */
	public function get_post_meta( $id, $key = '', $single = true ) {
		return get_post_meta( $id, self::PREFIX . $key, $single );
	}

	/**
	 * Update post meta
	 *
	 * @param type $id
	 * @param type $key
	 * @param type $value
	 */
	public function update_post_meta( $id, $key, $value ) {
		return update_post_meta( $id, self::PREFIX . $key, $value );
	}

	/**
	 * Add post meta
	 *
	 * @param type $id
	 * @param type $key
	 * @param type $value
	 * @return type
	 */
	public function add_post_meta( $id, $key, $value ) {
		return add_post_meta( $id, self::PREFIX . $key, $value );
	}

	/**
	 * Delete post meta
	 *
	 * @param type $id
	 * @param type $key
	 * @param type $value
	 * @return type
	 */
	public function delete_post_meta( $id, $key, $value = '' ) {
		return delete_post_meta( $id, self::PREFIX . $key, $value );
	}

	/**
	 * Activate plugin
	 */
	public function activate_plugin() {
		// include plugin models files
		Model::install();
		// include plugin Preprocessors files
		Preprocessor::install();
		// install state
		self::install_state();
		$args = $this( 'media' )->get_content_params( array( 'fields' => 'ids' ) );
		$this( 'image' )->add_image_sizes();
		if ( $args ) {
			$posts = get_posts( $args );
			foreach ( $posts as $id ) {
				$captured	 = $this->get_post_meta( $id, 'captured', true );
				$uploaded	 = $this->get_post_meta( $id, 'uploaded', true );
				if ( ! $captured || ! $uploaded ) {
					$post = get_post( $id );
					$this->update_post_meta( $id, 'captured', strtotime( $post->post_date ) );
					$this->update_post_meta( $id, 'uploaded', strtotime( $post->post_date ) );
				}
			}
		}

		flush_rewrite_rules();
		update_option( 'tm_pg_first_activated', 1 );
	}

	/**
	 * Deactivate plugin
	 */
	public function deactivate_plugin() {
		update_option( 'tm_pg_first_activated', 0 );
		update_option( 'tm_pg_plugin_notice', 0 );
	}

	/**
	 * Delete plugin
	 */
	public function delete_plugin() {
		// include plugin models files
		Model::install();
		// include plugin Modules files
		Module::install();
		// install state
		self::install_state();
		// include plugin Preprocessors files
		Preprocessor::install();
		// register custom post type and taxonomyes
		Media::get_instance()->register_all_post_type();
		Media::get_instance()->register_all_taxonomies();
		// delete options
		delete_option( 'tm_pg_first_activated' );
		delete_option( 'tm_pg_plugin_notice' );
		// delete attachment post meta
		$args = $this( 'media' )->get_content_params( array( 'fields' => 'ids' ) );
		if ( $args ) {
			$posts = get_posts( $args );
			foreach ( $posts as $id ) {
				$this->delete_post_meta( $id, 'focal_point' );
				$this->delete_post_meta( $id, 'captured' );
				$this->delete_post_meta( $id, 'uploaded' );
				$this->delete_post_meta( $id, 'favorite' );
				$this( 'term' )->delete_term( array(
					'type'	 => self::$tax_names['images'],
					'value'	 => $id,
				) );
			}
		}
		// delete all albums
		$args = $this( 'album' )->get_content_params( array( 'fields' => 'ids' ) );
		if ( $args ) {
			$posts = get_posts( $args );
			foreach ( $posts as $id ) {
				$this( 'media' )->delete_post( array( 'id' => $id ) );
			}
		}
		// delete all sets
		$args = $this( 'set' )->get_content_params( array( 'fields' => 'ids' ) );
		if ( $args ) {
			$posts = get_posts( $args );
			foreach ( $posts as $id ) {
				$this( 'media' )->delete_post( array( 'id' => $id ) );
			}
		}
		// deleta all galleys
		$args = $this( 'gallery' )->get_content_params( array( 'fields' => 'ids' ) );
		if ( $args ) {
			$posts = get_posts( $args );
			foreach ( $posts as $id ) {
				$this( 'media' )->delete_post( array( 'id' => $id ) );
			}
		}
		// delete all short codes
		$pages = get_posts( array(
			'post_type'		 => array( 'post', 'page' ),
			'post_status'	 => 'any',
			'posts_per_page' => -1,
		) );
		foreach ( $pages as $post ) {
			if ( preg_match( '/\[(.*)tm-gallery(.*)\]/', $post->post_content ) ) {
				wp_update_post(
				array(
					'ID'			 => $post->ID,
					'post_content'	 => preg_replace( '/\[(.*)tm-gallery(.*)\]/', '', $post->post_content ),
				)
				);
			}
		}
	}

	/**
	 * install current state
	 */
	public function install_state() {
		Core::get_instance()->set_state( new State_Factory() );
	}

	/**
	 * Route admin plugin url
	 */
	public function wp_admin_ajax_route_url() {
		// check current_user_can
		if ( ! current_user_can( 'manage_options' ) ) {
			die( 'Current user can`t use plugin!' );
		}

		$this->validate_nonce();

		$controller	 = isset( $_POST['controller'] ) ? esc_attr( $_POST['controller'] ) : null;
		$action		 = isset( $_POST[ self::ACTION ] ) ? esc_attr( $_POST[ self::ACTION ] ) : null;
		if ( ! empty( $action ) ) {
			// call controller
			Preprocessor::get_instance()->call_controller( $controller );
		}
	}

	/**
	 * Route frontend plugin url
	 */
	public function wp_frontend_ajax_route_url() {

		$this->validate_nonce();

		$controller	 = isset( $_POST['controller'] ) ? esc_attr( $_POST['controller'] ) : null;
		$action		 = isset( $_POST[ self::ACTION ] ) ? esc_attr( $_POST[ self::ACTION ] ) : null;
		if ( ! empty( $action ) && 'grid' == $controller ) {
			// call controller
			Preprocessor::get_instance()->call_controller( $controller );
		}
	}

	/**
	 * Validate nonce before form processing
	 *
	 * @return void
	 */
	public function validate_nonce() {

		// check ajax nonce
		if ( empty( $_POST['tm_pg_nonce'] ) ) {
			die( 'Ajax nonce error!' );
		}

		if ( ! wp_verify_nonce( esc_attr( $_POST['tm_pg_nonce'] ), 'tm_pg_nonce' ) ) {
			die( 'Ajax nonce error!' );
		}

	}

	/**
	 * Do action
	 *
	 * @param type $name
	 */
	public static function do_action( $name, $arg = '' ) {
		return do_action( self::CSS_PREFIX . $name, $arg );
	}

	/**
	 * Add action
	 *
	 * @param  type $name
	 * @param  type $function
	 * @param  type $type
	 * @return type
	 */
	public static function add_action( $name, $function = false, $type = false,
									$priority = 10, $accepted_args = 1 ) {
		if ( ! $function ) {
			$function = str_replace( '-', '_', $name );
		}
		if ( $type ) {
			if ( 'grid' == $type ) {
				$type = 'tm_photo_gallery\classes\frontend\\' . ucfirst( $type );
			} else {
				$type = 'tm_photo_gallery\classes\models\\' . ucfirst( $type );
			}
			if ( class_exists( $type ) && method_exists( $type::get_instance(), $function ) ) {
				$function = array( $type::get_instance(), $function );
			}
		}
		if ( ! is_array( $function ) && ! function_exists( $function ) ) {
			$action = false;
		} else {
			$action = add_action( self::CSS_PREFIX . $name, $function, $priority, $accepted_args );
		}
		return $action;
	}

	/**
	 * Apply filters
	 */
	public static function apply_filters( $name, $value ) {
		return apply_filters( self::CSS_PREFIX . $name, $value );
	}

	/**
	 * Check for ajax post
	 *
	 * @return type
	 */
	static function is_ajax() {
		$return = false;
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$return = true;
		}
		return $return;
	}
}
