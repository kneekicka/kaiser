<?php
/**
 * Preprocessor image class
 *
 * @package classes/preprocessors
 */

namespace tm_photo_gallery\classes\preprocessors;

use tm_photo_gallery\classes\Preprocessor;

/**
 * Preprocessor image class
 */
class Preprocessor_Image extends Preprocessor {

	/**
	 * Model type
	 *
	 * @var type
	 */
	private $type = 'image';

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
	 * Preprocessor rotate image
	 *
	 * @param array $params
	 *
	 * @return \tm_photo_gallery\classes\type
	 */
	public function rotate_image( $params = array() ) {
		$this->validation_rules( array(
			'id'		 => 'required',
			'angle'	 	 => 'required|numeric',
		) );
		return $this->progress( $params, __FUNCTION__, $this->type );
	}

}
