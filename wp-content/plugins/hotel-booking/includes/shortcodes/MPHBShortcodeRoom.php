<?php

class MPHBShortcodeRoom extends MPHBShortcode {

	protected $shortcodeName = 'mphb_room';

	private $isShowTitle;
	private $isShowFeaturedImage;
	private $isShowExcerpt;
	private $isShowDetails;
	private $isShowPricePerNight;
	private $isShowBookButton;

	public function render( $atts, $content = '', $shortcodeName ) {
		$atts = shortcode_atts(array(
			'id' => '',
			'title' => 'false',
			'featured_image' => 'false',
			'excerpt' => 'false',
			'details' => 'false',
			'price_per_night' => 'false',
			'book_button' => 'false'
		), $atts, $shortcodeName);
		$result = '';

		$id = intval($atts['id']);

		if ( $this->isValidRoom( $id ) ) {

			$this->id = $id;

			$this->isShowTitle = $this->convertParameterToBoolean( $atts['title'] );
			$this->isShowFeaturedImage = $this->convertParameterToBoolean( $atts['featured_image'] );
			$this->isShowExcerpt = $this->convertParameterToBoolean( $atts['excerpt'] );
			$this->isShowDetails = $this->convertParameterToBoolean( $atts['details'] );
			$this->isShowPricePerNight = $this->convertParameterToBoolean( $atts['price_per_night'] );
			$this->isShowBookButton = $this->convertParameterToBoolean( $atts['book_button'] );

			ob_start();
			$this->renderRoom();
			$result = '<div class="mphb_sc_room-wrapper">' . ob_get_clean() . '</div>';
		}

		return $result;
	}

	private function isValidRoom( $id ){
		return get_post_type( $id ) === MPHB()->getRoomTypeCPT()->getPostType();
	}

	private function renderRoom(){

		$roomQuery = new WP_Query(array(
			'post_type' => MPHB()->getRoomTypeCPT()->getPostType(),
			'p' => $this->id,
			'ignore_sticky_posts' => true
		));

		if ( $roomQuery->have_posts() ) {
			while ( $roomQuery->have_posts() ) {
				$roomQuery->the_post();
				echo '<div class="mphb-room-type ' .  apply_filters('mphb_sc_room_room_type_class', '') . ' '  . join(' ', mphb_tmpl_get_filtered_post_class()) . '">';
				if ( $this->isShowFeaturedImage ) {
					MPHBLoopRoomTypeView::renderGalleryOrFeaturedImage();
				}
				if ( $this->isShowTitle ) {
					MPHBLoopRoomTypeView::renderTitle();
				}
				if ( $this->isShowExcerpt ) {
					MPHBLoopRoomTypeView::renderExcerpt();
				}
				if ( $this->isShowDetails ) {
					MPHBLoopRoomTypeView::renderAttributes();
				}
				if ( $this->isShowPricePerNight ) {
					MPHBLoopRoomTypeView::renderPrice();
				}
				if ( $this->isShowBookButton ) {
					MPHBLoopRoomTypeView::renderBookButton();
				}
				echo '</div>';
			}
		} else {
			// no posts found
		}

		wp_reset_postdata();
	}

}
