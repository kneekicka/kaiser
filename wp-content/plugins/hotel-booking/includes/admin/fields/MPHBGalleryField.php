<?php

class MPHBGalleryField extends MPHBInputField{

	const TYPE = 'gallery';

	private $delimiter = ',';

	public function __construct($name, $details, $value = '') {
		parent::__construct($name, $details, $value);
		$this->delimiter = isset($details['delimiter']) ? $details['delimiter'] : $this->delimiter;
	}

	protected function renderInput() {
		$result = '';
		$previewSrc = '';
		$previewClass = '';
		$addGalleryClass = '';
		$removeGalleryClass = '';
		if (empty($this->value)) {
			$previewClass .= ' mphb-hide';
			$removeGalleryClass = ' mphb-hide';
		} else {
			$ids = explode($this->delimiter, $this->value);
			$previewId = array_shift($ids);
			$previewSrc = wp_get_attachment_image_src($previewId, 'medium', false);
			$previewSrc = ($previewSrc) ? $previewSrc[0] : '';
			$addGalleryClass .= ' mphb-hide';
		}

		$result = '<input type="hidden" name="' . esc_attr($this->getName()) . '" value="' . esc_attr($this->value) . '" id="' . MPHB()->addPrefix($this->getName()) . '"' . $this->generateAttrs() . '/>';
		$result .= '<div><img src="' . esc_url($previewSrc) . '" class="attachment-post-thumbnail size-post-thumbnail' . $previewClass . '"/></div>';
		$result .= '<a href="#" class="mphb-admin-organize-gallery-add' . $addGalleryClass . '">' . __('Add Gallery', 'motopress-hotel-booking') . '</a>';
		$result .= '<a href="#" class="mphb-admin-organize-gallery-remove' . $removeGalleryClass . '">' . __('Remove Gallery', 'motopress-hotel-booking') . '</a>';

		return $result;
	}
	
	public function sanitize($value){
		$images = explode($this->delimiter, $value);
		foreach ($images as $key => $image) {
			if (!wp_attachment_is_image($image)) {
				unset($images[$key]);
			}
		}
		return implode($this->delimiter, $images);
	}

}