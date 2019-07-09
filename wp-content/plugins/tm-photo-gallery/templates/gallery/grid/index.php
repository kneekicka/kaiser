<?php
/**
 * Gallery list grid
 *
 * @package templates/gallery/grid
 */
?>
<h5 class="tm-pg_library_title">
	<a href="#" style="display: none">
		<i class="material-icons">check_circle</i>
	</a>
	<?php esc_attr_e( 'Gallery(s)', 'tm_gallery' ) ?>
</h5>

<div class="tm-pg_library_grid tm-pg_grid tm-pg_columns-4 tm-pg_library_gallery"style="display: none">

</div>
<?php $this->render_html( 'photo-gallery/preloader' ); ?>
