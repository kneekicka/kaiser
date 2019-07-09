<?php
/**
 * Focal point
 *
 * @package classes/models
 */

namespace tm_photo_gallery\classes\models;

use tm_photo_gallery\classes\lib\FB;
use tm_photo_gallery\classes\Model;
use tm_photo_gallery\classes\structure\Point;
use tm_photo_gallery\classes\structure\Size;

/**
 * Focal point class
 */
class Focal_point extends Model {

	/**
	 * Instance
	 *
	 * @var type
	 */
	protected static $instance;

	/**
	 * Get instance
	 *
	 * @return type
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Crop image by focal poin and view size
	 *
	 * @global type $_wp_additional_image_sizes
	 * @param type $id
	 * @param type $size
	 * @param type $source_type
	 * @param type $filePath
	 * @return type
	 */
	public function crop_image( $id, $size ) {
		$filePath	 = get_attached_file( $id );
		global $_wp_additional_image_sizes;
		$result		 = false;
		$viewWidth	 = $_wp_additional_image_sizes[ $size ]['width'];
		$viewHeight	 = $_wp_additional_image_sizes[ $size ]['height'];
		// get wp image object
		$image		 = wp_get_image_editor( $filePath );
		if ( ! is_wp_error( $image ) ) {
			$imd_size = $image->get_size();
			if ( $imd_size['width'] > $viewWidth || $imd_size['height'] > $viewHeight ) {
				$thumbPath	 = $this( 'image' )->get_thumbnail_path( $id, $size );
				$metadata	 = wp_get_attachment_metadata( $id );
				// get focal point position
				$focal_point = $this->get_post_meta( $id, 'focal_point', true );
				$org_prop	 = $imd_size['width'] / $imd_size['height'];
				if ( 0 == $viewHeight || 0 == $viewWidth ) {
					$focal_point = null;
				} else {
					$view_prop = $viewWidth / $viewHeight;
				}
				if ( ! $focal_point || ($org_prop == $view_prop) ) {
					$image->resize( $viewWidth, $viewHeight, true );
				} else {
					if ( $view_prop > $org_prop ) {
						$newWidth	 = $imd_size['width'];
						$newHeight	 = (int) ($newWidth * $view_prop);

						$startLeft	 = 0;
						$startTop	 = (int) ($focal_point['yPos'] - ($newHeight / 2));
						if ( $startTop < 0 ) {
							$startTop = 0;
						}
						if ( $startTop + $newHeight > $imd_size['height'] ) {
							$startTop = $imd_size['height'] - $newHeight;
						}
					} else {
						$newHeight = $imd_size['height'];

						$newWidth = (int) ($newHeight * $view_prop);

						$startTop	 = 0;
						$startLeft	 = (int) ($focal_point['xPos'] - ($newWidth / 2));
						if ( $startLeft < 0 ) {
							$startLeft = 0;
						}
						if ( $startLeft + $newWidth > $imd_size['width'] ) {
							$startLeft = $imd_size['width'] - $newWidth;
						}
					}
					// crop image
					$image->crop( $startLeft, $startTop, $newWidth, $newHeight, false, false, false );
					$image->resize( $viewWidth, $viewHeight, true );
				}
				// set image quality
				$image->set_quality( 80 );
				// save image
				$save_data = $image->save( $thumbPath );
				if ( ! is_wp_error( $save_data ) ) {
					unset( $save_data['path'] );
					// update attachment meta data
					$metadata['sizes'][ $size ]	 = $save_data;
					$result						 = wp_update_attachment_metadata( $id, $metadata );
				} else {
					FB::warn( $save_data, 'error' );
				}
			}
		}
		return $result;
	}

	/**
	 * Save new focal point
	 *
	 * @param $params
	 */
	public function post_focal_point( $params ) {
		$success		 = false;
		unset( $params['action'] );
		unset( $params['controller'] );
		unset( $params[ self::PREFIX . 'action' ] );
		// callculate new point
		$point			 = new Point( esc_attr( $params['x'] ), esc_attr( $params['y'] ) );
		$size			 = new Size( esc_attr( $params['width'] ), esc_attr( $params['height'] ) );
		$new_point		 = $this->calculate_focal_point( intval( $params['id'] ), $point, $size );
		$params['xPos']	 = $new_point->X;
		$params['yPos']	 = $new_point->Y;
		// delate all amages
		$this( 'image' )->delete_thumbnails( intval( $params['id'] ) );
		// update meta data
		$this->update_post_meta( intval( $params['id'] ), 'focal_point', $params );
		$filePath		 = get_attached_file( intval( $params['id'] ) );
		// save new copy img
		global $_wp_additional_image_sizes;
		foreach ( $_wp_additional_image_sizes as $key => $size ) {
			if ( $key != 'copy' && $size ) {
				unset( $_wp_additional_image_sizes[ $key ] );
			}
		}
		wp_update_attachment_metadata(
			intval( $params['id'] ),
			wp_generate_attachment_metadata( intval( $params['id'] ), $filePath )
		);

		if ( $params ) {
			$success = true;
		}

		return $this( 'model' )->get_arr( $params, $success );
	}

	/**
	 * Calculate focal point
	 *
	 * @param type  $id
	 * @param Point $point
	 * @param Size  $size
	 * @return type
	 */
	public function calculate_focal_point( $id, Point $point, Size $size ) {
		$metadata	 = wp_get_attachment_metadata( $id );
		$newX		 = (int) (($point->X * $metadata['width']) / $size->width);
		$newY		 = (int) (($point->Y * $metadata['height']) / $size->height);
		return new Point( $newX, $newY );
	}

	/**
	 * Rotate point
	 *
	 * @param Point $point
	 * @param type  $angle
	 * @param Size  $size
	 * @return Point
	 */
	public function rotatePoint( Point $point, $angle, Size $size ) {
		$angle	 = $this->angleToRadian( (int) $angle );
		$newX	 = (int) ($point->X * cos( $angle ) - $point->Y * sin( $angle ));
		$newY	 = (int) ($point->X * sin( $angle ) + $point->Y * cos( $angle ));
		$point	 = new Point( $newX + ($size->width / 2), $newY + ($size->height / 2) );
		return $point;
	}

	/**
	 * Convert to radian
	 *
	 * @param type $angle
	 * @return type
	 */
	private function angleToRadian( $angle ) {
		return $angle * (pi() / 180);
	}
}
