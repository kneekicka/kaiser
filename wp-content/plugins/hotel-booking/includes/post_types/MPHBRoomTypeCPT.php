<?php
/**
 * Description of MPHBRoomTypeCPT
 *
 */
class MPHBRoomTypeCPT extends MPHBCustomPostType {

	protected $postTypeName = 'mphb_room_type';
	private $facilityTaxName = 'mphb_room_type_facility';
	private $categoryTaxName = 'mphb_room_type_category';

	protected function addActions(){
		parent::addActions();
		add_action('after_setup_theme', array($this, 'addFeaturedImageSupport'), 11);

		add_filter('single_template', array($this, 'filterSingleTemplate'));

		add_action('parse_query', array($this, 'parseQuery'));
		add_filter( 'request', array($this, 'filterCustomOrderBy') );
		add_filter('post_class', array($this, 'filterPostClass'), 20, 3);

		add_action('admin_menu', array($this, 'customizeMetaBoxes'));
		add_action('save_post', array($this, 'generateRooms'), 11, 2);
	}

	public function setManagePageCustomColumns( $columns ){
		$customColumns = array(
			'price' => __('Price', 'motopress-hotel-booking'),
			'capacity' => __('Capacity', 'motopress-hotel-booking'),
			'bed' => __('Bed Type', 'motopress-hotel-booking'),
			'rooms' => __('Rooms Count', 'motopress-hotel-booking')
		);
		$offset = array_search('date', array_keys($columns)); // Set custom columns position before "DATE" column
		$columns = array_slice($columns, 0, $offset, true) + $customColumns + array_slice($columns, $offset, count($columns) - 1, true);

		return $columns;
	}

	public function setManagePageCustomColumnsSortable( $columns ){
		$columns['price'] = 'mphb_price';
		$columns['bed'] = 'mphb_bed';

		return $columns;
	}

	public function renderManagePageCustomColumns( $column, $postId ) {
		$roomType = new MPHBRoomType($postId);
		switch ( $column ) {
			case 'price' :
				echo $roomType->getPriceHTML();
				break;
			case 'capacity' :
				?>
				<p><span><?php _e('Adults:', 'motopress-hotel-booking'); ?></span>&nbsp;<strong><?php echo $roomType->getAdultsCapacity(); ?></strong></p>
				<p><span><?php _e('Chlids:', 'motopress-hotel-booking'); ?></span>&nbsp;<strong><?php echo $roomType->getChildsCapacity(); ?></strong></p>
				<p><span><?php _e('Size:', 'motopress-hotel-booking'); ?></span>&nbsp;<strong><?php echo $roomType->getSize(true); ?></strong></p>
				<?php
				break;
			case 'bed' :
				$bedType = $roomType->getBedType();
				echo !empty($bedType) ? sprintf('<a href="%s">%s</a>', esc_url(add_query_arg('mphb_bed', $bedType)), $bedType) : '<span aria-hidden="true">&#8212;</span>';
				break;
			case 'rooms':
				$totalRooms = $roomType->getAllRooms();
				$activeRooms = $roomType->getActiveRooms();
				$totalRoomsLink = MPHB()->getRoomCPT()->getManagePostsLink(
					array(
						'mphb_room_type_id' => $roomType->getId()
					)
				);
				$activeRoomsLink = MPHB()->getRoomCPT()->getManagePostsLink(
					array(
						'mphb_room_type_id' => $roomType->getId(),
						'post_status' => 'publish'
					)
				);
				?>
				<p>
					<span><?php _e('Total:', 'motopress-hotel-booking'); ?></span>
					<strong><a href="<?php echo $totalRoomsLink; ?>"><?php echo count($totalRooms); ?></a></strong>
				</p>
				<p>
					<span><?php _e('Active:', 'motopress-hotel-booking'); ?></span>
					<strong><a href="<?php echo $activeRoomsLink; ?>"><?php echo count($activeRooms); ?><a></strong>
				</p>
				<?php
				break;
		}
	}

