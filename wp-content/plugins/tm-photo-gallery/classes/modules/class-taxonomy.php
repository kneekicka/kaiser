<?php
/**
 * Taxonomy module
 *
 * @package classes/modules
 */

namespace tm_photo_gallery\classes\modules;

use tm_photo_gallery\classes\Module;
use tm_photo_gallery\classes\View;

/**
 * Taxonomy module
 */
class Taxonomy extends Module {

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
	 * Register taxonomy
	 *
	 * @param $params
	 */
	public function register( array $params ) {
		$args	 = array(
			'label'					 => ucfirst( $params['titles']['many'] ),
			'labels'				 => $this->get_labels( $params ),
			'parent_item'			 => esc_attr__( 'Parent', 'tm_gallery' ) . ' ' . $params['titles']['single'],
			'parent_item_colon'		 => esc_attr__( 'Parent', 'tm_gallery' ) . ' ' . $params['titles']['single'],
			'public'				 => true,
			'show_in_nav_menus'		 => true,
			'show_ui'				 => true,
			'show_in_menu'			 => false,
			'show_tagcloud'			 => true,
			'show_in_quick_edit'	 => true,
			'hierarchical'			 => true,
			'update_count_callback'	 => '',
			'rewrite'				 => ( ! empty( $params['slug'] )) ? array(
				'slug'			 => $params['slug'],
				'with_front'	 => true,
				'hierarchical'	 => true,
			) : false,
			'capabilities'			 => array(),
			'meta_box_cb'			 => null,
			'show_admin_column'		 => false,
			'_builtin'				 => false,
		);
		$status	 = register_taxonomy( $params['taxonomy'], $params['object_type'], $args );
		if ( ! is_wp_error( $status ) ) {
			return true;
		}
	}

	/**
	 * Render html for filter taxonomy link
	 *
	 * @param $post
	 * @param $tax_name
	 *
	 * @return string
	 */
	public function get_the_term_filter_list( $post, $tax_name ) {
		$taxonomies		 = wp_get_post_terms( $post->ID, $tax_name );
		$taxonomies_html = '';
		foreach ( $taxonomies as $tax ) {
			$data['wp']			 = $tax;
			$data['filter_link'] = '/wp-admin/edit.php?post_type=' . $post->post_type . '&' . $tax->taxonomy . '=' . $tax->slug;
			$taxonomies_html .= View::get_instance()->render_html( 'taxonomies/taxonomy-link', $data, false );
		}
		return ( ! empty( $taxonomies_html )) ? $taxonomies_html : '—';
	}

	/**
	 * Get terms
	 *
	 * @param type $name
	 * @return type
	 */
	public function get_terms( $name ) {
		return get_terms( $name );
	}
}
