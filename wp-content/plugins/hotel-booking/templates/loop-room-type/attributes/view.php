<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php $view = mphb_tmpl_get_room_type_view(); ?>

<?php if ( !empty( $view ) ) : ?>

	<?php
		/**
		 * @hooked MPHBLoopRoomView::_renderViewListItemOpen	- 10
		 * @hooked MPHBLoopRoomView::_renderViewTitle			- 20
		 */
		do_action('mphb_render_loop_room_type_before_view');
	?>

	<?php echo $view; ?>

	<?php
		/**
		 * @hooked MPHBLoopRoomView::_renderViewListItemClose	- 10
		 */
		do_action('mphb_render_loop_room_type_after_view');
	?>

<?php endif; ?>