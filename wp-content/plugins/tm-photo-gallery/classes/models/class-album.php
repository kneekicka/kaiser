<?php

/**
 * Album class
 *
 * @package classes/models
 */

namespace tm_photo_gallery\classes\models;

use tm_photo_gallery\classes\Model as Model;

/**
 * Album model class
 */
class Album extends Model {

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
	 * Get album query parameters
	 *
	 * $parms['set'] - id set
	 * $params['fields'] - select fields
	 *
	 * @return array
	 */
	public function get_content_params( $params = array() ) {
		$args = array(
			'post_type'		 => self::$post_types['album'],
			'post_status'	 => 'any',
			'order'			 => 'DESC',
			'orderby'		 => 'date',
			'posts_per_page' => -1,
			'post__in'		 => ! empty( $params['post__in'] ) ? $params['post__in'] : array(),
		);

		if ( ! empty( $params['fields'] ) ) {
			$args['fields'] = esc_attr( $params['fields'] );
		}
		// get albums by attachment ID
		if ( ! empty( $params['img'] ) ) {
			$args['meta_query'] = array(
				array(
					'key'	 => self::$post_types['album'],
					'value'	 => esc_attr( $params['img'] ),
				),
			);
		}
		if ( ! empty( $params['set'] ) ) {
			$args['meta_query'] = array(
				array(
					'key'	 => self::$post_types['set'],
					'value'	 => esc_attr( $params['set'] ),
				),
			);
		}
		return $args;
	}

	/**
	 * Get albums
	 *
	 * @param array   $params['set_id'] - set id.
	 * @param array   $params['img_type'] - image type.
	 * @param type    $params['img_count'] - images type.
	 * @param boolean $params['show_all'] - show_all.
	 *
	 * @return type
	 */
	public function get_albums( array $params ) {
		$params['set_id']	 = isset( $params['set_id'] ) ? $params['set_id'] : 0;
		$album_args			 = $this( 'album' )->get_content_params( array( 'fields' => 'ids' ) );
		$ids				 = get_posts( $album_args );
		$albums				 = array();
		if ( ! empty( $ids ) ) {
			foreach ( $ids as $key => $id ) {
				if ( isset( $params['show_all'] ) ) {
					$albums[ $key ] = $this( 'media' )->get_content( $id, array(
						'cover_img',
						'tags',
						'categories',
						'sets',
						'img_count',
						'cover_id',
					) );
				} else {
					$set = $this( 'set' )->get_set_by_album_id( $id );
					if ( $set == $params['set_id'] ) {
						$albums[ $key ] = $this( 'media' )->get_content( $id, array(
							'cover_img',
							'tags',
							'categories',
							'sets',
							'img_count',
							'cover_id',
						) );
					}
				}
			}
		}

		return $albums;
	}

	/**
	 * Save img count
	 *
	 * @param type $id
	 */
	public function get_img_count( $id ) {
		$meta = get_post_meta( $id, self::$post_types['album'], false );
		return count( $meta );
	}

	/**
	 * Get cover img
	 *
	 * @param type $id - album id
	 */
	public function get_cover_img( $id, $type ) {
		$image_id	 = $this( 'folder' )->get_cover( $id );
		$image		 = array();
		if ( ! empty( $image_id ) ) {
			$post = get_post( $image_id, ARRAY_A );
			if ( ! empty( $post ) ) {
				$image = image_downsize( $image_id, $type );
			} else {
				$image = $this( 'folder' )->render_cover( $id, 'album', $type );
			}
		} else {
			$image = $this( 'folder' )->render_cover( $id, 'album', $type );
		}
		return $image;
	}

	/**
	 * Get content ids
	 *
	 * @param type $id
	 * @return type
	 */
	public function get_content_ids( $id ) {
		return get_posts( $this( 'media' )->get_content_params( array( 'album' => $id, 'fields' => 'ids' ) ) );
	}
}
