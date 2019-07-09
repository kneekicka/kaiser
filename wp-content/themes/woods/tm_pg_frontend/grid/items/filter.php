<?php
/**
 * Frontend grid filters
 *
 * @package templates/frontend/grid/items
 */
?>
<li class="active">
	<a data-id="all" data-count="<?php echo $data->posts_count ?>" href="#">
		<?php echo esc_attr__( 'All', 'tm_gallery' ) . " ({$data->posts_count})" ?>
	</a>
</li>
<?php foreach ( $data->terms as $term ) : ?>
	<li>
		<a href="#" data-count="<?php echo $term->count ?>" 
		   data-id="<?php echo $term->term_id ?>" 
		   data-type="<?php echo $term->taxonomy ?>">
			   <?php echo $term->name . " ({$term->count})" ?>
		</a>
	</li>
<?php endforeach; ?>
