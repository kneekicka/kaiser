<?php

/**
 * Term Class
 *
 * @package classes/models
 */

namespace tm_photo_gallery\classes\models;

use tm_photo_gallery\classes\Model as Model;
use tm_photo_gallery\classes\lib\FB;

/**
 * Class Term
 */
class Term extends Model {

	/**
	 * Instance
	 *
	 * @var type
	 */
	protected static $instance;

	/**
	 * Terms data for terms processed during current request.
	 *
	 * @var array
	 */
	private $processed_terms = array();

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
	 * Hook get_object_terms
	 *
	 * @param array $terms
	 * @param type  $object_id_array
	 * @param type  $taxonomy_array
	 * @param type  $args
	 * @return boolean
	 */
	public function get_object_terms( array $terms, $object_id_array, $taxonomy_array, $args ) {
		// check isset taxonomy array in plugin terms
		if ( ! array_diff( $taxonomy_array, array_values( self::$tax_names ) ) ) {
			return $terms;
		}
		// set term count
		if ( ! empty( $terms ) ) {
			$args['images_per_page'] = isset( $args['images_per_page'] ) ? $args['images_per_page'] : -1;
			$args['offset']			 = isset( $args['offset'] ) ? $args['offset'] : 0;
			$terms					 = $this->get_pagination_arr( $terms, (int) $args['images_per_page'], (int) $args['offset'] );
			foreach ( $terms as $i => $term ) {
				if ( ! empty( $term ) && is_object( $term ) ) {
					switch ( $term->taxonomy ) {
						case self::$tax_names['tag']:
							if ( empty( $terms[ $i ]->count ) ) {
								$terms[ $i ]->count = $this->get_posts_count( $terms[ $i ]->term_id, 'tag' );
							}
							break;
						case self::$tax_names['category']:
							if ( empty( $terms[ $i ]->count ) ) {
								$terms[ $i ]->count = $this->get_posts_count( $terms[ $i ]->term_id, 'cat' );
							}
							break;
					}
				}
			}
		}
		return $terms;
	}

	/**
	 * Get terms count
	 *
	 * @param type $id
	 * @param type $type
	 * @return type
	 */
	public function get_posts_count( $id, $type ) {
		return count( $this->get_posts_by_term_id( $id, $type ) );
	}

	/**
	 * Get posts by term id
	 *
	 * @param type $id
	 * @param type $type
	 * @return type
	 */
	public function get_posts_by_term_id( $id, $type ) {
		return get_posts( array(
			'post_type'		 => 'any',
			'post_status'	 => 'any',
			'posts_per_page' => -1,
			'fields'		 => 'ids',
			'tax_query'		 => array( $this( 'media' )->get_term_params( $id, $type ) ),
		) );
	}

	/**
	 * Get term by field
	 *
	 * @param $params
	 *
	 * @return array|false|null|object|\WP_Error
	 */
	public function get_term( $params ) {
		$params['field'] = empty( $params['field'] ) ? 'name' : $params['field'];
		$term			 = get_term_by( $params['field'], trim( $params['value'] ), $params['type'], ARRAY_A );

		if ( ! $term ) {
			$term = wp_insert_term( $params['value'], $params['type'] );
		}
		return $term;
	}

