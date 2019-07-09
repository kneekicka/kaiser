<?php

abstract class MPHBShortcode{

	protected $shortcodeName;

	public function __construct() {
		$this->addActions();
	}

	public function addActions(){
		add_action('init', array($this, 'register'));		
	}

	public function register(){
		add_shortcode($this->shortcodeName, array($this, 'render'));
	}	

	abstract public function render($atts, $content = '', $shortcodeName);

	/**
	 *
	 * @param array $attrs Attributes of shortcode
	 * @return string
	 */
	public function generateShortcode($attrs = array()){
		$shortcode	= '[' . $this->shortcodeName;
		foreach ($attrs as $attrName => $attrValue) {
			$shortcode .= sprintf(' %s="%s"', $attrName, $attrValue);
		}
		$shortcode .= ']';

		return $shortcode;
	}

	public function convertParameterToBoolean( $value ){
		return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
	}

	public function getShortcodeName(){
		return $this->shortcodeName;
	}

}
