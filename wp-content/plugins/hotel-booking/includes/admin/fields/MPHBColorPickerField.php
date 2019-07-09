<?php

class MPHBColorPickerField extends MPHBTextField{

	const TYPE = 'color-picker';

	protected function renderInput(){
		$result = '<input type="text" name="' . esc_attr( $this->getName() ) . '" value="' . esc_attr( $this->getValue() ) . '" id="' . MPHB()->addPrefix( $this->getName() ) . '" class="' . $this->generateSizeClasses() . '"' . $this->generateAttrs() . '/>';

		return $result;
	}

}
