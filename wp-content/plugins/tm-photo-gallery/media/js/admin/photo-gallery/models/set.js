/* global _ */

Registry.register( "set", ( function ( $ ) {
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
	 * @returns {set_L3.createInstance.setAnonym$0}
	 */
	function createInstance() {
		return {
			tax_name: window.tm_pg_options.tax_names.set,
			post_type: window.tm_pg_options.post_types.set,
			_clone: '#set-term-clone',
			_item: '.tm-pg_set_container',
			/**
			 * Init Events
			 *
			 * @returns {undefined}
			 */
			initEvents: function () {
				var _grid = __( 'grid' ),
					_right = __( 'pg-right' );
				// On focusout album name input
				$( document ).on( 'focusout', _grid._item.set + '.new input:visible',
					state.onFocusoutSetInput.bind( this ) );
				// On keyup album name input
				$( document ).on( 'keyup', _grid._item.set + '.new input:visible',
					state.onKeyupSetInput.bind( this ) );
				// On click Add to album
				$( document ).on( 'click', _right._slidebar + ' .tm-pg_add-to-set_form > button.tm-pg_btn',
					state.onClickAddToSet.bind( this ) );
				// On click Add to album
				$( document ).on( 'click', _right._slidebar + ' ' + state._item + ' .tm-pg_set-delete',
					state.onClickDeleteFromSet.bind( this ) );
			},
			/**
			 * On click delete from set
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onClickDeleteFromSet: function ( e ) {
				var ids = __( 'pg-right' ).ids,
					$this = $( e.currentTarget ).parent(),
					value = $this.data( 'id' ),
					key = 0;
				state.setAction( ids, value, key, 'delete_from_folder', $this );
			},
			/**
			 * On click add to set
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onClickAddToSet: function ( e ) {
				e.preventDefault();
				var ids = __( 'pg-right' ).ids,
					$this = $( e.currentTarget ).parent(),
					value = $( 'select', $this ).val(),
					key = 0;
				state.setAction( ids, value, key, 'add_to_folder', $this );
			},
			/**
			 * On focusout set input
			 *
			 * @param {type} e - Mouse event.
			 * @returns {undefined}
			 */
			onFocusoutSetInput: function ( e ) {
				e.preventDefault();
				var $this = $( e.currentTarget );
				if ( _.isEmpty( $this.val() ) ) {
					$this.val( 'Set name' );
				}
				state.addSet( false, $this );
			},
			/**
			 * On keyup set input
			 *
			 * @param {type} e
			 * @returns {Boolean}
			 */
			onKeyupSetInput: function ( e ) {
				e.preventDefault();
				if ( e.keyCode === 13 ) {
					$( e.currentTarget ).blur();
					return false;
				}
			},
			/**
			 * Set action
			 *
			 * @param {type} ids
			 * @param {type} value
			 * @param {type} key
			 * @param {type} action
			 * @returns {undefined}
			 */
			setAction: function ( ids, value, key, action, $this ) {
				var $params = { },
					_content = __( 'pg-content' );
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
								var _notification = __( 'notification' ),
									$lang = _notification.getLangData( value );
								// show notification
								//_notification.show( 'added_to_' + $lang.type, $lang.replase );
								// update folder
								__( 'pg-folder' ).updateFolder( value, 'set' );
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
					var sets   = __( 'pg-content' ).getContent( id ).sets,
						setKey = sets.indexOf( Number( value ) );
					sets.push( Number( value ) );
				} );

				// add right folder
				__( 'folder' ).addRightFolder( value, 'set' );
				$( _right._slidebar + ' .tm-pg_add-to-set_form select.select2' ).val('').select2();

				if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
					callback(  );
				}
				// enable rightbar
				//_content.toggleDisable( false );
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
					var sets   = __( 'pg-content' ).getContent( id ).sets,
						setKey = sets.indexOf( Number( value ) );
					sets.splice( setKey, 1 );
				} );

				// add right folder
				__( 'folder' ).removeRightFolder( value, 'set' );
				$( _right._slidebar + ' .tm-pg_add-to-set_form select.select2' ).select2();
				// show notification
				//_notification.show( 'delete_from_' + $lang.type, $lang.replase );
				// in folder
				if ( _folder._ID ) {
					// remove content if in folder
					_folder.removeContent( ids, value );
				}
				// update folder
				_folder.updateFolder( value, 'set', function (  ) {
					if ( _folder._ID > 0 ) {
						_right.init( [ _folder._ID ] );
					}
				} );
				// enable rightbar
				//_content.toggleDisable( false );
				if ( $this ) {
					__( 'preloader' ).hide( $this );
				}
			},
			/**
			 * Add set
			 *
			 * @param {type} $this
			 * @param {type} callback
			 * @returns {undefined}
			 */
			addSet: function ( callback, $this ) {
				var $params = { },
					_grid = __( 'grid' ),
					_folder = __( 'folder' ),
					$parent = $this.parents( '.tm-pg_library_item' );
				$params.type = state.post_type;
				$params.title = $this.val();
				$this.remove();
				// add new album or set
				_folder.addFolder( $params, function ( data ) {
					$parent.attr( 'data-id', data.id );
					// add "set" content
					__( 'pg-content' ).setContent( data.id, '', data.folder, 'set' );
					// bild set grid
					_grid.buildItem( data.id );
					// render right select
					_folder.renderRightSelect( 'set' );
					if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
						callback( data );
					}
					_grid.toggleSelectAllBtn( 'set', 'show' );
					// show notofication
					//__( 'notification' ).show( 'add_set', { name: $params.title } );

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
