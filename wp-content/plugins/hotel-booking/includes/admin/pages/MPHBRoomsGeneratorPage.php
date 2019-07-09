<?php
class MPHBRoomsGeneratorPage extends MPHBAdminPage{
	
	protected $name = 'mphb_rooms_generator';
	private $hookSuffix;
	
	const NONCE_NAME = 'mphb-generate-rooms-nonce';
	const NONCE_ACTION_GENERATE = 'mphb-generate-rooms';		

	public function createMenu(){		
		$this->hookSuffix = add_submenu_page(MPHB()->getRoomTypeCPT()->getMenuSlug()
			, __('Rooms Generator', 'motopress-hotel-booking')
			, __('Rooms Generator', 'motopress-hotel-booking')
			, 'edit_posts'
			, $this->name
			, array($this, 'renderPage')
		);
		add_action('load-' . $this->hookSuffix, array($this, 'save'));
	}

	public function renderPage(){
		$this->showNotices();
		$roomTypeId = isset($_GET['mphb_room_type_id']) ? $_GET['mphb_room_type_id'] : '';
		?>
		<h1><?php _e('Generate Rooms', 'motopress-hotel-booking'); ?></h1>
		<form method="POST">
		<?php wp_nonce_field(self::NONCE_ACTION_GENERATE, self::NONCE_NAME); ?>
		<table class="form-table">
			<tbody>				
				<tr>
					<th>
						<label><?php _e('Number of rooms', 'motopress-hotel-booking'); ?></label>
					</th>
					<td>
						<input name="mphb_rooms_count" type="number" class="small-text" required="required" min="1" step="1" value="1"/>
					</td>					
				</tr>
				<tr>
					<th>
						<label><?php _e('Room Type', 'motopress-hotel-booking'); ?></label>
					</th>
					<td>
						<select name="mphb_room_type_id">
							<option value=""><?php _e('— Select —', 'motopress-hotel-booking'); ?></option>
						<?php
						$roomTypes = MPHB()->getRoomTypeCPT()->getRoomTypesList();
						foreach ( $roomTypes as $id => $title ) {
						?>
							<option value="<?php echo $id; ?>" <?php selected($roomTypeId, $id); ?>><?php echo $title; ?></option>
						<?php
						}

						?>
						</select>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php _e('Title Prefix', 'motopress-hotel-booking'); ?></label>
					</th>
					<td>
						<input name="mphb_room_title_prefix" type="text" class="regular-text" />
						<p class="description"><?php _e('Optional. Leave empty for use auto generated prefix.', 'motopress-hotel-booking'); ?></p>
					</td>
				</tr>
			</tbody>
		</table>
		<?php submit_button(__('Generate', 'motoress-hotel-booking'))?>
		</form>
		<?php
	}

	public function showNotices(){
		if ( isset($_GET['mphb-rooms-generated']) ){
			$number = isset( $_GET['mphb-rooms-count'] ) ? absint( $_GET['mphb-rooms-count'] ) : 0;
			$message = sprintf( _n( 'Room generated.', '%s rooms generated.', $number, 'motopress-hotel-booking' ), number_format_i18n( $number ) );
			$linkArgs = array(
				'orderby' => 'date',
				'order' => 'desc'
			);
			if ( isset($_GET['mphb_room_type_id']) ) {
				$linkArgs['mphb_room_type_id'] = $_GET['mphb_room_type_id'];
			}
			$viewUrl = MPHB()->getRoomCPT()->getManagePostsLink($linkArgs);
			$message .= ' ' . sprintf('<a href="%s">%s</a>', $viewUrl, __('View', 'motopress-hotel-booking'));
			echo '<div class="updated"><p>' . $message . '</p></div>';
		}
	}

	public function save(){		

		if ( $this->checkSaveNonce() ) {
			$roomTypeId = isset($_POST['mphb_room_type_id']) && absint($_POST['mphb_room_type_id']) > 0 ? absint($_POST['mphb_room_type_id']) : '';
			$roomsCount = isset($_POST['mphb_rooms_count']) && absint($_POST['mphb_rooms_count']) > 0 ? absint($_POST['mphb_rooms_count']) : 1;
			$titlePrefix = isset($_POST['mphb_room_title_prefix']) && !empty($_POST['mphb_room_title_prefix']) ? sanitize_title($_POST['mphb_room_title_prefix']) : false;
			$generated = MPHB()->getRoomCPT()->generateRooms($roomsCount, $roomTypeId, $titlePrefix);
			if ($generated) {

				$sendbackArgs = array(					
					'mphb-rooms-generated' => true,
					'mphb-rooms-count' => $roomsCount,
				);

				if ($roomTypeId !== '') {
					$sendbackArgs['mphb_room_type_id'] = $roomTypeId;
				}

				$sendbackUrl = $this->getUrl();
				$sendback = add_query_arg($sendbackArgs, $sendbackUrl);

				wp_redirect( esc_url_raw( $sendback ) );
				exit();
			}
		}
	}

	public function checkSaveNonce(){
		return isset($_POST[self::NONCE_NAME]) && wp_verify_nonce($_POST[self::NONCE_NAME], self::NONCE_ACTION_GENERATE);
	}

	public function getUrl( $additionalArgs = array() ){
		$adminUrl = admin_url('edit.php');
		$args = array_merge(array(
			'page' => $this->getName(),
			'post_type' => MPHB()->getRoomTypeCPT()->getPostType()
		), $additionalArgs);
		$url = add_query_arg($args, $adminUrl);
		return $url;
	}

}
