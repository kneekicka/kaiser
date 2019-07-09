<?php
/**
 * Customer pending booking email
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php _e('Dear %customer_first_name% %customer_last_name%, your reservation is pending now!', 'motopress-hotel-booking'); ?>
<br/>
<?php _e('We will send confirmation by email.', 'motopress-hotel-booking'); ?>
<br/>
<h4><?php _e('Details of booking:', 'motopress-hotel-booking')?></h4>
<?php _e('ID: #%booking_id%', 'motopress-hotel-booking'); ?>
<br/>
<?php _e('Check-In Date: %check_in_date%', 'motopress-hotel-booking'); ?>
<br/>
<?php _e('Check-Out Date: %check_out_date%', 'motopress-hotel-booking'); ?>
<br/>
<?php _e('Adults: %adults%', 'motopress-hotel-booking'); ?>
<br/>
<?php _e('Childs: %childs%', 'motopress-hotel-booking'); ?>
<br/>
<?php _e('Room: <a href="%room_type_link%">%room_type_title%</a>', 'motopress-hotel-booking'); ?>
<br/>
<h4><?php _e('Additional Services', 'motopress-hotel-booking'); ?></h4>
%services%
<br/>
<?php _e('Bed Type: %room_type_bed_type%', 'motopress-hotel-booking'); ?>
<br/>
<?php _e('Total Price: %booking_total_price%', 'motopress-hotel-booking')?>
<br/>
<h4><?php _e('Customer Info', 'motopress-hotel-booking'); ?></h4>
<?php _e('Name: %customer_first_name% %customer_last_name%', 'motopress-hotel-booking')?>
<br/>
<?php _e('Email: %customer_email%', 'motopress-hotel-booking')?>
<br/>
<?php _e('Phone: %customer_phone%', 'motopress-hotel-booking')?>
<br/>
<?php _e('Note: %customer_note%', 'motopress-hotel-booking')?>
<br/>
<?php _e('Thank you!', 'motopress-hotel-booking'); ?>