<?php
class Tm_Builder_Cherry_Projects_Terms extends Tm_Builder_Module {

	function init() {
		$this->name = esc_html__( 'Projects Terms', 'tm-builder-integrator' );
		$this->icon = 'f288';
		$this->slug = 'tm_pb_cherry_projects_terms';
		$this->main_css_element = '%%order_class%%.' . $this->slug;

		$this->whitelisted_fields = array(
			'term_type',
			'listing_layout',
			'load_animation',
			'column_number',
			'post_per_page',
			'item_margin',
			'grid_template',
			'masonry_template',
			'list_template',
		);

		$this->fields_defaults = array(
			'term_type'        => array( 'category' ),
			'listing_layout'   => array( 'masonry-layout' ),
			'load_animation'   => array( 'loading-animation-fade' ),
			'column_number'    => array( 3 ),
			'post_per_page'    => array( 6 ),
			'item_margin'      => array( 10 ),
			'grid_template'    => array( 'terms-grid-default.tmpl' ),
			'masonry_template' => array( 'terms-masonry-default.tmpl' ),
			'list_template'    => array( 'terms-list-default.tmpl' ),
		);
	}


	function get_fields() {

		$fields = array(
			'term_type' => array(
				'label'           => esc_html__( 'Term type', 'tm-builder-integrator' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'category' => esc_html__( 'Category', 'tm-builder-integrator' ),
					'tag'      => esc_html__( 'Tag', 'tm-builder-integrator' ),
				),
				'description'     => esc_html__( 'Choose term type', 'tm-builder-integrator' ),
			),
			'listing_layout' => array(
				'label'           => esc_html__( 'Projects listing layout', 'tm-builder-integrator' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'grid-layout'           => esc_html__( 'Grid', 'tm-builder-integrator' ),
					'masonry-layout'        => esc_html__( 'Masonry', 'tm-builder-integrator' ),
					'list-layout'           => esc_html__( 'List', 'tm-builder-integrator' ),
				),
				'description'     => esc_html__( 'Choose terms listing view layout.', 'tm-builder-integrator' ),
			),
			'load_animation' => array(
				'label'           => esc_html__( 'Loading animation', 'tm-builder-integrator' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'loading-animation-fade'             => esc_html__( 'Fade animation', 'tm-builder-integrator' ),
					'loading-animation-scale'            => esc_html__( 'Scale animation', 'tm-builder-integrator' ),
					'loading-animation-move-up'          => esc_html__( 'Move Up animation', 'tm-builder-integrator' ),
					'loading-animation-flip'             => esc_html__( 'Flip animation', 'tm-builder-integrator' ),
					'loading-animation-helix'            => esc_html__( 'Helix animation', 'tm-builder-integrator' ),
					'loading-animation-fall-perspective' => esc_html__( 'Fall perspective animation', 'tm-builder-integrator' ),
				),
				'description'     => esc_html__( 'Choose terms loading animation.', 'tm-builder-integrator' ),
			),
			'column_number' => array(
				'label'           => esc_html__( 'Columns', 'tm-builder-integrator' ),
				'type'            => 'range',
				'option_category' => 'basic_option',
				'default'         => '3',
				'range_settings' => array(
					'min'  => 2,
					'max'  => 10,
					'step' => 1,
				),
				'mobile_options'      => false,
				'mobile_global'       => false,
			),
			'post_per_page' => array(
				'label'           => esc_html__( 'Posts per page', 'tm-builder-integrator' ),
				'type'            => 'range',
				'option_category' => 'basic_option',
				'default'         => '6',
				'range_settings' => array(
					'min'  => 1,
					'max'  => 50,
					'step' => 1,
				),
				'mobile_options'      => false,
				'mobile_global'       => false,
			),
			'item_margin' => array(
				'label'           => esc_html__( 'Item margin', 'tm-builder-integrator' ),
				'type'            => 'range',
				'option_category' => 'basic_option',
				'default'         => '10',
				'range_settings' => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
				'mobile_options'      => false,
				'mobile_global'       => false,
				'description'     => esc_html__( 'Select projects item margin (outer indent) value.', 'tm-builder-integrator' ),
			),
			'grid_template' => array(
				'label'           => esc_html__( 'Grid template', 'tm-builder-integrator' ),
				'option_category' => 'basic_option',
				'type'            => 'text',
				'default'         => 'terms-grid-default.tmpl',
				'value'           => 'terms-grid-default.tmpl',
			),
			'masonry_template' => array(
				'label'           => esc_html__( 'Masonry template', 'tm-builder-integrator' ),
				'option_category' => 'basic_option',
				'type'            => 'text',
				'default'         => 'terms-masonry-default.tmpl',
				'value'           => 'terms-masonry-default.tmpl',
			),
			'list_template' => array(
				'label'           => esc_html__( 'List template', 'tm-builder-integrator' ),
				'option_category' => 'basic_option',
				'type'            => 'text',
				'default'         => 'terms-list-default.tmpl',
				'value'           => 'terms-list-default.tmpl',
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {

		$this->set_vars(
			array(
				'term_type',
				'listing_layout',
				'load_animation',
				'column_number',
				'post_per_page',
				'item_margin',
				'grid_template',
				'masonry_template',
				'list_template',
			)
		);

		$callback = tm_builder_integrator()->get_shortcode_cb( 'cherry-projects', 'cherry_projects_terms' );

		if ( ! is_callable( $callback ) ) {
			return;
		}

		$content = call_user_func(
			$callback,
			array(
				'term_type'         => $this->_var( 'term_type' ),
				'listing_layout'    => $this->_var( 'listing_layout' ),
				'loading_animation' => $this->_var( 'load_animation' ),
				'column_number'     => $this->_var( 'column_number' ),
				'post_per_page'     => $this->_var( 'post_per_page' ),
				'item_margin'       => $this->_var( 'item_margin' ),
				'grid_template'     => $this->_var( 'grid_template' ),
				'masonry_template'  => $this->_var( 'masonry_template' ),
				'list_template'     => $this->_var( 'list_template' ),
			)
		);

		$output = $this->wrap_clean( $content, array(), $function_name );

		return $output;
	}
}

new Tm_Builder_Cherry_Projects_Terms;
