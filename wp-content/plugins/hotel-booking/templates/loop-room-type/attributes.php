<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php
	/**
	 * @hooked MPHBLoopRoomView::renderAttributesTitle	- 10
	 * @hooked MPHBLoopRoomView::renderAttributesListOpen	- 20
	 */
	do_action('mphb_render_loop_room_type_before_attributes');
?>

<?php
	/**
	 * @hooked MPHBLoopRoomTypeView::renderCategories		- 10
	 * @hooked MPHBLoopRoomTypeView::renderFacilities		- 20
	 * @hooked MPHBLoopRoomTypeView::renderView			- 30
	 * @hooked MPHBLoopRoomTypeView::renderSize			- 40
	 * @hooked MPHBLoopRoomTypeView::renderBedType		- 50
	 * @hooked MPHBLoopRoomTypeView::renderAdults			- 60
	 * @hooked MPHBLoopRoomTypeView::renderChilds			- 70
	 */
	do_action('mphb_render_loop_room_type_attributes');
?>

<?php
	/**
	 * @hooked MPHBLoopRoomView::renderAttributesListClose - 10
	 */
	do_action('mphb_render_loop_room_type_after_attributes');
?>