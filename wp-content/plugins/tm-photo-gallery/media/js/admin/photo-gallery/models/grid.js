/* global _, Registry */

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
			// main container
			_main: {
				grid: '#tm-pg-grid',
				folder: '#tm-pg-folder'
			},
			// parent
			_parent: {
				img: 'div[data-id="photos"]',
				album: 'div[data-id="albums"]',
				set: 'div[data-id="sets"]'
			},
			_order: [],
			// grid container
			_container: {
				img: 'div[data-id="photos"] .tm-pg_library_photos',
				album: 'div[data-id="albums"] .tm-pg_library_albums',
				set: 'div[data-id="sets"] .tm-pg_library_sets'
			},
			// grid item
			_item: {
				img: 'div[data-id="photos"] .tm-pg_library_item',
				album: 'div[data-id="albums"] .tm-pg_library_item',
				set: 'div[data-id="sets"] .tm-pg_library_item'
			},
			// add grid item
			_addItem: {
				img: 'div[data-id="photos"] .tm-pg_library_item_add > a',
				album: 'div[data-id="albums"] .tm-pg_library_item_add > a',
				set: 'div[data-id="sets"] .tm-pg_library_item_add > a'
			},
			// clone item
			_cloneItem: {
				img: '#image-clone',
				album: '#album-clone',
				set: '#set-clone'
			},
			// select all btn
			_selectAll: '.tm-pg_library_grid_header_select_all',
			_showMore: '.tm-pg-load-more .tm-pg_btn.tm-pg_btn-primary',
			/**
			 * Get view
			 *
			 * @returns {String}
			 */
			getView: function () {
				var view = 'grid';
				if ( _.isEqual( $( state._main.folder ).is( ":visible" ), true ) ) {
					view = 'folder';
				}
				return view;
			},
			/**
			 * Get selector
			 *
			 * @param {type} view
			 * @param {type} type
			 * @param {type} selector
			 * @param {type} value
			 * @returns {String}
			 */
			getSelector: function ( view, type, selector, value ) {
				value = value || '';
				return state._main[view] + ' ' + state[selector][type] + value;
			},
			/**
			 * Init Events
			 *
			 * @returns {undefined}
			 */
			initEvents: function ( ) {

				var _content = __( 'pg-content' );

				$( document ).on( 'click', '.tm_pager-item-btn', state.processPager );

				$( document ).on( 'click', state._showMore, state.onClickShowMore.bind( this ) );
				_.each( _content._types, function ( type ) {
					// On click item
					$( document ).on( 'click', state._item[type] + ':visible',
						state.onClickItem.bind( this ) );
					// On click select all
					$( document ).on( 'click', state._parent[type] + ':visible ' + state._selectAll,
						state.onClickSelectAll.bind( this ) );
					// On click cheker
					$( document ).on( 'click', state._item[type] + ':visible a.tm-pg_library_item-check',
						state.onClickCheker.bind( this ) );
					// On click zoom
					$( document ).on( 'click', state._item[type] + ':visible a.tm-pg_library_item-zoom',
						state.onClickZoom.bind( this ) );
					// On click next
					$( document ).on( 'click', state._item[type] + ':visible a.tm-pg_library_item-link',
						state.onClickNext.bind( this ) );
					// Add new img if add button is clicked
					$( document ).on( 'click', state._addItem[type], state.onClickAddNewItem.bind( this ) );
					// Reorder on sort
					$( document ).on( 'reorder-grid', state.reorderGrid );
				} );
			},
			reorderGrid: function( event ) {
				state._order = event.order;
			},
			/**
			 * On click add new item
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onClickAddNewItem: function ( e ) {
				e.preventDefault();
				var view = state.getView(),
					type = $( e.currentTarget ).parent().data( 'type' );
				switch ( type ) {
					case 'img':
						// show popup in folder
						if ( __( 'pg-folder' )._ID > 0 ) {
							__( 'media-popup' ).init();
						} else {
							__( 'image' ).showUpload();
						}
						break;
					case 'album':
					case 'set':
						state.addNewItem( type, view );
						break;
				}
			},
			/**
			 * On click next
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onClickNext: function ( e ) {
				e.preventDefault();
				var $parent = 0,
					$item = $( e.currentTarget ).parents( '.tm-pg_library_item:visible' );
				if ( _.isEqual( state.getView(), 'folder' ) ) {
					$parent = __( 'pg-folder' )._folder;
				}
				__( 'pg-folder' ).init( $item.data( 'id' ), $item.data( 'type' ), $parent );
				// init top bar
				__( 'pg-top-bar' ).addTopBar();
				if ( null !== __( 'pg-content' ).pager ) {
					__( 'pg-content' ).pager.remove();
				}
			},
			/**
			 * On click zoom
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onClickZoom: function ( e ) {
				e.preventDefault();
				var $item = $( e.currentTarget ).parents( '.tm-pg_library_item:visible' );
				state.editItem( $item );
			},
			/**
			 * On click checker
			 *
			 * @param {type} e - Mouse event.
			 * @returns {undefined}
			 */
			onClickCheker: function ( e ) {
				e.preventDefault();
				var $item = $( e.currentTarget ).parents( '.tm-pg_library_item:visible' );
				state.checkItem( $item );
			},
			/**
			 * On click select all
			 *
			 * @param {type} e - Mouse event.
			 * @returns {undefined}
			 */
			onClickSelectAll: function ( e ) {
				e.preventDefault();
				var $this = $( e.currentTarget ),
					item = '.tm-pg_library_item:visible';
				$this.toggleClass( 'selected' );
				// toggle all cheked
				if ( $this.hasClass( 'selected' ) ) {
					$( item, $this.parents( '.accordion-content' ) ).addClass( 'checked' );
				} else {
					$( item, $this.parents( '.accordion-content' ) ).removeClass( 'checked' );
				}
				// pre check all
				state.preCheckedAll();
				// init right bar
				__( 'pg-right' ).init( );
				// init top bar
				__( 'pg-top-bar' ).addTopBar();
			},
			/**
			 * On click item
			 *
			 * @param {type} e - Mouse event.
			 * @returns {undefined}
			 */
			onClickItem: function ( e ) {
				e.preventDefault();
				var $item = $( e.currentTarget ),
					type = $item.data( 'type' );
				if ( _.isEqual( e.target, e.currentTarget ) ) {
					if ( $item.hasClass( 'pre-checked' ) || $item.hasClass( 'checked' ) ) {
						state.checkItem( $item );
					} else {
						switch ( type ) {
							case 'img':
								state.editItem( $item );
								break;
							case 'album':
							case 'set':
								var $parent = 0;
								if ( _.isEqual( state.getView(), 'folder' ) ) {
									$parent = __( 'pg-folder' )._folder;
								}
								__( 'pg-folder' ).init( $item.data( 'id' ), type, $parent );
								__( 'pg-content' ).pager.remove();
								break;
						}
					}
				} else if ( $item.hasClass( 'new' ) ) {
					if ( !$( e.target ).hasClass( 'material-icons' ) ) {
						state.checkItem( $item );
					}
				}
			},
			/**
			 * On click show more
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onClickShowMore: function ( e ) {
				e.preventDefault();
				var _content = __( 'pg-content' ),
					_folder = __( 'pg-folder' ),
					args = { count: _content._imgCount },
					paged,
					lastPage,
					newPage;
				if ( _folder._ID > 0 ) {
					_folder._count += _folder._step;
					_folder.buildFolder( function () {
						_folder.buildItems();
						_folder.checkLoadMore();
					} );
					state.toggleSelectAllBtn( 'img', 'hide' );
					return false;
				}

				if ( !_.isUndefined( _content.pager ) && !_.isNull( _content.pager ) ) {
					paged      = _content.pager.attr( 'data-show-pages' );
					lastPage   = _content.pager.attr( 'data-last' );
					lastPage   = parseInt( lastPage );
					newPage    = lastPage + 1;
					args.paged = paged + ',' + newPage;
					args.count = lastPage*_folder._step;
				}

				// show loader
				_content.toggleDisable( true );
				__( 'common' ).loadImages( args, function ( $data ) {
					// show grid
					_content.showGrid( { type: 'img' } );
					if ( $data.posts ) {
						_content.renderGrid( { type: 'img', data: $data.posts } );
					}
					if ( $data.last ) {
						_content._lastRequest = $data.last;
						$( _content._loadMore ).hide();
					} else {
						_content._imgCount = $data.count;

					}
					if ( !_.isUndefined( $data.pager ) ) {
						_content.replacePager( $data.pager );
					}
					state.checkSelectAllBtn( 'img' );
					// hide loader
					_content.toggleDisable( false );
				} );
				state.toggleSelectAllBtn( 'img', 'hide' );
			},
			processPager: function() {
				var _content = __( 'pg-content' ),
					_folder = __( 'pg-folder' ),
					args = { count: _content._imgCount },
					page;

				if ( _folder._ID > 0 ) {
					_folder._count += _folder._step;
					_folder.buildFolder( function () {
						_folder.buildItems();
						_folder.checkLoadMore();
					} );
					return false;
				}

				page              = $(this).attr( 'data-page' );
				page              = parseInt( page );
				args.paged        = page;
				args.getPage      = page;
				args.requestFirst = true;

				__( 'pg-top-bar' ).unselectAll();

				// show loader
				_content.toggleDisable( true );
				__( 'common' ).loadImages( args, function ( $data ) {
					// show grid
					_content.showGrid( { type: 'img' } );
					if ( $data.posts ) {
						_content.renderGrid( { type: 'img', data: $data.posts }, 'replace' );
					}
					if ( $data.last ) {
						_content._lastRequest = $data.last;
						$( _content._loadMore ).hide();
					} else {
						$( _content._loadMore ).show();
						_content._imgCount = $data.count;

					}
					if ( !_.isUndefined( $data.pager ) ) {
						_content.replacePager( $data.pager );
					}
					state.checkSelectAllBtn( 'img' );

					$( state._container.img ).each( function() {
						if ( ! $( '> .add-new', $( this ) ).length ) {
							$( this ).prepend( $data.first_item );
						}
					});

					// hide loader
					_content.toggleDisable( false );
				} );

				state.toggleSelectAllBtn( 'img', 'show' );
			},
			/**
			 * Toggle add photo button
			 *
			 * @returns {undefined}
			 */
			toggleAddPhotoBtn: function () {
				var $btn = $( state._addItem.img );
				if ( $btn.hasClass( 'disable' ) ) {
					$btn.removeClass( 'disable' );
				} else {
					$btn.addClass( 'disable' );
				}
			},
			/**
			 * Pre checked all items
			 *
			 * @param {type} view
			 * @returns {undefined}
			 */
			preCheckedAll: function ( view ) {
				view = view || 'grid';
				var $selected = __( 'pg-content' ).getSelectedIds( );
				_.each( __( 'pg-content' )._types, function ( type ) {
					$( state._item[type] + ':visible' ).each( function () {
						if ( $selected.length ) {
							if ( !$( this ).hasClass( 'checked' ) ) {
								$( this ).addClass( 'pre-checked' );
							} else {
								$( this ).removeClass( 'pre-checked' );
							}
						} else {
							$( this ).removeClass( 'pre-checked' );
						}
					} );
				} );
			},
			/**
			 * Init grid
			 *
			 * @param {type} $posts
			 * @param {type} type
			 * @param {type} view
			 * @returns {undefined}
			 */
			init: function ( $posts, type, view, action ) {
				type = type || 'img';
				view = view || 'grid';
				action = action || 'append';
				// render content
				if ( !_.isEmpty( $posts ) ) {
					state.renderContent( $posts, action, type, view );
				}
			},
			/**
			 * Check item
			 *
			 * @param {type} $item
			 * @returns {undefined}
			 */
			checkItem: function ( $item ) {
				var type = $item.data( 'type' );
				$item.toggleClass( 'checked' );
				state.preCheckedAll();
				__( 'pg-right' ).init( );
				__( 'pg-top-bar' ).addTopBar();
				// select all if all selected, else unselect
				state.checkSelectAllBtn( type );
			},
			/**
			 * Check select all btn
			 *
			 * @param {type} type
			 * @returns {undefined}
			 */
			checkSelectAllBtn: function ( type ) {
				var _content = __( 'pg-content' ),
					selectedSize = _content.getSelectedIds( type ).length,
					allSize = _content.getLength( type );
				if ( _.isEqual( selectedSize, allSize ) ) {
					$( state._parent[type] + ' ' + state._selectAll ).addClass( 'selected' );
				} else {
					$( state._parent[type] + ' ' + state._selectAll ).removeClass( 'selected' );
				}
			},
			/**
			 * Call editor
			 *
			 * @param {type} $item
			 * @returns {undefined}
			 */
			editItem: function ( $item ) {
				var checked = true;
				if ( !$item.hasClass( 'checked' ) ) {
					checked = false;
					$item.addClass( 'checked' );
				}
				__( 'pg-editor' ).init(
					$( '#tm-pg-editor' ),
					_.template( $( '#tm-pg-editor-tpl' ).html() ),
					__( 'pg-content' ).getContent( $item.data( 'id' ) ),
					true,
					__( 'pg-content' ).getIds( 'img' )
					);
				__( 'pg-content' ).initScrollbar( 5 );
				if ( !checked ) {
					$item.removeClass( 'checked' );
				}
			},
			/**
			 * Render content
			 *
			 * @param {type} $posts
			 * @param {type} action
			 * @param {type} type
			 * @param {type} view
			 * @param {type} callback
			 * @returns {undefined}
			 */
			renderContent: function ( $posts, action, type, view, callback ) {
				action = action || 'append';
				type = type || 'img';
				view = view || state.getView();
				var html = '', selector = state.getSelector( view, type, '_container' );
				// add grid items
				_.each( $posts, function ( value ) {
					if ( value ) {
						var id = _.isUndefined( value.id ) ? value.ID : value.id;
						__( 'pg-content' ).setContent( id, '', value, type, action );
						html += state.addItem( id, type );
					}
				} );

				// set content
				switch ( action ) {
					case 'append':
						$( selector ).append( html );
						break;
					case 'prepend':
						$( selector ).prepend( html );
						break;
					case 'replace':
						$( selector ).html( html );
						break;
				}

				// render grid items
				_.each( $posts, function ( value ) {
					if ( value ) {
						var id = _.isUndefined( value.id ) ? value.ID : value.id;
						state.buildItem( id );
					}
				} );
				// Callback data
				if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
					callback();
				}
			},
			/**
			 * Add new item
			 *
			 * @param {type} type
			 * @param {type} view
			 * @returns {undefined}
			 */
			addNewItem: function ( type, view ) {
				type = type || 'img';
				view = view || state.getView();
				var item = state.addItem( 0, type );
				$( state.getSelector( view, type, '_item', '_add' ) ).parent().after( item );
				$( state.getSelector( view, type, '_item', '.new input' ) ).focus();
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
			},
			/**
			 * Get item
			 *
			 * @param {type} id
			 * @returns {$}
			 */
			getItem: function ( id ) {
				return $( '.tm-pg_library_item[data-id="' + id + '"]' );
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
					_.each( __( 'pg-content' )._types, function ( type ) {
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
			 * Delete item
			 *
			 * @param {type} id
			 * @param {type} callback
			 * @returns {undefined}
			 */
			deleteItem: function ( id, callback ) {
				var $args = {
						id: id,
						controller: "media",
						action: "delete"
					},
					_folder  = __( 'pg-folder' );

				if ( ! _.isUndefined( _folder._ID ) ) {
					$args.parent = _folder._ID;
					$args.from   = _folder._type;
				}
				__( 'common' ).wpAjax( $args,
					function ( data ) {
						if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
							callback( data );
						}
					},
					function ( data ) {
						console.warn( 'Some error!!!' );
						console.warn( data );
					}
				);
			},
			/**
			 * Build one item
			 *
			 * @param {type} id
			 * @returns {undefined}
			 */
			buildItem: function ( id ) {
				try {
					var $item = state.getItem( id ),
						type = $item.data( 'type' ),
						data = __( 'pg-content' ).getContent( id, type );
					switch ( type ) {
						case 'img':
							// hide preloader
							$( '.tm-pg_library_item_loading', $item.parents( '.tm-pg_column' ) ).addClass( 'hidden' );
							// show image
							//$( 'figure img', $item ).attr( "src", data.thumbnails.big.url );
							//
							$item.css('background-image', 'url("' + data.thumbnails.big.url + '")');
							// show item
							$item.removeClass( 'hidden' );
							break;
						case 'set':
						case 'album':
							var footer = '.tm-pg_library_item-content_footer';
							// show folder image
							if ( !_.isUndefined( data.cover_img ) && !_.isEmpty( data.cover_img[type] ) ) {
								var date = new Date( );
								$item.removeClass( 'new' );
								$( 'figure', $item ).removeClass( 'hidden' );
								//$( 'figure img', $item ).attr( "src", data.cover_img[type][0] + '?' + date.getTime( ) );
								$item.css('background-image', 'url("' + data.cover_img[type][0] + '?' + date.getTime( ) + '")');
							} else {
								$item.addClass( 'new' );
								$( 'figure', $item ).addClass( 'hidden' );
							}
							// show footer
							$( footer + ' .tm-pg_library_item-content_footer_left', $item ).removeClass( 'hidden' );
							$( footer + ' .tm-pg_library_item-content_footer_right', $item ).removeClass( 'hidden' );
							// hide input
							$( footer + ' input', $item ).addClass( 'hidden' );
							// set title
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
