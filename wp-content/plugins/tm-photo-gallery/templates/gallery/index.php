<?php
/**
 * Gallery index
 *
 * @package templates\gallery
 */
?>
<?php $this->render_html( 'svg-preloader' ); ?>
<div id="wp-gallery-list" class="wrap tm-pg" data-search="">
	<?php $this->render_html( 'gallery/list/index' ); ?>
</div>
<div id="wp-gallery-editor" class="wrap tm-pg" data-search="" style="display: none">
	<?php $this->render_html( 'gallery/editor/content' ); ?>
	<div class="tm-pg_body-sidebar_loading">
		<svg version="1.1"
			 xmlns="http://www.w3.org/2000/svg"
			 xmlns:xlink="http://www.w3.org/1999/xlink"
			 viewBox="0 0 66 66"
			 height="60px"
			 width="60px"
			 class="preloader"
			 style="display:inline-block">
			<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#preloader"></use>
		</svg>
	</div>
</div>
<div id="clone-items" style="display: none">
	<?php $this->render_html( 'gallery/list/topbar/default' ); ?>
	<?php $this->render_html( 'gallery/list/topbar/selected' ); ?>
	<?php $this->render_html( 'photo-gallery/grid/models/image' ); ?>
	<?php $this->render_html( 'photo-gallery/grid/models/album' ); ?>
	<?php $this->render_html( 'photo-gallery/grid/models/set' ); ?>
	<?php $this->render_html( 'gallery/grid/models/gallery' ); ?>
	<?php $this->render_html( 'gallery/grid/models/new-gallery' ); ?>
	<?php $this->render_html( 'gallery/grid/models/right-grid' ); ?>
</div>
<div class="tm-pg_body-sidebar_loading">
	<svg version="1.1"
		 xmlns="http://www.w3.org/2000/svg"
		 xmlns:xlink="http://www.w3.org/1999/xlink"
		 viewBox="0 0 66 66"
		 height="60px"
		 width="60px"
		 class="preloader"
		 style="display:inline-block">
		<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#preloader"></use>
	</svg>
</div>
