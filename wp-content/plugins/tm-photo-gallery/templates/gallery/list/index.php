<?php
/**
 * Gallery list
 *
 * @package templates/gallery/list
 */
?>
<div class="wrapper">
	<div id="post-body">
		<div class="tm-pg-scroll-cotainer">
			<div class="tm-pg_body_container tm-pg_library-list_gallery_container">
				<div class="tm-pg_library-filter">

				</div>
				<!-- Photos -->
				<div id="gallery" class="tm-pg_library tm-pg_library-list_gallery">
					<div data-id="public">
						<?php $this->render_html( 'gallery/grid/index' ); ?>
					</div>
					<div data-id="trash" style="display: none">
						<?php $this->render_html( 'gallery/grid/index' ); ?>
					</div>
				</div>
				<!-- /Photos -->
			</div>
		</div>
		<div class="clear"></div>
	</div>
</div>
