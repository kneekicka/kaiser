/*global _:false,Registry:false,uploader:false,wpUploaderInit:false,plupload:false,pluploadL10n:false,tm_pg_admin_lang, Infinity, id:false*/
Registry.register( "upload", ( function ( $ ) {
	"use strict";
	function __( value ) {
		return Registry._get( value );
	}
	var state,
		selected = [ ],
		isIE = navigator.userAgent.indexOf( 'Trident/' ) !== -1 || navigator.userAgent.indexOf( 'MSIE ' ) !== -1;

	function createInstance() {
		return {
			pluopload: false,
			uploader_init: function () {
				var key, message;
				if ( !this.pluopload ) {
					// Make sure flash sends cookies (seems in IE it does whitout switching to urlstream mode)
					if ( !isIE && 'flash' === plupload.predictRuntime( wpUploaderInit ) &&
						( !wpUploaderInit.required_features || !wpUploaderInit.required_features.hasOwnProperty( 'send_binary_string' ) ) ) {
						wpUploaderInit.required_features = wpUploaderInit.required_features || { };
						wpUploaderInit.required_features.send_binary_string = true;
					}

					/**
					 * Add filter mime_types by extension
					 *
					 * @param {type} acceptExtension
					 * @param {type} file
					 * @param {type} cb
					 */
					plupload.addFileFilter( 'mime_types', function ( acceptExtension, file, cb ) {
						var view = __( 'grid' ).getView(),
							ext = file.name.slice( ( Math.max( 0, file.name.lastIndexOf( "." ) ) || Infinity ) + 1 ),
							$this = this;

						if ( 'grid' !== view ) {
							cb( true );
							return;
						}

						if ( _.isUndefined( acceptExtension ) || _.isEmpty( acceptExtension ) ) {
							acceptExtension = $( __( 'grid' )._main[view] + ' #plupload-browse-button' ).attr( 'accept' ).replace( /\./g, '' ).split( ',' );
						}
						// invalid extension
						if ( $.inArray( ext.toLowerCase(), acceptExtension ) !== -1 ) {
							cb( true );
						} else {
							$this.trigger( 'Error', {
								code: plupload.FILE_EXTENSION_ERROR,
								message: uploader.errorMap.FILE_EXTENSION_ERROR( file ),
								file: file
							} );
							cb( false );
						}
					} );

					/**
					 * init Uploader
					 *
					 * @type {o.Uploader|*}
					 */
					wpUploaderInit.drop_element = document.body;
					wpUploaderInit.dropzone = document.body;
					wpUploaderInit.container = document.body;
					uploader = new plupload.Uploader( wpUploaderInit );

					/**
					 * Init pluopload
					 *
					 * @param {type} up
					 */
					uploader.bind( 'Init', function ( up ) {
						var timer, active, dragdrop = up.features.dragdrop,
							dropzone = $( 'body' );

						setResize( getUserSetting( 'upload_resize', false ) );

						if ( up.runtime === 'html4' ) {
							$( '.upload-flash-bypass' ).hide();
						}

						dropzone.toggleClass( 'supports-drag-drop', !!dragdrop );
						if ( !dragdrop ) {
							return dropzone.unbind( '.wp-uploader' );
						}

						/**
						 *  Drag start
						 */
						dropzone.bind( 'dragover.wp-uploader', function ( e ) {
							if ( timer ) {
								clearTimeout( timer );
							}
							if ( active ) {
								return;
							}
							dropzone.trigger( 'dropzone:enter' ).addClass( 'drag-over' );
							active = true;
						} );

						/**
						 * Dragleave/drop
						 */
						$( '.uploader-window' ).bind( 'dragleave.wp-uploader, drop.wp-uploader', function ( e ) {
							timer = setTimeout( function () {
								active = false;
								dropzone.trigger( 'dropzone:leave' ).removeClass( 'drag-over' );
							}, 0 );
						} );
					} );

					/**
					 * Init pluopload
					 */
					uploader.init();
					/**
					 * Bind filesAdded
					 *
					 * @param {type} up
					 * @param {type} files
					 */
					uploader.bind( 'FilesAdded', function ( up, files ) {
						$( 'body' ).unbind( 'dragleave.wp-uploader' );
						$( 'body' ).removeClass( 'drag-over' );
						var noMedia = $( "p.no-media" ),
							html = '', _grid = __( "grid" ),
							i = files.length,
							view = _grid.getView();
						if ( noMedia[0] ) {
							noMedia.remove();
						}
						$( '#media-upload-error' ).empty();
						uploadStart();
						for ( ; i--; ) {
							html += _grid.addItem( files[i].id );
						}
						$( _grid.getSelector( view, 'img', '_addItem' ) ).parents( '.tm-pg_column' ).after( html );
						up.refresh();
						up.start();
					} );

					/**
					 * File uploading
					 *
					 * @param {type} up
					 * @param {type} file
					 */
					uploader.bind( 'UploadFile', function ( up, file ) {
						fileUploading( up, file );
					} );

					/**
					 * Upload progress
					 *
					 * @param {type} up
					 * @param {type} file
					 */
					uploader.bind( 'UploadProgress', function ( up, file ) {
						state.uploadProgress( up, file );
					} );

					/**
					 * File uploaded
					 *
					 * @param {type} up
					 * @param {type} file
					 * @param {type} response
					 */
					uploader.bind( 'FileUploaded', function ( up, file, response ) {
						try {
							response = JSON.parse( response.response );
						} catch ( e ) {
							return state.uploadError( pluploadL10n.default_error, e, file );
						}

						if ( !_.isObject( response ) || _.isUndefined( response.success ) ) {
							return state.uploadError( pluploadL10n.default_error, null, file );
						} else if ( !response.success ) {
							return state.uploadError( response.data.message, response.data, file );
						}

						state.fileUploaded( file, response );
					} );

					/**
					 *  Upload error
					 *
					 * @param {type} up
					 * @param {type} pluploadError
					 */
					uploader.bind( 'Error', function ( up, pluploadError ) {
						// Check for plupload errors.
						for ( key in uploader.errorMap ) {
							if ( pluploadError.code === plupload[key] ) {
								message = uploader.errorMap[key];

								if ( _.isFunction( message ) ) {
									message = message( pluploadError.file, pluploadError );
								}
								break;
							}
						}
						state.uploadError( message, pluploadError, pluploadError.file );
						up.refresh();
					} );

					/**
					 * Upload complete
					 *
					 * @param {type} up
					 * @param {type} files
					 */
					uploader.bind( 'UploadComplete', function ( up, files ) {
						uploader.unbindAll();
						state.uploadComplete();
					} );
					/**
					 *
					 * @type {{FAILED: *, IMAGE_FORMAT_ERROR: *, IMAGE_MEMORY_ERROR: *, IMAGE_DIMENSIONS_ERROR: *, GENERIC_ERROR: *, IO_ERROR: *,
					 * HTTP_ERROR: *, SECURITY_ERROR: *, FILE_EXTENSION_ERROR: plupload.Uploader.errorMap.'FILE_EXTENSION_ERROR', FILE_SIZE_ERROR: plupload.Uploader.errorMap.'FILE_SIZE_ERROR'}}
					 */
					uploader.errorMap = {
						'FAILED': pluploadL10n.upload_failed,
						'IMAGE_FORMAT_ERROR': pluploadL10n.not_an_image,
						'IMAGE_MEMORY_ERROR': pluploadL10n.image_memory_exceeded,
						'IMAGE_DIMENSIONS_ERROR': pluploadL10n.image_dimensions_exceeded,
						'GENERIC_ERROR': pluploadL10n.upload_failed,
						'IO_ERROR': pluploadL10n.io_error,
						'HTTP_ERROR': pluploadL10n.http_error,
						'SECURITY_ERROR': pluploadL10n.security_error,
						'FILE_EXTENSION_ERROR': function ( file ) {
							return tm_pg_admin_lang.FILE_EXTENSION_ERROR.replace( '%filename%', file.name );
						},
						'FILE_SIZE_ERROR': function ( file ) {
							var message = tm_pg_admin_lang.FILE_SIZE_ERROR.replace( '%filename%', file.name ).replace( '%size%', state.formatSizeUnits( parseInt( uploader.settings.filters.max_file_size ) ) );
							return message;
						}
					};
					this.pluopload = true;
				}
			},
			/**
			 * Init close button
			 *
			 * @param {type} view
			 * @returns {undefined}
			 */
			initCloseButton: function ( view ) {
				$( __( 'grid' )._main[view] + " button.close" ).on( "click", function ( e ) {
					e.preventDefault();
					state.closeUploader( view );
				} );
			},
			/**
			 * Close uploader
			 *
			 * @param {type} view
			 * @returns {undefined}
			 */
			closeUploader: function ( view ) {
				var _body = $( 'body' ),
					_uploader = $( '.uploader-window' ),
					_grid = __( 'grid' );
				uploader.unbindAll();
				selected = [ ];
				view = view || _grid.getView();
				var parent_id = $( _grid._main[view] + " button.close" ).attr( "parent" );
				_grid.toggleAddPhotoBtn();
				if ( parent_id ) {
					$( "#" + parent_id ).remove();
				}
				// dragover.wp-uploader
				_uploader.unbind( 'dragleave.wp-uploader' );
				_uploader.unbind( 'drop.wp-uploader' );
				_body.unbind( 'dragover.wp-uploader' );
				_body.unbind( 'dragleave.wp-uploader' );
				_body.unbind( 'drop.wp-uploader' );
				this.pluopload = false;
			},
			/**
			 * Load uploader ajax
			 *
			 * @param data
			 */
			loadUploader: function ( data ) {
				var view = __( 'grid' ).getView();
				$( __( 'grid' )._main[view] + ' .tm-pg_upload_container' ).html( data.html );
				state.initCloseButton( view );
				state.uploader_init();
			},
			/**
			 * Formatting allowed size
			 *
			 * @param bytes
			 * @returns {*}
			 */
			formatSizeUnits: function ( bytes ) {
				var fileSizeType = [ 'B', 'KB', 'MB', 'GB', 'TB' ],
					sizeTypeLength = fileSizeType.length - 1,
					i = 0,
					oneByteSize = 1024;
				for ( ; bytes > oneByteSize && i < sizeTypeLength; i++ ) {
					bytes /= oneByteSize;
				}
				return bytes;
			},
			/**
			 * Upload complete
			 */
			uploadComplete: function () {
				var _pgFolder = __( 'pg-folder' ),
					_grid = __( 'grid' );
				selected = [ ];
				state.closeUploader();
				// show notofication
				__( 'notification' ).show( 'upload_complite' );
				// update folder
				if ( _pgFolder._ID ) {
					_pgFolder.updateFolder();
					// toggle select all
					if ( _pgFolder.getLength( 'img' ) ) {
						_grid.toggleSelectAllBtn( 'img', 'show' );
					}
				} else {
					// toggle select all
					if ( __( 'pg-content' ).getLength( 'img' ) ) {
						_grid.toggleSelectAllBtn( 'img', 'show' );
					}
				}
				_grid.checkSelectAllBtn( 'img' );

			},
			/**
			 * Upload progress
			 *
			 * @param up
			 * @param file
			 */
			uploadProgress: function ( up, file ) {
				var $item = __( 'grid' ).getItem( file.id, 'img' ).parent( '.tm-pg_column' );
				$( '.tm-pg_library_item_loading_container > span', $item ).text( file.percent + '%' );
				$( '.tm-pg_library_item_loading_bar', $item ).css( 'width', file.percent + '%' );
			},
			/**
			 * Upload Success
			 *
			 * @param fileObj
			 * @param serverData
			 */
			fileUploaded: function ( fileObj, serverData ) {
				if ( serverData.success ) {
					var _grid = __( 'grid' ),
						_pgFolder = __( 'pg-folder' ),
						_content = __( 'pg-content' ),
						_common = __( 'common' );
					// set new image id
					_grid.getItem( fileObj.id, 'img' ).attr( 'data-id', serverData.data.id );
					// set content
					_content.setContent( serverData.data.id, '', serverData.data, 'img', 'prepend' );
					// bild item
					_grid.buildItem( serverData.data.id );
					// check item
					_grid.checkItem( _grid.getItem( serverData.data.id, 'img' ) );
					// add content to folder
					if ( _pgFolder._ID ) {
						_pgFolder.setContent( serverData.data.id, serverData.data, 'img', 'prepend' );
					}
					// set on selected
					selected.push( serverData.data.id );
					_content._allImages = _common.prependArr( serverData.data.id, _content._allImages );
					_content._totalCount++;

				} else {
					state.uploadError( fileObj, serverData.data );
				}
			},
			/**
			 * Upload error
			 *
			 * @param message
			 * @param data
			 * @param file
			 */
			uploadError: function ( message, data, file ) {
				__( 'notification' ).show( 'error' );
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
