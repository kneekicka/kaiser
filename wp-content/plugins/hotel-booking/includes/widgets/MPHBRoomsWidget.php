<?php

class MPHBRoomsWidget extends MPHBWidget {

	private $isShowTitle;
	private $isShowFeaturedImage;
	private $isShowExcerpt;
	private $isShowDetails;
	private $isShowPricePerNight;
	private $isShowBookButton;

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'mphb_rooms_widget', // Base ID
			__( 'Room Types', 'motopress-hotel-booking' ), // Name
			array( 'description' => __( 'Output Room Types', 'motopress-hotel-booking' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $args['after_title'];
		}

		$room_type_ids = $instance['room_type_ids'];

		if ( !empty($room_type_ids) ) {
			$this->enqueueScriptStyles();
			$this->isShowTitle = $this->convertParameterToBoolean( $instance['show_title'] );
			$this->isShowFeaturedImage = $this->convertParameterToBoolean( $instance['show_featured_image'] );
			$this->isShowExcerpt = $this->convertParameterToBoolean( $instance['show_excerpt'] );
			$this->isShowDetails = $this->convertParameterToBoolean( $instance['show_details'] );
			$this->isShowPricePerNight = $this->convertParameterToBoolean( $instance['show_price'] );
			$this->isShowBookButton = $this->convertParameterToBoolean( $instance['show_book_button'] );

			ob_start();

			$roomQuery = new WP_Query(array(
				'post_type' => MPHB()->getRoomTypeCPT()->getPostType(),
				'post__in' => $room_type_ids,
				'ignore_sticky_posts' => true
			));

			if ( $roomQuery->have_posts() ) {
				while ( $roomQuery->have_posts() ) {
					$roomQuery->the_post();
					$this->renderRoom();
				}
			} else {
				_e('No rooms found.', 'motopress-hotel-booking');
			}

			wp_reset_postdata();

			echo '<div class="mphb_widget_rooms-wrapper">' . ob_get_clean() . '</div>';

		}

		echo $args['after_widget'];
	}

	private function isValidRoom( $id ){
		return get_post_type( $id ) === MPHB()->getRoomTypeCPT()->getPostType() && get_post_status($id) === 'publish';
	}

	private function renderRoom(){
		echo '<div class="mphb-room-type ' .  apply_filters('mphb_widget_rooms_room_type_class', '') . ' '  . join(' ', mphb_tmpl_get_filtered_post_class()) . '">';

		if ( $this->isShowTitle ) {
			$this->renderTitle();
		}
		if ( $this->isShowFeaturedImage ) {
			$this->renderFeaturedImage();
		}
		if ( $this->isShowExcerpt ) {
			$this->renderExcerpt();
		}
		if ( $this->isShowDetails ) {
			$this->renderAttributes();
		}
		if ( $this->isShowPricePerNight ) {
			$this->renderPrice();
		}
		if ( $this->isShowBookButton ) {
			$this->renderBookButton();
		}

		echo '</div>';
	}

	private function renderTitle(){
		?>
		<div class="mphb-widget-room-type-title">
			<b><?php the_title(); ?></b>
		</div>
		<?php
	}

	private function renderFeaturedImage(){
		if (  has_post_thumbnail() ) :
		?>
			<div class="mphb-widget-room-type-featured-image">
				<?php
					$size = apply_filters('mphb_widget_rooms-thumbnail-size', 'post-thumbnail');
					the_post_thumbnail($size);
				?>
			</div>
		<?php
		endif;
	}

	private function renderExcerpt(){
		if ( has_excerpt() ) :
		?>
			<div class="mphb-widget-room-type-description"><?php
				echo get_the_excerpt();
			?></div>
		<?php
		endif;
	}

	private function renderAttributes(){
			$categories = mphb_tmpl_get_room_type_categories();
			$facilities = mphb_tmpl_get_room_type_facilities();
			$view = mphb_tmpl_get_room_type_view();
			$size = mphb_tmpl_get_room_type_size();
			$bedType = mphb_tmpl_get_room_type_bed_type();
			$adults = mphb_tmpl_get_room_type_adults_capacity();
			$childs = mphb_tmpl_get_room_type_childs_capacity();
		?>
		<ul class="mphb-widget-room-type-attributes">
			<?php if ( !empty($categories) ) : ?>
				<li class="mphb-room-type-categories">
					<span><?php _e('Categories:', 'motopress-hotel-booking'); ?></span>
					<?php echo $categories; ?>
				</li>
			<?php endif; ?>
			<?php if ( !empty($facilities) ) : ?>
				<li class="mphb-room-type-facilities">
					<span><?php _e('Facilities:', 'motopress-hotel-booking'); ?></span>
					<?php echo $facilities; ?>
				</li>
			<?php endif; ?>
			<?php if ( !empty($view) ) : ?>
				<li class="mphb-room-type-view">
					<span><?php _e('View:', 'motopress-hotel-booking'); ?></span>
					<?php echo $view; ?>
				</li>
			<?php endif; ?>
			<?php if ( !empty($size) ) : ?>
				<li class="mphb-room-type-size">
					<span><?php _e('Size:', 'motopress-hotel-booking'); ?></span>
					<?php echo $size; ?>
				</li>
			<?php endif; ?>
			<?php if ( !empty($bedType) ) : ?>
				<li class="mphb-room-type-bed-type">
					<span><?php _e('Bed Type:', 'motopress-hotel-booking'); ?></span>
					<?php echo $bedType; ?>
				</li>
			<?php endif; ?>
			<li class="mphb-room-type-adults">
				<span><?php _e('Adults:', 'motopress-hotel-booking'); ?></span>
				<?php echo $adults; ?>
			</li>
			<?php if ( $childs != 0 ) : ?>
				<li class="mphb-room-type-childs">
					<span><?php _e('Childs:', 'motopress-hotel-booking'); ?></span>
					<?php echo $childs; ?>
				</li>
			<?php endif; ?>
		</ul>
		<?php
	}

