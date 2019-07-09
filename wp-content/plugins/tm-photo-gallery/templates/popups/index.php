<?php
/**
 * Popup
 *
 * @package templates/popup
 */
?>
<div id="tm-pg-popup-wraper" style="display: none">
	<div class="tm-pg_library_popup">
		<div class="tm-pg_library_popup-title">
			<h5><?php esc_attr_e( 'Select gallery', 'tm_gallery' ) ?></h5>
			<a href="#"><i class="material-icons">clear</i></a>
		</div>
		<div class="tm-pg_library_popup-content" >
			<?php foreach ( $data as $post ) : ?>
				<div class="tm-pg_library_popup-item" data-id="<?php echo $post['id'] ?>">
					<a class="tm-pg_library_popup-item_link" href="#">
						<figure>
							<?php if ( ! empty( $post['cover'] ) ) :  ?>
								<div >
									<img src="<?php echo $post['cover'][0] ?>" >
								</div>
							<?php else : ?>
								<div >
									<img src="#" class="hide" >
								</div>
							<?php endif; ?>
							<figcaption>
								<?php echo mb_strlen( $post['post']['post_title'] ) > 30 ? mb_substr( $post['post']['post_title'], 0, 29 ) . '...' : $post['post']['post_title'] ?>
							</figcaption>
						</figure>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
