<?php
/**
 * Structure
 *
 * @package classes
 */

namespace tm_photo_gallery\classes;

use tm_photo_gallery\classes\Core;
use tm_photo_gallery\classes\structure\Grid;
use tm_photo_gallery\classes\structure\Filter;
use tm_photo_gallery\classes\structure\Display;
use tm_photo_gallery\classes\structure\Lightbox;
use tm_photo_gallery\classes\structure\Animation;
use tm_photo_gallery\classes\structure\Pagination;
use tm_photo_gallery\classes\lib\FB;

/**
 * Description of Structure
 *
 * @author gellios3
 */
class Structure {

	/**
	 * ID
	 *
	 * @var type
	 */
	public $id;

	/**
	 * Date
	 *
	 * @var type
	 */
	public $date;

	/**
	 * Post
	 *
	 * @var type
	 */
	public $post;

	/**
	 * Grid
	 *
	 * @var type
	 */
	public $grid;

	/**
	 * Filter
	 *
	 * @var type
	 */
	public $filter;

	/**
	 * pagination
	 *
	 * @var type
	 */
	public $pagination;

	/**
	 * Construct
	 *
	 * @param type $id
	 */
	public function __construct( $id ) {
		$this->id	 = $id;
		$this->post	 = get_object_vars( get_post( $id ) );
		$this->date	 = $this->get_date();
		$this->set_gallery_folder();
	}

	/**
	 * Get display
	 */
	protected function get_display() {
		$display = new Display( $this->id );
		return get_object_vars( $display );
	}

	/**
	 * Get pagination
	 *
	 * @return type
	 */
	protected function get_pagination() {
		$pagination = new Pagination( $this->id );
		return get_object_vars( $pagination );
	}

	/**
	 * Get animation
	 */
	protected function get_animation() {
		$animation = new Animation( $this->id );
		return get_object_vars( $animation );
	}

	/**
	 * Get lightbox
	 */
	protected function get_lightbox() {
		$lightbox = new Lightbox( $this->id );
		return get_object_vars( $lightbox );
	}

	/**
	 * Get filter
	 */
	protected function get_filter() {
		$filter = new Filter( $this->id );
		return get_object_vars( $filter );
	}

	/**
	 * Get date
	 *
	 * @return type
	 */
	protected function get_date() {
		return mysql2date( esc_attr__( 'F j, Y' ), $this->post['post_date'] );
	}

	/**
	 * Get grid
	 *
	 * @return type
	 */
	protected function get_grid() {
		$grid = new Grid( $this->id );
		return get_object_vars( $grid );
	}

	/**
	 * Get model
	 *
	 * @param type $type
	 * @return type
	 */
	protected function model( $type ) {
		return Core::get_instance()->get_model( $type );
	}

	/**
	 * Get post meta
	 *
	 * @param type $key
	 * @return type
	 */
	protected function get_post_meta( $key, $single = true ) {
		return $this->model( 'model' )->get_post_meta( $this->id, $key, $single );
	}

	/**
	 * Update post meta
	 *
	 * @param type $key
	 * @param type $value
	 * @return type
	 */
	protected function update_post_meta( $key, $value ) {
		return $this->model( 'model' )->$this->update_post_meta( $this->id, $key, $value );
	}

	/**
	 * Set gallery folder
	 *
	 * @return string
	 */
	protected function set_gallery_folder() {
		$folder = $this->post['post_type'];

		if ( 'tm_pg_set' === $folder ) {
			$folder = 'set';
		} else if ( 'tm_pg_album' === $folder ) {
			$folder = 'album';
		} else {
			$folder = 'gallery';
		}

		$this->post['gallery_folder'] = $folder;
	}

	/**
	 * Try to get gallery ID from query
	 *
	 * @return int|bool
	 */
	public function get_gallery_id() {

		$gallery = get_query_var( 'parent_gallery' );
		$id      = false;

		if ( ! $gallery ) {
			return false;
		}

		if ( is_numeric( $gallery ) ) {
			$id = $gallery;
		} else {
			global $wpdb;
			$id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type= %s",
					$gallery, Core::$post_types['gallery']
				)
			);
			$id = intval( $id );
		}

		if ( $id ) {
			return $id;
		} else {
			return false;
		}

	}
}
