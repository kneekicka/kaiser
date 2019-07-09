<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php
	/**
	 * @hooked MPHBSingleRoomTypeView::_renderPriceParagraphOpen	- 10
	 * @hooked MPHBSingleRoomTypeView::_renderPriceTitle			- 20
	 */
	do_action('mphb_render_single_room_type_before_price');
?>

	<?php mphb_tmpl_the_room_type_regular_price();?>

<?php
	/**
	 * @hooked MPHBSingleRoomTypeView::_renderPriceParagraphClose	- 10
	 */
	do_action('mphb_render_single_room_type_after_price');
?>