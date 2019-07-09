<?php
/**
 * Gallery editor lightbox
 *
 * @package templates/gallery/editor
 */
?>
<h6><?php esc_attr_e( 'Lightbox settings', 'tm_gallery' ); ?></h6>
<div class="tm-pg_gallery_pagination">
	<div class="tm-pg_gallery_lightbox_item" data-type="autoplay">
		<div class="ui tm-pg_ui tm-pg_checkbox">
			<div class="tm-pg_checkbox-item">
				<input type="checkbox" name="autoplay" id="lightbox-autoplay">
				<label for="lightbox-autoplay">
					<span class="checkbox"></span>
					<span class="name">	<?php esc_attr_e( 'Autoplay', 'tm_gallery' ); ?></span>
				</label>
			</div>
		</div>
	</div>
	<div class="tm-pg_gallery_lightbox_item" data-type="fullscreen">
		<div class="ui tm-pg_ui tm-pg_checkbox">
			<div class="tm-pg_checkbox-item">
				<input type="checkbox" name="fullscreen" id="lightbox-fullscreen">
				<label for="lightbox-fullscreen">
					<span class="checkbox"></span>
					<span class="name"> <?php esc_attr_e( ' Fullscreen mode', 'tm_gallery' ); ?>
				</label>
			</div>
		</div>
	</div>
	<div class="tm-pg_gallery_lightbox_item" data-type="thumbnails">
		<div class="ui tm-pg_ui tm-pg_checkbox">
			<div class="tm-pg_checkbox-item">
				<input type="checkbox" name="thumbnails" id="lightbox-thumbnails">
				<label for="lightbox-thumbnails">
					<span class="checkbox"></span>
					<span class="name"><?php esc_attr_e( 'Thumbnails', 'tm_gallery' ); ?></span>
				</label>
			</div>
		</div>
	</div>
	<div class="tm-pg_gallery_lightbox_item" data-type="arrows">
		<div class="ui tm-pg_ui tm-pg_checkbox">
			<div class="tm-pg_checkbox-item">
				<input type="checkbox" name="arrows" id="lightbox-arrows">
				<label for="lightbox-arrows">
					<span class="checkbox"></span>
					<span class="name"><?php esc_attr_e( 'Nav Arrows', 'tm_gallery' ); ?></span>
				</label>
			</div>
		</div>
	</div>
</div>
