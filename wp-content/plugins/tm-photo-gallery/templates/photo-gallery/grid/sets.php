<?php
/**
 * Sets grid
 *
 * @package templates/photo-gallery/sets
 */
?>
<!-- Sets -->
<div data-id="sets">
	<div  class="tm-pg_library accordion" >
		<?php $this->render_html( 'photo-gallery/grid/header', array( 'title' => esc_attr( 'Sets', 'tm_gallery' ) ) ); ?>
		<div class="accordion-content" data-type="set">
			<div style="display: flex;">
				<a class="tm-pg_library_grid_header_select_all" href="#" style="display: none">
					<i class="material-icons">check_circle</i>
					<span ><?php esc_attr_e( 'Select all', 'tm_gallery' ) ?></span>
				</a>
			</div>
			<div class="tm-pg_library_grid tm-pg_library_sets tm-pg_grid tm-pg_columns-<?php echo $data['colums'] ?>" style="display: none">
				<div class="tm-pg_column">
					<div class="tm-pg_library_item_add set" data-type="set">
						<a href="#" class="tm-pg_add-item tm-pg_add-set">
							<i class="material-icons">add</i>
							<?php esc_attr_e( 'Add Set', 'tm_gallery' ) ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /Sets -->
	<div class="tm-pg_hr"></div>
</div>
