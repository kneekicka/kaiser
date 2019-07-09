<?php

/**
 * Model Image
 *
 * @package classes/models
 */

namespace tm_photo_gallery\classes\models;

use tm_photo_gallery\classes\Model;
use tm_photo_gallery\classes\lib\FB;

/**
 * Model Image
 */
class Image extends Model {

	/**
	 * Instance
	 *
	 * @var type
	 */
	protected static $instance;

	/**
	 * Sizes
	 *
	 * @var type
	 */
	private static $sizes;

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
	 * Construct
	 */
	public function __construct() {
		if ( !self::$sizes ) {
			self::$sizes = include TM_PG_CONFIGS_PATH . 'img-sizes.php';
		}
	}

	/**
	 * Hook get tm-pg-get_sizes
	 */
	public function get_image_sizes( $sizes ) {
		return $sizes;
	}

	/**
	 * Get all image sizes
	 *
	 * @return type
	 */
	public static function get_sizes() {
		return apply_filters( 'tm_pg_get_sizes', self::$sizes );
	}

	/**
	 * Get size
	 *
	 * @param type $size
	 * @param type $key
	 * @return type
	 */
	public static function get_size( $size, $key = false ) {
		$sizes	 = self::get_sizes();
		$return	 = false;
		if ( !is_array( $size ) ) {
			if ( $key && !empty( $sizes[$size][$key] ) ) {
				$return = $sizes[$size][$key];
			} elseif ( !empty( $sizes[$size] ) ) {
				$return = $sizes[$size];
			}
		}
		return $return;
	}

	/**
	 * Get sizes by type
	 *
	 * @param type $type
	 *
	 * @return type
	 */
	public static function get_sizes_by_type( $type ) {
		$sizes	 = self::get_sizes();
		$return	 = array();
		if ( $type ) {
			foreach ( $sizes as $key => $size ) {
				if ( is_array( $size['type'] ) ) {
					if ( in_array( $type, $size['type'] ) ) {
						$return[$key] = $size;
					}
				} else {
					if ( $size['type'] == $type ) {
						$return[$key] = $size;
					}
				}
			}
		}
		return $return;
	}

	/**
	 * Get images
	 *
	 * @param type $id
	 * @param type $type
	 * @return type
	 */
	public function get_thumbnails( $id, $type = 'img' ) {
		$sizes	 = $this->get_sizes_by_type( $type );
		$images	 = array();
		if ( !empty( $sizes ) ) {
			foreach ( $sizes as $key => $value ) {
				if ( $value ) {
					$images[$key] = $this->get_thumbnail( $id, $key );
				}
			}
		}
		return $images;
	}

	/**
	 * Get thumbnail
	 *
	 * @param type $id
	 * @param type $size
	 */
	public function get_thumbnail( $id, $size = 'medium' ) {
		$downsize = image_downsize( $id, $size );
		if ( $downsize ) {
			return array(
				'height' => $downsize[2],
				'width'	 => $downsize[1],
				'url'	 => $downsize[0],
			);
		}
	}

	/**
	 * Add image sizes
	 */
	public function add_image_sizes() {
		foreach ( self::get_sizes() as $key => $size ) {
			if ( 0 != $size['width'] && 0 != $size['height'] && 'copy' != $key ) {
				add_image_size( $key, $size['width'], $size['height'], true );
			} else {
				add_image_size( $key, $size['width'], $size['height'] );
			}
		}
	}

