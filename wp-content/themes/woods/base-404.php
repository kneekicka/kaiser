<?php get_header( woods_template_base() ); ?>

	<div <?php echo woods_get_container_classes( array( 'site-content_wrap' ), 'content' ); ?>>

		<div class="row">

			<div id="primary">

				<main id="main" class="site-main" role="main">

					<?php include woods_template_path(); ?>

				</main><!-- #main -->

			</div><!-- #primary -->

		</div><!-- .row -->

	</div><!-- .container -->


<?php get_footer( woods_template_base() ); ?>
