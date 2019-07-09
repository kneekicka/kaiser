<?php
class Tm_Builder_Cherry_Projects extends Tm_Builder_Module {

	function init() {
		$this->name = esc_html__( 'Projects', 'tm-builder-integrator' );
		$this->icon = 'f288';
		$this->slug = 'tm_pb_cherry_projects';
		$this->main_css_element = '%%order_class%%.' . $this->slug;

		$this->whitelisted_fields = array(
			'listing_layout',
			'loading_mode',
			'load_animation',
			'hover_animation',
			'filter_visible',
			'filter_type',
			'category_list',
			'tags_list',
			'order_filter_visible',
			'order_filter_default_value',
			'posts_format',
			'single_term',
			'column_number',
			'post_per_page',
			'item_margin',
			'justified_fixed_height',
			'masonry_template',
			'grid_template',
			'justified_template',
			'cascading_grid_template',
			'list_template',
		);

		$this->fields_defaults = array(
			'listing_layout'             => array( 'masonry-layout' ),
			'loading_mode'               => array( 'ajax-pagination-mode' ),
			'load_animation'             => array( 'loading-animation-fade' ),
			'hover_animation'            => array( 'simple-fade' ),
			'filter_visible'             => array( 'true' ),
			'filter_type'                => array( 'category' ),
			'order_filter_visible'       => array( 'desc' ),
			'order_filter_default_value' => array( 'date' ),
			'posts_format'               => array( 'post-format-all' ),
			'single_term'                => array( '' ),
			'column_number'              => array( 3 ),
			'post_per_page'              => array( 9 ),
			'item_margin'                => array( 10 ),
			'justified_fixed_height'     => array( 300 ),
			'masonry_template'           => array( 'masonry-default.tmpl' ),
			'grid_template'              => array( 'grid-default.tmpl' ),
			'justified_template'         => array( 'justified-default.tmpl' ),
			'cascading_grid_template'    => array( 'cascading-grid-default.tmpl' ),
			'list_template'              => array( 'list-default.tmpl' ),
		);
	}


