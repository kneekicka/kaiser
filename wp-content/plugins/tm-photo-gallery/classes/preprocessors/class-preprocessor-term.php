<?php

/**
 * Preprocessor term class
 *
 * @package classes/preprocessors
 */

namespace tm_photo_gallery\classes\preprocessors;

use tm_photo_gallery\classes\Preprocessor as Preprocessor;

/**
 * Preprocessor term
 */
class Preprocessor_Term extends Preprocessor {

	/**
	 * Model type
	 *
	 * @var type
	 */
	private $type = 'term';

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
	public function set_post_terms( $params = array() ) {
		$this->validation_rules( array(
			'id'	 => 'required',
			'type'	 => 'required',
			'value'	 => 'required',
		) );
		return $this->progress( $params, __FUNCTION__, $this->type );
	}

	/**
	 * Remove post term
	 *
	 * @param type $params
	 */
	public function remove_post_term( $params = array() ) {
		$this->validation_rules( array(
			'id'	 => 'required',
			'type'	 => 'required',
		) );
		return $this->progress( $params, __FUNCTION__, $this->type );
	}
}
