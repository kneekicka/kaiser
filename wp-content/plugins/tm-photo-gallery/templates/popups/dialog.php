<?php
/**
 * Popup dialog
 *
 * @package templates/photo-gallery/popup
 */
?>
<div id="popup-dialog-wraper" style="display: none">
	<div class="tm-pg_library_popup">
		<div class="tm-pg_library_popup-dialog-title" >
			<a href="#"><i class="material-icons">clear</i></a>
		</div>
		<div class="tm-pg_library_popup-dialog-content" >
			<h5></h5>		
		</div>
		<div class="tm-pg_library_popup-dialog-content tm-pg" >
			<a class="tm-pg_btn tm-pg_btn-primary" href="#"><?php esc_attr_e( 'Yes', 'tm_gallery' ) ?></a>
			<a class="tm-pg_btn tm-pg_btn-default" href="#"><?php esc_attr_e( 'Cancel', 'tm_gallery' ) ?></a>
		</div>
	</div>
</div>

