<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php $size = mphb_tmpl_get_room_type_size(); ?>

<?php if ( !empty( $size ) ) : ?>

	<?php
		/**
		 * @hooked MPHBLoopRoomView::_renderSizeListItemOpen	- 10
		 * @hooked MPHBLoopRoomView::_renderSizeTitle			- 20
		 */
		do_action('mphb_render_loop_room_type_before_size');
	?>

	<?php echo $size; ?>

	<?php
		/**
		 * @hooked MPHBLoopRoomView::_renderSizeListItemClose	- 10
		 */
		do_action('mphb_render_loop_room_type_after_size');
	?>

<?php endif; ?>