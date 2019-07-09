<?php
/**
 * Frontend grid content
 *
 * @package templates/frontend/grid/actions
 */

$folder = $data->post['gallery_folder'];
$lightbox_settings = '';

if ( !$data->lightbox['autoplay'] ) {
	$lightbox_settings .= ' data-lightbox-autoplay="false"';
}
if ( !$data->lightbox['fullscreen'] ) {
	$lightbox_settings .= ' data-lightbox-fullscreen="false"';
}
if ( !$data->lightbox['thumbnails'] ) {
	$lightbox_settings .= ' data-lightbox-thumbnails="false"';
}
if ( !$data->lightbox['arrows'] ) {
	$lightbox_settings .= ' data-lightbox-arrows="false"';
}

?>

<div class="tm-pg_front_gallery-masonry tm-pg_front_gallery-masonry-colum-<?php echo $data->grid['colums'][$folder]?> <?php echo apply_filters( 'tm-pg-gallery-list-class', '' ) ?> <?php echo $data->animation['type'] ?> <?php echo $data->animation['hover_type'] ?>"
	 data-load-more-img="<?php echo $data->pagination['load_more_btn'] ?>"
	 data-columns<?php echo $lightbox_settings ?>>
	<?php do_action( 'tm-pg-grid-posts', $data ); ?>
</div>
