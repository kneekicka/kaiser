<?php
/**
 * Controller image
 *
 * @package classes/controllers
 */

namespace tm_photo_gallery\classes\controllers;

use tm_photo_gallery\classes\Controller;
use tm_photo_gallery\classes\lib\FB;
use tm_photo_gallery\classes\structure\Image as Single_Image;

/**
 * Controller image
 */
class Controller_Image extends Controller {

	/**
	 * 	Instance
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
	 * Action rotate
	 */
	public function action_rotate() {
		$result	 = $this( 'image', true )->rotate_image( $_POST );
		// get focal point image
		$result	 = $this( 'media' )->get_content_data( array(
			'posts' => array( intval( $_POST['id'] ) ),
		) );
		$this->send_json( $result );
	}

	/**
	 * Action focus point
	 */
	public function action_focus_point() {
		$meta = $this( 'focal_point', true )->post_focal_point( $_POST );
		if ( ! $meta['success'] ) {
			$this->send_json( $meta );
		}
		// get focal point image
		$result = $this( 'media' )->get_content_data( array(
			'posts' => array( $meta['data']['id'] ),
		) );
		$this->send_json( $result );
	}

	/**
	 * Action upload attachment
	 */
	public function action_upload_attachment() {
		$id		 = $this( 'file' )->upload_file( $_FILES['admin-ajax'] );
		$result	 = array();
		if ( ! empty( $id ) ) {
			$result['success']	 = $this( 'file' )->add_attachment( $id );
			$result['data']		 = $this( 'media' )->get_content( $id );
		}
		$this->send_json( $result );
	}
}
