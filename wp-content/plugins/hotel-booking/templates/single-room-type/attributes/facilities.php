<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php $facilities = mphb_tmpl_get_room_type_facilities(); ?>

<?php if ( !empty( $facilities ) ) : ?>

	<?php
		/**
		 * @hooked MPHBSingleRoomView::_renderFacilitiesListItemOpen	- 10
		 * @hooked MPHBSingleRoomView::_renderFacilitiesTitle			- 20
		 */
		do_action('mphb_render_single_room_type_before_facilities');
	?>

	<?php echo $facilities; ?>

	<?php
		/**
		 * @hooked MPHBSingleRoomView::_renderFacilitiesListItemClose	- 10
		 */
		do_action('mphb_render_single_room_type_after_facilities');
	?>

<?php endif; ?>