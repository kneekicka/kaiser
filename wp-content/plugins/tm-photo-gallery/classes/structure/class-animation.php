<?php
/*
 * Gallery animation
 *
 * @package classes\structure
 */

namespace tm_photo_gallery\classes\structure;

use tm_photo_gallery\classes\Structure;
use tm_photo_gallery\classes\Core;

/**
 * Gallery animation
 */
class Animation extends Structure {

	/**
	 * Animation content
	 *
	 * @var type
	 */
	private $content;

	/**
	 * Animation type
	 *
	 * @var type
	 */
	public $type = 'tm-pg_animation-fade';

	/**
	 * Hover animation type
	 *
	 * @var type
	 */
	public $hover_type = 'tm-pg_hover-fade';

	/**
	 * Contruct
	 *
	 * @param type $id
	 */
	public function __construct( $id ) {

		$prent_id = $this->get_gallery_id();

		if ( $prent_id ) {
			$id = $prent_id;
		}

		$this->content		 = Core::get_instance()->get_post_meta( $id, 'animation' );
		$this->type			 = $this->get_type();
		$this->hover_type	 = $this->get_hover_type();
	}

	/**
	 * Get type
	 *
	 * @param type $id
	 * @return type
	 */
	private function get_type() {
		return !empty($this->content) && !empty( $this->content['type'] ) ? $this->content['type'] : 'tm-pg_animation-fade';
	}

	/**
	 * Get hover type
	 *
	 * @param type $id
	 * @return type
	 */
	private function get_hover_type() {
		return !empty($this->content) && !empty( $this->content['hover_type'] ) ? $this->content['hover_type'] : 'tm-pg_hover-fade';
	}

}
