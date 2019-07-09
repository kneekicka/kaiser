<?php
/**
 * Media model
 *
 * @package classes/models
 */

namespace tm_photo_gallery\classes\models;

use tm_photo_gallery\classes\Model as Model;
use tm_photo_gallery\classes\lib\FB;
use tm_photo_gallery\classes\structure\Image as Single_Image;
use tm_photo_gallery\classes\structure\Album as Single_Album;
use tm_photo_gallery\classes\structure\Set as Single_Set;
/**
 * Class for the rule media files
 */
class Media extends Model {

	/**
	 * All media
	 * @var null
	 */
	public $all_posts = null;

	/**
	 * Posts
	 * @var null
	 */
	public $posts = null;

	/**
	 * Step size.
	 * @var integer
	 */
	public $step = 40;

	/**
	 * Current pages.
	 * @var array
	 */
	public $paged = array( 1 );

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
	 * Get content params
	 *
	 * @param $params['step'] - post per page.
	 * @param $params['count'] - offset.
	 * @param $params['fields'] - post fields.
	 * @param $params['year'] - post year.
	 * @param $params['month'] - post month.
	 * @param $params['post__in'] - post in ids.
	 * @param $params['orderby'] - orderby.
	 * @param $params['order'] - order.
	 * @param $params['set'] - post set.
	 * @param $params['in_set'] - in set.
	 * @param $params['album'] - post album.
	 * @param $params['tag'] - tag.
	 * @param $params['post_tag'] - tag.
	 * @param $params['cat'] - cat.
	 * @param $params['category'] - category.
	 *
	 * @return array
	 */
	public function get_content_params( $params = array() ) {

		$args = array(
			'post_type'		 => 'attachment',
			'post_mime_type' => array( 'image/jpeg', 'image/gif', 'image/png' ),
			'post_status'	 => array( 'publish', 'inherit', 'draft', 'private' ),
			'posts_per_page' => ! empty( $params['step'] ) ? $params['step'] : -1,
			'offset'		 => isset( $params['count'] ) ? $params['count'] : '',
			'fields'		 => ! empty( $params['fields'] ) ? $params['fields'] : '',
			'date_query'	 => ! empty( $params['year'] ) && ! empty( $params['month'] ) ? array( 'year' => $params['year'], 'month' => $params['month'] ) : false,
			'post__in'		 => ! empty( $params['post__in'] ) ? $params['post__in'] : array(),
		);

		// order by
		if ( ! empty( $params['orderby'] ) ) {
			$args['orderby'] = $params['orderby'];
			$args['order']	 = ! empty( $params['order'] ) ? strtoupper( $params['order'] ) : 'DESC';
		} else {
			$args['orderby'] = array( 'date' => 'DESC' );
		}

		// get set or album images
		if ( ! empty( $params['set'] ) && empty( $params['album'] ) ) {
			$set_args = $this->get_set_params( array( 'set' => $params['set'] ) );
			if ( ! empty( $args ) && ! empty( $set_args ) ) {
				$args = array_merge( $args, $set_args );
			}
			$meta_query = array(
				array(
					'key'	 => self::$post_types['set'],
					'value'	 => $params['set'],
				),
			);
			if ( ! empty( $args['meta_query'] ) && empty( $params['in_set'] ) ) {
				$args['meta_query'] = array_merge( $args['meta_query'], $meta_query, array( 'relation' => 'OR' ) );
			} else {
				$args['meta_query'] = $meta_query;
			}
		}

		// order by captured
		if ( ! empty( $params['orderby'] ) && 'captured' === $params['orderby'] ) {
			$args['meta_key']	 = 'captured';
			$args['orderby']	 = 'meta_value';
		}

		// get album
		if ( ! empty( $params['album'] ) ) {
			$args['meta_query'] = array(
				array(
					'key'	 => self::$post_types['album'],
					'value'	 => $params['album'],
				),
			);
		}

		// get images by tag
		if ( ! empty( $params['tag'] ) || ! empty( $params['post_tag'] ) ) {
			$tag				 = ! empty( $params['tag'] ) ? $params['tag'] : $params['post_tag'];
			$args['tax_query']	 = array( $this->get_term_params( $tag, 'tag' ) );
		}

		// get cat
		if ( ! empty( $params['cat'] ) || ! empty( $params['category'] ) ) {
			$cat				 = ! empty( $params['cat'] ) ? $params['cat'] : $params['category'];
			$args['tax_query']	 = array( $this->get_term_params( $cat, 'category' ) );
		}

		return $args;
	}

	/**
	 * Get term params
	 *
	 * @param type $id
	 * @return type
	 */
	public function get_term_params( $id, $type ) {
		return array(
			'taxonomy'	 => self::$tax_names[ $type ],
			'field'		 => 'id',
			'terms'		 => $id,
		);
	}

