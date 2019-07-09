<?php

abstract class MPHBInputGroup {
	/**
	 *
	 * @var MPHBInputField[]
	 */
	protected $fields = array();
	protected $name;
	protected $label;

	public function __construct($name, $label) {
		$this->name = $name;
		$this->label = $label;
	}

	public function addField(  MPHBInputField $field ){
		$this->fields[] = $field;
	}

	public function getName(){
		return $this->name;
	}

	public function getLabel() {
		return $this->label;
	}

	public function setName( $name ) {
		$this->name = $name;
	}

	/**
	 *
	 * @return MPHBInputField[]
	 */
	public function getFields() {
		return $this->fields;
	}

	abstract public function render();
	abstract public function save();

	public function getAttsFromRequest( $request = null ){
		if ( is_null($request) ) {
			$request = $_REQUEST;
		}

		$atts = array();
		foreach ($this->fields as $field) {
			if ( isset($request[$field->getName()]) ) {
				$value = $request[$field->getName()];
				$atts[$field->getName()] = $field->sanitize($value);
			}
		}

		return $atts;
	}

}