<?php

class MPHBLoopServiceView {

	const TEMPLATE_CONTEXT = 'loop-service';

	public static function renderTitle(){
		mphb_get_template_part( static::TEMPLATE_CONTEXT . '/title');
	}

	public static function renderExcerpt(){
		mphb_get_template_part( static::TEMPLATE_CONTEXT . '/excerpt');
	}

	public static function renderDescription(){
		mphb_get_template_part( static::TEMPLATE_CONTEXT . '/description');
	}

	public static function renderFeaturedImage(){
		mphb_get_template_part( static::TEMPLATE_CONTEXT . '/featured-image');
	}

	public static function renderPrice(){
		mphb_get_template_part( static::TEMPLATE_CONTEXT . '/price');
	}

	public static function _renderFeaturedImageParagraphOpen(){
		echo '<p class="mphb-loop-service-thumbnail">';
	}

	public static function _renderFeaturedImageParagraphClose(){
		echo '</p>';
	}

	public static function _renderPriceParagraphOpen(){
		echo '<p class="mphb-price-wrapper">';
	}

	public static function _renderPriceParagraphClose(){
		echo '</p>';
	}

	public static function _renderPriceTitle(){
		echo '<strong>' . __('Price:', 'motopress-hotel-booking') . '</strong>';
	}

	public static function _renderTitleHeadingOpen(){
		echo '<h2 itemprop="name" class="mphb-service-title">';
	}

	public static function _renderTitleHeadingClose(){
		echo '</h2>';
	}

}

add_action('mphb_render_loop_service_before_featured_image', array('MPHBLoopServiceView', '_renderFeaturedImageParagraphOpen'), 10);

add_action('mphb_render_loop_service_after_featured_image', array('MPHBLoopServiceView', '_renderFeaturedImageParagraphClose'), 10);

add_action('mphb_render_loop_service_before_price', array('MPHBLoopServiceView', '_renderPriceParagraphOpen'), 10);
add_action('mphb_render_loop_service_before_price', array('MPHBLoopServiceView', '_renderPriceTitle'), 20);

add_action('mphb_render_loop_service_after_price', array('MPHBLoopServiceView', '_renderPriceParagraphClose'), 10);

add_action('mphb_render_loop_service_before_title', array('MPHBLoopServiceView', '_renderTitleHeadingOpen'), 10);
add_action('mphb_render_loop_service_after_title', array('MPHBLoopServiceView', '_renderTitleHeadingClose'), 10);
