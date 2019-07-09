<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php
	/**
	 * @hooked MPHBSingleRoomView::renderAttributesTitle	- 10
	 * @hooked MPHBSingleRoomView::renderAttributesListOpen	- 20
	 */
	do_action('mphb_render_single_room_type_before_attributes');
?>

<?php
	/**
	 * @hooked MPHBSingleRoomTypeView::renderCategories		- 10
	 * @hooked MPHBSingleRoomTypeView::renderFacilities		- 20
	 * @hooked MPHBSingleRoomTypeView::renderView			- 30
	 * @hooked MPHBSingleRoomTypeView::renderSize			- 40
	 * @hooked MPHBSingleRoomTypeView::renderBedType		- 50
	 * @hooked MPHBSingleRoomTypeView::renderAdults			- 60
	 * @hooked MPHBSingleRoomTypeView::renderChilds			- 70
	 */
	do_action('mphb_render_single_room_type_attributes');
?>

<?php
	/**
	 * @hooked MPHBSingleRoomView::renderAttributesListClose - 10
	 */
	do_action('mphb_render_single_room_type_after_attributes');
?>