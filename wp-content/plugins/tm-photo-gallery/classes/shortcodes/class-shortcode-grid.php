<?php
/**
 * Shortcode grid class
 *
 * @package classes/shortcodes
 */

namespace tm_photo_gallery\classes\shortcodes;

use tm_photo_gallery\classes\Shortcode;
use tm_photo_gallery\classes\structure\Gallery;
use tm_photo_gallery\classes\lib\FB;

/**
 * Class shortcode grid
 */
class Shortcode_Grid extends Shortcode {

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
	 * Set highest level breadcrumbs trail part
	 *
	 * @param int $id Gallery ID.
	 */
	public function set_breadcrumbs_trail( $id ) {
		$bc = isset( $_SESSION['tm-gallery-breadcrumbs'] ) ? $_SESSION['tm-gallery-breadcrumbs'] : array();
		$bc[ $id ] = array( 'page' => get_the_id() );
		$_SESSION['tm-gallery-breadcrumbs'] = $bc;
	}

	/**
	 * Show shortcode
	 *
	 * @param type $params
	 */
	public function show_shortcode( $params ) {

		if ( 'trash' === get_post_status( $params['id'] ) ) {
			return;
		}

		wp_cache_set( 'current_gallery', $params['id'], 'tm-gallery' );

		$this->set_breadcrumbs_trail( $params['id'] );

		$data = new Gallery(
			$params['id'],
			array( 'pagination', 'display', 'animation', 'lightbox', 'filter', 'grid', 'terms', 'img_count' )
		);

		return $this->get_view()->render_action_html( 'frontend/grid/index', $data, false );
	}
}
