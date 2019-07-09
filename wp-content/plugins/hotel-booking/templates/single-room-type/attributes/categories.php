<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php $categories = mphb_tmpl_get_room_type_categories(); ?>

<?php if ( !empty( $categories ) ) : ?>

	<?php
		/**
		 * @hooked MPHBSingleRoomView::_renderCategoriesListItemOpen	- 10
		 * @hooked MPHBSingleRoomView::_renderCategoriesTitle			- 20
		 */
		do_action('mphb_render_single_room_type_before_categories');
	?>

	<?php echo $categories; ?>

	<?php
		/**
		 * @hooked MPHBSingleRoomView::_renderCategoriesListItemClose	- 10
		 */
		do_action('mphb_render_single_room_type_after_categories');
	?>

<?php endif; ?>