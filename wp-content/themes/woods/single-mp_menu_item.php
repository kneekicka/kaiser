<?php

do_action('mprm-before-main-wrapper');
while (have_posts()) : the_post(); ?>
	<div <?php post_class(apply_filters('mprm-main-wrapper-class', 'mprm-main-wrapper')) ?>>
		<div class="<?php echo apply_filters('mprm-content-wrapper-class', 'mprm-container content-wrapper') ?>">
			<?php do_action( 'mprm_menu_item_header' ); ?>

			<div class="mprm-row">
				<div class="<?php echo apply_filters('mprm-menu-content-class', 'mprm-content mprm-eight mprm-columns') ?>">
					<?php $utility = woods_utility()->utility; ?>

					<?php $utility->media->get_image( array(
						'size'        => 'woods-thumb-post',
						'mobile_size' => 'woods-thumb-post',
						'html'        => '<figure class="mprm-thumbnail"><img class="mprm-thumbnail__img" src="%3$s" alt="%4$s"></figure>',
						'placeholder' => false,
						'echo'        => true,
					) );
					?>

					<?php do_action( 'mprm_menu_item_content' ); ?>
					<?php do_action( 'mprm_menu_item_gallery' ); ?>
				
				</div>
				<div class="<?php echo apply_filters('mprm-menu-sidebar-class', 'mprm-sidebar mprm-four mprm-columns') ?>">
					<?php do_action('mprm_menu_item_slidebar'); ?>
				</div>
				<div class="mprm-clear"></div>
			</div>
		</div>
	</div>
<?php endwhile; ?>
<div class="mprm-clear"></div>
<?php do_action('mprm-after-main-wrapper');

?>
