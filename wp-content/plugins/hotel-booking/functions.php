<?php

/**
 * Get template part.
 *
 * @param mixed $slug
 * @param string $name Optional. Default ''.
 */
function mphb_get_template_part( $slug, $args = array() ){

	$template = '';

	// Look in %theme_dir%/%template_path%/slug.php
	$template = locate_template( MPHB()->getTemplatePath() . "{$slug}.php" );

	// Get default template from plugin
	if ( empty( $template ) && file_exists( MPHB()->getPluginPath("templates/{$slug}.php") ) ) {
		$template = MPHB()->getPluginPath("templates/{$slug}.php");
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'mphb_get_template_part', $template, $slug, $args );

	if ( !empty( $template ) ) {
		mphb_load_template( $template, $args );
	}
}

function mphb_load_template($template, $templateArgs = array() ){
	if ( $templateArgs && is_array( $templateArgs ) ) {
		extract( $templateArgs );
	}
	require $template;
}


/**
 * @note requires PHP >= 5.3.0
 * @param DateTime|string $dateFrom date in format 'Y-m-d' or DateTime object
 * @param DateTime|string $dateTo date in format 'Y-m-d' or DateTime object
 *
 * @return array Array of dates representing in front end date format
 */
function mphbCreateDateRangeArray($dateFrom, $dateTo, $includeEndDate = false) {
	$dates = array();
	$dateRange = mphbCreateDatePeriod($dateFrom, $dateTo);

	foreach($dateRange as $date){
		$dates[$date->format('Y-m-d')] = $date->format(MPHB()->getSettings()->getDateFormat());
	}

	return $dates;
}

/**
 * @warning PHP <5.3.3 has bug with iterating over DatePeriod twice https://bugs.php.net/bug.php?id=52668
 *
 * @param DateTime|string $dateFrom date in format 'Y-m-d' or DateTime object
 * @param DateTime|string $dateTo date in format 'Y-m-d' or DateTime object
 * @return DatePeriod
 */
function mphbCreateDatePeriod( $dateFrom, $dateTo, $includeEndDate = false ){
	$dateFrom	 = ( $dateFrom instanceof DateTime ) ? clone $dateFrom : DateTime::createFromFormat( 'Y-m-d', $dateFrom );
	$dateTo		 = ( $dateTo instanceof DateTime ) ? clone $dateTo : DateTime::createFromFormat( 'Y-m-d', $dateTo );

	$dateFrom->setTime(0, 0, 0);
	$dateTo->setTime(0, 0, 0);

	if ( $includeEndDate ) {
		$dateTo = $dateTo->modify( '+1 day' );
	}

	$interval = new DateInterval( 'P1D' );
	return new DatePeriod( $dateFrom, $interval, $dateTo );
}

/**
 *
 * @param int|string $relation Optional.
 * @param DateTime|false $baseDate Optional.
 * @return DatePeriod
 */
function mphbCreateQuarterPeriod( $relation = 0, $baseDate = false ){
	$relation		 = intval( $relation );
	$relationSign	 = $relation < 0 ? '-' : '+';
	$baseMonth		 = date( 'n', $baseDate ? $baseDate->format( 'U' ) : current_time( 'timestamp' )  );
	$baseYear		 = date( 'Y', $baseDate ? $baseDate->format( 'U' ) : current_time( 'timestamp' )  );

	if ( $baseMonth <= 3 ) {
		$baseQuarterFirstDate = new DateTime( 'first day of January ' . $baseYear );
	} elseif ( $baseMonth <= 6 ) {
		$baseQuarterFirstDate = new DateTime( 'first day of April ' . $baseYear );
	} elseif ( $baseMonth <= 9 ) {
		$baseQuarterFirstDate = new DateTime( 'first day of July ' . $baseYear );
	} else {
		$baseQuarterFirstDate = new DateTime( 'first day of October' . $baseYear );
	}

	$firstDate = clone $baseQuarterFirstDate;
	if ( $relation !== 0 ) {
		$firstDate->modify( $relationSign . ( absint( $relation ) * 3 ) . ' month' );
	}

	$lastDate = clone $firstDate;
	$lastDate->modify( '+2 month' )->modify( 'last day of this month' );

	return mphbCreateDatePeriod( $firstDate, $lastDate );
}

