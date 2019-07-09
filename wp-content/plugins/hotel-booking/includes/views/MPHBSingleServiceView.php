<?php

class MPHBSingleServiceView {

	const TEMPLATE_CONTEXT = 'single-service';

	public static function renderPrice(){
		mphb_get_template_part( static::TEMPLATE_CONTEXT . '/price');
	}

	public static function _renderMetas(){
		self::renderPrice();
	}

	public static function _renderPriceTitle(){
		echo '<h2>' . __('Price', 'motopress-hotel-booking') . '</h2>';
	}

	public static function _renderPriceParagraphOpen(){
		echo '<p class="mphb-price-wrapper">';
	}

	public static function _renderPriceParagraphClose(){
		echo '</p>';
	}

}

add_action('mphb_render_single_service_before_price', array('MPHBSingleServiceView', '_renderPriceTitle'), 10);
add_action('mphb_render_single_service_before_price', array('MPHBSingleServiceView', '_renderPriceParagraphOpen'), 20);

add_action('mphb_render_single_service_after_price', array('MPHBSingleServiceView', '_renderPriceParagraphClose'), 10);