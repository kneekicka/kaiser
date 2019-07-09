<?php
/**
 * Photo editor
 *
 * @package templates/photo-gallery
 */
?>
<script type="text/html" id="tm-pg-editor-tpl">

	<div class="tm-pg_editor">
		<div class="tm-pg_editor_controls">
			<a href="#" class="tm-pg_btn tm-pg_btn-default tm-pg_editor_controls_cancel"><i class="material-icons">keyboard_backspace</i>Back</a>
			<div class="tm-pg_editor_controls-effects">
				<a href="#" class="tm-pg_editor_controls_left-rotate" data-tooltip="<?php echo esc_attr__( 'Rotate left', 'tm_gallery' ); ?>"><i class="material-icons">replay</i></a>
				<a href="#" class="tm-pg_editor_controls_right-rotate" data-tooltip="<?php echo esc_attr__( 'Rotate right', 'tm_gallery' ); ?>"><i class="material-icons">replay</i></a>
				<a href="#" class="tm-pg_editor_controls_focus" data-tooltip="<?php esc_attr_e( 'Focus', 'tm_gallery' ) ?>"><i class="material-icons">filter_center_focus</i></a>
			</div>
			<a href="#" class="tm-pg_btn tm-pg_btn-primary tm-pg_editor_controls_save" disabled="disabled"><?php esc_attr_e( 'Save', 'tm_gallery' ) ?></a>
		</div>
		<div class="tm-pg_editor_image_container">
			<div class="tm-pg_editor_image_wrapper">
				<div id="tm-pg-editor-image" class="tm-pg_editor_image">
					<div class="tm-pg_editor_focus-box_image-layer tm-pg_editor_focus-box_image_background-layer">
						<img src="<%= post_data . thumbnails . copy . url %>">
					</div>

					<div class="tm-pg_editor_focus-box_overlay"></div>

					<div class="tm-pg_editor_focus-box">

						<div class="tm-pg_editor_focus-box_image-layer tm-pg_editor_focus-box_image_visible-layer" style="background-image: url('<%= _ . escape( post_data . thumbnails . copy . url ) %>');"></div>

						<div class="tm-pg_editor_focus-box_center_big"></div>
						<div class="tm-pg_editor_focus-box_center_small"></div>
					</div>
				</div>
			</div>
			<div class="tm-pg_editor_image-navigations">
				<div class="tm-pg_editor_image-navigations_left">
					<a href="#" class="tm-pg_editor_controls_navigate_previous"><i class="material-icons">keyboard_arrow_left</i></a>
				</div>
				<div class="tm-pg_editor_image-navigations_right">
					<a href="#" class="tm-pg_editor_controls_navigate_next"><i class="material-icons">keyboard_arrow_right</i></a>
				</div>
			</div>
			<div class="tm-pg_editor_image-counter">
				<%= currentIndex + 1 %> from <%= totalCount %>
			</div>
		</div>
	</div>

</script>
