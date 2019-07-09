<?php get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<h1 class="entry-title"><?php the_title(); ?></h1>
		</header><!-- .entry-header -->
		<div class="entry-content">
			<?php the_content(); ?>
			<?php do_action( 'tm-pg-grid-album', get_the_ID() ); ?>
		</div><!-- .entry-content -->
	</article><!-- #post-<?php the_ID(); ?> --> 
<?php endwhile; ?>
<?php get_footer(); ?>
