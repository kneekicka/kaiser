<?php
/**
 * Preloader
 *
 * @package templates/uploader
 */
?>
<script type="text/javascript">
	var resize_height = <?php echo $data['uploader_data']['large_size_h']; ?>,
		resize_width = <?php echo $data['uploader_data']['large_size_w']; ?>,
		wpUploaderInit = <?php echo wp_json_encode( $data['uploader_data']['plupload_init'] ); ?>;
</script>

<div id="plupload-upload-ui" class="hide-if-no-js drag-drop">
	<div id="drag-drop-area" class="uploader-inline">
		<button class="close dashicons dashicons-no" parent="plupload-upload-ui">
			<span class="screen-reader-text"><?php esc_attr_e( 'Close uploader' ) ?></span>
		</button>
		<div class="drag-drop-inside">
			<p class="drag-drop-info"> <?php esc_attr_e( 'Drop files anywhere to upload' ); ?> </p>

			<p><?php esc_attr_e( 'OR' ) ?></p>

			<p class="drag-drop-buttons">
				<input id="plupload-browse-button" type="button" value="<?php esc_attr_e( 'Select Files' ); ?>" accept=".gif,.jpg,.png,.jpeg" class="button"
					   style="position: relative; z-index: 1;">
			</p>

			<p><?php esc_attr_e( '(Limitations: JPG, PNG, GIF, Max size: 100 Mb)' ); ?></p>
		</div>
	</div>
</div>

