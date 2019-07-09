<?php

class MPHBEmailTemplater {

	private $tags = array();

	/**
	 *
	 * @var MPHBBooking
	 */
	private $booking;

	public function __construct() {
		$this->setupTags();
	}

	private function setupTags(){
		$tags = array(
			array(
				'name' => 'site_title',
				'description' => __('Site title (set in Settings > General)', 'motopress-hotel-booking'),
			),
			array(
				'name'			=> 'booking_id',
				'description'	=> __( 'Booking ID', 'motopress-hotel-booking' ),
			),
			array(
				'name'			=> 'booking_edit_link',
				'description'	=> __( 'Booking Edit Link', 'motopress-hotel-booking' ),
			),
			array(
				'name'			=> 'booking_total_price',
				'description'	=> __( 'Booking Total Price', 'motopress-hotel-booking' ),
			),
			array(
				'name'			=> 'check_in_date',
				'description'	=> __( 'Check-In Date', 'motopress-hotel-booking' ),
			),
			array(
				'name'			=> 'check_out_date',
				'description'	=> __( 'Check-Out Date', 'motopress-hotel-booking' ),
			),
			array(
				'name'			=> 'check_in_time',
				'description'	=> __( 'Check-In Time', 'motopress-hotel-booking' ),
			),
			array(
				'name'			=> 'check_out_time',
				'description'	=> __( 'Check-Out Time', 'motopress-hotel-booking' ),
			),
			array(
				'name'			=> 'adults',
				'description'	=> __( 'Adults', 'motopress-hotel-booking' ),
			),
			array(
				'name'			=> 'childs',
				'description'	=> __( 'Childs', 'motopress-hotel-booking' ),
			),
			array(
				'name'			=> 'services',
				'description'	=> __( 'Services', 'motopress-hotel-booking' ),
			),
			// Customer
			array(
				'name'			=> 'customer_first_name',
				'description'	=> __( 'Customer First Name', 'motopress-hotel-booking' ),
			),
			array(
				'name'			=> 'customer_last_name',
				'description'	=> __( 'Customer Last Name', 'motopress-hotel-booking' ),
			),
			array(
				'name'			=> 'customer_email',
				'description'	=> __( 'Customer Email', 'motopress-hotel-booking' ),
			),
			array(
				'name'			=> 'customer_phone',
				'description'	=> __( 'Customer Phone', 'motopress-hotel-booking' ),
			),
			array(
				'name'			=> 'customer_note',
				'description'	=> __( 'Customer Note', 'motopress-hotel-booking' ),
			),
			// Room Type
			array(
				'name'			=> 'room_type_id',
				'description'	=> __( 'Room Type ID', 'motopress-hotel-booking' ),
			),
			array(
				'name'			=> 'room_type_link',
				'description'	=> __( 'Room Type Link', 'motopress-hotel-booking' ),
			),
			array(
				'name'			=> 'room_type_title',
				'description'	=> __( 'Room Type Title', 'motopress-hotel-booking' ),
			),
			array(
				'name'			=> 'room_type_categories',
				'description'	=> __( 'Room Type Categories', 'motopress-hotel-booking' ),
			),
			array(
				'name'			=> 'room_type_bed_type',
				'description'	=> __( 'Room Type Bed', 'motopress-hotel-booking' ),
			)
		);

		$tags = apply_filters( 'mphb_email_tags', $tags );

		foreach ( $tags as $tag ){
			$this->addTag( $tag['name'], $tag['description'] );
		}

	}

	/**
	 *
	 * @param string $name
	 * @param string $description
	 */
	public function addTag( $name, $description ){
		if ( !empty( $name ) ) {
			$this->tags[$name] = array(
				'name'			 => $name,
				'description'	 => $description,
			);
		}
	}

