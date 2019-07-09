<?php
if ( !defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php
	/**
	 * @hooked MPHBSingleRoomTypeView::renderPageWrapperStart - 10
	 */
	do_action( 'mphb_render_single_room_type_wrapper_start' );
?>

<?php
	while ( have_posts() ) : the_post();

		if ( post_password_required() ) {
			echo get_the_password_form();
			return;
		}
?>

		<div <?php post_class(); ?>>

			<?php do_action( 'mphb_render_single_room_type_before_content' ); ?>

				<?php
					/**
					 * @hooked MPHBSingleRoomTypeView::renderTitle				- 10
					 * @hooked MPHBSingleRoomTypeView::renderFeaturedImage		- 20
					 * @hooked MPHBSingleRoomTypeView::renderDescription		- 30
					 * @hooked MPHBSingleRoomTypeView::renderPrice				- 40
					 * @hooked MPHBSingleRoomTypeView::renderAttributes			- 50
					 * @hooked MPHBSingleRoomTypeView::renderCalendar			- 60
					 * @hooked MPHBSingleRoomTypeView::renderReservationForm	- 70
					 */
					do_action( 'mphb_render_single_room_type_content' );
				?>

			<?php do_action( 'mphb_render_single_room_type_after_content' ); ?>

		</div>

<?php
	endwhile;
?>

<?php
	/**
	 * @hooked MPHBSingleRoomTypeView::renderPageWrapperEnd - 10
	 */
	do_action( 'mphb_render_single_room_type_wrapper_end' );
?>
