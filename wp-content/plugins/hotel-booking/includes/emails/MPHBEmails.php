<?php

class MPHBEmails {

	/**
	 *
	 * @var MPHBCustomerPendingEmail
	 */
	private $customerPending;

	/**
	 *
	 * @var MPHBAdminPendingEmail
	 */
	private $adminPending;

	/**
	 *
	 * @var MPHBCustomerApprovedEmail
	 */
	private $customerApproved;

	/**
	 *
	 * @var MPHBCustomerCancelledEmail
	 */
	private $customerCancelled;

	public function __construct(){

		$this->adminPending = new MPHBAdminEmail( array(
			'id'					 => 'admin_pending_booking',
			'label'					 => __( 'Pending Booking', 'motopress-hotel-booking' ),
			'description'			 => __( 'Email that will be sended to administrator after booking.', 'motopress-hotel-booking' ),
			'default_subject'		 => __( '%site_title% - New booking #%booking_id%', 'motopress-hotel-booking' ),
			'default_header_text'	 => __( 'New booking is pending.', 'motopress-hotel-booking' )
		) );

		$this->customerPending = new MPHBCustomerEmail( array(
			'id'					 => 'customer_pending_booking',
			'label'					 => __( 'Pending Booking', 'motopress-hotel-booking' ),
			'description'			 => __( 'Email template that will be sended to customer after booking. <strong>Only on manual confirmation mode.</strong>', 'motopress-hotel-booking' ),
			'default_subject'		 => __( '%site_title% - New booking #%booking_id%', 'motopress-hotel-booking' ),
			'default_header_text'	 => __( 'New booking is pending.', 'motopress-hotel-booking' )
		) );

		$this->customerApproved	 = new MPHBCustomerEmail( array(
			'id'					 => 'customer_approved_booking',
			'label'					 => __( 'Approved Booking', 'motopress-hotel-booking' ),
			'description'			 => __( 'Email template that will be sended to customer when booking is approved.', 'motopress-hotel-booking' ),
			'default_subject'		 => __( '%site_title% - Booking #%booking_id% Approved', 'motopress-hotel-booking' ),
			'default_header_text'	 => __( 'Booking Approved', 'motopress-hotel-booking' ),
		) );

		$this->customerCancelled = new MPHBCustomerEmail( array(
			'id'					 => 'customer_cancelled_booking',
			'label'					 => __( 'Cancelled Booking', 'motopress-hotel-booking' ),
			'description'			 => __( 'Email template that will be sended to customer when booking is cancelled.', 'motopress-hotel-booking' ),
			'default_subject'		 => __( '%site_title% - Booking #%booking_id% Cancelled', 'motopress-hotel-booking' ),
			'default_header_text'	 => __( 'Booking Cancelled', 'motopress-hotel-booking' )
		) );
	}

	/**
	 *
	 * @return MPHBCustomerPendingEmail
	 */
	function getCustomerPending(){
		return $this->customerPending;
	}

	/**
	 *
	 * @return MPHBAdminPendingEmail
	 */
	function getAdminPending(){
		return $this->adminPending;
	}

	/**
	 *
	 * @return MPHBCustomerApprovedEmail
	 */
	function getCustomerApproved(){
		return $this->customerApproved;
	}

	/**
	 *
	 * @return MPHBCustomerCancelledEmail
	 */
	function getCustomerCancelled(){
		return $this->customerCancelled;
	}

}