/* global _ */

Registry.register( "album", ( function ( $ ) {
	"use strict";
	var state;
	function __( value ) {
		return Registry._get( value );
	}
	function createInstance() {
		return {
			tax_name: window.tm_pg_options.tax_names.album,
			post_type: window.tm_pg_options.post_types.album,
			_clone: '#album-term-clone',
			_item: '.tm-pg_album_container',
			/**
			 * Init Events
			 *
			 * @returns {undefined}
			 */
			initEvents: function () {
				var _grid = __( 'grid' ),
					_right = __( 'pg-right' );
				// On focusout album name input
				$( document ).on( 'focusout', _grid._item.album + '.new input:visible',
					state.onFocusoutAlbumInput.bind( this ) );
				// On keyup album name input
				$( document ).on( 'keyup', _grid._item.album + '.new input:visible',
					state.onKeyupAlbumInput.bind( this ) );
				// On click Add to album
				$( document ).on( 'click', _right._slidebar + ' .tm-pg_add-to-album_form > button.tm-pg_btn',
					state.onClickAddToAlbum.bind( this ) );
				// On click Add to album
				$( document ).on( 'click', _right._slidebar + ' ' + state._item + ' .tm-pg_album-delete',
					state.onClickDeleteFromAlbum.bind( this ) );
			},
			/**
			 * On click delete from album
			 *
			 * @param {type} e - Mouse event.
			 * @returns {undefined}
			 */
			onClickDeleteFromAlbum: function ( e ) {
				e.preventDefault();
				var ids = __( 'pg-right' ).ids,
					$this = $( e.currentTarget ).parent(),
					value = $this.data( 'id' ),
					key = 0;
				state.albumAction( ids, value, key, 'delete_from_folder', $this );
			},
			/**
			 * On click add to album
			 *
			 * @param {type} e - Mouse event.
			 * @returns {undefined}
			 */
			onClickAddToAlbum: function ( e ) {
				e.preventDefault();
				var ids = __( 'pg-right' ).ids,
					$this = $( e.currentTarget ).parent(),
					value = $( 'select', $this ).val(),
					key = 0;
				state.albumAction( ids, value, key, 'add_to_folder', $this );
			},
			/**
			 * On keyup album name input
			 *
			 * @param {type} e - Mouse event.
			 * @returns {Boolean}
			 */
			onKeyupAlbumInput: function ( e ) {
				e.preventDefault();
				if ( e.keyCode === 13 ) {
					$( e.currentTarget ).blur();
					return false;
				}
			},
			/**
			 * On focusout album name input
			 *
			 * @param {type} e - Mouse event.
			 * @returns {undefined}
			 */
			onFocusoutAlbumInput: function ( e ) {
				e.preventDefault();
				var $this = $( e.currentTarget );
				if ( _.isEmpty( $this.val() ) ) {
					$this.val( 'Album name' );
				}
				state.addAlbum( false, $this );
			},
			/**
			 * Album action
			 *
			 * @param {type} ids
			 * @param {type} value
			 * @param {type} key
			 * @param {type} action
			 * @returns {undefined}
			 */
			albumAction: function ( ids, value, key, action, $this ) {
				var $params = { },
					id = ids[key],
					_content = __( 'pg-content' ),
					_folder = __( 'pg-folder' );
				if ( value ) {
					$params.value = ids;
					$params.id = value;
					$params.controller = "folder";
					$params.action = action;
					// disable rightbar
					//_content.toggleDisable( true );
					if ( $this ) {
						__( 'preloader' ).show( $this );
					}
					__( 'common' ).wpAjax( $params, function () {
						if ( _.isEqual( action, 'delete_from_folder' ) ) {
							state.removeRightCallback( ids, key, value, $this );
						} else {
							state.addRightCallback( ids, key, value, function () {
								//var _notification = __( 'notification' ),
								//    $lang = _notification.getLangData( value );
								// show notification
								//_notification.show( 'added_to_' + $lang.type, $lang.replase );
								// update folder
								_folder.updateFolder( value, 'album', function ( data ) {
									_.each( data.sets, function ( id ) {
										_folder.updateFolder( id, 'set' );
									} );
								} );
							}, $this );
						}
					} );
				}
			},
			/**
			 * Add album callback
			 *
			 * @param {type} ids
			 * @param {type} key
			 * @param {type} value
			 * @returns {undefined}
			 */
			addRightCallback: function ( ids, key, value, callback, $this ) {
				var _content = __( 'pg-content' ),
					_right   = __( 'pg-right' );

				_.each( ids, function( id ) {
					var albums   = __( 'pg-content' ).getContent( id ).albums,
						albumKey = albums.indexOf( Number( value ) );
					albums.push( Number( value ) );
				} );

				// add right folder
				__( 'folder' ).addRightFolder( value, 'album' );
				$( _right._slidebar + ' .tm-pg_add-to-album_form select.select2' ).val('').select2();

				if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
					callback();
				}
				// enable rightbar
				_content.toggleDisable( false );
				if ( $this ) {
					__( 'preloader' ).hide( $this );
				}
			},
			/**
			 * Remove album callback
			 *
			 * @param {type} ids
			 * @param {type} key
			 * @param {type} value
			 * @returns {undefined}
			 */
			removeRightCallback: function ( ids, key, value, $this ) {
				var _notification = __( 'notification' ),
					_folder = __( 'pg-folder' ),
					_content = __( 'pg-content' ),
					_right = __( 'pg-right' ),
					$lang = _notification.getLangData( value );

				_.each( ids, function( id ) {
					var albums   = __( 'pg-content' ).getContent( id ).albums,
						albumKey = albums.indexOf( Number( value ) );
					albums.splice( albumKey, 1 );
				} );

				// add right folder
				__( 'folder' ).removeRightFolder( value, 'album' );
				$( _right._slidebar + ' .tm-pg_add-to-album_form select.select2' ).select2();

				// show notification
				//_notification.show( 'delete_from_' + $lang.type, $lang.replase );
				// in folder
				if ( _folder._ID ) {
					// remove content if in folder
					_folder.removeContent( ids, value );
				}
				// update folder
				_folder.updateFolder( value, 'album', function ( data ) {
					if ( _folder._ID > 0 ) {
						_right.init( [ _folder._ID ] );
					}
					__( 'cover' ).removeCover( data, 'set' );
				} );
				// enable rightbar
				//_content.toggleDisable( false );
				if ( $this ) {
					__( 'preloader' ).hide( $this );
				}
			},
			/**
			 * Add album
			 *
			 * @param {type} $this
			 * @param {type} callback
			 * @returns {undefined}
			 */
			addAlbum: function ( callback, $this ) {
				var $params = { },
					_grid = __( 'grid' ),
					_folder = __( 'folder' ),
					view = _grid.getView(),
					$parent = $this.parents( '.tm-pg_library_item' );
				if ( _.isEqual( view, 'folder' ) ) {
					$params.parent = __( 'pg-folder' )._ID;
				}
				$params.type = state.post_type;
				$params.title = $this.val();
				$this.remove();
				// add new album or set
				_folder.addFolder( $params, function ( data ) {
					$parent.attr( 'data-id', data.id );
					__( 'pg-content' ).setContent( data.id, '', data.folder, 'album' );
					_grid.buildItem( data.id );
					// render right select
					_folder.renderRightSelect( 'album' );
					_grid.toggleSelectAllBtn( 'album', 'show' );
					// callback function
					if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
						callback( data );
					}
					// update folder
					if ( __( 'pg-folder' )._ID ) {
						__( 'pg-folder' ).updateFolder( __( 'pg-folder' )._ID, 'set' );
					}
					// show notofication
					//__( 'notification' ).show( 'add_album', { name: $params.title } );

					// photo gallery right init
					__( 'pg-right' ).init( );
				} );
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
