/*global _:false,Registry:false,jQuery:false, console:fale*/
Registry.register( "gl-editor-grid", ( function ( $ ) {
	"use strict";
	var state;

	/**
	 * Get instance
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
	 * @returns {grid_L2.createInstance.gridAnonym$0}
	 */
	function createInstance() {
		return {
			// grid container
			_container: {
				img: 'div[data-id="photos"] .tm-pg_library_photos',
				album: 'div[data-id="albums"] .tm-pg_library_albums',
				set: 'div[data-id="sets"] .tm-pg_library_sets'
			},
			// parent
			_parent: {
				img: 'div[data-id="photos"]',
				album: 'div[data-id="albums"]',
				set: 'div[data-id="sets"]'
			},
			// clone item
			_cloneItem: {
				img: '#image-clone',
				album: '#album-clone',
				set: '#set-clone'
			},
			// grid item
			_item: {
				img: 'div[data-id="photos"] .tm-pg_library_item',
				album: 'div[data-id="albums"] .tm-pg_library_item',
				set: 'div[data-id="sets"] .tm-pg_library_item'
			},
			// add Item
			_addItem: {
				img: 'div[data-id="photos"] .tm-pg_library_item_add',
				album: 'div[data-id="albums"] .tm-pg_library_item_add',
				set: 'div[data-id="sets"] .tm-pg_library_item_add'
			},
			// grid types
			_types: [ 'img', 'album', 'set' ],
			_selectAll: '.tm-pg_library_grid_header_select_all',
			/**
			 * Init grid
			 *
			 * @param {type} $posts
			 * @param {type} type
			 * @returns {undefined}
			 */
			init: function ( $posts, type ) {
				state.renderContent( $posts, type );
			},
			/**
			 * Init Ajax
			 *
			 * @param {type} type
			 * @returns {undefined}
			 */
			initAjax: function ( type ) {
				type = type || 'img';
				// On click item
				$( document ).on( 'click', state._item[type], state.onClickItem.bind( this, type ) );
				//  On click select all
				$( document ).on( 'click', state._parent[type] + ' ' + state._selectAll,
					state.onClickSelectAll.bind( this, type ) );
				// On click  show more
				$( document ).on( 'click', state._parent[type] + ' .tm-pg-load-more',
					state.onClickShowMore.bind( this ) );
			},
			/**
			 * On click show more
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onClickShowMore: function ( e ) {
				e.preventDefault();
				var _editor = __( 'gl-editor' );
				// show loader
				_editor.toggleDisable( true );
				__( 'common' ).loadImages( { count: _editor._imgCount }, function ( $data ) {
					if ( $data.posts ) {
						_editor.renderGrid( 'img', $data.posts );
					}
					// show grid
					state.showGrid( 'img' );
					// render right items
					__( 'gl-editor-right' ).renderItems();
					if ( $data.last ) {
						_editor._lastRequest = $data.last;
						$( '.tm-pg-load-more' ).hide();
					} else {
						_editor._imgCount = $data.count;
					}
					state.checkSelectAllBtn( 'img' );
					// hide loader
					_editor.toggleDisable( false );
				} );
			},
			/**
			 * On click select all
			 *
			 * @param {type} type
			 * @param {type} e
			 * @returns {undefined}
			 */
			onClickSelectAll: function ( type, e ) {
				e.preventDefault();
				var $this = $( e.currentTarget );
				$this.toggleClass( 'selected' );
				if ( $this.hasClass( 'selected' ) ) {
					$( state._item[type] ).addClass( 'checked' );
				} else {
					$( state._item[type] ).removeClass( 'checked' );
				}
				// toggle all cheked
				$( state._item[type] ).each( function () {
					state.toggleItem( $( this ).data( 'id' ), type );
				} );
				__( 'gl-editor-right' ).renderItems();
			},
			/**
			 * On click item
			 *
			 * @param {type} type
			 * @param {type} e
			 * @returns {undefined}
			 */
			onClickItem: function ( type, e ) {
				e.preventDefault();
				$( e.currentTarget ).toggleClass( 'checked' );
				var id = $( e.currentTarget ).data( 'id' );
				state.toggleItem( id, type );
				__( 'gl-editor-right' ).renderItems();
			},
			/**
			 * Toggle item
			 *
			 * @param {type} id
			 * @param {type} type
			 * @returns {undefined}
			 */
			toggleItem: function ( id, type ) {
				var $childs  = __( 'gl-editor' )._childs[type],
					$order   = __( 'gl-editor' )._order,
					$gallery = __( 'gl-editor' )._gallery.childs[type];

				// add remove gallery childs
				if ( $( state._item[type] + '[data-id="' + id + '"]' ).hasClass( 'checked' ) ) {
					if ( _.isEqual( $.inArray( id, $childs ), -1 ) ) {
						$childs.unshift( id );
						$order.unshift( id );
					}
					if ( _.isEqual( $.inArray( id, $gallery ), -1 ) ) {
						$gallery.unshift( id );
					}
				} else {
					var index = $childs.indexOf( id ),
						indexOrder = $order.indexOf( id );
					$childs.splice( index, 1 );
					$order.splice( indexOrder, 1 );
					index = $gallery.indexOf( id );
					$gallery.splice( index, 1 );
				}
				// init editor
				_.each( state._types, function ( type ) {
					__( 'gl-editor-right' ).init( type );
				} );

				__( 'gl-editor' )._order = $order;
			},
			checkSelectAllBtn: function ( type ) {
				// select all if all selected, else unselect
				var selectedSize = state.getSelectedIds( type ).length,
					allSize = __( 'gl-editor' ).getLength( type );
				if ( _.isEqual( selectedSize, allSize ) ) {
					$( state._parent[type] + ':visible ' + state._selectAll ).addClass( 'selected' );
				} else {
					$( state._parent[type] + ':visible ' + state._selectAll ).removeClass( 'selected' );
				}
			},
			/**
			 * Hide select all
			 *
			 * @param {type} type
			 * @param {type} action
			 * @returns {undefined}
			 */
			toggleSelectAllBtn: function ( type, action ) {
				action = action || 'hide';
				if ( _.isUndefined( type ) || _.isEmpty( type ) ) {
					_.each( state._types, function ( type ) {
						// toggle select all
						_togggleSelectAll( type, action );
					} );
				} else {
					// toggle select all
					_togggleSelectAll( type, action );
				}
				// toggle select all
				function _togggleSelectAll( type, action ) {
					if ( _.isEqual( 'hide', action ) ) {
						$( state._parent[type] + ' ' + state._selectAll ).hide();
					} else {
						$( state._parent[type] + ' ' + state._selectAll ).show();
					}
				}
			},
			/**
			 * Render grid
			 *
			 * @param {type} type
			 * @returns {undefined}
			 */
			showGrid: function ( type ) {
				type = type || false;
				if ( !type ) {
					_.each( state._types, function ( type ) {
						$( state._container[type] ).show();
						$( state._addItem[type] ).parent().hide();
						// check gallery items
						state.selectItems( type );
					} );
				} else {
					$( state._container[type] ).show();
					$( state._parent[type] + ' ' + __( 'gl-editor' )._preloader ).hide();
					$( state._addItem[type] ).parent().hide();
					// check gallery items
					state.selectItems( type );
				}
			},
			/**
			 * Select items
			 *
			 * @param {type} type
			 * @returns {undefined}
			 */
			selectItems: function ( type ) {
				if ( !_.isUndefined( type ) ) {
					__( 'gl-editor-right' ).init( type );
					$.each( __( 'gl-editor-right' )._content[type], function ( id, item ) {
						if ( item ) {
							state.getItem( id, type ).addClass( 'checked' );
						}
					} );
				} else {
					_.each( state._types, function ( type ) {
						__( 'gl-editor-right' ).init( type );
						$.each( __( 'gl-editor-right' )._content[type], function ( id, item ) {
							if ( item ) {
								state.getItem( id, type ).addClass( 'checked' );
							}
						} );
					} );
				}

				state.checkSelectAllBtn( type );
			},
			/**
			 * Render content
			 *
			 * @param {type} $posts
			 * @param {type} type
			 * @param {type} callback
			 * @returns {undefined}
			 */
			renderContent: function ( $posts, type, callback ) {
				type = type || 'img';

				var html = '', selector = state._container[type];
				// add grid items
				_.each( $posts, function ( value ) {
					if ( value ) {
						var id = _.isUndefined( value.id ) ? value.ID : value.id;
						__( 'gl-editor' ).setContent( id, '', value, type );
						html += state.addItem( id, type );
					}
				} );

				$( selector ).append( html );

				// render grid items
				_.each( $posts, function ( value ) {
					if ( value ) {
						var id = _.isUndefined( value.id ) ? value.ID : value.id;
						state.buildItem( id, type );
					}
				} );
				// Callback data
				if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
					callback();
				}
			},
			/**
			 * Get item
			 *
			 * @param {type} id
			 * @param {type} type
			 * @returns {$}
			 */
			getItem: function ( id, type ) {
				type = type || __( 'gl-editor' ).getType( id );
				return $( state._item[type] + '[data-id="' + id + '"]' );
			},
			/**
			 * Get selected ids
			 *
			 * @param {type} type
			 * @returns {Array}
			 */
			getSelectedIds: function ( type ) {
				var _return = [ ];
				type = type || 'img';
				var $selected = $( state._item[type] + '.checked' );
				$.each( $selected, function ( key, value ) {
					_return[key] = $( value ).data( 'id' );
				} );
				return _return;
			},
			/**
			 * Get all selected
			 *
			 * @returns {Array}
			 */
			getAllSelectedIds: function (  ) {
				var $return = { };
				_.each( state._types, function ( type ) {
					$return[type] = state.getSelectedIds( type );
				} );
				return $return;
			},
			/**
			 * Build item
			 *
			 * @param {type} id
			 * @param {type} type
			 * @returns {undefined}
			 */
			buildItem: function ( id, type ) {
				try {
					type = type || __( 'gl-editor' ).getType( id );
					var data = __( 'gl-editor' ).getContent( id, type ),
						$item = state.getItem( id, type );
					switch ( type ) {
						case 'img':
							// hide preloader
							$( '.tm-pg_library_item_loading', $item.parents( '.tm-pg_column' ) ).addClass( 'hidden' );
							// show image
							//$( 'figure img', $item ).attr( "src", data.thumbnails.big.url );
							$item.css('background-image', 'url("' + data.thumbnails.big.url + '")');
							// hide zoom
							$( '.tm-pg_library_item-zoom', $item ).hide();
							// show item
							$item.removeClass( 'hidden' );
							break;
						case 'set':
						case 'album':
							var footer = '.tm-pg_library_item-content_footer';
							// show folder image
							if ( !_.isUndefined( data.cover_img ) && !_.isEmpty( data.cover_img[type] ) ) {
								$item.removeClass( 'new' );
								$( 'figure', $item ).removeClass( 'hidden' );
								//$( 'figure img', $item ).attr( "src", data.cover_img[type][0] );
								$item.css('background-image', 'url("' + data.cover_img[type][0] + '")');
							}
							// show footer
							$( footer + ' .tm-pg_library_item-content_footer_left', $item ).removeClass( 'hidden' );
							// hide input
							$( footer + ' input', $item ).addClass( 'hidden' );
							var title = data.post.post_title,
								step = 25;
							$( footer + ' .tm-pg_library_item-name', $item ).attr( 'title', title );
							if ( _.isEqual( type, 'album' ) ) {
								step = 15;
							}
							if ( !_.isEqual( title.substring( 0, step ), title ) ) {
								title = title.substring( 0, step ) + '...';
							}
							$( footer + ' .tm-pg_library_item-name', $item ).text( title );
							// set date
							$( footer + ' span[data-type="date"]', $item ).text( data.date );
							// set count
							$( footer + ' span[data-type="count"]', $item ).text( data.img_count );
							// albums count for set
							$( footer + ' span[data-type="albums-count"]', $item ).text( data.img_count.albums );
							// imgs count for set
							$( footer + ' span[data-type="imgs-count"]', $item ).text( data.img_count.images );
							// show checker
							$( '.tm-pg_library_item-content_header', $item ).removeClass( 'hidden' );
							break;
					}
				} catch ( e ) {
					console.warn( e );
				}
			},
			/**
			 * Unchecked All items
			 *
			 * @returns {undefined}
			 */
			unCheckedAll: function ( ) {
				_.each( state._types, function ( type ) {
					$( state._item[type] ).each( function () {
						$( this ).removeClass( 'checked' );
					} );
				} );
			},
			/**
			 *  Add new item
			 *
			 * @param {type} id
			 * @param {type} type
			 * @returns {unresolved}
			 */
			addItem: function ( id, type ) {
				type = type || 'img';
				var item = $( state._cloneItem[type] ).clone();
				$( '.tm-pg_library_item', item ).attr( "data-id", id );
				return item.html();
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
