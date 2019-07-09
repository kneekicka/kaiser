<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php $bedType = mphb_tmpl_get_room_type_bed_type(); ?>

<?php if ( !empty( $bedType ) ) : ?>
	<?php
		/**
		 * @hooked MPHBLoopRoomView::_renderBedTypeListItemOpen	- 10
		 * @hooked MPHBLoopRoomView::_renderBedTypeTitle			- 20
		 */
		do_action('mphb_render_loop_room_type_before_bed_type');
	?>

	<?php echo $bedType; ?>

	<?php
		/**
		 * @hooked MPHBLoopRoomView::_renderBedTypeListItemClose - 10
		 */
		do_action('mphb_render_loop_room_type_after_bed_type');
	?>

<?php endif; ?>