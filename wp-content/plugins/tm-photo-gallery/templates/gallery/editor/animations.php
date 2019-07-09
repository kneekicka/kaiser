<?php
/**
 * Gallery editor animations
 *
 * @package templates/gallery/editor
 */
?>
<h6><?php esc_attr_e( 'Grid Animation Effects', 'tm_gallery' ); ?></h6>
<div class="tm-pg_gallery_animations">
	<div class="tm-pg_radio-group">
		<div class="tm-pg_radio-img tm-pg_gallery_animations_item">
			<input type="radio" id="tm-pg_animation-fade" class="tm-pg_radio-input " name="tm-pg_animation" value="tm-pg_animation-fade">
			<label for="tm-pg_animation-fade">
				<img src="<?php echo TM_PG_MEDIA_URL ?>icons/animation-fade.svg" alt="Fade animation">
				<?php esc_attr_e( 'Fade animation', 'tm_gallery' ); ?>
			</label>
		</div>
		<div class="tm-pg_radio-img tm-pg_gallery_animations_item">
			<input type="radio" id="tm-pg_animation-scale" class="tm-pg_radio-input " name="tm-pg_animation" value="tm-pg_animation-scale">
			<label for="tm-pg_animation-scale">
				<img src="<?php echo TM_PG_MEDIA_URL ?>icons/animation-scale.svg" alt="Scale animation">
				<?php esc_attr_e( 'Scale animation', 'tm_gallery' ); ?>
			</label>
		</div>
		<div class="tm-pg_radio-img tm-pg_gallery_animations_item">
			<input type="radio" id="tm-pg_animation-move-up" class="tm-pg_radio-input " name="tm-pg_animation" value="tm-pg_animation-move-up">
			<label for="tm-pg_animation-move-up">
				<img src="<?php echo TM_PG_MEDIA_URL ?>icons/animation-move-up.svg" alt="Move up animation">
				<?php esc_attr_e( 'Move up animation', 'tm_gallery' ); ?>
			</label>
		</div>
		<div class="tm-pg_radio-img tm-pg_gallery_animations_item">
			<input type="radio" id="tm-pg_animation-flip" class="tm-pg_radio-input " name="tm-pg_animation" value="tm-pg_animation-flip">
			<label for="tm-pg_animation-flip">
				<img src="<?php echo TM_PG_MEDIA_URL ?>icons/animation-flip.svg" alt="Flip animation">
				<?php esc_attr_e( 'Flip animation', 'tm_gallery' ); ?>
			</label>
		</div>
		<div class="tm-pg_radio-img tm-pg_gallery_animations_item">
			<input type="radio" id="tm-pg_animation-helix" class="tm-pg_radio-input " name="tm-pg_animation" value="tm-pg_animation-helix">
			<label for="tm-pg_animation-helix">
				<img src="<?php echo TM_PG_MEDIA_URL ?>icons/animation-helix.svg" alt="Helix animation">
				<?php esc_attr_e( 'Helix animation', 'tm_gallery' ); ?>
			</label>
		</div>
		<div class="tm-pg_radio-img tm-pg_gallery_animations_item">
			<input type="radio" id="tm-pg_animation-fall-perspective" class="tm-pg_radio-input " name="tm-pg_animation" value="tm-pg_animation-fall-perspective">
			<label for="tm-pg_animation-fall-perspective">
				<img src="<?php echo TM_PG_MEDIA_URL ?>icons/animation-fall-perspective.svg" alt="Fall perspective animation">
				<?php esc_attr_e( 'Fall perspective animation', 'tm_gallery' ); ?>
			</label>
		</div>
	</div>
</div>
<div class="tm-pg_spacer"></div>
<h6><?php esc_attr_e( 'Hover Animation Effects', 'tm_gallery' ); ?></h6>
<div class="tm-pg_gallery_hove">
	<div class="tm-pg_radio-group">
		<div class="tm-pg_radio-img tm-pg_gallery_hover_item">
			<input type="radio" id="tm-pg_hover-fade" class="tm-pg_radio-input " name="tm-pg_hover" value="tm-pg_hover-fade">
			<label for="tm-pg_hover-fade">
				<img src="<?php echo TM_PG_MEDIA_URL ?>icons/hover-fade.svg" alt="Fade">
				<?php esc_attr_e( 'Fade', 'tm_gallery' ); ?>
			</label>
		</div>
		<div class="tm-pg_radio-img tm-pg_gallery_hover_item">
			<input type="radio" id="tm-pg_hover-scale" class="tm-pg_radio-input " name="tm-pg_hover" value="tm-pg_hover-scale">
			<label for="tm-pg_hover-scale">
				<img src="<?php echo TM_PG_MEDIA_URL ?>icons/hover-scale.svg" alt="Scale">
				<?php esc_attr_e( 'Scale', 'tm_gallery' ); ?>
			</label>
		</div>
		<div class="tm-pg_radio-img tm-pg_gallery_hover_item">
			<input type="radio" id="tm-pg_hover-str" class="tm-pg_radio-input " name="tm-pg_hover" value="tm-pg_hover-str">
			<label for="tm-pg_hover-str">
				<img src="<?php echo TM_PG_MEDIA_URL ?>icons/hover-sweep-to-right.svg" alt="Custom">
				<?php esc_attr_e( 'Sweep to right', 'tm_gallery' ); ?>
			</label>
		</div>
		<div class="tm-pg_radio-img tm-pg_gallery_hover_item">
			<input type="radio" id="tm-pg_hover-custom" class="tm-pg_radio-input " name="tm-pg_hover" value="tm-pg_hover-custom">
			<label for="tm-pg_hover-custom">
				<img src="<?php echo TM_PG_MEDIA_URL ?>icons/hover-custom.svg" alt="Custom">
				<?php esc_attr_e( 'Custom', 'tm_gallery' ); ?>
			</label>
		</div>
	</div>
</div>
