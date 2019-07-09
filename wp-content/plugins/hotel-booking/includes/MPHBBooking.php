<?php

// @todo simplify booking class (divided into several classes)
class MPHBBooking {

	/**
	 *
	 * @var int
	 */
	private $id;

	/**
	 *
	 * @var WP_POST
	 */
	private $post;

	/**
	 *
	 * @var MPHBRoom
	 */
	private $room;

	/**
	 *
	 * @var MPHBRoomRate
	 */
	private $roomRate;

	/**
	 *
	 * @var DateTime
	 */
	private $checkInDate;

	/**
	 *
	 * @var DateTime
	 */
	private $checkOutDate;

	/**
	 *
	 * @var int
	 */
	private $adults;

	/**
	 *
	 * @var int
	 */
	private $childs;

	/**
	 *
	 * @var MPHBCustomer
	 */
	private $customer;

	/**
	 *
	 * @var MPHBServices[]
	 */
	private $services = array();

	/**
	 *
	 * @var string
	 */
	private $note;

	/**
	 *
	 * @var float
	 */
	private $totalPrice;

	/**
	 *
	 * @var string
	 */
	private $status;

	/**
	 *
	 * @var string
	 */
	private $paymentStatus;

	/**
	 *
	 * @var WP_Error
	 */
	private $errors;

	const PAYMENT_STATUS_PAID		 = 'paid';
	const PAYMENT_STATUS_UNPAID	 = 'unpaid';

	/**
	 *
	 * @param int|WP_POST $post
	 * @param array $parameters
	 */
	public function __construct( $post = null ){

		if ( is_a( $post, 'WP_Post' ) ) {
			$this->setupPost( $post );
		} else if ( is_int( $post ) && $post > 0 ) {
			$this->setupPost( get_post( $post ) );
		} else if ( is_array( $post ) ) {
			$this->setupParameters( $post );
		} else {
			return false;
		}
	}

	/**
	 *
	 * @param WP_Post $post
	 */
	private function setupPost( $post ){
		$this->post	 = $post;
		$this->id	 = $post->ID;

		return $this->setupFromDB();
	}

	/**
	 *
	 * @return boolean
	 */
	private function setupFromDB(){

		if ( is_null( $this->id ) ) {
			return false;
		}

		$checkInDate	 = DateTime::createFromFormat( 'Y-m-d', $this->getField( 'check_in_date' ) );
		$checkOutDate	 = DateTime::createFromFormat( 'Y-m-d', $this->getField( 'check_out_date' ) );

		$adults	 = absint( $this->getField( 'adults' ) );
		$childs	 = absint( $this->getField( 'childs' ) );

		$note = $this->getField( 'note' );

		$roomId	 = $this->getField( 'room_id' );
		$rateId	 = $this->getField( 'room_rate_id' );

		$room		 = new MPHBRoom( $roomId );
		$roomType	 = new MPHBRoomType( $room->getRoomTypeId() );
		$roomRate	 = $roomType->getRates()->getRate( $rateId );

		$servicesArr = $this->getField( 'services' );
		if ( $servicesArr === '' ) {
			$servicesArr = array();
		}
		$services = array();
		foreach ( $servicesArr as $key => $serviceDetails ) {
			$service	 = new MPHBService( $serviceDetails['id'] );
			$service->setAdults( $serviceDetails['count'] );
			$services[]	 = $service;
		}

		$customerDetails = array(
			'email'		 => $this->getField( 'email' ),
			'first_name' => $this->getField( 'first_name' ),
			'last_name'	 => $this->getField( 'last_name' ),
			'phone'		 => $this->getField( 'phone' )
		);

		$customer = new MPHBCustomer( $customerDetails );

		$paymentStatus = $this->getField( 'payment_status' );

		$status = get_post_status( $this->id );

		$totalPrice = floatval( $this->getField( 'total_price' ) );

		$parameters = array(
			'room'			 => $room,
			'room_rate'		 => $roomRate,
			'adults'		 => $adults,
			'childs'		 => $childs,
			'check_in_date'	 => $checkInDate,
			'check_out_date' => $checkOutDate,
			'services'		 => $services,
			'customer'		 => $customer,
			'total_price'	 => $totalPrice,
			'payment_status' => $paymentStatus,
			'status'		 => $status,
			'note'			 => $note
		);

		return $this->setupParameters( $parameters );
	}