	public function register(){
		register_post_type($this->postTypeName, array(
			'labels' => array(
				'name' => __('Room Types', 'motopress-hotel-booking'),
				'singular_name' => __('Room Type', 'motopress-hotel-booking'),
				'add_new' => _x('Add New', 'Add New Room Type', 'motopress-hotel-booking'),
				'add_new_item' => __('Add New Room Type', 'motopress-hotel-booking'),
				'edit_item' => __('Edit Room Type', 'motopress-hotel-booking'),
				'new_item' => __('New Room Type', 'motopress-hotel-booking'),
				'view_item' => __('View Room Type', 'motopress-hotel-booking'),
				'search_items' => __('Search Room Type', 'motopress-hotel-booking'),
				'not_found' => __('No room types found', 'motopress-hotel-booking'),
				'not_found_in_trash' => __('No room types found in Trash', 'motopress-hotel-booking'),
				'all_items' => __('All Room Types', 'motopress-hotel-booking'),
				'insert_into_item' => __('Insert into room type description', 'motopress-hotel-booking'),
				'uploaded_to_this_item' => __('Uploaded to this room type', 'motopress-hotel-booking')
			),
			'description' => __( 'This is where you can add new room types to your hotel.', 'motopress-hotel-booking' ),
			'public' => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'show_in_menu' => true,
			'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'),
			'hierarchical' => false,
			'register_meta_box_cb' => array($this, 'registerMetaBoxes'),
			'rewrite' => array(
				'slug' => 'room-types',
				'with_front' => false,
				'feeds' => true
			),
			'query_var' => _x('room-type', 'slug','motopress-hotel-booking'),
		));

		register_taxonomy($this->categoryTaxName, $this->postTypeName, array(
			'label' => __('Type', 'motopress-hotel-booking'),
			'labels' => array(
				'name'							=> __('Room Category', 'motopress-hotel-booking'),
				'singular_name'					=> __('Room Category', 'motopress-hotel-booking'),
				'search_items'					=> __( 'Search Room Categories', 'motopress-hotel-booking' ),
				'popular_items'					=> __( 'Popular Room Categories', 'motopress-hotel-booking' ),
				'all_items'						=> __( 'All Room Categories', 'motopress-hotel-booking' ),
				'parent_item'					=> __( 'Parent Room Category', 'motopress-hotel-booking'),
				'parent_item_colon'				=> __( 'Parent Room Category:', 'motopress-hotel-booking'),
				'edit_item'						=> __( 'Edit Room Category', 'motopress-hotel-booking' ),
				'update_item'					=> __( 'Update Room Category', 'motopress-hotel-booking' ),
				'add_new_item'					=> __( 'Add New Room Category', 'motopress-hotel-booking' ),
				'new_item_name'					=> __( 'New Room Category Name', 'motopress-hotel-booking' ),
				'separate_items_with_commas'	=> __( 'Separate categories with commas', 'motopress-hotel-booking' ),
				'add_or_remove_items'			=> __( 'Add or remove categories', 'motopress-hotel-booking' ),
				'choose_from_most_used'			=> __( 'Choose from the most used categories', 'motopress-hotel-booking' ),
				'not_found'						=> __( 'No categories found.', 'motopress-hotel-booking' ),
				'menu_name'						=> __( 'Categories', 'motopress-hotel-booking' )
			),
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => MPHB()->getMainMenuSlug(),
			'show_tagcloud' => true,
			'show_admin_column' => true,
			'hierarchical' => true,
			'rewrite' => array(
				'slug' => 'room-type-category',
				'with_front' => false,
				'hierarchical' => true
			),
			'query_var' => _x('room-type-category', 'slug','motopress-hotel-booking')
		));
		register_taxonomy_for_object_type($this->categoryTaxName, $this->postTypeName);

