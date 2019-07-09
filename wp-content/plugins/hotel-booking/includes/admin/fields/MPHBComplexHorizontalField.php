<?php

class MPHBComplexHorizontalField extends MPHBAbstractComplexField {

	const TYPE = 'complex';	

	protected function renderInput() {

		$result = '<input type="hidden" name="' . $this->getName() . '" value="" />';
		$result .= '<table class="widefat striped mphb-table-centered" data-uniqid="' . $this->uniqid . '">';
		$result .= '<thead>';

		$result .= '<tr>';
		foreach($this->fields as $field) {
			$result .= '<th class="row-title">' . $field->getLabel() . '</th>';
		}

		$result .= '<th>' . __('Actions', 'motopress-hotel-booking') . '</th>';
		$result .= '</tr>';
		$result .= '</thead>';
		$result .= '<tbody>';		
		$result .= $this->generateItem('%key_' . $this->uniqid . '%', array(), true);
		foreach($this->value as $key => $value) {
			$result .= $this->generateItem($key, $value);
		}
		$result .= '</tbody>';
		$result .= '<tfoot><tr><td colspan="' . ( count($this->fields) + 1 ) . '">';
		$result .= $this->renderAddItemButton();
		$result .= '</td></tr></tfoot>';
		$result .= '</table>';		

		return $result;
	}

	protected function generateItem($key, $value, $prototype = false){
		$itemClass = ($prototype) ? 'mphb-complex-item-prototype mphb-hide' : '';
		$result = '<tr class="' . $itemClass . '" data-id="' . $key . '">';
		foreach($this->fields as $field) {
			$result .= '<td>';
			$newField = clone $field;
			$newField->setName($this->getName() . '[' . $key . ']' . '[' . $field->getName() . ']');
			if ($prototype) {
				$newField->setDisabled(true);
				$value[$field->getName()] = '';
			}
			$newField->setValue( (!$prototype) ? $value[$field->getName()] : '' );
			$result .= $newField->render();
			$result .= '</td>';
		}
		$result .= '<td>';
		$result .= $this->renderDeleteItemButton();

		$result .= '</td>';
		$result .= '</tr>';

		return $result;
	}

	public function sanitize($values){
		if (!is_array($values)){
			$values = $this->default;
		} else {
			$values = array_values($values); // reset keys of array
			foreach ($values as $key => &$value ) {
				foreach ($this->fields as $field) {
					$value[$field->getName()] = $field->sanitize(isset($value[$field->getName()]) ? $value[$field->getName()] : $field->getDefault());
				}
			}
		}
		return $values;
	}

}
