<?php

class MPHBCurrency {
	
	private $labels;
	private $symbols;

	public function __construct() {
		add_action('init', array($this, 'init'), 9);
	}

	public function init(){
		$labels = apply_filters( 'mphb_currency_labels' ,array(
			'AED' => __( 'United Arab Emirates Dirham', 'motopress-hotel-booking' ),
			'ARS' => __( 'Argentine Peso', 'motopress-hotel-booking' ),
			'AUD' => __( 'Australian Dollars', 'motopress-hotel-booking' ),
			'BDT' => __( 'Bangladeshi Taka', 'motopress-hotel-booking' ),
			'BGN' => __( 'Bulgarian Lev', 'motopress-hotel-booking' ),
			'BRL' => __( 'Brazilian Real', 'motopress-hotel-booking' ),
			'CAD' => __( 'Canadian Dollars', 'motopress-hotel-booking' ),
			'CHF' => __( 'Swiss Franc', 'motopress-hotel-booking' ),
			'CLP' => __( 'Chilean Peso', 'motopress-hotel-booking' ),
			'CNY' => __( 'Chinese Yuan', 'motopress-hotel-booking' ),
			'COP' => __( 'Colombian Peso', 'motopress-hotel-booking' ),
			'CZK' => __( 'Czech Koruna', 'motopress-hotel-booking' ),
			'DKK' => __( 'Danish Krone', 'motopress-hotel-booking' ),
			'DOP' => __( 'Dominican Peso', 'motopress-hotel-booking' ),
			'EGP' => __( 'Egyptian Pound', 'motopress-hotel-booking' ),
			'EUR' => __( 'Euros', 'motopress-hotel-booking' ),
			'GBP' => __( 'Pounds Sterling', 'motopress-hotel-booking' ),
			'HKD' => __( 'Hong Kong Dollar', 'motopress-hotel-booking' ),
			'HRK' => __( 'Croatia kuna', 'motopress-hotel-booking' ),
			'HUF' => __( 'Hungarian Forint', 'motopress-hotel-booking' ),
			'IDR' => __( 'Indonesia Rupiah', 'motopress-hotel-booking' ),
			'ILS' => __( 'Israeli Shekel', 'motopress-hotel-booking' ),
			'INR' => __( 'Indian Rupee', 'motopress-hotel-booking' ),
			'ISK' => __( 'Icelandic krona', 'motopress-hotel-booking' ),
			'JPY' => __( 'Japanese Yen', 'motopress-hotel-booking' ),
			'KES' => __( 'Kenyan shilling', 'motopress-hotel-booking' ),
			'KRW' => __( 'South Korean Won', 'motopress-hotel-booking' ),
			'LAK' => __( 'Lao Kip', 'motopress-hotel-booking' ),
			'MXN' => __( 'Mexican Peso', 'motopress-hotel-booking' ),
			'MYR' => __( 'Malaysian Ringgits', 'motopress-hotel-booking' ),
			'NGN' => __( 'Nigerian Naira', 'motopress-hotel-booking' ),
			'NOK' => __( 'Norwegian Krone', 'motopress-hotel-booking' ),
			'NPR' => __( 'Nepali Rupee', 'motopress-hotel-booking' ),
			'NZD' => __( 'New Zealand Dollar', 'motopress-hotel-booking' ),
			'PHP' => __( 'Philippine Pesos', 'motopress-hotel-booking' ),
			'PKR' => __( 'Pakistani Rupee', 'motopress-hotel-booking' ),
			'PLN' => __( 'Polish Zloty', 'motopress-hotel-booking' ),
			'PYG' => __( 'Paraguayan Guaraní', 'motopress-hotel-booking' ),
			'RON' => __( 'Romanian Leu', 'motopress-hotel-booking' ),
			'RUB' => __( 'Russian Ruble', 'motopress-hotel-booking' ),
			'SAR' => __( 'Saudi Riyal', 'motopress-hotel-booking' ),
			'SEK' => __( 'Swedish Krona', 'motopress-hotel-booking' ),
			'SGD' => __( 'Singapore Dollar', 'motopress-hotel-booking' ),
			'THB' => __( 'Thai Baht', 'motopress-hotel-booking' ),
			'TRY' => __( 'Turkish Lira', 'motopress-hotel-booking' ),
			'TWD' => __( 'Taiwan New Dollars', 'motopress-hotel-booking' ),
			'UAH' => __( 'Ukrainian Hryvnia', 'motopress-hotel-booking' ),
			'USD' => __( 'US Dollars', 'motopress-hotel-booking' ),
			'VND' => __( 'Vietnamese Dong', 'motopress-hotel-booking' ),
			'ZAR' => __( 'South African rand', 'motopress-hotel-booking' ),
		));
		$symbols = apply_filters( 'mphb_currency_symbols', array(
			'AED' => 'د.إ',
			'ARS' => '&#36;',
			'AUD' => '&#36;',
			'BDT' => '&#2547;&nbsp;',
			'BGN' => '&#1083;&#1074;.',
			'BRL' => '&#82;&#36;',
			'CAD' => '&#36;',
			'CHF' => '&#67;&#72;&#70;',
			'CLP' => '&#36;',
			'CNY' => '&yen;',
			'COP' => '&#36;',
			'CZK' => '&#75;&#269;',
			'DKK' => 'DKK',
			'DOP' => 'RD&#36;',
			'EGP' => 'EGP',
			'EUR' => '&euro;',
			'GBP' => '&pound;',
			'HKD' => '&#36;',
			'HRK' => 'Kn',
			'HUF' => '&#70;&#116;',
			'IDR' => 'Rp',
			'ILS' => '&#8362;',
			'INR' => '&#8377;',
			'ISK' => 'Kr.',
			'JPY' => '&yen;',
			'KES' => 'KSh',
			'KRW' => '&#8361;',
			'LAK' => '&#8365;',
			'MXN' => '&#36;',
			'MYR' => '&#82;&#77;',
			'NGN' => '&#8358;',
			'NOK' => '&#107;&#114;',
			'NPR' => '&#8360;',
			'NZD' => '&#36;',
			'PHP' => '&#8369;',
			'PKR' => '&#8360;',
			'PLN' => '&#122;&#322;',
			'PYG' => '&#8370;',
			'RMB' => '&yen;',
			'RON' => 'lei',
			'RUB' => '&#8381;',
			'SAR' => '&#x631;.&#x633;',
			'SEK' => '&#107;&#114;',
			'SGD' => '&#36;',
			'THB' => '&#3647;',
			'TRY' => '&#8378;',
			'TWD' => '&#78;&#84;&#36;',
			'UAH' => '&#8372;',
			'USD' => '&#36;',
			'VND' => '&#8363;',
			'ZAR' => '&#82;',
		));
		
		foreach ($labels as $key => &$label) {
			$label .= ' (' . $symbols[$key] . ')';
		}
		$this->labels = $labels;
		$this->symbols = $symbols;
	}

	public function getLabels(){
		return $this->labels;
	}

	public function getSymbols(){
		return $this->symbols;
	}

	public function getLabel($key) {
		return isset($this->labels[$key]) ? $this->labels[$key] : '';
	}

	/**
	 * Get symbol from settings.
	 *
	 * @param string $key
	 * @return string
	 */
	public function getSymbol($key) {
		return isset($this->symbols[$key]) ? $this->symbols[$key] : '';
	}

}