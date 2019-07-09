<?php
/**
 * Gallery editor display
 *
 * @package templates/gallery/editor
 */
?>
<h6><?php esc_attr_e( 'Display settings for Gallery', 'tm_gallery' ); ?></h6>
<div class="tm-pg_gallery_display">
	<div class="tm-pg_gallery_display_item" data-type="labels_gallery">
		<div class="ui tm-pg_ui tm-pg_checkbox">
			<div class="tm-pg_checkbox-item">
				<input type="checkbox" name="labels" id="show-labels-gallery">
				<label for="show-labels-gallery">
					<span class="checkbox"></span>
					<span class="name">	<?php esc_attr_e( 'Labels', 'tm_gallery' ); ?></span>
				</label>
			</div>
		</div>
	</div>
	<div class="tm-pg_gallery_display_item" data-type="set_label">
		<div class="ui tm-pg_ui tm-pg_text">
			<div class="tm-pg_text-item">
				<label for="set-label">
					<?php esc_attr_e( 'Set label', 'tm_gallery' ); ?> :
				</label>
				<input type="text" size="20" name="set_label" id="set-label">
			</div>
		</div>
	</div>
	<div class="tm-pg_gallery_display_item" data-type="album_label">
		<div class="ui tm-pg_ui tm-pg_text">
			<div class="tm-pg_text-item">
				<label for="albums-label">
					<?php esc_attr_e( 'Album label', 'tm_gallery' ); ?> :
				</label>
				<input type="text" size="20" name="album_label" id="albums-label">
			</div>
		</div>
	</div>
	<div class="tm-pg_gallery_display_item" data-type="icon_gallery">
		<div class="ui tm-pg_ui tm-pg_checkbox">
			<div class="tm-pg_checkbox-item">
				<input type="checkbox" name="icon" id="show-icon-gallery">
				<label for="show-icon-gallery">
					<span class="checkbox"></span>
					<span class="name">	<?php esc_attr_e( 'Icon', 'tm_gallery' ); ?></span>
				</label>
			</div>
		</div>
	</div>
	<div class="tm-pg_gallery_display_item" data-type="title_gallery">
		<div class="ui tm-pg_ui tm-pg_checkbox">
			<div class="tm-pg_checkbox-item">
				<input type="checkbox" name="title" id="show-title-gallery">
				<label for="show-title-gallery">
					<span class="checkbox"></span>
					<span class="name">	<?php esc_attr_e( 'Title', 'tm_gallery' ); ?></span>
				</label>
			</div>
		</div>
	</div>
	<div class="tm-pg_gallery_display_item" data-type="description_gallery">
		<div class="ui tm-pg_ui tm-pg_checkbox">
			<div class="tm-pg_checkbox-item">
				<input type="checkbox" name="description" id="show-description-gallery">
				<label for="show-description-gallery">
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
	<div class="tm-pg_gallery_display_item" data-type="counter_gallery">
		<div class="ui tm-pg_ui tm-pg_checkbox">
			<div class="tm-pg_checkbox-item">
				<input type="checkbox" name="counter" id="show-counter-gallery">
				<label for="show-counter-gallery">
					<span class="checkbox"></span>
					<span class="name">	<?php esc_attr_e( 'Meta counter', 'tm_gallery' ); ?></span>
				</label>
			</div>
		</div>
	</div>
	<div class="tm-pg_gallery_display_item" data-type="loader_color">
		<div class="ui tm-pg_ui tm-pg_text">
			<div class="tm-pg_text-item">
				<label for="loader_color">
					<?php esc_attr_e( 'Preloader Color', 'tm_gallery' ); ?> :
				</label>
				<input type="text" size="20" class="tm-color-picker" name="loader_color" id="loader_color">
			</div>
		</div>
	</div>
</div>
