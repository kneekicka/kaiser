<?php
/**
 * Gallery model
 *
 * @package classes\models
 */

namespace tm_photo_gallery\classes\models;

use tm_photo_gallery\classes\Model;
use tm_photo_gallery\classes\structure\Gallery as Single_Gallery;

/**
 * Gallery class
 */
class Gallery extends Model {

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
	 * Construct
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Get gallereies
	 *
	 * @param type $params
	 * @return type
	 */
	public function get_galleries( $params = array() ) {
		// get all galleries
		$args	 = $this->get_content_params( $params );
		$posts	 = get_posts( $args );
		if ( ! empty( $posts ) ) {
			// get galleries by params
			$return = $this->get_content_data( array( 'posts' => $posts ) );
		} else {
			$return = $this->get_arr( '', true );
		}
		return $return;
	}

	/**
	 * Get gallery by ids
	 *
	 * @param $ids
	 */
	public function get_gallery_by_id( $ids ) {
		if ( isset( $ids ) ) {
			$posts = array();
			if ( is_array( $ids ) ) {
				foreach ( $ids as $id ) {
					$post = get_post( $id );
					if ( ! empty( $post ) ) {
						$posts[ $id ] = $post;
					}
				}
				$posts = $this->get_content_data( array( 'posts' => $posts ) );
			} else {
				$posts[ $ids ] = get_post( $ids );
				$posts		 = $this->get_content_data( array( 'posts' => $posts ) );
			}
			return $posts['data'];
		}
	}

	/**
	 * Assign terms to gallery
	 *
	 * @param $params
	 *
	 * @return array
	 */
	public function assign_to_gallery( $params ) {

		$success = true;

		if ( ! is_array( $params['id'] ) ) {
			$params['id'] = esc_attr( $params['id'] );
		}

		$this->delete_post_meta( $params['id'], 'terms' );

		$status = $this->update_post_meta( $params['id'], 'order', $params['order'] );
		$status = $this->update_post_meta( $params['id'], 'terms', $params['content'] );
		if ( ! empty( $params['grid'] ) ) {
			$this->delete_post_meta( $params['id'], 'grid' );
			$this->update_post_meta( $params['id'], 'grid', $this->esc_attr( $params['grid'] ) );
		}
		if ( !empty( $params['display'] ) ) {
			$this->delete_post_meta( $params['id'], 'display' );
			$this->update_post_meta( $params['id'], 'display', $this->esc_attr( $params['display'] ) );
		}
		if ( !empty( $params['animation'] ) ) {
			$this->delete_post_meta( $params['id'], 'animation' );
			$this->update_post_meta( $params['id'], 'animation', $this->esc_attr( $params['animation'] ) );
		}
		if ( ! empty( $params['filter'] ) ) {
			$this->delete_post_meta( $params['id'], 'filter' );
			$this->update_post_meta( $params['id'], 'filter', $this->esc_attr( $params['filter'] ) );
		}
		if ( ! empty( $params['pagination'] ) ) {
			$this->delete_post_meta( $params['id'], 'pagination' );
			$this->update_post_meta( $params['id'], 'pagination', $this->esc_attr( $params['pagination'] ) );
		}
		if ( ! empty( $params['lightbox'] ) ) {
			$this->delete_post_meta( $params['id'], 'lightbox' );
			$this->update_post_meta( $params['id'], 'lightbox', $this->esc_attr( $params['lightbox'] ) );
		}
		$gallery = new Single_Gallery( $params['id'], false );
		$return	 = get_object_vars( $gallery );
		return $this->get_arr( $return, $success );
	}

	/**
	 * Post gallery
	 *
	 * @param type $params
	 *
	 * $params['id'] - Gallery ID.
	 *
	 * @return type
	 */
	public function post_gallery( $params ) {
		$return	 = array();
		$args	 = array(
			'ID'			 => ! empty( $params['id'] ) ? intval( $params['id'] ) : null,
			'post_type'		 => self::$post_types['gallery'],
			'post_title'	 => sanitize_text_field( $params['title'] ),
			'post_author'	 => get_current_user_id(),
			'post_status'	 => 'publish',
		);
		$id		 = ! empty( $params['id'] ) ? wp_update_post( $args ) : wp_insert_post( $args, true );
		$success = is_wp_error( $id ) ? false : true;
		if ( $success ) {
			$gallery = new Single_Gallery( $id, false );
			$return	 = get_object_vars( $gallery );
		}
		return $this->get_arr( $return, $success );
	}

