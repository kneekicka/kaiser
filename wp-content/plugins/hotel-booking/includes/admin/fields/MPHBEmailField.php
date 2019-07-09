<?php

class MPHBEmailField extends MPHBTextField{

	const TYPE = 'email';
	
	public function sanitize($value){
		return is_email($value) ? $value : $this->default;
	}
}