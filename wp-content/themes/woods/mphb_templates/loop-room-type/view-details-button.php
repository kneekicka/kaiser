<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php
	/**
	 * @hooked MPHBLoopRoomTypeView::_renderViewDetailsButtonParagraphOpen - 10
	 */
	do_action('mphb_render_loop_room_type_before_view_details_button');
?>

<?php
	/**
	 * @hooked MPHBLoopRoomTypeView::_renderViewDetailsButtonParagraphClose - 10
	 */
	do_action('mphb_render_loop_room_type_after_view_details_button');
?>
