<?php
/**
 * Gallery editor topbar
 *
 * @package templates\gallery\editor\topbar
 */
?>
<div class="tm-pg_body_container tm-pg_library_gallery_container">
	<div class="tm-pg_back-btn">
		<a class="tm-pg_btn tm-pg_btn-default tm-pg_btn_icon" href="#">
			<i class="material-icons">keyboard_backspace</i><?php esc_attr_e( 'Back', 'tm_gallery' ) ?>
		</a>
	</div>
	<div class="tm-pg_gallery_title">
		<div class="tm-pg_page-title">
			<h2></h2>
		</div>
		<div class="tm-pg_gallery_save">
			<a class="tm-pg_btn tm-pg_btn-primary" href="#"><?php esc_attr_e( 'Save gallery', 'tm_gallery' ) ?></a>
			<span class="spinner"></span>
		</div>
	</div>
	<div class="tm-pg_gallery_title-tabs">
		<div class="tm-pg_gallery_tabs">
			<a class="active" href="#" data-type="images" ><?php esc_attr_e( 'Images', 'tm_gallery' ) ?></a>
			<a href="#" data-type="grid-settings" ><?php esc_attr_e( 'Grid Settings', 'tm_gallery' ) ?></a>
			<a href="#" data-type="display" ><?php esc_attr_e( 'Display', 'tm_gallery' ) ?></a>
			<a href="#" data-type="animations" ><?php esc_attr_e( 'Animation Effects', 'tm_gallery' ) ?></a>
			<a href="#" data-type="navigation" ><?php esc_attr_e( 'Navigation', 'tm_gallery' ) ?></a>
			<a href="#" data-type="lightbox" ><?php esc_attr_e( 'Lightbox settings', 'tm_gallery' ) ?></a>
		</div>
	</div>
</div>
