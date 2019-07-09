<?php
class MPHBShortcodeRooms extends MPHBShortcode{

	protected $shortcodeName = 'mphb_rooms';

	public function __construct() {
		parent::__construct();
	}

	public function addActions() {
		parent::addActions();
		$this->addTemplateActions();
	}

	private function addTemplateActions(){
		add_action('mphb_sc_rooms_room_type_details', array('MPHBLoopRoomTypeView', 'renderGalleryOrFeaturedImage'), 10);
		add_action('mphb_sc_rooms_room_type_details', array('MPHBLoopRoomTypeView', 'renderTitle'), 20);
		add_action('mphb_sc_rooms_room_type_details', array('MPHBLoopRoomTypeView', 'renderExcerpt'), 30);
		add_action('mphb_sc_rooms_room_type_details', array( 'MPHBLoopRoomTypeView', 'renderAttributes'), 40);
		add_action('mphb_sc_rooms_room_type_details', array( 'MPHBLoopRoomTypeView', 'renderPrice'), 50);
		add_action('mphb_sc_rooms_room_type_details', array('MPHBLoopRoomTypeView', 'renderViewDetailsButton'), 60);
		add_action('mphb_sc_rooms_room_type_details', array( 'MPHBLoopRoomTypeView', 'renderBookButton'), 70);


		add_action('mphb_sc_rooms_after_loop', array($this, 'renderPagination'));
	}

	/**
	 *
	 * @param array $atts
	 * @param null $content
	 * @param string $shortcodeName
	 * @return string
	 */
	public function render($atts, $content = null, $shortcodeName){
		$atts = shortcode_atts(array(), $atts, $shortcodeName);
		ob_start();
		?>
		<div class="mphb_sc_rooms-wrapper mphb-room-types <?php echo apply_filters('mphb_sc_rooms_wrapper_class', ''); ?>">
		<?php
		$roomTypes = $this->getRoomTypes();

		if ($roomTypes->have_posts()) {

			do_action('mphb_sc_rooms_before_loop', $roomTypes);

			while ($roomTypes->have_posts()) : $roomTypes->the_post();
				$this->renderRoomType();
			endwhile;

			wp_reset_postdata();

			do_action('mphb_sc_rooms_after_loop', $roomTypes);
		} else {
			$this->showNotMatchedMessage();
		}
		?>
		</div>
		<?php
		$content = ob_get_clean();

		return $content;
	}

	public function renderPagination($rooms){

		$big = 999999;
		$search_for   = array( $big, '#038;' );
		$replace_with = array( '%#%', '&' );
		$pagination = paginate_links( array(
			'base'    => str_replace( $search_for, $replace_with, get_pagenum_link( $big ) ),
			'format'  => '?paged=%#%',
			'current' => max( 1, $this->getPagedVar() ),
			'total'   => $rooms->max_num_pages
		) );
		?>

		<?php if ( ! empty( $pagination ) ) { ?>
		<div id="mphb_sc_rooms-pagination" class="mphb-pagination">
			<?php echo $pagination; ?>
		</div>
		<?php };
	}

	public function getPagedVar(){
		if ( get_query_var( 'paged' ) ) {
			$paged = absint( get_query_var('paged') );
		} else if ( get_query_var( 'page' ) ) {
			$paged = absint(get_query_var( 'page' ));
		} else {
			$paged = 1;
		}
		return $paged;
	}

	public function getRoomTypes(){
		return new WP_Query(array(
			'post_type' => MPHB()->getRoomTypeCPT()->getPostType(),
			'post_status' => 'publish',
			'paged' => $this->getPagedVar(),
			'ignore_sticky_posts' => true
		));
	}

	private function renderRoomType(){
		$roomType = MPHB()->getCurrentRoomType();
		do_action('mphb_sc_rooms_before_room', $roomType);
		?>
		<div class="mphb-room-type <?php echo apply_filters('mphb_sc_rooms_room_type_class', ''); ?>  <?php echo join(' ', mphb_tmpl_get_filtered_post_class()); ?>">
			<?php do_action('mphb_sc_rooms_room_type_details', $roomType); ?>
		</div>
	<?php
		do_action('mphb_sc_rooms_after_room', $roomType);
	}

	public function showNotMatchedMessage(){
		?>
		<p class="mphb-not-found"><?php _e('No rooms matched criteria.', 'motopress-hotel-booking'); ?></p>
		<?php
	}

}