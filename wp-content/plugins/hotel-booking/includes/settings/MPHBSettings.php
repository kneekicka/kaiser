<?php

class MPHBSettings {

	/**
	 *
	 * @var MPHBCurrency
	 */
	private $currency;
	/**
	 *
	 * @var MPHBSquareUnits
	 */
	private $squareUnits;

	private $currencyDefault			= 'USD';
	private $squareUnitDefault			= 'm2';
	private $confirmationModeDefault	= 'auto';

	private $dateFormat					= 'm/d/Y';
	private $dateTimeFormat				= 'm/d/Y H:i:s';

	public function __construct() {
		MPHB()->requireOnce('includes/settings/MPHBCurrency.php');
		MPHB()->requireOnce('includes/settings/MPHBSquareUnits.php');
		$this->squareUnits = new MPHBSquareUnits();
		$this->currency = new MPHBCurrency();
	}

	/**
	 *
	 * @return MPHBCurrency
	 */
	public function getCurrency(){
		return $this->currency;
	}

	/**
	 *
	 * @return MPHBSquareUnits
	 */
	public function getSquareUnits(){
		return $this->squareUnits;
	}

	public function getCurrencySymbol(){
		$currencyKey = get_option('mphb_currency_symbol', $this->currencyDefault);
		return $this->currency->getSymbol($currencyKey);
	}

	public function getSquareUnit(){
		$squareUnitKey = get_option('mphb_square_unit', $this->squareUnitDefault);
		return $this->squareUnits->getSymbol($squareUnitKey);
	}

	/**
	 * Retrieve checkout page id.
	 * The Checkout Page ID or false if checkout page not setted.
	 *
	 * @return string|bool
	 */
	public function getCheckoutPageID(){
		$pageId = get_option('mphb_checkout_page');
		return $pageId ? $pageId : false;
	}

	/**
	 * Retrieve checkout page url.
	 * Description:
	 * The permalink URL or false if post does not exist or checkout page not setted.
	 *
	 * @return string|bool
	 */
	public function getCheckoutPageUrl(){
		$pageId = $this->getCheckoutPageID();
		return $pageId ? get_permalink($pageId) : false;
	}

	/**
	 *
	 * @return string|bool False if search results page was not setted.
	 */
	public function getSearchResultsPageID(){
		$pageId = get_option('mphb_search_results_page');
		return $pageId ? $pageId : false;
	}

	/**
	 *
	 * @return string|bool False if search results page was not setted.
	 */
	public function getSearchResultsPageUrl(){
		$pageId = $this->getSearchResultsPageID();
		return $pageId ? get_permalink($pageId) : false;
	}

	/**
	 *
	 * @param string $id ID of page
	 * @return bool False if value was not updated and true if value was updated.
	 */
	public function setCheckoutPage($id){
		return update_option('mphb_checkout_page', $id);
	}

	/**
	 *
	 * @param string $id ID of page
	 * @return bool False if value was not updated and true if value was updated.
	 */
	public function setSearchResultsPage($id){
		return update_option('mphb_search_results_page', $id);
	}

	/**
	 *
	 * @return array
	 */
	public function getBedTypesList(){
		$bedsList = array();
		$beds = get_option( 'mphb_bed_types', array() );
		foreach($beds as $bed) {
			if (!empty($bed['type'])) {
				$bedsList[$bed['type']] = $bed['type'];
			}
		}
		return $bedsList;
	}

	/**
	 * Retrieve confirmation mode. Possible values 'manual', 'auto'.
	 *
	 * @return string
	 */
	public function getConfirmationMode(){
		$mode = get_option( 'mphb_confirmation_mode', $this->confirmationModeDefault );
		return $mode;
	}

	/**
	 *
	 * @return bool
	 */
	public function isAutoConfirmationMode(){
		return $this->getConfirmationMode() === 'auto';
	}

	/**
	 *
	 * @return string
	 */
	public function getAdminEmail(){
		$wpAdminEmail = get_bloginfo('admin_email');
		$mphbAdminEmail = get_option( 'mphb_admin_email', '' );
		return empty($mphbAdminEmail) ? $wpAdminEmail : $mphbAdminEmail;
	}

	/**
	 *
	 * @return string
	 */
	public function getAdminName(){
		$wpAdminName = get_bloginfo('name');
		$mphbAdminName = get_option( 'mphb_admin_name', '' );
		return empty($mphbAdminName) ? $wpAdminName : $mphbAdminName;
	}

	/**
	 *
	 * @return array
	 */
	public function getDaysList(){
		return array(
			__('Sunday', 'motopress-hotel-booking'),
			__('Monday', 'motopress-hotel-booking'),
			__('Tuesday', 'motopress-hotel-booking'),
			__('Wednesday', 'motopress-hotel-booking'),
			__('Thursday', 'motopress-hotel-booking'),
			__('Friday', 'motopress-hotel-booking'),
			__('Saturday', 'motopress-hotel-booking')
		);
	}

