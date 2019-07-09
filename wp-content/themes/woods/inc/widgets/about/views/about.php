<?php
/**
 * Template part to display About Woods widget.
 *
 * @package Woods
 * @subpackage widgets
 */
?>

<div class="widget-about__logo">
	<a class="widget-about__logo-link" href="<?php echo $home_url; ?>">
		<img class="widget-about__logo-img" src="<?php echo $logo_url; ?>" alt="<?php echo $site_name; ?>">
	</a>
</div>
<div class="widget-about__tagline"><?php echo $tagline; ?></div>
<div class="widget-about__content"><?php echo $content; ?></div>
<?php echo $this->get_social_nav( '<div class="widget-about__social">%s</div>' ); ?>
