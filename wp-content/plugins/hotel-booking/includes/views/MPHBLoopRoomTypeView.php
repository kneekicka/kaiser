<?php

class MPHBLoopRoomTypeView extends MPHBRoomTypeView {

	const TEMPLATE_CONTEXT = 'loop-room-type';

	public static function renderViewDetailsButton(){
		mphb_get_template_part( static::TEMPLATE_CONTEXT . '/view-details-button' );
	}

	public static function renderBookButton(){
		mphb_get_template_part( static::TEMPLATE_CONTEXT . '/book-button');
	}

	public static function renderGallery(){
		$roomType = MPHB()->getCurrentRoomType();
		do_action('mphb_render_loop_room_type_gallery', $roomType);

		parent::renderGallery();
	}

	public static function renderGalleryOrFeaturedImage(){
		$roomType = MPHB()->getCurrentRoomType();
		if ( $roomType->hasGallery() ) {
			self::renderGallery();
		} else {
			self::renderFeaturedImage();
		}
	}

	public static function _renderAttributesTitle(){
		echo '<h3>' . __('Room Details', 'motopress-hotel-booking') . '</h3>';
	}

	public static function _renderAttributesListOpen(){
		echo '<ul class="mphb-loop-room-type-attributes">';
	}

	public static function _renderAttributesListClose(){
		echo '</ul>';
	}

	public static function _renderCategoriesListItemOpen(){
		echo '<li class="mphb-room-type-categories">';
	}

	public static function _renderCategoriesTitle(){
		echo '<span>' . __('Categories:', 'motopress-hotel-booking') . '</span>';
	}

	public static function _renderCategoriesListItemClose(){
		echo '</li>';
	}

	public static function _renderFacilitiesListItemOpen(){
		echo '<li class="mphb-room-type-facilities">';
	}

	public static function _renderFacilitiesTitle(){
		echo '<span>' . __('Facilites:', 'motopress-hotel-booking') . '</span>';
	}

	public static function _renderFacilitiesListItemClose(){
		echo '</li>';
	}

	public static function _renderAdultsListItemOpen(){
		echo '<li class="mphb-room-type-adults-capacity">';
	}

	public static function _renderAdultsTitle(){
		echo '<span>' . __('Adults:', 'motopress-hotel-booking') . '</span>';
	}

	public static function _renderAdultsListItemClose(){
		echo '</li>';
	}

	public static function _renderChildsListItemOpen(){
		echo '<li class="mphb-room-type-childs-capacity">';
	}

	public static function _renderChildsTitle(){
		echo '<span>' . __('Childs:', 'motopress-hotel-booking') . '</span>';
	}

	public static function _renderChildsListItemClose(){
		echo '</li>';
	}

	public static function _renderBedTypeListItemOpen(){
		echo '<li class="mphb-room-type-bed-type">';
	}

	public static function _renderBedTypeTitle(){
		echo '<span>' . __('Bed Type:', 'motopress-hotel-booking') . '</span>';
	}

	public static function _renderBedTypeListItemClose(){
		echo '</li>';
	}

	public static function _renderSizeListItemOpen(){
		echo '<li class="mphb-room-type-size">';
	}

	public static function _renderSizeTitle(){
		echo '<span>' . __('Size:', 'motopress-hotel-booking') . '</span>';
	}

	public static function _renderSizeListItemClose(){
		echo '</li>';
	}

	public static function _renderViewListItemOpen(){
		echo '<li class="mphb-room-type-view">';
	}

	public static function _renderViewTitle(){
		echo '<span>' . __('View:', 'motopress-hotel-booking') . '</span>';
	}

	public static function _renderViewListItemClose(){
		echo '</li>';
	}

	public static function _renderFeaturedImageParagraphOpen(){
		echo '<p class="post-thumbnail mphb-loop-room-thumbnail">';
	}

	public static function _renderFeaturedImageParagraphClose(){
		echo '</p>';
	}

	public static function _renderPriceParagraphOpen(){
		echo '<p class="mphb-regular-price">';
	}

