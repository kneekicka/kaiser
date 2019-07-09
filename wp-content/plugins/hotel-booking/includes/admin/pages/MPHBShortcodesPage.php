<?php
class MPHBShortcodesPage extends MPHBAdminPage{

	protected $name = 'mphb_shortcodes';
	private $hookSuffix;
	private $shortcodes = array();

	public function addActions(){
		parent::addActions();
		add_action('admin_init', array($this, 'initShortcodes'));
	}

	public function createMenu(){
		$this->hookSuffix = add_submenu_page(MPHB()->getMainMenuSlug()
			, __('MotoPress Hotel Booking Shortcodes Parameters', 'motopress-hotel-booking')
			, __('Shortcodes', 'motopress-hotel-booking')
			, 'edit_pages'
			, $this->name
			, array($this, 'renderPage')
		);
	}

	public function initShortcodes(){
		$this->shortcodes[MPHB()->getShortcodeSearch()->getShortcodeName()] = array(
			'label' => __('Availability Search Form', 'motopress-hotel-booking'),
			'parameters' => array(
				'adults' => array(
					'label' => __('Adults Number', 'motopress-hotel-booking'),
					'description' => __('The number of adults which will be presetted in the search form.', 'motopress-hotel-booking'),
					'values' => sprintf('%d...%d', MPHB()->getSettings()->getMinAdults(), MPHB()->getSettings()->getMaxAdults()),
					'default' => strval(MPHB()->getSettings()->getMinAdults())
				),
				'childs' => array(
					'label' => __('Childs Number', 'motopress-hotel-booking'),
					'description' => __('The number of childs which will be presetted in the search form.', 'motopress-hotel-booking'),
					'values' => sprintf('%d...%d', 0, MPHB()->getSettings()->getMaxChilds()),
					'default' => strval(0),
				),
				'check_in_date' => array(
					'label' => __('Check-In Date', 'motopress-hotel-booking'),
					'description' => __('The check-in date which will be presetted in the search form.', 'motopress-hotel-booking'),
					'values' => sprintf(__('Date in format %s', 'motopress-hotel-booking'), 'm/d/Y'),
					'default' => '',
				),
				'check_out_date' => array(
					'label' => __('Check-Out Date', 'motopress-hotel-booking'),
					'description' => __('The check-out date which will be presetted in the search form.', 'motopress-hotel-booking'),
					'values' => sprintf(__('Date in format %s', 'motopress-hotel-booking'), 'm/d/Y'),
					'default' => '',
				)
			),
			'example' => array(
				'shortcode' => MPHB()->getShortcodeSearch()->generateShortcode(),
				'description' => __('Simple Search Form', 'motopress-hotel-booking')
			)
		);
		$this->shortcodes[MPHB()->getShortcodeSearchResults()->getShortcodeName()] = array(
			'label' => __('Availability Search Results', 'motopress-hotel-booking'),
			'description' => __('Display listing of room types that meet the search criteria.', 'motopress-hotel-booking'),
			'parameters' => array(
				'title' => array(
					'label' => __('Show Title', 'motopress-hotel-booking'),
					'description' => __('Whether to display title of the room type.', 'motopress-hotel-booking'),
					'values' => 'true | false (yes,1,on | no,0,off)',
					'default' => 'true'
				),
				'featured_image' => array(
					'label' => __('Show Featured Image', 'motopress-hotel-booking'),
					'description' => __('Whether to display featured image of the room type.', 'motopress-hotel-booking'),
					'values' => 'true | false (yes,1,on | no,0,off)',
					'default' => 'false'
				),
				'gallery' => array(
					'label' => __('Show Gallery', 'motopress-hotel-booking'),
					'description' => __('Whether to display gallery of the room type.', 'motopress-hotel-booking'),
					'values' => 'true | false (yes,1,on | no,0,off)',
					'default' => 'true'
				),
				'excerpt' => array(
					'label' => __('Show Excerpt', 'motopress-hotel-booking'),
					'description' => __('Whether to display excerpt of the room type.', 'motopress-hotel-booking'),
					'values' => 'true | false (yes,1,on | no,0,off)',
					'default' => 'true'
				),
				'details' => array(
					'label' => __('Show Details', 'motopress-hotel-booking'),
					'description' => __('Whether to display details of the room type.', 'motopress-hotel-booking'),
					'values' => 'true | false (yes,1,on | no,0,off)',
					'default' => 'true'
				),
				'default_sorting' => array(
					'label' => __('Sorting Mode', 'motopress-hotel-booking'),
					'values' => 'order, price',
					'default' => 'order'
				)
			),
			'example' => array(
				'shortcode' => MPHB()->getShortcodeSearchResults()->generateShortcode(array(
					'default_sorting' => 'price'
				)),
				'description' => __('Search Results sorting by price.') . '<br/>' . sprintf(__('<strong>NOTE:</strong> Use only on page that you set as Search Results Page on <a href="%s">Settings</a>', 'motopress-hotel-booking'), MPHB()->getSettingsPageUrl())
			)
		);
		$this->shortcodes[MPHB()->getShortcodeRooms()->getShortcodeName()] = array(
			'label' => __('Room Types Listing', 'motopress-hotel-booking'),
			'parameters' => array(),
			'example' => array(
				'shortcode' => MPHB()->getShortcodeRooms()->generateShortcode(),
			)
		);
		$this->shortcodes[MPHB()->getShortcodeServices()->getShortcodeName()] = array(
			'label' => __('Services Listing', 'motopress-hotel-booking'),
			'parameters' => array(
				'ids' => array(
					'label' => __('IDs', 'motopress-hotel-booking'),
					'values' => __('Comma-Separated IDs.', 'motopress-hotel-booking'),
					'description' => __('IDs of services that will be shown. ', 'motopress-hotel-booking'),
				)
			),
			'example' => array(
				'shortcode' => MPHB()->getShortcodeServices()->generateShortcode(),
				'description' => __('Show All Services', 'motopress-hotel-booking')
			)
		);
		$this->shortcodes[MPHB()->getShortcodeRoom()->getShortcodeName()] = array(
			'label' => __('Output Single Room Type', 'motopress-hotel-booking'),
			'parameters' => array(
				'id' => array(
					'label' => __('ID', 'motopress-hotel-booking'),
					'description' => __('ID of room type to display. <strong>Required parameter.</strong>', 'motopress-hotel-booking'),
					'values' => __('Integer Number', 'motopress-hotel-booking'),
				),
				'title' => array(
					'label' => __('Show Title', 'motopress-hotel-booking'),
					'description' => __('Whether to display title of the room type.', 'motopress-hotel-booking'),
					'values' => 'true | false (yes,1,on | no,0,off)',
					'default' => 'false'
				),
				'featured_image' => array(
					'label' => __('Show Featured Image', 'motopress-hotel-booking'),
					'description' => __('Whether to display featured image of the room type.', 'motopress-hotel-booking'),
					'values' => 'true | false (yes,1,on | no,0,off)',
					'default' => 'false'
				),
				'excerpt' => array(
					'label' => __('Show Excerpt', 'motopress-hotel-booking'),
					'description' => __('Whether to display excerpt of the room type.', 'motopress-hotel-booking'),
					'values' => 'true | false (yes,1,on | no,0,off)',
					'default' => 'false'
				),
				'details' => array(
					'label' => __('Show Details', 'motopress-hotel-booking'),
					'description' => __('Whether to display details of the room type.', 'motopress-hotel-booking'),
					'values' => 'true | false (yes,1,on | no,0,off)',
					'default' => 'false'
				),
				'price_per_night' => array(
					'label' => __('Show Price Per Night', 'motopress-hotel-booking'),
					'description' => __('Whether to display price of the room type.', 'motopress-hotel-booking'),
					'values' => 'true | false (yes,1,on | no,0,off)',
					'default' => 'false'
				),
				'book_button' => array(
					'label' => __('Show Book Button', 'motopress-hotel-booking'),
					'description' => __('Whether to display book button.', 'motopress-hotel-booking'),
					'values' => 'true | false (yes,1,on | no,0,off)',
					'default' => 'false'
				)
			),
			'example' => array(
				'shortcode' => MPHB()->getShortcodeRoom()->generateShortcode(array(
					'id' => '777',
					'title' => 'true',
					'excerpt' => 'true',
					'room_details' => 'true',
					'price_per_night' => 'true',
					'book_button' => 'true'
				)),
				'description' => __('Display room type with title, excerpt, details, price and book button.', 'motopress-hotel-booking')
			)
		);
		$this->shortcodes[MPHB()->getShortcodeCheckout()->getShortcodeName()] = array(
			'label' => __('Checkout Form', 'motopress-hotel-booking'),
			'description' => __('Display checkout form.', 'motopress-hotel-booking'),
			'parameters' => array(),
			'example' => array(
				'shortcode' => MPHB()->getShortcodeCheckout()->generateShortcode(),
				'description' => sprintf(__('<strong>NOTE:</strong> Use only on page that you set as Checkout Page on <a href="%s">Settings</a>', 'motopress-hotel-booking'), MPHB()->getSettingsPageUrl())
			)
		);
		$this->shortcodes[MPHB()->getShortcodeBookingForm()->getShortcodeName()] = array(
			'label' => __('Booking Form', 'motopress-hotel-booking'),
			'parameters' => array(
				'id' => array(
					'label' => __('Room Type ID', 'motopress-hotel-booking'),
					'description' => __('ID of room type. <strong>Optional.</strong>', 'motopress-hotel-booking'),
					'values' => __('Integer Number', 'motopress-hotel-booking'),
				)
			),
			'example' => array(
				'shortcode' => MPHB()->getShortcodeBookingForm()->generateShortcode(array(
					'id' => '777'
				)),
				'description' => __('Show Booking Form for Room Type with id 777', 'motopress-hotel-booking')
			)
		);
		$this->shortcodes[MPHB()->getShortcodeRoomRates()->getShortcodeName()] = array(
			'label' => __('Room Rates List', 'motopress-hotel-booking'),
			'parameters' => array(
				'id' => array(
					'label' => __('Room Type ID', 'motopress-hotel-booking'),
					'description' => __('ID of room type. <strong>Optional.</strong>', 'motopress-hotel-booking'),
					'values' => __('Integer Number', 'motopress-hotel-booking'),
				)
			),
			'example' => array(
				'shortcode' => MPHB()->getShortcodeRoomRates()->generateShortcode(array(
					'id' => '777'
				)),
				'description' => __('Show Room Rates List for room type with id 777', 'motopress-hotel-booking')
			)
		);

	}

