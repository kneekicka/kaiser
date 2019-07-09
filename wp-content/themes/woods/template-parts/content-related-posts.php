<?php
/**
 * The template for displaying related posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Woods
 * @subpackage single-post
 */
?>
<div class="related-post page-content<?php echo esc_html( $grid_class ); ?>">
	<figure class="post-thumbnail">
		<?php echo $image; ?>
	</figure>
	<div class="post_badge_wrapper">
		<?php echo $category; ?>
		<?php echo $tag; ?>
	</div>
	<header class="entry-header">
		<?php echo $title; ?>

		<div class="entry-meta">
			<?php echo $author; ?>
			<?php echo $date; ?>
			<?php echo $comment_count; ?>
		</div>
	</header>
	<div class="entry-content">
		<?php echo $excerpt; ?>
	</div>
</div>
