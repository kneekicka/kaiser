<?php
/**
 * Template part for default Header layout.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Woods
 */
?>

<div class="site-branding">
	<?php woods_header_logo() ?>
	<?php woods_site_description(); ?>
</div>

<div class="flex_wrapper">
	<?php woods_main_menu(); ?>
	<?php woods_top_header_right( '%s' ); ?>
</div>
