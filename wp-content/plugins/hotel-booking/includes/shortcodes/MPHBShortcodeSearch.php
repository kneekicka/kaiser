<?php
class MPHBShortcodeSearch extends MPHBShortcode{

	protected $shortcodeName = 'mphb_availability_search';

	private $uniqid = '';
	private $checkInDate;
	private $checkOutDate;
	private $adults;
	private $childs;

	/**
	 *
	 * @param array $atts
	 * @param null $content
	 * @param string $shortcodeName
	 * @return string
	 */
	public function render($atts, $content = null, $shortcodeName){
		$atts = shortcode_atts(array(
			'adults' => MPHB()->getSettings()->getMinAdults(),
			'childs' => 0,
			'check_in_date' => '',
			'check_out_date' => '',
			'uniqid' => uniqid('mphb-search-form-')
		), $this->fillStoredSearchParameters($atts), $shortcodeName);

		$this->setup($atts);

		$this->enqueueScriptsStyles();

		ob_start();

		do_action('mphb_sc_search_before_form');

		$this->renderSearchForm();

		do_action('mphb_sc_search_after_form');

		return '<div class="mphb_sc_search-wrapper' . apply_filters('mphb_sc_search_wrapper_classes', '') . '">' . ob_get_clean() . '</div>';
	}

	public function fillStoredSearchParameters($atts){

		$storedParameters = MPHB()->getStoredSearchParameters();
		$allowedAtts = array( 'check_in_date', 'check_out_date', 'adults', 'childs');

		foreach ( $allowedAtts as $attName ) {
			if ( !isset( $atts[$attName] ) || empty( $atts[$attName] ) && isset($storedParameters['mphb_' . $attName]) ) {
				$atts[$attName] = (string) $storedParameters['mphb_' . $attName];
			}
		}

		return $atts;
	}

	private function setup($atts){
		$this->uniqid = $atts['uniqid'];
		$this->adults = $this->sanitizeAdults($atts['adults']);
		$this->childs = $this->sanitizeChilds($atts['childs']);
		$this->checkInDate = $this->sanitizeCheckInDate($atts['check_in_date']);
		$this->checkOutDate = $this->sanitizeCheckOutDate($atts['check_out_date']);
	}

	private function sanitizeAdults($adults){
		$adults = absint($adults);
		return $adults >= MPHB()->getSettings()->getMinAdults() && $adults <= MPHB()->getSettings()->getMaxAdults() ? $adults : MPHB()->getSettings()->getMinAdults();
	}

	private function sanitizeChilds($childs){
		$childs = absint($childs);
		return $childs >= MPHB()->getSettings()->getMinChilds() && $childs <= MPHB()->getSettings()->getMaxChilds() ? $childs : 0;
	}

	/**
	 *
	 * @param string $date
	 * @return DateTime|null
	 */
	private function sanitizeCheckInDate($date){
		$checkInDateObj = MPHBUtils::createCheckInDate( MPHB()->getSettings()->getDateFormat(), $date );
		$todayDate = MPHBUtils::createCheckInDate('Y-m-d', mphb_current_time('Y-m-d'));
		return $checkInDateObj && MPHBUtils::calcNights( $todayDate, $checkInDateObj ) >= 0 ? $checkInDateObj : null;
	}

	/**
	 *
	 * @param string $date
	 * @return DateTime|null
	 */
	private function sanitizeCheckOutDate($date){
		$checkOutDateObj = MPHBUtils::createCheckOutDate( MPHB()->getSettings()->getDateFormat(), $date );
		return $checkOutDateObj && ( isset($this->checkInDate) && MPHBUtils::calcNights( $this->checkInDate, $checkOutDateObj ) >= 1  ) ? $checkOutDateObj : null;
	}