		register_taxonomy($this->facilityTaxName, $this->postTypeName, array(
			'label' => __('Facility', 'motopress-hotel-booking'),
			'labels' => array(
				'name'							=> __('Facilities', 'motopress-hotel-booking'),
				'singular_name'					=> __('Facility', 'motopress-hotel-booking'),
				'search_items'					=> __( 'Search Facilities', 'motopress-hotel-booking' ),
				'popular_items'					=> __( 'Popular Facilities', 'motopress-hotel-booking' ),
				'all_items'						=> __( 'All Facilities', 'motopress-hotel-booking' ),
				'parent_item'					=> __( 'Parent Facility', 'motopress-hotel-booking'),
				'parent_item_colon'				=> __( 'Parent Facility:', 'motopress-hotel-booking'),
				'edit_item'						=> __( 'Edit Facility', 'motopress-hotel-booking' ),
				'update_item'					=> __( 'Update Facility', 'motopress-hotel-booking' ),
				'add_new_item'					=> __( 'Add New Facility', 'motopress-hotel-booking' ),
				'new_item_name'					=> __( 'New Facility Name', 'motopress-hotel-booking' ),
				'separate_items_with_commas'	=> __( 'Separate facilities with commas', 'motopress-hotel-booking' ),
				'add_or_remove_items'			=> __( 'Add or remove facilities', 'motopress-hotel-booking' ),
				'choose_from_most_used'			=> __( 'Choose from the most used facilities', 'motopress-hotel-booking' ),
				'not_found'						=> __( 'No facilities found.', 'motopress-hotel-booking' ),
				'menu_name'						=> __( 'Facilities', 'motopress-hotel-booking' )
			),
			'public' => true,
			'hierarchical' => true,
			'show_ui' => true,
			'show_in_menu' => MPHB()->getMainMenuSlug(),
			'show_tagcloud' => true,
			'show_admin_column' => true,
			'rewrite' => array(
				'slug' => 'room-type-facility',
				'with_front' => false,
				'hierarchical' => true
			),
			'query_var' => _x('room-type-facility', 'slug','motopress-hotel-booking'),
		));

