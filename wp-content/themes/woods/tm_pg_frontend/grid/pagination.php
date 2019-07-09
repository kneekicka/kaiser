<?php
/**
 * Grid pagination
 *
 * @package templates/frontend/grid
 */
?>
<nav class=" <?php echo apply_filters( 'tm-pg-gallery-pagination-class', '' ) ?>" role="navigation"
	 data-count="<?php echo $data['count'] ?>">
	<div class="tm_pg_nav-links" >
		<a class="prev tm_pg_page-numbers"  <?php echo $data['current'] > 0 ? '' : 'style="display: none"' ?> href="#">
			<i class="fa fa-chevron-left" aria-hidden="true"></i>
		</a>
		<?php
			if ( 1 < $data['count'] ) {
				for ( $i = 0; $i < $data['count']; $i++ ) : ?>
					<?php
					$j = $i;
					$j++;
					?>
					<a href="#" data-pos="<?php echo $j; ?>" class="tm_pg_page-numbers <?php echo $i == $data['current'] ? 'current' : '' ?>">
						<?php echo $j; ?>
					</a>
				<?php endfor;
			}
		?>
		<a class="next tm_pg_page-numbers" <?php echo ++$data['current'] < $data['count'] ? '' : 'style="display: none"' ?> href="#">
			<i class="fa fa-chevron-right" aria-hidden="true"></i>
		</a>
	</div>
</nav>

