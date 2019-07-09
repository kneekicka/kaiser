<?php

/**
 * Retrieve the room type regular price
 *
 * @return string
 */
function mphb_tmpl_get_room_type_regular_price(){
	return mphb_format_price(MPHB()->getCurrentRoomType()->getPrice());
}

/**
 * Display the room type regular price
 */
function mphb_tmpl_the_room_type_regular_price(){
	echo mphb_tmpl_get_room_type_regular_price();
}

/**
 * Retrieve dayname for key
 *
 * @param string|int $key number from 0 to 6
 * @return string
 */
function mphb_tmpl_get_day_by_key($key){
	return MPHB()->getSettings()->getDayByKey($key);
}

/**
 * Retrieve the room type adults capacity
 *
 * @return int
 */
function mphb_tmpl_get_room_type_adults_capacity(){
	return MPHB()->getCurrentRoomType()->getAdultsCapacity();
}

/**
 * Retrieve the room type childs capacity
 *
 * @return int
 */
function mphb_tmpl_get_room_type_childs_capacity(){
	return MPHB()->getCurrentRoomType()->getChildsCapacity();
}

/**
 * Retrieve the room type bed type
 *
 * @return string
 */
function mphb_tmpl_get_room_type_bed_type(){
	return MPHB()->getCurrentRoomType()->getBedType();
}

/**
 * Retrieve the room type comma-separated facilities
 *
 * @return string
 */
function mphb_tmpl_get_room_type_facilities(){
	return MPHB()->getCurrentRoomType()->getFacilities();
}

/**
 * Retrieve the room type size
 *
 * @return string
 */
function mphb_tmpl_get_room_type_size(){
	return MPHB()->getCurrentRoomType()->getSize(true);
}

/**
 * Retrieve the room type categories
 *
 * @return string
 */
function mphb_tmpl_get_room_type_categories(){
	return MPHB()->getCurrentRoomType()->getCategories();
}

/**
 * Retrieve the room type view
 *
 * @return string
 */
function mphb_tmpl_get_room_type_view(){
	return MPHB()->getCurrentRoomType()->getView();
}

/**
 * Check is current room type has gallery.
 *
 * @return boolean
 */
function mphb_tmpl_has_room_type_gallery(){
	return MPHB()->getCurrentRoomType()->hasGallery();
}

/**
 *
 * @param bool $withFeaturedImage
 * @return array
 */
function mphb_tmpl_get_room_type_gallery_ids( $withFeaturedImage = false){
	$roomType = MPHB()->getCurrentRoomType();
	$galleryIds = $roomType->getGalleryIds();

	if ( $withFeaturedImage && $roomType->hasFeaturedImage() ) {
		array_unshift($galleryIds, $roomType->getFeaturedImageId());
	}

	return $galleryIds;
}

/**
 *
 * @param array $atts @see gallery_shortcode . Additional parameters: mphb_wrap_ul - use for wrap gallery in ul, mphb_wrapper_class.
 */
function mphb_tmpl_the_room_type_galery($atts = array()){
	$defaultAtts = apply_filters('mphb_gallery_atts', array(
		'ids' => join(',', mphb_tmpl_get_room_type_gallery_ids()),
		'link' => 'file',
		'columns' => '4',
		'size' => 'medium'
	));
	$atts = array_merge($defaultAtts, $atts);

	$wrapperClass = 'mphb-room-type-gallery-wrapper';
	if ( isset($atts['mphb_wrapper_class']) ){
		$wrapperClass .= ' ' . $atts['mphb_wrapper_class'];
	}
	$result = '<div class="' . esc_attr( $wrapperClass ) . '">' . gallery_shortcode($atts) . '</div>';

	// Allow gallery in ul. Fix for flexslider
	if ( isset($atts['mphb_wrap_ul']) && $atts['mphb_wrap_ul'] ) {
		$result = preg_replace('/((?:<li.*>.*<\/li>)+)/s', '<ul class="slides">$1</ul>', $result);
	}

	echo $result;
}