	/**
	 * Get term by params
	 *
	 * @param $params
	 *
	 * @return array|bool|false|null|object|\WP_Error
	 */
	public function get_term_by_params( $params ) {
		$termArgs	 = array( 'fields' => 'ids' );
		$term		 = false;
		switch ( $params['termType'] ) {
			case 'set':
			case 'album':
				$termArgs[ $params['termType'] ]	 = $params[ $params['termType'] ];
				$post							 = get_post( $params[ $params['termType'] ] );
				$taxonomy						 = self::$tax_names[ $params['termType'] . 's' ];
				$term							 = $this->get_term( array( 'value' => $params[ $params['termType'] ], 'type' => $taxonomy, 'field' => 'name' ) );
				if ( is_array( $term ) ) {
					$term['count']	 = count( get_posts( $this( 'media' )->get_content_params( $termArgs ) ) );
					$term['name']	 = $term['slug']	 = $post->post_title;
					$term['term_id'] = $term['name'];
				} elseif ( is_object( $term ) ) {
					$term->count	 = count( get_posts( $this( 'media' )->get_content_params( $termArgs ) ) );
					$term->term_id	 = $term->name;
					$term->name		 = $term->slug		 = $post->post_title;
				}
				break;
			case 'favorite':
				$termArgs['favorite']			 = true;
				$term							 = array(
					'count'			 => count( get_posts( $this( 'media' )->get_content_params( $termArgs ) ) ),
					'name'			 => 'favorite images',
					'slug'			 => 'favorite',
					'description'	 => '',
					'term_id'		 => $params['favorite'],
				);
				break;
			case 'tag':
			case 'cat':
				$termArgs[ $params['termType'] ]	 = $params[ $params['termType'] ];
				if ( 'tag' === $params['termType'] ) {
					$taxonomy = self::$tax_names['tag'];
				} else {
					$taxonomy = self::$tax_names['category'];
				}
				$term		 = get_term( $params[ $params['termType'] ], $taxonomy );
				$term->count = count( get_posts( $this( 'media' )->get_content_params( $termArgs ) ) );
				break;
		}
		return $term;
	}

	/**
	 * Get all terms
	 *
	 * @param $params
	 *
	 * @return array|int|\WP_Error
	 */
	public function get_terms( $params ) {
		$args = array( 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false );
		return get_terms( $params['type'], $args );
	}

	/**
	 * Set post terms
	 *
	 * @param $params
	 *
	 * @return array
	 */
	public function set_post_terms( $params ) {

		$success = true;
		$result  = array();

		$ids = is_array( $params['id'] ) ? $params['id'] : explode( ',', $params['id'] );

		foreach ( $ids as $key => $id ) {

			$processed_terms = is_array( $params['value'] ) ? $params['value'] : explode( ',', $params['value'] );
			$new_terms       = array();


			foreach ( $processed_terms as $process_term ) {

				if ( 'term_taxonomy_id' !== $params['field'] ) {
					$term_id = term_exists( $process_term, $params['type'] );
				} else {
					$term_id = intval( $process_term );
				}

				if ( 0 === $term_id || null === $term_id ) {
					$term_id = wp_insert_term(
						$process_term,
						$params['type'],
						array(
							'slug' => esc_attr( strtolower( str_ireplace( ' ', '-', $process_term ) ) ),
						)
					);

				}

				if ( is_array( $term_id ) ) {
					$term_id = intval( $term_id['term_id'] );
				}

				if ( $term_id && ! is_wp_error( $term_id ) ) {
					$new_terms[]        = $term_id;
					$current            = array();
					$current['term_id'] = $term_id;
					$current['name']    = $this->get_term_name( $term_id, $params['type'] );
					$result[ $key ][]   = $current;
				}

			}

			switch ( $params[ self::ACTION ] ) {
				case 'add_term':
					wp_set_object_terms( $id, $new_terms, $params['type'], true );
					break;

				// De-attach
				case 'delete_term':
					wp_remove_object_terms( $id, $new_terms, $params['type'] );
					break;

				// Completely remove
				case 'remove_term':
					wp_get_object_terms( $id, $params['type'] );
					break;

			}

			/*
			$new_terms		 = array();
			// get post terms
			$post_terms		 = wp_get_object_terms( $id, $params['type'] );
			// explode values to array
			$params['value'] = is_array( $params['value'] ) ? $params['value'] : explode( ',', $params['value'] );

			foreach ( $params['value'] as $value ) {
				$params['field'] = ! empty( $params['field'] ) ? $params['field'] : 'name';
				$value			 = 'term_taxonomy_id' == $params['field'] ? (int) ($value) : $value;
				// get new term
				$term			 = $this->get_term( array( 'value' => $value, 'type' => $params['type'], 'field' => $params['field'] ) );
				if ( is_wp_error( $term ) ) {
					continue;
				} else {
					// assign new term to post terms
					if ( $post_terms ) {
						foreach ( $post_terms as $post_term ) {
							if ( is_wp_error( $post_term ) ) {
								continue;
							} else {
								if ( (int) ($post_term->term_id) != (int) ($term['term_id']) && ! empty( $params[ self::ACTION ] ) && 'set_term' != $params[ self::ACTION ] ) {
									$new_terms[] = (int) ($post_term->term_id);
								}
							}
						}
					}
				}
				// add term if action not delete term
				if ( empty( $params[ self::ACTION ] ) || 'delete_term' != $params[ self::ACTION ] ) {
					$new_terms[] = (int) ($term['term_id']);
				}
			}
			// return all post terms
			if ( ! is_wp_error( wp_set_object_terms( $id, $new_terms, $params['type'] ) ) ) {
				$success = true;
			} else {
				$success = false;
				break;
			}
			$result[ $key ] = wp_get_object_terms( $id, $params['type'] );*/
		}

		return $this->get_arr( $result, $success );
	}

