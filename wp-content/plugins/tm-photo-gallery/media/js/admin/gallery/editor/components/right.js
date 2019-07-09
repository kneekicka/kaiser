/*global _:false,Registry:false,upload:false*/
Registry.register( "gl-editor-right", ( function ( $ ) {
	"use strict";
	var state;
	function __( value ) {
		return Registry._get( value );
	}
	/**
	 * Create Instance
	 *
	 * @returns {right_L2.createInstance.rightAnonym$0}
	 */
	function createInstance() {
		return {
			// clone
			_clone: '#right-grid',
			_container: '.tm-pg_sidebar_chosen-items',
			_item: '.tm-pg_sidebar_chosen-items .tm-pg_sidebar_chosen-item',
			_header: '.tm-pg_sidebar_chosen-items_header',
			_delete: '.tm-pg_sidebar_chosen-items_delete',
			_selectAll: '.tm-pg_sidebar_chosen-items_select-all',
			_unselectAll: '.tm-pg_sidebar_chosen-items_unselect-all',
			// content
			_content: {
				img: { },
				album: { },
				set: { }
			},
			// load content
			_load_content: {
				img: [ ],
				album: [ ],
				set: [ ]
			},
			/**
			 * Init
			 *
			 * @param {type} type
			 * @returns {undefined}
			 */
			init: function ( type ) {
				state._content[type] = { };
				state._load_content[type] = [ ];
				var _editor = __( 'gl-editor' );

				if ( !_.isEmpty( _editor._gallery.childs[type] ) ) {
					$.each( _editor._content[type], function ( key, item ) {
						_.each( _editor._gallery.childs[type], function ( id ) {
							if ( _.isUndefined( _editor._content[type][id] ) ) {
								var index = state._load_content[type].indexOf( id );
								if ( _.isEqual( -1, index ) ) {
									state._load_content[type].push( id );
								}
							} else if ( _.isEqual( Number( key ), Number( id ) ) ) {
								state._content[type][id] = item;
							}
						} );
					} );
				} else if ( !_.isNull( _editor._childs ) ) {
					$.each( _editor._content[type], function ( key, item ) {
						_.each( _editor._childs[type], function ( id ) {
							if ( _.isEqual( Number( key ), Number( id ) ) ) {
								state._content[type][id] = item;
							}
						} );
					} );
				}

				$( state._container ).sortable({
					cursor: 'move',
					scrollSensitivity: 40,
					forcePlaceholderSize: true,
					forceHelperSize: false,
					helper: 'clone',
					opacity: 0.65,
					placeholder: 'order-placeholder tm-pg_column',
					update: function() {
						var order = [];
						$( state._item ).each( function() {
							order.push( $( this ).data( 'id' ) );
						});
						$( document ).trigger( { type: 'reorder-gallery', order: order } );
					}
				});
			},
			/**
			 * Init grid
			 *
			 * @returns {undefined}
			 */
			initGrid: function () {
				state.initScrollbar();
				state.toggleHeader();
			},
			/**
			 * Init events
			 *
			 * @returns {undefined}
			 */
			initEvents: function () {
				//On click right item
				$( document ).on( 'click', state._item, state.onClickRightItem.bind( this ) );
				// On click delete
				$( document ).on( 'click', state._delete, state.onClickDelete.bind( this ) );
				// Select all
				$( document ).on( 'click', state._selectAll, state.onClickSelectAll.bind( this ) );
				// Unselect all
				$( document ).on( 'click', state._unselectAll, state.onClickUnselectAll.bind( this ) );
			},
			/**
			 * On click select all
			 *
			 * @param {Object} e - Mouse event.
			 */
			onClickSelectAll: function ( e ) {
				e.preventDefault();

				var $items = $( state._item );

				$items.addClass( 'checked' );
				state.toggleHeader();
			},
			/**
			 * On click unselect all
			 *
			 * @param {Object} e - Mouse event.
			 */
			onClickUnselectAll: function ( e ) {
				e.preventDefault();

				var $items = $( state._item );

				$items.removeClass( 'checked' );
				state.toggleHeader();
			},
			/**
			 * On click delete
			 *
			 * @param {Object} e - Mouse event.
			 */
			onClickDelete: function ( e ) {
				e.preventDefault();
				var _glEditorGrid = __( 'gl-editor-grid' );
				_.each( $( state._item + '.checked' ), function ( item ) {
					var type = $( item ).data( 'type' ),
						id = $( item ).data( 'id' );
					$( _glEditorGrid._item[type] + '[data-id="' + id + '"]' ).removeClass( 'checked' );
					_glEditorGrid.toggleItem( id, type );
				} );
				state.renderItems();
			},
			/**
			 * On click right item
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onClickRightItem: function ( e ) {
				e.preventDefault();
				$( e.currentTarget ).toggleClass( 'checked' );
				state.toggleHeader();
			},
			/**
			 * Toggle right header
			 *
			 * @returns {undefined}
			 */
			toggleHeader: function () {
				if ( $( state._item ).hasClass( 'checked' ) ) {
					//$( 'h5', state._header ).removeClass( 'hide' );
					//$( 'h5 span', state._header ).text( $( state._item + '.checked' ).length );
					$( state._selectAll ).hide();
					$( state._unselectAll ).show();
					$( state._delete, state._header ).removeClass( 'hide' );
				} else {
					//$( 'h5', state._header ).addClass( 'hide' );
					$( state._selectAll ).show();
					$( state._unselectAll ).hide();
					$( state._delete, state._header ).addClass( 'hide' );
				}
			},
			/**
			 * Init scrollbar
			 *
			 * @returns {undefined}
			 */
			initScrollbar: function () {
				$( state._container ).css( 'height', '' );
				// init sidebar scrollbar
				var rightHeight = innerHeight - $( state._container ).offset().top - 45;
				if ( $( state._container ).height() > rightHeight ) {
					$( state._container ).height( rightHeight );
				}
			},
			/**
			 * Render items
			 *
			 * @returns {undefined}
			 */
			renderItems: function () {
				var html = '',
					childs = __( 'gl-editor' )._childs,
					order  = __( 'gl-editor' )._order,
					types = __( 'gl-editor-grid' )._types;

				if ( !_.isEmpty( state._load_content.img ) ) {
					state.loadData();
					return;
				}
				// add right items
				_.each( order, function ( id ) {
					_.each( types, function ( type ) {
						if ( ! _.isUndefined( state._content[ type ][ id ] ) ) {
							html += state.addItem( id );
						}
					} );
				} );

				$( state._container ).html( html );
				// build right items
				_.each( types, function ( type ) {
					$.each( state._content[type], function ( id, $item ) {
						if ( $item ) {
							state.buildItem( type, id );
						}
					} );
				} );
				// init grid events
				state.initGrid();
			},
			/**
			 * Load item
			 *
			 * @returns {undefined}
			 */
			loadData: function ( ) {
				var $params = {
					ids: state._load_content.img.join( ',' ),
					controller: 'media',
					action: 'load_data'
				},
				_editor = __( 'gl-editor' );

				// show loader
				_editor.toggleDisable( true );
				__( 'common' ).wpAjax( $params, function ( $data ) {
					_.each( $data, function ( item ) {
						//_editor._content.img
						if ( ! _.isNull( item ) ) {
							state._content.img[item.id] = item;
							_editor._content.img[item.id] = item;
						}
					} );
					state._load_content.img = [ ];
					state.renderItems();
					_editor.toggleDisable( false );
				} );
			},
			/**
			 * Add item
			 *
			 * @param {type} id
			 * @returns {undefined}
			 */
			addItem: function ( id ) {
				var $clone = $( state._clone ).clone();
				$( '.tm-pg_column', $clone ).attr( 'data-id', id );
				return $clone.html();
			},
			/**
			 * Build item
			 *
			 * @param {type} type
			 * @param {type} id
			 * @returns {undefined}
			 */
			buildItem: function ( type, id ) {
				var $data = state._content[type][id],
					$item = $( state._item + '[data-id="' + id + '"]' );
				$item.attr( 'data-type', type );
				if ( _.isEqual( type, 'img' ) ) {
					//$( 'figure img', $item ).attr( 'src', $data.thumbnails.big.url );
					$item.find( 'figure' ).css('background-image', 'url("' + $data.thumbnails.big.url + '")');
					$( 'figure', $item ).removeClass( 'hidden' );
				} else {
					if ( !_.isNull( $data.cover_img.big ) && !_.isEmpty( $data.cover_img.right ) ) {
						$( 'figure', $item ).removeClass( 'hidden' );
						//$( 'figure img', $item ).attr( 'src', $data.cover_img.big[0] );
						$item.find( 'figure' ).css('background-image', 'url("' + $data.cover_img.big[0] + '")');
					} else {
						$item.addClass( 'new' );
					}
					$( 'div[data-type="' + type + '"]', $item ).removeClass( 'hidden' );
				}
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
