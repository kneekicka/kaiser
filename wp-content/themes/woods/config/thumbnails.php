<?php
/**
 * Thumbnails configuration.
 *
 * @package Woods
 */

add_action( 'after_setup_theme', 'woods_register_image_sizes', 5 );
function woods_register_image_sizes() {
	set_post_thumbnail_size( 410, 267, true );

	// Registers a new image sizes.
	add_image_size( 'woods-thumb-s', 150, 150, true );
	add_image_size( 'woods-thumb-m', 400, 400, true );
	add_image_size( 'woods-thumb-l', 1051, 725, true );
	add_image_size( 'woods-thumb-xl', 1920, 1080, true );
	add_image_size( 'woods-author-avatar', 512, 512, true );

	add_image_size( 'woods-thumb-240-100', 240, 100, true );
	add_image_size( 'woods-thumb-560-350', 560, 350, true );

	add_image_size( 'woods-blog-module', 490, 290, true );
	add_image_size( 'woods-room-type', 831, 554, true );
	add_image_size( 'woods-thumb-493-380', 493, 380, true );
	add_image_size( 'woods-thumb-372-372', 372, 372, true );

	add_image_size( 'woods-thumb-140-117', 140, 117, true );
	add_image_size( 'woods-thumb-post', 1500, 768, true );

	add_image_size( 'woods-thumb-1500-554', 1500, 554, true );
	add_image_size( 'woods-thumb-150-150', 200, 200, true );
}
