<?php
/**
 * Plugin Name: Power Builder Integrator
 * Plugin URI:
 * Description: Helps to integrate 3rd party plugins into Power Builder
 * Version:     1.0.1
 * Author:      Template Monster
 * Author URI:  http://www.templatemonster.com/
 * Text Domain: tm-builder-integrator
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'TM_Buider_Integrator' ) ) {

	/**
	 * Define TM_Buider_Integrator class.
	 */
	class TM_Buider_Integrator {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Supported plugins array.
		 *
		 * @since 1.0.0
		 * @var   array
		 */
		private $plugins = array();

		/**
		 * Supported shortcodes array.
		 *
		 * @since 1.0.0
		 * @var   array
		 */
		private $shortcodes = array();

		/**
		 * Holder for currently processed plugin.
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		private $processed_plugin = '';

		/**
		 * Holder for plugin folder URL.
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		private $plugin_url = '';

		/**
		 * Holder for plugin folder path.
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		private $plugin_dir = '';

		/**
		 * Holder for loader instance.
		 *
		 * @var string
		 */
		private $loader = '';

		/**
		 * Constructor for the class.
		 *
		 * @since 1.0.0
		 */
		function __construct() {
			add_action( 'wp_loaded', array( $this, 'config' ) );
			add_action( 'tm_builder_load_user_modules', array( $this, 'load_plugins' ) );
		}

		/**
		 * Sets supported plugins and shortcodes.
		 *
		 * @since 1.0.0
		 */
		public function config() {

			$this->plugins = array(
				'woocommerce'          => 'woocommerce.php',
				'booked'               => 'booked.php',
				'mp-restaurant-menu'   => 'restaurant-menu.php',
				'cherry-team-members'  => 'cherry-team-members.php',
				'cherry-testi'         => 'testimonials.php',
				'cherry-projects'      => 'cherry-projects.php',
				'cherry-services-list' => 'cherry-services-list.php',
			);

			$this->shortcodes = array(
				'woocommerce' => array(
					'product'                    => array( 'cb' => $this->get_cb( 'product' ) ),
					'product_page'               => array( 'cb' => $this->get_cb( 'product_page' ) ),
					'product_category'           => array( 'cb' => $this->get_cb( 'product_category' ) ),
					'product_categories'         => array( 'cb' => $this->get_cb( 'product_categories' ) ),
					'add_to_cart'                => array( 'cb' => $this->get_cb( 'add_to_cart' ) ),
					'add_to_cart_url'            => array( 'cb' => $this->get_cb( 'add_to_cart_url' ) ),
					'recent_products'            => array( 'cb' => $this->get_cb( 'recent_products' ) ),
					'sale_products'              => array( 'cb' => $this->get_cb( 'sale_products' ) ),
					'best_selling_products'      => array( 'cb' => $this->get_cb( 'best_selling_products' ) ),
					'top_rated_products'         => array( 'cb' => $this->get_cb( 'top_rated_products' ) ),
					'featured_products'          => array( 'cb' => $this->get_cb( 'featured_products' ) ),
					'product_attribute'          => array( 'cb' => $this->get_cb( 'product_attribute' ) ),
					'related_products'           => array( 'cb' => $this->get_cb( 'related_products' ) ),
					'shop_messages'              => array( 'cb' => $this->get_cb( 'shop_messages' ) ),
					'woocommerce_order_tracking' => array( 'cb' => $this->get_cb( 'woocommerce_order_tracking' ) ),
					'woocommerce_cart'           => array( 'cb' => $this->get_cb( 'woocommerce_cart' ) ),
					'woocommerce_checkout'       => array( 'cb' => $this->get_cb( 'woocommerce_checkout' ) ),
					'woocommerce_my_account'     => array( 'cb' => $this->get_cb( 'woocommerce_my_account' ) ),
				),
				'booked' => array(
					'booked-calendar'     => array( 'cb' => $this->get_cb( 'booked-calendar' ) ),
					'booked-appointments' => array( 'cb' => $this->get_cb( 'booked-appointments' ) ),
					'booked-login'        => array( 'cb' => $this->get_cb( 'booked-login' ) ),
				),
				'mp-restaurant-menu' => array(
					'mprm_items'      => array( 'cb' => $this->get_cb( 'mprm_items' ) ),
					'mprm_categories' => array( 'cb' => $this->get_cb( 'mprm_categories' ) ),
				),
				'cherry-team-members' => array(
					'cherry_team' => array( 'cb' => $this->get_cb( 'cherry_team' ) ),
				),
				'cherry-testi' => array(
					'tm_testimonials' => array( 'cb' => $this->get_cb( 'tm_testimonials' ) ),
				),
				'cherry-projects' => array(
					'cherry_projects' => array( 'cb' => $this->get_cb( 'cherry_projects' ) ),
					'cherry_projects_terms' => array( 'cb' => $this->get_cb( 'cherry_projects_terms' ) ),
				),
				'cherry-services-list' => array(
					'cherry_services' => array( 'cb' => $this->get_cb( 'cherry_services' ) ),
				),
			);

		}

		/**
		 * Get shortcode callback by tag.
		 *
		 * @since  1.0.0
		 * @param  string $tag Shortcode tag name.
		 * @return string|array
		 */
		public function get_cb( $tag ) {
			global $shortcode_tags;

			if ( ! isset( $shortcode_tags[ $tag ] ) ) {
				return false;
			}

			return $shortcode_tags[ $tag ];
		}

		/**
		 * Get supported plugins array.
		 *
		 * @since  1.0.0
		 * @return array
		 */
		public function get_plugins() {
			return $this->plugins;
		}

		/**
		 * Get plugin URL (or some plugin dir/file URL)
		 *
		 * @since  1.0.0
		 * @param  string $path Dir or file inside plugin dir.
		 * @return string
		 */
		public function plugin_url( $path = null ) {

			if ( ! $this->plugin_url ) {
				$this->plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );
			}

			if ( null != $path ) {
				return $this->plugin_url . $path;
			}

			return $this->plugin_url;
		}

		/**
		 * Get plugin dir path (or some plugin dir/file path)
		 *
		 * @since  1.0.0
		 * @param  string $path Dir or file inside plugin dir.
		 * @return string
		 */
		public function plugin_dir( $path = null ) {

			if ( ! $this->plugin_dir ) {
				$this->plugin_dir = trailingslashit( plugin_dir_path( __FILE__ ) );
			}

			if ( null != $path ) {
				return $this->plugin_dir . $path;
			}

			return $this->plugin_dir;
		}

		/**
		 * Get plugin shortcodes.
		 *
		 * @since  1.0.0
		 * @param  string $plugin Plugin name to get shortcodes for.
		 * @return array
		 */
		public function get_shortcodes( $plugin ) {

			if ( isset( $this->shortcodes[ $plugin ] ) ) {
				return $this->shortcodes[ $plugin ];
			}

			return array();
		}

		/**
		 * Load modules for supported plugins.
		 *
		 * @since 1.0.0
		 */
		public function load_plugins( $loader ) {

			if ( ! class_exists( 'Tm_Builder_Module' ) ) {
				return;
			}

			$this->loader = $loader;

			foreach ( $this->get_plugins() as $slug => $file ) {

				if ( ! $this->is_active_plugin( $slug, $file ) ) {
					continue;
				}

				$this->load_shortcodes( $slug );

			}

		}

		/**
		 * Load shortcodes for passed plugin.
		 *
		 * @since  1.0.0
		 * @param  string $slug Plugin slug.
		 */
		public function load_shortcodes( $slug ) {

			$this->processed_plugin = $slug;

			foreach ( $this->get_shortcodes( $slug ) as $shortcode => $data ) {

				$class = $this->get_classname( $shortcode );
				$path  = $this->get_shortcode_path( $shortcode );

				if ( ! file_exists( $path ) ) {
					continue;
				}

				include_once $path;
				$this->loader->add_module( $class, $path );

			}

			$this->processed_plugin = null;

		}

		/**
		 * Get path to shortcode by name.
		 * Work only if $this->processed_plugin is provided.
		 *
		 * @since  1.0.0
		 * @param  string $shortcode Shortcode name.
		 * @return string
		 */
		public function get_shortcode_path( $shortcode ) {

			if ( null === $this->processed_plugin ) {
				return '';
			}

			return $this->plugin_dir( 'plugins/' . $this->processed_plugin . '/' . $this->get_filename( $shortcode ) );

		}

		/**
		 * Get claa back name for current plugin and shortcode.
		 *
		 * @since  1.0.0
		 * @param  string $plugin    Plugin slug.
		 * @param  string $shortcode Shortcode slug.
		 * @return mixed
		 */
		public function get_shortcode_cb( $plugin, $shortcode ) {

			if ( ! $plugin || ! $shortcode ) {
				return false;
			}

			if ( ! isset( $this->shortcodes[ $plugin ][ $shortcode ] ) ) {
				return false;
			}

			$data = $this->shortcodes[ $plugin ][ $shortcode ];

			if ( ! isset( $data['cb'] ) ) {
				return false;
			}

			return $data['cb'];
		}

		/**
		 * Get classname for passed shortcode.
		 *
		 * @since  1.0.0
		 * @param  string $shortcode Shortcode slug.
		 * @return string
		 */
		public function get_classname( $shortcode ) {
			$shortcode = str_replace( array( '-', '_' ), array( ' ', ' ' ), $shortcode );

			return sprintf( 'Tm_Builder_%s', str_replace( ' ', '_', ucwords( $shortcode ) ) );
		}

		/**
		 * Get filename for current shortcode.
		 *
		 * @since  1.0.0
		 * @param  string $shortcode Shortcode which gets name for.
		 * @return string
		 */
		public function get_filename( $shortcode ) {
			return sprintf( 'tm-builder-module-%s.php', str_replace( '_', '-', $shortcode ) );
		}

		/**
		 * Check if is active plugin.
		 *
		 * @since  1.0.0
		 * @param  string  $slug Plugin slug (folder name).
		 * @param  string  $file Plugin file (main file name with extension).
		 * @return boolean
		 */
		public function is_active_plugin( $slug, $file ) {
			return in_array(
				sprintf( '%s/%s', $slug, $file ),
				apply_filters( 'active_plugins', get_option( 'active_plugins' ) )
			);
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}

}

/**
 * Returns instance of TM_Buider_Integrator
 *
 * @return object
 */
function tm_builder_integrator() {
	return TM_Buider_Integrator::get_instance();
}

tm_builder_integrator();
