<?php
/**
 * Preprocessor album class
 *
 * @package classes/preprocessors
 */

namespace tm_photo_gallery\classes\preprocessors;

use tm_photo_gallery\classes\Preprocessor as Preprocessor;

/**
 * Preprocessor album class
 */
class Preprocessor_Album extends Preprocessor {

	/**
	 * Model type
	 *
	 * @var type
	 */
	private $type = 'album';

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
	 * Add term
	 *
	 * @param array $params
	 *
	 * @return \tm_photo_gallery\classes\type
	 */
	public function get_content( $params = array() ) {
		$this->validation_rules( array(
			'id' => 'required',
		) );
		return $this->progress( $params, __FUNCTION__, $this->type );
	}
}