	/**
	 *
	 * @param string $key
	 * @return string
	 */
	public function getDayByKey($key){
		$daysArr = $this->getDaysList();
		return isset($daysArr[$key]) ? $daysArr[$key] : false;
	}

	/**
	 * Retrieve plugin's frontend date format. Uses for datepickers.
	 *
	 * @return string
	 */
	public function getDateFormat(){
		return $this->dateFormat;
	}

	/**
	 * Retrieve WP date format
	 *
	 * @return string
	 */
	public function getDateFormatWP(){
		return get_option( 'date_format' );
	}

	/**
	 *
	 * @return string
	 */
	public function getDateTimeFormat(){
		return $this->dateTimeFormat;
	}

	/**
	 *
	 * @return int
	 */
	public function getMinAdults(){
		return 1;
	}

	/**
	 *
	 * @return int
	 */
	public function getMinChilds(){
		return 0;
	}

	/**
	 *
	 * @return int
	 */
	public function getMaxAdults() {
		return (int) apply_filters('mphb_settings_max_adults', 10);
	}

	/**
	 *
	 * @return int
	 */
	public function getMaxChilds(){
		return (int) apply_filters('mphb_settings_max_childs', 10);
	}

	/**
	 *
	 * @return array
	 */
	public function getAdultsList(){
		$values = array_map('strval', range($this->getMinAdults(), $this->getMaxAdults()));
		return array_combine( $values, $values );
	}

	/**
	 *
	 * @return array
	 */
	public function getChildsList(){
		$values = array_map('strval', range(0, $this->getMaxChilds()));
		return array_combine( $values, $values );
	}

	/**
	 *
	 * @return string|array time in format "H:i:s" or array
	 */
	public function getCheckInTime($asArray = false){
		$separator = ':';
		$seconds = '00';
		$timeHM = get_option('mphb_check_in_time', '11:00');
		$time = explode($separator, $timeHM);
		$time[] = $seconds;
		return $asArray ? $time : implode( $separator, $time );
	}

	/**
	 * Retrieve check-in time in WordPress time format
	 */
	public function getCheckInTimeWPFormatted(){
		$time = $this->getCheckInTime();
		$timeObj = DateTime::createFromFormat('H:i:s', $time);

		return MPHBUtils::convertTimeToWPFront( $timeObj );
	}

	/**
	 *
	 * @return string time in format "H:i:s"
	 */
	public function getCheckOutTime($asArray = false){
		$separator = ':';
		$seconds = '00';
		$timeHM = get_option('mphb_check_out_time', '10:00');
		$time = explode($separator, $timeHM);
		$time[] = $seconds;
		return $asArray ? $time : implode( $separator, $time);
	}

	/**
	 * Retrieve check-out time in WordPress time format
	 */
	public function getCheckOutTimeWPFormatted(){
		$time = $this->getCheckOutTime();
		$timeObj = DateTime::createFromFormat('H:i:s', $time);

		return MPHBUtils::convertTimeToWPFront( $timeObj );
	}

	/**
	 * Check whether to use single room type template from plugin
	 *
	 * @return bool
	 */
	public function isPluginTemplateMode(){
		return $this->getTemplateMode() === 'plugin';
	}

	/**
	 * Retrieve template mode. Possible values: plugin, theme.
	 *
	 * @return string
	 */
	public function getTemplateMode(){
		return current_theme_supports('motopress-hotel-booking') ? 'plugin' : get_option('mphb_template_mode', 'theme');
	}

	/**
	 * Retrieve first day of the week.
	 *
	 * @return int
	 */
	public function getFirstDay(){
		$wpFirstDay = (int) get_option( 'start_of_week', 0 );
		return $wpFirstDay;
	}

	/**
	 *  Return currency position. Posible values 'left', 'right', 'left_space', 'right_space'.
	 *
	 * @return string
	 */
	public function getCurrencyPosition(){
		$currencyPosition = get_option('mphb_currency_position', 'left');
		return $currencyPosition;
	}

	/**
	 *
	 * @return string
	 */
	public function getPriceFormat(){
		$currencyPosition = $this->getCurrencyPosition();
		$currencySpan = '<span class="mphb-currency">' . $this->getCurrencySymbol() . '</span>';

		switch ( $currencyPosition ) {
			case 'right' :
				$format = '%s' . $currencySpan;
			break;
			case 'left_space' :
				$format = $currencySpan . '&nbsp;%s';
			break;
			case 'right_space' :
				$format = '%s&nbsp;' . $currencySpan;
			break;
			case 'left' : // left is default position
			default:
				$format = $currencySpan . '%s';
				break;
		}

		return $format;
	}

	/**
	 *
	 * @return string
	 */
	public function getPriceDecimalsSeparator(){
		$separator = get_option('mphb_decimals_separator', '.');
		return $separator;
	}

	/**
	 *
	 * @return string
	 */
	public function getPriceThousandSeparator(){
		$separator = get_option('mphb_thousand_separator', ',');
		return $separator;
	}

	/**
	 *
	 * @return int
	 */
	public function getPriceDecimalsCount(){
		$count = get_option('mphb_decimal_count', 2);
		return intval($count);
	}

}
