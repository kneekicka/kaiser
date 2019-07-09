<?php

class MPHBImporter {

	private $processedPosts = array();
	private $importedPosts = array();
	private $importProcess = false;

	public function __construct(){
		add_action('import_end', array($this, 'backfillPostMeta'), 10);
		add_action('import_start', array($this, 'startImportProcess'));
		add_action('import_end', array($this, 'endImportProcess'), 20);
	}

	public function startImportProcess(){
		$this->importProcess = true;
	}

	public function endImportProcess(){
		$this->importProcess = false;
	}

	/**
	 *
	 * @return boolean
	 */
	public function isImportProcess(){
		return $this->importProcess;
	}

	/**
	 *
	 * @global WP_Import $wp_import
	 */
	public function backfillPostMeta(){
		global $wp_import;

		if ( is_a($wp_import, 'WP_Import') ) {

			$this->processedPosts = $wp_import->processed_posts;
			$this->importedPosts = array_keys($wp_import->processed_posts);

			$this->backfillBookingsMeta();
			$this->backfillRoomsMeta();
			$this->backfillRoomTypesMeta();

		}

	}

	public function backfillRoomTypesMeta(){
		foreach ($this->importedPosts as $postID) {
			if ( get_post_type( $postID ) === MPHB()->getRoomTypeCPT()->getPostType() ) {

				// Fix ids gallery images ids
				$gallery = get_post_meta($postID, 'mphb_gallery', true);
				if ( !empty($gallery) ) {
					$gallery = explode(',', $gallery);
					$fixedGallery = array();
					foreach ($gallery as $imageId) {
						if (isset($this->processedPosts[$imageId])) {
							$fixedGallery[] = $this->processedPosts[$imageId];
						}
					}
					update_post_meta($postID, 'mphb_gallery', implode(',', $fixedGallery));
				}

				// Fix services ids
				$services = get_post_meta($postID, 'mphb_services', true);
				if ( !empty($services) ) {
					$fixedServices = array();
					foreach ($services as $serviceId) {
						if (isset($this->processedPosts[$serviceId])) {
							$fixedServices[] = $this->processedPosts[$serviceId];
						}
					}
					update_post_meta($postID, 'mphb_services', $fixedServices);
				}

			}
		}
	}

	public function backfillRoomsMeta(){
		foreach ($this->importedPosts as $postID) {
			if ( get_post_type( $postID ) === MPHB()->getRoomCPT()->getPostType() ) {

				// Fix Room Type ID
				$roomTypeId = get_post_meta($postID ,'mphb_room_type_id', true);
				$fixedRoomTypeId = isset($this->processedPosts[$roomTypeId]) ? $this->processedPosts[$roomTypeId] : '';
				update_post_meta($postID, 'mphb_room_type_id', $fixedRoomTypeId);

			}
		}
	}

	public function backfillBookingsMeta(){
		foreach ($this->importedPosts as $postID) {
			if ( get_post_type( $postID ) === MPHB()->getBookingCPT()->getPostType() ) {

				// Fix Room Id
				$roomID = get_post_meta($postID, 'mphb_room_id', true);
				if (!empty($roomID)) {
					if (isset($this->processedPosts[$roomID])) {
						update_post_meta($postID, 'mphb_room_id', $this->processedPosts[$roomID]);
					}
				}

				// Fix Services Ids
				$services = get_post_meta($postID, 'mphb_services', true);
				if ( !empty($services) ) {
					foreach ($services as &$serviceDetails) {
						if (isset($serviceDetails['id']) && isset($this->processedPosts[$serviceDetails['id']])) {
							$serviceDetails['id'] = $this->processedPosts[$serviceDetails['id']];
						}
					}
					update_post_meta($postID, 'mphb_services', $services);
				}
			}
		}
	}

}
