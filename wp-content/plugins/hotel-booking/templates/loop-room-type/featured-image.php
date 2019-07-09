<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php if (  has_post_thumbnail() ) : ?>

	<?php
		/**
		 * @hooked MPHBLoopRoomTypeView::_renderFeaturedImageParagraphOpen	- 10
		 */
		do_action('mphb_render_loop_room_type_before_featured_image');
	?>

	<?php mphb_tmpl_the_loop_room_type_thumbnail(); ?>

	<?php
		/**
		 * @hooked MPHBLoopRoomTypeView::_renderFeaturedImageParagraphClose	- 10
		 */
		do_action('mphb_render_loop_room_type_after_featured_image');
	?>

<?php endif; ?>