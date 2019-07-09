<?php
/**
 * Preprocessor class
 *
 * @package classes
 */

namespace tm_photo_gallery\classes;

use tm_photo_gallery\classes\Core as Core;
use GUMP as GUMP;
use SimpleXMLElement as SimpleXMLElement;
use tm_photo_gallery\classes\lib\FB;

/**
 * Class Preprocessor
 */
class Preprocessor extends GUMP {

	/**
	 * Instance
	 *
	 * @var type
	 */
	protected static $instance;

	/**
	 * Get instance
	 *
	 * @return type
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Install Preprocessors
	 */
	static function install() {
		Core::include_all( TM_PG_PREPROCESSORS_PATH );
	}

	/**
	 * Check for fatal
	 */
	static function fatal_error_handler( $buffer ) {
		$error = error_get_last();
		if ( E_ERROR == $error['type'] ) {
			FB::error( $error, 'error log' );
			// type, message, file, line
			// Preprocessor::get_instance()->add_error_log($_REQUEST, 'fatal error', $error);
		}
		return $buffer;
	}

	/**
	 * Action defalt
	 */
	public function call_controller( $page = 'controller' ) {
		$path = TM_PG_PLUGIN_PATH;
		// if controller exists
		if ( 'controller' != $page || ! file_exists( "{$path}controllers/Controller-{$page}.class.php" ) ) {
			$ControllerName = 'Controller_' . ucfirst( $page );
			if ( class_exists( $ControllerName ) ) {
				die( "Wrong controller {$path}controllers/Controller-{$page}.class.php" );
			}
		}
		$action_request	 = isset( $_REQUEST[ Core::ACTION ] )
								? esc_attr( $_REQUEST[ Core::ACTION ] )
								: esc_attr( $_REQUEST['action'] );
		$action			 = 'action_' . $action_request;
		$controller		 = Core::get_instance()->get_state()->get_controller( $page );
		// if metod exists
		if ( method_exists( $controller, $action ) ) {
			return $controller->$action();
		} else {
			die( "Wrong action {$action}" );
		}
	}

	/**
	 * Add error log
	 *
	 * @param array      $data
	 * @param string     $type
	 * @param bool|false $error
	 * @param bool|false $call_stack
	 */
	public function add_error_log( $data = array(), $type = 'callback',
								$error = false, $call_stack = false ) {
		FB::error( $error, 'error log' );
		if ( ! $call_stack ) {
			$call_stack = debug_backtrace();
		}
		// creating object of SimpleXMLElement
		$xml		 = new SimpleXMLElement( '<?xml version="1.0"?><root></root>' );
		$state		 = Core::get_instance();
		// get current logs
		$logs		 = $state( 'file' )->get_array_from_XML( TM_PG_PLUGIN_PATH . 'log/logs.xml' );
		$logs		 = ! is_array( $logs ) ? array() : $logs;
		// save settings
		$time		 = time();
		$settings	 = array();
		if ( $type == 'callback' ) {
			$settings[ "time_$time" ]['request'] = base64_encode( serialize( $_REQUEST ) );
		}
		$settings[ "time_$time" ]['date']	 = date( 'Y-m-d H:i:s' );
		$settings[ "time_$time" ]['type']	 = $type;
		if ( $error ) {
			$settings[ "time_$time" ]['error'] = base64_encode( serialize( $error ) );
		}
		$settings[ "time_$time" ]['data']		 = base64_encode( serialize( $data ) );
		$settings[ "time_$time" ]['callstack'] = base64_encode( serialize( $call_stack ) );
		$logs								 = array_merge( $logs, $settings );
		// function call to convert array to xml
		$state( 'file' )->array_to_xml( $logs, $xml );
		// saving generated xml file
		$state( 'file' )->create_dir( TM_PG_PLUGIN_PATH . '/log' );
		$xml->asXML( TM_PG_PLUGIN_PATH . '/log/logs.xml' );
	}

	/**
	 * Progress
	 *
	 * @param type $params
	 * @param type $name
	 *
	 * @return type
	 */
	protected function progress( $params, $name, $type ) {
		$success = $this->run( $params );
		if ( false !== $success ) {
			$return = Core::get_instance()->get_model( $type )->$name( $params );
		} else {
			$return = array( 'success' => $success, $this->get_errors_array() );
		}
		return $return;
	}