	public static function _renderPriceTitle(){
		if ( MPHB()->getCurrentRoomType()->getRates()->isSingleRate() ) {
			echo '<strong>' . __('Price Per Night:', 'motopress-hotel-booking') . '</strong>';
		} else {
			echo '<strong>' . __('Price From:', 'motopress-hotel-booking') . '</strong>';
		}
	}

	public static function _renderPriceParagraphClose(){
		echo '</p>';
	}

	public static function _renderTitleHeadingOpen(){
		echo '<h2 itemprop="name" class="mphb-room-type-title entry-title">';
	}

	public static function _renderTitleHeadingClose(){
		echo '</h2>';
	}

	public static function _renderBookButtonParagraphOpen(){
		echo '<p class="mphb-to-book-btn-wrapper">';
	}

	public static function _renderBookButtonParagraphClose(){
		echo '</p>';
	}

	public static function _renderViewDetailsButtonParagraphOpen(){
		echo '<p class="mphb-view-details-button-wrapper">';
	}

	public static function _renderViewDetailsButtonParagraphClose(){
		echo '</p>';
	}

	public static function _enqueueGalleryScripts(){
		wp_enqueue_script('mphb-flexslider');
		wp_enqueue_style('mphb-flexslider-css');
	}

}

/*	Attributes	*/
add_action('mphb_render_loop_room_type_before_attributes', array('MPHBLoopRoomTypeView', '_renderAttributesTitle'),		10);
add_action('mphb_render_loop_room_type_before_attributes', array('MPHBLoopRoomTypeView', '_renderAttributesListOpen'),	20);

add_action('mphb_render_loop_room_type_attributes', array('MPHBLoopRoomTypeView', 'renderCategories'),	10);
add_action('mphb_render_loop_room_type_attributes', array('MPHBLoopRoomTypeView', 'renderFacilities'),	20);
add_action('mphb_render_loop_room_type_attributes', array('MPHBLoopRoomTypeView', 'renderView'),		30);
add_action('mphb_render_loop_room_type_attributes', array('MPHBLoopRoomTypeView', 'renderSize'),		40);
add_action('mphb_render_loop_room_type_attributes', array('MPHBLoopRoomTypeView', 'renderBedType'),		50);
add_action('mphb_render_loop_room_type_attributes', array('MPHBLoopRoomTypeView', 'renderAdults'),		60);
add_action('mphb_render_loop_room_type_attributes', array('MPHBLoopRoomTypeView', 'renderChilds'),		70);

add_action('mphb_render_loop_room_type_after_attributes', array('MPHBLoopRoomTypeView', '_renderAttributesListClose'),	10);

/*	Attributes - Categories	*/
add_action('mphb_render_loop_room_type_before_categories', array('MPHBLoopRoomTypeView', '_renderCategoriesListItemOpen'),	10);
add_action('mphb_render_loop_room_type_before_categories', array('MPHBLoopRoomTypeView', '_renderCategoriesTitle'),			20);

add_action('mphb_render_loop_room_type_after_categories', array('MPHBLoopRoomTypeView', '_renderCategoriesListItemClose'),	10);

/*	Attributes - Facilities	*/
add_action('mphb_render_loop_room_type_before_facilities', array('MPHBLoopRoomTypeView', '_renderFacilitiesListItemOpen'),	10);
add_action('mphb_render_loop_room_type_before_facilities', array('MPHBLoopRoomTypeView', '_renderFacilitiesTitle'),			20);

add_action('mphb_render_loop_room_type_after_facilities', array('MPHBLoopRoomTypeView', '_renderFacilitiesListItemClose'),	10);

/*	Attributes - View	*/
add_action('mphb_render_loop_room_type_before_view', array('MPHBLoopRoomTypeView', '_renderViewListItemOpen'),	10);
add_action('mphb_render_loop_room_type_before_view', array('MPHBLoopRoomTypeView', '_renderViewTitle'),			20);

