<?php
/**
 * Folder class
 *
 * @package classes/models
 */

namespace tm_photo_gallery\classes\models;

use tm_photo_gallery\classes\Model;
use tm_photo_gallery\classes\lib\FB;

/**
 * Folder class
 */
class Folder extends Model {

	const POS_KEY		 = 'position_';
	const ISSET_POS	 = 'isset_pos';

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
	 * Update cover
	 *
	 * @param array $params
	 * @return type
	 */
	public function update_cover( array $params ) {
		$data	 = array();
		$cover	 = $this->get_cover( $params['parent_id'] );
		if ( empty( $cover ) || $cover != $params['id'] ) {
			$this->update_post_meta( $params['parent_id'], 'user_cover', $params['id'] );
			$data['update'] = $this->update_post_meta( $params['parent_id'], 'cover_img', $params['id'] );
		} else {
			$this->update_post_meta( $params['parent_id'], 'cover_img', '' );
			$data['update'] = false;
		}
		return $this->get_arr( $data, true );
	}

	/**
	 * Get cover
	 *
	 * @param type $id
	 * @return type
	 */
	public function get_cover( $id ) {
		return $this->get_post_meta( $id, 'cover_img', true );
	}

	/**
	 * Remove cover
	 *
	 * @param type $id
	 * @return type
	 */
	public function remove_cover( $id ) {
		return $this->delete_post_meta( $id, 'cover_img' );
	}

	/**
	 * Get album images
	 *
	 * @param type $type
	 */
	public function get_images( $id, $state ) {
		$types	 = $this( 'image' )->get_sizes_by_type( $state );
		$return	 = array();
		foreach ( $types as $name => $value ) {
			if ( $value ) {
				$return[ $name ] = $this( $state )->get_cover_img( $id, $name );
			}
		}
		return $return;
	}

	/**
	 * Get content
	 *
	 * @param type $params['id'] - folder id.
	 * @param type $params['type'] - folder type.
	 *
	 * @return type
	 */
	public function get_content( $params ) {
		$args = array(
			'cover_img',
			'tags',
			'categories',
			'img_count',
			'cover_id',
		);
		if ( 'album' == $params['type'] ) {
			$args[] = 'sets';
		}
		$return = $this( 'media' )->get_content( esc_attr( $params['id'] ), $args );
		return $this->get_arr( $return, true );
	}

	/**
	 * Add new album or set
	 *
	 * @param string $params['type'] - post type.
	 * @param string $params['title'] - post title.
	 * @param string $params['parent'] - parent id.
	 *
	 * @return array
	 */
	public function add_folder( $params ) {
		$new_post	 = array(
			'post_type'		 => esc_attr( $params['type'] ),
			'post_title'	 => esc_html( $params['title'] ),
			'post_content'	 => '',
			'post_status'	 => 'publish',
			'post_author'	 => get_current_user_id(),
		);
		$id			 = wp_insert_post( $new_post, true );
		$success	 = is_wp_error( $id ) ? false : true;
		if ( $success && ! empty( $params['parent'] ) ) {
			$this( 'folder' )->set_folder_content( array(
				'id'	 => esc_attr( $params['parent'] ),
				'value'	 => $id,
				'action' => 'add_to_folder',
			) );
		}
		$return['id']		 = $id;
		$return['folder']	 = $this( 'media' )->get_content( $id );
		return $this->get_arr( $return, $success );
	}

	/**
	 * Render cover
	 *
	 * @param type $id
	 * @param type $img_type
	 * @return type
	 */
	public function render_cover( $id, $folder_type, $img_type ) {
		$image	 = false;
		$posts	 = get_post_meta( $id, self::$post_types[ $folder_type ], false );
		$images	 = array();
		foreach ( $posts as $post_id ) {
			$post = get_post( $post_id );
			switch ( $post->post_type ) {
				case self::$post_types['image']:
					$images[]	 = $post_id;
					break;
				case self::$post_types['album']:
					$images[]	 = $this->get_cover( $post_id );
					break;
			}
		}
		if ( ! empty( $images ) ) {
			$post_id = end( $images );
			$image	 = image_downsize( $post_id, $img_type );
			$this->update_post_meta( $id, 'cover_img', $post_id );
		}
		return $image;
	}

