<?php

class MPHBSettingsGroup extends MPHBInputGroup {
	protected $name;
	protected $page;
	protected $description;

	/**
	 * @note that name of group must
	 *
	 * @param string $name
	 * @param string $label Optional.
	 * @param string $page
	 * @param string $description Optional.
	 */
	public function __construct($name, $label = '', $page, $description = '') {
		parent::__construct($name, $label);
		$this->description = $description;
		$this->page = $page;
	}

	public function addField( MPHBInputField $field ) {
		$field->setValue(get_option($field->getName(), $field->getDefault()));
		parent::addField( $field );
	}

	public function register(){
		add_settings_section($this->getName(), $this->getLabel(), array($this, 'render'), $this->getPage());
		foreach ($this->fields as $field) {
			register_setting($this->getName(), $field->getName());
			add_settings_field($field->getName(), $field->getLabel(), array($field, 'output'), $this->getPage(), $this->getName());
		}
	}

	public function getPage() {
		return $this->page;
	}

	public function render(){
		if ( !empty( $this->description ) ) {
			echo '<p class="description">' . $this->description . '</p>';
		}
	}

	public function save() {
		foreach ($this->fields as $field) {
			if (isset($_POST[$field->getName()])) {
				$value = $_POST[$field->getName()];
				$value = wp_unslash($value);
				$value = $field->sanitize($value);
				update_option( $field->getName(), $value );
			}
		}
	}

}