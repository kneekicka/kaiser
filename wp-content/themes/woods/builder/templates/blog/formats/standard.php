<?php
/**
 * Template for displaying standard post format item content
 */
?>
<?php if ( $this->_var( 'thumb' ) ) : ?>
<?php if ( 'list' !== $this->_var( 'blog_layout' ) ) {
	echo '<div class="tm_pb_image_container">';
} ?>
	<a href="<?php esc_url( the_permalink() ); ?>" class="entry-featured-image-url">
		<?php echo $this->_var( 'thumb' ); ?>
		<?php if ( 'on' === $this->_var( 'use_overlay' ) ) {
			echo $this->_var( 'item_overlay' );
		} ?>
	</a>
<?php
if ( 'list' !== $this->_var( 'blog_layout' ) ) {
	echo '</div> <!-- .tm_pb_image_container -->';
}
?>
<?php endif; ?>
<div class="caption">

	<div class="tm_pb_post_categories"><?php
		if ( 'on' === $this->_var( 'show_categories' ) ) {
			echo get_the_category_list(' ');
		}
	?></div>

	<h6 class="entry-title"><a href="<?php esc_url( the_permalink() ); ?>"><?php the_title(); ?></a></h6>
	<?php echo $this->get_template_part( 'blog/meta.php' ); ?>
	<?php echo $this->get_post_content(); ?>
	<?php echo $this->get_more_button(); ?>
</div>
