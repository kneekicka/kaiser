/* global _ */

Registry.register( "image", ( function ( $ ) {
	"use strict";
	var state;
	/**
	 * __
	 *
	 * @param {type} value
	 * @returns {wp.mce.View|*}
	 */
	function __( value ) {
		return Registry._get( value );
	}
	/**
	 * Create instance
	 *
	 * @returns {image_L3.createInstance.imageAnonym$0}
	 */
	function createInstance() {
		return {
			/**
			 * Show upload
			 *
			 * @returns {undefined}
			 */
			showUpload: function () {
				var _grid = __( 'grid' ),
					_content = __( 'pg-content' ),
					view = _grid.getView(),
					uploadWrap = $( _grid._main[view] + ' .tm-pg_upload_container' );
				if ( !$( _grid._main[view] + " #plupload-upload-ui" ).length ) {
					var $params = { };
					$params.nonce = $( '[name="nonce"]' ).val();
					_content.toggleDisable(  );
					state.uploadImages( $params, function ( $data ) {
						_grid.toggleAddPhotoBtn();
						__( "upload" ).loadUploader( $data );
						uploadWrap.css( 'height', 'auto' );
						_content.toggleDisable( false );
					} );
				}
			},
			/**
			 * Uploading images
			 *
			 * @param {type} $params
			 * @param {type} callback
			 */
			uploadImages: function ( $params, callback ) {
				var $args = {
					controller: "media",
					action: "uploader"
				};
				if ( _.isEqual( __( 'grid' ).getView(), 'folder' ) ) {
					$args.folder = __( 'pg-folder' )._ID;
				}
				$.extend( $args, $params );
				__( 'common' ).wpAjax( $args,
					function ( data ) {
						if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
							callback( data );
						}
					},
					function ( data ) {
						if ( console ) {
							console.warn( data );
						}
					}
				);
			}
		};
	}

	return {
		getInstance: function () {
			if ( !state ) {
				state = createInstance();
			}
			return state;
		}
	};
} )( jQuery ) );
