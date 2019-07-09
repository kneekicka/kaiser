<?php
/**
 * Gallery item
 *
 * @package templates/gallery/grid/models
 */
?>
<!-- Gallery Clone -->
<div id="gallery-clone">
	<div class="tm-pg_column">
		<div class="tm-pg_library_item new" data-id="0">
			<!-- <figure class="hidden">
				<img src="#">
			</figure> -->
			<div class="tm-pg_library_item-content">
				<div class="tm-pg_library_item-content_header">
					<div class="tm-pg_library_item-content_header_left">
						<a href="#" class="tm-pg_library_item-check" data-tooltip="<?php esc_attr_e( 'Check It', 'tm_gallery' ); ?>">
							<i class="material-icons">check_circle</i>
						</a>
					</div>
					<div class="tm-pg_library_item-content_header_right">
						<a href="#" class="tm-pg_library_item-rename" data-tooltip="<?php esc_attr_e( 'Rename', 'tm_gallery' ); ?>" data-tooltip-position="right">
							<i class="material-icons">mode_edit</i>
						</a>
						<a href="#" class="tm-pg_library_item-delete" data-tooltip="<?php esc_attr_e( 'Delete', 'tm_gallery' ); ?>" data-tooltip-position="right">
							<i class="material-icons">delete</i>
						</a>
					</div>
				</div>
				<div class="tm-pg_library_item-content_footer gallery">
					<input name="new_gallery" placeholder="<?php esc_attr_e( 'Gallery name', 'tm_gallery' ) ?>" >
					<input name="edit_gallery" placeholder="<?php esc_attr_e( 'Gallery name', 'tm_gallery' ) ?>" class="hidden">
					<div class="tm-pg_library_item-content_footer_left hidden gallery">
						<a href="#" class="tm-pg_library_item-name"></a>
						<div class="tm-pg_library_item-content_description">
							<span data-type="sets-count">0</span>
								<?php esc_attr_e( 'sets', 'tm_gallery' ) ?>,
							<span data-type="albums-count">0</span>
								<?php esc_attr_e( 'albums', 'tm_gallery' ) ?>,
							<span data-type="imgs-count">0</span> <?php esc_attr_e( 'photos', 'tm_gallery' ) ?>    |   <?php /*esc_attr_e( 'Date created', 'tm_gallery' )*/ ?> <span data-type="date"></span>
						</div>
					</div>
					<div class="tm-pg_library_item-content_footer_right hidden gallery">
						<a href="#" class="tm-pg_library_item-link"><i class="material-icons">keyboard_arrow_right</i></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

