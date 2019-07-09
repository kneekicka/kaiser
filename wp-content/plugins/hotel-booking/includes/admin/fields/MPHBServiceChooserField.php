<?php

class MPHBServiceChooserField extends MPHBInputField{

	const TYPE = 'service-chooser';

	protected $default = array();
	protected $showAddNew = false;
	protected $showPrices = false;

	public function __construct( $name, $details, $value = '' ) {
		parent::__construct( $name, $details, $value );
		$this->showAddNew = isset($details['show_add_new']) ? $details['show_add_new'] : $this->showAddNew;
		$this->showPrices = isset($details['show_prices']) ? $details['show_prices'] : $this->showPrices;
	}

	protected function renderInput(){
		$list = MPHB()->getServiceCPT()->getServices();
		$servicePostTypeObj = get_post_type_object(MPHB()->getServiceCPT()->getPostType());
		ob_start();
		?>
		<div class="categorydiv" id="<?php echo MPHB()->addPrefix($this->getName()); ?>" >
			<div class="tabs-panel">
				<input type="hidden" name="<?php echo esc_attr($this->getName()); ?>" value="">
				<ul class="categorychecklist form-no-clear">
					<?php foreach ($list as $serviceId => $service) { ?>
					<li class="popular-category">
						<?php $label = ( $this->showPrices ? $service->getTitle() . ' (' . $service->getPriceWithConditions() . ')' : $service->getTitle() ); ?>
						<label class="selectit"><input value="<?php echo esc_attr( $serviceId ); ?>" type="checkbox" name="<?php echo esc_attr($this->getName()); ?>[]" <?php echo in_array( $serviceId, $this->value ) ? 'checked="checked"' : ''; ?> /> <?php echo $label; ?></label>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<?php if ( $this->showAddNew ) { ?>
<a href="<?php echo esc_attr(MPHB()->getServiceCPT()->getAddNewLink()); ?>" target="_blank" class="taxonomy-add-new">+ <?php echo $servicePostTypeObj->labels->add_new_item; ?></a>
		<?php
		}
		return ob_get_clean();
	}

}