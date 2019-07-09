<?php
/**
 * File class
 *
 * @package classes/models
 */

namespace tm_photo_gallery\classes\models;

use tm_photo_gallery\classes\Model as Model;
use tm_photo_gallery\classes\lib\FB;

/**
 * File class
 */
class File extends Model {

	/**
	 * Allowed image array
	 *
	 * @var type
	 */
	private $allowed_image_array;

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
	 * Get
	 *
	 * @param type $name
	 * @return type
	 * @throws \Exception
	 */
	public function __get( $name ) {
		if ( ! empty( $this->$name ) ) {
			return $this->$name;
		} else {
			throw new \Exception( 'Undefined property ' . $name . ' referenced.' );
		}
	}

	/**
	 * Construct
	 */
	public function __construct() {
		parent::__construct();
		// init allowed types
		$this->allowed_image_array = array(
			'image/jpeg',
			'image/gif',
			'image/png',
			'image/bmp',
			'image/tiff',
			'image/x-icon',
		);
	}

	/**
	 * Hook add_attachment
	 *
	 * @param $post_ID
	 */
	public function add_attachment( $post_ID ) {
		$mimeType	 = get_post_mime_type( $post_ID );
		$image		 = get_attached_file( $post_ID );
		$metaData	 = false;
		// generate image meta data
		global $_wp_additional_image_sizes;
		foreach ( $_wp_additional_image_sizes as $key => $size ) {
			if ( $key != 'copy' && $size ) {
				unset( $_wp_additional_image_sizes[ $key ] );
			}
		}
		$attachment_metadata = wp_generate_attachment_metadata( $post_ID, $image );
		wp_update_attachment_metadata( $post_ID, $attachment_metadata );

		$imageMeta  = false;
		$path_parts = pathinfo( $image );

		$this->update_attachment_data( $imageMeta, $post_ID, $metaData, $path_parts['filename'], $image );
		return true;
	}

	/**
	 *
	 * @param $path
	 * @param string     $chmod
	 * @param bool|false $recursive
	 *
	 * @return bool
	 */
	public function create_dir( $path, $chmod = '0666', $recursive = false ) {
		if ( ! is_dir( $path ) ) {
			$return = @mkdir( $path, $chmod, $recursive );
		} else {
			$return = false;
		}
		return $return;
	}

	/**
	 * Set thumbnail type
	 *
	 * @param $file
	 * @param $id
	 */
	public function set_thumbnail_type( $file, $id ) {
		$mimeType = get_post_mime_type( $id );

		if ( in_array( $mimeType, $this->allowed_image_array ) ) {
			global $_wp_additional_image_sizes;
			foreach ( $_wp_additional_image_sizes as $key => $size ) {
				if ( $size && $key != 'copy' ) {
					unset( $_wp_additional_image_sizes[ $key ] );
				}
			}
			$wp_meta = wp_generate_attachment_metadata( $id, $file );
			// update attachment meta data
			return wp_update_attachment_metadata( $id, $wp_meta );
		}
	}

