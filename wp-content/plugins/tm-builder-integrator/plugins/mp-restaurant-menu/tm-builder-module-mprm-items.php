<?php
class Tm_Builder_Mprm_Items extends Tm_Builder_Module {

	function init() {
		$this->name = esc_html__( 'Restaurant Menu Items', 'tm-builder-integrator' );
		$this->icon = 'f0f4';
		$this->slug = 'tm_pb_menu_items';
		$this->main_css_element = '%%order_class%%.' . $this->slug;

		$this->whitelisted_fields = array(
			'view',
			'categ',
			'tags_list',
			'item_ids',
			'col',
			'categ_name',
			'show_attributes',
			'feat_img',
			'excerpt',
			'price',
			'tags',
			'ingredients',
			'link_item',
			'desc_length',
			'module_id',
			'module_class',
		);
	}

	function get_fields() {

		$fields = array(
			'view' => array(
				'label'           => esc_html__( 'View mode', 'tm-builder-integrator' ),
				'option_category' => 'basic_option',
				'type'            => 'select',
				'options'         => array(
					'grid' => esc_html__( 'Grid', 'tm-builder-integrator' ),
					'list' => esc_html__( 'List', 'tm-builder-integrator' ),
				),
			),
			'categ' => array(
				'label'           => esc_html__( 'Categories', 'tm-builder-integrator' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'default'         => '',
				'description'     => esc_html__( 'Comma separated categories IDs list', 'tm-builder-integrator' ),
			),
			'tags_list' => array(
				'label'           => esc_html__( 'Tags', 'tm-builder-integrator' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Comma separated tags IDs list', 'tm-builder-integrator' ),
			),
			'item_ids' => array(
				'label'           => esc_html__( 'Menu item IDs', 'tm-builder-integrator' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Comma separated menu items IDs list', 'tm-builder-integrator' ),
			),
			'col' => array(
				'label'           => esc_html__( 'Columns', 'tm-builder-integrator' ),
				'option_category' => 'basic_option',
				'type'            => 'select',
				'options'         => array(
					'1' => __( '1 column', 'tm-builder-integrator' ),
					'2' => __( '2 columns', 'tm-builder-integrator' ),
					'3' => __( '3 columns', 'tm-builder-integrator' ),
					'4' => __( '4 columns', 'tm-builder-integrator' ),
					'6' => __( '6 columns', 'tm-builder-integrator' ),
				),
			),
			'categ_name' => array(
				'label'           => esc_html__( 'Show category name', 'tm-builder-integrator' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'only_text' => __('Only text', 'tm-builder-integrator'),
					'with_img'  => __('Title with image', 'tm-builder-integrator'),
					'none'      => __('Don`t show', 'tm-builder-integrator'),
				),
			),
			'show_attributes' => array(
				'label'           => esc_html__( 'Show attributes', 'tm-builder-integrator' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
			),
			'feat_img' => array(
				'label'           => esc_html__( 'Show featured image', 'tm-builder-integrator' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
			),
			'excerpt' => array(
				'label'           => esc_html__( 'Show excerpt', 'tm-builder-integrator' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
			),
			'price' => array(
				'label'           => esc_html__( 'Show price', 'tm-builder-integrator' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
			),
			'tags' => array(
				'label'           => esc_html__( 'Show tags', 'tm-builder-integrator' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
			),
			'ingredients' => array(
				'label'           => esc_html__( 'Show ingredients', 'tm-builder-integrator' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
			),
			'link_item' => array(
				'label'           => esc_html__( 'Link item', 'tm-builder-integrator' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm_builder' ),
					'off' => esc_html__( 'No', 'tm_builder' ),
				),
			),
			'desc_length' => array(
				'label'           => esc_html__( 'Description length', 'tm-builder-integrator' ),
				'type'            => 'text',
				'option_category' => 'configuration',
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
				'view',
				'categ',
				'tags_list',
				'item_ids',
				'col',
				'categ_name',
				'show_attributes',
				'feat_img',
				'excerpt',
				'price',
				'tags',
				'ingredients',
				'link_item',
				'desc_length',
			)
		);

		$callback = tm_builder_integrator()->get_shortcode_cb( 'mp-restaurant-menu', 'mprm_items' );

		if ( ! is_callable( $callback ) ) {
			return;
		}

		$content = call_user_func(
			$callback,
			array(
				'view'            => $this->_var( 'view' ),
				'categ'           => $this->_var( 'categ' ),
				'tags_list'       => $this->_var( 'tags_list' ),
				'item_ids'        => $this->_var( 'item_ids' ),
				'col'             => $this->_var( 'col' ),
				'categ_name'      => $this->_var( 'categ_name' ),
				'show_attributes' => ( 'on' === $this->_var( 'show_attributes' ) ) ? 1 : 0,
				'feat_img'        => ( 'on' === $this->_var( 'feat_img' ) ) ? 1 : 0,
				'excerpt'         => ( 'on' === $this->_var( 'excerpt' ) ) ? 1 : 0,
				'price'           => ( 'on' === $this->_var( 'price' ) ) ? 1 : 0,
				'tags'            => ( 'on' === $this->_var( 'tags' ) ) ? 1 : 0,
				'ingredients'     => ( 'on' === $this->_var( 'ingredients' ) ) ? 1 : 0,
				'link_item'       => ( 'on' === $this->_var( 'link_item' ) ) ? 1 : 0,
				'desc_length'     => $this->_var( 'desc_length' ),
			)
		);

		$output = $this->wrap_clean( $content, array(), $function_name );

		return $output;
	}
}

new Tm_Builder_Mprm_Items;
