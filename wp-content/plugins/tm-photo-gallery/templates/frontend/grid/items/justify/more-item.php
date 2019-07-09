<?php
/**
 * Load more item
 *
 * @package templates/frontend/grid/items
 */
?>

<?php

	$data_attrs = sprintf(
		'data-image-width="%1$s" data-image-height="%2$s"',
		445,
		350
	);

?>

<div class="tm_pg_gallery-item tm_pg_gallery-item_show-more" style="opacity: 0" <?php echo $data_attrs ?>>
	<a href="#" class="tm_pg_gallery-item_link_show-more">
		<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 66 66" height="117px" width="117px" class="preloader" style="display:none">
			<g>
				<circle stroke-linejoin="round" stroke="url(#justify-gradient-white)" r="30" cy="33" cx="33" stroke-width="3" fill="transparent" class="path"/>
				<linearGradient id="justify-gradient-white">
					<stop stop-opacity="1" stop-color="#fff" offset="50%"/>
					<stop stop-opacity=".5" stop-color="#fff" offset="65%"/>
					<stop stop-opacity="0" stop-color="#fff" offset="100%"/>
				</linearGradient>
			</g>
		</svg>
		<span><?php esc_attr_e( 'Load More', 'tm_gallery' ) ?></span>
	</a>
</div>
