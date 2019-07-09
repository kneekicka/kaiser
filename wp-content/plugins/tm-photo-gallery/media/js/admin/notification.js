/* global _, tm_pg_admin_lang */

Registry.register( "notification", ( function ( $ ) {
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
			callback: null,
			// popup container
			_popup: {
				main: '#popup-dialog-wraper',
				title: '.tm-pg_library_popup-dialog-title',
				content: '.tm-pg_library_popup-dialog-content'
			},
			/**
			 * init
			 *
			 * @returns {undefined}
			 */
			init: function () {
				// On click Yes
				$( state._popup.main ).on( 'click.notification', state._popup.content + ' a.tm-pg_btn-primary', state.onClickYes.bind( this ) );
				// On click close
				$( state._popup.main ).on( 'click.notification', state._popup.title + ' a', state.onClickClose.bind( this ) );
				// On click cancel
				$( state._popup.main ).on( 'click.notification', state._popup.content + ' a.tm-pg_btn-default', state.onClickClose.bind( this ) );
			},
			/**
			 * On click close
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onClickClose: function ( e ) {
				e.preventDefault();
				$( state._popup.main ).hide();
			},
			/**
			 * On click yes
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onClickYes: function ( e ) {
				e.preventDefault();
				$( state._popup.main ).hide();
				if ( state.callback && _.isFunction( state.callback ) ) {
					state.callback( );
				}
			},
			/**
			 * Show notification
			 *
			 * @param {type} id - lang id.
			 * @param {type} $replase
			 * @returns {undefined}
			 */
			show: function ( id, $replase ) {
				// call notification
				noty( {
					theme: 'tm-pg-noty-theme',
					timeout: 5000,
					text: state.getText( id, $replase ),
					layout: 'tmPgTopRight',
					animation: {
						open: 'animated bounceInRight',
						close: 'animated bounceOutRight',
						easing: 'swing',
						speed: 500
					}
				} );
			},
			/**
			 * Get lang data
			 *
			 * @param {type} id
			 * @param {type} ids
			 * @returns {notification_L3.createInstance.notificationAnonym$0.getLangData.notificationAnonym$2}
			 */
			getLangData: function ( id, ids ) {
				ids = ids || __( 'pg-content' ).getSelectedIds();
				var type = 'all',
					_folder = __( 'pg-folder' ),
					$replase;

				// get current id
				if ( _.isUndefined( id ) && _.isEqual( ids.length, 1 ) ) {
					id = ids[0];
				}
				// get replase params
				if ( !_.isUndefined( id ) ) {
					type = __( 'pg-content' ).getType( id );
					if ( _.isEqual( type, 'img' ) ) {
						$replase = { name: __( 'pg-content' ).getContent( id, type ).filename };
					} else {
						$replase = { name: __( 'pg-content' ).getContent( id, type ).post.post_title };
					}
				}

				if ( 0 !== _folder._ID && !_.isUndefined( id ) ) {
					$replase.parent = _folder._folder.post.post_title;
					type += '_from_folder';
				}

				if ( 0 !== _folder._ID && 'all' === type ) {
					$replase = { parent: _folder._folder.post.post_title };
					type = 'all_from_folder';
				}

				return { replase: $replase, type: type };
			},
			/**
			 * Get text
			 *
			 * @param {type} id
			 * @param {type} $replase
			 * @returns {undefined}
			 */
			getText: function ( id, $replase ) {
				var text = tm_pg_admin_lang[id] || '';
				// replase texts
				if ( !_.isEmpty( text ) && !_.isUndefined( $replase ) && _.isObject( $replase ) ) {
					$.each( $replase, function ( key, value ) {
						text = text.replace( '%' + key + '%', value );
					} );
				}
				return text;
			},
			/**
			 * Show dialod
			 *
			 * @param {type} id
			 * @param {type} $replase
			 * @param {type} callback
			 * @returns {undefined}
			 */
			showDialog: function ( id, $replase, callback ) {
				var text = state.getText( id, $replase );
				$( state._popup.main + ' h5' ).text( text );
				// show dialod
				$( state._popup.main ).show();
				state.callback = callback;
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