/**
 *
 * @global string $wp_version
 * @param string $type
 * @param int $gmt
 * @return string
 */
function mphb_current_time($type, $gmt = 0){
	global $wp_version;
	if (  version_compare( $wp_version, '3.9', '<=') && !in_array( $type, array('timestmap', 'mysql') ) ) {
		$timestamp = current_time('timestamp', $gmt);
		return date($type, $timestamp);
	} else {
		return current_time($type, $gmt);
	}
}

/**
 * Retrieve a post status label by name
 *
 * @param string $status
 * @return string
 */
function mphb_get_status_label($status){
	switch( $status ){
		case 'new':
			$label = _x('New', 'Post Status', 'motopress-hotel-booking');
			break;
		case 'auto-draft':
			$label = _x('Auto Draft', 'Post Status', 'motopress-hotel-booking');
			break;
		default:
			$statusObj = get_post_status_object($status);
			$label = !is_null($statusObj) && property_exists($statusObj, 'label') ? $statusObj->label : '';
			break;
	}

	return $label;
}

/**
 *
 * @param string $name
 * @param string $value
 * @param int $expire
 */
function mphb_set_cookie($name, $value, $expire = 0){
	setcookie($name, $value, $expire, COOKIEPATH, COOKIE_DOMAIN);
	if ( COOKIEPATH != SITECOOKIEPATH ) {
		setcookie($name, $value, $expire, SITECOOKIEPATH, COOKIE_DOMAIN);
	}
}

/**
 *
 * @param string $name
 * @return mixed|null Cookie value or null if not exists.
 */
function mphb_get_cookie( $name ){
	return ( mphb_has_cookie( $name ) ) ? $_COOKIE[ $name ] : null;
}

/**
 *
 * @param string $name
 * @return bool
 */
function mphb_has_cookie( $name ){
	return isset($_COOKIE[ $name ]);
}

function mphb_is_checkout_page(){
	$checkoutPageId = MPHB()->getSettings()->getCheckoutPageID();
	return $checkoutPageId && is_page( $checkoutPageId );
}

function mphb_is_search_results_page(){
	$searchResultsPageId = MPHB()->getSettings()->getSearchResultsPageID();
	return $searchResultsPageId && is_page( $searchResultsPageId );
}

function mphb_is_single_room_type_page(){
	return is_singular( MPHB()->getRoomTypeCPT()->getPostType() );
}

function mphb_get_thumbnail_width(){
	$width = 150;

	$imageSizes = get_intermediate_image_sizes();
	if ( in_array( 'thumbnail', $imageSizes ) ) {
		$width = (int) get_option( "thumbnail_size_w", $width );
	}

	return $width;
}

/**
 *
 * @param float $price
 * @param array $args
 * @return string
 */
function mphb_format_price( $price, $args = array() ){

	$args = wp_parse_args( $args, array(
		'decimal_separator' => MPHB()->getSettings()->getPriceDecimalsSeparator(),
		'thousand_separator' => MPHB()->getSettings()->getPriceThousandSeparator(),
		'decimals' => MPHB()->getSettings()->getPriceDecimalsCount(),
		'price_format' => MPHB()->getSettings()->getPriceFormat(),
		'literal_free' => false,
		'trim_zeros' => true
	) );

	if ( $args['literal_free'] && $price == 0 ) {
		$formattedPrice = apply_filters('mphb_free_literal', __('Free', 'motopress-hotel-booking'));
	} else {
		$negative = $price < 0;
		$price = number_format( $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] );
		if ( $args['trim_zeros'] ) {
			$price = mphb_trim_zeros($price);
		}
		$formattedPrice = ( $negative ? '-' : '' ) . sprintf( $args['price_format'], $price );
	}

	return '<span class="mphb-price">' . $formattedPrice . '</span>';
}

/**
 * Trim trailing zeros off prices.
 *
 * @param mixed $price
 * @return string
 */
function mphb_trim_zeros( $price ) {
	return preg_replace( '/' . preg_quote( MPHB()->getSettings()->getPriceDecimalsSeparator() , '/' ) . '0++$/', '', $price );
}