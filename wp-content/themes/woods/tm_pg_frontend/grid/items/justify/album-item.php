<?php
/**
 * Album item
 *
 * @package templates/frontend/grid/items
 */
?>

<?php

	$folder = $data->gallery_folder;
	$data_attrs = sprintf(
		'data-image-width="%1$s" data-image-height="%2$s"',
		isset( $data->cover[1] ) ? $data->cover[1] : 445,
		isset( $data->cover[2] ) ? $data->cover[2] : 350
	);

?>

<div class="tm_pg_gallery-item <?php echo apply_filters( 'tm-pg-gallery-item-class', '' ) ?>"
	 data-id="<?php echo $data->ID ?>"  data-type="album" <?php echo $data_attrs ?>>
	<div class="tm_pg_gallery-item-wrapper">
		<a href="<?php do_action( 'tm-pg-the_post_link', $data ) ?>" class="tm_pg_gallery-item_link" data-effect="fadeIn">
			<img src="<?php echo !empty( $data->cover[0] ) ? $data->cover[0] : TM_PG_IMG_URL . 'no-image.png' ?>">
			<?php if ( $data->display['labels'][$folder] ): ?>
				<div class="tm_pg_gallery-item_label"><?php echo $data->display['album_label'][$folder] ?></div>
			<?php endif; ?>
			<div class="tm_pg_gallery-item_meta">
				<?php $show_default_icon = true; ?>
				<?php if ( $data->display['icon'][$folder] ): ?>
					<i class="tm_pg_gallery-item_icon tm_pg_album-icon"></i>
					<?php $show_default_icon = false; ?>
				<?php endif; ?>
				<?php if ( $data->display['title'][$folder] ): ?>
					<h3 class="tm_pg_gallery-item_title"><?php echo $data->post_title ?></h3>
					<?php $show_default_icon = false; ?>
				<?php endif; ?>
				<?php if ( $data->display['description'][$folder] ): ?>
					<p class="tm_pg_gallery-item_description"><?php echo wp_trim_words( $data->post_content, intval( $data->display['description_trim'][$folder] ) ); ?></p>
					<?php $show_default_icon = false; ?>
				<?php endif; ?>
				<?php if ( $data->display['counter'][$folder] ): ?>
					<p class="tm_pg_gallery-item_counter"><?php
						printf(
							esc_html( _nx( '1 image', '%1$s images', $data->img_count, 'album images', 'tm_gallery' ) ),
							number_format_i18n( $data->img_count )
						);
					?></p>
					<?php $show_default_icon = false; ?>
				<?php endif; ?>
				<?php if ( $show_default_icon ): ?>
					<i class="tm_pg_gallery-item_default_icon material-icons">visibility</i>
				<?php endif; ?>
			</div>
		</a>
	</div>
</div>
