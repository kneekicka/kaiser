<?php
/**
 * Index frontend grid
 *
 * @package templates/frontend/grid
 */

$folder = $data->post['gallery_folder'];

?>
<?php do_action( 'tm-pg-grid-preloader', $data ); ?>
<div class="tm-pg_frontend" <?php echo isset( $data->id ) ? "data-id=\"{$data->id}\"" : ''; ?>
	 data-view="<?php echo $data->grid['type'][$folder] ?>" data-post-id="<?php the_ID() ?>">
	<div class="tm-pg_front_gallery <?php echo apply_filters( 'tm-pg-grid-container-class', '' ) ?>">
		<?php do_action( 'tm-pg-grid-filters', $data ); ?>
		<div class="tm-pg_front_gallery-preloader tm-pg_hidden">
			<svg version="1.1"
				xmlns="http://www.w3.org/2000/svg"
				xmlns:xlink="http://www.w3.org/1999/xlink"
				viewBox="0 0 66 66"
				class="preloader"
				style="display:inline-block">
				<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#preloader<?php echo $data->id; ?>"></use>
			</svg>
		</div>
		<?php do_action( 'tm-pg-grid-content', $data ); ?>
		<?php do_action( 'tm-pg-grid-pagination', $data ); ?>
	</div>
</div>

