<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php $facilities = mphb_tmpl_get_room_type_facilities(); ?>

<?php if ( !empty( $facilities ) ) : ?>

	<?php
		/**
		 * @hooked MPHBLoopRoomView::_renderFacilitiesListItemOpen	- 10
		 * @hooked MPHBLoopRoomView::_renderFacilitiesTitle			- 20
		 */
		do_action('mphb_render_loop_room_type_before_facilities');
	?>

	<?php echo $facilities; ?>

	<?php
		/**
		 * @hooked MPHBLoopRoomView::_renderFacilitiesListItemClose	- 10
		 */
		do_action('mphb_render_loop_room_type_after_facilities');
	?>

<?php endif; ?>