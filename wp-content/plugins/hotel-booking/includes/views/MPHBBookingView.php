<?php

class MPHBBookingView {

	public static function renderPriceBreakdown( MPHBBooking $booking ){
		echo self::generatePriceBreakdown( $booking );
	}

	public static function generatePriceBreakdown( MPHBBooking $booking ){
		$priceBreakdown = $booking->getPriceBreakdown();
		ob_start();
		if ( !empty( $priceBreakdown ) ) :
			?>
			<table class="mphb-price-breakdown">
				<tbody>
					<?php if ( isset( $priceBreakdown['room'] ) ) : ?>
						<tr>
							<th colspan="3"><?php printf( __( 'Room ( %s )', 'motopress-hotel-booking' ), $priceBreakdown['room']['title'] ); ?></th>
						</tr>
						<tr>
							<th colspan="2"><?php _e( 'Date', 'motopress-hotel-booking' ); ?></th>
							<th><?php _e( 'Price', 'motopress-hotel-booking' ); ?></th>
						</tr>
						<?php foreach ( $priceBreakdown['room']['list'] as $date => $datePrice ) : ?>
							<tr>
								<td colspan="2"><?php echo MPHBUtils::convertDateToWPFront( DateTime::createFromFormat( 'Y-m-d', $date ) ); ?></td>
								<td><?php echo mphb_format_price( $datePrice ); ?></td>
							</tr>
						<?php endforeach; ?>
						<tr>
							<th colspan="2"><?php _e( 'Room Subtotal', 'motopress-hotel-booking' ); ?></th>
							<th><?php echo mphb_format_price( $priceBreakdown['room']['total'] ); ?></th>
						</tr>
					<?php endif; ?>
					<?php if ( isset( $priceBreakdown['services'] ) && !empty( $priceBreakdown['services']['list'] ) ) : ?>
						<tr>
							<th colspan="3">
								<?php _e( 'Services', 'motopress-hotel-booking' ); ?>
							</th>
						</tr>
						<tr>
							<th><?php _e( 'Title', 'mototpress-hotel-booking' ); ?></th>
							<th><?php _e( 'Details', 'motopress-hotel-booking' ); ?></th>
							<th><?php _e( 'Price', 'motopress-hotel-booking' ); ?></th>
						</tr>
						<?php foreach ( $priceBreakdown['services']['list'] as $serviceDetails ) : ?>
							<tr>
								<td><?php echo $serviceDetails['title']; ?></td>
								<td><?php echo $serviceDetails['details']; ?></td>
								<td><?php echo mphb_format_price( $serviceDetails['total'] ); ?></td>
							</tr>
						<?php endforeach; ?>
						<tr>
							<th colspan="2">
								<?php _e( 'Services Subtotal', 'motopress-hotel-booking' ); ?>
							</th>
							<th>
								<?php echo mphb_format_price( $priceBreakdown['services']['total'] ); ?>
							</th>
						</tr>
					<?php endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="2">
							<?php _e( 'Total', 'motopress-hotel-booking' ); ?>
						</th>
						<th>
							<?php
							echo mphb_format_price( $priceBreakdown['total'] );
							?>
						</th>
					</tr>
				</tfoot>
			</table>
			<?php
		endif;
		return ob_get_clean();
	}

	public static function renderServicesList( MPHBBooking $booking ){
		$services = $booking->getServices();
		if ( !empty( $services ) ) {
			?>
			<ul class="mphb-services-list">
				<?php
				foreach ( $services as $service ) {
					?>
					<li><?php echo $service->getTitle(); ?>
						<?php
						if ( $service->isPayPerAdult() ) {
							printf( _n( 'for %d adult', 'for %d adults', $service->getAdults(), 'motopress-hotel-booking' ), $service->getAdults() );
						}
						?>
					</li>
					<?php
				}
				?>
			</ul>
			<?php
		}
	}

	public static function renderCheckInDateWPFormatted( MPHBBooking $booking ){
		echo $booking->getCheckInDate()->format( MPHB()->getSettings()->getDateFormatWP() );
	}

	public static function renderCheckOutDateWPFormatted( MPHBBooking $booking ){
		echo $booking->getCheckOutDate()->format( MPHB()->getSettings()->getDateFormatWP() );
	}

	public static function renderTotalPriceHTML( MPHBBooking $booking ){
		echo mphb_format_price( $booking->getTotalPrice() );
	}

}
