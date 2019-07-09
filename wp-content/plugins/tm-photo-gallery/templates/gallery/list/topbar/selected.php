<?php
/**
 * Selected top bar
 *
 * @package templates/photo-gallery/topbar
 */
?>
<div id="selected-topbar">
	<div class="tm-pg_library-filter_selected-objects">
		<div class="tm-pg_library-filter_selected-close"> 
			<a href="#">
				<i class="material-icons">close</i>
			</a>
		</div>
		<h5 class="tm-pg_library-filter_selected-title">
			<?php esc_attr_e( 'Selected', 'tm_gallery' ) ?> <span>0</span> <?php esc_attr_e( 'objects', 'tm_gallery' ) ?>
		</h5>
	</div>

	<div class="tm-pg_library-filter_empty_sep"></div>

	<div class="tm-pg_library-filter_history_container" style="display: none">
		<a class="history" href="#">
			<i class="material-icons">history</i>
		</a>
	</div>

	<div class="tm-pg_library-filter_sep"></div>

	<div class="tm-pg_library-filter_selected-settings">
		<div class="tm-pg_library-filter_selected-settings_delete">
			<a href="#"><i class="material-icons">delete</i></a>
		</div>
	</div>
</div>
