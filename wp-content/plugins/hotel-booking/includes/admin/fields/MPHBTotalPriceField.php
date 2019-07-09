<?php

class MPHBTotalPriceField extends MPHBNumberField{

	const TYPE = 'total-price';
	
	protected $step = 0.01;
	protected $min = 0;

	protected $inputType = 'number';

	public function renderInput(){
		$result = parent::renderInput();
		$result .= '<span class="description">' . MPHB()->getSettings()->getCurrencySymbol() . '</span>';
		$result .= ' <button type="button" id="mphb-recalculate-total-price" class="button button-secondary button-small">' . __('Recalculate Total', 'motopress-hotel-booking') . '</button>';
		$result .= '<span class="mphb-preloader mphb-hide"></span>';
		$result .= '<div class="mphb-errors-wrapper mphb-hide"></div>';
		return $result;
	}

}