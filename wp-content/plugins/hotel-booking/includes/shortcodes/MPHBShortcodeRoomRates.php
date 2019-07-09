<?php
class MPHBShortcodeRoomRates extends MPHBShortcode{

	protected $shortcodeName = 'mphb_rates';

	/**
	 *
	 * @param array $atts
	 * @param null $content
	 * @param string $shortcodeName
	 * @return string
	 */
	public function render($atts, $content = null, $shortcodeName){
		$atts = shortcode_atts(array(
			'id' => get_the_ID()
		), $atts, $shortcodeName);

		ob_start();

		do_action('mphb_sc_room_rates_before_form');

		$roomType = new MPHBRoomType($atts['id']);

		if ( $roomType->isCorrect() ) {
			$rates = $roomType->getRates()->getActiveRates();
			?>
			<ul class="mphb-room-rates-list">
			<?php
			foreach( $rates as $rate ) {
				?>
				<li><?php echo $rate->getTitle(); ?>, <?php echo $rate->getRegularPriceHTML(); ?><br/><?php echo $rate->getDescription(); ?></li>
				<?php
			}
			?>
			</ul>
			<?php
		}

		do_action('mphb_sc_room_rates_after_form');

		return '<div class="mphb_sc_room_rates-wrapper ' . apply_filters('mphb_sc_room_rates_wrapper_classes', '') . '">' . ob_get_clean() . '</div>';
	}

}
