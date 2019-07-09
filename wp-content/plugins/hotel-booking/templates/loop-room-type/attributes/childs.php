<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php $childs = mphb_tmpl_get_room_type_childs_capacity(); ?>

<?php if ( !empty( $childs ) ) : ?>

	<?php
		/**
		 * @hooked MPHBLoopRoomView::_renderChildsListItemOpen	- 10
		 * @hooked MPHBLoopRoomView::_renderChildsTitle			- 20
		 */
		do_action('mphb_render_loop_room_type_before_childs');
	?>

	<?php echo $childs; ?>

	<?php
		/**
		 * @hooked MPHBLoopRoomView::_renderChildsListItemClose - 10
		 */
		do_action('mphb_render_loop_room_type_after_childs');
	?>

<?php endif; ?>