<?php
/**
 * Index Photo Gallery
 *
 * @package templates/photo-gallery
 */
?>
<?php $this->render_html( 'svg-preloader' ); ?>
<div id="wp-media-grid" class="wrap tm-pg" data-search="">
	<div class="wrapper">
		<div id="post-body">
			<input type="hidden" value="<?php echo wp_create_nonce( 'tm-pg-nonce' ) ?>" name="nonce">
			<div class="tm-pg_body-sidebar_container">
				<div class="tm-pg_breadcrumbs hidden" style="display: none">
				</div>

				<div class="tm-pg_back-btn hidden">
					<a class="tm-pg_btn tm-pg_btn-default tm-pg_btn_icon" href="#">
						<i class="material-icons">keyboard_backspace</i>
						<?php esc_attr_e( 'Back', 'tm_gallery' ) ?>
					</a>
				</div>

				<div class="tm-pg_page-title hidden">
					<h2></h2>
				</div>

				<div class="tm-pg_library-filter">
				</div>

				<!-- /Filter -->
				<div id="tm-pg-scroll-cotainer">
					<div id="tm-pg-grid">
						<?php $this->render_html( 'photo-gallery/grid/sets', array( 'colums' => 3 ) ); ?>
						<?php $this->render_html( 'photo-gallery/grid/albums', array( 'colums' => 4 ) ); ?>
						<?php $this->render_html( 'photo-gallery/grid/photos', array( 'colums' => 6 ) ); ?>
					</div>

					<div id="tm-pg-folder" class="hidden">
						<?php $this->render_html( 'photo-gallery/grid/albums', array( 'colums' => 4 ) ); ?>
						<?php $this->render_html( 'photo-gallery/grid/photos', array( 'colums' => 6 ) ); ?>
					</div>
					<!-- Editor -->
					<div id="tm-pg-editor"></div>
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
			</div>


			<!-- Sidebar -->
			<div id="tm-pg-sidebar-scroll-container" class="tm-pg_sidebar_container" >
				<h5 class="tm-pg_sidebar-title"><?php esc_attr_e( 'Properties', 'tm_gallery' ) ?></h5>
				<div class="tm-pg_hr"></div>
				<div id="sidebar-content"></div>
				<div class="tm-pg_sidebar_loading">
					<svg version="1.1"
						 xmlns="http://www.w3.org/2000/svg"
						 xmlns:xlink="http://www.w3.org/1999/xlink"
						 viewBox="0 0 66 66"
						 height="45px"
						 width="45px"
						 class="preloader"
						 style="display:inline-block">
					<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#preloader"></use>
					</svg>
				</div>
				<!-- /Sidebar -->
				<div class="clear"></div>
			</div>
		</div>
	</div>

	<div class="uploader-window">
		<div class="uploader-window-content">
			<h3><?php esc_attr_e( 'Drag your files here', 'tm_gallery' ) ?></h3>
		</div>
	</div>

	<div id="clone-items" style="display: none">
		<?php $this->render_html( 'photo-gallery/grid/models/image' ); ?>
		<?php $this->render_html( 'photo-gallery/grid/models/album' ); ?>
		<?php $this->render_html( 'photo-gallery/grid/models/set' ); ?>

		<?php $this->render_html( 'photo-gallery/slidebar/models/index' ); ?>
		<?php $this->render_html( 'photo-gallery/slidebar/index', $data ); ?>

		<?php $this->render_html( 'photo-gallery/topbar/default' ); ?>
		<?php $this->render_html( 'photo-gallery/topbar/selected' ); ?>

		<?php $this->render_html( 'photo-gallery/breadcrumbs' ); ?>
		<?php $this->render_html( 'photo-gallery/popup/models/item' ); ?>
	</div>
</div>
