<?php

class MPHBTimePickerField extends MPHBTextField{

	const TYPE = 'timepicker';
	
	private $format = 'h:i a';
	private $dbFormat = 'H:i';

	protected function renderInput(){
		$timeArr = $this->getParsedValue();
		$timeArr = explode(':', $this->value);
		$selectedHours = isset($timeArr[0]) ? $timeArr[0] : 1;
		$selectedMinutes = isset($timeArr[1]) ? $timeArr[1] : 1;
		$am_pm = isset($timeArr[2]) ? $timeArr[2] : 'am';
		
		
		$result = '<div id="' . MPHB()->addPrefix($this->getName()) . '">';
		
		// Hours
		$result .= '<label>';
//		$result .= __('H:', 'motopress-hotel-booking');
		$result .= '<select name="' . esc_attr($this->getName() . '[hours]') . '" ' . $this->generateAttrs() . '>';
		for ($h = 1; $h <= 12; $h++) {
			$hours = str_pad($h, 2, '0', STR_PAD_LEFT);
			$result .= sprintf('<option value="%s" '. selected( $selectedHours, $hours, false ) . '>%s</option>', $hours, $hours);
		}
		$result .= '</select>';
		$result .= '</label>';

		// Minutes
		$result .= '<label>';
//		$result .= __('M:', 'motopress-hotel-booking');
		$result .= '<select name="' . esc_attr($this->getName() . '[minutes]') . '" ' . $this->generateAttrs() . '>';
		for ($m = 0; $m <= 59; $m++) {
			$minutes = str_pad($m, 2, '0', STR_PAD_LEFT);
			$result .= sprintf('<option value="%s" '. selected( $selectedMinutes, $minutes, false ) . '>%s</option>', $minutes, $minutes);
		}
		$result .= '</select>';
		$result .= '</label>';

		// AM/PM
		$result .= '<label>';		
		$result .= '<select name="' . esc_attr($this->getName() . '[am_pm]') . '" ' . $this->generateAttrs() . '>';
		$result .= '<option value="am" '. selected( $am_pm, 'am', false ) . '>' . __('AM', 'motopress-hotel-booking') . '</option>';
		$result .= '<option value="pm" '. selected( $am_pm, 'pm', false ) . '>' . __('PM', 'motopress-hotel-booking') . '</option>';
		$result .= '</select>';
		$result .= '</label>';

		$result .= '</div>';
		
		return $result;
	}

	public function getParsedValue(){
		$value = $this->value;
		$time = $this->convertToFormat($value);
		$timeArr = preg_match('/^(0[1-9]|1[01]):([0-5][0-9]) (am|pm)$/', $time);

		return $timeArr;
	}

	private function convertToDBFormat($time){
		$timeObj = DateTime::createFromFormat($this->format, $time);
		return $timeObj ? $timeObj->format($this->dbFormat) : false;
	}

	private function convertToFormat($time){
		$timeObj = DateTime::createFromFormat($this->dbFormat, $time);
		return $timeObj ? $timeObj->format($this->format) : '';
	}

	public function sanitize($values){		
		if ( !is_array( $values ) ){
			$values = $this->default;
		} else {			
			$time = $values['hours'] . ':' . $values['minutes'] . ' ' . $values['am_pm'];
			$values = $this->convertToDBFormat( $time );
			if (false === $values) {
				$values = $this->default;
			}
		}
		return $values;
	}
}