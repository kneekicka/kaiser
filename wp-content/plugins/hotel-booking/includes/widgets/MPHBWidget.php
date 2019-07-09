<?php

class MPHBWidget extends WP_Widget{

	/**
	 *
	 * @param mixed $value
	 * @return boolean
	 */
	protected function convertParameterToBoolean( $value ){
		return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
	}

	protected function sanitizeBoolean( $value ){
//		return $this->convertParameterToBoolean( $value );
		// @todo sanitize
		return (bool) $value;
	}

	/**
	 *
	 * @param string|int $value
	 * @param int|false $min
	 * @param int|false $max
	 * @return string Empty string for uncorrect value
	 */
	protected function sanitizeInt( $value, $min = false, $max = false){
		$value = absint($value);
		return ( $min === false || $value >= $min ) && ( $max === false || $value <= $max ) ? (string) $value : '';
	}

	/**
	 *
	 * @param string $date format 'mm/dd/yyyy'
	 * @return string
	 */
	protected function sanitizeDate( $date, $inFormat = 'm/d/Y', $outFormat = false ){
		if ( $outFormat === false ) {
			$outFormat = $inFormat;
		}
		$dateObj = DateTime::createFromFormat( $inFormat, $date );
		return $dateObj ? $dateObj->format($outFormat) : '';
	}

	public static function register(){
		register_widget( get_called_class() );
	}

}
