<?php
/**
 * Controller Media
 *
 * @package classes/controllers
 */

namespace tm_photo_gallery\classes\controllers;

use tm_photo_gallery\classes\Controller;
use tm_photo_gallery\classes\View;
use tm_photo_gallery\classes\lib\FB;

/**
 * Controller media
 */
class Controller_Media extends Controller {

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
		// get mode
		$data['mode']		 = get_query_var( 'mode', 'grid' );
		$data['categories']	 = $this( 'term' )->get_terms( array( 'type' => self::$tax_names['category'] ) );
		$data['tags']		 = $this( 'term' )->get_terms( array( 'type' => self::$tax_names['tag'] ) );
		$this->get_view()->render_html( 'photo-gallery/index', $data );
	}

	/**
	 * Action content
	 */
	public function action_content() {

		$_post = $_POST;

		$count = ! empty( $_post['getPage'] )
					? intval( $_post['step'] ) * ( intval( $_post['getPage'] ) - 1 )
					: intval( $_post['count'] );

		// get all media
		$posts = get_posts( $this( 'media' )->get_content_params( array(
			'count'	 => $count,
			'step'	 => intval( $_post['step'] ),
			'fields' => 'ids',
		) ) );
		$data = $this( 'media' )->get_content_data( array(
			'posts'	 => $posts,
			'count'	 => intval( $_post['count'] ),
			'step'	 => intval( $_post['step'] ),
		) );
		$all_posts = get_posts( $this( 'media' )->get_content_params( array( 'fields' => 'ids' ) ) );

		$this( 'media' )->posts     = $posts;
		$this( 'media' )->all_posts = $all_posts;
		$this( 'media' )->paged     = explode( ',', esc_attr( $_post['paged'] ) );

		$data['data']['all_posts']    = $all_posts;
		$data['data']['images_count'] = count( $all_posts );
		$data['data']['pager']        = $this( 'media' )->get_pager();

		if ( ! empty( $_post['requestFirst'] ) && 'true' === $_post['requestFirst'] ) {
			$view = new View();
			$data['data']['first_item'] = $view->render_html( 'photo-gallery/grid/new-image', array(), false );
		} else {
			$data['data']['first_item'] = false;
		}

		$this->send_json( $data );
	}

	/**
	 * Action save details
	 */
	public function action_save_details() {
		$data = $this( 'media', true )->save_details( $_REQUEST );
		if ( $data['success'] ) {
			$return = array();
			foreach ( $data['data'] as $id ) {
				$return[] = $this( 'media' )->get_content( $id );
			}
			$data['data'] = $return;
		}
		$this->send_json( $data );
	}

	/**
	 * Action uploader
	 */
	public function action_uploader() {
		$up_data		 = array(
			'nonce'			 => esc_attr( $_POST['nonce'] ),
			'uploader_data'	 => $this( 'file' )->init_uploader_data( $_POST ),
		);
		$data['html']	 = $this->get_view()->render_html( 'photo-gallery/uploader', $up_data, false );
		$this->send_json( $this( 'model' )->get_arr( $data, true ) );
	}

	/**
	 * Delete img
	 */
	public function action_delete() {
		$return = $this( 'media', true )->delete_post( $_POST );
		$this->send_json( $return );
	}

	/**
	 * Action Init popup
	 */
	public function action_init_popup() {
		$attachments	 = get_posts( $this( 'media' )->get_content_params( array( 'fields' => 'ids' ) ) );
		$ng_images		 = $this( 'image' )->get_next_get_images();
		$posts			 = array_merge( $ng_images, $attachments );
		$data['html']	 = $this->get_view()->render_html( 'popups/init_popup', array( 'count' => count( $posts ) ), false );
		$data['posts']	 = $posts;
		$this->send_json( $this( 'model' )->get_arr( $data, true ) );
	}

	/**
	 * Action load item
	 */
	public function action_load_data() {
		if ( empty( $_POST['ids'] ) ) {
			$this->send_json( $this( 'model' )->get_arr( array(), false ) );
		}
		$ids	 = explode( ',', esc_attr( $_POST['ids'] ) );
		$posts	 = array();
		foreach ( $ids as $id ) {
			$post = $this( 'media' )->get_content( $id );
			if ( ! empty( $_POST['parent'] ) && empty( $post ) ) {
				$this( 'folder' )->set_folder_content( array(
					'id'	 => esc_attr( $_POST['parent'] ),
					'action' => 'delete_from_folder',
					'value'	 => esc_attr( $id ),
				) );
			}
			$posts[] = $post;
		}

		$this->send_json( $this( 'model' )->get_arr( $posts, true ) );
	}
}
