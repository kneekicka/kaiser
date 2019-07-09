<?php

class MPHBCheckboxField extends MPHBInputField{

	const TYPE = 'checkbox';

	protected $innerLabel;

	public function __construct( $name, $details, $value = '' ){
		parent::__construct( $name, $details, $value );
		$this->innerLabel = isset($details['inner_label']) ? $details['inner_label'] : '';
	}

	public function getInnerLabel(){
		return $this->innerLabel;
	}

	public function getInnerLabelTag(){
		return !empty($this->innerLabel) ? '<label for="mphb-' . $this->getName() . '">' . $this->getInnerLabel() . '</label>' : '';
	}

	protected function renderInput(){
		$result = '<input name="' . esc_attr($this->getName()) . '" value="0" id="' . MPHB()->addPrefix($this->getName()) . '" ' . $this->generateAttrs() . ' type="hidden"/>';

		$input = '<input name="' . esc_attr($this->getName()) . '" value="1" id="' . MPHB()->addPrefix($this->getName()) . '" ' . $this->generateAttrs() . ' type="checkbox" ' . checked( '1', $this->value, false) . '/>';

		$result .= !empty($this->innerLabel) ? '<label>' . $input . $this->getInnerLabel() . '</label>' : $input;

		return $result;
	}

	public function sanitize($value){
		return sanitize_text_field($value);
	}
}
