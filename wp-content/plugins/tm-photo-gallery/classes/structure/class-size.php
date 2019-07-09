<?php
/**
 * Structure of Size
 *
 * @package classes/structure
 */

namespace tm_photo_gallery\classes\structure;

/**
 * Class size
 */
class Size {

	/**
	 * Width
	 *
	 * @var type
	 */
	private $width;

	/**
	 * Height
	 *
	 * @var type
	 */
	private $height;

	/**
	 * Magic get value
	 *
	 * @param type $name
	 * @return type
	 */
	public function __get( $name ) {
		if ( isset( $this->$name ) ) {
			return $this->$name;
		}
	}

	/**
	 * Magic set value
	 *
	 * @param type $name
	 * @param type $value
	 */
	public function __set( $name, $value ) {
		if ( isset( $this->$name ) ) {
			$this->$name = $value;
		}
	}

	/**
	 * Construct
	 *
	 * @param type $width
	 * @param type $y
	 */
	public function __construct( $width, $height ) {
		$this->width	 = $width;
		$this->height	 = $height;
	}
}
