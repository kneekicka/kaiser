<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php
	/**
	 * @hooked MPHBLoopServiceView::_renderPriceTitle			- 10
	 * @hooked MPHBLoopServiceView::_renderPriceParagraphOpen	- 20
	 */
	do_action('mphb_render_loop_service_before_price');
?>

<?php mphb_tmpl_the_service_price();?>

<?php
	/**
	 * @hooked MPHBLoopServiceView::_renderPriceParagraphClose	- 10
	 */
	do_action('mphb_render_loop_service_after_price');
?>
