<?php

/**
 * Term controller
 *
 * @package classes/controllers
 */

namespace tm_photo_gallery\classes\controllers;

use tm_photo_gallery\classes\Controller as Controller;

/**
 * Controller term
 */
class Controller_Term extends Controller {

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
	 * Action add term
	 */
	public function action_add_term() {
		$return = $this( 'term', true )->set_post_terms( $_POST );
		$this->send_json( $return );
	}

	/**
	 * Action Delete term
	 */
	public function action_delete_term() {
		$return = $this( 'term', true )->set_post_terms( $_POST );
		$this->send_json( $return );
	}

	/**
	 * Action remove term
	 */
	public function action_remove_term() {
		$return = $this( 'term', true )->remove_post_term( array(
			'id'	 => intval( $_POST['id'] ),
			'type'	 => esc_attr( $_POST['type'] ),
		) );
		$this->send_json( $return );
	}

	/**
	 * Action Delete term
	 */
	public function action_set_term() {
		$return = $this( 'term', true )->set_post_terms( $_POST );
		$this->send_json( $return );
	}

	/**
	 * Action get term
	 */
	public function action_get_term() {
		$id		 = intval( $_POST['term_id'] );
		$return	 = $this( 'term' )->get_term_by_id( $id );
		$this->send_json( $return );
	}

	/**
	 * Search terms by request
	 */
	public function action_search_term() {
		$terms = $this( 'term' )->search_term( $_POST );
		$this->send_json( $this( 'model' )->get_arr( $terms, true ) );
	}
}
