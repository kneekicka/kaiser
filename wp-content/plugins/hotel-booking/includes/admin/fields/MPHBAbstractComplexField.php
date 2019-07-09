<?php

abstract class MPHBAbstractComplexField extends MPHBInputField {

	const TYPE = 'complex';

	protected $default = array();
	protected $fields = array();
	protected $add_label;
	protected $delete_label;
	protected $prototypeFields = array();
	protected $uniqid = '';

	public function __construct( $name, $details, $values = array()) {
		parent::__construct( $name, $details, $values );
		$this->add_label = isset($details['add_label']) ? $details['add_label'] : __('Add', 'motopress-hotel-booking');
		$this->delete_label = isset($details['delete_label']) ? $details['delete_label'] : __('Delete', 'motopress-hotel-boooking');
		$this->uniqid = uniqid();
		if (is_array($details['fields'])) {
			foreach ($details['fields'] as $field) {
				if (is_a($field, 'MPHBInputField')) {
					$this->fields[] = $field;
				}
			}
		}
	}

	protected function renderAddItemButton($attrs = '', $classes = ''){
		return '<button type="button" class="button mphb-complex-add-item ' . $classes . '" data-id="' . $this->uniqid . '" ' . $attrs . '>' . $this->add_label . '</button>';
	}

	protected function renderDeleteItemButton($attrs = '', $classes = ''){
		return '<button type="button" class="button mphb-complex-delete-item ' . $classes . '" data-id="' . $this->uniqid . '" ' . $attrs . '>' . $this->delete_label . '</button>';
	}

	abstract protected function generateItem($key, $value, $prototype = false);

}
