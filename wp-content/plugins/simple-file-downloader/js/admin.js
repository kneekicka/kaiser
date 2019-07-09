/**
 * Adding custom popup event for Simple File Download link generator
 */
jQuery(document).ready( function($){ 
	var wrap 			= $('.file-download-wrap'),
		locationSelect  = $(wrap).find('#download_term_id'),   
		selectFile 		= $(wrap).find('#download_attachment_id'),   
		generate_button = $(wrap).find('#insert-download-link'),
		get_term_data 	= []; 

	generate_button.prop('disabled', true);

	locationSelect.on("change", function(){
		get_term_data = {
			'action': 'get-media-location',
			'media_location': $(this).val() 
		}
		$.post( adminParam.ajaxURL, get_term_data, function( response ) {
			jQuery('.error-file').remove();
			
			if ( typeof response !== 'undefined' ) {
				selectFile.html( response.message );

				if ( response.success === true ) {
					generate_button.prop('disabled', false);
				} else {
					generate_button.prop('disabled', true);
				}
			}
		}, "json");
	});
});
	
function sfd_InsertDownloadLink(){
	jQuery('.error-file').remove();
	var media_id = jQuery("#download_attachment_id").val();
	var downloader_texts = jQuery("#downloader_texts").val();
	
	var text_attr = ( jQuery.trim(downloader_texts) != '' ) ? ' texts="'+ downloader_texts +'"' : '';
	
	if( media_id != null && media_id != '' && media_id != 0)
		window.send_to_editor("[media-downloader media_id=\"" + media_id + "\""+text_attr+"]");
	else
		jQuery('<span class="error-file" style="color: #ff0000; font-style: italic; padding: 0 5px">You need to select a file</span>').insertAfter('#download_attachment_id');
		
}	