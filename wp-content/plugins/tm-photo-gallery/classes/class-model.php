<?php
/**
 * Model class
 *
 * @package classes
 */

namespace tm_photo_gallery\classes;

use tm_photo_gallery\classes\Core;

/**
 * Model class
 */
class Model extends Core {

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
	 * Install models by type
	 */
	static function install() {
		// include all core models
		Core::include_all( TM_PG_MODELS_PATH );
	}

	/**
	 * Get return Array
	 *
	 * @param array      $data
	 * @param bool|false $success
	 *
	 * @return array
	 */
	public function get_arr( $data = array(), $success = false ) {
		return array( 'success' => $success, 'data' => $data );
	}

	/**
	 * Update post meta
	 *
	 * @param $params
	 *
	 * @return type
	 */
	public function updates_post_meta( $params ) {
		$data = array();
		foreach ( $params['data'] as $meta_key => $value ) {
			$success						 = $this->update_post_meta( $params['id'], $meta_key, $value );
			$data[ $params['id'] ][ $meta_key ]	 = $this->get_arr( $value, $success );
		}
		return $this->get_arr( $data, true );
	}

	/**
	 * Get Archive
	 *
	 * @param $params
	 *
	 * @return mixed
	 */
	public function get_archives( $params ) {
		$domain	 = get_site_url() . '/';
		$arhives = wp_get_archives( array(
			'type'			 => 'monthly',
			'format'		 => 'option',
			'echo'			 => 0,
			'post_type'		 => ! empty( $params['post_type'] ) ? $params['post_type'] : '',
			'post_status'	 => ! empty( $params['post_status'] ) ? $params['post_status'] : '',
		) );
		return str_replace( $domain, '', $arhives );
	}

	/**
	 * Get pagination array
	 *
	 * @param array $arr
	 * @param type  $per_page
	 * @param type  $offset
	 * @return array
	 */
	public function get_pagination_arr( array $arr, $per_page = -1, $offset = 0 ) {
		$return	 = array();
		$count	 = $per_page > 0 ? (int) $per_page + (int) $offset : count( $arr );
		for ( $i = $offset; $i < $count; $i++ ) {
			if ( ! empty( $arr[ $i ] ) ) {
				$return[ $i ] = $arr[ $i ];
			}
		}
		return $return;
	}
}