	/**
	 * Hook Get image thumbnail
	 *
	 * @param int    $downsize - downsize.
	 * @param int    $id - id.
	 * @param string $size - size.
	 *
	 * @return array|bool
	 */
	public function image_downsize( $downsize, $id, $size = 'medium' ) {
		$return		 = array();
		$metadata	 = wp_get_attachment_metadata( $id );
		$attached_file		 = get_attached_file( $id );
		if ( !empty( $metadata['file'] ) ) {
			$img_url			 = wp_get_attachment_url( $id );
			$img_url_basename	 = wp_basename( $img_url );
			$intermediate		 = image_get_intermediate_size( $id, $size );
			// check thumbnail was created
			//var_dump($size);
			if ( !empty( $intermediate ) && !empty( $intermediate['file'] ) ) {
				$return[0]	 = str_replace( $img_url_basename, $intermediate['file'], $img_url );
				$return[1]	 = $intermediate['width'];
				$return[2]	 = $intermediate['height'];
				$return[3]	 = true;
			} else {
				// if full thumbnail
				if ( 'full' == $size ) {
					$return[0]	 = $img_url;
					$return[1]	 = $metadata['width'];
					$return[2]	 = $metadata['height'];
					$return[3]	 = true;
				} else {
					// generate thumbnail
					if ( $this->generate_thumbnail( $id, $size ) ) {
						$return = $this->image_downsize( $downsize, $id, $size );
					} else {
						$return = $this->image_downsize( $downsize, $id, 'full' );
					}
				}
			}
		}
		return $return;
	}

	/**
	 * Get register image size names by type
	 *
	 * @param string $type
	 *
	 * @return array|bool
	 */
	public function get_image_size_names_by_type( $type ) {
		//var_dump($type);
	}

	/**
	 * Get thumbnail path
	 *
	 * @param $id
	 * @param string $size
	 *
	 * @return string
	 */
	public function get_thumbnail_path( $id, $size = 'copy' ) {
		$metadata	 = wp_get_attachment_metadata( $id );
		$file		 = get_attached_file( $id );
		if ( !empty( $metadata ) && !empty( $metadata['sizes'][$size]['file'] ) ) {
			$file_name = $metadata['sizes'][$size]['file'];
		} else {
			// get global img sizes
			global $_wp_additional_image_sizes;
			$ext		 = pathinfo( $file, PATHINFO_EXTENSION );
			$name		 = basename( $file, '.' . $ext );
			$width		 = $_wp_additional_image_sizes[$size]['width'];
			$height		 = $_wp_additional_image_sizes[$size]['height'];
			$file_name	 = "$name-{$width}x{$height}.$ext";
		}
		$dir	 = pathinfo( $file, PATHINFO_DIRNAME );
		$path	 = "$dir/$file_name";
		return $path;
	}

	/**
	 * Generate image thumbnail
	 *
	 * @global \tm_photo_gallery\classes\models\type $_wp_additional_image_sizes
	 * @param type $id
	 * @param type $size
	 * @return type
	 */
	public function generate_thumbnail( $id, $size ) {
		// get global img sizes
		global $_wp_additional_image_sizes;
		$return = false;
		if ( !is_string( $size ) ) {
			return $return;
		}
		$img_type	 = $this->get_size( $size, 'type' );
		$img_type	 = is_array( $img_type ) ? $img_type : array( $img_type );
		if ( empty( $_wp_additional_image_sizes[$size] ) || in_array( 'copy', $img_type ) ) {
			$this->add_image_sizes();
			global $_wp_additional_image_sizes;
		}
		if ( !empty( $_wp_additional_image_sizes[$size] ) && !in_array( 'copy', $img_type ) ) {
			$return = $this( 'focal_point' )->crop_image( $id, $size );
		}
		return $return;
	}

