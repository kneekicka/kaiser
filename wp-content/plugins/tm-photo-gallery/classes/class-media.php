<?php

/**
 * Media
 *
 * @package classes
 */

namespace tm_photo_gallery\classes;

use tm_photo_gallery\classes\Core;
use tm_photo_gallery\classes\modules\Post;
use tm_photo_gallery\classes\modules\Taxonomy;
use tm_photo_gallery\classes\modules\Menu;
use tm_photo_gallery\classes\frontend\Grid;
use tm_photo_gallery\classes\lib\FB;

/**
 * Media class
 */
class Media extends Core {

	/**
	 * instance
	 *
	 * @var type
	 */
	protected static $instance;

	/**
	 * access_admin_pages
	 *
	 * @var type
	 */
	private static $access_admin_pages = array(
		'toplevel_page_tm_pg_media',
		'post.php',
		'post-new.php',
		'tm-photo-gallery_page_gallery',
	);

	/**
	 * PREFIX
	 */
	const PREFIX = 'tm-pg-';

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
	 * Registered page in admin wp
	 */
	public function admin_menu() {
		$action = 'action_template';
		Menu::add_menu_page(
		array(
			'title'		 => esc_attr__( 'TM Photo Gallery', 'tm_gallery' ),
			'menu_slug'	 => 'tm_pg_media',
			'icon_url'	 => TM_PG_MEDIA_URL . 'img/icon.png',
			'capability' => 'manage_options',
			'position'	 => '10.5',
		)
		);
		Menu::add_submenu_page(
		array(
			'parent_slug'	 => 'tm_pg_media',
			'title'			 => esc_attr__( 'Media library', 'tm_gallery' ),
			'menu_slug'		 => 'tm_pg_media',
			'capability'	 => 'manage_options',
			'function'		 => array( $this->get_controller( 'media' ), $action ),
		)
		);
		Menu::add_submenu_page(
		array(
			'parent_slug'	 => 'tm_pg_media',
			'title'			 => esc_attr__( 'Galleries', 'tm_gallery' ),
			'menu_slug'		 => 'gallery',
			'capability'	 => 'manage_options',
			'function'		 => array( $this->get_controller( 'gallery' ), $action ),
		)
		);
	}

	/**
	 * Admin enqueue scripts
	 *
	 * @param type $hook
	 */
	public function admin_enqueue_scripts( $hook ) {
		$this->add_admin_css( $hook );
		$this->add_admin_js( $hook );
		// $this->add_admin_min_js( $hook );
	}

	/**
	 * Wp head
	 */
	public function wp_head() {
		$this->add_theme_js();
	}

	/**
	 * init shortcode media
	 *
	 * @param array $params
	 */
	public function init_shortcode( array $params ) {
		$this->add_shortcode_js( $params );
	}

	/**
	 * Add theme js
	 */
	private function add_theme_js() {
		global $post_type;
		if ( in_array( $post_type, self::$post_types ) ) {
			switch ( $post_type ) {
				case self::$post_types['album']:
				case self::$post_types['set']:
					$this->enqueue_script( 'registry-factory', 'lib/registry-factory.js' );
					$this->enqueue_script( 'lightgallery', 'frontend/lib/lightgallery.min.js' );
					$this->enqueue_script( 'lg-autoplay', 'frontend/lib/lg-autoplay.min.js' );
					$this->enqueue_script( 'lg-fullscreen', 'frontend/lib/lg-fullscreen.min.js' );
					$this->enqueue_script( 'lg-thumbnail', 'frontend/lib/lg-thumbnail.min.js' );
					$this->enqueue_script( 'constructor', 'frontend/grid/constructor.js' );
					$this->enqueue_script( 'frontend-grid', 'frontend/grid/grid.js' );
					wp_localize_script( self::PREFIX . 'frontend-grid', Core::PREFIX . 'options', include TM_PG_CONFIGS_PATH . 'options-js.php' );
					$this->enqueue_script( 'slider', 'frontend/grid/slider.js' );
					$this->enqueue_script( 'images-loaded', 'frontend/lib/imagesloaded.pkgd.min.js' );
					$this->enqueue_script( 'salvattore', 'frontend/lib/masonry.pkgd.min.js' );
					break;
			}
		}
	}

