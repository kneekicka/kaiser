<?php
/**
 * Menus configuration.
 *
 * @package Woods
 */

add_action( 'after_setup_theme', 'woods_register_menus', 5 );
function woods_register_menus() {

	// This theme uses wp_nav_menu() in four locations.
	register_nav_menus( array(
		'top'    => esc_html__( 'Top', 'woods' ),
		'main'   => esc_html__( 'Main', 'woods' ),
		'footer' => esc_html__( 'Footer', 'woods' ),
		'social' => esc_html__( 'Social', 'woods' ),
	) );
}
