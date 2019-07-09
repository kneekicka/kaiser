<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php
	/**
	 * @hooked MPHBLoopRoomTypeView::_renderBookButtonParagraphOpen - 10
	 */
	do_action('mphb_render_loop_room_type_before_book_button');
?>

<?php mphb_tmpl_the_loop_room_type_book_button_form(); ?>

<?php
	/**
	 * @hooked MPHBLoopRoomTypeView::_renderBookButtonParagraphClose - 10
	 */
	do_action('mphb_render_loop_room_type_after_book_button');
?>