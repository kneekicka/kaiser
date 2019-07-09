<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Woods
 */
?>

<section class="error-404 not-found">
	<header class="page-header">
		<h1 class="page-title"><?php esc_html_e( '404', 'woods' ); ?></h1>
	</header><!-- .page-header -->

	<div class="page-content">
		<h3><?php esc_html_e( 'We&rsquo;ve encountered an error and we&rsquo;re working on it!', 'woods' ); ?></h3>
		<div class="row">
			<div class="col-lg-8 col-lg-offset-2">
				<h6 class="description"><?php esc_html_e( 'While we&rsquo;re checking which brick in the wall was a cause, please try refreshing this page again!', 'woods' ); ?></h6>
				<p><a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Visit home page', 'woods' ); ?></a></p>
				<!-- <?php get_search_form(); ?> -->
			</div>
		</div>
	</div><!-- .page-content -->
</section><!-- .error-404 -->
