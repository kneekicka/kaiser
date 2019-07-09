<?php
/**
 * Controller frontend Grid
 *
 * @package classes/controllers
 */

namespace tm_photo_gallery\classes\controllers;

use tm_photo_gallery\classes\frontend\Grid;
use tm_photo_gallery\classes\structure\Gallery as Single_Gallery;
use tm_photo_gallery\classes\lib\FB;

/**
 * Controller frontend grid
 */
class Controller_Grid extends \tm_photo_gallery\classes\Controller {

	/**
	 * Intence
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
	 * Action filter
	 */
	public function action_filter_grid() {

		wp_cache_set( 'current_gallery', intval( $_POST['id'] ), 'tm-gallery' );

		$gallery = new Single_Gallery( intval( $_POST['id'] ), array(
			'pagination',
			'filter',
			'grid',
			'terms',
			'img_count',
			'display',
		) );
		if ( ! empty( $_POST['term_id'] ) && $_POST['term_id'] != 'all' ) {
			$gallery->posts = $gallery->sort_gallery_by_term_id( esc_attr( $_POST['term_id'] ), true );
		}
		$html = Grid::get_instance()->render_grid_html( $gallery );
		$this->send_json( $this( 'model' )->get_arr( $html, true ) );
	}

	/**
	 * Action get content
	 */
	public function action_get_content() {

		wp_cache_set( 'current_gallery', intval( $_POST['id'] ), 'tm-gallery' );

		$gallery = new Single_Gallery( intval( $_POST['id'] ), array(
			'pagination',
			'filter',
			'grid',
			'terms',
			'img_count',
			'display',
		) );
		$gallery->pagination['offset']	 = intval( $_POST['offset'] );
		if ( ! empty( $_POST['term_id'] ) && $_POST['term_id'] != 'all' ) {
			$gallery->posts = $gallery->sort_gallery_by_term_id( esc_attr( $_POST['term_id'] ), true );
		}
		$html = Grid::get_instance()->render_grid_html( $gallery );
		$this->send_json( $this( 'model' )->get_arr( $html, true ) );
	}

	/**
	 * Action get pagination
	 */
	public function action_get_pagination() {

		wp_cache_set( 'current_gallery', intval( $_POST['id'] ), 'tm-gallery' );

		$gallery = new Single_Gallery( intval( $_POST['id'] ), array(
			'pagination',
			'filter',
			'grid',
			'terms',
			'img_count',
			'display',
		) );
		if ( ! empty( $_POST['term_id'] ) && $_POST['term_id'] != 'all' ) {
			$gallery->posts = $gallery->sort_gallery_by_term_id( esc_attr( $_POST['term_id'] ) );
		}
		// get pagination html
		$html = $this->get_view()->render_html( 'frontend/grid/pagination', array(
			'count'		 => $gallery->get_pagination_count(),
			'current'	 => 0,
		), false );
		$this->send_json( $this( 'model' )->get_arr( $html, true ) );
	}
}
