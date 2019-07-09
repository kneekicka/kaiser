<?php
/*
 * Gallery display
 *
 * @package classes\structure
 */

namespace tm_photo_gallery\classes\structure;

use tm_photo_gallery\classes\Structure;
use tm_photo_gallery\classes\Core;

/**
 * Gallery display
 */
class Display extends Structure {

	/**
	 * Display content
	 *
	 * @var type
	 */
	private $content;

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

		$this->content			 = Core::get_instance()->get_post_meta( $id, 'display' );
		$this->labels			 = $this->get_checkbox_val( 'labels' );
		$this->set_label		 = $this->get_set_label();
		$this->album_label		 = $this->get_album_label();
		$this->icon				 = $this->get_checkbox_val( 'icon' );
		$this->title			 = $this->get_checkbox_val('title');
		$this->description		 = $this->get_checkbox_val('description');
		$this->description_trim	 = $this->get_trim();
		$this->counter			 = $this->get_checkbox_val('counter');
		$this->loader_color      = $this->get_loader_color();
	}

	/**
	 * Returns loader color
	 *
	 * @return string
	 */
	private function get_loader_color() {
		$color = ! empty( $this->content['loader_color'] ) ? $this->content['loader_color'] : '#298ffc';
		return $color;
	}

	/**
	 * Get label
	 *
	 * @return int
	 */
	private function get_checkbox_val( $checkbox ) {
		$show = array(
			'gallery' => 1,
			'set'     => 1,
			'album'   => 1
		);

		if ( isset( $this->content[$checkbox] ) ) {
			foreach( array_keys( $show ) as $key ){
				if ( isset( $this->content[$checkbox][$key] ) ) {
					$show[$key] = (int) $this->content[$checkbox][$key];
				}
			}
		}

		return $show;
	}

	/**
	 * Get set label
	 *
	 * @return int
	 */
	private function get_set_label() {
		$set_label = array(
			'gallery' => 'Set',
			'set'     => 'Set',
			'album'   => 'Set'
		);

		if ( isset( $this->content['set_label'] ) ) {
			foreach( array_keys( $set_label ) as $key ){
				if ( isset( $this->content['set_label'][$key] ) ) {
					$set_label[$key] = $this->content['set_label'][$key];
				}
			}
		}

		return $set_label;
	}

	/**
	 * Get albums label
	 *
	 * @return int
	 */
	private function get_album_label() {
		$album_label = array(
			'gallery' => 'Album',
			'set'     => 'Album',
			'album'   => 'Album'
		);

		if ( isset( $this->content['album_label'] ) ) {
			foreach( array_keys( $album_label ) as $key ){
				if ( isset( $this->content['album_label'][$key] ) ) {
					$album_label[$key] = $this->content['album_label'][$key];
				}
			}
		}

		return $album_label;
	}

	/**
	 * Get albums label
	 *
	 * @return int
	 */
	private function get_trim() {
		$trim = array(
			'gallery' => 10,
			'set'     => 10,
			'album'   => 10
		);

		if ( isset( $this->content['description_trim'] ) ) {
			foreach( array_keys( $trim ) as $key ){
				if ( isset( $this->content['description_trim'][$key] ) ) {
					$trim[$key] = (int) $this->content['description_trim'][$key];
				}
			}
		}

		return $trim;
	}
}
