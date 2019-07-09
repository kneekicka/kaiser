<?php
/**
 * Register required plugins for TGM Plugin Activator
 *
 * @package Woods
 */
add_action( 'tgmpa_register', 'woods_register_required_plugins' );
/**
 * Register the required plugins for this theme.
 *
 * In this example, we register five plugins:
 * - one included with the TGMPA library
 * - two from an external source, one from an arbitrary source, one from a GitHub repository
 * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function woods_register_required_plugins() {
	/*
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(
		array(
			'name'     => esc_html__( 'Contact Form 7', 'woods' ),
			'slug'     => 'contact-form-7',
		),
		array(
		 'name'     => esc_html__( 'Cherry Sidebars', 'woods' ),
		 'slug'     => 'cherry-sidebars',
		),
		array(
			'name'     => esc_html__( 'Cherry Ld Mods Switcher', 'woods' ),
			'slug'      => 'cherry-ld-mods-switcher',
			'source' => WOODS_THEME_DIR . '/assets/plugins/cherry-ld-mods-switcher.zip',
		),
		array(
			'name'     => esc_html__( 'Power Builder', 'woods' ),
			'slug'      => 'tm-content-builder',
			'source' => WOODS_THEME_DIR . '/assets/plugins/tm-content-builder.zip',
		),
		array(
			'name'     => esc_html__( 'Power Builder Integrator', 'woods' ),
			'slug'      => 'tm-builder-integrator',
			'source' => WOODS_THEME_DIR . '/assets/plugins/tm-builder-integrator.zip',
		),
		array(
			'name'     => esc_html__( 'Hotel Booking', 'woods' ),
			'slug'      => 'hotel-booking',
			'source' => WOODS_THEME_DIR . '/assets/plugins/motopress-booking.zip',
		),
		array(
			'name'     => esc_html__( 'Restaurant Menu', 'woods' ),
			'slug'      => 'mp-restaurant-menu',
			'source' => WOODS_THEME_DIR . '/assets/plugins/mp-restaurant-menu.zip',
		),
		array(
			'name'         => esc_html__( 'Simple File Downloader', 'woods' ),
			'slug'         => 'simple-file-downloader',
			'required'  => false,
		),
		array(
			'name'     => esc_html__( 'TM Photo Gallery', 'woods' ),
			'slug'      => 'tm-photo-gallery',
			'required'  => false,
		),
		array(
			'name'         => esc_html__( 'Cherry Search', 'woods' ),
			'slug'         => 'cherry-search',
			'required'  => false,
		),
		array(
			'name'      => 'Cherry Data Importer',
			'slug'      => 'cherry-data-importer',
			'required'  => false,
			'source' => WOODS_THEME_DIR . '/assets/plugins/cherry-data-importer.zip',
		),
	);

	/*
	 * Array of configuration settings. Amend each line as needed.
	 *
	 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
	 * strings available, please help us make TGMPA even better by giving us access to these translations or by
	 * sending in a pull-request with .po file(s) with the translations.
	 *
	 * Only uncomment the strings in the config array if you want to customize the strings.
	 */
	$config = array(
		'id'           => 'woods',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.
	);

	tgmpa( $plugins, $config );
}
