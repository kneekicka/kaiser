<?php

class MPHBSingleRoomTypeView extends MPHBRoomTypeView{

	const TEMPLATE_CONTEXT = 'single-room-type';

	public static function renderReservationForm(){
		if ( MPHB()->getCurrentRoomType()->getRates()->hasActiveRates() ) {
			mphb_get_template_part( static::TEMPLATE_CONTEXT . '/reservation-form');
		}
	}

	public static function renderCalendar(){
		mphb_get_template_part( static::TEMPLATE_CONTEXT . '/calendar');
	}

	public static function renderGallery(){
		$roomType = MPHB()->getCurrentRoomType();
		do_action('mphb_render_single_room_type_gallery', $roomType);

		parent::renderGallery();
	}

	public static function _renderPageWrapperStart(){

		$template = get_option( 'template' );

		switch( $template ) {
			case 'twentyeleven' :
				echo '<div id="primary"><div id="content" role="main" class="twentyeleven">';
				break;
			case 'twentytwelve' :
				echo '<div id="primary" class="site-content"><div id="content" role="main" class="twentytwelve">';
				break;
			case 'twentythirteen' :
				echo '<div id="primary" class="site-content"><div id="content" role="main" class="entry-content twentythirteen">';
				break;
			case 'twentyfourteen' :
				echo '<div id="primary" class="content-area"><div id="content" role="main" class="site-content twentyfourteen"><div class="tfwc">';
				break;
			case 'twentyfifteen' :
				echo '<div id="primary" role="main" class="content-area twentyfifteen"><div id="main" class="site-main t15wc">';
				break;
			case 'twentysixteen' :
				echo '<div id="primary" class="content-area twentysixteen"><main id="main" class="site-main" role="main">';
				break;
			default :
				echo '<div id="container"><div id="content" role="main">';
				break;
		}
	}

	public static function _renderPageWrapperEnd(){

		$template = get_option( 'template' );

		switch( $template ) {
			case 'twentyeleven' :
				echo '</div></div>';
				break;
			case 'twentytwelve' :
				echo '</div></div>';
				break;
			case 'twentythirteen' :
				echo '</div></div>';
				break;
			case 'twentyfourteen' :
				echo '</div></div></div>';
				get_sidebar( 'content' );
				break;
			case 'twentyfifteen' :
				echo '</div></div>';
				break;
			case 'twentysixteen' :
				echo '</div></main>';
				break;
			default :
				echo '</div></div>';
				break;
		}

	}

	public static function _renderCalendarTitle(){
		echo '<h2>' . __('Room Availability', 'motopress-hotel-booking') . '</h2>';
	}

	public static function _renderAttributesTitle(){
		echo '<h2>' . __('Room Details', 'motopress-hotel-booking') . '</h2>';
	}

	public static function _renderAttributesListOpen(){
		echo '<ul class="mphb-single-room-type-attributes">';
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
		echo '<p class="post-thumbnail mphb-single-room-type-post-thumbnail">';
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
		echo '<h1 itemprop="name" class="mphb-room-type-title entry-title">';
	}

	public static function _renderTitleHeadingClose(){
		echo '</h1>';
	}

	public static function _renderReservationFormTitle(){
		echo '<h2>' . __('Reservation Form', 'motopress-hotel-booking') . '</h2>';
	}

	public static function _renderMetas(){
		self::renderGallery();
		self::renderAttributes();
		self::renderPrice();
		self::renderCalendar();
		self::renderReservationForm();
	}

	public static function _enqueueGalleryScripts(){
		wp_enqueue_script('mphb-magnific-popup');
		wp_enqueue_style('mphb-magnific-popup-css');
		?>
		<script type="text/javascript">
		document.addEventListener("DOMContentLoaded", function(event) {
			(function($){
				$(function(){
					var galleryItems = $(".mphb-single-room-type-gallery-wrapper .gallery-icon>a");
					if ( galleryItems.length && $.magnificPopup ) {
						galleryItems.magnificPopup({
							type: 'image',
							gallery:{
							  enabled:true
							}
						});
					}
				});
			})(jQuery);
		});
		</script>
		<?php
	}

}

