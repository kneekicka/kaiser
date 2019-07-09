<?php
class Tm_Builder_Booked_Appointments extends Tm_Builder_Module {

	function init() {
		$this->name = esc_html__( 'Booked Appointments', 'tm-builder-integrator' );
		$this->icon = 'f274';
		$this->slug = 'tm_pb_booked_appointments';
		$this->main_css_element = '%%order_class%%.' . $this->slug;

		$this->whitelisted_fields = array(
			'historic',
			'remove_wrapper',
			'module_id',
			'module_class',
		);
	}

	function get_fields() {

		$fields = array(
			'historic' => array(
				'label'           => esc_html__( 'Is Past Appointments?', 'tm-builder-integrator' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm-builder-integrator' ),
					'off' => esc_html__( 'No', 'tm-builder-integrator' ),
				),
			),
			'remove_wrapper' => array(
				'label'           => esc_html__( 'Remove HTML wrappers', 'tm-builder-integrator' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm-builder-integrator' ),
					'off' => esc_html__( 'No', 'tm-builder-integrator' ),
				),
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'tm-builder-integrator' ),
				'type'        => 'text',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'tm-builder-integrator' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'tm_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'tm-builder-integrator' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'tm_pb_custom_css_regular',
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {

		$this->set_vars(
			array(
				'historic',
				'remove_wrapper',
			)
		);

		$callback = tm_builder_integrator()->get_shortcode_cb( 'booked', 'booked-appointments' );

		if ( ! is_callable( $callback ) ) {
			return;
		}

		if ( 'off' !== $this->_var( 'historic' ) ) {
			$this->_var( 'historic', 'true' );
		} else {
			$this->_var( 'historic', false );
		}

		if ( 'off' !== $this->_var( 'remove_wrapper' ) ) {
			$this->_var( 'remove_wrapper', 'true' );
		} else {
			$this->_var( 'remove_wrapper', false );
		}

		$content = call_user_func(
			$callback,
			array(
				'historic'       => $this->_var( 'historic' ),
				'remove_wrapper' => $this->_var( 'remove_wrapper' ),
			)
		);

		$output = $this->wrap_clean( $content, array(), $function_name );

		return $output;
	}
}

new Tm_Builder_Booked_Appointments;