	/**
	 * Return term name by term id
	 * @param  int    $term_id  Term ID.
	 * @param  string $taxonomy Taxonomy name.
	 * @return string
	 */
	public function get_term_name( $term_id, $taxonomy ) {

		if ( ! isset( $this->processed_terms[ $term_id ] ) ) {
			$this->processed_terms[ $term_id ] = get_term( $term_id, $taxonomy, ARRAY_A );
		}

		if ( empty( $this->processed_terms[ $term_id ] ) || is_wp_error( $this->processed_terms[ $term_id ] ) ) {
			return '';
		}

		return $this->processed_terms[ $term_id ]['name'];
	}

	/**
	 * Remove post term
	 *
	 * @param type $params
	 */
	public function remove_post_term( $params ) {
		$return = wp_delete_term( $params['id'], $params['type'] );
		$success = false;
		if ( ! is_wp_error( $return ) ) {
			$success = true;
		}
		return $this->get_arr( $return, $success );
	}

	/**
	 * Delete term
	 *
	 * @param $params
	 *
	 * @return bool|int|\WP_Error
	 */
	public function delete_term( $params ) {
		$success		 = false;
		$params['field'] = empty( $params['field'] ) ? 'name' : $params['field'];
		$term			 = get_term_by( $params['field'], $params['value'], $params['type'], ARRAY_A );
		if ( ! empty( $term ) ) {
			$success = wp_delete_term( $term['term_taxonomy_id'], $params['type'] );
		}
		return $success;
	}

	/**
	 * Get posts terms
	 *
	 * @param array $ids
	 * @param type  $type
	 */
	function get_posts_terms( $ids, $type ) {
		$terms = wp_get_object_terms( $ids, $type );
		if ( ! is_wp_error( $terms ) ) {
			return $terms;
		}
	}

	/**
	 * Search term
	 *
	 * @param $params
	 *
	 * @return array|int|\WP_Error
	 */
	public function search_term( $params ) {
		$taxonomy	 = sanitize_key( $params['tax'] );
		$tax		 = get_taxonomy( $taxonomy );
		if ( ! $tax ) {
			return false;
		}
		$s		 = wp_unslash( $params['q'] );
		$comma	 = _x( ',', 'tag delimiter' );
		if ( ',' !== $comma ) {
			$s = str_replace( $comma, ',', $s );
		}
		if ( false !== strpos( $s, ',' ) ) {
			$s	 = explode( ',', $s );
			$s	 = $s[ count( $s ) - 1 ];
		}
		$s		 = trim( $s );
		$results = get_terms( $taxonomy, array( 'name__like' => $s, 'fields' => 'all', 'hide_empty' => false ) );
		foreach ( $results as $key => $term ) {
			$args					 = array(
				'post_type'	 => 'attachment',
				'fields'	 => 'ids',
				'tag_id'	 => $term->term_id,
				'tax_query'	 => array(
					array(
						'taxonomy'	 => $term->taxonomy,
						'field'		 => 'slug',
						'terms'		 => $term->slug,
					),
				),
			);
			$results[ $key ]->count	 = count( get_posts( $args ) );
		}
		return $results;
	}
}