function mphb_tmpl_the_room_type_flexslider_gallery(){
	$uniqid = uniqid();
	$sliderId = 'mphb-gallery-slider-' . $uniqid;
	$thumbSliderId = 'mphb-gallery-thumbnail-slider-' . $uniqid;
	?>
	<div id="<?php echo $sliderId; ?>">
		<?php mphb_tmpl_the_room_type_galery(array(
			'size' => 'full',
			'itemtag' => 'li',
			'icontag' => 'span',
			'mphb_wrap_ul' => true
		)); ?>
	</div>
	<div id="<?php echo $thumbSliderId; ?>">
		<?php mphb_tmpl_the_room_type_galery(array(
			'itemtag' => 'li',
			'icontag' => 'span',
			'mphb_wrap_ul' => true
		)); ?>
	</div>
	<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(event) {
		(function($){
			$(function(){
				// The slider being synced must be initialized first
				var navSlider = $('#<?php echo $thumbSliderId; ?>>div');
				var slider = $('#<?php echo $sliderId; ?>>div');
				var navSliderItemWidth = navSlider.find('ul > li img').width();
				navSlider.addClass('flexslider mphb-flexslider mphb-gallery-thumbnails-slider').flexslider({
					animation: "slide",
					controlNav: false,
					animationLoop: true,
					slideshow: false,
					itemWidth: navSliderItemWidth,
					itemMargin: 5,
					asNavFor: '#<?php echo $sliderId; ?>>div',
				});

				slider.addClass('flexslider mphb-flexslider mphb-gallery-slider').flexslider({
				  animation: "slide",
				  controlNav: false,
				  animationLoop: true,
				  smoothHeight: true,
				  slideshow: false,
				  sync: "#<?php echo $thumbSliderId; ?>>div",
				});
			});
		})(jQuery);
	});
	</script>
	<?php
}


function mphb_tmpl_the_room_type_featured_image(){
	$imageExcerpt = get_post_field( 'post_excerpt', get_post_thumbnail_id() );
	$imageLink = wp_get_attachment_url( get_post_thumbnail_id() );
	$image = mphb_tmpl_get_room_type_image();

	printf( '<a href="%s" class="mphb-lightbox" title="%s" data-rel="magnific-popup[mphb-room-type-gallery]">%s</a>', esc_url($imageLink), esc_attr($imageExcerpt), $image );
}

/**
 * Retrieve single room type featured image
 *
 * @param int $id Optional. ID of post.
 * @param string $size Optional. Size of image.
 * @return string HTML img element or empty string on failure.
 */
function mphb_tmpl_get_room_type_image( $postID = null, $size = null){
	if ( is_null($postID) ) {
		$postID = get_the_ID();
	}
	if ( is_null( $size ) ) {
		$size = apply_filters( 'mphb_single_room_type_image_size', 'large' );
	}
	$imageTitle = get_the_title( get_post_thumbnail_id($postID) );
	return get_the_post_thumbnail( $postID, $size, array(
		'title'	=> $imageTitle,
	) );
}

/**
 * Retrieve in-loop room type thumbnail
 *
 * @param string $size
 */
function mphb_tmpl_the_loop_room_type_thumbnail( $size = null ){
	if ( is_null($size) ) {
		$size = apply_filters('mphb_loop_room_type_thumbnail_size', 'post-thumbnail');
	}
	the_post_thumbnail( $size );
}

/**
 *
 * @param string $buttonText
 */
function mphb_tmpl_the_loop_room_type_book_button( $buttonText = null ){
	if (  is_null($buttonText) ) {
		$buttonText = __('Book', 'motopress-hotel-booking');
	}
	echo '<a class="button mphb-book-button" href="' . get_the_permalink() . '#booking-form-' . get_the_ID . '">' . $buttonText . '</a>';
}

/**
 *
 * @param string $buttonText
 */
function mphb_tmpl_the_loop_room_type_book_button_form( $buttonText = null ){
	if (  is_null($buttonText) ) {
		$buttonText = __('Book', 'motopress-hotel-booking');
	}
	echo '<form action="' . get_the_permalink() . '#booking-form-' . get_the_ID() . '" method="get" >';
	echo '<button type="submit" class="button mphb-book-button" >' . $buttonText . '</button>';
	echo '</form>';
}

/**
 *
 * @param string $buttonText
 */
function mphb_tmpl_the_loop_room_type_view_details_button( $buttonText = null ){
	if (  is_null($buttonText) ) {
		$buttonText = __('View Details', 'motopress-hotel-booking');
	}
	echo '<a class="button mphb-view-details-button" href="' . get_the_permalink() . '" >' . $buttonText . '</a>';
}

/**
 * Display room type calendar
 *
 * @param MPHBRoomType $roomType Optional. Use current room type by default.
 */
function mphb_tmpl_the_room_type_calendar($roomType = null){
	if ( is_null( $roomType ) ) {
		$roomType = MPHB()->getCurrentRoomType();
	}
	?>
		<div class="mphb-calendar mphb-datepick inlinePicker" id="mphb-calendar-<?php echo $roomType->getId(); ?>"></div>
	<?php
}

/**
 * Display room type reservation form
 *
 * @param MPHBRoomType $roomType Optional. Use current room type by default.
 */