	public function renderPage(){
		?>
		<h1><?php _e('Shortcodes', 'motopress-hotel-booking'); ?></h1>
		<table class="widefat">
			<thead>
				<tr class="">
					<td><?php _e('Shortcode', 'motopress-hotel-booking'); ?></td>
					<td><?php _e('Parameters', 'motopress-hotel-booking'); ?></td>
					<td><?php _e('Example', 'motopress-hotel-booking'); ?></td>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->shortcodes as $name => $details) : ?>
					<tr valign="top" >
						<th scope="row">
							<h4><?php echo $details['label']; ?></h4>
							<p><?php _e('Shortcode Name:', 'motopress-hotel-booking'); ?> <strong>[<?php echo $name ?>]</strong></p>
							<?php if ( isset( $details['description'] ) && !empty( $details['description'] ) ) { ?>
								<p class="description"><?php echo $details['description']; ?></p>
							<?php } ?>
						</th>
						<td scope="row">
							<?php if ( empty( $details['parameters'] ) ) { ?>
								<span aria-hidden="true">&#8212;</span>
							<?php } else { ?>
								<?php foreach ($details['parameters'] as $paramName => $paramDetails ) { ?>
									<p>
										<strong><?php echo $paramName; ?></strong>
										<em>&nbsp;&#8211;&nbsp;<?php echo $paramDetails['label']; ?></em>
									</p>
									<?php if ( isset( $paramDetails['description'] ) ) { ?>
									<p class="description">
										<?php echo $paramDetails['description']; ?>
									</p>
									<?php } ?>
									<p>
										<em><?php _e('Possible values:', 'motopress-hotel-booking'); ?></em>
										<strong><?php echo $paramDetails['values']; ?></strong>
									</p>
									<?php if ( isset( $paramDetails['default'] ) ) { ?>
									<p>
										<em><?php _e('Default value:', 'motopress-hotel-booking'); ?></em>
										<strong>
										<?php switch ($paramDetails['default']) {
											case '':
												echo '<em>' . __('empty string', 'motopress-hotel-booking') . '</em>';
												break;
											default:
												echo $paramDetails['default'];
												break;
										}?>
										</strong>
									</p>
									<?php }?>
									<hr/>
								<?php } ?>
							<?php } ?>
						</td>
						<td scope="row">
							<p><?php echo $details['example']['shortcode']; ?></p>
							<?php if (isset($details['example']['description'])) { ?>
								<p class="description"><?php echo $details['example']['description']; ?></p>
							<?php }?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

}
