<?php
class Tm_Builder_Cherry_Services extends Tm_Builder_Module {

	function init() {

		$this->name = esc_html__( 'Services', 'tm-builder-integrator' );
		$this->icon = 'f0eb';
		$this->slug = 'tm_pb_cherry_services';
		$this->main_css_element = '%%order_class%%.' . $this->slug;

		$this->whitelisted_fields = array(
			'super_title',
			'title',
			'subtitle',
			'show_filters',
			'columns',
			'columns_tablet',
			'columns_phone',
			'posts_per_page',
			'category',
			'id',
			'excerpt_length',
			'more',
			'more_text',
			'more_url',
			'ajax_more',
			'pagination',
			'show_title',
			'show_media',
			'show_content',
			'show_position',
			'show_social',
			'image_size',
			'template',
			'use_space',
			'use_rows_space',
			'module_id',
			'module_class',
		);

		$this->fields_defaults = array(
			'columns'        => array( '4' ),
			'columns_tablet' => array( '2' ),
			'columns_phone'  => array( '1' ),
			'posts_per_page' => array( '4' ),
			'excerpt_length' => array( '25' ),
			'more'           => array( 'on' ),
			'ajax_more'      => array( 'on' ),
			'more_text'      => array( __( 'More', 'tm-builder-integrator' ) ),
			'pagination'     => array( 'off' ),
			'show_filters'   => array( 'off' ),
			'show_title'     => array( 'on' ),
			'show_media'     => array( 'on' ),
			'show_content'   => array( 'on' ),
			'image_size'     => array( 'thumbnail' ),
			'template'       => array( 'default' ),
			'use_space'      => array( 'on' ),
			'use_rows_space' => array( 'on' ),
		);
	}