	/**
	 *  Get set params
	 *
	 * @param $params - params array.
	 * @param $params['set'] - Set id.
	 *
	 * @return bool
	 */
	private function get_set_params( $params = array() ) {
		$albums	 = get_posts( $this( 'album' )->get_content_params( array( 'set' => $params['set'], 'fields' => 'ids' ) ) );
		$args	 = false;
		if ( ! empty( $albums ) ) {
			$args['meta_query'] = array(
				array(
					'key'		 => self::$post_types['album'],
					'value'		 => $albums,
					'compare'	 => 'IN',
				),
			);
		}
		return $args;
	}

	/**
	 * Save details
	 *
	 * @param $params
	 *
	 * @return bool|\tm_photo_gallery\classes\type
	 */
	public function save_details( $params ) {
		$ids	 = explode( ',', $params['id'] );
		$return	 = array();
		foreach ( $ids as $id ) {
			$post_array['ID'] = $id;
			switch ( $params['type'] ) {
				case 'post_title':
					$post_array['post_title']	 = $params['value'];
					break;
				case 'post_content':
					$post_array['post_content']	 = $params['value'];
					break;
				case 'status':
					$post_array['post_status']	 = $params['value'];
					break;
			}
			if ( ! empty( $post_array ) ) {
				$return[] = wp_update_post( $post_array );
			}
		}
		return $this->get_arr( $return, true );
	}

	/**
	 * Update img count
	 *
	 * @param type $id
	 * @return type
	 */
	public function update_img_count( $id ) {
		$parent = get_post( $id );
		switch ( $parent->post_type ) {
			case self::$post_types['album']:
				$return	 = $this( 'album' )->get_img_count( $id );
				break;
			case self::$post_types['set']:
				$return	 = $this( 'set' )->get_img_count( $id );
				break;
		}
		return $return;
	}

	/**
	 * Delete img
	 *
	 * @param $params
	 *
	 * $params['id'] - post ID
	 *
	 * @return \tm_photo_gallery\classes\type
	 */
	public function delete_post( $params ) {

		$parent = ! empty( $params['parent'] ) ? intval( $params['parent'] ) : false;
		$from   = ! empty( $params['from'] )   ? esc_attr( $params['from'] ) : false;

		if ( false !== $parent && false !== $from ) {
			return $this->delete_from_parent( $params );
		} else {
			return $this->delete_permanently( $params );
		}

	}

	/**
	 * Remove item from parent.
	 *
	 * @param  array $params Arguments array.
	 * @return
	 */
	public function delete_from_parent( $params ) {
		$ids	 = explode( ',', $params['id'] );
		$return  = array();
		foreach ( $ids as $id ) {

			$this( 'folder' )->set_folder_content( array(
				'id'	 => $params['parent'],
				'action' => 'delete_from_folder',
				'value'	 => $id,
			) );

			$return[ $id ] = get_post( $id );

			switch ( $params['parent'] ) {
				case 'set':
					$return[ $id ]->sets = array( $params['parent'] );
					break;

				default:
					$return[ $id ]->albums = array( $params['parent'] );
					break;
			}

			$return[ $id ]->permanently = false;

		}

		return $this->get_arr( $return, true );
	}