	private function renderSearchForm(){
		$formattedCheckInDate = isset($this->checkInDate) ? $this->checkInDate->format(MPHB()->getSettings()->getDateFormat()) : '';
		$formattedCheckOutDate = isset($this->checkOutDate) ? $this->checkOutDate->format(MPHB()->getSettings()->getDateFormat()) : '';

		$action = MPHB()->getSettings()->getSearchResultsPageUrl();
		?>
		<form method="GET" class="mphb_sc_search-form" action="<?php echo $action; ?>">
			<p class="mphb-required-fields-tip"><small><?php _e('Required fields are followed by <strong><abbr title="required">*</abbr></strong>', 'motopress-hotel-booking'); ?></small></p>
			<?php
				$parameters = array();
				$actionQuery = parse_url($action, PHP_URL_QUERY);
				parse_str( $actionQuery, $parameters );
				foreach ( $parameters as $paramName => $paramValue ) {
					printf('<input type="hidden" name="%s" value="%s" />', $paramName, $paramValue);
				}
			?>
			<?php do_action('mphb_sc_search_form_top');?>
			<p class="mphb_sc_search-check-in-date">
				<label for="<?php echo 'mphb_check_in_date-' . $this->uniqid; ?>"><?php _e('Check-in', 'motopress-hotel-booking'); ?></label>&nbsp;<strong><abbr title="<?php printf(_x('Formated as %s', 'Date format tip','motopress-hotel-booking'), 'mm/dd/yyyy'); ?>">*</abbr></strong><br />
				<input id="<?php echo 'mphb_check_in_date-' . $this->uniqid; ?>" data-datepick-group="<?php echo $this->uniqid; ?>" value="<?php echo $formattedCheckInDate; ?>" placeholder="<?php _e('Check-in Date', 'motopress-hotel-booking'); ?>" required="required" type="text" name="mphb_check_in_date" class="mphb-datepick" autocomplete="off"/>
			</p>
			<p class="mphb_sc_search-check-out-date">
				<label for="<?php echo 'mphb_check_out_date-' . $this->uniqid; ?>"><?php _e('Check-out', 'motopress-hotel-booking'); ?></label>&nbsp;<strong><abbr title="<?php printf(_x('Formated as %s', 'Date format tip','motopress-hotel-booking'), 'mm/dd/yyyy'); ?>">*</abbr></strong><br />
				<input id="<?php echo 'mphb_check_out_date-' . $this->uniqid; ?>" data-datepick-group="<?php echo $this->uniqid; ?>" value="<?php echo $formattedCheckOutDate; ?>" placeholder="<?php _e('Check-out Date', 'motopress-hotel-booking'); ?>" required="required" type="text" name="mphb_check_out_date" class="mphb-datepick" autocomplete="off" />
			</p>
			<p class="mphb_sc_search-adults">
				<label for="<?php echo 'mphb_adults-' . $this->uniqid; ?>"><?php _e('Adults', 'motopress-hotel-booking'); ?></label>&nbsp;<strong><abbr title="<?php _e('Required', 'motopress-hotel-booking'); ?>">*</abbr></strong><br />
				<select id="<?php echo 'mphb_adults-' . $this->uniqid; ?>" name="mphb_adults" required="required">
				<?php foreach( MPHB()->getSettings()->getAdultsList() as $value ) { ?>
					<option value="<?php echo $value; ?>" <?php selected($this->adults, $value); ?>><?php echo $value; ?></option>
				<?php } ?>
				</select>
			</p>
			<p class="mphb_sc_search-childs">
				<label for="<?php echo 'mphb_childs-' . $this->uniqid; ?>"><?php _e('Child', 'motopress-hotel-booking'); ?></label>&nbsp;<strong><abbr title="<?php _e('Required', 'motopress-hotel-booking'); ?>">*</abbr></strong><br />
				<select id="<?php echo 'mphb_childs-' . $this->uniqid; ?>" name="mphb_childs" required="required">
				<?php foreach( MPHB()->getSettings()->getChildsList() as $value ) { ?>
					<option value="<?php echo $value; ?>" <?php echo selected($this->childs, $value); ?>><?php echo $value; ?></option>
				<?php } ?>
				</select>
			</p>
			<?php do_action('mphb_sc_search_form_before_submit_btn'); ?>
			<p class="mphb_sc_search-submit-button-wrapper">
				<input type="submit" class="button" value="<?php _e('Search Room', 'motopress-hotel-booking'); ?>"/>
			</p>
			<?php do_action('mphb_sc_search_form_bottom'); ?>
		</form>
		<?php
	}

	private function enqueueScriptsStyles(){
		MPHB()->getFrontendMainScriptManager()->enqueue();
	}

}
