<?php
/**
 * Preprocessor gallery
 *
 * @package classes/preprocessors
 */

namespace tm_photo_gallery\classes\preprocessors;

use tm_photo_gallery\classes\Preprocessor;
use tm_photo_gallery\classes\Core;

/**
 * Preprocessor Gallery
 */
class Preprocessor_Gallery extends Preprocessor {

	/**
	 * Model type
	 *
	 * @var type
	 */
	private $type = 'gallery';

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
	 * Get gallery content
	 *
	 * @param array $params
	 *
	 * @return \tm_photo_gallery\classes\type
	 */
	public function get_gallery_content( $params = array() ) {
		$this->validation_rules( array(
			'id' => 'required|numeric',
		) );
		return $this->progress( $params, __FUNCTION__, $this->type );
	}

	/**
	 * Assign to gallery
	 *
	 * @param array $params
	 *
	 * @return \tm_photo_gallery\classes\type
	 */
	public function assign_to_gallery( $params = array() ) {
		$this->validation_rules( array(
			'id' => 'required|numeric',
		) );
		return $this->progress( $params, __FUNCTION__, $this->type );
	}

	/**
	 * Check post gallery
	 *
	 * @param array $params
	 *
	 * @return \tm_photo_gallery\classes\type
	 */
	public function post_gallery( $params = array() ) {
		$this->validation_rules( array(
			'title' => 'required',
		) );
		$this->filter_rules( array(
			'title' => 'trim',
		) );
		return $this->progress( $params, __FUNCTION__, $this->type );
	}

	/**
	 * Get content data
	 *
	 * @param array $params
	 *
	 * @return \tm_photo_gallery\classes\type
	 */
	public function get_content_data( $params = array() ) {
		$this->validation_rules( array(
			'posts' => 'required',
		) );
		return $this->progress( $params, __FUNCTION__, $this->type );
	}

	/**
	 * Delete gallery
	 *
	 * @param array $params
	 *
	 * @return \tm_photo_gallery\classes\type
	 */
	public function trash_gallery( $params = array() ) {
		$this->validation_rules( array(
			'ids' => 'required',
		) );
		return $this->progress( $params, __FUNCTION__, $this->type );
	}

	/**
	 * Update gallery filters
	 *
	 * @param array $params
	 *
	 * @return \tm_photo_gallery\classes\type
	 */
	public function update_gallery_filters( $params = array() ) {
		$this->validation_rules( array(
			'id'		 => 'required',
			'filters'	 => 'required',
		) );
		return $this->progress( $params, __FUNCTION__, $this->type );
	}

	/**
	 * Sort gallery by term id
	 *
	 * @param array $params
	 */
	public function sort_gallery_by_term_id( array $params ) {
		$this->validation_rules( array(
			'term_id'	 => 'required',
			'gallery_ID' => 'required|numeric',
			'type'		 => 'contains,' . Core::$tax_names['tag'] . ' ' . Core::$tax_names['category'],
		) );
		return $this->progress( $params, __FUNCTION__, $this->type );
	}
}
