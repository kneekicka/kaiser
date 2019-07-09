<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php $size = mphb_tmpl_get_room_type_size(); ?>

<?php if ( !empty( $size ) ) : ?>

	<?php
		/**
		 * @hooked MPHBSingleRoomView::_renderSizeListItemOpen	- 10
		 * @hooked MPHBSingleRoomView::_renderSizeTitle			- 20
		 */
		do_action('mphb_render_single_room_type_before_size');
	?>

	<?php echo $size; ?>

	<?php
		/**
		 * @hooked MPHBSingleRoomView::_renderSizeListItemClose	- 10
		 */
		do_action('mphb_render_single_room_type_after_size');
	?>

<?php endif; ?>