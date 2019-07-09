<?php
class Tm_Builder_Add_To_Cart_Url extends Tm_Builder_Module {

	function init() {
		$this->name = esc_html__( 'Product Add To Cart URL', 'tm-builder-integrator' );
		$this->icon = 'f07a';
		$this->slug = 'tm_pb_add_to_cart_url';
		$this->main_css_element = '%%order_class%%.' . $this->slug;

		$this->whitelisted_fields = array(
			'id',
			'sku',
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
			'sku' => array(
				'label'           => esc_html__( 'Product SKU', 'tm-builder-integrator' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'default'         => '',
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
				'sku',
			)
		);

		$callback = tm_builder_integrator()->get_shortcode_cb( 'woocommerce', 'add_to_cart_url' );

		if ( ! is_callable( $callback ) ) {
			return;
		}

		$content = call_user_func(
			$callback,
			array(
				'sku'        => $this->_var( 'sku' ),
				'id'         => $this->_var( 'id' ),
			)
		);

		$output = $this->wrap_clean( $content, array(), $function_name );

		return $output;
	}
}

new Tm_Builder_Add_To_Cart_Url;
