<?php get_header( woods_template_base() ); ?>

	<?php do_action( 'woods_render_widget_area', 'full-width-header-area' ); ?>

	<div <?php echo woods_get_container_classes( array( 'site-content_wrap' ), 'content' ); ?>>

		<?php do_action( 'woods_render_widget_area', 'before-content-area' ); ?>

		<div class="row">

			<div id="primary" class="col-md-12">

				<?php do_action( 'woods_render_widget_area', 'before-loop-area' ); ?>

				<main id="main" class="site-main" role="main">

					<?php include woods_template_path(); ?>

				</main><!-- #main -->

				<?php do_action( 'woods_render_widget_area', 'after-loop-area' ); ?>

			</div><!-- #primary -->

		</div><!-- .row -->

		<?php do_action( 'woods_render_widget_area', 'after-content-area' ); ?>

	</div><!-- .container -->

	<?php do_action( 'woods_render_widget_area', 'after-content-full-width-area' ); ?>

<?php get_footer( woods_template_base() ); ?>
