<?php
if (empty($price)) {
	$price = mprm_get_price();
}
if (!empty($price)) {
	$price = mprm_currency_filter(mprm_format_amount($price));
	?>
	<div class="mprm-price-box">
		<?php echo( '<span>' . esc_html__( 'Price:', 'woods' ) . '</span>' ); ?>
		<h5 class="mprm-price"><?php printf(__('%s', 'mp-restaurant-menu'), $price); ?></h5>
	</div>
	<?php
}
