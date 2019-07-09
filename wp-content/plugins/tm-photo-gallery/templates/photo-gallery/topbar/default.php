<?php
/**
 * Default top bar
 *
 * @package templates/photo-gallery/topbar
 */
?>
<div id="default-topbar">
	<div class="tm-pg_library-filter_add-buttons">
		<a class="tm-pg_library-filter_add-photo tm-pg_library-filter_add-media" href="#">
			<i class="material-icons">add</i>
			<?php esc_attr_e( 'Photo', 'tm_gallery' ) ?>
		</a>
		<a class="tm-pg_library-filter_add-album tm-pg_library-filter_add-media" href="#">
			<i class="material-icons">add</i>
			<?php esc_attr_e( 'Album', 'tm_gallery' ) ?>        
		</a>
		<a class="tm-pg_library-filter_add-set tm-pg_library-filter_add-media" href="#">
			<i class="material-icons">add</i>
			<?php esc_attr_e( 'Set', 'tm_gallery' ) ?>  
		</a>
	</div>

	<div class="tm-pg_library-filter_sep"></div>
</div>
