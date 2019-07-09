<?php
/**
 * Loop Room title
 *
 * This template can be overridden by copying it to %theme%/mphb_templates/loop-room-type/title.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php
	/**
	 * @hooked MPHBLoopRoomTypeView::_renderTitleHeadingOpen - 10
	 */
	do_action('mphb_render_loop_room_type_before_title');
?>

<?php the_title(); ?>

<?php
	/**
	 * @hooked MPHBLoopRoomTypeView::_renderTitleHeadingClose - 10
	 */
	do_action('mphb_render_loop_room_type_after_title');
?>
