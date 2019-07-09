<?php

/**
 * Description of MPHBInputField
 *
 */
abstract class MPHBInputField {
	protected $name;
	protected $details;
	protected $required = false;
	protected $disabled = false;
	protected $readonly = false;
	protected $default = '';
	protected $value;
	protected $label = '';
	protected $description = '';
	const TYPE = '';

	/**
	 *
	 * @param string $name
	 * @param array $details
	 * @param string $value
	 */
	public function __construct($name, $details, $value = ''/*, $model */) {
		$this->details = $details;
		$this->name = $name;
		$this->required		= ( isset($details['required']) )	? $details['required'] : $this->required;
		$this->disabled		= ( isset($details['disabled']) )	? $details['disabled'] : $this->disabled;
		$this->readonly		= ( isset($details['readonly']) )	? $details['readonly'] : $this->readonly;
		$this->default		= ( isset($details['default']) )	? $details['default']	 : $this->default;
		$this->value		= ( !empty($value) )				? $value : $this->default;
		$this->label		= ( isset($details['label']) )		? $details['label'] : $this->label;
		$this->description	= ( isset($details['description']) )? $details['description'] : $this->description;
	}

	protected function getCtrlClasses(){
		return 'mphb-ctrl mphb-ctrl-' . static::TYPE;
	}

	protected function getCtrlTypeAttr(){
		return ' data-type="' . static::TYPE . '"';
	}

	protected function generateAttrs(){
		$attrs = '';
		$attrs .= ( $this->required ) ? ' required="required"' : '';
		$attrs .= ( $this->disabled ) ? ' disabled="disabled"' : '';
		$attrs .= ( $this->readonly ) ? ' readonly="readonly"' : '';
		return $attrs;
	}

	public function setValue($value){
		$this->value = ( $value !== '' ) ? $value : $this->default;
	}

	public function getValue(){
		return $this->value;
	}

	public function getLabel(){
		return $this->label;
	}

	public function getLabelTag(){
		return !empty($this->label) ? '<label for="mphb-' . $this->getName() . '">' . $this->getLabel() . '</label>' : '';
	}

	public function hasLabel(){
		return $this->label !== false;
	}

	/**
	 *
	 * @param bool $disabled
	 */
	public function setDisabled($disabled){
		$this->disabled = $disabled;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function render(){
		$result = $this->renderInput();
		if ( $this->required ) {
			$result .= '<strong><abbr title="required">*</abbr></strong>';
		}
		if ( !empty( $this->description ) ) {
			$result .= '&nbsp;<span class="description">' . $this->description . '</span>';
		}
		return '<div class="mphb-ctrl-wrapper ' . $this->getCtrlClasses() . '" ' . $this->getCtrlTypeAttr() . '>' . $result . '</div>';
	}

	public function output(){
		echo $this->render();
	}

	public function getDefault() {
		return $this->default;
	}

	abstract protected function renderInput();

	public function sanitize($value){
		return $value;
	}

	public function getType(){
		return static::TYPE;
	}

}