	/**
	 * Process the validation errors and return an array of errors with field names as keys.
	 *
	 * @param $convert_to_string
	 *
	 * @return array | null (if empty)
	 */
	public function get_errors_array( $convert_to_string = null ) {
		if ( empty( $this->errors ) ) {
			return ($convert_to_string) ? null : array();
		}

		$resp = array();

		foreach ( $this->errors as $e ) {
			$field	 = ucwords( str_replace( array( '_', '-' ), chr( 32 ), $e['field'] ) );
			$param	 = $e['param'];

			// Let's fetch explicit field names if they exist
			if ( array_key_exists( $e['field'], self::$fields ) ) {
				$field = self::$fields[ $e['field'] ];
			}

			switch ( $e['rule'] ) {
				case 'mismatch' :
					$resp[ $e['field'] ]	 = "There is no validation rule for $field";
					break;
				case 'validate_required':
					$resp[ $e['field'] ]	 = "The $field field is required";
					break;
				case 'validate_valid_email':
					$resp[ $e['field'] ]	 = "The $field field is required to be a valid email address";
					break;
				case 'validate_max_len':
					$resp[ $e['field'] ]	 = "The $field field needs to be $param or shorter in length";
					break;
				case 'validate_min_len':
					$resp[ $e['field'] ]	 = "The $field field needs to be $param or longer in length";
					break;
				case 'validate_exact_len':
					$resp[ $e['field'] ]	 = "The $field field needs to be exactly $param characters in length";
					break;
				case 'validate_alpha':
					$resp[ $e['field'] ]	 = "The $field field may only contain alpha characters(a-z)";
					break;
				case 'validate_alpha_numeric':
					$resp[ $e['field'] ]	 = "The $field field may only contain alpha-numeric characters";
					break;
				case 'validate_alpha_dash':
					$resp[ $e['field'] ]	 = "The $field field may only contain alpha characters &amp; dashes";
					break;
				case 'validate_numeric':
					$resp[ $e['field'] ]	 = "The $field field may only contain numeric characters";
					break;
				case 'validate_integer':
					$resp[ $e['field'] ]	 = "The $field field may only contain a numeric value";
					break;
				case 'validate_boolean':
					$resp[ $e['field'] ]	 = "The $field field may only contain a true or false value";
					break;
				case 'validate_float':
					$resp[ $e['field'] ]	 = "The $field field may only contain a float value";
					break;
				case 'validate_valid_url':
					$resp[ $e['field'] ]	 = "The $field field is required to be a valid URL";
					break;
				case 'validate_url_exists':
					$resp[ $e['field'] ]	 = "The $field URL does not exist";
					break;
				case 'validate_valid_ip':
					$resp[ $e['field'] ]	 = "The $field field needs to contain a valid IP address";
					break;
				case 'validate_valid_cc':
					$resp[ $e['field'] ]	 = "The $field field needs to contain a valid credit card number";
					break;
				case 'validate_valid_name':
					$resp[ $e['field'] ]	 = "The $field field needs to contain a valid human name";
					break;
				case 'validate_contains':
					$resp[ $e['field'] ]	 = "The $field field needs to contain one of these values: " . implode( ', ', $param );
					break;
				case 'validate_street_address':
					$resp[ $e['field'] ]	 = "The $field field needs to be a valid street address";
					break;
				case 'validate_date':
					$resp[ $e['field'] ]	 = "The $field field needs to be a valid date";
					break;
				case 'validate_min_numeric':
					$resp[ $e['field'] ]	 = "The $field field needs to be a numeric value, equal to, or higher than $param";
					break;
				case 'validate_max_numeric':
					$resp[ $e['field'] ]	 = "The $field field needs to be a numeric value, equal to, or lower than $param";
					break;
				case 'validate_min_age':
					$resp[ $e['field'] ]	 = "The $field field needs to have an age greater than or equal to $param";
					break;
				default:
					$resp[ $e['field'] ]	 = "The $field field is invalid";
			}
		}

		return $resp;
	}
}
