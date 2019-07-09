<?php
/**
 * Album grid
 *
 * @package templates/photo-gallery/grid/models
 */
?>
<!-- Album Clone -->
<div id="album-clone">
	<div class="tm-pg_column">
		<div class="tm-pg_library_item new album" data-id="0" data-type="album">
			<!-- <figure class="hidden">
				<img src="#">
			</figure> -->
			<div class="tm-pg_library_item-content">
				<div class="tm-pg_library_item-content_header hidden">
					<a href="#" class="tm-pg_library_item-check">
						<i class="material-icons">check_circle</i>
					</a>
					<a href="#" class="tm-pg_library_item-check-circle">
						<i class="material-icons">panorama_fish_eye</i>
					</a>
				</div>
				<div class="tm-pg_library_item-content_footer">
					<input name="album_name" placeholder="<?php esc_attr_e( 'Album name', 'tm_gallery' ) ?>" >
					<div class="tm-pg_library_item-content_footer_left hidden">
						<a href="#" class="tm-pg_library_item-name"></a>
						<div class="tm-pg_library_item-content_description">
							<span data-type="count">0</span>
								<?php esc_attr_e( 'photos', 'tm_gallery' ) ?>
								|	<?php /*esc_attr_e( 'Date created', 'tm_gallery' )*/ ?>
							<span data-type="date"></span>
						</div>
					</div>
					<div class="tm-pg_library_item-content_footer_right hidden">
						<a href="#" class="tm-pg_library_item-link">
							<i class="material-icons">keyboard_arrow_right</i>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
