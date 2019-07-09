<?php
/**
 * Preprocessor folder class
 *
 * @package classes/preprocessors
 */

namespace tm_photo_gallery\classes\preprocessors;

use tm_photo_gallery\classes\Preprocessor as Preprocessor;

/**
 * Preprocessor folder class
 */
class Preprocessor_Folder extends Preprocessor {

	/**
	 * Model type
	 *
	 * @var type
	 */
	private $type = 'folder';

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
	 * Update cover
	 *
	 * @param type $params
	 * @return type
	 */
	public function update_cover( $params = array() ) {
		$this->validation_rules( array(
			'id'		 => 'required',
			'parent_id'	 => 'required',
		) );
		return $this->progress( $params, __FUNCTION__, $this->type );
	}

	/**
	 * Add folder
	 *
	 * @param array $params
	 *
	 * @return \tm_photo_gallery\classes\type
	 */
	public function add_folder( $params = array() ) {
		$this->validation_rules( array(
			'type'	 => 'required',
			'title'	 => 'required',
		) );
		return $this->progress( $params, __FUNCTION__, $this->type );
	}

	/**
	 * Get content
	 *
	 * @param array $params
	 *
	 * @return \tm_photo_gallery\classes\type
	 */
	public function get_content( $params = array() ) {
		$this->validation_rules( array(
			'type'	 => 'required',
			'id'	 => 'required',
		) );
		return $this->progress( $params, __FUNCTION__, $this->type );
	}

	/**
	 * Add to folder
	 *
	 * @param type $params
	 */
	public function set_folder_content( $params = array() ) {
		$this->validation_rules( array(
			'id'	 => 'required|numeric',
			'value'	 => 'required|numeric',
			'action' => 'required',
		) );
		return $this->progress( $params, __FUNCTION__, $this->type );
	}
}
