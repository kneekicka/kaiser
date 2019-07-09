/* global _ */

Registry.register( "cover", ( function ( $ ) {
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
	 * @returns {cover_L3.createInstance.coverAnonym$0}
	 */
	function createInstance() {
		return {
			_content: '.tm-pg_library-filter_selected-settings_cover',
			/**
			 * Init events
			 *
			 * @returns {undefined}
			 */
			initEvents: function () {
				// On click set cover
				$( document ).on( 'click', state._content + '[data-type="set_cover"]', state.onClickSetCover.bind( this ) );
				// On click set cover
				$( document ).on( 'click', state._content + '[data-type="unset_cover"]', state.onClickUnsetCover.bind( this ) );
			},
			/**
			 * On click unset cover
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onClickUnsetCover: function ( e ) {
				e.preventDefault();
				var _content = __( 'pg-content' ),
					_folder = __( 'pg-folder' );
				// disable content
				_content.toggleDisable( );
				// update folder
				state.updateCover( 0, function (  ) {
					var id = _folder._ID,
						type = _content.getType( id );
					_folder.updateFolder( id, type, function ( data ) {
						console.log( data );
					} );
				} );
			},
			/**
			 * On click set cover
			 *
			 * @param {type} e - Mouse event.
			 * @returns {undefined}
			 */
			onClickSetCover: function ( e ) {
				e.preventDefault();
				var _content = __( 'pg-content' ),
					_folder = __( 'pg-folder' ),
					ids = _content.getSelectedIds();
				// disable content
				_content.toggleDisable( );
				// update cover
				state.updateCover( ids[0], function () {
					var id = _folder._ID,
						type = _content.getType( id );
					_folder.updateFolder( _folder._ID, type, function ( data ) {
						if ( !_.isUndefined( data.sets ) ) {
							_.each( data.sets, function ( set_id ) {
								_folder.updateFolder( set_id, 'set' );
							} );
						}
					} );
				} );
			},
			/**
			 * Init
			 *
			 * @returns {undefined}
			 */
			init: function ( ) {
				var ids = __( 'pg-content' ).getSelectedIds();
				if ( ids.length > 1 ) {
					$( state._content ).hide();
				} else {
					// check current folder id
					if ( _.isEqual( ids[0], __( 'pg-folder' )._cover ) ) {
						$( state._content + '[data-type="unset_cover"]' ).show();
						$( state._content + '[data-type="set_cover"]' ).hide();
					} else {
						$( state._content + '[data-type="set_cover"]' ).show();
						$( state._content + '[data-type="unset_cover"]' ).hide();
					}
				}
			},
			/**
			 * Remove cover
			 *
			 * @param {type} data
			 * @param {type} type
			 * @returns {undefined}
			 */
			removeCover: function ( data, type ) {
				var _folder = __( 'pg-folder' ),
					_content = __( 'pg-content' ),
					folders = data.albums;
				if(_.isEqual('set',type)){
					folders = data.sets;
				}
				_.each( folders, function ( id ) {
					var item = _content.getContent( id, type );
					if ( _.isEqual( item.cover_id, data.id ) ) {
						// update folder
						state.updateCover( 0, function (  ) {
							_folder.updateFolder( id, type );
						} );
					}
				} );
			},
			/**
			 * Reset cover
			 *
			 * @returns {undefined}
			 */
			resetCover: function () {
				$( state._content + '[data-type="unset_cover"]' ).hide();
				$( state._content + '[data-type="set_cover"]' ).hide();
			},
			/**
			 * Update cover
			 *
			 * @param {type} id
			 * @param {type} callback
			 * @returns {undefined}
			 */
			updateCover: function ( id, callback ) {
				var _folder = __( 'pg-folder' ),
					_content = __( 'pg-content' ),
					_notification = __( 'notification' ),
					$args = {
						controller: "folder",
						action: "change_cover",
						parent_id: _folder._ID,
						id: id
					};
				// send data to sever
				__( 'common' ).wpAjax( $args,
					function ( data ) {
						if ( data.update ) {
							_folder._folder.cover_id = id;
							_folder._cover = id;
						}
						state.init();
						if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
							callback( data );
						}
						// enable content
						_content.toggleDisable( false );
						var lang = 'set_cover';
						if ( !id ) {
							lang = 'remove_cover';
						}
						// show notofication
						/*_notification.show( lang, {
							name: _folder._folder.post.post_title,
							type: _folder._type
						} );*/
					},
					function ( data ) {
						if ( console ) {
							console.warn( data );
						}
						// enable content
						_content.toggleDisable( false );
						// show notofication
						_notification.show( 'error' );
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