	/**
	 * Get content params
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public function get_content_params( $params = array() ) {
		if ( ! empty( $params['type'] ) ) {
			switch ( $params['type'] ) {
				case 'trash':
					$params['post_status']	 = 'trash';
					break;
				default:
					$params['post_status']	 = array( 'publish', 'draft' );
					break;
			}
		}
		$args = array(
			'post_type'		 => self::$post_types['gallery'],
			'post_status'	 => ! empty( $params['post_status'] ) ? $params['post_status'] : array( 'publish', 'draft' ),
			'order'			 => ! empty( $params['order'] ) ? $params['order'] : 'DESC',
			'orderby'		 => ! empty( $params['orderby'] ) ? $params['orderby'] : 'date',
			'posts_per_page' => -1,
			'date_query'	 => ! empty( $params['year'] ) && ! empty( $params['month'] ) ? array( 'year' => $params['year'], 'month' => $params['month'] ) : false,
		);
		if ( isset( $params['fields'] ) ) {
			$args['fields'] = $params['fields'];
		}

		if ( isset( $params['date_query'] ) ) {
			// $args["suppress_filters"] = false;
			foreach ( $params['date_query'] as $key => $value ) {
				$args['date_query'][ $key ] = $value;
			}
		}
		return $args;
	}

	/**
	 * Get content data
	 *
	 * @param type $params['posts']
	 * @param type $params['show_footer']
	 *
	 * @return type
	 */
	public function get_content_data( $params ) {
		$return = array();
		foreach ( $params['posts'] as $key => $post ) {
			if ( ! is_wp_error( $post ) ) {
				$gallery		 = new Single_Gallery( $post->ID, false );
				$return[ $key ]	 = get_object_vars( $gallery );
			}
		}
		return $this->get_arr( $return, true );
	}

	/**
	 * Get pagination
	 *
	 * @param type $id
	 * @param type $name
	 * @return type
	 */
	public function get_gallery_pagination( $id, $name = false ) {
		$pagination	 = $this->get_post_meta( $id, 'pagination', true );
		$return		 = false;
		if ( ! empty( $name ) ) {
			$return = $pagination[ $name ];
		} else {
			$return = empty( $pagination ) ? array() : $pagination;
		}
		return $return;
	}

	/**
	 * Get gallery filters
	 */
	public function get_gallery_effects( $id ) {
		$effects = $this->get_post_meta( $id, 'effects', true );
		return empty( $effects ) ? array() : $effects;
	}

	/**
	 * Get images per page
	 *
	 * @param type $id
	 * @return type
	 */
	public function get_images_per_page( $id ) {
		$show_pagination = $this->get_gallery_pagination( $id, 'show_pagination' );
		if ( $show_pagination ) {
			$return = $this->get_gallery_pagination( $id, 'images_per_page' );
		} else {
			$return = -1;
		}
		return $return;
	}

	/**
	 * Delete gallery
	 *
	 * @param type $params
	 */
	public function trash_gallery( $params ) {
		$success		 = false;
		$return			 = array();
		$params['ids']	 = explode( ',', esc_attr( $params['ids'] ) );
		foreach ( $params['ids'] as $id ) {
			switch ( $params[ self::ACTION ] ) {
				case 'public':
					$success	 = wp_untrash_post( $id );
					$gallery	 = new Single_Gallery( $id, false );
					$return[ $id ] = get_object_vars( $gallery );
					break;
				case 'trash':
					$success	 = wp_trash_post( $id );
					$gallery	 = new Single_Gallery( $id, false );
					$return[ $id ] = get_object_vars( $gallery );
					break;
				case 'delete':
					$success	 = true;
					$return[ $id ] = $this( 'media' )->delete_post( array( 'id' => $id ) );
					break;
			}
		}
		return $this->get_arr( $return, $success );
	}
}
