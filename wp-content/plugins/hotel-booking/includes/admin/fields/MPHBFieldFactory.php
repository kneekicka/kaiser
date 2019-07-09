<?php

class MPHBFieldFactory {
	/**
	 *
	 * @param string $name
	 * @param array $details
	 * @param mixed $value
	 * @return MPHBInputField
	 */
	public static function create($name, $details, $value = null){
		switch($details['type']) {
			case 'text':
				return new MPHBTextField($name, $details, $value);
				break;
			case 'number':
				return new MPHBNumberField($name, $details, $value);
				break;
			case 'email':
				return new MPHBEmailField($name, $details, $value);
				break;
			case 'textarea':
				return new MPHBTextareaField($name, $details, $value);
				break;
			case 'rich-editor':
				return new MPHBRichEditorField($name, $details, $value);
				break;
			case 'select':
				return new MPHBSelectField($name, $details, $value);
				break;
			case 'page-select':
				return new MPHBPageSelectField($name, $details, $value);
				break;
			case 'dynamic-select':
				return new MPHBDynamicSelectField($name, $details, $value);
				break;
			case 'multiple-select':
				return new MPHBMultipleSelectField($name, $details, $value);
				break;
			case 'gallery':
				return new MPHBGalleryField($name, $details, $value);
				break;
			case 'datepicker':
				return new MPHBDatePickerField($name, $details, $value);
				break;
			case 'timepicker':
				return new MPHBTimePickerField($name, $details, $value);
				break;
			case 'complex':
				return new MPHBComplexHorizontalField($name, $details, $value);
				break;
			case 'complex-vertical':
				return new MPHBComplexVerticalField($name, $details, $value);
				break;
			case 'total-price':
				return new MPHBTotalPriceField($name, $details, $value);
				break;
			case 'service-chooser':
				return new MPHBServiceChooserField($name, $details, $value);
				break;
			case 'checkbox':
				return new MPHBCheckboxField($name, $details, $value);
				break;
			case 'color-picker':
				return new MPHBColorPickerField($name, $details, $value);
				break;
		}
		return $field;
	}
}