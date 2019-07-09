<?php

class MPHBUtils {

	/**
	 *
	 * @param string $date date in format 'Y-m-d'
	 * @return string date in front format
	 */
	public static function convertDateFrontToDB( $date ){
		$dateObj = DateTime::createFromFormat(MPHB()->getSettings()->getDateFormat(), $date);
		return $dateObj ? $dateObj->format('Y-m-d') : '';
	}

	/**
	 *
	 * @param string $date date in front format
	 * @return string date in db format ('Y-m-d')
	 */
	public static function convertDateDBToFront( $date ){
		$dateObj = DateTime::createFromFormat('Y-m-d', $date);
		return $dateObj ? $dateObj->format(MPHB()->getSettings()->getDateFormat()) : '';
	}

	/**
	 *
	 * @param Date $date
	 * @return String Date in WordPress Date format.
	 */
	public static function convertDateToWPFront( $date ){
		return date_i18n( MPHB()->getSettings()->getDateFormatWP(), $date->format('U') );
	}

	/**
	 *
	 * @param Date $time
	 * @return String Time in WordPress Time format.
	 */
	public static function convertTimeToWPFront( $time ) {
		return date_i18n( get_option( 'time_format' ), $time->format('U') );
	}

	/**
	 *
	 * @param string $format See http://php.net/manual/ru/datetime.formats.php
	 * @param string $date
	 * @param bool $needSetTime
	 * @return DateTime|bool
	 */
	public static function createCheckInDate( $format, $date, $needSetTime = true ){
		$dateObj = DateTime::createFromFormat( $format, $date );
		if ( $dateObj && $needSetTime) {
			$checkInTime = MPHB()->getSettings()->getCheckInTime(true);
			$dateObj->setTime($checkInTime[0], $checkInTime[1], $checkInTime[2]);
		}

		return $dateObj ? $dateObj : false;
	}

	/**
	 *
	 * @param string $format See http://php.net/manual/ru/datetime.formats.php
	 * @param string $date
	 * @param bool $needSetTime
	 * @return DateTime|bool
	 */
	public static function createCheckOutDate( $format, $date, $needSetTime = true ){
		$dateObj = DateTime::createFromFormat( $format, $date );
		if ( $dateObj && $needSetTime) {
			$checkOutTime = MPHB()->getSettings()->getCheckOutTime(true);
			$dateObj->setTime($checkOutTime[0], $checkOutTime[1], $checkOutTime[2]);
		}
		return $dateObj ? $dateObj : false;
	}

	/**
	 *
	 * @note requires PHP 5 >= 5.3
	 * @param DateTime $checkInDate
	 * @param DateTime $checkOutDate
	 * @return int
	 */
	public static function calcNights( DateTime $checkInDate, DateTime $checkOutDate ){
		$from = clone $checkInDate;
		$to = clone $checkOutDate;

		// set same time to dates
		$from->setTime( 0, 0, 0 );
		$to->setTime( 0, 0, 0);

		$diff = $from->diff($to);

		return (int) $diff->format('%r%a');
	}

	/**
	 * Hex darker/lighter/contrast functions for colours.
	 *
	 * @param mixed $color
	 * @return string
	 */
	public static function rgbFromHex( $color ) {
		$color = str_replace( '#', '', $color );
		// Convert shorthand colors to full format, e.g. "FFF" -> "FFFFFF"
		$color = preg_replace( '~^(.)(.)(.)$~', '$1$1$2$2$3$3', $color );

		$rgb      = array();
		$rgb['R'] = hexdec( $color{0}.$color{1} );
		$rgb['G'] = hexdec( $color{2}.$color{3} );
		$rgb['B'] = hexdec( $color{4}.$color{5} );

		return $rgb;
	}

	/**
	 * Hex darker/lighter/contrast functions for colours.
	 *
	 * @param mixed $color
	 * @param int $factor (default: 30)
	 * @return string
	 */
	public static function hexDarker( $color, $factor = 30 ) {
		$base  = self::rgbFromHex( $color );
		$color = '#';

		foreach ( $base as $k => $v ) {
			$amount      = $v / 100;
			$amount      = round( $amount * $factor );
			$new_decimal = $v - $amount;

			$new_hex_component = dechex( $new_decimal );
			if ( strlen( $new_hex_component ) < 2 ) {
				$new_hex_component = "0" . $new_hex_component;
			}
			$color .= $new_hex_component;
		}

		return $color;
	}

	/**
	 * Hex darker/lighter/contrast functions for colours.
	 *
	 * @param mixed $color
	 * @param int $factor (default: 30)
	 * @return string
	 */
	public static function hexLighter( $color, $factor = 30 ) {
		$base  = self::rgbFromHex( $color );
		$color = '#';

		foreach ( $base as $k => $v ) {
			$amount      = 255 - $v;
			$amount      = $amount / 100;
			$amount      = round( $amount * $factor );
			$new_decimal = $v + $amount;

			$new_hex_component = dechex( $new_decimal );
			if ( strlen( $new_hex_component ) < 2 ) {
				$new_hex_component = "0" . $new_hex_component;
			}
			$color .= $new_hex_component;
		}

		return $color;
	}

	/**
	 * Detect if we should use a light or dark colour on a background colour.
	 *
	 * @param mixed $color
	 * @param string $dark (default: '#000000')
	 * @param string $light (default: '#FFFFFF')
	 * @return string
	 */
	public static function lightOrDark( $color, $dark = '#000000', $light = '#FFFFFF' ) {

		$hex = str_replace( '#', '', $color );

		$c_r = hexdec( substr( $hex, 0, 2 ) );
		$c_g = hexdec( substr( $hex, 2, 2 ) );
		$c_b = hexdec( substr( $hex, 4, 2 ) );

		$brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;

		return $brightness > 155 ? $dark : $light;
	}

	/**
	 * Format string as hex.
	 *
	 * @param string $hex
	 * @return string
	 */
	public static function formatHex( $hex ) {

		$hex = trim( str_replace( '#', '', $hex ) );

		if ( strlen( $hex ) == 3 ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		return $hex ? '#' . $hex : null;
	}

}