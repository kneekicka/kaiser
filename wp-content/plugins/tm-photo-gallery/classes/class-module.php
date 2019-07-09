<?php
/**
 * Module class
 *
 * @package classes/modules
 */

namespace tm_photo_gallery\classes;

use tm_photo_gallery\classes\Core;

/**
 * Module class
 */
class Module extends Core {

	/**
	 * Get lables
	 *
	 * @param array  $params
	 * @param string $plugin_name
	 * @return type
	 */
	public function get_labels( array $params ) {
		$labels = array();
		if ( ! empty( $params['titles'] ) ) {
			$many	 = ! empty( $params['titles']['many'] ) ? $params['titles']['many'] : '';
			$single	 = ! empty( $params['titles']['single'] ) ? $params['titles']['single'] : '';
			$labels	 = array(
				'name'				 => ucfirst( $many ),
				'singular_name'		 => ucfirst( $single ),
				'add_new'			 => _x( 'Add New', $many, 'tm_gallery' ),
				'add_new_item'		 => esc_attr__( 'Add New', 'tm_gallery' ) . ' ' . ucfirst( $single ),
				'edit_item'			 => esc_attr__( 'Edit', 'tm_gallery' ) . ' ' . ucfirst( $single ),
				'new_item'			 => esc_attr__( 'New', 'tm_gallery' ) . ' ' . ucfirst( $single ),
				'all_items'			 => esc_attr__( 'All', 'tm_gallery' ) . ' ' . ucfirst( $single ),
				'view_item'			 => esc_attr__( 'View', 'tm_gallery' ) . ' ' . ucfirst( $single ),
				'search_items'		 => esc_attr__( 'Search', 'tm_gallery' ) . ' ' . ucfirst( $single ),
				'not_found'			 => str_replace( '%many%', $many, esc_attr__( 'No %many% found', 'tm_gallery' ) ),
				'not_found_in_trash' => str_replace( '%many%', $many, esc_attr__( 'No %many% found in Trash', 'tm_gallery' ) ),
				'parent_item_colon'	 => 'media',
				'menu_name'			 => ucfirst( $many ),
			);
		}
		return $labels;
	}

	/**
	 * Install controllers
	 */
	public static function install() {
		// include all core controllers
		Core::include_all( TM_PG_MODULES_PATH );
	}
}
