<?php
class Tm_Builder_Best_Selling_Products extends Tm_Builder_Module {

	function init() {
		$this->name = esc_html__( 'Best Selling Products', 'tm-builder-integrator' );
		$this->icon = 'f07a';
		$this->slug = 'tm_pb_best_selling_products';
		$this->main_css_element = '%%order_class%%.' . $this->slug;

		$this->whitelisted_fields = array(
			'per_page',
			'columns',
			'category',
			'operator',
			'module_id',
			'module_class',
		);
	}

	function get_fields() {

		$fields = array(
			'per_page' => array(
				'label'           => esc_html__( 'Products Per Page', 'tm-builder-integrator' ),
				'type'            => 'range',
				'option_category' => 'basic_option',
				'default'         => '12',
				'range_settings' => array(
					'min'  => '1',
					'max'  => '60',
					'step' => '1',
				),
			),
			'columns' => array(
				'label'           => esc_html__( 'Columns', 'tm-builder-integrator' ),
				'type'            => 'range',
				'option_category' => 'basic_option',
				'default'         => '4',
				'range_settings' => array(
					'min'  => '1',
					'max'  => '6',
					'step' => '1',
				),
			),
			'category' => array(
				'label'           => esc_html__( 'Category slug', 'tm-builder-integrator' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
			),
			'operator' => array(
				'label'           => esc_html__( 'Operator', 'tm-builder-integrator' ),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'options'         => array(
					'IN'     => 'IN',
					'NOT IN' => 'NOT IN',
					'AND'    => 'AND',
				),
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'tm-builder-integrator' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'tm-builder-integrator' ),
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
				'per_page',
				'columns',
				'category',
				'operator',
			)
		);

		$callback = tm_builder_integrator()->get_shortcode_cb( 'woocommerce', 'best_selling_products' );

		if ( ! is_callable( $callback ) ) {
			return;
		}

		$content = call_user_func(
			$callback,
			array(
				'per_page' => $this->_var( 'per_page' ),
				'columns'  => $this->_var( 'columns' ),
				'orderby'  => $this->_var( 'orderby' ),
				'order'    => $this->_var( 'order' ),
			)
		);

		$output = $this->wrap_clean( $content, array(), $function_name );

		return $output;
	}
}

new Tm_Builder_Best_Selling_Products;
