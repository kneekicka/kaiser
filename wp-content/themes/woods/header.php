<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Woods
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php woods_get_page_preloader(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'woods' ); ?></a>
	<header id="masthead" <?php woods_header_class(); ?> role="banner">
		<?php woods_ads_header(); ?>
		<?php get_template_part( 'template-parts/header/top-panel' ); ?>
		<div <?php woods_header_container_class(); ?>>
			<div <?php echo woods_get_container_classes( array( 'header-container_wrap container' ) ); ?>>
				<?php get_template_part( 'template-parts/header/layout', get_theme_mod( 'header_layout_type' ) ); ?>
			</div>
		</div><!-- .header-container -->
		<?php woods_site_breadcrumbs(); ?>
	</header><!-- #masthead -->

	<div id="content" <?php woods_content_class(); ?>>