	/**
	 *
	 * @param array	$parameters
	 * @param MPHBRoom $parameters['room']
	 * @param MPHBRoomRate $parameters['room_rate']
	 * @param int $parameters['adults']
	 * @param int $parameters['childs']
	 * @param DateTime $parameters['check_in_date']
	 * @param DateTime $parameters['check_out_date']
	 * @param MPHBService[] $parameters['services']
	 * @param MPHBCustomer $parameters['customer']
	 * @param float $parameters['total_price']
	 * @param string $parameters['note']
	 *
	 * @return boolean
	 */
	public function setupParameters( $parameters = array() ){

		$this->errors = new WP_Error();

		if ( isset( $parameters['room'] ) && is_a( $parameters['room'], 'MPHBRoom' ) ) {
			$this->room = $parameters['room'];
		} else {
			$this->errors->add( 'room_not_set', __( 'Room is not set.', 'motopress-hotel-booking' ) );
		}

		if ( isset( $parameters['room_rate'] ) && is_a( $parameters['room_rate'], 'MPHBRoomRate' ) ) {
			$this->roomRate = $parameters['room_rate'];
		} else {
			$this->errors->add( 'room_rate_not_set', __( 'Room Rate is not set.', 'motopress-hotel-booking' ) );
		}

		if ( isset( $parameters['check_in_date'] ) && isset( $parameters['check_out_date'] ) && is_a($parameters['check_in_date'], 'DateTime') && is_a($parameters['check_out_date'], 'DateTime') ) {
			$this->setDates( $parameters['check_in_date'], $parameters['check_out_date'] );
		} else {
			$this->errors->add( 'dates_not_set', __( 'Dates are not set.', 'motopress-hotel-booking' ) );
		}

		if ( isset( $parameters['adults'] ) ) {
			$childs = isset( $parameters['childs'] ) ? $parameters['childs'] : 0;
			$this->setGuests( $parameters['adults'], $childs );
		}

		if ( isset( $parameters['services'] ) && !empty( $parameters['services'] ) ) {
			$this->services = $parameters['services'];
		}

		if ( isset( $parameters['customer'] ) ) {
			$this->customer = $parameters['customer'];
		} else {
			$this->errors->add( 'customer_not_set', __( 'Customer is not set.', 'motopress-hotel-booking' ) );
		}

		$this->paymentStatus = isset( $parameters['payment_status'] ) ? $parameters['payment_status'] : self::PAYMENT_STATUS_UNPAID;

		$this->status = isset( $parameters['status'] ) ? $parameters['status'] : MPHBBookingCPT::STATUS_PENDING;

		if ( isset( $parameters['note'] ) ) {
			$this->note = $parameters['note'];
		}

		if ( isset( $parameters['total_price'] ) ) {
			$this->totalPrice = $parameters['total_price'];
		} else {
			$this->updateTotal();
		}

		if ( $this->errors->get_error_code() ) {
			return false;
		}

		return true;
	}

	/**
	 *
	 * @return WP_Error|false
	 */
	function getErrors(){
		return $this->errors->get_error_code() ? $this->errors : false;
	}

	/**
	 *
	 * @param DateTime $checkInDate
	 * @param DateTime $checkOutDate
	 */
	public function setDates( DateTime $checkInDate, DateTime $checkOutDate ){
		$this->checkInDate	 = $checkInDate;
		$this->checkOutDate	 = $checkOutDate;
	}

	/**
	 *
	 * @param MPHBRoom $room
	 * @param MPHBRoomType $roomRate
	 */
	public function setRoom( MPHBRoom $room, MPHBRoomRate $roomRate ){
		$this->room		 = $room;
		$this->roomRate	 = $roomRate;
	}

	/**
	 *
	 * @param MPHBService[] $services
	 */
	public function setServices( $services ){
		foreach ( $services as $service ) {
			$this->addService( $service );
		}
	}

	/**
	 *
	 * @param MPHBService $service
	 */
	public function addService( MPHBService $service ){
		$this->services[] = $service;
	}

	/**
	 *
	 * @param MPHBCustomer $customer
	 */
	public function setCustomer( MPHBCustomer $customer ){
		$this->customer = $customer;
	}

	/**
	 *
	 * @param int $adults
	 * @param int $childs Optional. Default 0.
	 */
	public function setGuests( $adults, $childs = 0 ){
		$this->adults	 = $adults;
		$this->childs	 = $childs;
	}

	/**
	 *
	 * @param string $paymentStatus
	 */
	public function setPaymentStatus( $paymentStatus ){
		$this->paymentStatus = $paymentStatus;
	}

	/**
	 *
	 * @param string $status
	 */
	public function setStatus( $status ){
		$this->status = $status;
	}

