<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php
	/**
	 * @hooked MPHBLoopServiceView::_renderTitleHeadingOpen - 10
	 */
	do_action('mphb_render_loop_service_before_title');
?>

<?php the_title( sprintf( '<a href="%s">', esc_url( get_permalink() ) ), '</a>' ); ?>

<?php
	/**
	 * @hooked MPHBLoopServiceView::_renderTitleHeadingClose - 10
	 */
	do_action('mphb_render_loop_service_after_title');
?>
