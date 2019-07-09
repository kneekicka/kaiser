<?php
/**
 * Gallery editor content
 *
 * @package templates\gallery\editor
 */
?>
<?php
	$this->render_html( 'gallery/editor/topbar/index' );
	$image_sizes = $this->data['image_sizes'];
?>

<div data-view="content">
	<div class="tm-pg_body-sidebar_container">
		<!-- /Filter -->
		<div class="tm-pg-scroll-cotainer">
			<div id="tm-pg-grid">
				<?php $this->render_html( 'photo-gallery/grid/sets', array( 'colums' => 3 ) ); ?>
				<?php $this->render_html( 'photo-gallery/grid/albums', array( 'colums' => 4 ) ); ?>
				<?php $this->render_html( 'photo-gallery/grid/photos', array( 'colums' => 6 ) ); ?>
			</div>
			<!-- Editor -->
			<div id="tm-pg-editor"></div>
		</div>
	</div>
	<?php $this->render_html( 'gallery/editor/slidebar/content' ); ?>
	<div class="clear"></div>
</div>
<div data-view="display" style="display: none">
	<div class="tm-pg_gallery_options_container tm-pg_gallery_options_container_display display-main">
		<?php $this->render_html( 'gallery/editor/display' ); ?>
	</div>
	<div class="tm-pg_gallery_options_container tm-pg_gallery_options_container_display_set">
		<?php $this->render_html( 'gallery/editor/display-set' ); ?>
	</div>
	<div class="tm-pg_gallery_options_container tm-pg_gallery_options_container_display_album">
		<?php $this->render_html( 'gallery/editor/display-album' ); ?>
	</div>
</div>
<div data-view="grid-settings" style="display: none">
	<div class="tm-pg_gallery_options_container tm-pg_gallery_options_container_grid_settings" >
		<?php $this->render_html( 'gallery/editor/grid-settings', $image_sizes ); ?>
	</div>
	<div class="tm-pg_gallery_options_container tm-pg_gallery_options_container_grid_settings_set" >
		<?php $this->render_html( 'gallery/editor/grid-settings-set', $image_sizes ); ?>
	</div>
	<div class="tm-pg_gallery_options_container tm-pg_gallery_options_container_grid_settings_album" >
		<?php $this->render_html( 'gallery/editor/grid-settings-album', $image_sizes ); ?>
	</div>
</div>
<div data-view="animations" style="display: none">
	<div class="tm-pg_gallery_options_container">
		<?php $this->render_html( 'gallery/editor/animations' ); ?>
	</div>
</div>
<div data-view="navigation" style="display: none">
	<div class="tm-pg_gallery_options_container">
		<?php $this->render_html( 'gallery/editor/navigation' ); ?>
	</div>
</div>
<div data-view="pagination" style="display: none">
	<div class="tm-pg_gallery_options_container" >
		<?php $this->render_html( 'gallery/editor/pagination' ); ?>
	</div>
</div>
<div data-view="lightbox" style="display: none">
	<div class="tm-pg_gallery_options_container" >
		<?php $this->render_html( 'gallery/editor/lightbox' ); ?>
	</div>
</div>