	/**
	 * Add admin js
	 *
	 * @param type $hook
	 */
	private function add_admin_js( $hook ) {
		if ( in_array( $hook, self::$access_admin_pages ) ) {
			// include wp scripts
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-effects-core' );
			wp_enqueue_script( 'jquery-effects-fade' );
			wp_enqueue_script( 'jquery-ui-draggable' );
			wp_enqueue_script( 'jquery-ui-droppable' );
			wp_enqueue_script( 'plupload-handlers' );
			wp_enqueue_script( 'wp-plupload' );
			wp_enqueue_script( 'wp-util' );
			wp_enqueue_script( 'media-editor' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'wp-color-picker' );
			// include global scripts
			$this->enqueue_script( 'registry-factory', 'lib/registry-factory.js' );
			// include admin script
			$this->enqueue_script( 'common', 'admin/common.js' );
			wp_localize_script( self::PREFIX . 'common', Core::PREFIX . 'admin_lang', require( TM_PG_CONFIGS_PATH . 'language-admin-js.php' ) );
			wp_localize_script( self::PREFIX . 'common', Core::PREFIX . 'options', require( TM_PG_CONFIGS_PATH . 'options-js.php' ) );
			// include constructor
			$this->enqueue_script( 'constructor', 'admin/constructor.js' );
			$this->enqueue_script( 'js-noty', 'admin/lib/jquery.noty.packaged.min.js' );
			$this->enqueue_script( 'js-noty-layout', 'admin/lib/tm-topRight.noty.min.js' );
			$this->enqueue_script( 'notification', 'admin/notification.js' );
			$this->enqueue_script( 'preloader', 'admin/preloader.js' );
			$this->enqueue_script( 'select2', 'admin/lib/select2.full.min.js' );
			$this->enqueue_script( 'accordion', 'admin/accordion.js' );
			// include admin script
			switch ( $hook ) {
				case 'post-new.php':
				case 'post.php':
					break;
				case 'toplevel_page_tm_pg_media':
					wp_enqueue_media();
					// include upload hooks
					$this->enqueue_script( 'uploader', 'admin/uploader.js' );
					$this->enqueue_script( 'grid', 'admin/photo-gallery/models/grid.js' );
					$this->enqueue_script( 'image', 'admin/photo-gallery/models/image.js' );
					$this->enqueue_script( 'tag', 'admin/photo-gallery/models/tag.js' );
					$this->enqueue_script( 'category', 'admin/photo-gallery/models/category.js' );
					$this->enqueue_script( 'folder', 'admin/photo-gallery/models/folder.js' );
					$this->enqueue_script( 'album', 'admin/photo-gallery/models/album.js' );
					$this->enqueue_script( 'set', 'admin/photo-gallery/models/set.js' );
					$this->enqueue_script( 'term', 'admin/photo-gallery/models/term.js' );
					$this->enqueue_script( 'cover', 'admin/photo-gallery/models/cover.js' );
					$this->enqueue_script( 'pg-editor', 'admin/photo-gallery/components/editor.js', array( 'jquery', 'underscore', 'backbone' ) );
					$this->enqueue_script( 'pg-folder-content', 'admin/photo-gallery/components/folder.js' );
					$this->enqueue_script( 'pg-popup', 'admin/photo-gallery/components/popup.js' );
					$this->enqueue_script( 'pg-right', 'admin/photo-gallery/components/right.js' );
					$this->enqueue_script( 'pg-top-bar', 'admin/photo-gallery/components/top-bar.js' );
					$this->enqueue_script( 'pg-content', 'admin/photo-gallery/components/content.js' );
					$this->enqueue_script( 'pg-media-popup', 'admin/photo-gallery/components/media-popup.js' );
					break;
				case 'tm-photo-gallery_page_gallery':
					$this->enqueue_script( "gallery", 'admin/gallery/list/models/gallery.js' );
					$this->enqueue_script( "grid", 'admin/gallery/list/models/grid.js' );
					$this->enqueue_script( "gl-editor-grid-settings", 'admin/gallery/editor/components/grid-settings.js' );
					$this->enqueue_script( "gl-editor-display", 'admin/gallery/editor/components/display.js' );
					$this->enqueue_script( "gl-editor-animations", 'admin/gallery/editor/components/animations.js' );
					$this->enqueue_script( "gl-editor-navigation", 'admin/gallery/editor/components/navigation.js' );
					$this->enqueue_script( "gl-editor-lightbox", 'admin/gallery/editor/components/lightbox.js' );
					$this->enqueue_script( "gl-editor-grid", 'admin/gallery/editor/components/grid.js' );
					$this->enqueue_script( "gl-right", 'admin/gallery/editor/components/right.js' );
					$this->enqueue_script( "gl-editor", 'admin/gallery/editor/editor.js' );
					$this->enqueue_script( "gl-top-bar", 'admin/gallery/list/components/top-bar.js' );
					$this->enqueue_script( "gl-editor-top-bar", 'admin/gallery/editor/components/top-bar.js' );
					$this->enqueue_script( "gl-content", 'admin/gallery/list/components/content.js' );
					break;
			}
		}
	}