	/**
	 *
	 */
	public function updateTotalPrice(){
		$this->totalPrice = $this->calculateTotalPrice();
	}

	/**
	 *
	 * @return int|false The post ID on success. False on failure.
	 */
	public function save(){

		if ( ! $this->isCanSave() ) {
			return false;
		}

		if ( !isset( $this->id ) ) {

			$postAttrs = array(
				'post_type'		 => MPHB()->getBookingCPT()->getPostType(),
				'post_status'	 => $this->status
			);

			$postId = wp_insert_post( $postAttrs );

			if ( !$postId ) {
				return false;
			}

			$this->id	 = $postId;
			$this->post	 = get_post( $this->id );
		}

		$services = array();
		foreach ( $this->services as $service ) {
			$services[] = array(
				'id'	 => $service->getId(),
				'count'	 => $service->getAdults()
			);
		}

		$postMetas = array(
			'room_id'		 => $this->room->getId(),
			'room_rate_id'	 => $this->roomRate->getId(),
			'check_in_date'	 => $this->checkInDate->format( 'Y-m-d' ),
			'check_out_date' => $this->checkOutDate->format( 'Y-m-d' ),
			'adults'		 => $this->adults,
			'childs'		 => $this->childs,
			'note'			 => $this->note,
			'email'			 => $this->customer->getEmail(),
			'first_name'	 => $this->customer->getFirstName(),
			'last_name'		 => $this->customer->getLastName(),
			'phone'			 => $this->customer->getPhone(),
			'payment_status' => $this->paymentStatus,
			'services'		 => $services,
			'total_price'	 => $this->totalPrice
		);

		foreach ( $postMetas as $postMetaName => $postMetaValue ) {
			$this->setField( $postMetaName, $postMetaValue );
		}

		if ( get_post_status( $this->id ) !== $this->status ) {
			wp_update_post( array(
				'ID' => $this->id,
				'post_status' => $this->status
			) );
		}

		return $this->id;
	}

	public function updateTotal(){
		$this->totalPrice = $this->calculateTotalPrice();
	}

	/**
	 * Verifies that all required fields are set and correct
	 *
	 * @return boolean
	 */
	public function isCanSave(){

		return !is_null( $this->room ) && !is_null( $this->roomRate ) &&
			!is_null( $this->customer ) && $this->customer->isValid() &&
			!is_null( $this->checkInDate ) && !is_null( $this->checkOutDate ) &&
			!is_null( $this->adults ) && !is_null( $this->childs ) &&
			!is_null( $this->totalPrice ) &&
			!is_null( $this->paymentStatus ) &&
			!is_null( $this->status );
	}

	/**
	 *
	 * @return array
	 */
	public function getPriceBreakdown(){
		$roomPriceBreakdown	 = $this->roomRate->getPriceBreakdown( $this->checkInDate, $this->checkOutDate );
		$roomTotal			 = $this->roomRate->calcTotalPrice( $this->checkInDate, $this->checkOutDate );

		$servicesBreakdown = array(
			'list'	 => array(),
			'total'	 => 0.0
		);
		foreach ( $this->services as $service ) {
			$serviceTotal = $service->calcPrice( $this->checkInDate, $this->checkOutDate );

			$servicesBreakdown['list'][] = array(
				'title'		 => $service->getTitle(),
				'details'	 => $service->generatePriceDetailsString( $this->checkInDate, $this->checkOutDate ),
				'total'		 => $serviceTotal,
			);
			$servicesBreakdown['total'] += $serviceTotal;
		}

		return array(
			'room'		 => array(
				'title'	 => $this->roomRate->getTitle(),
				'list'	 => $roomPriceBreakdown,
				'total'	 => $roomTotal
			),
			'services'	 => $servicesBreakdown,
			'total'		 => $this->calculateTotalPrice()
		);
	}

	/**
	 *
	 * @return float
	 */
	public function calculateTotalPrice(){

		$price = 0.0;

		if ( is_null( $this->checkInDate ) || is_null( $this->checkOutDate ) ) {
			return $price;
		}

		if ( !is_null( $this->roomRate ) ) {
			$price += $this->roomRate->calcTotalPrice( $this->checkInDate, $this->checkOutDate );
		}

		if ( !is_null( $this->services ) && !empty( $this->services ) ) {
			foreach ( $this->services as $service ) {
				$price += $service->calcPrice( $this->checkInDate, $this->checkOutDate );
			}
		}

		$price = apply_filters( 'mphb_booking_calculate_total_price', $price, $this );

		return $price;
	}

