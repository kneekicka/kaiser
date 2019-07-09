<?php
class Tm_Builder_Module_Video_Slider extends Tm_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Video Slider', 'tm_builder' );
		$this->slug            = 'tm_pb_video_slider';
		$this->child_slug      = 'tm_pb_video_slider_item';
		$this->child_item_text = esc_html__( 'Video', 'tm_builder' );
		$this->icon = 'f008';

		$this->whitelisted_fields = array(
			'show_image_overlay',
			'show_arrows',
			'show_thumbnails',
			'admin_label',
			'module_id',
			'module_class',
			'play_icon_color',
			'thumbnail_overlay_color',
		);

		$this->fields_defaults = array(
			'show_image_overlay' => array( 'hide' ),
			'show_arrows'        => array( 'on' ),
			'show_thumbnails'    => array( 'on' ),
		);

		$this->custom_css_options = array(
			'play_button' => array(
				'label'    => esc_html__( 'Play Button', 'tm_builder' ),
				'selector' => '.tm_pb_video_play',
			),
			'thumbnail_item' => array(
				'label'    => esc_html__( 'Thumbnail Item', 'tm_builder' ),
				'selector' => '.tm_pb_carousel_item',
			),
			'arrows' => array(
				'label'    => esc_html__( 'Slider Arrows', 'tm_builder' ),
				'selector' => '.et-pb-slider-arrows a',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'show_image_overlay' => array(
				'label'           => esc_html__( 'Display Image Overlays on Main Video', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'hide' => esc_html__( 'Hide', 'tm_builder' ),
					'show' => esc_html__( 'Show', 'tm_builder' ),
				),
				'description'        => esc_html__( 'This option will cover the player UI on the main video. This image can either be uploaded in each video setting or auto-generated by Divi.', 'tm_builder' ),
			),
			'show_arrows' => array(
				'label'           => esc_html__( 'Arrows', 'tm_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Show Arrows', 'tm_builder' ),
					'off' => esc_html__( 'Hide Arrows', 'tm_builder' ),
				),
				'description'        => esc_html__( 'This setting will turn on and off the navigation arrows.', 'tm_builder' ),
			),
			'show_thumbnails' => array(
				'label'             => esc_html__( 'Slider Controls', 'tm_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Use Thumbnail Track', 'tm_builder' ),
					'off' => esc_html__( 'Use Dot Navigation', 'tm_builder' ),
				),
				'description'        => esc_html__( 'This setting will let you choose to use the thumbnail track controls below the slider or dot navigation at the bottom of the slider.', 'tm_builder' ),
			),
			'play_icon_color' => array(
				'label'             => esc_html__( 'Play Icon Color', 'tm_builder' ),
				'type'              => 'color',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
			),
			'thumbnail_overlay_color' => array(
				'label'             => esc_html__( 'Thumbnail Overlay Color', 'tm_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'tm_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => tm_pb_media_breakpoints(),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'tm_builder' ),
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'tm_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'tm_builder' ),
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'tm_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'tm_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'tm_pb_custom_css_regular',
			),
		);
		return $fields;
	}

	function pre_shortcode_content() {
		global $tm_pb_slider_image_overlay;

		$show_image_overlay = $this->shortcode_atts['show_image_overlay'];

		$tm_pb_slider_image_overlay = $show_image_overlay;

	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id          = $this->shortcode_atts['module_id'];
		$module_class       = $this->shortcode_atts['module_class'];
		$show_arrows        = $this->shortcode_atts['show_arrows'];
		$show_thumbnails    = $this->shortcode_atts['show_thumbnails'];
		$play_icon_color = $this->shortcode_atts['play_icon_color'];
		$thumbnail_overlay_color = $this->shortcode_atts['thumbnail_overlay_color'];

		global $tm_pb_slider_image_overlay;

		$module_class = TM_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( '' !== $play_icon_color ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_video_play, %%order_class%% .tm_pb_carousel .tm_pb_video_play',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $play_icon_color )
				),
			) );
		}

		if ( '' !== $thumbnail_overlay_color ) { TM_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .tm_pb_carousel_item .tm_pb_video_overlay_hover:hover, %%order_class%%.tm_pb_video_slider .tm_pb_slider:hover .tm_pb_video_overlay_hover, %%order_class%% .tm_pb_carousel_item.et-pb-active-control .tm_pb_video_overlay_hover',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $thumbnail_overlay_color )
				),
			) );
		}

		$class  = '';
		$class .= 'off' === $show_arrows ? ' tm_pb_slider_no_arrows' : '';
		$class .= 'on' === $show_thumbnails ? ' tm_pb_slider_carousel tm_pb_slider_no_pagination' : '';
		$class .= 'off' === $show_thumbnails ? ' tm_pb_slider_dots' : '';
		$class .= " tm_pb_controls_light";

		$content = $this->shortcode_content;

		$output = sprintf(
			'<div%3$s class="tm_pb_module tm_pb_video_slider%4$s">
				<div class="tm_pb_slider tm_pb_preload%1$s">
					<div class="tm_pb_slides">
						%2$s
					</div> <!-- .tm_pb_slides -->
				</div> <!-- .tm_pb_slider -->
			</div> <!-- .tm_pb_video_slider -->
			',
			esc_attr( $class ),
			$content,
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
		);

		return $output;
	}
}
new Tm_Builder_Module_Video_Slider;

