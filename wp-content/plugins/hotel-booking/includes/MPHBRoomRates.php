<?php

class MPHBRoomRates implements Iterator{

	protected $rates = array();
	protected $defaultRateId = 0;
	protected $lastIndex = 0;

	/**
	 *
	 * @param array $rates
	 * @param int|string $default
	 */
	public function __construct($rates = array(), $default) {
		foreach ($rates as $rateId => $rate) {
			$this->rates[$rateId] = new MPHBRoomRate(array_merge(array('id' => $rateId), $rate));
		}
		$this->defaultRateId = $default;
	}

	/**
	 *
	 * @return string
	 */
	public function getDefaultRateId(){
		return $this->defaultRateId;
	}

	/**
	 *
	 * @param int $rateId
	 * @return MPHBRoomRate|false
	 */
	public function getRate( $rateId ){
		return $this->hasRate( $rateId ) ? $this->rates[$rateId] : false;
	}

	/**
	 *
	 * @param int $rateId
	 * @return bool
	 */
	public function hasRate($rateId){
		return isset($this->rates[$rateId]);
	}

	/**
	 *
	 * @param int $rateId
	 */
	public function hasActiveRate($rateId){
		$activeRates = $this->getActiveRates();
		return isset($activeRates[$rateId]);
	}

	/**
	 *
	 * @return boolean
	 */
	public function hasActiveRates(){
		return count($this->getActiveRates()) > 0;
	}

	/**
	 *
	 * @return MPHBRate
	 */
	public function getDefaultRate(){
		return $this->getRate($this->getDefaultRateId());
	}

	/**
	 *
	 * @return bool
	 */
	public function isSingleRate(){
		// @todo needs checking is rates exists
		return count($this->getActiveRates()) === 1;
	}

	public function current(){
		return current($this->rates);
	}

	public function key(){
		return key($this->rates);
	}

	public function next(){
		return next($this->rates);
	}

	public function rewind(){
		return rewind($this->rates);
	}

	public function valid(){
		return isset($this->rates[$this->key()]);
	}

	/**
	 *
	 * @return MPHBRoomRate[]
	 */
	public function getActiveRates(){
		return array_filter( $this->rates, array($this, 'isActiveRate') );
	}

	/**
	 *
	 * @param MPHBRate $rate
	 * @return bool
	 */
	public function isActiveRate( $rate ){
		return !$rate->isDisabled();
	}

	/**
	 *
	 * @return array
	 */
	public function getIdTitleList(){
		$list = array();
		foreach( $this->rates as $rate ) {
			$list[$rate->getId()] = $rate->getTitle();
		}
		return $list;
	}

	/**
	 *
	 * @return float
	 */
	public function getLowestPrice(){
		$prices = array();
		foreach ($this->rates as $rate) {
			$prices[] = $rate->getRegularPrice();
		}
		return min($prices);
	}

}
