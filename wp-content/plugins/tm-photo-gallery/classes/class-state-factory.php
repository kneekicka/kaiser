<?php
/**
 * State factory
 *
 * @package classes
 */

namespace tm_photo_gallery\classes;

// models
use tm_photo_gallery\classes\Model;
// Preprocessors
use tm_photo_gallery\classes\Preprocessor as Preprocessor;

/**
 *  Singleton factory
 */
class State_Factory {

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
	 * Get register instance objects
	 *
	 * @param string $value
	 *
	 * @return object instance
	 */
	public function get_model( $value = null ) {
		$model = false;
		if ( 'model' == $value ) {
			$model = Model::get_instance();
		} else {
			$class = 'tm_photo_gallery\classes\models\\' . ucfirst( $value );
			if ( class_exists( $class ) ) {
				$model = $class::get_instance();
			}
		}
		return $model;
	}

	/**
	 * Get controller action
	 *
	 * @param string $value
	 *
	 * @return object instance
	 */
	public function get_controller( $value = null ) {
		$controller	 = false;
		$class		 = 'tm_photo_gallery\classes\controllers\Controller_' . ucfirst( $value );
		if ( class_exists( $class ) ) {
			$controller = $class::get_instance();
		}
		return $controller;
	}

	/**
	 *  Get Preprocessor instance objects
	 *
	 * @param null $value
	 *
	 * @return StoreValidate|Preprocessor
	 */
	public function get_preprocessor( $value = null ) {
		$preprocessor	 = false;
		$class			 = 'tm_photo_gallery\classes\preprocessors\Preprocessor_' . ucfirst( $value );
		if ( class_exists( $class ) ) {
			$preprocessor = $class::get_instance();
		}
		return $preprocessor;
	}
}
