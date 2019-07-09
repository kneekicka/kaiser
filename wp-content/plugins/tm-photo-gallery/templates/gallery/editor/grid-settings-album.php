<?php
/**
 * Gallery editor display
 *
 * @package templates/gallery/editor
 */
?>
<h6><?php esc_attr_e( 'Choose display type for your album', 'tm_gallery' ); ?></h6>
<div class="tm-pg_gallery_grid-settings-type tm-pg_grid tm-pg_columns-4">
	<div class="tm-pg_gallery_grid-settings-type_item tm-pg_column">
		<a href="#" class="tm-pg_gallery_grid-settings-type_grid" data-type="grid">
			<div class="tm-pg_gallery_grid-settings-type_image">
				<img src="<?php echo TM_PG_MEDIA_URL ?>icons/display_grid.svg">
			</div>
			<?php esc_attr_e( 'Grid', 'tm_gallery' ); ?>
		</a>
	</div>

	<div class="tm-pg_gallery_grid-settings-type_item tm-pg_column">
		<a href="#" class="tm-pg_gallery_grid-settings-type_masonry" data-type="masonry">
			<div class="tm-pg_gallery_grid-settings-type_image">
				<img src="<?php echo TM_PG_MEDIA_URL ?>icons/display_masonry.svg">
			</div>
			<?php esc_attr_e( 'Masonry', 'tm_gallery' ); ?>
		</a>
	</div>

	<div class="tm-pg_gallery_grid-settings-type_item tm-pg_column">
		<a href="#" class="tm-pg_gallery_grid-settings-type_justify" data-type="justify">
			<div class="tm-pg_gallery_grid-settings-type_image">
				<img src="<?php echo TM_PG_MEDIA_URL ?>icons/display_masonry.svg">
			</div>
			<?php esc_attr_e( 'Justify', 'tm_gallery' ); ?>
		</a>
	</div>

	<div class="tm-pg_gallery_grid-settings-type_properties tm-pg_column">
		<div class="tm-pg_gallery_grid-settings-type_properties_holder">
			<h5 class="tm-pg_sidebar-title"><?php esc_attr_e( 'Properties', 'tm_gallery' ) ?></h5>
			<div class="tm-pg_hr"></div>
			<div data-type="colums">
				<p>
					<label for="colums">
						<span><?php esc_attr_e( 'Colums count', 'tm_gallery' ); ?> </span>
					</label>
					<select class="select2" data-placeholder="<?php esc_attr_e( 'Choose colum', 'tm_gallery' ) ?>" id="colums">
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
					</select>
				</p>
			</div>
			<div data-type="height">
				<p><?php
					$this->number_input( array(
						'label'     => esc_attr__( 'Fixed height', 'tm_gallery' ),
						'name'      => 'height',
						'id'        => 'height',
						'min'       => 50,
						'max'       => 350,
						'maxlength' => 3
					) );
				?></p>
			</div>
			<div data-type="gutter">
				<p><?php
					$this->number_input( array(
						'label'     => esc_attr__( 'Gutter', 'tm_gallery' ),
						'name'      => 'gutter',
						'id'        => 'gutter',
						'min'       => 0,
						'max'       => 50,
						'maxlength' => 2
					) );
				?></p>
			</div>
			<div data-type="grid-images-size">
				<p>
					<label for="grid-image-size">
						<span><?php esc_attr_e( 'Images size', 'tm_gallery' ); ?> </span>
					</label>
					<select class="select2" data-placeholder="<?php esc_attr_e( 'Images size', 'tm_gallery' ) ?>" id="grid-images-size">
						<?php
							foreach( $data['grid'] as $type => $value ) {
								echo '<option value="' . $type . '">' . $type . ' ' . $value['width'] . 'x' . $value['height'] . '</option>';
							}
						?>
					</select>
				</p>
			</div>
			<div data-type="masonry-images-size">
				<p>
					<label for="masonry-image-size">
						<span><?php esc_attr_e( 'Images size', 'tm_gallery' ); ?> </span>
					</label>
					<select class="select2" data-placeholder="<?php esc_attr_e( 'Images size', 'tm_gallery' ) ?>" id="masonry-images-size">
						<?php
							foreach( $data['masonry'] as $type => $value ) {
								echo '<option value="' . $type . '">' . $type . ' ' . $value['width'] . 'x' . $value['height'] . '</option>';
							}
						?>
					</select>
				</p>
			</div>
			<div data-type="justify-images-size">
				<p>
					<label for="justify-image-size">
						<span><?php esc_attr_e( 'Images size', 'tm_gallery' ); ?> </span>
					</label>
					<select class="select2" data-placeholder="<?php esc_attr_e( 'Images size', 'tm_gallery' ) ?>" id="justify-images-size">
						<?php
							foreach( $data['justify'] as $type => $value ) {
								echo '<option value="' . $type . '">' . $type . ' ' . $value['width'] . 'x' . $value['height'] . '</option>';
							}
						?>
					</select>
				</p>
			</div>
		</div>
	</div>
</div>