	/**
	 * Permanently delete item
	 *
	 * @param  array $params Arguments array.
	 * @return
	 */
	public function delete_permanently( $params ) {

		$ids	 = explode( ',', $params['id'] );
		$return	 = array();
		foreach ( $ids as $id ) {
			$post = get_post( $id, ARRAY_A );
			if ( empty( $post ) ) {
				continue;
			}
			switch ( $post['post_type'] ) {
				// delete album
				case self::$post_types['album']:
					$sets		 = $this->removeFromFolder( $id, 'set' );
					$return[ $id ] = wp_delete_post( $id );
					if ( ! empty( $return[ $id ] ) ) {
						$return[ $id ]->sets = $sets;
					}
					break;
				// delete set
				case self::$post_types['set']:
					$return[ $id ] = wp_delete_post( $id );
					break;
				// delete gallery
				case self::$post_types['gallery']:
					$return[ $id ] = wp_delete_post( $id );
					break;
				// delete image
				case self::$post_types['image']:
					$albums		 = $this->removeFromFolder( $id, 'album' );
					$sets		 = $this->removeFromFolder( $id, 'set' );
					$return[ $id ] = $this( 'image' )->delete_image( $id );
					if ( ! empty( $return[ $id ] ) ) {
						$return[ $id ]->albums = $albums;
						$return[ $id ]->sets	 = $sets;
					}
					break;
				// delete post
				default :
					$return[ $id ] = wp_delete_post( $id );
					break;
			}

			$return[ $id ]->permanently = true;
		}

		// remove postmeta from DB
		global $wpdb;

		$term_id    = $params['id'];
		$meta_key   = 'tm_pg_terms';

		$query = $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE meta_key=%s AND meta_value REGEXP 'i\:[0-9]+;s\:[0-9]+\:\"%d\"'", array(
			$meta_key,
			$term_id
		) );

		$query_results = $wpdb->get_results( $query );

		foreach ( $query_results as $term ) {
			$changed = false;
			$term_values = unserialize( $term->meta_value );

			foreach ( $term_values as $type => $images ) {
				$key = array_search( $term_id, $images );
				if ( $key !== false ){
					unset( $term_values[ $type ][ $key ] );
					$changed = true;
				}
			}

			if ( $changed ) {
				update_post_meta( $term->post_id, $meta_key, $term_values );
			}
		}

		return $this->get_arr( $return, true );

	}

	/**
	 * Remove from folder
	 *
	 * @param type $id
	 * @param type $type
	 */
	public function removeFromFolder( $id, $type ) {
		$folders = $this->get_folders( $id, $type );
		$return	 = array();
		if ( ! empty( $folders ) ) {
			foreach ( $folders as $folder ) {
				if ( $this( 'folder' )->is_equal_covers( get_post( $folder ), get_post( $id ) ) ) {
					$return[] = $folder;
				}
				$this( 'folder' )->set_folder_content( array(
					'id'	 => $folder,
					'action' => 'delete_from_folder',
					'value'	 => $id,
				) );
			}
		}
		return $return;
	}

	/**
	 * Get folders
	 *
	 * @param type $type
	 * @return type
	 */
	public function get_folders( $id, $type = 'album' ) {
		return get_posts( $this( $type )->get_content_params( array(
			'img'	 => $id,
			'fields' => 'ids',
		) ) );
	}

	/**
	 * Get content data
	 *
	 * @param array $params
	 *
	 * @return \tm_photo_gallery\classes\type
	 */
	public function get_content_data( $params = array() ) {
		$data = array();
		if ( ! empty( $params['posts'] ) ) {
			// get content data
			foreach ( $params['posts'] as $key => $id ) {
				$data['posts'][ $key ] = $this->get_content( $id );
				if ( ! empty( $params['count'] ) ) {
					$data['posts'][ $key ]['order'] = $params['count'] + $key;
				}
			}
			// get step
			if ( ! empty( $params['step'] ) && count( $data['posts'] ) === (int) $params['step'] ) {
				$data['count'] = $params['count'] + $params['step'];
			}
		}
		// check if last step
		if ( ! empty( $data['posts'] ) ) {
			if ( ! empty( $params['step'] ) && count( $data['posts'] ) < $params['step'] ) {
				$data['last'] = true;
			} else {
				$data['last'] = false;
			}
		} else {
			$data['last'] = true;
		}
		return $this->get_arr( $data, true );
	}

	/**
	 * Get content
	 *
	 * @param type $id
	 */
	function get_content( $id, $params = false ) {
		$post = get_post( $id );
		if ( ! empty( $post ) ) {
			switch ( $post->post_type ) {
				case self::$post_types['image']:
					$data	 = new Single_Image( $post->ID, $params );
					break;
				case self::$post_types['album']:
					$data	 = new Single_Album( $post->ID, $params );
					break;
				case self::$post_types['set'];
					$data	 = new Single_Set( $post->ID, $params );
					break;
			}
		}
		return get_object_vars( $data );
	}

	/**
	 * Get pager for current query
	 *
	 * @return string
	 */
	function get_pager() {

		if ( null === $this->all_posts ) {
			return '';
		}

		$count = count( $this->all_posts );
		$pages = ceil( $count / $this->step );
		$paged = array_filter( $this->paged );

		if ( ! $paged || ! is_array( $paged ) ) {
			$paged = array( 1 );
		}

		$paged = array_map( 'intval', $paged );

		if ( 1 >= $pages ) {
			return '';
		}

		$item = '<div class="tm_pager-item %2$s">
			<button class="tm_pager-item-btn" data-page="%1$d">%1$d</button>
		</div>';

		$result  = '';
		$last    = 1;
		$current = '';

		for ( $i = 1; $i <= $pages; $i++) {
			if ( in_array( $i, $paged ) ) {
				$last    = $i;
				$current = 'current-page';
			}
			$result .= sprintf( $item, $i, $current );
			$current = '';
		}

		return sprintf(
			'<div data-show-pages="%2$s" data-total="%3$s" data-last="%4$s" class="tm_pager">%1$s</div>',
			$result, implode( ',', $paged ), $pages, $last
		);

	}

}