	/**
	 * Function definition to convert array to xml
	 *
	 * @param $data
	 * @param $xml
	 */
	public function array_to_xml( $data, &$xml ) {
		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				if ( ! is_numeric( $key ) ) {
					$sub_node = $xml->addChild( "$key" );
					$this->array_to_xml( $value, $sub_node );
				} else {
					$sub_node = $xml->addChild( "item$key" );
					$this->array_to_xml( $value, $sub_node );
				}
			} else {
				if ( ! empty( $value ) ) {
					$xml->addChild( "$key", htmlspecialchars( "$value" ) );
				} else {
					$xml->addChild( "$key", '0' );
				}
			}
		}
	}

	/**
	 * Update attachment data
	 *
	 * @param type $imageMeta
	 * @param type $id
	 * @param type $metaData
	 * @param type $name
	 * @param type $file
	 */
	public function update_attachment_data( $imageMeta, $id, $metaData, $name,
										 $file ) {
		if ( ! empty( $imageMeta ) ) {
			$postUpdate = array( 'ID' => $id );
			if ( ! empty( $imageMeta['title'] ) ) {
				// Post title
				$postUpdate['post_title'] = trim( $imageMeta['title'] );
			}
			if ( ! empty( $imageMeta['caption'] ) ) {
				// Post content
				$postUpdate['post_content'] = trim( $imageMeta['caption'] );
			}
			// Post date
			if ( $imageMeta['date'] ) {
				$postUpdate['captured'] = strtotime( $imageMeta['date'] );
			} else {
				$postUpdate['captured'] = $metaData['FILE']['FileDateTime'];
			}
			wp_update_post( $postUpdate );
			// If isset keywords set image tag
			if ( is_array( $imageMeta['keywords'] ) ) {
				$tags = array();
				foreach ( $imageMeta['keywords'] as $keyword ) {
					$tags[] = $keyword;
				}
				$this( 'term' )->set_post_terms( array(
					'id'	 => $id,
					'value'	 => $tags,
					'type'	 => self::$tax_names['tag'],
				) );
			}

			$this->update_post_meta( $id, 'captured', $postUpdate['captured'] );
			$this->update_post_meta( $id, 'uploaded', time() );
			// add image to album or set
			if ( ! empty( $_REQUEST['folder'] ) ) {
				$folder = get_post( esc_attr( $_REQUEST['folder'] ) );
				$this( 'folder' )->set_folder_content( array(
					'id'	 => $folder->ID,
					'value'	 => $id,
					'action' => 'add_to_folder',
				) );
			}
		}
	}

	/**
	 * Upload file
	 *
	 * @param type $file
	 * @param type $time
	 *
	 * @return \WP_Error
	 */
	function upload_file( $file, $parent = 0, $time = false ) {
		if ( ! $time ) {
			$time = current_time( 'mysql' );
		}
		$uploads = wp_upload_dir( $time );
		$path	 = $uploads['path'] . '/' . $file['name'];
		$url	 = $uploads['url'] . '/' . $file['name'];
		if ( preg_match( '/^image/i', $file['type'] ) ) {
			$image = wp_get_image_editor( $file['tmp_name'] );
			if ( ! is_wp_error( $image ) ) {
				// save image
				$data		 = $image->save( $path );
				$mime_type	 = $data['mime-type'];
				// unset wp image editor from menory
				unset( $image );
			} else {
				return false;
			}
		}
		// Construct the attachment array
		$attachment = array(
			'post_mime_type' => $mime_type,
			'guid'			 => $url,
			'post_parent'	 => $parent,
			'post_title'	 => $file['name'],
			'post_content'	 => '',
			'post_date'		 => $time,
		);
		// Save the data
		return wp_insert_attachment( $attachment, $path );
	}

	/**
	 * Get array from XML file
	 *
	 * @param type $path
	 *
	 * @return array
	 */
	function get_array_from_XML( $path ) {
		if ( is_file( $path ) ) {
			$result	 = array();
			// load as string
			$xmlstr	 = file_get_contents( $path );
			$xml_obj = simplexml_load_string( $xmlstr );
			// normalize xml to array
			$this->normalize_simple_XML( $xml_obj, $result );
			if ( $result ) {
				return $result;
			}
		}
	}

	/**
	 * Normalize simple xml
	 *
	 * @param type  $obj
	 * @param array $result
	 */
	function normalize_simple_XML( $obj, &$result ) {
		$data = $obj;
		if ( is_object( $data ) ) {
			$data = get_object_vars( $data );
		}
		if ( is_array( $data ) ) {
			foreach ( $data as $key => $value ) {
				$res = null;
				$this->normalize_simple_XML( $value, $res );
				if ( $key != 'comment' ) {
					if ( ($key == '@attributes') && ($key) ) {
						$result = $res;
					} else {
						$result[ $key ] = $res;
					}
				}
			}
		} else {
			$result = $data;
		}
	}

	/**
	 * Init uploader data
	 *
	 * @param $params
	 *
	 * @return array
	 */
	public function init_uploader_data( $params ) {
		$uploader_data = array();
		if ( empty( $params['folder'] ) ) {
			$params['folder'] = 0;
		}
		$uploader_data['max_upload_size'] = wp_max_upload_size();
		if ( ! $uploader_data['max_upload_size'] ) {
			$uploader_data['max_upload_size'] = 0;
		}
		$post_params = array(
			'folder'		 => esc_attr( $params['folder'] ),
			'_wpnonce'		 => wp_create_nonce( 'media-form' ),
			'controller'	 => 'image',
			'action'		 => 'tm_pg',
			'tm_pg_action'	 => 'upload_attachment',
			'tm_pg_nonce'	 => wp_create_nonce( 'tm_pg_nonce' ),
		);

		$plupload_init = array(
			'runtimes'				 => 'html5,flash,silverlight,html4',
			'browse_button'			 => 'plupload-browse-button',
			'file_data_name'		 => 'admin-ajax',
			'url'					 => admin_url( 'admin-ajax.php' ),
			'flash_swf_url'			 => includes_url( 'js/plupload/plupload.flash.swf' ),
			'silverlight_xap_url'	 => includes_url( 'js/plupload/plupload.silverlight.xap' ),
			'filters'				 => array(
				'max_file_size' => $uploader_data['max_upload_size'] . 'b',
			),
			'multipart_params'		 => $post_params,
		);

		$uploader_data['plupload_init']	 = apply_filters( 'plupload_init', $plupload_init );
		// Verify size is an int. If not return default value.
		$uploader_data['large_size_h']	 = absint( get_option( 'large_size_h' ) );
		if ( ! $uploader_data['large_size_h'] ) {
			$uploader_data['large_size_h'] = 1024;
		}
		$uploader_data['large_size_w'] = absint( get_option( 'large_size_w' ) );
		if ( ! $uploader_data['large_size_w'] ) {
			$uploader_data['large_size_w'] = 1024;
		}

		return $uploader_data;
	}
}
