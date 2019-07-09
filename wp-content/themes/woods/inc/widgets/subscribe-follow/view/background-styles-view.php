<?php

$background_options = array(
	'background_image'        => '',
	'background_position'     => 'center',
	'background_repeat'       => 'no-repeat',
	'background_attachment'   => '',
	'background_size'         => '',
	'background_color'        => '',
	'invert_text_colorscheme' => '',
);

$output = array();

foreach( $background_options as $property => $default_value ) {

	$value = $default_value;

	if ( empty( $instance[ $property ] ) ) {
		continue;
	}

	switch( $property ) {
		case 'background_position':
			$value = str_replace( '-', ' ', $instance[ $property ] );
		break;

		case 'background_image':
			$url   = wp_get_attachment_image_url( $instance[ $property ], 'full' );
			$value = ( ! empty( $url ) ) ? sprintf( 'url("%s")', esc_url( $url ) ) : false;
		break;

		case 'invert_text_colorscheme':
			if ( is_array( $instance[ $property ] ) && isset( $instance[ $property ][ $property ] ) ) {

				$type  = $instance[ $property ][ $property ] === 'true' ? 'invert' : 'regular';
				$value = get_theme_mod( sprintf( '%s_text_color', $type ) );

				// Add widget header color css rules
				if ( ! empty( $value ) ) {
					woods_theme()->dynamic_css->add_style(
						$this->add_selector( '.widget-title' ),
						array( 'color' => esc_html( $value ) )
					);
				}
			}
			$property = 'color';
		break;

		default:
			$value = esc_attr( $instance[ $property ] );
		break;
	}

	if ( ! empty( $value ) ) {
		$output[ str_replace( '_', '-', esc_attr( $property ) ) ] = $value;
	}
}

// Remove background options if no background image is set
if ( ! isset( $output['background-image'] ) ) {
	unset( $output['background-position'] );
	unset( $output['background-repeat'] );
	unset( $output['background-size'] );
}

woods_theme()->dynamic_css->add_style(
	$this->add_selector(),
	$output
);
