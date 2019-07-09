<?php
/**
 * Menu module
 *
 * @package classes/modules
 */

namespace tm_photo_gallery\classes\modules;

/**
 * Menu module
 */
class Menu {

	/**
	 * Add menu page
	 *
	 * @param  $params
	 */
	public static function add_menu_page( array $params ) {
		$params['capability']	 = ! empty( $params['capability'] ) ? $params['capability'] : 'manage_options';
		$params['function']		 = ! empty( $params['function'] ) ? $params['function'] : '';
		$params['position']		 = ! empty( $params['position'] ) ? $params['position'] : null;
		$params['icon_url']		 = ! empty( $params['icon_url'] ) ? $params['icon_url'] : '';
		add_menu_page( $params['title'], $params['title'], $params['capability'], $params['menu_slug'], $params['function'], $params['icon_url'], $params['position'] );
	}

	/**
	 * Add submenu page
	 *
	 * @param $params
	 */
	public static function add_submenu_page( array $params ) {
		$params['capability']	 = ! empty( $params['capability'] ) ? $params['capability'] : 'manage_options';
		$params['function']		 = ! empty( $params['function'] ) ? $params['function'] : '';
		add_submenu_page( $params['parent_slug'], $params['title'], $params['title'], $params['capability'], $params['menu_slug'], $params['function'] );
	}
}
