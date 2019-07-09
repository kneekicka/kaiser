<?php
/**
 * Gallery editor display
 *
 * @package templates/gallery/editor
 */
?>
<h6><?php esc_attr_e( 'Display settings for Set', 'tm_gallery' ); ?></h6>
<div class="tm-pg_gallery_display">
	<div class="tm-pg_gallery_display_item" data-type="labels_set">
		<div class="ui tm-pg_ui tm-pg_checkbox">
			<div class="tm-pg_checkbox-item">
				<input type="checkbox" name="labels" id="show-labels-set">
				<label for="show-labels-set">
					<span class="checkbox"></span>
					<span class="name">	<?php esc_attr_e( 'Labels', 'tm_gallery' ); ?></span>
				</label>
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
	<div class="tm-pg_gallery_display_item" data-type="icon_set">
		<div class="ui tm-pg_ui tm-pg_checkbox">
			<div class="tm-pg_checkbox-item">
				<input type="checkbox" name="icon" id="show-icon-set">
				<label for="show-icon-set">
					<span class="checkbox"></span>
					<span class="name">	<?php esc_attr_e( 'Icon', 'tm_gallery' ); ?></span>
				</label>
			</div>
		</div>
	</div>
	<div class="tm-pg_gallery_display_item" data-type="title_set">
		<div class="ui tm-pg_ui tm-pg_checkbox">
			<div class="tm-pg_checkbox-item">
				<input type="checkbox" name="title" id="show-title-set">
				<label for="show-title-set">
					<span class="checkbox"></span>
					<span class="name">	<?php esc_attr_e( 'Title', 'tm_gallery' ); ?></span>
				</label>
			</div>
		</div>
	</div>
	<div class="tm-pg_gallery_display_item" data-type="description_set">
		<div class="ui tm-pg_ui tm-pg_checkbox">
			<div class="tm-pg_checkbox-item">
				<input type="checkbox" name="description" id="show-description-set">
				<label for="show-description-set">
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
	<div class="tm-pg_gallery_display_item" data-type="counter_set">
		<div class="ui tm-pg_ui tm-pg_checkbox">
			<div class="tm-pg_checkbox-item">
				<input type="checkbox" name="counter" id="show-counter-set">
				<label for="show-counter-set">
					<span class="checkbox"></span>
					<span class="name">	<?php esc_attr_e( 'Meta counter', 'tm_gallery' ); ?></span>
				</label>
			</div>
		</div>
	</div>
</div>
