<?php
/**
 * Frontend grid pagination
 *
 * @package templates/frontend/grid/actions
 */
?>
<?php if ( $data->pagination['load_more_btn'] || $data->pagination['load_more_grid'] || $data->pagination['pagination_block'] ) : ?>
	<?php if ( $data->pagination['load_more_btn'] ) : ?>
		<div class="<?php echo apply_filters( 'tm-pg-load-more-button-class', 'load-more-button' ) ?>">
			<a href="#" class="btn"><?php esc_attr_e( 'Load more', 'tm_gallery' ) ?></a>
		</div>
	<?php endif; ?>
	<input type="hidden" name="images_per_page" value="<?php echo $data->pagination['images_per_page'] ?>"/>
	<input type="hidden" name="offset" autocomplete="off" value="<?php echo $data->pagination['offset'] ?>"/>
	<input type="hidden" name="term_id" autocomplete="off" value="<?php echo 'all' ?>"/>
	<input type="hidden" name="term_type" autocomplete="off" value="<?php echo '' ?>"/>
	<input type="hidden" name="all_count" autocomplete="off" value="<?php echo $data->posts_count ?>"/>
	<div class="<?php echo apply_filters( 'tm-pg-gallery-navigation-class', 'tm-pg_front_gallery-navigation' ) ?>" data-load-more-page="<?php echo $data->pagination['pagination_block'] ?>">
		<?php if ( $data->pagination['pagination_block'] ) : ?>
			<?php do_action( 'tm-pg-grid-pagination-block', $data ); ?>
		<?php endif; ?>
	</div>
<?php endif; ?>
