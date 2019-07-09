<?php
/**
 * Preprocessor focal point class
 *
 * @package classes/preprocessors
 */

namespace tm_photo_gallery\classes\preprocessors;

/**
 * Preprocessor focal point class
 */
class Preprocessor_Focal_point extends \tm_photo_gallery\classes\Preprocessor {

	/**
	 * Model type
	 *
	 * @var type
	 */
	private $type = 'focal_point';

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
	 * Preprocessor set new focal point
	 *
	 * @param array $params
	 *
	 * @return \tm_photo_gallery\classes\type
	 */
	public function post_focal_point( $params = array() ) {
		$this->validation_rules( array(
			'id'	 => 'required|numeric',
			'height' => 'required|numeric',
			'width'	 => 'required|numeric',
			'x'		 => 'required|numeric',
			'y'		 => 'required|numeric',
		) );
		return $this->progress( $params, __FUNCTION__, $this->type );
	}
}
