<?php

class MPHBRoomRate{

	protected $id = '0';
	protected $title = '';
	protected $description = '';
	protected $price = 0.0;
	protected $specialPrices = array();
	protected $sheduledPrices = array();
	protected $disabled = false;

	/**
	 *
	 * @param array $rate Array of rate parameters: id, title, description, price, special_prices, sheduled_prices, disabled
	 */
	public function __construct( $rate = array() ){
		$this->id = isset($rate['id']) ? $rate['id'] : $this->id;
		$this->title = isset($rate['title']) ? $rate['title'] : $this->title;
		$this->description = isset($rate['description']) ? $rate['description'] : $this->description;
		$this->price = isset($rate['price']) ? floatval($rate['price']) : $this->price;
		if ( isset($rate['special_prices']) ) {
			$this->initSpecialPrice($rate['special_prices']);
		}
		if ( isset($rate['sheduled_prices']) ){
			$this->initSheduledPrice($rate['sheduled_prices']);
		}
		$this->disabled = isset($rate['disabled']) ? $rate['disabled'] == '1' : $this->disabled;
	}

	public function initSpecialPrice( $specialPrices ){
		$prices = array();
		if (!empty($specialPrices)) {
			foreach ($specialPrices as $priceDetails) {
				foreach ( explode(',', $priceDetails['dates']) as $date ) {
					$prices[$date] = $priceDetails['price'];
				}
			}
			ksort($prices);
		}
		$this->specialPrices = $prices;
	}

	public function initSheduledPrice( $sheduledPrices ){
		$prices = array();
		if (!empty($sheduledPrices)) {
			foreach ($sheduledPrices as $priceDetails) {
				foreach ($priceDetails['days'] as $day) {
					$prices[$day] = $priceDetails['price'];
				}
			}
			ksort($prices);
		}
		$this->sheduledPrices = $prices;
	}

	/**
	 *
	 * @return string
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 *
	 * @return float
	 */
	public function getRegularPrice(){
		return $this->price;
	}

	/**
	 *
	 * @return string
	 */
	public function getRegularPriceHTML(){
		$price = $this->getRegularPrice();
		return mphb_format_price($price);
	}

	/**
	 * @return bool
	 */
	public function hasSpecialPrices(){
		$specialPrices = $this->getSpecialPrices();
		return !empty( $specialPrices );
	}

	/**
	 *
	 * @return bool
	 */
	public function hasSheduledPrices(){
		$sheduledPrices = $this->getSheduledPrices();
		return !empty( $sheduledPrices );
	}

	/**
	 * Retrieve special prices. Returns array of arrays(date, price).
	 *
	 *
	 * @return array
	 */
	function getSpecialPrices(){
		return $this->specialPrices;
	}

	/**
	 * Retrieve sheduled prices. Returns array of arrays(days, price).
	 *
	 * @return array
	 */
	function getSheduledPrices(){
		return $this->sheduledPrices;
	}

	/**
	 *
	 * @return bool
	 */
	function isDisabled(){
		return $this->disabled === true;
	}

	/**
	 *
	 * @return string
	 */
	function getTitle(){
		return $this->title;
	}

	/**
	 *
	 * @return string
	 */
	function getDescription(){
		return $this->description;
	}

	/**
	 *
	 * @param string $checkInDate date in format 'Y-m-d'
	 * @param string $checkOutDate date in format 'Y-m-d'
	 * @return float
	 */
	public function calcTotalPrice( $checkInDate, $checkOutDate ){
		$totalPrice = array_sum($this->getPriceBreakdown($checkInDate, $checkOutDate));
		return $totalPrice;
	}

	/**
	 *
	 * @param string $checkInDate datein format 'Y-m-d'
	 * @param string $checkOutDate date in formta 'Y-m-d'
	 *
	 * @return array Array where keys are dates and values are prices
	 */
	public function getPriceBreakdown( $checkInDate, $checkOutDate) {
		$regularPrice = $this->getRegularPrice();
		$sheduledPrices = $this->getSheduledPrices();
		$specialPrices = $this->getSpecialPrices();

		$prices = array();

		foreach ( mphbCreateDatePeriod($checkInDate, $checkOutDate) as $date ) {
			$dateDB = $date->format('Y-m-d');
			if (  array_key_exists($dateDB, $specialPrices)) {
				$prices[$dateDB] = floatval($specialPrices[$dateDB]);
			} else if ( array_key_exists($date->format('w'), $sheduledPrices)){
				$prices[$dateDB] = floatval($sheduledPrices[$date->format('w')]);
			} else {
				$prices[$dateDB] = $regularPrice;
			}
		}

		return $prices;
	}

}