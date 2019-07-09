<?php
/**
 * Structure Gallery Grid
 *
 * @package classes/structure
 */

namespace tm_photo_gallery\classes\structure;

use tm_photo_gallery\classes\Structure;
use tm_photo_gallery\classes\Core;

/**
 * Gallery Grid
 */
class Grid extends Structure {

	/**
	 * Grid content
	 *
	 * @var type
	 */
	private $content;

	/**
	 * Grid pading
	 *
	 * @var type
	 */
	public $gutter;

	/**
	 * Grid pading
	 *
	 * @var type
	 */
	public $height;

	/**
	 * Colums
	 *
	 * @var type
	 */
	public $colums;

	/**
	 * Grid images size
	 *
	 * @var type
	 */
	public $grid_images_size;

	/**
	 * Masonry images size
	 *
	 * @var type
	 */
	public $masonry_images_size;

	/**
	 * Justify images size
	 *
	 * @var type
	 */
	public $justify_images_size;

	/**
	 * Grid type
	 *
	 * @var type
	 */
	public $type;

	/**
	 * Construct
	 *
	 * @param type $id
	 */
	public function __construct( $id ) {

		$prent_id = $this->get_gallery_id();

		if ( $prent_id ) {
			$id = $prent_id;
		}

		$this->content				 = Core::get_instance()->get_post_meta( $id, 'grid' );
		$this->gutter				 = $this->get_gutter();
		$this->height				 = $this->get_height();
		$this->colums				 = $this->get_colums();
		$this->grid_images_size		 = $this->get_grid_images_size();
		$this->masonry_images_size	 = $this->get_masonry_images_size();
		$this->justify_images_size	 = $this->get_justify_images_size();
		$this->type					 = $this->get_type();
	}

	/**
	 * Get gutter
	 *
	 * @param type $id
	 * @return int
	 */
	private function get_gutter() {
		$gutter = array(
			'gallery' => 5,
			'set'     => 5,
			'album'   => 5
		);

		if ( isset( $this->content['gutter'] ) ) {
			foreach( array_keys( $gutter ) as $key ){
				if ( isset( $this->content['gutter'][$key] ) ) {
					$gutter[$key] = (int) $this->content['gutter'][$key];
				}
			}
		}

		return $gutter;
	}

	/**
	 * Get height
	 *
	 * @param type $id
	 * @return int
	 */
	private function get_height() {
		$height = array(
			'gallery' => 250,
			'set'     => 250,
			'album'   => 250
		);

		if ( isset( $this->content['height'] ) ) {
			foreach( array_keys( $height ) as $key ){
				if ( isset( $this->content['height'][$key] ) ) {
					$height[$key] = (int) $this->content['height'][$key];
				}
			}
		}

		return $height;
	}

	/**
	 * Get colums
	 *
	 * @param type $id
	 * @return int
	 */
	private function get_colums() {
		$colums = array(
			'gallery' => 3,
			'set'     => 3,
			'album'   => 3
		);

		if ( isset( $this->content['colums'] ) ) {
			foreach( array_keys( $colums ) as $key ){
				if ( isset( $this->content['colums'][$key] ) ) {
					$colums[$key] = (int) $this->content['colums'][$key];
				}
			}
		}

		return $colums;
	}

	/**
	 * Get grid images size
	 *
	 * @param type $id
	 * @return int
	 */
	private function get_grid_images_size() {
		$grid_images_size = array(
			'gallery' => '',
			'set'     => '',
			'album'   => ''
		);

		if ( isset( $this->content['grid_images_size'] ) ) {
			foreach( array_keys( $grid_images_size ) as $key ){
				if ( isset( $this->content['grid_images_size'][$key] ) ) {
					$grid_images_size[$key] = $this->content['grid_images_size'][$key];
				}
			}
		}

		return $grid_images_size;
	}

	/**
	 * Get masonry images size
	 *
	 * @param type $id
	 * @return int
	 */
	private function get_masonry_images_size() {
		$masonry_images_size = array(
			'gallery' => '',
			'set'     => '',
			'album'   => ''
		);

		if ( isset( $this->content['masonry_images_size'] ) ) {
			foreach( array_keys( $masonry_images_size ) as $key ){
				if ( isset( $this->content['masonry_images_size'][$key] ) ) {
					$masonry_images_size[$key] = $this->content['masonry_images_size'][$key];
				}
			}
		}

		return $masonry_images_size;
	}

	/**
	 * Get justify images size
	 *
	 * @param type $id
	 * @return int
	 */
	private function get_justify_images_size() {
		$justify_images_size = array(
			'gallery' => '',
			'set'     => '',
			'album'   => ''
		);

		if ( isset( $this->content['justify_images_size'] ) ) {
			foreach( array_keys( $justify_images_size ) as $key ){
				if ( isset( $this->content['justify_images_size'][$key] ) ) {
					$justify_images_size[$key] = $this->content['justify_images_size'][$key];
				}
			}
		}

		return $justify_images_size;
	}

	/**
	 * Get type
	 *
	 * @param type $id
	 * @return type
	 */
	private function get_type() {
		$type = array(
			'gallery' => 'grid',
			'set'     => 'grid',
			'album'   => 'grid'
		);

		if ( isset( $this->content['type'] ) ) {
			foreach( array_keys( $type ) as $key ){
				if ( isset( $this->content['type'][$key] ) ) {
					$type[$key] = $this->content['type'][$key];
				}
			}
		}

		return $type;
	}
}
