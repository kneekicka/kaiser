<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php $childs = mphb_tmpl_get_room_type_childs_capacity(); ?>

<?php if ( !empty( $childs ) ) : ?>

	<?php
		/**
		 * @hooked MPHBSingleRoomView::_renderChildsListItemOpen	- 10
		 * @hooked MPHBSingleRoomView::_renderChildsTitle			- 20
		 */
		do_action('mphb_render_single_room_type_before_childs');
	?>

	<?php echo $childs; ?>

	<?php
		/**
		 * @hooked MPHBSingleRoomView::_renderChildsListItemClose - 10
		 */
		do_action('mphb_render_single_room_type_after_childs');
	?>

<?php endif; ?>