	/**
	 * Set folder content
	 *
	 * @param['id'] - folder id.
	 * @param['type'] - folder type.
	 * @param['value'] - child id.
	 * @param['action'] - metod action.
	 */
	public function set_folder_content( $params ) {
		$post		 = get_post( $params['id'] );
		$value_post	 = get_post( $params['value'] );
		if ( empty( $post ) || empty( $value_post ) ) {
			return $this->get_arr( array(), false );
		}

		switch ( get_post_type( $value_post->ID ) ) {
			case 'tm_pg_album':
				$meta_key = 'tm_pg_order_albums';
				break;

			default:
				$meta_key = 'tm_pg_order';
				break;
		}

		$meta  = get_post_meta( $post->ID, $post->post_type, false );
		$order = get_post_meta( $post->ID, $meta_key, true );

		if ( ! $order ) {
			$order = array();
		}

		switch ( $params['action'] ) {
			case 'add_to_folder':
				if ( ! in_array( strval( $value_post->ID ), $meta ) ) {
					add_post_meta( $post->ID, $post->post_type, $value_post->ID );
					$meta = get_post_meta( $post->ID, $post->post_type, false );
				}

				if ( ! in_array( intval( $params['value'] ), $order ) ) {
					array_unshift( $order, intval( $params['value'] ) );
				}

				break;
			case 'delete_from_folder':
				if ( in_array( $params['value'], $meta ) ) {
					// remove cover if remove from folder
					if ( $this->is_equal_covers( $post, $value_post ) ) {
						$this->update_cover( array(
							'parent_id'	 => $post->ID,
							'id'		 => 0,
						) );
					}
					delete_post_meta( $post->ID, $post->post_type, $value_post->ID );
					$key = array_search( $value_post->ID, $meta );
					unset( $meta[ $key ] );
				}

				$order_key = array_search( intval( $params['value'] ), $order );
				if ( $order && false !== $order_key ) {
					unset( $order[ $order_key ] );
				}

				$order = array_values( $order );

				break;
		}

		update_post_meta( $post->ID, $meta_key, array_map( 'intval', $order ) );
		return $this->get_arr( array_values( $meta ), true );
	}

	/**
	 * Store folder order into post meta.
	 *
	 * @param array $params Parameters array.
	 */
	public function set_folder_order( $params ) {

		if ( ! $params['id'] || ! $params['order'] || ! is_array( $params['order'] ) ) {
			return false;
		}

		switch ( $params['action'] ) {
			case 'reorder_albums':
				$key = 'tm_pg_order_albums';
				break;

			default:
				$key = 'tm_pg_order';
				break;
		}

		$current_order = get_post_meta( $params['id'], $key, true );
		$input_order   = $params['order'];
		$new_order     = array();

		if ( empty( $current_order ) ) {
			$new_order = $input_order;
		} else {
			$new_order = $this->merge_order( $input_order, $current_order );
		}

		update_post_meta( $params['id'], $key, array_map( 'intval', $new_order ) );

	}

	/**
	 * Merge new order and old
	 *
	 * @param  array $new_order
	 * @param  array $old_order
	 * @return array
	 */
	private function merge_order( $new_order, $old_order ) {

		$new_length = count( $new_order );
		$old_length = count( $old_order );

		if ( $new_length >= $old_length ) {
			return $new_order;
		}

		return array_merge( $new_order, array_slice( $old_order, $new_length, $old_length - $new_length ) );
	}

	/**
	 * Is equal covers
	 *
	 * @param \WP_Post $post
	 * @param \WP_Post $value_post
	 */
	public function is_equal_covers( \WP_Post $post, \WP_Post $value_post ) {
		$cover = $this->get_cover( $post->ID );
		// get cover value
		if ( self::$post_types['album'] == $value_post->post_type ) {
			$cover_value = $this->get_cover( $value_post->ID );
		} else {
			$cover_value = $value_post->ID;
		}
		// if covers equals
		$return = false;
		if ( $cover == $cover_value ) {
			$return = true;
		}
		return $return;
	}
}
