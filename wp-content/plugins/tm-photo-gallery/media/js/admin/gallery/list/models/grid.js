/* global _ */

Registry.register( "grid", ( function ( $ ) {
	"use strict";
	var state;
	function __( value ) {
		return Registry._get( value );
	}
	/**
	 * Create instance
	 *
	 * @returns {grid_L3.createInstance.gridAnonym$0}
	 */
	function createInstance() {
		return {
			// grid item
			_item: {
				public: 'div[data-id="public"] .tm-pg_library_item',
				trash: 'div[data-id="trash"] .tm-pg_library_item'
			},
			// grid container
			_container: {
				public: 'div[data-id="public"] .tm-pg_library_gallery',
				trash: 'div[data-id="trash"] .tm-pg_library_gallery'
			},
			// add grid item
			_addItem: {
				public: 'div[data-id="public"] .tm-pg_library_item_add > a',
				trash: 'div[data-id="trash"] .tm-pg_library_item_add > a'
			},
			// parent
			_parent: {
				public: 'div[data-id="public"]',
				trash: 'div[data-id="trash"]'
			},
			// clone item
			_cloneItem: '#gallery-clone',
			// select all btn
			_selectAll: '.tm-pg_library_title a',
			status: 'public',
			/**
			 * Init Ajax
			 *
			 * @param {type} type
			 * @returns {undefined}
			 */
			initAjax: function ( type ) {
				type = type || 'public';
				state.status = type;
				// hide add new item
				if ( _.isEqual( type, 'trash' ) ) {
					$( state._addItem[type] ).parents( '.tm-pg_column' ).hide();
				}
			},
			/**
			 * Init events
			 *
			 * @returns {undefined}
			 */
			initEvents: function ( ) {
				_.each( __( 'gl-content' )._types, function ( type ) {
					// Add new img if add button is clicked
					$( document ).on( 'click', state._addItem[type], state.onClickAddNewItem.bind( this ) );
					// On click item
					$( document ).on( 'click', state._item[type], state.onClickItem.bind( this ) );
					// On click select all
					$( document ).on( 'click', state._parent[type] + ' .tm-pg_library_title a',
						state.onClickSelectAll.bind( this ) );
					// On click delete
					$( document ).on( 'click', state._item[type] + ' a.tm-pg_library_item-delete',
						state.onClickDelete.bind( this ) );
					// On click rename
					$( document ).on( 'click', state._item[type] + ' a.tm-pg_library_item-rename',
						state.onClickRename.bind( this ) );
					// On click cheker
					$( document ).on( 'click', state._item[type] + ' a.tm-pg_library_item-check',
						state.onClickCheker.bind( this ) );
					// On click next
					$( document ).on( 'click', state._item[type] + ' a.tm-pg_library_item-link',
						state.onClickNext.bind( this ) );

				} );
			},
			/**
			 * On click next
			 *
			 * @param {type} e - Mouse event.
			 * @returns {undefined}
			 */
			onClickNext: function ( e ) {
				e.preventDefault();
				var $item = $( e.currentTarget ).parents( state._item[state.status] ),
					id = $item.data( 'id' );
				__( 'gl-editor' ).init( id );
			},
			/**
			 * On click cheker
			 *
			 * @param {type} e - Mouse event.
			 * @returns {undefined}
			 */
			onClickCheker: function ( e ) {
				e.preventDefault( );
				var $item = $( e.currentTarget ).parents( state._item[state.status] );
				$item.toggleClass( 'checked' );
				state.checkSelectAll( state.status );
				__( 'gl-top-bar' ).addTopBar( state.status );
			},
			/**
			 * On click rename
			 *
			 * @param {type} e - Mouse event.
			 * @returns {undefined}
			 */
			onClickRename: function ( e ) {
				e.preventDefault( );
				var $item = $( e.currentTarget ).parents( state._item[state.status ] );
				state.initRename( $item, state.status );
			},
			/**
			 * On click delete
			 *
			 * @param {type} e - Mouse event.
			 * @returns {undefined}
			 */
			onClickDelete: function ( e ) {
				e.preventDefault( );
				var action = 'trash',
					$item = $( e.currentTarget ).parents( state._item[state.status ] );
				if ( _.isEqual( state.status, 'trash' ) ) {
					action = 'delete';
					__( 'notification' ).showDialog( 'delete_gallery_dialog', '', function ( ) {
						__( 'gallery' ).deleteGallery( [ $item.data( 'id' ) ], action, function ( ) {
							__( 'gl-top-bar' ).addTopBar( state.status );
							state.initAjax( state.status );
						} );
					} );
				} else {
					__( 'gallery' ).deleteGallery( [ $item.data( 'id' ) ], action, function ( ) {
						if ( _.isEqual( state.status, 'public' ) ) {
							__( 'grid' ).renderContent( __( 'gl-content' )._content.trash, 'trash' );
						}
						__( 'gl-top-bar' ).addTopBar( state.status );
						state.initAjax( state.status );
					} );
				}
			},
			/**
			 * On click select all
			 *
			 * @param {type} e - Mouse event.
			 * @returns {undefined}
			 */
			onClickSelectAll: function ( e ) {
				e.preventDefault( );
				var $this = $( e.currentTarget );
				$this.toggleClass( 'selected' );
				// toggle all cheked
				$( state._item[state.status] ).each( function ( ) {
					if ( $this.hasClass( 'selected' ) ) {
						$( this ).addClass( 'checked' );
					} else {
						$( this ).removeClass( 'checked' );
					}
				} );
				__( 'gl-top-bar' ).addTopBar( state.status );
			},
			/**
			 * On click item
			 *
			 * @param {Object} e - Mouse event.
			 */
			onClickItem: function ( e ) {
				e.preventDefault();

				var $items = $( state._item[state.status] ),
					prechecked = false,
					id;

				$items.each(function() {
					if ( $(this).hasClass( 'checked' ) ) {
						prechecked = true;
						return false;
					}
				});

				if ( !$( e.target ).hasClass( 'material-icons' ) ) {
					if ( !prechecked && 'trash' !== state.status ) {
						id = $( e.currentTarget ).data( 'id' );
						if ( 0 !== id ) {
							__( 'gl-editor' ).init( id );
						}
					} else {
						$( e.currentTarget ).toggleClass( 'checked' );
						state.checkSelectAll( state.status );
						__( 'gl-top-bar' ).addTopBar( state.status );
					}
				}
			},
			/**
			 *  Add new img if add button is clicked
			 *
			 * @param {Object} e - Mouse event.
			 */
			onClickAddNewItem: function ( e ) {
				e.preventDefault();
				state.addNewItem( state.status );
			},
			/**
			 * Check select all
			 *
			 * @param {type} type
			 * @returns {undefined}
			 */
			checkSelectAll: function ( type ) {
				var selectedSize = __( 'gl-content' ).getSelectedIds( type ).length,
					allSize = __( 'gl-content' ).getLength( type );
				if ( _.isEqual( selectedSize, allSize ) ) {
					$( state._parent[type] + ':visible ' + state._selectAll ).addClass( 'selected' );
				} else {
					$( state._parent[type] + ':visible ' + state._selectAll ).removeClass( 'selected' );
				}
			},
			/**
			 * Init rename
			 *
			 * @param {type} $item
			 * @param {type} type
			 * @returns {undefined}
			 */
			initRename: function ( $item, type ) {
				var id = $item.data( 'id' ),
					title = __( 'gl-content' ).getContent( id, type ).post.post_title;
				// hide footer
				$( '.tm-pg_library_item-content_footer_left', $item ).addClass( 'hidden' );
				$( '.tm-pg_library_item-content_footer_right', $item ).addClass( 'hidden' );
				// show input
				$( 'input[name="edit_gallery"]', $item )
					.removeClass( 'hidden' )
					.focus()
					.val( title );
			},
			/**
			 * Init album
			 *
			 * @param {type} $posts
			 * @param {type} type
			 * @returns {undefined}
			 */
			init: function ( $posts, type ) {
				// render content
				state.renderContent( $posts, type );
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
				var html = '',
					selector = state._container[type];
				// add new gallery if public
				if ( _.isEqual( type, 'public' ) ) {
					html += $( '#new-gallery-clone' ).clone().html();
				}

				// add grid items
				_.each( $posts, function ( value ) {
					if ( value ) {
						var id = value.id;
						__( 'gl-content' ).setContent( id, '', value, type );
						html += state.addItem( id, type );
					}
				} );
				// set content
				$( selector ).html( html );
				// render grid items
				_.each( $posts, function ( value ) {
					if ( value ) {
						var id = _.isUndefined( value.id ) ? value.ID : value.id;
						state.buildItem( id, type );
					}
				} );
				// show section
				$( state._container[type] ).removeClass( 'hidden' );
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
				type = type || __( 'gl-content' ).getType( id );
				return $( state._item[type] + '[data-id="' + id + '"]' );
			},
			/**
			 *  Add new item
			 *
			 * @param {type} id
			 * @returns {unresolved}
			 */
			addItem: function ( id ) {
				id = id || 0;
				var item = $( state._cloneItem ).clone();
				$( '.tm-pg_library_item', item ).attr( "data-id", id );
				return item.html();
			},
			/**
			 * Add new item
			 *
			 * @param {type} type
			 * @returns {undefined}
			 */
			addNewItem: function ( type ) {
				type = type || 'public';
				var item = state.addItem( );
				$( state._item[type] + '_add' ).parent().after( item );
				$( state._item[type] + '.new input[name="new_gallery"]' ).focus();
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
					_.each( __( 'gl-content' )._types, function ( type ) {
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
			 * Build one item
			 *
			 * @param {type} id
			 * @param {type} type
			 * @returns {undefined}
			 */
			buildItem: function ( id, type ) {
				type = type || 'public';
				var $data = __( 'gl-content' ).getContent( id, type ),
					$item = state.getItem( id, type ),
					footer = '.tm-pg_library_item-content_footer';
				// hide input
				$( footer + ' input', $item ).addClass( 'hidden' );
				// show footer
				$( footer + ' .tm-pg_library_item-content_footer_left', $item ).removeClass( 'hidden' );
				if ( _.isEqual( type, 'public' ) ) {
					$( footer + ' .tm-pg_library_item-content_footer_right', $item ).removeClass( 'hidden' );
				}
				// set gallery title
				var title = $data.post.post_title,
					step = 30;
				$( footer + ' .tm-pg_library_item-name', $item ).attr( 'title', title );
				if ( !_.isEqual( title.substring( 0, step ), title ) ) {
					title = title.substring( 0, step ) + '...';
				}
				// set folder title
				$( footer + ' .tm-pg_library_item-name', $item ).text( title );
				// set gallery date
				$( footer + ' span[data-type="date"]', $item ).text( $data.date );
				// hide rename  in trash
				if ( _.isEqual( type, 'trash' ) ) {
					$( '.tm-pg_library_item-rename', $item ).hide();
				}
				// set gallery count
				if ( !_.isEmpty( $data.childs ) ) {
					// sets count for gallery
					$( footer + ' span[data-type="sets-count"]', $item ).text( $data.img_count.sets );
					// albums count for gallery
					$( footer + ' span[data-type="albums-count"]', $item ).text( $data.img_count.albums );
					// imgs count for gallery
					$( footer + ' span[data-type="imgs-count"]', $item ).text( $data.img_count.images );
				}
				// set gallery cover
				if ( !_.isEmpty( $data.cover ) ) {
					$item.removeClass( 'new' );
					$( 'figure', $item ).removeClass( 'hidden' );
					// show image
					//$( 'figure img', $item ).attr( "src", $data.cover[0] );
					$item.css('background-image', 'url("' + $data.cover[0] + '")');
				} else {
					$item.addClass( 'new' );
					$( 'figure', $item ).addClass( 'hidden' );
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
