<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php if ( has_post_thumbnail() ) : ?>

	<?php
		/**
		 * @hooked MPHBLoopServiceView::_renderFeaturedImageParagraphOpen - 10
		 */
		do_action('mphb_render_loop_service_before_featured_image');
	?>

	<figure class="service_thumb"><?php mphb_tmpl_the_loop_service_thumbnail('woods-room-type'); ?></figure>

	<?php
		/**
		 * @hooked MPHBLoopServiceView::_renderFeaturedImageParagraphClose - 10
		 */
		do_action('mphb_render_loop_service_after_featured_image');
	?>

<?php endif; ?>
