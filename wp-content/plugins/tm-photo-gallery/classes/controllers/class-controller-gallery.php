<?php

/**
 * Controller Gallery
 *
 * @package classes/controllers
 */

namespace tm_photo_gallery\classes\controllers;

use tm_photo_gallery\classes\Controller as Controller;
use tm_photo_gallery\classes\lib\FB;

/**
 * Class controller gallery
 */
class Controller_Gallery extends Controller {

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
	 * Action template
	 */
	public function action_template() {
		$data				 = array();
		// get all galleries
		$params				 = $this( 'gallery' )->get_content_params();
		$posts				 = get_posts( $params );
		// get trash galleries
		$trash_posts		 = get_posts( $this( 'gallery' )->get_content_params( array( 'post_status' => 'trash' ) ) );
		$data['posts']		 = $this( 'gallery', true )->get_content_data( array( 'posts' => $posts ) );
		$data['all_count']	 = count( $posts );
		$data['trash_count'] = count( $trash_posts );
		$data['image_sizes']['grid'] = $this->get_model('image')->get_sizes_by_type('grid');
		$data['image_sizes']['masonry'] = $this->get_model('image')->get_sizes_by_type('masonry');
		$data['image_sizes']['justify'] = $this->get_model('image')->get_sizes_by_type('justify');
		$this->get_view()->render_html( 'gallery/index', $data );
	}

	/**
	 * Action edit
	 */
	public function action_edit() {
		$result = $this( 'gallery', true )->post_gallery( $_POST );
		$this->send_json( $result );
	}

	/**
	 * Action content
	 */
	public function action_content() {
		// get all galleries
		$return['public']	 = $this( 'gallery' )->get_galleries();
		$return['trash']	 = $this( 'gallery' )->get_galleries( array( 'post_status' => 'trash' ) );
		$this->send_json( $this( 'model' )->get_arr( $return, true ) );
	}

	/**
	 * Action add gallery
	 */
	public function action_add() {
		$return = $this( 'gallery', true )->post_gallery( $_POST );
		$this->send_json( $return );
	}

	/**
	 * Action assign to gallery
	 */
	public function action_save() {
		$return = $this( 'gallery', true )->assign_to_gallery( $_POST );
		$this->send_json( $return );
	}

	/**
	 * Action trash gallery
	 */
	public function action_trash() {
		$return = $this( 'gallery', true )->trash_gallery( $_POST );
		$this->send_json( $return );
	}

	/**
	 * Action delete gallery
	 */
	public function action_delete() {
		$return = $this( 'gallery', true )->trash_gallery( $_POST );
		$this->send_json( $return );
	}

	/**
	 * Action restore gallery
	 */
	public function action_public() {
		$return = $this( 'gallery', true )->trash_gallery( $_POST );
		$this->send_json( $return );
	}

	/**
	 * Action get popup
	 */
	public function action_popup() {
		// get all galleries
		$params			 = $this( 'gallery' )->get_content_params();
		$posts			 = get_posts( $params );
		$data			 = $this( 'gallery', true )->get_content_data( array( 'posts' => $posts ) );
		$return['html']	 = $this->get_view()->render_html( 'popups/index', $data['data'], false );
		$this->send_json( $this( 'model' )->get_arr( $return, true ) );
	}

	/**
	 * Action load item
	 */
	public function action_load_item() {

	}
}
