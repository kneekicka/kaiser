<?php
/**
 * Albums grid
 *
 * @package templates/photo-gallery/grid
 */
?>
<!-- Albums -->
<div data-id="albums" >
	<div class="tm-pg_library accordion">
		<?php $this->render_html( 'photo-gallery/grid/header', array( 'title' => esc_attr( 'Albums', 'tm_gallery' ) ) ); ?>
		<div class="accordion-content" data-type="album">
			<div style="display: flex;">
				<a class="tm-pg_library_grid_header_select_all" href="#" style="display: none">
					<i class="material-icons">check_circle</i>
					<span ><?php esc_attr_e( 'Select all', 'tm_gallery' ) ?></span>
				</a>
			</div>
			<div class="tm-pg_library_grid tm-pg_grid tm-pg_columns-<?php echo $data['colums'] ?> tm-pg_library_albums" style="display: none">
				<div class="tm-pg_column add-new">
					<div class="tm-pg_library_item_add album" data-type="album">
						<a href="#" class="tm-pg_add-item tm-pg_add-album">
							<i class="material-icons">add</i><?php esc_attr_e( 'Add Album', 'tm_gallery' ); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /Albums -->

	<div class="tm-pg_hr"></div>
</div>