	/**
	 * Crop image
	 *
	 * @global \tm_photo_gallery\classes\models\type $_wp_additional_image_sizes
	 * @param type $id
	 * @param type $size
	 * @param type $filePath
	 * @return type
	 */
	public function crop_image( $id, $size, $filePath ) {
		global $_wp_additional_image_sizes;
		$return		 = false;
		$viewWidth	 = $_wp_additional_image_sizes[$size]['width'];
		$viewHeight	 = $_wp_additional_image_sizes[$size]['height'];
		$thumbPath	 = $this->get_thumbnail_path( $id, $size );
		$image		 = wp_get_image_editor( $filePath );
		if ( !is_wp_error( $image ) ) {
			$metadata					 = wp_get_attachment_metadata( $id );
			$image->resize( $viewWidth, $viewHeight, true );
			$image->set_quality( 80 );
			// save image
			$save_data					 = $image->save( $thumbPath );
			unset( $save_data['path'] );
			// update attachment meta data
			$metadata['sizes'][$size]	 = $save_data;
			$return						 = wp_update_attachment_metadata( $id, $metadata );
		}
		return $return;
	}

	/**
	 * Image Rotate
	 *
	 * @global \tm_photo_gallery\classes\models\type $_wp_additional_image_sizes
	 * @param type $params['id'] - id image.
	 * @param type $params['angle'] - angle.
	 * @return type
	 */
	public function rotate_image( $params ) {
		$ids	 = explode( ',', esc_attr( $params['id'] ) );
		$result	 = array();
		$success = false;
		foreach ( $ids as $id ) {
			$focal_point = $this->get_post_meta( $id, 'focal_point', true );
			$filePath	 = get_attached_file( $id );
			$image		 = wp_get_image_editor( $filePath );
			$success	 = false;
			if ( !is_wp_error( $image ) ) {
				// $old_size  = $image->get_size();
				// rotate image
				$image->rotate( (int) $params['angle'] );
				// $new_size  = $image->get_size();
				// save rotate original file
				$image->save( $filePath );
				// delete all thubnails
				$this( 'image' )->delete_thumbnails( $id );
				// save only original img
				global $_wp_additional_image_sizes;
				foreach ( $_wp_additional_image_sizes as $key => $size ) {
					if ( $key != 'copy' && $size ) {
						unset( $_wp_additional_image_sizes[$key] );
					}
				}
				// generate image meta data
				$attachment_metadata = wp_generate_attachment_metadata( $id, $filePath );
				$success			 = true;
				wp_update_attachment_metadata( $id, $attachment_metadata );
				// save new focal point
				if ( $focal_point ) {
					// rotate focus point
					/*
					  $point                = new Point( $focal_point['xPos'], $focal_point['yPos'] );
					  $old_size          = new Size( $old_size['width'], $old_size['height'] );
					  $new_size          = new Size( $new_size['width'], $new_size['height'] );
					  $_point                = new Point( $point->X - ($new_size->width / 2), $point->Y - ($new_size->height / 2) );
					  $new_point             = $this->rotatePoint( $_point, $params['angle'], $new_size );
					  $focal_point['xPos'] = $new_point->X;
					  $focal_point['yPos'] = $new_point->Y; */
					// rotate position
					$this->update_post_meta( $id, 'focal_point', '' );
				}
			}
		}

		return $this( 'model' )->get_arr( $params, $success );
	}

	/**
	 * Delete Img file
	 *
	 * @param type $id
	 * @param type $except
	 *
	 * @return boolean
	 */
	function delete_thumbnails( $id, $except = array() ) {
		$return				 = false;
		// get attachment metadata
		$metadata			 = wp_get_attachment_metadata( $id );
		$metadata['sizes']	 = array_merge( $metadata['sizes'], self::get_sizes() );
		foreach ( $metadata['sizes'] as $size => $val ) {
			if ( !in_array( $size, $except ) && $val ) {
				$path = $this->get_thumbnail_path( $id, $size );
				if ( file_exists( $path ) ) {
					unset( $metadata['sizes'][$size] );
					unlink( $path );
				}
			}
		}
		if ( wp_update_attachment_metadata( $id, $metadata ) ) {
			$return = true;
		}
		return $return;
	}

	/**
	 * Delete image
	 *
	 * @param type $id
	 * @return type
	 */
	function delete_image( $id ) {
		$return = wp_delete_attachment( $id );
		return $return;
	}

}
