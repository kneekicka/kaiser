<?php
/**
 * Gallery list topbar
 *
 * @package templates/gallery/topbar
 */
?>
<div id="default-topbar">
	<div class="tm-pg_library-filter_add-buttons">
		<a class="tm-pg_library-filter_add-gallery tm-pg_library-filter_add-media" href="#">
			<i class="material-icons">add</i><?php esc_attr_e( 'Gallery', 'tm_gallery' ); ?>
		</a>
	</div>

	<div class="tm-pg_library-filter_add-buttons" style="display:none">
		<a class="tm-pg_library-filter_all-galleries" href="#">
			<i class="material-icons">keyboard_backspace</i>
			<?php esc_attr_e( 'Back', 'tm_gallery' ); ?>
		</a>
	</div>

	<div class="tm-pg_library-filter_sep"></div>

	<div class="tm-pg_library-filter_add-buttons tm-pg_library-filter_add-buttons_with-counter">
		<a class="tm-pg_library-filter_trash" href="#">
			<?php esc_attr_e( 'Trash', 'tm_gallery' ) ?>
			<span class="tm-pg_library-filter_counter">0</span>
		</a>
	</div>
	<div class="tm-pg_library-filter_empty_sep"></div>

</div>
