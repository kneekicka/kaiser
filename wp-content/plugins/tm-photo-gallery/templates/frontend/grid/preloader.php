<?php
/**
 * Preloader
 *
 * @package templates/frontend/grid
 */
$color = $data->display['loader_color'];
?>
<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 66 66" height="0" width="0" style="position:absolute">
	<g id="preloader<?php echo $data->id; ?>">
		<circle class="path" fill="transparent" stroke-width="3" cx="33" cy="33" r="30" stroke="url(#gradient<?php echo $data->id; ?>)" stroke-linejoin="round"/>
		<linearGradient id="gradient<?php echo $data->id; ?>">
			<stop class="stop-color" offset="50%" stop-color="<?php echo $color; ?>" stop-opacity="1"/>
			<stop class="stop-color" offset="65%" stop-color="<?php echo $color; ?>" stop-opacity=".5"/>
			<stop class="stop-color" offset="100%" stop-color="<?php echo $color; ?>" stop-opacity="0"/>
		</linearGradient>
	</g>
</svg>