function mphb_tmpl_the_room_reservation_form($roomType = null){
	if ( is_null( $roomType ) ) {
		$roomType = MPHB()->getCurrentRoomType();
	}

	$searchParameters = $roomType->sanitizeSearchParameters( MPHB()->getStoredSearchParameters() );
	$uniqueSuffix = uniqid();
	?>
	<form method="POST" action="<?php echo MPHB()->getSettings()->getCheckoutPageUrl(); ?>" class="mphb-booking-form" id="booking-form-<?php echo $roomType->getId(); ?>">
		<p class="mphb-required-fields-tip"><small><?php _e('Required fields are followed by <strong><abbr title="required">*</abbr></strong>', 'motopress-hotel-booking'); ?></small></p>
		<?php wp_nonce_field(MPHBShortcodeCheckout::NONCE_ACTION_CHECKOUT, MPHBShortcodeCheckout::NONCE_NAME); ?>
		<input type="hidden" name="mphb_room_type_id" value="<?php echo $roomType->getId(); ?>" />
		<p class="mphb-check-in-date-wrapper">
			<label for="mphb_check_in_date-<?php echo $uniqueSuffix; ?>"><?php _e('Check-In Datum', 'motopress-hotel-booking'); ?></label>&nbsp;<strong><abbr title="<?php printf(_x('formated as %s', 'Date format tip','motopress-hotel-booking'), 'mm/dd/yyyy'); ?>">*</abbr></strong><br />
			<input id="mphb_check_in_date-<?php echo $uniqueSuffix; ?>" type="text" class="mphb-datepick" name="mphb_check_in_date" value="<?php echo esc_attr($searchParameters['mphb_check_in_date']); ?>" required="required" autocomplete="off"/>

		</p>
		<p class="mphb-check-out-date-wrapper">
			<label for="mphb_check_out_date-<?php echo $uniqueSuffix; ?>"><?php _e('Check-Out Datum', 'motopress-hotel-booking'); ?></label>&nbsp;<strong><abbr title="<?php printf(_x('formated as %s', 'Date format tip','motopress-hotel-booking'), 'mm/dd/yyyy'); ?>">*</abbr></strong><br />
			<input id="mphb_check_out_date-<?php echo $uniqueSuffix; ?>" type="text" class="mphb-datepick" name="mphb_check_out_date" value="<?php echo esc_attr($searchParameters['mphb_check_out_date']); ?>" required="required" autocomplete="off"/>
		</p>
		<p class="mphb-adults-wrapper">
			<label for="mphb_adults-<?php echo $uniqueSuffix; ?>"><?php _e('Erwachsene', 'motopress-hotel-booking'); ?></label>&nbsp;<strong><abbr title="<?php _e('Required', 'motopress-hotel-booking'); ?>">*</abbr></strong><br />
			<select id="mphb_adults-<?php echo $uniqueSuffix; ?>" name="mphb_adults" required="required">
			<?php foreach( range( MPHB()->getSettings()->getMinAdults(), $roomType->getAdultsCapacity() ) as $value ) { ?>
				<option value="<?php echo $value; ?>" <?php selected( (string) esc_attr($searchParameters['mphb_adults']), (string) $value ); ?>><?php echo $value; ?></option>
			<?php } ?>
			</select>
		</p>
		<p class="mphb-check-childs-date-wrapper">
			<label for="mphb_childs-<?php echo $uniqueSuffix; ?>"><?php _e('Kinder', 'motopress-hotel-booking'); ?></label>&nbsp;<strong><abbr title="<?php _e('Required', 'motopress-hotel-booking'); ?>">*</abbr></strong><br />
			<select id="mphb_childs-<?php echo $uniqueSuffix; ?>" name="mphb_childs" required="required">
			<?php foreach( range( 0, $roomType->getChildsCapacity() ) as $value ) { ?>
				<option value="<?php echo $value; ?>" <?php selected(esc_attr( (string) $searchParameters['mphb_childs']), (string) $value ); ?>><?php echo $value; ?></option>
			<?php } ?>
			</select>

		</p>
		<div class="mphb-errors-wrapper mphb-hide"></div>
		<p class="mphb-reserve-btn-wrapper">
			<input class="mphb-reserve-btn button" disabled="disabled" type="submit" value="<?php _e('reservieren', 'motopress-hotel-booking');?>" />
			<span class="mphb-preloader mphb-hide"></span>
		</p>
	</form>
	<?php
}

/**
 * Retrieve in-loop service thumbnail
 *
 * @param string $size
 */
function mphb_tmpl_the_loop_service_thumbnail( $size = null ){
	if ( is_null($size) ) {
		$size = apply_filters('mphb_loop_service_thumbnail_size', 'post-thumbnail');
	}
	the_post_thumbnail( $size );
}

function mphb_tmpl_the_service_price(){
	$service = new MPHBService(get_the_ID());
	echo $service->getPriceWithConditions();
}

/**
 * Retrieve the classes for the post div as an array.
 *
 * @param string|array $class   One or more classes to add to the class list.
 * @param int|WP_Post  $post_id Optional. Post ID or post object.
 * @return array Array of classes.
 */
function mphb_tmpl_get_filtered_post_class($class = '', $postId = null){
	$classes = get_post_class($class, $postId);
	if ( false !== ( $key = array_search( 'hentry', $classes ) ) ) {
		unset( $classes[ $key ] );
	}
	return $classes;
}