	/**
	 *
	 * @param string $content
	 * @param MPHBBooking $booking
	 * @return string
	 */
	public function replaceTags( $content, $booking ){

		if ( !empty( $this->tags ) ) {
			$this->booking = $booking;

			$content = preg_replace_callback( $this->_generateTagsFindString(), array( $this, 'replaceTag' ), $content );
		}

		return $content;
	}

	/**
	 *
	 * @return string
	 */
	private function _generateTagsFindString(){
//		$tagNames	 = array_column( $this->tags, 'name' );
		$tagNames = array();
		foreach ($this->tags as $tag) {
			$tagNames[] = $tag['name'];
		}

		$find	 = '/%' . join( '%|%', $tagNames ) . '%/s';
		return $find;
	}

	/**
	 *
	 * @param array $match
	 * @param string $match[0] Tag
	 *
	 * @return string
	 */
	public function replaceTag( $match ){

		$tag = str_replace( '%', '', $match[0] );

		switch ( $tag ) {

			// Global
			case 'site_title':
				$replaceText = get_bloginfo( 'name' );
				break;
			case 'check_in_time':
				$replaceText = MPHB()->getSettings()->getCheckInTimeWPFormatted();
				break;
			case 'check_out_time':
				$replaceText = MPHB()->getSettings()->getCheckOutTimeWPFormatted();
				break;

			// Booking
			case 'booking_id':
				$replaceText = $this->booking->getId();
				break;
			case 'booking_edit_link':
				$replaceText = $this->booking->getEditLink();
				break;
			case 'booking_total_price':
				ob_start();
				MPHBBookingView::renderTotalPriceHTML( $this->booking );
				$replaceText = ob_get_clean();
				break;
			case 'check_in_date':
				ob_start();
				MPHBBookingView::renderCheckInDateWPFormatted( $this->booking );
				$replaceText = ob_get_clean();
				break;
			case 'check_out_date':
				ob_start();
				MPHBBookingView::renderCheckOutDateWPFormatted( $this->booking );
				$replaceText = ob_get_clean();
				break;
			case 'adults':
				$replaceText = $this->booking->getAdults();
				break;
			case 'childs':
				$replaceText = $this->booking->getChilds();
				break;
			case 'services':
				ob_start();
				MPHBBookingView::renderServicesList( $this->booking );
				$replaceText = ob_get_clean();
				break;

			// Customer
			case 'customer_first_name':
				$replaceText = $this->booking->getCustomer()->getFirstName();
				break;
			case 'customer_last_name':
				$replaceText = $this->booking->getCustomer()->getLastName();
				break;
			case 'customer_email':
				$replaceText = $this->booking->getCustomer()->getEmail();
				break;
			case 'customer_phone';
				$replaceText = $this->booking->getCustomer()->getPhone();
				break;
			case 'customer_note':
				$replaceText = $this->booking->getNote();
				break;

			// Room Type
			case 'room_type_id':
				$replaceText = $this->booking->getRoom()->getRoomType()->getId();
				break;
			case 'room_type_link':
				$replaceText = $this->booking->getRoom()->getRoomType()->getLink();
				break;
			case 'room_type_title':
				$replaceText = $this->booking->getRoom()->getRoomType()->getTitle();
				break;
			case 'room_type_categories':
				$replaceText = $this->booking->getRoom()->getRoomType()->getCategories();
				break;
			case 'room_type_bed_type':
				$replaceText = $this->booking->getRoom()->getRoomType()->getBedType();
				break;
			default:
				$replaceText = '';
				break;
		}

		$replaceText = apply_filters( 'mphb_email_replace_tag', $replaceText, $tag );

		return $replaceText;
	}

	/**
	 *
	 * @return string
	 */
	public function getTagsDescription(){
		$description = __( 'Possible tags:', 'motopress-hotel-booking' );
		$description .= '<br/>';
		foreach ( $this->tags as $tagDetails ) {
			$description .= sprintf( '<strong>%%%s%%</strong> - %s<br/>', $tagDetails['name'], $tagDetails['description'] );
		}
		return $description;
	}

}