	/**
	 * Add admin js
	 *
	 * @param type $hook
	 */
	private function add_admin_min_js( $hook ) {
		if ( in_array( $hook, self::$access_admin_pages ) ) {
			// include wp scripts
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-effects-core' );
			wp_enqueue_script( 'jquery-effects-fade' );
			wp_enqueue_script( 'jquery-ui-draggable' );
			wp_enqueue_script( 'jquery-ui-droppable' );
			wp_enqueue_script( 'plupload-handlers' );
			wp_enqueue_script( 'wp-plupload' );
			wp_enqueue_script( 'wp-util' );
			wp_enqueue_script( 'media-editor' );
			// include constructor
			$this->enqueue_script( 'js-noty', 'admin/lib/jquery.noty.packaged.min.js' );
			$this->enqueue_script( 'js-noty-layout', 'admin/lib/tm-topRight.noty.min.js' );
			$this->enqueue_script( 'select2', 'admin/lib/select2.full.min.js' );
			// include admin script
			switch ( $hook ) {
				case 'post-new.php':
				case 'post.php':
					// include global scripts
					$this->enqueue_script( 'registry-factory', 'lib/registry-factory.js' );
					// include admin script
					$this->enqueue_script( 'common', 'admin/common.js' );
					wp_localize_script( self::PREFIX . 'common', Core::PREFIX . 'admin_lang', require( TM_PG_CONFIGS_PATH . 'language-admin-js.php' ) );
					wp_localize_script( self::PREFIX . 'common', Core::PREFIX . 'options', require( TM_PG_CONFIGS_PATH . 'options-js.php' ) );
					break;
				case 'toplevel_page_tm_pg_media':
					wp_enqueue_media();
					$this->enqueue_script( 'photo-gallery', 'min/photo-gallery.min.js' );
					wp_localize_script( self::PREFIX . 'photo-gallery', Core::PREFIX . 'admin_lang', require( TM_PG_CONFIGS_PATH . 'language-admin-js.php' ) );
					wp_localize_script( self::PREFIX . 'photo-gallery', Core::PREFIX . 'options', require( TM_PG_CONFIGS_PATH . 'options-js.php' ) );
					break;
				case 'tm-photo-gallery_page_gallery':
					$this->enqueue_script( 'gallery', 'min/gallery.min.js' );
					wp_localize_script( self::PREFIX . 'gallery', Core::PREFIX . 'admin_lang', require( TM_PG_CONFIGS_PATH . 'language-admin-js.php' ) );
					wp_localize_script( self::PREFIX . 'gallery', Core::PREFIX . 'options', require( TM_PG_CONFIGS_PATH . 'options-js.php' ) );
					break;
			}
		}
	}

