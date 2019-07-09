<?php
/*
Plugin Name: Simple File Downloader
Version: 1.0.4
Plugin URI: http://phplugins.softanalyzer.com/simple-file-downloader
Author: eugenealegiojo
Author URI: http://wpdevph.com
Description: Simplest way to add download links into your posts/pages.
License:

  Copyright 2014 Eugene A. (eugene@wpdevph.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( !defined('SFD_URL') )
define( 'SFD_URL', plugins_url( basename( plugin_dir_path(__FILE__) ), basename( __FILE__ ) ) );

if ( !defined('SFD_PATH') )
define( 'SFD_PATH', plugin_dir_path( __FILE__ ) );
			
if ( !class_exists('SIMPLE_FILE_DOWNLOADER') ) {

	class SIMPLE_FILE_DOWNLOADER {
		
		public function __construct(){
			// TO DO: Add submenu for options
			//add_action('admin_menu', array(&$this, 'sdf_menu_options'));
			
			// Adding media button into the content editor
			add_action('media_buttons_context',  array($this, 'editor_download_button'), 10);
			
			// Adding popup window for file selection from media library
			add_action( 'admin_footer',  array($this, 'add_inline_popup_content') );
			
			// Adding admin footer scripts when download link is available'
			add_action('admin_enqueue_scripts', array($this, 'admin_footer_scripts'), 10 );
			
			// Do the ajax request for media files
			add_action( 'wp_ajax_get-media-location', array($this, 'get_media_files_attachment') );
			add_action( 'wp_ajax_nopriv_get-media-location', array($this, 'get_media_files_attachment') );
			
			// Shortcode support
			add_shortcode('media-downloader', array($this, 'sfd_media_downloader') ); 	
			
			// Process download
			add_action('init', array($this, 'sfd_process_download'), 10);
			
			// TO DO: Localization support
			//add_action('plugins_loaded', array(&$this,'sfd_plugins_loaded'), 10, 2 );
		}
		
		/**
		 * Check if the current screen is supported. Making sure it works for posts, pages & post_types
		 *
		 * @return boolean
		 */
		function is_screen_supported(){
			global $current_screen;

			if( isset($current_screen->base) && $current_screen->base == 'post' ) 
				return true;
			else 
				return false;
		}
		
		/**
		 * Admin footer scripts
		 */		
		function admin_footer_scripts(){
			global $current_screen;
			if ( $this->is_screen_supported() ) {
				wp_enqueue_script( 'sfd_admin_script', SFD_URL . '/js/admin.js' );
				wp_localize_script( 'sfd_admin_script', 'adminParam', array( 'ajaxURL' => admin_url('admin-ajax.php') ) );
			}
		} 
		
		/**
		 * Localization handler
		 */
		function sfd_plugins_loaded(){
			load_plugin_textdomain( 'sfd', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}
		
		/**
		 * TO DO: Adding submenu in the media
		 */
		//function sdf_menu_options() {
			//add_submenu_page('upload.php', 'sdf-options', 'SFD Options', 'administrator', 'sdf-options-menu', array(&$this,'manage_sfd_options') );
		//}
		
		/**
		 * TO DO: File download options management
		 */
		//function manage_sfd_options(){
			
		//}

		/**
		 * Add custom media button for download file. This button will display popup that allows us to... 
		 * ...select file and generate shortcode to be inserted automatically in content editor.
		 *
		 * @param string $context
		 * @return string $context
		 */	
		function editor_download_button( $context ){
			if( !$this->is_screen_supported() ) return false;
			
			//path to my icon
			$img = SFD_URL . '/images/download-16x16.png';

			//the id of the container I want to show in the popup
			$container_id = 'sfd_download_container';
		  
			//our popup's title
			$title = 'Add Downloader Link';

			// HTML for new button
			$context .= "<a class='thickbox button sfd_download_link' title='{$title}' href='#TB_inline?width=400&height=500&inlineId={$container_id}'>
						<img src='{$img}' / style='padding-bottom: 5px;'><span class='sfd_download_icon'>Add Download</span></a>";
		  	
		  	echo $context;
		}
		
		/**
		 * Add a popup content when download button is clicked.
		 *
		 * @return string $content
		 */
		function add_inline_popup_content() {
			if( !$this->is_screen_supported() ) return false;
			
			$content = '<div id="sfd_download_container" style="display:none;">
					<div class="file-download-wrap">
							<div style="padding:15px 15px 0 15px;">
							<h2>'. __('Select file location', 'sfd') .'</h2></div>';
				// Select file location
				$content .= '<div style="padding: 15px 15px 0 15px">';
					$content .= '<span>'. __('Select a Media Library location from where you get the file.', 'sfd') .'</span><br />';
					$content .= '<select id="download_term_id"><option value="">-'. __('Select location', 'sfd') .'-</option>
									<option value="all-files">-'. __('All Media Library files', 'sfd') .'-</option>
									<option value="attached-files">-'. __('Attached to posts/pages', 'sfd') .'-</option>
								</select>';
				$content .= '</div>';
				
				// Select file to download
				$content .= '<div style="padding: 15px 15px 0 15px">
								<h2>'. __('Select media file', 'sfd') .'</h2>
								<span>'. __('Select a file from Media Library to add in the content as download link.', 'sfd') .'</span><br />'.
								__('<select id="download_attachment_id"><option value="">-No location selected', 'sfd') .'-</option></select>
							</div>';
							
				// Download texts			
				$content .= '<div style="padding: 15px 15px 0 15px">	
								<h2>'. __('Download texts', 'sfd') .'</h2>
								<span>'. __('Input texts you want to display for the link.', 'sfd') .'</span><br />
								<input type="text" name="downloader_texts" id="downloader_texts" size="40" />
								<br /><span><em>Default texts: "Download File"</em></span>
							</div>
							<div style="padding: 15px;";>
								<input type="button" onclick="sfd_InsertDownloadLink();" value="'. __('Generate Download Link', 'sfd'). '" id="insert-download-link" class="button-primary">
								<a onclick="tb_remove(); return false;" href="#" style="color:#bbb;" class="button">'. __('Cancel', 'sfd'). '</a>
							</div>
					</div></div>';
					
			echo $content;					
		}
		
		/**
		 * Load media files based from location selected (All media | Attached media)
		 * 
		 * @return string|json $response
		 */
		function get_media_files_attachment() {
			$media_location = $_POST['media_location'];

			$response = array('success' => false, 'message' => '<option value="">-'. __('No files found.', 'sfd') .'-</option>');

			if ( !empty($media_location) ) {
				$args = array(
					'post_type' => 'attachment', 
					'posts_per_page' => -1,
					'post_status' => 'inherit', 
					'orderby' => 'title', 
					'order' => 'ASC'
				);

				if ( $media_location == 'attached-files' ) {
					$args['post_parent__not_in'] = array(0);
				} else {
					$args['post_parent'] = 0;
				}

				$get_media = get_posts( $args );
				if ( $get_media ) {
					$html = '';
					foreach ( $get_media as $key => $attachment ) {
						$file_name = get_attached_file( $attachment->ID );
						$basename = basename($file_name);
						$check_type = wp_check_filetype( $file_name );
						$file_type = isset($check_type['ext']) ? '('.strtoupper($check_type['ext']).') ' : '';
						
						$title_base = substr($basename, 0, (strlen($basename)) - (strlen(strrchr($basename, '.'))));
						
						$title = empty($attachment->post_title) ? $title_base : $attachment->post_title;
						$html .= '<option value="'. $attachment->ID .'">'. $file_type . $title .'</option>';
					} // end foreach

					$response = array('success' => true, 'message' => $html); 
				}
			} else {
				$response['message'] = '<option value="">-'. __('No location selected', 'sfd') .'-</option>';
			}

			wp_send_json($response);
		} 
		
		/**
		 * Shortcode handler
		 *
		 * @param array $atts
		 * @return string $download_link
		 */
		function sfd_media_downloader( $atts ){
			global $post;
			
			extract( shortcode_atts( array(
				'media_id' => 0,
				'texts' => 'Download File',
				'image_url' => '',
				'class' => '',
				'size_texts' => '',
				'display_filesize' => ''
			), $atts ) );
			
			if ( $media_id > 0 ) {
				$link_class = (!empty($class)) ? ' class="'. $class .'"' : '';
				$texts = (!empty($image_url)) ? '<img src="'. $image_url .'" />' : $texts;
				$download_link = '<a href="'. site_url('?media_dl=') . $media_id .'"'.$link_class.'>'. $texts .'</a>';
				
				$get_media_item = wp_get_attachment_url( $media_id );
				$uploads = wp_upload_dir();
				$file_path = str_replace( $uploads['baseurl'], $uploads['basedir'], $get_media_item );
				$size_texts = (!empty($size_texts)) ? $size_texts : 'Size';
				if( !empty($display_filesize) && $display_filesize == 'yes' ) {
					$download_link .= ' '.strtoupper($size_texts) .': '. $this->formatSizeUnits(filesize($file_path));
				}
				return $download_link;
			}
		}
		
		/** 
		 * File downloader processor
		 * @return void
		 */
		function sfd_process_download(){
			if( isset($_GET['media_dl']) && !empty($_GET['media_dl']) ) {
				$media_file = $_GET['media_dl'];
				
				$media_id = $_GET['media_dl'];
				if ( (int)$media_id <= 0 ) return;
				
				// grab the requested file's name
				$file_name = get_attached_file( $media_id );
				
				// make sure it's a file before doing anything!
				if ( is_file($file_name) ) {
					
					// required for IE & Safari
					if(ini_get('zlib.output_compression')) { ini_set('zlib.output_compression', 'Off');	}
					
					// get the file mime type using the file extension
					switch(strtolower(substr(strrchr($file_name, '.'), 1))) {
						case 'pdf': $mime = 'application/pdf'; break;
						case 'zip': $mime = 'application/zip'; break;
						case 'jpeg':
						case 'jpg': $mime = 'image/jpg'; break;
						default: $mime = 'application/force-download';
					}
			
					header('Pragma: public'); 	// required
					header('Expires: 0');		// no cache
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Last-Modified: '.gmdate ('D, d M Y H:i:s', @filemtime ($file_name)).' GMT');
					header('Cache-Control: private',false);
					header('Content-Type: '.$mime);
					header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
					header('Content-Transfer-Encoding: binary');
					header('Content-Length: '. @filesize($file_name) );	// provide file size
					header('Connection: close');
					$this->readfileChunked( $file_name );		// push it out
					die();		
				}
			}	
		}

		/**
  		 * Read a file and display its content chunk by chunk to address the large file download
  		 * 
  		 * @param string $filename Required
  		 * @param boolean $retbytes Default: true
		 */
		function readfileChunked($filename, $retbytes=true){
			$chunksize = 1*(1024*1024);
			$buffer = '';
			$cnt = 0;
			$handle = fopen($filename, 'rb');
			if ($handle === false) {
				return false;
			}
			while (!feof($handle)) {
				$buffer = fread($handle, $chunksize);
				echo $buffer;
				ob_flush();
				flush();
				if ($retbytes) {
					$cnt += strlen($buffer);
				}
			}
			$status = fclose($handle);
			if ($retbytes && $status) {
				return $cnt; // return num. bytes delivered like readfile() does.
			}
			return $status;
		}
		
		/**
		 * Filesize formatting
		 * 
		 * @param int $bytes
		 * @return string $bytes
		 */
		function formatSizeUnits($bytes){
			if ($bytes >= 1073741824){
				 $bytes = number_format($bytes / 1073741824, 2) . ' GB';
			} elseif ($bytes >= 1048576) {
				 $bytes = number_format($bytes / 1048576, 2) . ' MB';
			} elseif ($bytes >= 1024) {
				$bytes = number_format($bytes / 1024, 2) . ' KB';
			} elseif ($bytes > 1) {
				$bytes = $bytes . ' bytes';
			} elseif ($bytes == 1) {
				$bytes = $bytes . ' byte';
			} else {
				$bytes = '0 bytes';
			}
			return $bytes;
		}
				
	}	// end class SIMPLE_FILE_DOWNLOADER
	
	new SIMPLE_FILE_DOWNLOADER();
}