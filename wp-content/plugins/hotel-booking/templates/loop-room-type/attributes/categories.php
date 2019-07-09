<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php $categories = mphb_tmpl_get_room_type_categories(); ?>

<?php if ( !empty( $categories ) ) : ?>

	<?php
		/**
		 * @hooked MPHBLoopRoomView::_renderCategoriesListItemOpen	- 10
		 * @hooked MPHBLoopRoomView::_renderCategoriesTitle			- 20
		 */
		do_action('mphb_render_loop_room_type_before_categories');
	?>

	<?php echo $categories; ?>

	<?php
		/**
		 * @hooked MPHBLoopRoomView::_renderCategoriesListItemClose	- 10
		 */
		do_action('mphb_render_loop_room_type_after_categories');
	?>

<?php endif; ?>