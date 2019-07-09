<?php
class Tm_Builder_Add_To_Cart extends Tm_Builder_Module {

	function init() {
		$this->name = esc_html__( 'Product Add To Cart', 'tm-builder-integrator' );
		$this->icon = 'f07a';
		$this->slug = 'tm_pb_add_to_cart';
		$this->main_css_element = '%%order_class%%.' . $this->slug;

		$this->whitelisted_fields = array(
			'id',
			'quantity',
			'sku',
			'show_price',
			'module_id',
			'module_class',
		);
	}

	function get_fields() {

		$fields = array(
			'id' => array(
				'label'           => esc_html__( 'Product ID', 'tm-builder-integrator' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'default'         => '',
			),
			'quantity' => array(
				'label'           => esc_html__( 'Quantity', 'tm-builder-integrator' ),
				'type'            => 'range',
				'option_category' => 'basic_option',
				'default'         => '1',
				'range_settings' => array(
					'min'  => '1',
					'max'  => '100',
					'step' => '1',
				),
			),
			'sku' => array(
				'label'           => esc_html__( 'Product SKU', 'tm-builder-integrator' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'default'         => '',
			),
			'show_price' => array(
				'label'           => esc_html__( 'Show Price?', 'tm-builder-integrator' ),
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
				'id',
				'quantity',
				'sku',
				'show_price',
			)
		);

		$callback = tm_builder_integrator()->get_shortcode_cb( 'woocommerce', 'add_to_cart' );

		if ( ! is_callable( $callback ) ) {
			return;
		}

		if ( 'off' !== $this->_var( 'show_price' ) ) {
			$this->_var( 'show_price', 'true' );
		} else {
			$this->_var( 'show_price', 'false' );
		}

		$content = call_user_func(
			$callback,
			array(
				'sku'        => $this->_var( 'sku' ),
				'id'         => $this->_var( 'id' ),
				'quantity'   => $this->_var( 'quantity' ),
				'show_price' => $this->_var( 'show_price' ),
			)
		);

		$output = $this->wrap_clean( $content, array(), $function_name );

		return $output;
	}
}

new Tm_Builder_Add_To_Cart;
