<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php
	/**
	 * @hooked MPHBLoopRoomTypeView::_renderPriceParagraphOpen	- 10
	 * @hooked MPHBLoopRoomTypeView::_renderPriceTitle			- 20
	 */
	do_action('mphb_render_loop_room_type_before_price');
?>

	<?php mphb_tmpl_the_room_type_regular_price();?>

<?php
	/**
	 * @hooked MPHBLoopRoomTypeView::_renderPriceParagraphClose	- 10
	 */
	do_action('mphb_render_loop_room_type_after_price');
?>