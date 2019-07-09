<?php
/**
 * Image structure
 *
 * @package classes/structure
 */

namespace tm_photo_gallery\classes\structure;

use tm_photo_gallery\classes\lib\FB;
use tm_photo_gallery\classes\Structure;
use tm_photo_gallery\classes\Core;

/**
 * Image class
 */
class Image extends Structure {

	/**
	 * Default args
	 *
	 * @var type
	 */
	private $default_args = array(
		'albums',
		'sets',
		'categories',
		'tags',
		'focus_point',
		'thumbnails',
		'filename',
		'image',
		'filesize',
	);

	/**
	 * Albums
	 *
	 * @var type
	 */
	public $albums;

	/**
	 * Sets
	 *
	 * @var type
	 */
	public $sets;

	/**
	 * Categories
	 *
	 * @var type
	 */
	public $categories;

	/**
	 * Tags
	 *
	 * @var type
	 */
	public $tags;

	/**
	 * Focus point
	 *
	 * @var type
	 */
	public $focus_point;

	/**
	 * Thumbnails
	 *
	 * @var type
	 */
	public $thumbnails;

	/**
	 * Filename
	 *
	 * @var type
	 */
	public $filename;

	/**
	 * Get full image
	 *
	 * @var type
	 */
	public $image;

	/**
	 * filesize
	 *
	 * @var type
	 */
	public $filesize;

	/**
	 * Construct
	 *
	 * @param type $id
	 */
	public function __construct( $id, $args ) {
		parent::__construct( $id );
		$this->init( $args );
	}

	/**
	 * Init
	 *
	 * @param type $args
	 */
	public function init( $args = false ) {
		$args = ! is_array( $args ) ? $this->default_args : $args;
		$attached_file = get_attached_file( $this->id );
		// get alums
		if ( in_array( 'albums', $args ) ) {
			$this->albums = $this->model( 'media' )->get_folders( $this->id, 'album' );
		}
		// get sets
		if ( in_array( 'sets', $args ) ) {
			$this->sets = $this->model( 'media' )->get_folders( $this->id, 'set' );
		}
		// get categories
		if ( in_array( 'categories', $args ) ) {
			$this->categories = wp_get_object_terms( $this->id, Core::$tax_names['category'] );
		}
		// get tags
		if ( in_array( 'tags', $args ) ) {
			$this->tags = wp_get_object_terms( $this->id, Core::$tax_names['tag'] );
		}
		// get focus point
		if ( in_array( 'focus_point', $args ) ) {
			$this->focus_point = $this->get_post_meta( 'focal_point' );
		}
		// get thumbnails
		if ( in_array( 'thumbnails', $args ) ) {
			$this->thumbnails = $this->model( 'image' )->get_thumbnails( $this->id );
		}
		// get filename
		if ( in_array( 'filename', $args ) ) {
			$this->filename = wp_basename( $attached_file );
		}
		// get full image
		if ( in_array( 'image', $args ) ) {
			$this->image = $this->model( 'image' )->get_thumbnail( $this->id, 'full' );
		}
		// get filensize
		if ( in_array( 'filesize', $args ) ) {
			$this->filesize = size_format( filesize( $attached_file ) );
		}
	}
}