/*	Wrapper		*/
add_action('mphb_render_single_room_type_wrapper_start', array('MPHBSingleRoomTypeView', '_renderPageWrapperStart'), 10);
add_action('mphb_render_single_room_type_wrapper_end', array('MPHBSingleRoomTypeView', '_renderPageWrapperEnd'), 10);

/*	Content	*/
add_action('mphb_render_single_room_type_content', array('MPHBSingleRoomTypeView', 'renderTitle'),				10);
add_action('mphb_render_single_room_type_content', array('MPHBSingleRoomTypeView', 'renderFeaturedImage'),		20);
add_action('mphb_render_single_room_type_content', array('MPHBSingleRoomTypeView', 'renderDescription'),		30);
add_action('mphb_render_single_room_type_content', array('MPHBSingleRoomTypeView', 'renderPrice'),				40);
add_action('mphb_render_single_room_type_content', array('MPHBSingleRoomTypeView', 'renderAttributes'),			50);
add_action('mphb_render_single_room_type_content', array('MPHBSingleRoomTypeView', 'renderCalendar'),			60);
add_action('mphb_render_single_room_type_content', array('MPHBSingleRoomTypeView', 'renderReservationForm'),	70);

/*	Attributes	*/
add_action('mphb_render_single_room_type_before_attributes', array('MPHBSingleRoomTypeView', '_renderAttributesTitle'),		10);
add_action('mphb_render_single_room_type_before_attributes', array('MPHBSingleRoomTypeView', '_renderAttributesListOpen'),	20);

add_action('mphb_render_single_room_type_attributes', array('MPHBSingleRoomTypeView', 'renderCategories'),	10);
add_action('mphb_render_single_room_type_attributes', array('MPHBSingleRoomTypeView', 'renderFacilities'),	20);
add_action('mphb_render_single_room_type_attributes', array('MPHBSingleRoomTypeView', 'renderView'),		30);
add_action('mphb_render_single_room_type_attributes', array('MPHBSingleRoomTypeView', 'renderSize'),		40);
add_action('mphb_render_single_room_type_attributes', array('MPHBSingleRoomTypeView', 'renderBedType'),		50);
add_action('mphb_render_single_room_type_attributes', array('MPHBSingleRoomTypeView', 'renderAdults'),		60);
add_action('mphb_render_single_room_type_attributes', array('MPHBSingleRoomTypeView', 'renderChilds'),		70);

add_action('mphb_render_single_room_type_after_attributes', array('MPHBSingleRoomTypeView', '_renderAttributesListClose'),	10);

/*	Attributes - Categories	*/
add_action('mphb_render_single_room_type_before_categories', array('MPHBSingleRoomTypeView', '_renderCategoriesListItemOpen'),	10);
add_action('mphb_render_single_room_type_before_categories', array('MPHBSingleRoomTypeView', '_renderCategoriesTitle'),			20);

add_action('mphb_render_single_room_type_after_categories', array('MPHBSingleRoomTypeView', '_renderCategoriesListItemClose'),	10);

/*	Attributes - Facilities	*/
add_action('mphb_render_single_room_type_before_facilities', array('MPHBSingleRoomTypeView', '_renderFacilitiesListItemOpen'),	10);
add_action('mphb_render_single_room_type_before_facilities', array('MPHBSingleRoomTypeView', '_renderFacilitiesTitle'),			20);

add_action('mphb_render_single_room_type_after_facilities', array('MPHBSingleRoomTypeView', '_renderFacilitiesListItemClose'),	10);

/*	Attributes - View	*/
add_action('mphb_render_single_room_type_before_view', array('MPHBSingleRoomTypeView', '_renderViewListItemOpen'),	10);
add_action('mphb_render_single_room_type_before_view', array('MPHBSingleRoomTypeView', '_renderViewTitle'),			20);

