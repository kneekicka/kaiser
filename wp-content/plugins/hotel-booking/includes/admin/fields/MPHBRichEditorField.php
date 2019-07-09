<?php

class MPHBRichEditorField extends MPHBTextareaField{

	const TYPE = 'rich-editor';

	protected $rows = 10;

	public function __construct($name, $details, $value = '') {
		parent::__construct($name, $details, $value);
		$this->rows = ( isset($details['rows']) ) ? $details['rows'] : $this->rows;
	}

	protected function renderInput(){
		ob_start();
		wp_editor( $this->value, 'mphb_field_' . $this->getName(), array(
			'wpautop' => false,
			'media_buttons' => true,
			'textarea_name' => esc_attr($this->getName()),
			'textarea_rows' => $this->rows,
			'editor_class' => $this->generateSizeClasses(),
			'tinymce'       => array(
				'toolbar1' => 'bold,italic,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,spellchecker,wp_adv'
			),
		));
		$result = ob_get_clean();

		return $result;
	}

}
