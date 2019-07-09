<?php get_header( 'coming-soon' ); ?>

	<div <?php echo woods_get_container_classes( array( 'site-content_wrap' ), 'content' ); ?>>

		<div class="row">

			<div id="primary" <?php woods_primary_content_class(); ?>>

				<main id="main" class="site-main" role="main">
						<?php include woods_template_path(); ?>
				</main><!-- #main -->

			</div><!-- #primary -->

		</div><!-- .row -->

	</div><!-- .container -->

<?php get_footer( 'coming-soon' ); ?>
