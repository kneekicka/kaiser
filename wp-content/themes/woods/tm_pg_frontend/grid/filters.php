<?php
/**
 * Frontend grid filters
 *
 * @package templates/frontend/grid
 */
?>
<?php if ( $data->filter['show'] && ! empty( $data->terms ) ) : ?>
	<?php if ( $data->filter['type'] == 'line' ) : ?>
		<ul class="<?php echo apply_filters( 'tm-pg-filter-list-class', 'tm-pg_front_gallery-tabs' ) ?>">
			<?php do_action( 'tm-pg-grid-filter', $data ); ?>
		</ul>
	<?php elseif ( $data->filter['type'] == 'dropdown' ) :  ?>
		<div class="<?php echo apply_filters( 'tm-pg-filter-select-class', 'filter-select' ) ?>">
			<div class="<?php echo apply_filters( 'tm-pg-filter-select__panel-class', 'filter-select__panel' ) ?>">
				<?php
				$data->filter['by'] == 'tag' ?
				esc_attr_e( 'Filter Tags', 'tm_gallery' ) :
				esc_attr_e( 'Filter Categories', 'tm_gallery' )
				?>
			</div>
			<ul class="<?php echo apply_filters( 'tm-pg-filter-select__list-class', 'filter-select__list' ) ?>">
				<?php do_action( 'tm-pg-grid-filter', $data ); ?>
			</ul>
		</div>
	<?php endif; ?>
<?php endif; ?>