add_action('mphb_render_loop_room_type_after_view', array('MPHBLoopRoomTypeView', '_renderViewListItemClose'),	10);

/*	Attributes - Size	*/
add_action('mphb_render_loop_room_type_before_size', array('MPHBLoopRoomTypeView', '_renderSizeListItemOpen'),	10);
add_action('mphb_render_loop_room_type_before_size', array('MPHBLoopRoomTypeView', '_renderSizeTitle'),			20);

add_action('mphb_render_loop_room_type_after_size', array('MPHBLoopRoomTypeView', '_renderSizeListItemClose'),	10);

/*	Attributes - Bed Type	*/
add_action('mphb_render_loop_room_type_before_bed_type', array('MPHBLoopRoomTypeView', '_renderBedTypeListItemOpen'), 10);
add_action('mphb_render_loop_room_type_before_bed_type', array('MPHBLoopRoomTypeView', '_renderBedTypeTitle'), 20);

add_action('mphb_render_loop_room_type_after_bed_type', array('MPHBLoopRoomTypeView', '_renderBedTypeListItemClose'), 10);

/*	Attributes - Adults		*/
add_action('mphb_render_loop_room_type_before_adults', array('MPHBLoopRoomTypeView', '_renderAdultsListItemOpen'), 10);
add_action('mphb_render_loop_room_type_before_adults', array('MPHBLoopRoomTypeView', '_renderAdultsTitle'), 20);

add_action('mphb_render_loop_room_type_after_adults', array('MPHBLoopRoomTypeView', '_renderAdultsListItemClose'), 10);

/*	Attributes - Childs	*/
add_action('mphb_render_loop_room_type_before_childs', array('MPHBLoopRoomTypeView', '_renderChildsListItemOpen'), 10);
add_action('mphb_render_loop_room_type_before_childs', array('MPHBLoopRoomTypeView', '_renderChildsTitle'), 20);

add_action('mphb_render_loop_room_type_after_childs', array('MPHBLoopRoomTypeView', '_renderChildsListItemClose'), 10);

/*	Featured Image	*/
add_action('mphb_render_loop_room_type_before_featured_image', array('MPHBLoopRoomTypeView', '_renderFeaturedImageParagraphOpen'), 10);

add_action('mphb_render_loop_room_type_after_featured_image', array('MPHBLoopRoomTypeView', '_renderFeaturedImageParagraphClose'), 10);

/*	Gallery		*/
add_action('mphb_render_loop_room_type_after_gallery', array('MPHBLoopRoomTypeView', '_enqueueGalleryScripts'), 10);

/*	Price	*/
add_action('mphb_render_loop_room_type_before_price', array('MPHBLoopRoomTypeView', '_renderPriceParagraphOpen'), 10);
add_action('mphb_render_loop_room_type_before_price', array('MPHBLoopRoomTypeView', '_renderPriceTitle'), 20);

add_action('mphb_render_loop_room_type_after_price', array('MPHBLoopRoomTypeView', '_renderPriceParagraphClose'), 10);

/*	Title	*/
add_action('mphb_render_loop_room_type_before_title', array('MPHBLoopRoomTypeView', '_renderTitleHeadingOpen'), 10);

add_action('mphb_render_loop_room_type_after_title', array('MPHBLoopRoomTypeView', '_renderTitleHeadingClose'), 10);

/*	Book Button	*/
add_action('mphb_render_loop_room_type_before_book_button', array('MPHBLoopRoomTypeView', '_renderBookButtonParagraphOpen'), 10);
add_action('mphb_render_loop_room_type_after_book_button', array('MPHBLoopRoomTypeView', '_renderBookButtonParagraphClose'), 10);

/*	View Details Button	*/
add_action('mphb_render_loop_room_type_before_view_details_button', array('MPHBLoopRoomTypeView', '_renderViewDetailsButtonParagraphOpen'), 10);

add_action('mphb_render_loop_room_type_after_view_details_button', array('MPHBLoopRoomTypeView', '_renderViewDetailsButtonParagraphClose'), 10);