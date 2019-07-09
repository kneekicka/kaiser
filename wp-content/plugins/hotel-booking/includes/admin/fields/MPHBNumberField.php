<?php

class MPHBNumberField extends MPHBTextField{

	const TYPE = 'number';

	protected $min;
	protected $max;
	
	protected $step = 1;
	protected $size = 'small';
	protected $default = 0;

	public function __construct($name, $details, $value = '') {
		parent::__construct($name, $details, $value);
		$this->min = isset($details['min']) ? $details['min'] : null;
		$this->max = isset($details['max']) ? $details['max'] : null;
		$this->step = isset($details['step']) ? $details['step'] : $this->step;
	}

	public function generateAttrs() {
		$attrs = parent::generateAttrs();
		$attrs .= ( isset($this->min) ) ? ' min="' . esc_attr($this->min) . '"' : '';
		$attrs .= ( isset($this->max) ) ? ' max="' . esc_attr($this->max) . '"' : '';
		$attrs .= ( isset($this->step) ) ? ' step="' . esc_attr($this->step) . '"' : '';	
		return $attrs;
	}

	public function sanitize($value){
		$value = sanitize_text_field($value);
		$value = is_numeric($value) ? $value : $this->default;
		$value = isset($this->min) && $value < $this->min ? $this->min : $value;
		$value = isset($this->max) && $value > $this->max ? $this->max : $value;
		return $value;
	}
}