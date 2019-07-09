<?php

class MPHBTextField extends MPHBInputField{

	const TYPE = 'text';

	protected $inputType;
	protected $size = 'regular';
	protected $placeholder = '';
	protected $pattern;

	public function __construct($name, $details, $value = '') {
		parent::__construct($name, $details, $value);
		$this->size = ( isset($details['size']) ) ? $details['size'] : $this->size;
		$this->placeholder = ( isset($details['placeholder']) ) ? $details['placeholder'] : $this->placeholder;		
		if ( !isset($this->inputType) ) {
			$this->inputType = static::TYPE;
		}
	}

	protected function renderInput(){
		$result = '<input name="' . esc_attr($this->getName()) . '" value="' . esc_attr($this->value) . '" id="' . MPHB()->addPrefix($this->getName()) . '" class="' . $this->generateSizeClasses() .'"' . $this->generateAttrs() . '/>';
		return $result;
	}

	protected function generateAttrs() {
		$attrs = parent::generateAttrs();
		$attrs .= ' type="' . esc_attr( $this->inputType ) . '"';
		$attrs .= ( !empty($this->placeholder) ) ? ' placeholder="' . esc_attr($this->placeholder) . '"' : '';
		$attrs .= ( !empty($this->pattern) ) ?  ' pattern="' . esc_attr($this->pattern) . '"' : '';
		return $attrs;
	}

	protected function generateSizeClasses(){
		$classes = '';
		switch($this->size) {
			case 'small':
				$classes .= ' small-text';
				break;
			case 'regular':
				$classes .= ' regular-text';
				break;
			case 'large':
				$classes .= ' large-text';
				break;
			case 'medium':
				$classes .= ' all-options';
				break;
			case 'price':
				$classes .= ' mphb-price-text';
				break;
			case 'long-price':
				$classes .= ' mphb-long-price-text';
				break;
		}
		return $classes;
	}

	public function sanitize($value){
		return sanitize_text_field($value);
	}
}