	function get_fields() {

		if ( function_exists( 'cherry_services_tools' ) ) {
			$templates = cherry_services_tools()->get_templates();
		} else {
			$templates = array();
		}

		$fields = array(
			'columns' => array(
				'label'           => esc_html__( 'Columns', 'tm-builder-integrator' ),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'default'         => 4,
				'options'         => array(
					1 => 1,
					2 => 2,
					3 => 3,
					4 => 4,
					6 => 6,
				),
			),
			'category' => array(
				'label'					=> esc_html__( 'Include categories', 'tm-builder-integrator' ),
				'option_category'		=> 'basic_option',
				'renderer'				=> 'tm_builder_include_categories_option',
				'renderer_options'		=> array(
					'use_terms'  => true,
					'term_name'  => cherry_services_list()->tax( 'category' ),
					'input_name' => 'tm_pb_category',
				),
				'description'			=> esc_html__( 'Choose which categories you would like to include.', 'tm-builder-integrator' ),
			),
			'id' => array(
				'label'           => esc_html__( 'Include post id', 'tm-builder-integrator' ),
				'option_category' => 'basic_option',
				'type'            => 'text',
				'description'     => esc_html__( 'Enter post id you would like to include. The separator gap. Example: 256 472 23 6', 'tm-builder-integrator' ),
			),
			'posts_per_page' => array(
				'label'               => esc_html__( 'Posts count ( Set 0 to show all ) ', 'tm-builder-integrator' ),
				'option_category'     => 'basic_option',
				'type'                => 'range',
				'range_settings'      => array(
					'min'  => 0,
					'max'  => 30,
					'step' => 1,
				),
				'default'             => '4',
			),
			'super_title' => array(
				'label'           => esc_html__( 'Super Title', 'tm-builder-integrator' ),
				'type'            => 'text',
				'option_category' => 'configuration',
			),
			'title' => array(
				'label'           => esc_html__( 'Title', 'tm-builder-integrator' ),
				'type'            => 'text',
				'option_category' => 'configuration',
			),
			'subtitle' => array(
				'label'           => esc_html__( 'Sub Title', 'tm-builder-integrator' ),
				'type'            => 'text',
				'option_category' => 'configuration',
			),
			'show_filters' => array(
				'label'             => esc_html__( 'Show filter by categorys', 'tm-builder-integrator' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'tm-builder-integrator' ),
					'off' => esc_html__( 'No', 'tm-builder-integrator' ),
				),
			),
			'more' => array(
				'label'           => esc_html__( 'Display more button', 'tm-builder-integrator' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm-builder-integrator' ),
					'off' => esc_html__( 'No', 'tm-builder-integrator' ),
				),
				'affects'         => array(
					'#tm_pb_more_text',
					'#tm_pb_ajax_more',
					'#tm_pb_pagination',
				),
			),
			'more_text' => array(
				'label'           => esc_html__( 'More button text', 'tm-builder-integrator' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'depends_show_if' => 'on',
			),
			'ajax_more' => array(
				'label'   => esc_html__( 'AJAX load more', 'tm-builder-integrator' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm-builder-integrator' ),
					'off' => esc_html__( 'No', 'tm-builder-integrator' ),
				),
				'affects'         => array(
					'#tm_pb_more_url',
				),
			),
			'more_url' => array(
				'label'           => esc_html__( 'More button URL', 'tm-builder-integrator' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'depends_show_if' => 'off',
			),
			'pagination' => array(
				'label'           => esc_html__( 'Show paging navigation', 'tm-builder-integrator' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'tm-builder-integrator' ),
					'on'  => esc_html__( 'Yes', 'tm-builder-integrator' ),
				),
				'depends_show_if' => 'off',
				'description'     => esc_html__( 'Will be ignored if More button shown.', 'tm-builder-integrator' ),
			),
			'image_size' => array(
				'label'           => esc_html__( 'Featured Image Size', 'tm-builder-integrator' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => tm_builder_tools()->get_image_sizes(),
				'description'     => esc_html__( 'Select featured thumbnail size.', 'tm-builder-integrator' ),
			),
			'show_title' => array(
				'label'   => esc_html__( 'Show title', 'tm-builder-integrator' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm-builder-integrator' ),
					'off' => esc_html__( 'No', 'tm-builder-integrator' ),
				),
			),
			'show_media' => array(
				'label'   => esc_html__( 'Show featured media (image or icon)', 'tm-builder-integrator' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm-builder-integrator' ),
					'off' => esc_html__( 'No', 'tm-builder-integrator' ),
				),
			),
			'show_content' => array(
				'label'   => esc_html__( 'Show description', 'tm-builder-integrator' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'tm-builder-integrator' ),
					'off' => esc_html__( 'No', 'tm-builder-integrator' ),
				),
				'affects'         => array(
					'#tm_pb_excerpt',
				),
			),
			'excerpt_length' => array(
				'label'           => esc_html__( 'Description length', 'tm-builder-integrator' ),
				'type'            => 'range',
				'option_category' => 'basic_option',
				'default'         => '25',
				'range_settings' => array(
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				),
				'description'     => esc_html__( 'Set words number in excerpt (set 0 to hide excerpt)', 'tm-builder-integrator' ),
			),
			'template' => array(
				'label'   => esc_html__( 'Layout', 'tm-builder-integrator' ),
				'type'    => 'select',
				'options' => $templates,
			),
			'use_space' => array(
				'label'             => esc_html__( 'Use gutter between columns', 'tm-builder-integrator' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'tm-builder-integrator' ),
					'off' => esc_html__( 'No', 'tm-builder-integrator' ),
				),
			),
			'use_rows_space' => array(
				'label'             => esc_html__( 'Use gutter between rows', 'tm-builder-integrator' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'tm-builder-integrator' ),
					'off' => esc_html__( 'No', 'tm-builder-integrator' ),
				),
			),
			'columns_tablet' => array(
				'type' => 'skip',
			),
			'columns_phone' => array(
				'type' => 'skip',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'tm-builder-integrator' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'tm-builder-integrator' ),
					'tablet'  => esc_html__( 'Tablet', 'tm-builder-integrator' ),
					'desktop' => esc_html__( 'Desktop', 'tm-builder-integrator' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'tm-builder-integrator' ),
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
				'super_title',
				'title',
				'subtitle',
				'show_filters',
				'columns',
				'columns_tablet',
				'columns_phone',
				'posts_per_page',
				'category',
				'id',
				'excerpt_length',
				'more',
				'more_text',
				'more_url',
				'ajax_more',
				'pagination',
				'show_title',
				'show_media',
				'show_content',
				'show_position',
				'show_social',
				'image_size',
				'template',
				'use_space',
				'use_rows_space',
			)
		);

		$callback = tm_builder_integrator()->get_shortcode_cb( 'cherry-services-list', 'cherry_services' );

		if ( ! is_callable( $callback ) ) {
			return;
		}

		$cats = $this->_var( 'category' );

		if ( $cats ) {
			$cats_array = explode( ',', $cats );
		}

		if ( ! empty( $cats_array ) ) {

			$cats = array();
			foreach ( $cats_array as $category ) {
				$category_term = get_term_by( 'id', $category, cherry_services_list()->tax( 'category' ) );
				$cats[]        = $category_term->slug;
			}
			$cats = implode( ',', $cats );
		}

		$content = call_user_func(
			$callback,
			array(
				'super_title'    => $this->_var( 'super_title' ),
				'title'          => $this->_var( 'title' ),
				'subtitle'       => $this->_var( 'subtitle' ),
				'show_filters'   => $this->_var( 'show_filters' ),
				'columns'        => $this->_var( 'columns' ),
				'columns_tablet' => $this->_var( 'columns_tablet' ),
				'columns_phone'  => $this->_var( 'columns_phone' ),
				'posts_per_page' => $this->_var( 'posts_per_page' ),
				'category'       => $cats,
				'id'             => $this->_var( 'id' ),
				'excerpt_length' => $this->_var( 'excerpt_length' ),
				'more'           => $this->_var( 'more' ),
				'more_text'      => $this->_var( 'more_text' ),
				'more_url'       => $this->_var( 'more_url' ),
				'ajax_more'      => $this->_var( 'ajax_more' ),
				'pagination'     => $this->_var( 'pagination' ),
				'show_title'     => $this->_var( 'show_title' ),
				'show_media'     => $this->_var( 'show_media' ),
				'show_content'   => $this->_var( 'show_content' ),
				'show_position'  => $this->_var( 'show_position' ),
				'show_social'    => $this->_var( 'show_social' ),
				'image_size'     => $this->_var( 'image_size' ),
				'template'       => $this->_var( 'template' ),
				'use_space'      => $this->_var( 'use_space' ),
				'use_rows_space' => $this->_var( 'use_rows_space' ),
			)
		);

		$output = $this->wrap_clean( $content, array(), $function_name );

		return $output;
	}
}

new Tm_Builder_Cherry_Services;
