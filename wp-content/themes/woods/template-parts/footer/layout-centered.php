<?php
/**
 * The template for displaying the default footer layout.
 *
 * @package Woods
 */

?>

<div class="footer-container">
	<div <?php echo woods_get_container_classes( array( 'site-info' ), 'footer' ); ?>>
		<div class="container">
			<?php
				woods_footer_logo();
				woods_footer_menu();
				woods_social_list( 'footer' );
				woods_footer_copyright();
			?>
		</div>
	</div><!-- .site-info -->
</div><!-- .container -->
