<?php
/**
 * Preprocessor model class
 *
 * @package classes/preprocessors
 */

namespace tm_photo_gallery\classes\preprocessors;

use tm_photo_gallery\classes\Preprocessor as Preprocessor;

/**
 * Preprocessor model class
 */
class Preprocessor_Model extends Preprocessor {

	/**
	 * Model type
	 *
	 * @var type
	 */
	private $type = 'model';

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
	 * Update post meta
	 *
	 * @param array $params
	 *
	 * @return \tm_photo_gallery\classes\type
	 */
	public function updates_post_meta( $params = array() ) {
		$this->validation_rules( array(
			'data'	 => 'required',
			'id'	 => 'required',
		) );
		return $this->progress( $params, __FUNCTION__, $this->type );
	}
}
