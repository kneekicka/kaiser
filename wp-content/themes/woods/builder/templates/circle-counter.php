<?php
/**
 * Template part for displaying circle counter
 */
?>
<div class="tm_pb_circle_counter_bar container-width-change-notify"<?php echo $this->circle_data_atts(); ?>>
	<div class="percent">
		<p>
			<span class="percent-value"></span><?php echo $this->circle_sign( '%' ); ?>
		</p>
		<?php echo $this->html( $this->_var( 'title' ), '<h6>%s</h6>' ); ?>
	</div>
</div>
