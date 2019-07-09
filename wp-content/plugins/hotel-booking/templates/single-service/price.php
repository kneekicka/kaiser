<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php
	/**
	 * @hooked MPHBSingleServiceView::_renderPriceTitle				- 10
	 * @hooked MPHBSingleServiceView::_renderPriceParagraphOpen		- 20
	 */
	do_action('mphb_render_single_service_before_price');
?>

<?php mphb_tmpl_the_service_price();?>

<?php
	/**
	 * @hooked MPHBSingleServiceView::_renderPriceParagraphClose	- 10
	 */
	do_action('mphb_render_single_service_after_price');
?>