	/**
	 * Add admin css
	 *
	 * @param type $hook
	 */
	private function add_admin_css( $hook ) {
		if ( in_array( $hook, self::$access_admin_pages ) ) {

			wp_enqueue_style( 'wp-color-picker' );

			$this->enqueue_style( 'select2', 'admin/select2.min.css' );
			$this->enqueue_style( 'material-icons', 'material-icons.css' );
			$this->enqueue_style( 'dropdown', 'admin/dropdown.css' );
			$this->enqueue_style( 'perfect-scrollbar', 'admin/perfect-scrollbar.min.css' );
			$this->enqueue_style( 'animate-css', 'admin/animate.css' );
			$this->enqueue_style( 'accordion', 'accordion/main.css' );
			$this->enqueue_style( 'style-backend', 'admin/style-backend.css', array(
				self::PREFIX . 'select2',
				self::PREFIX . 'material-icons',
			) );
		}
	}

	/**
	 * Add shortcode css
	 *
	 * @param array $params
	 */
	public function add_shortcode_css() {
		$this->enqueue_style( 'font-awesome', 'frontend/font-awesome.min.css' );
		$this->enqueue_style( 'material-icons', 'material-icons.css' );
		$this->enqueue_style( 'fontello', 'frontend/fontello.css' );
		$this->enqueue_style( 'lightgallery', 'frontend/lightgallery.css' );
		$this->enqueue_style( 'style-frontend', 'frontend/style-frontend.css', array( self::PREFIX . 'material-icons' ) );
		$this->enqueue_style( 'grid', 'frontend/grid.css' );
	}

	/**
	 * Add shortcode js
	 *
	 * @param array $params - params.
	 */
	private function add_shortcode_js( array $params ) {
		if ( ! empty( $params['id'] ) ) {
			// include global scripts
			$this->enqueue_script( 'registry-factory', 'lib/registry-factory.js' );
			$this->enqueue_script( 'lightgallery', 'frontend/lib/lightgallery.min.js' );
			$this->enqueue_script( 'lg-autoplay', 'frontend/lib/lg-autoplay.min.js' );
			$this->enqueue_script( 'lg-fullscreen', 'frontend/lib/lg-fullscreen.min.js' );
			$this->enqueue_script( 'lg-thumbnail', 'frontend/lib/lg-thumbnail.min.js' );
			$this->enqueue_script( 'constructor', 'frontend/grid/constructor.js' );
			$this->enqueue_script( 'frontend-grid', 'frontend/grid/grid.js' );
			wp_localize_script( self::PREFIX . 'frontend-grid', Core::PREFIX . 'options', include TM_PG_CONFIGS_PATH . 'options-js.php' );
			$this->enqueue_script( 'slider', 'frontend/grid/slider.js' );
			$this->enqueue_script( 'images-loaded', 'frontend/lib/imagesloaded.pkgd.min.js' );
			//$this->enqueue_script( 'salvattore', 'frontend/lib/salvattore.min.js' );
			$this->enqueue_script( 'salvattore', 'frontend/lib/masonry.pkgd.min.js' );
		}
	}

	/**
	 * Register all post type
	 */
	public function register_all_post_type() {
		// register albums (sub-folder) post type
		Post::get_instance()->register_post_type(
		array(
			'post_type'	 => self::$post_types['album'],
			'titles'	 => array( 'many' => 'TM Albums', 'single' => 'TM Album' ),
			'supports'	 => array( 'title', 'editor', 'comments', 'page-attributes' ),
			'slug'		 => self::$post_types['album'],
			'taxonomies' => array( self::$tax_names['category'], self::$tax_names['tag'] ),
		)
		);
		// register sets (folder) post type
		Post::get_instance()->register_post_type(
		array(
			'post_type'	 => self::$post_types['set'],
			'titles'	 => array( 'many' => 'TM Sets', 'single' => 'TM Set' ),
			'supports'	 => array( 'title', 'editor', 'comments', 'page-attributes' ),
			'slug'		 => self::$post_types['set'],
			'taxonomies' => array( self::$tax_names['category'], self::$tax_names['tag'] ),
		)
		);
		// register gallery post type
		Post::get_instance()->register_post_type(
		array(
			'post_type'	 => self::$post_types['gallery'],
			'titles'	 => array( 'many' => 'TM Galleries', 'single' => 'TM Gallery' ),
			'supports'	 => array( 'title', 'editor', 'comments', 'page-attributes' ),
			'slug'		 => self::$post_types['gallery'],
			'taxonomies' => array( self::$tax_names['category'], self::$tax_names['tag'] ),
			'public'	 => false,
		)
		);
	}

