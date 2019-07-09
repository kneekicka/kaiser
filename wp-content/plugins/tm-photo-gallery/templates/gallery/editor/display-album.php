<?php
/**
 * Gallery editor display
 *
 * @package templates/gallery/editor
 */
?>
<h6><?php esc_attr_e( 'Display settings for Albums', 'tm_gallery' ); ?></h6>
<div class="tm-pg_gallery_display">
	<div class="tm-pg_gallery_display_item" data-type="icon_album">
		<div class="ui tm-pg_ui tm-pg_checkbox">
			<div class="tm-pg_checkbox-item">
				<input type="checkbox" name="icon" id="show-icon-album">
				<label for="show-icon-album">
					<span class="checkbox"></span>
					<span class="name">	<?php esc_attr_e( 'Icon', 'tm_gallery' ); ?></span>
				</label>
			</div>
		</div>
	</div>
	<div class="tm-pg_gallery_display_item" data-type="title_album">
		<div class="ui tm-pg_ui tm-pg_checkbox">
			<div class="tm-pg_checkbox-item">
				<input type="checkbox" name="title" id="show-title-album">
				<label for="show-title-album">
					<span class="checkbox"></span>
					<span class="name">	<?php esc_attr_e( 'Title', 'tm_gallery' ); ?></span>
				</label>
			</div>
		</div>
	</div>
	<div class="tm-pg_gallery_display_item" data-type="description_album">
		<div class="ui tm-pg_ui tm-pg_checkbox">
			<div class="tm-pg_checkbox-item">
				<input type="checkbox" name="description" id="show-description-album">
				<label for="show-description-album">
					<span class="checkbox"></span>
					<span class="name">	<?php esc_attr_e( 'Description', 'tm_gallery' ); ?></span>
				</label>
			</div>
		</div>
	</div>
	<div class="tm-pg_gallery_display_item" data-type="description_trim">
		<div class="ui tm-pg_ui tm-pg_number">
			<div class="tm-pg_number-item"><?php
				$this->number_input( array(
					'label'     => esc_attr__( 'Description trim words', 'tm_gallery' ),
					'name'      => 'description_trim',
					'id'        => 'description-trim',
					'min'       => 5,
					'max'       => 25,
					'maxlength' => 2,
				) );
			?></div>
		</div>
	</div>
</div>