add_action('mphb_render_single_room_type_after_view', array('MPHBSingleRoomTypeView', '_renderViewListItemClose'),	10);

/*	Attributes - Size	*/
add_action('mphb_render_single_room_type_before_size', array('MPHBSingleRoomTypeView', '_renderSizeListItemOpen'),	10);
add_action('mphb_render_single_room_type_before_size', array('MPHBSingleRoomTypeView', '_renderSizeTitle'),			20);

add_action('mphb_render_single_room_type_after_size', array('MPHBSingleRoomTypeView', '_renderSizeListItemClose'),	10);

/*	Attributes - Bed Type	*/
add_action('mphb_render_single_room_type_before_bed_type', array('MPHBSingleRoomTypeView', '_renderBedTypeListItemOpen'), 10);
add_action('mphb_render_single_room_type_before_bed_type', array('MPHBSingleRoomTypeView', '_renderBedTypeTitle'), 20);

add_action('mphb_render_single_room_type_after_bed_type', array('MPHBSingleRoomTypeView', '_renderBedTypeListItemClose'), 10);

/*	Attributes - Adults		*/
add_action('mphb_render_single_room_type_before_adults', array('MPHBSingleRoomTypeView', '_renderAdultsListItemOpen'), 10);
add_action('mphb_render_single_room_type_before_adults', array('MPHBSingleRoomTypeView', '_renderAdultsTitle'), 20);

add_action('mphb_render_single_room_type_after_adults', array('MPHBSingleRoomTypeView', '_renderAdultsListItemClose'), 10);

/*	Attributes - Childs	*/
add_action('mphb_render_single_room_type_before_childs', array('MPHBSingleRoomTypeView', '_renderChildsListItemOpen'), 10);
add_action('mphb_render_single_room_type_before_childs', array('MPHBSingleRoomTypeView', '_renderChildsTitle'), 20);

add_action('mphb_render_single_room_type_after_childs', array('MPHBSingleRoomTypeView', '_renderChildsListItemClose'), 10);

/*	Calendar	*/
add_action('mphb_render_single_room_type_before_calendar', array('MPHBSingleRoomTypeView', '_renderCalendarTitle'), 10);

/*	Featured Image	*/
add_action('mphb_render_single_room_type_before_featured_image', array('MPHBSingleRoomTypeView', '_renderFeaturedImageParagraphOpen'), 10);

add_action('mphb_render_single_room_type_after_featured_image', array('MPHBSingleRoomTypeView', '_renderFeaturedImageParagraphClose'), 10);
add_action('mphb_render_single_room_type_after_featured_image', array('MPHBSingleRoomTypeView', 'renderGallery'), 20);

/*	Gallery		*/
add_action('mphb_render_single_room_type_after_gallery', array('MPHBSingleRoomTypeView', '_enqueueGalleryScripts'), 10);

/*	Price	*/
add_action('mphb_render_single_room_type_before_price', array('MPHBSingleRoomTypeView', '_renderPriceParagraphOpen'), 10);
add_action('mphb_render_single_room_type_before_price', array('MPHBSingleRoomTypeView', '_renderPriceTitle'), 20);

add_action('mphb_render_single_room_type_after_price', array('MPHBSingleRoomTypeView', '_renderPriceParagraphClose'), 10);

/*	Title	*/
add_action('mphb_render_single_room_type_before_title', array('MPHBSingleRoomTypeView', '_renderTitleHeadingOpen'), 10);

add_action('mphb_render_single_room_type_after_title', array('MPHBSingleRoomTypeView', '_renderTitleHeadingClose'), 10);

/*	Reservation Form	*/
add_action('mphb_render_single_room_type_before_reservation_form', array('MPHBSingleRoomTypeView', '_renderReservationFormTitle'), 10);