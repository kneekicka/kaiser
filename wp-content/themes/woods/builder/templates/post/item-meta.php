<?php
/**
 * Template part for displaying cureent item meta
 */

if ( ! $this->_var( 'meta_data' ) || 'off' === $this->_var( 'meta_data' ) ) {
	return;
}
?>
<div class="tm-posts_item_meta"><?php

	tm_builder_core()->utility()->meta_data->get_author( array(
		'prefix'  => esc_html__( 'by ', 'woods' ),
		'echo'    => true,
	) );

	tm_builder_core()->utility()->meta_data->get_date( array(
		'prefix'  => esc_html__( '- ', 'woods' ),
		'echo'    => true,
	) );

	tm_builder_core()->utility()->meta_data->get_comment_count( array(
		'prefix'  => esc_html__( '- ', 'woods' ),
		'sufix'   => _n_noop( '%s comment', '%s comments', 'tm_builder' ),
		'echo'    => true,
	) );

?></div>