	/**
	 * Register all taxonomies
	 */
	public function register_all_taxonomies() {
		// register sets (folder) taxonomy
		Taxonomy::get_instance()->register(
		array(
			'taxonomy'		 => self::$tax_names['tag'],
			'object_type'	 => array( self::$post_types['set'], self::$post_types['album'], 'attachment' ),
			'titles'		 => array( 'many' => 'Tags', 'single' => 'Tag' ),
			'slug'			 => self::$tax_names['tag'],
		)
		);
		// register albums (sub-folder) taxonomy
		Taxonomy::get_instance()->register(
		array(
			'taxonomy'		 => self::$tax_names['category'],
			'object_type'	 => array( self::$post_types['set'], self::$post_types['album'], 'attachment' ),
			'titles'		 => array( 'many' => 'Categories', 'single' => 'Category' ),
			'slug'			 => self::$tax_names['category'],
		)
		);
	}

	/**
	 * Template include
	 *
	 * @param string $template - template.
	 *
	 * @return string
	 */
	public function template_include( $template ) {
		global $post;
		if ( ! empty( $post ) && is_single() && in_array( get_post_type(), self::$post_types ) ) {
			if ( basename( $template ) != "single-{$post->post_type}.php" ) {
				$path = TM_PG_TEMPLATES_PATH . "single-{$post->post_type}.php";
				if ( file_exists( $path ) ) {
					$template = $path;
				}
			}
		}
		return $template;
	}

	/**
	 * Template redirect
	 */
	function template_redirect() {
		if ( get_option( 'permalink_structure' ) ) {
			$post_parent = get_query_var( 'post_parent' );
			$set_parent	 = get_query_var( 'set_parent' );
			if ( $post_parent || $set_parent ) {
				update_option( Core::PREFIX . 'post_parent', $post_parent );
				update_option( Core::PREFIX . 'set_parent', $set_parent );
				$name		 = get_query_var( 'name' );
				$post_type	 = get_query_var( 'post_type' );
				wp_redirect( home_url( "/$post_type/$name/" ) );
				exit();
			}
		}
	}

	/**
	 * Last request
	 */
	function output_into_footer() {
		delete_option( Core::PREFIX . 'post_parent' );
		delete_option( Core::PREFIX . 'set_parent' );
	}

	/**
	 * Connect js for MCE editor
	 *
	 * @param array $plugin_array - plugin_array.
	 *
	 * @return mixed
	 */
	public function mce_external_plugins( $plugin_array ) {
		$plugin_array['tm_photo_gallery'] = TM_PG_JS_URL . 'mce-media-buttons.js';
		return $plugin_array;
	}

	/**
	 * Add button in MCE editor
	 *
	 * @param $buttons
	 *
	 * @return mixed
	 */
	public function mce_buttons( $buttons ) {
		array_push( $buttons, 'add_tm_photo_gallery' );
		return $buttons;
	}

	/**
	 * Enqueue script
	 *
	 * @param  type $name
	 * @param  type $path
	 * @param  type $parent
	 * @param  type $version
	 * @return type
	 */
	public function enqueue_script( $name, $path, $parent = array( 'jquery', 'underscore' ), $version = false ) {
		if ( empty( $version ) ) {
			$version = $this->get_version();
		}
		// check url path
		if ( ! preg_match( '/^http/', $path ) ) {
			$path = TM_PG_JS_URL . $path;
		}
		return wp_enqueue_script( self::PREFIX . $name, $path, $parent, $version, true );
	}

	/**
	 * Enqueue style
	 *
	 * @param type $name
	 * @param type $path
	 * @param type $parent
	 */
	public function enqueue_style( $name, $path, $parent = array(), $version = false ) {
		if ( empty( $version ) ) {
			$version = $this->get_version();
		}
		// check url path
		if ( ! preg_match( '/^http/', $path ) ) {
			$path = TM_PG_CSS_URL . $path;
		}
		return wp_enqueue_style( self::PREFIX . $name, $path, $parent, $version );
	}
}
