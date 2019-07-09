<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php if ( mphb_tmpl_has_room_type_gallery() ) : ?>

	<?php
		/**
		 *
		 */
		do_action('mphb_render_single_room_type_before_gallery');
	?>

	<?php
		mphb_tmpl_the_room_type_galery(array(
			'mphb_wrapper_class' => 'mphb-single-room-type-gallery-wrapper'
		));
	?>

	<?php
		/**
		 * @hooked MPHBSingleRoomTypeView::_enqueueGalleryScripts - 10
		 */
		do_action('mphb_render_single_room_type_after_gallery');
	?>

<?php endif; ?>
