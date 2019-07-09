<?php
/**
 * Post module
 *
 * @package classes/modules
 */

namespace tm_photo_gallery\classes\modules;

use tm_photo_gallery\classes\Module;
use tm_photo_gallery\classes\lib\FB;

/**
 * Post module
 */
class Post extends Module {

	/**
	 * Instance
	 *
	 * @var type
	 */
	protected static $instance;

	/**
	 * Metaboxes
	 *
	 * @var type
	 */
	private $metaboxes;

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
	 * Register custom post
	 *
	 * @param  $params
	 */
	public function register_post_type( array $params ) {
		$args	 = array(
			'label'				 => $params['post_type'],
			'labels'			 => $this->get_labels( $params ),
			'public'			 => isset( $params['public'] ) ? $params['public'] : true,
			'show_ui'			 => true,
			'show_in_menu'		 => false,
			'capability_type'	 => 'post',
			'menu_position'		 => 21,
			'hierarchical'		 => false,
			'rewrite'			 => array(
				'slug'		 => $params['slug'],
				'with_front' => false,
			),
			'query_var'			 => false,
			'supports'			 => $params['supports'],
			'show_in_admin_bar'	 => true,
			'taxonomies'		 => ! empty( $params['taxonomies'] ) ? $params['taxonomies'] : array(),
		);
		$status	 = register_post_type( $params['post_type'], $args );
		if ( ! is_wp_error( $status ) ) {
			return true;
		}
	}

	/**
	 * Get theme image title
	 *
	 * @param type $id
	 * @param type $size
	 * @return type
	 */
	public static function get_title( $id, $size = 10 ) {
		$post = get_post( $id );
		if ( $post ) {
			return strlen( $post->post_title ) > $size ? substr( $post->post_title, 0, ++$size ) . '...' : $post->post_title;
		}
	}

	/**
	 * Get post type
	 *
	 * @param type $id
	 * @return type
	 */
	public static function get_post_type( $id ) {
		$post = get_post( $id );
		if ( $post ) {
			return $post->post_type;
		}
	}

	/**
	 * Set metabox params
	 *
	 * @param array $params
	 */
	public function set_metaboxes( array $params ) {
		$this->metaboxes = $params;
	}

	/**
	 * Hook Add meta boxes
	 */
	public function add_meta_boxes() {
		if ( ! empty( $this->metaboxes ) && is_array( $this->metaboxes ) ) {
			foreach ( $this->metaboxes as $metabox ) {
				// add metabox to current post type
				$context		 = ! empty( $metabox['context'] ) ? $metabox['context'] : 'advanced';
				$callback		 = ! empty( $metabox['callback'] ) ? $metabox['callback'] : array( $this, 'render_meta_box_content' );
				$priority		 = ! empty( $metabox['priority'] ) ? $metabox['priority'] : 'high';
				$callback_args	 = ! empty( $metabox['callback_args'] ) ? $metabox['callback_args'] : array();
				$callback_args	 = array_merge( $callback_args, array( 'name' => $metabox['name'], 'title' => $metabox['title'] ) );
				add_meta_box( $metabox['name'], $metabox['title'], $callback, $metabox['post_type'], $context, $priority, $callback_args );
			}
		}
	}

	/**
	 * Hook Save post
	 *
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function save( $post_id ) {
		// Check nonce.
		if ( ! isset( $_POST['tm_gallery_nonce_box'] ) ) {
			return $post_id;
		}
		$nonce = esc_attr( $_POST['tm_gallery_nonce_box'] );

		// Check correct nonce.
		if ( ! wp_verify_nonce( $nonce, 'tm_gallery_nonce' ) ) {
			return $post_id;
		}

		// Check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Cher user rules
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		foreach ( $this->metaboxes as $metabox ) {
			// update post if current post type
			if ( $_POST['post_type'] == $metabox['post_type'] ) {
				$value = esc_attr( $_POST[ $metabox['name'] ] );
				if ( is_array( $value ) ) {
					$mydata = $value;
				} else {
					$mydata = sanitize_text_field( $value );
				}
				$this->update_post_meta( $post_id, $metabox['name'], $mydata );
			}
		}
	}

	/**
	 * Edit form after title
	 */
	public function edit_form_after_title() {
		global $post, $wp_meta_boxes;
		unset( $wp_meta_boxes[ get_post_type( $post ) ]['normal']['core']['authordiv'] );
	}

	/**
	 * Add custom taxonomy columns
	 *
	 * @param $columns
	 *
	 * @return array
	 */
	public function init_menu_columns( $columns ) {
		$columns = array_slice( $columns, 0, 3, true ) + array( $this->get_tax_name( 'menu_tag' ) => 'Tags' ) + array_slice( $columns, 3, count( $columns ) - 1, true );
		$columns = array_slice( $columns, 0, 3, true ) + array( $this->get_tax_name( 'menu_category' ) => 'Categories' ) + array_slice( $columns, 3, count( $columns ) - 1, true );
		return $columns;
	}

	/**
	 * Add content to custom column
	 *
	 * @param $column
	 */
	public function show_menu_columns( $column ) {
		$category_name	 = $this->get_tax_name( 'menu_category' );
		$tag_name		 = $this->get_tax_name( 'menu_tag' );
		global $post;
		switch ( $column ) {
			case $category_name:
				echo Taxonomy::get_instance()->get_the_term_filter_list( $post, $category_name );
				break;
			case $tag_name:
				echo Taxonomy::get_instance()->get_the_term_filter_list( $post, $tag_name );
				break;
		}
	}
}
