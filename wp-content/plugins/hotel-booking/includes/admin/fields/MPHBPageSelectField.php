<?php

class MPHBPageSelectField extends MPHBInputField{

	const TYPE = 'page-select';

	protected $depth;
	protected $child_of;


	public function __construct( $name, $details, $value = '' ) {
		parent::__construct( $name, $details, $value );
	}


	protected function renderInput(){

		$args = array(
			'name' => $this->getName(),
			'id' => $this->getName(),			
			'echo' => 0,
			'selected' => $this->getValue(),
			'show_option_none' => __('— Select —', 'motopress-hotel-booking'),
			'option_none_value' => ''
		);

		$result = wp_dropdown_pages($args);
		return $result;
	}

	public function sanitize($value){
		$value = sanitize_text_field($value);
		return get_post($value) ? $value : $this->default;
	}
}