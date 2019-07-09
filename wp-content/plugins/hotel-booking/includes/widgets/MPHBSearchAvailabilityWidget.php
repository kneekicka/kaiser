<?php

class MPHBSearchAvailabilityWidget extends MPHBWidget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'mphb_search_availability_widget', // Base ID
			__( 'Search Availability', 'motopress-hotel-booking' ), // Name
			array( 'description' => __( 'Search Availability Form', 'motopress-hotel-booking' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$this->enqueueStylesScripts();
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $args['after_title'];
		}

		$instance = $this->fillStoredSearchParameters($instance);

		$adults = $instance['adults'];
		$childs = $instance['childs'];
		$formattedCheckInDate = $instance['check_in_date'];
		$formattedCheckOutDate = $instance['check_out_date'];

		$action = MPHB()->getSettings()->getSearchResultsPageUrl();
		$uniqid = uniqid();
		?>
		<form method="GET" class="mphb_widget_search-form" action="<?php echo $action; ?>">
			<p class="mphb-required-fields-tip"><small><?php _e('Required fields are followed by <strong><abbr title="required">*</abbr></strong>', 'motopress-hotel-booking'); ?></small></p>
			<?php do_action('mphb_widget_search_form_top');?>
			<p class="mphb_widget_search-check-in-date">
				<label for="<?php echo 'mphb_check_in_date-' . $uniqid; ?>"><?php _e('Check-in:', 'motopress-hotel-booking'); ?></label>&nbsp;<strong><abbr title="<?php printf(_x('Formated as %s', 'Date format tip','motopress-hotel-booking'), 'mm/dd/yyyy'); ?>">*</abbr></strong><br />
				<input id="<?php echo 'mphb_check_in_date-' . $uniqid; ?>" data-datepick-group="<?php echo $uniqid; ?>" value="<?php echo $formattedCheckInDate; ?>" placeholder="<?php _e('Check-in Date', 'motopress-hotel-booking'); ?>" required="required" type="text" name="mphb_check_in_date" class="mphb-datepick" autocomplete="off"/>
			</p>
			<p class="mphb_widget_search-check-out-date">
				<label for="<?php echo 'mphb_check_out_date-' . $uniqid; ?>"><?php _e('Check-out:', 'motopress-hotel-booking'); ?></label>&nbsp;<strong><abbr title="<?php printf(_x('Formated as %s', 'Date format tip','motopress-hotel-booking'), 'mm/dd/yyyy'); ?>">*</abbr></strong><br />
				<input id="<?php echo 'mphb_check_out_date-' . $uniqid; ?>" data-datepick-group="<?php echo $uniqid; ?>" value="<?php echo $formattedCheckOutDate; ?>" placeholder="<?php _e('Check-out Date', 'motopress-hotel-booking'); ?>" required="required" type="text" name="mphb_check_out_date" class="mphb-datepick" autocomplete="off" />
			</p>
			<p class="mphb_widget_search-adults">
				<label for="<?php echo 'mphb_adults-' . $uniqid; ?>"><?php _e('adults:', 'motopress-hotel-booking'); ?></label>&nbsp;<strong><abbr title="<?php _e('Required', 'motopress-hotel-booking'); ?>">*</abbr></strong><br />
				<select id="<?php echo 'mphb_adults-' . $uniqid; ?>" name="mphb_adults" required="required">
				<?php foreach( MPHB()->getSettings()->getAdultsList() as $value ) { ?>
					<option value="<?php echo $value; ?>" <?php selected($adults, $value); ?>><?php echo $value; ?></option>
				<?php } ?>
				</select>
			</p>
			<p class="mphb_widget_search-childs">
				<label for="<?php echo 'mphb_childs-' . $uniqid; ?>"><?php _e('Childs:', 'motopress-hotel-booking'); ?></label>&nbsp;<strong><abbr title="<?php _e('Required', 'motopress-hotel-booking'); ?>">*</abbr></strong><br />
				<select id="<?php echo 'mphb_childs-' . $uniqid; ?>" name="mphb_childs" required="required">
				<?php foreach( MPHB()->getSettings()->getChildsList() as $value ) { ?>
					<option value="<?php echo $value; ?>" <?php echo selected($childs, $value); ?>><?php echo $value; ?></option>
				<?php } ?>
				</select>
			</p>

			<?php do_action('mphb_widget_search_form_before_submit_btn'); ?>
			<p class="mphb_widget_search-submit-button-wrapper">
				<input type="submit" class="button" value="<?php _e('Search Room', 'motopress-hotel-booking'); ?>"/>
			</p>
			<?php do_action('mphb_widget_search_form_bottom'); ?>
		</form>
		<?php
		echo $args['after_widget'];
	}


	/**
	 *
	 * @param array $atts
	 * @return array
	 */
	private function fillStoredSearchParameters($atts){

		$storedParameters = MPHB()->getStoredSearchParameters();
		$allowedAtts = array( 'check_in_date', 'check_out_date', 'adults', 'childs');

		foreach ( $allowedAtts as $attName ) {
			if ( isset( $storedParameters['mphb_' . $attName] ) && !empty( $storedParameters['mphb_' . $attName] ) ) {
				$atts[$attName] = (string) $storedParameters['mphb_' . $attName];
			}
		}

		return $atts;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		$instance = wp_parse_args($instance, array(
			'title' => __( 'Search Room Availability', 'motopress-hotel-booking' ),
			'adults' => MPHB()->getSettings()->getMinAdults(),
			'childs' => MPHB()->getSettings()->getMinChilds(),
			'check_in_date' => '',
			'check_out_date' => ''
		));

		extract($instance);

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'motopress-hotel-booking' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'check_in_date' ) ); ?>"><?php _e( 'Check In Date:', 'motopress-hotel-booking' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'check_in_date' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'check_in_date' ) ); ?>" type="text" value="<?php echo esc_attr( $check_in_date ); ?>"><small><?php printf(_x('Formated as %s', 'Date format tip','motopress-hotel-booking'), 'mm/dd/yyyy'); ?></small>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'check_out_date' ) ); ?>"><?php _e( 'Check Out Date:', 'motopress-hotel-booking' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'check_out_date' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'check_out_date' ) ); ?>" type="text" value="<?php echo esc_attr( $check_out_date ); ?>">
			<small><?php printf(_x('Formated as %s', 'Date format tip','motopress-hotel-booking'), 'mm/dd/yyyy'); ?></small>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'adults' ) ); ?>"><?php _e( 'adults:', 'motopress-hotel-booking' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'adults' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'adults' ) ); ?>" >
				<?php foreach ( MPHB()->getSettings()->getAdultsList() as $adultsCount => $adultsCountLabel ) : ?>
					<option value="<?php echo $adultsCount; ?>" <?php selected($adults, $adultsCount); ?>><?php echo $adultsCountLabel; ?></option>
				<?php endforeach;?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'childs' ) ); ?>"><?php _e( 'Childs:', 'motopress-hotel-booking' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'childs' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'Kinder' ) ); ?>" >
				<?php foreach ( MPHB()->getSettings()->getChildsList() as $childsCount => $childsCountLabel ) : ?>
					<option value="<?php echo $childsCount; ?>" <?php selected($childs, $childsCount); ?>><?php echo $childsCountLabel; ?></option>
				<?php endforeach;?>
			</select>
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title'] =  ( isset($new_instance['title']) && $new_instance['title'] !== '' ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['adults'] = ( isset($new_instance['adults']) && $new_instance['adults'] !== '' ) ? $this->sanitizeInt( $new_instance['adults'], MPHB()->getSettings()->getMinAdults(), MPHB()->getSettings()->getMaxAdults() ) : '';
		$instance['childs'] = ( isset($new_instance['childs']) && $new_instance['childs'] !== '' ) ? $this->sanitizeInt( $new_instance['childs'], MPHB()->getSettings()->getMinChilds(), MPHB()->getSettings()->getMaxChilds() ) : '';
		$instance['check_in_date'] = ( isset($new_instance['check_in_date']) && !empty( $new_instance['check_in_date'] ) ) ? $this->sanitizeDate( $new_instance['check_in_date'] ) : '';
		$instance['check_out_date'] = ( isset($new_instance['check_out_date']) && !empty( $new_instance['check_out_date'] ) ) ? $this->sanitizeDate( $new_instance['check_out_date'] ) : '';

		return $instance;
	}

	public function enqueueStylesScripts(){
		MPHB()->getFrontendMainScriptManager()->enqueue();
	}

}

add_action( 'widgets_init', array('MPHBSearchAvailabilityWidget', 'register') );
