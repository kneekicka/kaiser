<?php
/**
 * Preprocessor media class
 *
 * @package classes/preprocessors
 */

namespace tm_photo_gallery\classes\preprocessors;

use tm_photo_gallery\classes\Preprocessor as Preprocessor;

/**
 * Preprocessor media class
 */
class Preprocessor_Media extends Preprocessor {

	/**
	 * Model type
	 *
	 * @var type
	 */
	private $type = 'media';

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
	 * Get media array
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
	 * Save datails
	 *
	 * @param array $params
	 *
	 * @return \tm_photo_gallery\classes\type
	 */
	public function save_details( $params = array() ) {
		$this->validation_rules( array(
			'type'	 => 'required|contains,post_title post_content status',
			'id'	 => 'required',
		) );
		return $this->progress( $params, __FUNCTION__, $this->type );
	}

	/**
	 * delete img
	 *
	 * @param array $params
	 *
	 * @return \tm_photo_gallery\classes\type
	 */
	public function delete_post( $params = array() ) {
		$this->validation_rules( array(
			'id' => 'required',
		) );
		return $this->progress( $params, __FUNCTION__, $this->type );
	}
}
