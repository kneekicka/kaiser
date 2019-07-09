<?php

class MPHBDynamicSelectField extends MPHBSelectField{

	const TYPE = 'dynamic-select';

	protected $dependency_input;
	protected $ajax_action;
	protected $list_callback;

	public function __construct( $name, $details, $value = '' ) {
		parent::__construct( $name, $details, $value );
		$this->dependency_input = $details['dependency_input'];
		$this->ajax_action = $details['ajax_action'];
		$this->list_callback = $details['list_callback'];
	}

	protected function renderInput(){

		$result = '<select name="' . esc_attr($this->getName()) . '" id="' . MPHB()->addPrefix($this->getName()) . '" ' . $this->generateAttrs() . '>';

		foreach ( $this->list as $key => $label ) {
			$result .= '<option value="' . esc_attr($key) . '"' . selected( $this->getValue(), $key, false) . '>' . esc_html($label) . '</option>';
		}

		$result .= '</select>';
		$result .= '<span class="mphb-preloader mphb-hide"></span>';
		$result .= '<div class="mphb-errors-wrapper mphb-hide"></div>';
		return $result;
	}

	protected function generateAttrs() {
		$attrs = parent::generateAttrs();
		$attrs .= ( isset($this->dependency_input) ) ? ' data-dependency="' . $this->dependency_input . '"' : '';
		$attrs .= ' data-ajax-action="' . $this->ajax_action . '"';
		$attrs .= ' data-ajax-nonce="' . wp_create_nonce( $this->ajax_action ) . '"';
		return $attrs;
	}

	public function getDependencyInput(){
		return $this->dependency_input;
	}

	public function sanitize($value){
		return sanitize_text_field($value);
	}

	public function updateList($dependencyValue){
		$list = call_user_func($this->list_callback, $dependencyValue);
		$this->list = array('' => __('— Select —', 'motopress-hotel-booking' )) + $list;
	}

}