	function get_fields() {

		$fields = array(
			'listing_layout' => array(
				'label'           => esc_html__( 'Projects listing layout', 'tm-builder-integrator' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'grid-layout'           => esc_html__( 'Grid', 'tm-builder-integrator' ),
					'masonry-layout'        => esc_html__( 'Masonry', 'tm-builder-integrator' ),
					'justified-layout'      => esc_html__( 'Justified', 'tm-builder-integrator' ),
					'cascading-grid-layout' => esc_html__( 'Cascading grid', 'tm-builder-integrator' ),
					'list-layout'           => esc_html__( 'List', 'tm-builder-integrator' ),
				),
				'description'     => esc_html__( 'Choose projects listing view layout.', 'tm-builder-integrator' ),
			),
			'loading_mode' => array(
				'label'           => esc_html__( 'Pagination mode', 'tm-builder-integrator' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'ajax-pagination-mode' => esc_html__( 'Ajax pagination', 'tm-builder-integrator' ),
					'more-button-mode'     => esc_html__( 'More button', 'tm-builder-integrator' ),
					'lazy-loading-mode'    => esc_html__( 'Lazy loading', 'tm-builder-integrator' ),
					'none-mode'            => esc_html__( 'None', 'tm-builder-integrator' ),
				),
				'description'     => esc_html__( 'Choose projects pagination mode.', 'tm-builder-integrator' ),
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
				'description'     => esc_html__( 'Choose posts loading animation.', 'tm-builder-integrator' ),
			),
			'hover_animation' => array(
				'label'           => esc_html__( 'Hover animation', 'tm-builder-integrator' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'simple-fade'  => esc_html__( 'Fade', 'tm-builder-integrator' ),
					'simple-scale' => esc_html__( 'Scale', 'tm-builder-integrator' ),
					'custom'       => esc_html__( 'Custom', 'tm-builder-integrator' ),
				),
				'description'     => esc_html__( 'Choose posts images hover animation.', 'tm-builder-integrator' ),
			),
			'filter_visible' => array(
				'label'             => esc_html__( 'Filters', 'tm-builder-integrator' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Show', 'tm-builder-integrator' ),
					'off' => esc_html__( 'Hide', 'tm-builder-integrator' ),
				),
			),
			'filter_type' => array(
				'label'           => esc_html__( 'Filter type', 'tm-builder-integrator' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'category' => esc_html__( 'Category', 'tm-builder-integrator' ),
					'tag'      => esc_html__( 'Tag', 'tm-builder-integrator' ),
				),
				'description'     => esc_html__( 'Select if you want to filter posts by tag or by category.', 'tm-builder-integrator' ),
			),
			'category_list' => array(
				'label'					=> esc_html__( 'Projects filter categories list', 'tm-builder-integrator' ),
				'option_category'		=> 'basic_option',
				'renderer'				=> 'tm_builder_include_categories_option',
				'renderer_options'		=> array(
					'use_terms'  => true,
					'term_name'  => 'projects_category',
					'input_name' => 'tm_pb_category_list',
				),
				'description'			=> esc_html__( 'Choose which categories you would like to include.', 'tm-builder-integrator' ),
			),
			'tags_list' => array(
				'label'					=> esc_html__( 'Projects filter tags list', 'tm-builder-integrator' ),
				'option_category'		=> 'basic_option',
				'renderer'				=> 'tm_builder_include_categories_option',
				'renderer_options'		=> array(
					'use_terms'  => true,
					'term_name'  => 'projects_tag',
					'input_name' => 'tm_pb_tags_list',
				),
				'description'			=> esc_html__( 'Choose which tags you would like to include.', 'tm-builder-integrator' ),
			),
			'order_filter_visible' => array(
				'label'             => esc_html__( 'Order filters', 'tm-builder-integrator' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Show', 'tm-builder-integrator' ),
					'off' => esc_html__( 'Hide', 'tm-builder-integrator' ),
				),
				'description'     => esc_html__( 'Enable/disable order filters.', 'tm-builder-integrator' ),
			),
			'order_filter_default_value' => array(
				'label'           => esc_html__( 'Order filter default value', 'tm-builder-integrator' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'desc' => esc_html__( 'DESC', 'tm-builder-integrator' ),
					'asc'  => esc_html__( 'ASC', 'tm-builder-integrator' ),
				),
			),
			'orderby_filter_default_value' => array(
				'label'           => esc_html__( 'Order by filter default value', 'tm-builder-integrator' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'date'          => esc_html__( 'Date', 'tm-builder-integrator' ),
					'name'          => esc_html__( 'Name', 'tm-builder-integrator' ),
					'modified'      => esc_html__( 'Modified', 'tm-builder-integrator' ),
					'comment_count' => esc_html__( 'Comments', 'tm-builder-integrator' ),
				),
			),
			'posts_format' => array(
				'label'           => esc_html__( 'Post Format', 'tm-builder-integrator' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'post-format-all'      => esc_html__( 'All formats', 'tm-builder-integrator' ),
					'post-format-standard' => esc_html__( 'Standard', 'tm-builder-integrator' ),
					'post-format-image'    => esc_html__( 'Image', 'tm-builder-integrator' ),
					'post-format-gallery'  => esc_html__( 'Gallery', 'tm-builder-integrator' ),
					'post-format-audio'    => esc_html__( 'Audio', 'tm-builder-integrator' ),
					'post-format-video'    => esc_html__( 'Video', 'tm-builder-integrator' ),
				),
			),
			'single_term' => array(
				'label'           => esc_html__( 'Single term slug', 'tm-builder-integrator' ),
				'option_category' => 'basic_option',
				'type'            => 'text',
				'default'         => '',
				'value'           => '',
			),
			'column_number' => array(
				'label'           => esc_html__( 'Columns', 'tm-builder-integrator' ),
				'type'            => 'range',
				'option_category' => 'basic_option',
				'default'         => '3',
				'range_settings' => array(
					'min'  => 2,
					'max'  => 6,
					'step' => 1,
				),
				'mobile_options'      => false,
				'mobile_global'       => false,
			),
			'post_per_page' => array(
				'label'           => esc_html__( 'Posts per page', 'tm-builder-integrator' ),
				'type'            => 'range',
				'option_category' => 'basic_option',
				'default'         => '9',
				'range_settings' => array(
					'min'  => -1,
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
			'justified_fixed_height' => array(
				'label'           => esc_html__( 'Justified fixed height', 'tm-builder-integrator' ),
				'type'            => 'range',
				'option_category' => 'basic_option',
				'default'         => '300',
				'range_settings' => array(
					'min'  => 50,
					'max'  => 1000,
					'step' => 1,
				),
				'mobile_options'      => false,
				'mobile_global'       => false,
				'description'     => esc_html__( 'Select projects item justified height value.', 'tm-builder-integrator' ),
			),
			'grid_template' => array(
				'label'           => esc_html__( 'Grid template', 'tm-builder-integrator' ),
				'option_category' => 'basic_option',
				'type'            => 'text',
				'default'         => 'grid-default.tmpl',
				'value'           => 'grid-default.tmpl',
			),
			'masonry_template' => array(
				'label'           => esc_html__( 'Masonry template', 'tm-builder-integrator' ),
				'option_category' => 'basic_option',
				'type'            => 'text',
				'default'         => 'masonry-default.tmpl',
				'value'           => 'masonry-default.tmpl',
			),
			'justified_template' => array(
				'label'           => esc_html__( 'Justified template', 'tm-builder-integrator' ),
				'option_category' => 'basic_option',
				'type'            => 'text',
				'default'         => 'justified-default.tmpl',
				'value'           => 'justified-default.tmpl',
			),
			'cascading_grid_template' => array(
				'label'           => esc_html__( 'Cascading grid template', 'tm-builder-integrator' ),
				'option_category' => 'basic_option',
				'type'            => 'text',
				'default'         => 'cascading-grid-default.tmpl',
				'value'           => 'cascading-grid-default.tmpl',
			),
			'list_template' => array(
				'label'           => esc_html__( 'List template', 'tm-builder-integrator' ),
				'option_category' => 'basic_option',
				'type'            => 'text',
				'default'         => 'list-default.tmpl',
				'value'           => 'list-default.tmpl',
			),
		);
		return $fields;
	}

	/**
	 * Get terms array
	 *
	 * @param  string $id_str Id set string
	 * @return array
	 */
	public function get_terms_array( $id_str = '', $term_type = '' ) {
		if ( $id_str ) {
			$id_array = explode( ',', $id_str );
		}

		$terms_array = array();
		if ( ! empty( $id_str ) ) {
			foreach ( $id_array as $id ) {
				$term = get_term_by( 'id', $id, $term_type );
				$terms_array[] = $term->slug;
			}
		}
		return $terms_array;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {

		$this->set_vars(
			array(
				'listing_layout',
				'loading_mode',
				'load_animation',
				'hover_animation',
				'filter_visible',
				'filter_type',
				'category_list',
				'tags_list',
				'order_filter_visible',
				'order_filter_default_value',
				'posts_format',
				'column_number',
				'post_per_page',
				'item_margin',
				'justified_fixed_height',
				'masonry_template',
				'grid_template',
				'justified_template',
				'cascading_grid_template',
				'list_template',
			)
		);

		$callback = tm_builder_integrator()->get_shortcode_cb( 'cherry-projects', 'cherry_projects' );

		if ( ! is_callable( $callback ) ) {
			return;
		}

		$category_array = $this->get_terms_array( $this->_var( 'category_list' ), 'projects_category' );
		$tag_array = $this->get_terms_array( $this->_var( 'tags_list' ), 'projects_tag' );

		$content = call_user_func(
			$callback,
			array(
				'listing_layout'             => $this->_var( 'listing_layout' ),
				'loading_mode'               => $this->_var( 'loading_mode' ),
				'loading_animation'          => $this->_var( 'load_animation' ),
				'hover_animation'            => $this->_var( 'hover_animation' ),
				'filter_visible'             => filter_var( $this->_var( 'filter_visible' ), FILTER_VALIDATE_BOOLEAN ),
				'filter_type'                => $this->_var( 'filter_type' ),
				'category_list'              => $category_array,
				'tags_list'                  => $tag_array,
				'order_filter_visible'       => filter_var( $this->_var( 'order_filter_visible' ), FILTER_VALIDATE_BOOLEAN ),
				'order_filter_default_value' => $this->_var( 'order_filter_default_value' ),
				'posts_format'               => $this->_var( 'posts_format' ),
				'column_number'              => $this->_var( 'column_number' ),
				'post_per_page'              => $this->_var( 'post_per_page' ),
				'item_margin'                => $this->_var( 'item_margin' ),
				'justified_fixed_height'     => $this->_var( 'justified_fixed_height' ),
				'masonry_template'           => $this->_var( 'masonry_template' ),
				'grid_template'              => $this->_var( 'grid_template' ),
				'justified_template'         => $this->_var( 'justified_template' ),
				'cascading_grid_template'    => $this->_var( 'cascading_grid_template' ),
				'list_template'              => $this->_var( 'list_template' ),
			)
		);

		$output = $this->wrap_clean( $content, array(), $function_name );

		return $output;
	}
}

new Tm_Builder_Cherry_Projects;
