<?php

class MPHBDatePickerField extends MPHBTextField{

	const TYPE = 'datepicker';

	private $multiple = false;
	private $delimiter = ',';
	private $format;
	private $datepickFormat;

	protected $readonly = true;

	public function __construct($name, $details, $value = '') {		
		parent::__construct($name, $details, $value);
		$this->multiple = isset($details['multiple']) ? $details['multiple'] : $this->multiple;
		$this->delimiter = isset($details['delimiter']) ? $details['delimiter'] : $this->delimiter;
		$this->detectFormat(isset($details['format']) ? $details['format'] : null);
	}

	public function detectFormat($format = null){
		$this->format = !is_null( $format ) ? $format : MPHB()->getSettings()->getDateFormat();
		$this->datepickFormat = 'mm/dd/yyyy';
		$this->pattern = $this->multiple ? '^((0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])\/(19|20)\d{2})(,(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])\/(19|20)\d{2})*$' : '^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])\/(19|20)\d{2}$';
	}

	public function getFormattedValue(){
		$value = $this->value;
		if (!empty($value)) {			
			$dates = ($this->multiple) ? explode($this->delimiter, $value) : (array) $value;
			foreach ($dates as &$date) {
				$date = $this->convertToFormat($date);
			}
			$value = implode($this->delimiter, $dates);
		}		
		return $value;
	}

	private function convertToDBFormat($date){
		$dateObj = DateTime::createFromFormat($this->format, $date);
		return $dateObj ? $dateObj->format('Y-m-d') : false;
	}

	private function convertToFormat($date){
		$dateObj = DateTime::createFromFormat('Y-m-d', $date);
		return $dateObj ? $dateObj->format($this->format) : '';		
	}

	protected function renderInput(){		
		$result = '<input type="text" name="' . esc_attr($this->getName()) . '" value="' . esc_attr($this->getFormattedValue()) . '" id="' . MPHB()->addPrefix($this->getName()) . '" class="' . $this->generateSizeClasses() . '"' . $this->generateAttrs() . '/>';

		return $result;
	}

	protected function generateAttrs() {
		$attrs = parent::generateAttrs();
		$attrs .= ($this->multiple) ? ' data-multiple="multiple"' : '';
		$attrs .= ' data-format="' . esc_attr( $this->datepickFormat ) . '"';
		$attrs .= !empty( $this->pattern ) ? ' pattern="' . esc_attr( $this->pattern) . '"' : '';
		return $attrs;
	}

	public function sanitize($value){
		if ($this->multiple) {
			$value = explode($this->delimiter, $value);
		} else {
			$value = (array) $value;
		}
		foreach ($value as $key => &$date) {
			if ( !$date = $this->convertToDBFormat($date) ) {
				unset($value[$key]);
			}
		}
		return implode($this->delimiter, $value);
	}
}