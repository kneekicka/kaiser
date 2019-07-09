<?php

use tm_photo_gallery\classes\Core;
use tm_photo_gallery\classes\models\Image;

return $options = array(
	'tm_pg_first_activated'	 => get_option( 'tm_pg_first_activated' ),
	'nonce'					 => wp_create_nonce( 'tm_pg_nonce' ),
	'site_url'				 => get_site_url(),
	'wp_version'			 => get_bloginfo( 'version' ),
	'media_url'				 => TM_PG_MEDIA_URL,
	'action'				 => Core::ACTION,
	'post_types'			 => Core::$post_types,
	'tax_names'				 => Core::$tax_names,
	'ajax_url'				 => admin_url( 'admin-ajax.php' ),
	'grid_thumb'			 => Image::get_sizes_by_type( 'grid' ),
	'back_img_url'			 => TM_PG_MEDIA_URL . 'img/back.png',
);
