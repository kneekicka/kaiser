<?php

class MPHBCustomer {

	private $email;
	private $firstName;
	private $lastName;
	private $phone;

	/**
	 *
	 * @param array $atts
	 * @param string $atts['email']
	 * @param string $atts['first_name']
	 * @param string $atts['last_name'] Optional.
	 * @param string $atts['phone']
	 */
	public function __construct( $atts = array() ){

		$atts = array_merge( array(
			'email'		 => '',
			'last_name'	 => '',
			'first_name' => '',
			'phone'		 => ''
			), $atts );

		$this->setEmail( $atts['email'] );
		$this->setFirstName( $atts['first_name'] );
		$this->setLastName( $atts['last_name'] );
		$this->setPhone( $atts['phone'] );
	}

	function getEmail(){
		return $this->email;
	}

	function getFirstName(){
		return $this->firstName;
	}

	function getLastName(){
		return $this->lastName;
	}

	function getPhone(){
		return $this->phone;
	}

	/**
	 *
	 * @param string $email
	 */
	function setEmail( $email ){
		$this->email = $email;
	}

	/**
	 *
	 * @param string $firstName
	 */
	function setFirstName( $firstName ){
		$this->firstName = $firstName;
	}

	/**
	 *
	 * @param string $lastName
	 */
	function setLastName( $lastName ){
		$this->lastName = $lastName;
	}

	/**
	 *
	 * @param string $phone
	 */
	function setPhone( $phone ){
		$this->phone = $phone;
	}

	/**
	 *
	 * @return boolean
	 */
	function isValid(){
		return !empty( $this->email ) && !empty( $this->phone ) && !empty( $this->firstName );
	}

}