	private function renderPrice(){
		?>
		<div class="mphb-widget-room-type-price">
			<span><?php _e('Price:', 'motopress-hotel-booking'); ?></span>
			<?php mphb_tmpl_the_room_type_regular_price(); ?>
		</div>
		<?php
	}

	private function renderBookButton(){
		?>
		<div class="mphb-widget-room-type-book-button">
			<p>
				<?php mphb_tmpl_the_loop_room_type_book_button_form(); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		$instance = wp_parse_args($instance, array(
			'title' => __( 'Room', 'motopress-hotel-booking' ),
			'room_type_ids' => array(),
			'show_title' => true,
			'show_featured_image' => true,
			'show_excerpt' => true,
			'show_details' => true,
			'show_price' => true,
			'show_book_button' => true
		));

		extract($instance);
		if ( $room_type_ids === '' ) {
			$room_type_ids = array();
		}
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( esc_attr( 'Title:' ) ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'room_type_ids' ) ); ?>"><?php _e( esc_attr( 'Room Types:' ) ); ?></label><br/>
			<select id="<?php echo esc_attr( $this->get_field_id( 'room_type_ids' ) ); ?>" multiple="multiple" name="<?php echo esc_attr( $this->get_field_name( 'room_type_ids' ) ); ?>[]" >
				<?php foreach ( MPHB()->getRoomTypeCPT()->getRoomTypesList() as $roomTypeId => $roomTypeTitle ) : ?>
				<?php $selected = in_array($roomTypeId, $room_type_ids) ? ' selected="selected"' : '';?>
					<option value="<?php echo $roomTypeId; ?>" <?php echo $selected; ?>><?php echo $roomTypeTitle; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<input class="checkbox" type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_title' ) ); ?>" <?php checked( $show_title ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_title' ) ); ?>"><?php _e( 'Show Title', 'motopress-hotel-booking' ); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_featured_image' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_featured_image' ) ); ?>" <?php checked( $show_featured_image ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_featured_image' ) ); ?>"><?php _e( 'Show Featured Image', 'motopress-hotel-booking' ); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_excerpt' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_excerpt' ) ); ?>" <?php checked( $show_excerpt ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_excerpt' ) ); ?>"><?php _e( 'Show Excerpt', 'motopress-hotel-booking' ); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_details' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_details' ) ); ?>" <?php checked( $show_details ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_details' ) ); ?>"><?php _e( 'Show Details', 'motopress-hotel-booking' ); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_price' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_price' ) ); ?>" <?php checked( $show_price ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_price' ) ); ?>"><?php _e( 'Show Price', 'motopress-hotel-booking' ); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_book_button' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_book_button' ) ); ?>" <?php checked( $show_book_button ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_book_button' ) ); ?>"><?php _e( 'Show Book Button', 'motopress-hotel-booking' ); ?></label>
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title'] = ( isset($new_instance['title']) && $new_instance['title'] !== '' ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['room_type_ids'] = ( isset($new_instance['room_type_ids']) && $new_instance['room_type_ids'] !== '' ) ? $this->sanitizeRoomTypeIdsArray( $new_instance['room_type_ids'] ) : '';
		$instance['show_title'] = ( isset($new_instance['show_title']) && $new_instance['show_title'] !== '' ) ? $this->sanitizeBoolean( $new_instance['show_title'] ) : '';
		$instance['show_featured_image'] = ( isset($new_instance['show_featured_image']) && $new_instance['show_featured_image'] !== '' ) ? $this->sanitizeBoolean( $new_instance['show_featured_image'] ) : '';
		$instance['show_excerpt'] = ( isset($new_instance['show_excerpt']) && $new_instance['show_excerpt'] !== '' ) ? $this->sanitizeBoolean( $new_instance['show_excerpt'] ) : '';
		$instance['show_details'] = ( isset($new_instance['show_details']) && $new_instance['show_details'] !== '' ) ? $this->sanitizeBoolean( $new_instance['show_details'] ) : '';
		$instance['show_price'] = ( isset($new_instance['show_price']) && $new_instance['show_price'] !== '' ) ? $this->sanitizeBoolean( $new_instance['show_price'] ) : '';
		$instance['show_book_button'] = ( isset($new_instance['show_book_button']) && $new_instance['show_book_button'] !== '' ) ? $this->sanitizeBoolean( $new_instance['show_book_button'] ) : '';

		return $instance;
	}

	protected function sanitizeRoomTypeIdsArray( $value ){
		$sanitizeValue = array();
		if ( is_array( $value ) ) {
			$sanitizeValue = array_filter(array_map( array($this, 'sanitizeRoomTypeId'), $value ));
		}
		return $sanitizeValue;
	}

	/**
	 *
	 * @param string $value
	 * @return string Empty string for uncorrect value
	 */
	public function sanitizeRoomTypeId( $value ){
		$value = absint($value);
		return ( $this->isValidRoom( $value ) ) ? (string) $value : '';
	}

	private function enqueueScriptStyles(){
		wp_enqueue_style('mphb');
	}

}

add_action( 'widgets_init', array('MPHBRoomsWidget', 'register') );