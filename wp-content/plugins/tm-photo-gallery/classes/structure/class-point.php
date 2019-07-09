<?php
/**
 * Structure Point
 *
 * @package classes/structure
 */

namespace tm_photo_gallery\classes\structure;

/**
 * Point class
 */
class Point {

	/**
	 * Y coordinate
	 *
	 * @var type
	 */
	private $Y;

	/**
	 * X coordiate
	 *
	 * @var type
	 */
	private $X;

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
			$this->$name = (int) ($value);
		}
	}

	/**
	 * Construct
	 *
	 * @param type $x
	 * @param type $y
	 */
	public function __construct( $x, $y ) {
		$this->X = (int) ($x);
		$this->Y = (int) ($y);
	}
}
