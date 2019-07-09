<?php
/*
 * Gallery filter
 *
 * @package classes\structure
 */

namespace tm_photo_gallery\classes\structure;

use tm_photo_gallery\classes\Structure;
use tm_photo_gallery\classes\Core;

/**
 * Gallery filter
 */
class Filter extends Structure {

	/**
	 * Filter content
	 *
	 * @var type
	 */
	private $content;

	/**
	 * Show filter
	 *
	 * @var type
	 */
	public $show = 1;

	/**
	 * Filter type
	 *
	 * @var type
	 */
	public $type = 'line';

	/**
	 * Filter by
	 *
	 * @var type
	 */
	public $by = 'category';

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

		$this->content	 = Core::get_instance()->get_post_meta( $id, 'filter' );
		$this->show		 = $this->get_show();
		$this->type		 = $this->get_type();
		$this->by		 = $this->get_by();
	}

	/**
	 * Get padding
	 *
	 * @param type $id
	 * @return int
	 */
	private function get_show() {
		return ! empty( $this->content ) && is_string( $this->content['show'] ) ? (int) ($this->content['show']) : 1;
	}

	/**
	 * Get colums
	 *
	 * @param type $id
	 * @return int
	 */
	private function get_by() {
		return ! empty( $this->content ) && ! empty( $this->content['by'] ) ? $this->content['by'] : 'category';
	}

	/**
	 * Get type
	 *
	 * @param type $id
	 * @return type
	 */
	private function get_type() {
		return ! empty( $this->content ) && ! empty( $this->content['type'] ) ? $this->content['type'] : 'line';
	}
}
