<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php $adults = mphb_tmpl_get_room_type_adults_capacity(); ?>

<?php if ( !empty( $adults ) ) : ?>

	<?php
		/**
		 * @hooked MPHBSingleRoomView::_renderAdultsListItemOpen	- 10
		 * @hooked MPHBSingleRoomView::_renderAdultsTitle			- 20
		 */
		do_action('mphb_render_single_room_type_before_adults');
	?>

	<?php echo $adults; ?>

	<?php
		/**
		 * @hooked MPHBSingleRoomView::_renderAdultsListItemClose - 10
		 */
		do_action('mphb_render_single_room_type_after_adults');
	?>

<?php endif; ?>