		register_taxonomy_for_object_type($this->facilityTaxName, $this->postTypeName);
	}

	public function initMetaBoxes(){
		$ratesGroup = new MPHBMetaBoxGroup('mphb_rates', __('Rates', 'motopress-hotel-booking'), $this->postTypeName);
		$complexRates = MPHBFieldFactory::create(
			'mphb_rates',
			array(
				'type' => 'complex-vertical',
				'label' => false,
				'fields' => array(
					MPHBFieldFactory::create('title',array(
						'type' => 'text',
						'label' => __('Title', 'motopress-hotel-booking'),
						'required' => true
					)),
					MPHBFieldFactory::create('description', array(
						'type' => 'textarea',
						'label' => __('Description', 'motopress-hotel-booking')
					)),
					MPHBFieldFactory::create('price', array(
						'type' => 'number',
						'label' => __('Price Per Night', 'motopress-hotel-booking'),
						'default' => 0,
						'step' => 0.01,
						'min' => 0,
						'size' => 'price',
						'description' => MPHB()->getSettings()->getCurrencySymbol(),
						'required' => true
					)),
					MPHBFieldFactory::create('sheduled_prices', array(
						'type' => 'complex',
						'label' => __('Sheduled Price', 'motopress-hotel-booking'),
						'fields' => array(
							MPHBFieldFactory::create('days', array(
								'type' => 'multiple-select',
								'label' => __('Day', 'motopress-hotel-booking'),
								'list' => MPHB()->getSettings()->getDaysList(),
								'required' => true
							)),
							MPHBFieldFactory::create('price', array(
								'type' => 'number',
								'label' => __('Price', 'motopress-hotel-booking'),
								'default' => 0,
								'min' => 0,
								'step' => 0.01,
								'size' => 'price',
								'required' => true,
								'description' => MPHB()->getSettings()->getCurrencySymbol()
							))
						),
						'add_label' => __('Add New Sheduled Price', 'motopress-hotel-booking'),
					)),
					MPHBFieldFactory::create('special_prices', array(
						'type' => 'complex',
						'label' => __('Special Days Price', 'motopress-hotel-booking'),
						'fields' => array(
							MPHBFieldFactory::create('dates', array(
								'type' => 'datepicker',
								'label' => __('Date', 'motopress-hotel-booking'),
								'size' => 'large',
								'multiple' => true,
								'required' => true
							)),
							MPHBFieldFactory::create('price', array(
								'type' => 'number',
								'label' => __('Price', 'motopress-hotel-booking'),
								'default' => 0,
								'min' => 0,
								'step' => 0.01,
								'size' => 'price',
								'required' => true,
								'description' => MPHB()->getSettings()->getCurrencySymbol()
							))
						),
						'add_label' => __('Add New Special Price', 'motopress-hotel-booking'),
					)),
					MPHBFieldFactory::create('disabled', array(
						'type' => 'checkbox',
						'label' => __('Availability', 'motopress-hotel-booking'),
						'inner_label' => __('This rate is disabled', 'motopress-hotel-booking')
					))
				),
				'delete_label' => __('Delete Rate', 'motopress-hotel-booking'),
				'min_items_count' => 1,
			)
		);
		$ratesGroup->addField($complexRates);

		$capacityGroup = new MPHBMetaBoxGroup('mphb_capacity', __('Capacity', 'motopress-hotel-booking'), $this->postTypeName);
		$adultsCapacityField = MPHBFieldFactory::create(
			'mphb_adults_capacity',
			array(
				'type' => 'select',
				'label' => __('Adults', 'motopress-hotel-booking'),
				'default' => (string) MPHB()->getSettings()->getMinAdults(),
				'list' => MPHB()->getSettings()->getAdultsList()
			)
		);
		$capacityGroup->addField($adultsCapacityField);
		$childsCapacityField = MPHBFieldFactory::create(
			'mphb_childs_capacity',
			array(
				'type' => 'select',
				'label' => __('Child(s)', 'motopress-hotel-booking'),
				'default' => '0',
				'list' => MPHB()->getSettings()->getChildsList()
			)
		);
		$capacityGroup->addField($childsCapacityField);
		$sizeField = MPHBFieldFactory::create(
			'mphb_size',
			array(
				'type' => 'number',
				'label' => __('Size', 'motopress-hotel-booking'),
				'default' => 0,
				'min' => 0,
				'step' => 0.1,
				'size' => 'small',
				'description' => MPHB()->getSettings()->getSquareUnit(),
			)
		);
		$capacityGroup->addField($sizeField);

		$otherGroup = new MPHBMetaBoxGroup('mphb_other', __('Other', 'motopress-hotel-booking'), $this->postTypeName);
		$viewField = MPHBFieldFactory::create(
			'mphb_view',
			array(
				'type' => 'text',
				'label' => __('View', 'motopress-hotel-booking'),
				'size' => 'large'
			)
		);
		$otherGroup->addField($viewField);

		$bedField = MPHBFieldFactory::create(
			'mphb_bed',
			array(
				'type' => 'select',
				'label' => __('Bed', 'motopress-hotel-booking'),
				'list' => array_merge( array('' => __('None', 'motopress-hotel-booking')),  MPHB()->getSettings()->getBedTypesList()),
				'description' => strtr( __('Set bed list in <a href="%link%" target="_blank">settings</a>.'), array( '%link%' => MPHB()->getSettingsPageUrl() ) ),
			)
		);
		$otherGroup->addField($bedField);

		$galleryGroup = new MPHBMetaBoxGroup( 'mphb_gallery', __('Room Type Gallery', 'motopress-hotel-booking'), $this->postTypeName, 'side');
		$galleryField = MPHBFieldFactory::create(
			'mphb_gallery',
			array(
				'type' => 'gallery'
			)
		);
		$galleryGroup->addField($galleryField);

		$servicesGroup = new MPHBMetaBoxGroup( 'mphb_services', __('Available Services', 'motopress-hotel-booking'), $this->postTypeName);
		$servicesField = MPHBFieldFactory::create(
			'mphb_services',
			array(
				'type' => 'service-chooser',
				'label' => __('Available Services', 'motopress-hotel-booking'),
				'show_prices' => true,
				'show_add_new' => true
			)
		);
		$servicesGroup->addField($servicesField);

		$this->fieldGroups = array($ratesGroup, $capacityGroup, $otherGroup, $galleryGroup, $servicesGroup);
	}

	public function customizeMetaBoxes(){
		add_meta_box('rooms', __('Rooms', 'motopress-hotel-booking'), array($this, 'renderRoomMetaBox'), $this->postTypeName, 'normal');
	}

	public function renderRoomMetaBox($post, $metabox){
		$roomType = new MPHBRoomType($post);
		?>
			<table class="form-table">
				<tbody>
					<?php if ( $this->isAdminSingleAddNewPage() ) { ?>
					<tr>
						<th>
							<label for="mphb_generate_rooms_count"><?php _e('Rooms Count:', 'motopress-hotel-booking'); ?></label>
						</th>
						<td>
							<div>
								<input type="number" required="required" name="mphb_generate_rooms_count" min="0" step="1" value="1" class="small-text"/>
								<p class="description"><?php _e('Count of rooms of this type that will be generated.', 'motopress-hotel-booking'); ?></p>
							</div>
						</td>
					</tr>
					<?php } else { ?>
					<tr>
						<th>
							<label><?php _e('Total Rooms:', 'motopress-hotel-booking'); ?></label>
						</th>
						<td>
							<div>
								<span><?php echo count($roomType->getAllRooms()); ?></span> <span class="description"><a href="<?php echo MPHB()->getRoomCPT()->getManagePostsLink(array('mphb_room_type_id' => $roomType->getId())); ?>" target="_blank"><?php _e('Show Rooms', 'motopress-hotel-booking'); ?></a></span>
							</div>
						</td>
					</tr>
					<tr>
						<th>
							<label><?php _e('Active Rooms:', 'motopress-hotel-booking'); ?></label>
						</th>
						<td>
							<div>
								<span><?php echo count($roomType->getActiveRooms()); ?></span> <span class="description"><a href="<?php echo MPHB()->getRoomCPT()->getManagePostsLink(array('mphb_room_type_id' => $roomType->getId(), 'post_status' => 'publish')); ?>" target="_blank"><?php _e('Show Rooms', 'motopress-hotel-booking'); ?></a></span>
							</div>
						</td>
					</tr>
					<tr>
						<th></th>
						<td>
							<div>
								<a href="<?php echo MPHB()->getRoomsGeneratorPage()->getUrl(array('mphb_room_type_id' => $roomType->getId())) ?>"><?php _e('Generate Rooms', 'motopress-hotel-booking'); ?></a>
							</div>
						</td>
					</tr>
					<?php } ?>

				</tbody>
			</table>
		<?php
	}

	public function generateRooms($postId, $post){

		if ( empty($postId) || empty($post) ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check the post being saved == the $postId to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $postId ) {
			return;
		}

		if ( $post->post_type === $this->getPostType() && $this->isAdminSingleEditPage() && $this->isCanSave($postId) ) {
			if ( isset($_POST['mphb_generate_rooms_count']) ) {
				$roomsCount = absint($_POST['mphb_generate_rooms_count']);
				if ($roomsCount > 0) {
					$isGenerated = MPHB()->getRoomCPT()->generateRooms($roomsCount, $postId);
				}
			}
			remove_action('save_post', array($this, 'generateRooms'));
		}
	}

	public function addFeaturedImageSupport() {
		$supportedTypes = get_theme_support('post-thumbnails');
		if ($supportedTypes === false) {
			add_theme_support('post-thumbnails', array($this->postTypeName));
		} elseif (is_array($supportedTypes)) {
			$supportedTypes[0][] = $this->postTypeName;
			add_theme_support('post-thumbnails', $supportedTypes[0]);
		}
	}

	public function filterSingleTemplate($template) {
		global $post;

		if ( $post->post_type === $this->postTypeName ) {
			if ( MPHB()->getSettings()->isPluginTemplateMode() ) {
				$template = locate_template(MPHB()->getTemplatePath() . 'single-room-type.php');
				if (!$template) {
					$template = MPHB()->getPluginPath('templates/single-room-type.php');
				}
			} else {
				add_action('loop_start', array($this, 'setupPseudoTemplate'));
			}
		}
		return $template;
	}

	public function setupPseudoTemplate( $query ){
		if ( $query->is_main_query() ) {
			add_filter('the_content', array($this, 'appendRoomMetas'));
			remove_action( 'loop_start', array( $this, 'setupPseudoTemplate' ) );
		}
	}

	public function appendRoomMetas($content){
		// only run once
		remove_filter( 'the_content', array( __CLASS__, 'appendRoomMetas' ) );

		global $post;

		if ($post->post_type === $this->postTypeName) {
			ob_start();
			MPHBSingleRoomTypeView::_renderMetas();
			$content .= ob_get_clean();
		}

		return $content;
	}

	public function parseQuery($query){
		if ( $this->isAdminListingPage()) {
			if (isset($_GET['mphb_bed']) && $_GET['mphb_bed'] != '') {
				$query->query_vars['meta_key'] = 'mphb_bed';
				$query->query_vars['meta_value'] = sanitize_text_field($_GET['mphb_bed']);
				$query->query_vars['meta_compare'] = 'LIKE';
			}
		}
	}

	public function filterCustomOrderBy( $vars ) {
		if ( $this->isAdminListingPage() ) {
			if ( isset( $vars['orderby'] ) ) {
				switch( $vars['orderby'] ) {
					case 'mphb_price':
						$vars = array_merge( $vars, array(
							'meta_key' => 'mphb_price',
							'orderby' => 'meta_value_num'
						) );
						break;
					case 'mphb_bed':
						$vars = array_merge( $vars, array(
							'meta_key' => 'mphb_bed',
							'orderby' => 'meta_value'
						) );
						break;
				}
			}
		}
		return $vars;
	}

	public function filterPostClass( $classes, $class = '', $postId ='' ){

		if ( $postId !== '' && get_post_type($postId) === $this->getPostType() ) {

			$roomType = new MPHBRoomType( $postId );

			$classes[] = 'mphb-room-type-adults-' . $roomType->getAdultsCapacity();
			$classes[] = 'mphb-room-type-childs-' . $roomType->getChildsCapacity();

//			if ( false !== ( $key = array_search( 'hentry', $classes ) ) ) {
//			if ( false !== ( $key = array_search( 'hentry', $classes ) ) && MPHB()->getSettings()->isPluginTemplateMode() ) {
//				unset( $classes[ $key ] );
//			}

		}

		return $classes;
	}

	public function getFacilityTaxName(){
		return $this->facilityTaxName;
	}

	public function getCategoryTaxName(){
		return $this->categoryTaxName;
	}

	public function getRoomTypesList(){
		$roomTypesList = array();

		$roomTypes = $this->getPosts( array(
			'orderby'	 => 'ID',
			'order'		 => 'ASC'
		) );

		foreach ( $roomTypes as $roomType ) {
			$roomTypesList[$roomType->ID] = $roomType->post_title;
		}

		return $roomTypesList;
	}

	public function getMenuSlug(){
		return 'edit.php?post_type=' . $this->getPostType();
	}

}