/* global _, tm_pg_admin_lang, Registry */
Registry.register( "pg-popup", ( function ( $ ) {
	"use strict";
	var state;

	/**
	 * Get obj state
	 *
	 * @param {type} value
	 * @returns {wp.mce.View|*}
	 */
	function __( value ) {
		return Registry._get( value );
	}

	/**
	 * Create Instance
	 *
	 * @returns {popup_L2.createInstance.popupAnonym$0}
	 */
	function createInstance() {
		return {
			// popup container
			_container: '#popup-wraper',
			// poopup title
			_title: '.tm-pg_library_popup-title',
			// poopup item
			_item: {
				main: '#popup-item',
				link: '.tm-pg_library_popup-item_link'
			},
			// popup content
			_content: '.tm-pg_library_popup-content',
			status: 'album',
			/**
			 * Init popup
			 *
			 * @param {type} type
			 * @returns {undefined}
			 */
			init: function ( type ) {
				// show popup
				$( state._container ).show();
				// bild popup
				state.buildPopup( type );
				state.status = type;
			},
			/**
			 * Init events
			 *
			 * @returns {undefined}
			 */
			initEvents: function () {
				//On clik item
				$( document ).on( 'click', state._item.link, state.onClickItem.bind( this ) );
				//On click close popup
				$( document ).on( 'click', state._title + ' a', state.onClickClose.bind( this ) );
			},
			/**
			 * On click close popup
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onClickClose: function ( e ) {
				e.preventDefault();
				$( state._content ).children().remove();
				$( state._container ).hide();
			},
			/**
			 * On clicl item
			 *
			 * @param {type} e - Mouse event.
			 * @returns {undefined}
			 */
			onClickItem: function ( e ) {
				e.preventDefault();
				// add album term
				state.addToFolder( $( e.currentTarget ), state.status );
				$( state._content ).children().remove();
				$( state._container ).hide();
			},
			/**
			 * Add album term
			 *
			 * @param {type} $this
			 * @param {type} type
			 * @param {type} key
			 * @returns {undefined}
			 */
			addToFolder: function ( $this, type, key ) {
				key = key || 0;
				var _content = __( 'pg-content' ),
					_folder = __( 'pg-folder' ),
					ids = _content.getSelectedIds(),
					id = $this.data( 'id' ),
					$params = {
						id: id,
						value: ids[key],
						action: 'add_to_folder',
						controller: 'folder'
					};
				// disable content
				_content.toggleDisable( );
				__( 'common' ).wpAjax( $params, function ( ) {
					__( type ).addRightCallback( ids, key, id );
					if ( !_.isUndefined( ids[++key] ) ) {
						state.addToFolder( $this, type, key );
					} else {
						// update folder
						_folder.updateFolder( id, type, function ( data ) {
							_.each( data.sets, function ( id ) {
								_folder.updateFolder( id, 'set' );
							} );
						} );
						// enable content
						_content.toggleDisable( false );
						// show notofication
						var title = _content.getContent( id, type ).post.post_title;
						//__( 'notification' ).show( 'added_to_' + type, { name: title } );
					}
				} );
			},
			/**
			 * Build popup
			 *
			 * @param {type} type
			 * @returns {undefined}
			 */
			buildPopup: function ( type ) {
				var title = _.isEqual( type, 'album' ) ? tm_pg_admin_lang.albums : tm_pg_admin_lang.sets,
					$items = __( 'pg-content' )._content[type];
				if ( !_.isEmpty( $items ) ) {
					_.each( $items, function ( $item ) {
						state.addItem( $item, type );
					} );
				}
				$( state._title + ' h5' ).text( title );
			},
			/**
			 * Add item
			 *
			 * @param {type} $item
			 * @param {type} type
			 * @returns {undefined}
			 */
			addItem: function ( $item, type ) {
				var $clone = $( state._item.main ).clone().children(),
					title = $item.post.post_title;
				// set folder ID
				$( state._item.link, $clone ).attr( 'data-id', $item.id );
				// set folder cover
				if ( !_.isEmpty( $item.cover_img[type] ) ) {
					$( state._item.link + ' img', $clone ).removeClass( 'hide' ).attr( 'src', $item.cover_img.big[0] );
				}
				// set folder title
				$( state._item.link + ' figcaption', $clone ).attr( 'title', title );
				if ( !_.isEqual( title.substring( 0, 20 ), title ) ) {
					title = title.substring( 0, 20 ) + '...';
				}
				$( state._item.link + ' figcaption', $clone ).text( title );
				// append item
				$( state._content ).append( $clone );
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