	/**
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return bool
	 */
	public function setField( $name, $value ){
		return update_post_meta( $this->id, MPHB()->addPrefix( $name, '_' ), $value );
	}

	/**
	 * Retrieve field value. Empty string will returns if field is not setted.
	 *
	 * @param string $fieldName
	 * @return string
	 */
	public function getField( $fieldName ){
		return get_post_meta( $this->id, MPHB()->addPrefix( $fieldName, '_' ), true );
	}

	/**
	 *
	 * @param string $message
	 * @param int $author
	 */
	public function addLog( $message, $author = null ){
		$author = !is_null( $author ) ? $author : ( is_admin() ? get_current_user_id() : 0);

		$commentdata = array(
			'comment_post_ID'		 => $this->getID(),
			'comment_content'		 => $message,
			'user_id'				 => $author,
			'comment_date'			 => mphb_current_time( 'mysql' ),
			'comment_date_gmt'		 => mphb_current_time( 'mysql', get_option( 'gmt_offset' ) ),
			'comment_approved'		 => 1,
			'comment_parent'		 => 0,
			'comment_author'		 => '',
			'comment_author_IP'		 => '',
			'comment_author_url'	 => '',
			'comment_author_email'	 => '',
			'comment_type'			 => 'mphb_booking_log'
		);

		wp_insert_comment( $commentdata );
	}

	public function getRoomLink(){
		return $this->room->getLink();
	}

	public function getLogs(){

		do_action( 'mphb_booking_before_get_logs' );

		$logs = get_comments( array(
			'post_id'	 => $this->getID(),
			'order'		 => 'ASC'
			) );

		do_action( 'mphb_booking_after_get_logs' );

		return $logs;
	}

	public function getEditLink(){
		$link = '';

		$post_type_object = get_post_type_object( MPHB()->getBookingCPT()->getPostType() );

		if ( $post_type_object && $post_type_object->_edit_link ) {
			$action	 = '&action=edit';
			$link	 = admin_url( sprintf( $post_type_object->_edit_link . $action, $this->id ) );
		}

		return $link;
	}

	/**
	 *
	 * @return int
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 *
	 * @return WP_POST
	 */
	public function getPost(){
		return $this->post;
	}

	/**
	 *
	 * @return MPHBRoom
	 */
	public function getRoom(){
		return $this->room;
	}

	/**
	 *
	 * @return MPHBRoomRate
	 */
	public function getRoomRate(){
		return $this->roomRate;
	}

	/**
	 *
	 * @return DateTime
	 */
	public function getCheckInDate(){
		return $this->checkInDate;
	}

	/**
	 *
	 * @return DateTime
	 */
	public function getCheckOutDate(){
		return $this->checkOutDate;
	}

	/**
	 *
	 * @return int
	 */
	public function getAdults(){
		return $this->adults;
	}

	/**
	 *
	 * @return int
	 */
	public function getChilds(){
		return $this->childs;
	}

	/**
	 *
	 * @return MPHBCustomer
	 */
	public function getCustomer(){
		return $this->customer;
	}

	/**
	 *
	 * @return MPHBService[]
	 */
	public function getServices(){
		return $this->services;
	}

	/**
	 *
	 * @return string
	 */
	public function getNote(){
		return $this->note;
	}

	/**
	 *
	 * @return float
	 */
	public function getTotalPrice(){
		return $this->totalPrice;
	}

	/**
	 *
	 * @return string
	 */
	public function getStatus(){
		return $this->status;
	}

	/**
	 *
	 * @return string
	 */
	public function getPaymentStatus(){
		return $this->paymentStatus;
	}

	/**
	 * Retrieve label of payment status.
	 *
	 * @return string
	 */
	public function getPaymentStatusLabel(){
		$statuses = MPHB()->getBookingCPT()->getPaymentStatuses();
		return isset( $statuses[$this->status] ) ? $statuses[$this->status] : '';
	}

	/**
	 *
	 * @return array of dates where key is date in 'Y-m-d' format and value is date in frontend date format
	 */
	public function getDates( $fromToday = false ){

		$fromDate	 = $this->checkInDate->format( 'Y-m-d' );
		$toDate		 = $this->checkOutDate->format( 'Y-m-d' );

		if ( $fromToday ) {
			$today		 = mphb_current_time( 'Y-m-d' );
			$fromDate	 = $fromDate >= $today ? $fromDate : $today;
		}
		//@todo return DatePeriod
		return mphbCreateDateRangeArray( $fromDate, $toDate );
	}

}
