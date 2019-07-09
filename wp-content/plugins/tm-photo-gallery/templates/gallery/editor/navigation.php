<?php
/**
 * Gallery editor navigation
 *
 * @package templates/gallery/editor
 */
?>
<h6><?php esc_attr_e( 'Filter options', 'tm_gallery' ); ?></h6>
<div class="tm-pg_gallery_filters">
	<div class="tm-pg_gallery_filters_item" data-type="show">
		<div class="ui tm-pg_ui tm-pg_checkbox">
			<div class="tm-pg_checkbox-item">
				<input type="checkbox" name="show-filter" id="show-filter">
				<label for="show-filter">
					<span class="checkbox"></span>
					<span class="name">	<?php esc_attr_e( 'Show filter', 'tm_gallery' ); ?></span>
				</label>
			</div>
		</div>
	</div>
	<div class="tm-pg_gallery_filters_item" data-type="type">
		<label><?php esc_attr_e( 'Filter type', 'tm_gallery' ); ?></label>
		<select class="select2" data-placeholder="<?php esc_attr_e( 'Choose filter type', 'tm_gallery' ) ?>" >
			<option value="line"><?php esc_attr_e( 'Line', 'tm_gallery' ); ?></option>
			<option value="dropdown"><?php esc_attr_e( 'Dropdown', 'tm_gallery' ); ?></option>
		</select>
	</div>
	<div class="tm-pg_gallery_filters_item" data-type="by">
		<label><?php esc_attr_e( 'Filter by', 'tm_gallery' ); ?></label>
		<select class="select2" data-placeholder="<?php esc_attr_e( 'Choose filter type', 'tm_gallery' ) ?>" >
			<option value="category"><?php esc_attr_e( 'Category', 'tm_gallery' ); ?></option>
			<option value="tag"><?php esc_attr_e( 'Tag', 'tm_gallery' ); ?></option>
		</select>
	</div>
</div>
<div class="tm-pg_spacer"></div>
<h6><?php esc_attr_e( 'Pagination options', 'tm_gallery' ); ?></h6>
<div class="tm-pg_gallery_pagination">
	<div class="tm-pg_gallery_pagination_item" data-type="images_per_page"><?php
		$this->number_input( array(
			'label'     => esc_attr__( 'Images per page', 'tm_gallery' ),
			'name'      => 'images_per_page',
			'id'        => 'images-per-page',
			'min'       => 1,
			'max'       => 100,
			'maxlength' => 3,
		) );
	?></div>
	<div class="tm-pg_gallery_pagination_item" data-type="load_more_btn">
		<div class="ui tm-pg_ui tm-pg_checkbox">
			<div class="tm-pg_checkbox-item">
				<input type="checkbox" name="load-more" id="load-more">
				<label for="load-more">
					<span class="checkbox"></span>
					<span class="name"> <?php esc_attr_e( 'Show "Load more" button ', 'tm_gallery' ); ?>
				</label>
			</div>
		</div>
	</div>
	<div class="tm-pg_gallery_pagination_item" data-type="load_more_grid">
		<div class="ui tm-pg_ui tm-pg_checkbox">
			<div class="tm-pg_checkbox-item">
				<input type="checkbox" name="load-more-img" id="load-more-img">
				<label for="load-more-img">
					<span class="checkbox"></span>
					<span class="name"><?php esc_attr_e( 'Show "Load more" grid', 'tm_gallery' ); ?></span>
				</label>
			</div>
		</div>
	</div>
	<div class="tm-pg_gallery_pagination_item" data-type="pagination_block">
		<div class="ui tm-pg_ui tm-pg_checkbox">
			<div class="tm-pg_checkbox-item">
				<input type="checkbox" name="load-more-page" id="load-more-page">
				<label for="load-more-page">
					<span class="checkbox"></span>
					<span class="name"><?php esc_attr_e( 'Show pagination block', 'tm_gallery' ); ?></span>
				</label>
			</div>
		</div>
	</div>
</div>
