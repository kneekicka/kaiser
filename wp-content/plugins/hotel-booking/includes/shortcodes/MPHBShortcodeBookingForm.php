<?php
class MPHBShortcodeBookingForm extends MPHBShortcode{

	protected $shortcodeName = 'mphb_availability';

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

		do_action('mphb_sc_booking_form_before_form');

		$roomType = new MPHBRoomType($atts['id']);

		if ( $roomType->isCorrect() ) {
			MPHB()->getFrontendMainScriptManager()->addRoomTypeData($roomType->getId());
			MPHB()->getFrontendMainScriptManager()->enqueue();
			mphb_tmpl_the_room_reservation_form($roomType);
		}

		do_action('mphb_sc_booking_form_after_form');

		return '<div class="mphb_sc_booking_form-wrapper ' . apply_filters('mphb_sc_booking_form_wrapper_classes', '') . '">' . ob_get_clean() . '</div>';
	}

}
