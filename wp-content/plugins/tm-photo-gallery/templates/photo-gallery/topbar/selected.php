<?php
/**
 * Selected top bar
 *
 * @package templates/photo-gallery/topbar
 */
?>
<div id="selected-topbar">
	<div class="tm-pg_library-filter_selected-objects">
		<div class="tm-pg_library-filter_selected-close"> 
			<a href="#">
				<i class="material-icons">close</i>
			</a>
		</div>
		<h5 class="tm-pg_library-filter_selected-title">
			<?php esc_attr_e( 'Selected', 'tm_gallery' ) ?> <span>5</span> <?php esc_attr_e( 'objects', 'tm_gallery' ) ?>
		</h5>
		<div class="tm-pg_library-filter_selected-objects-tooltip tm-pg_ui tm-pg_tooltip">
			<p data-type="sets"><span>0</span> <?php esc_attr_e( 'Sets', 'tm_gallery' ) ?></p>
			<p data-type="albums"><span>0</span> <?php esc_attr_e( 'Albums', 'tm_gallery' ) ?></p>
			<p data-type="photos"><span>0</span> <?php esc_attr_e( 'Photos', 'tm_gallery' ) ?></p>
		</div>
	</div>
	<div class="tm-pg_library-filter_selected-settings">
		<?php $this->render_html( 'photo-gallery/topbar/cover' ); ?>
		<div class="tm-pg_library-filter_selected-settings_add">
			<a href="#"><i class="material-icons">add_circle</i></a>

			<ul class="tm-pg_library-filter_selected-settings_add-menu tm-pg_ui tm-pg_popup-menu">
				<li>
					<a data-type="set" href="#"><?php esc_attr_e( 'Add to set', 'tm_gallery' ) ?></a>
				</li>
				<li>
					<a data-type="album" href="#"><?php esc_attr_e( 'Add to album', 'tm_gallery' ) ?></a>
				</li>
			</ul>
		</div>
		<div class="tm-pg_library-filter_selected-settings_delete">
			<a href="#"><i class="material-icons">delete</i></a>
		</div>
	</div>
</div>
