<?php
/**
 * Template part for top panel in header.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Woods
 */

// Don't show top panel if all elements are disabled.
if ( ! woods_is_top_panel_visible() ) {
	return;
} ?>

<div class="top-panel">
	<div <?php echo woods_get_container_classes( array( 'top-panel__wrap container' ), 'header' ); ?>><?php
		woods_top_message( '<div class="top-panel__message">%s</div>' );
		woods_top_search( '<div class="top-panel__search">%s</div>' );
		woods_top_menu();
		woods_social_list( 'header' );
	?></div>
</div><!-- .top-panel -->
