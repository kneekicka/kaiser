<?php

/**
 * Model Set class
 *
 * @package classes/models
 */

namespace tm_photo_gallery\classes\models;

use tm_photo_gallery\classes\Model;

/**
 * Set model
 */
class Set extends Model {

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
	 * Get set by album id
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public function get_set_by_album_id( $id ) {
		$return	 = 0;
		$terms	 = get_the_terms( $id, self::$tax_names['set'] );
		if ( ! empty( $terms ) && ! empty( $terms[0]->name ) ) {
			$return = $terms[0]->name;
		}
		return $return;
	}

	/**
	 * Get content query parameters
	 *
	 * @param type $params
	 * @return type
	 */
	public function get_content_params( $params = array() ) {
		$args = array(
			'post_type'		 => self::$post_types['set'],
			'post_status'	 => 'any',
			'order'			 => 'DESC',
			'orderby'		 => 'date',
			'posts_per_page' => -1,
			'fields'		 => ! empty( $params['fields'] ) ? $params['fields'] : '',
			'post__in'		 => ! empty( $params['post__in'] ) ? $params['post__in'] : array(),
		);
		// get albums by attachment ID
		if ( ! empty( $params['img'] ) ) {
			$args['meta_query'] = array(
				array(
					'key'	 => self::$post_types['set'],
					'value'	 => $params['img'],
				),
			);
		}
		return $args;
	}

	/**
	 * Get img count
	 *
	 * @param type $id
	 */
	public function get_img_count( $id ) {
		$total	= array(
			'images' => 0,
			'albums' => 0
		);
		$posts	 = get_post_meta( $id, self::$post_types['set'], false );
		foreach ( $posts as $post_id ) {
			$post = get_post( $post_id );
			if ( ! empty( $post ) ) {
				switch ( $post->post_type ) {
					case self::$post_types['image']:
						$total['images']++;
						break;
					case self::$post_types['album']:
						$total['albums']++;
						break;
				}
			}
		}
		return $total;
	}

	/**
	 * Get content ids
	 *
	 * @param type $id
	 * @return type
	 */
	public function get_content_ids( $id ) {
		// get albums
		$albums	 = get_posts( $this( 'album' )->get_content_params( array(
			'set'	 => $id,
			'fields' => 'ids',
		) ) );
		$images	 = get_posts( $this( 'media' )->get_content_params( array(
			'set'	 => $id,
			'fields' => 'ids',
			'in_set' => true,
		) ) );
		return array_merge( $albums, $images );
	}

	/**
	 * Get sets
	 */
	public function get_sets() {
		// get all sets
		$set_args	 = $this( 'set' )->get_content_params( array( 'fields' => 'ids' ) );
		$ids		 = get_posts( $set_args );
		// build sets
		$sets		 = array();
		if ( ! empty( $ids ) ) {
			foreach ( $ids as $key => $id ) {
				$sets[ $key ] = $this( 'media' )->get_content( $id, array(
					'cover_img',
					'tags',
					'categories',
					'img_count',
					'cover_id',
				) );
			}
		}
		return $sets;
	}

	/**
	 * Get cover img
	 *
	 * @param type $id - set id.
	 * @param type $type - thumbnail type.
	 * @return type
	 */
	public function get_cover_img( $id, $type ) {
		$post_id = $this( 'folder' )->get_cover( $id );
		$image	 = false;
		if ( ! empty( $post_id ) ) {
			$post = get_post( $post_id, ARRAY_A );
			if ( ! empty( $post ) ) {
				if ( $post['post_type'] == self::$post_types['album'] ) {
					$image = $this( 'album' )->get_cover_img( $post_id, $type );
				} else {
					$image = image_downsize( $post_id, $type );
				}
			} else {
				$image = $this( 'folder' )->render_cover( $id, 'set', $type );
			}
		} else {
			$image = $this( 'folder' )->render_cover( $id, 'set', $type );
		}
		return $image;
	}
}
