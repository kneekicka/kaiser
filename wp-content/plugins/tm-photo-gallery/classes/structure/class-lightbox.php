<?php
/*
 * Gallery lightbox
 *
 * @package classes\structure
 */

namespace tm_photo_gallery\classes\structure;

use tm_photo_gallery\classes\Structure;
use tm_photo_gallery\classes\Core;

/**
 * Gallery lightbox
 */
class Lightbox extends Structure {

	/**
	 * Filter content
	 *
	 * @var type
	 */
	private $content;

	/**
	 * Autoplay
	 *
	 * @var type
	 */
	public $autoplay;

	/**
	 * Show fullscreen
	 *
	 * @var type
	 */
	public $fullscreen;

	/**
	 * Show thumbnails
	 *
	 * @var type
	 */
	public $thumbnails;

	/**
	 * Show arrows
	 *
	 * @var type
	 */
	public $arrows;

	/**
	 *
	 * @param type $id
	 */
	public function __construct( $id ) {

		$prent_id = $this->get_gallery_id();

		if ( $prent_id ) {
			$id = $prent_id;
		}

		$this->content		 = Core::get_instance()->get_post_meta( $id, 'lightbox' );
		$this->autoplay		 = $this->get_checkbox_val( 'autoplay' );
		$this->fullscreen	 = $this->get_checkbox_val( 'fullscreen' );
		$this->thumbnails	 = $this->get_checkbox_val( 'thumbnails' );
		$this->arrows		 = $this->get_checkbox_val( 'arrows' );
	}

	/**
	 * Get label
	 *
	 * @return int
	 */
	private function get_checkbox_val( $checkbox ) {
		$show = 1;

		if ( isset( $this->content[$checkbox] ) ) {
			$show = (int) $this->content[$checkbox];
		}

		return $show;
	}
}
