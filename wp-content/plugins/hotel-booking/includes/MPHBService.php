<?php

class MPHBService {

	/**
	 *
	 * @var int
	 */
	private $id;

	/**
	 *
	 * @var int
	 */
	private $adults = 1;

	/**
	 *
	 * @param int|WP_POST $id
	 */
	public function __construct($post) {
		if (is_a($post, 'WP_Post')) {
			$this->post = $post;
			$this->id = $post->ID;
		} else {
			$this->id   = absint( $post );
			$this->post = get_post( $this->id );
		}
	}

	/**
	 *
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 *
	 * @return string
	 */
	public function getTitle(){
		return get_the_title($this->id);
	}

	/**
	 *
	 * @return float
	 */
	public function getPrice(){
		$price = get_post_meta($this->id, 'mphb_price', true);
		return $price !== '' ? floatval($price) : 0.0;
	}

	/**
	 *
	 * @return boolean
	 */
	public function isPayPerNight(){
		$payPerNight = get_post_meta($this->id, 'mphb_price_periodicity', true);
		if (empty($payPerNight)) {
			$payPerNight = 'once';
		}
		return $payPerNight === 'per_night';
	}

	/**
	 *
	 * @return boolean
	 */
	public function isPayPerAdult(){
		$payPerAdult = get_post_meta($this->id, 'mphb_price_quantity', true);
		if (empty($payPerAdult)) {
			$payPerAdult = 'once';
		}
		return $payPerAdult	=== 'per_adult';
	}

	/**
	 *
	 * @param int $adults
	 */
	function setAdults( $adults ){
		$this->adults = $adults;
	}

	/**
	 *
	 * @return int
	 */
	function getAdults(){
		return $this->adults;
	}

	/**
	 *
	 * @param DateTime $checkInDate
	 * @param DateTime $checkOutDate
	 * @param int $adults
	 * @return float
	 */
	public function calcPrice($checkInDate, $checkOutDate){
		$multiplier = 1;
		if ( $this->isPayPerNight() ) {
			$nights = MPHBUtils::calcNights($checkInDate, $checkOutDate);
			$multiplier = $multiplier * $nights;
		}

		if ( $this->isPayPerAdult() ) {
			$multiplier = $multiplier * $this->adults;
		}
		// todo float calculations
		return $multiplier * $this->getPrice();
	}

	public function generatePriceDetailsString( $checkInDate, $checkOutDate ){

		$priceDetails = mphb_format_price($this->getPrice());

		if ($this->isPayPerNight()) {
			$nights = MPHBUtils::calcNights( $checkInDate, $checkOutDate );
			$priceDetails .= sprintf( _n(' &#215; %d night', ' &#215; %d nights', $nights, 'motopress-hotel-booking'), $nights );
		}

		if ($this->isPayPerAdult()) {
			$priceDetails .= sprintf( _n(' &#215; %d adult', ' &#215; %d adults', $this->adults, 'motopress-hotel-booking'), $this->adults);
		}

		return $priceDetails;
	}

	/**
	 *
	 * @return boolean
	 */
	public function isFree(){
		return $this->getPrice() == 0;
	}

	/**
	 *
	 * @param boolean $quantity Whether to show conditions of quantity. Default TRUE.*
	 * @param boolean $periodicity Whether to show conditions of periodicity. Default TRUE.
	 * @param boolean $literalFree Whether to replace 0 price to free label. Default TRUE.
	 *
	 * @return string
	 */
	public function getPriceWithConditions($quantity = true, $periodicity = true, $literalFree = true){

		$price = $this->getPriceHTML($literalFree);

		if ( ! $this->isFree() ) {
			if ( $periodicity) {
				if ( $this->isPayPerNight() ) {
					$price .= __(' Per Night', 'motopress-hotel-booking');
				} else {
					$price .= __(' Once', 'motopress-hotel-booking');
				}
			}
			if ( $periodicity && $quantity ) {
				$price .= ' / ';
			}
			if ( $quantity ) {
				if ( $this->isPayPerAdult() ) {
					$price .= __(' Per Adult', 'motopress-hotel-booking');
				} else {
					$price .= __(' Per Room', 'motopress-hotel-booking');
				}
			}
		}

		return $price;
	}

	/**
	 *
	 * @param boolean $literalFree
	 * @return string
	 */
	public function getPriceHTML($literalFree = true){
		return mphb_format_price($this->getPrice(), array(
			'literal_free' => $literalFree
		));
	}

}
