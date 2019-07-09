/* global _, Registry */

Registry.register( "pg-folder", ( function ( $ ) {
	"use strict";
	var state;
	/**
	 * Get state
	 *
	 * @param {type} value
	 * @returns {wp.mce.View}
	 */
	function __( value ) {
		return Registry._get( value );
	}

	/**
	 * Create instance
	 *
	 * @returns {folder_L3.createInstance.folderAnonym$0}
	 */
	function createInstance( ) {
		return {
			_back: '.tm-pg_back-btn',
			_title: '.tm-pg_page-title',
			_breadcrumbs: {
				main: '.tm-pg_breadcrumbs',
				item: '#breadcrumbs-item',
				link: '#breadcrumbs-link',
				separator: '#breadcrumbs-separator'
			},
			_type: '',
			// folder elements
			_items: {
				img: [ ],
				album: [ ]
			},
			// folder types
			_types: [ 'img', 'album' ],
			// folder id
			_ID: 0,
			// cover id
			_cover: 0,
			// curent element
			_folder: { },
			// parent element
			_parent: { },
			// items step
			_step: 40,
			// items count
			_count: 0,
			// set load ids
			_loadIds: [ ],
			_loadData: [ ],
			_accordion: {
				img: true,
				album: true
			},
			/**
			 * delete content
			 *
			 * @param {type} id
			 * @param {type} type
			 * @returns {undefined}
			 */
			deleteContent: function ( id, type ) {
				id = Number( id );
				type = type || state.getType( id );
				$.each( state._items[type], function ( key, $item ) {
					if ( !_.isUndefined( $item ) && !_.isNull( $item ) ) {
						var _id = $item.id || $item.ID;
						if ( _.isEqual( id, _id ) ) {
							delete state._items[type][key];
						}
					}
				} );
				// remove folder child
				if ( !_.isUndefined( state._folder.posts ) ) {
					var index = state._folder.posts.indexOf( id );
					if ( !_.isEqual( -1, index ) ) {
						state._folder.posts.splice( index, 1 );
					}
				}
				if ( !_.isUndefined( state._folder.childs[type] ) ) {
					var index = state._folder.childs[type].indexOf( id );
					if ( !_.isEqual( -1, index ) ) {
						state._folder.childs[type].splice( index, 1 );
					}
				}
			},
			/**
			 * Get lenght
			 *
			 * @param {type} type
			 * @returns {Number}
			 */
			getLength: function ( type ) {
				return state._items[type].length;
			},
			/**
			 * Get content
			 *
			 * @param {type} id
			 * @param {type} type
			 * @returns {state.._content|state._content}
			 */
			getContent: function ( id, type ) {
				try {
					var $return;
					id = Number( id );
					type = type || state.getType( id );
					if ( _.isEqual( 0, id ) ) {
						$return = state._items[type];
					} else {
						$.each( state._items[type], function ( key, $item ) {
							var _id = $item.id || $item.ID;
							if ( _.isEqual( id, _id ) ) {
								$return = state._items[type][key];
							}
						} );
					}
					return $return;
				} catch ( e ) {
					console.warn( e );
				}
			},
			/**
			 * Set content
			 *
			 * @param {type} id
			 * @param {type} value
			 * @param {type} type
			 * @param {type} action
			 * @returns {undefined}
			 */
			setContent: function ( id, value, type, action ) {
				id = Number( id );
				type = type || state.getType( id );
				action = action || 'append';
				// check isset key and item by key
				var $item = state.getContent( id, type ),
					_common = __( 'common' );
				if ( _.isUndefined( $item ) ) {
					if ( _.isEqual( action, 'append' ) ) {
						state._items[type].push( value );
					} else {
						state._items[type] = _common.prependArr( value, state._items[type] );
					}
				} else {
					var index = state._items[type].indexOf( value );
					state._items[type][index] = value;
				}
				// add folder child
				var index = state._folder.posts.indexOf( id );
				if ( !_.isUndefined( state._folder.posts ) && _.isEqual( -1, index ) ) {
					if ( _.isEqual( action, 'append' ) ) {
						state._folder.posts.push( id );
					} else {
						state._folder.posts = _common.prependArr( id, state._folder.posts );
					}
				}
				index = state._folder.childs[type].indexOf( id );
				if ( !_.isUndefined( state._folder.childs[type] ) && _.isEqual( -1, index ) ) {
					if ( _.isEqual( action, 'append' ) ) {
						state._folder.childs[type].push( id );
					} else {
						state._folder.childs[type] = _common.prependArr( id, state._folder.childs[type] );
					}
				}
			},
			/**
			 * Get type by id
			 *
			 * @param {type} id
			 * @returns {jQuery|opt.type|String|Boolean}
			 */
			getType: function ( id ) {
				var _type = false;
				id = Number( id );
				_.each( state._types, function ( type ) {
					_.each( state._items[type], function ( $item ) {
						if ( !_.isUndefined( $item ) && !_.isNull( $item ) ) {
							var _id = $item.id || $item.ID;
							if ( _.isEqual( id, _id ) ) {
								_type = type;
							}
						}
					} );
				} );
				return _type;
			},
			/**
			 * Init
			 *
			 * @param {type} id
			 * @param {type} type
			 * @param {type} parent
			 * @returns {undefined}
			 */
			init: function ( id, type, parent ) {
				var _grid = __( 'grid' ),
					_content = __( 'pg-content' ),
					_topBar = __( 'pg-top-bar' ),
					view = _grid.getView();
				// set folder id
				state._ID = id;
				// set folder type
				state._type = type;
				// call destruct objs
				state.destruct();
				// set parent
				state._parent = parent || 0;
				// set current folder
				state._folder = _content.getContent( id );
				// set accordion default settings
				if ( _.isUndefined( state._folder._accordion ) ) {
					state._folder._accordion = {
						img: true,
						album: true
					};
				}
				// set cover id
				state._cover = state._folder.cover_id;
				// toggle container
				if ( _.isEqual( view, 'grid' ) ) {
					state.toggleView( );
				}
				if ( !_.isUndefined( state._folder ) ) {
					// init right
					__( 'pg-right' ).init( [ state._ID ] );
					// bild folder
					state.buildFolder( function () {
						state.buildItems();
					} );
				}
				//hide show elemens
				switch ( state._type ) {
					case 'set':
						// hide top add set
						$( _topBar._addContainer.set ).addClass( 'hidden' );
						// show album container
						$( _grid.getSelector( 'folder', 'album', '_container' ) ).show();
						$( _grid.getSelector( 'folder', 'album', '_parent' ) ).show();
						if ( !state.getLength( 'album' ) ) {
							_grid.toggleSelectAllBtn( 'album' );
						}
						break;
					case 'album':
						// hide top add album and sets
						$( _topBar._addContainer.album ).addClass( 'hidden' );
						$( _topBar._addContainer.set ).addClass( 'hidden' );
						// hide album container
						$( _grid.getSelector( 'folder', 'album', '_parent' ) ).hide();
						break;
				}
				// show img container
				$( _grid.getSelector( 'folder', 'img', '_container' ) ).show();
				// toggle select all
				if ( !state.getLength( 'img' ) ) {
					_grid.toggleSelectAllBtn( 'img' );
				}
				$( _content._scrollCotainer ).scrollTop( 0 );
				// Enable add photo
				$( _grid._addItem.img ).removeClass( 'disable' );
				// check load more
				if ( state._loadData.length > 0 ) {
					state.checkLoadMore();
				} else if ( state._folder.posts.length < state._step ) {
					$( _content._loadMore ).hide();
				}
				// init accordion
				state.initAccordion();

				$( _grid.getSelector( 'folder', 'img', '_container' ) ).sortable({
					cursor: 'move',
					items: '.tm-pg_column:not(.add-new)',
					scrollSensitivity: 40,
					forcePlaceholderSize: true,
					forceHelperSize: false,
					helper: 'clone',
					opacity: 0.65,
					placeholder: 'order-placeholder tm-pg_column',
					update: function( event, ui ) {
						var order = [],
							rest  = [],
							id    = 0,
							$args = null,
							_common = __( 'common' );

						$( this ).find( '*[data-type="img"]' ).each( function( index, element ) {
							if ( undefined !== $( element ).attr( 'data-id' ) ) {
								id = $( element ).attr( 'data-id' );
								order.push( parseInt( id ) );
							}
						});

						$( document ).trigger( { type: 'reorder-grid', order: order } );

						rest = state._folder.childs.img.slice( order.length, state._folder.childs.img.length );

						if ( 0 !== rest.length ) {
							state._folder.childs.img = order.concat( rest );
						} else {
							state._folder.childs.img = order;
						}

						$args = {
							id: state._ID,
							controller: 'folder',
							action: 'reorder',
							order: state._folder.childs.img
						};
						_common.wpAjax( $args,
							function ( data ) { },
							function ( data ) {
								if ( console ) {
									console.warn( data );
								}
							},
							function ( jqXHR, request ) {
								if ( null !== request ) {
									request.abort();
								}
							}
						);
					}
				});

				$( _grid.getSelector( 'folder', 'album', '_container' ) ).sortable({
					cursor: 'move',
					items: '.tm-pg_column:not(.add-new)',
					scrollSensitivity: 40,
					forcePlaceholderSize: true,
					forceHelperSize: false,
					helper: 'clone',
					opacity: 0.65,
					placeholder: 'order-placeholder sets-placeholder tm-pg_column',
					update: function( event, ui ) {
						var order = [],
							rest  = [],
							id    = 0,
							$args = null,
							_common = __( 'common' );

						$( this ).find( '*[data-type="album"]' ).each( function( index, element ) {
							if ( undefined !== $( element ).attr( 'data-id' ) ) {
								id = $( element ).attr( 'data-id' );
								order.push( parseInt( id ) );
							}
						});

						$( document ).trigger( { type: 'reorder-album', order: order } );

						rest = state._folder.childs.album.slice( order.length, state._folder.childs.album.length );

						if ( 0 !== rest.length ) {
							state._folder.childs.album = order.concat( rest );
						} else {
							state._folder.childs.album = order;
						}

						$args = {
							id: state._ID,
							controller: 'folder',
							action: 'reorder_albums',
							order: state._folder.childs.album
						};
						_common.wpAjax( $args,
							function ( data ) { },
							function ( data ) {
								if ( console ) {
									console.warn( data );
								}
							},
							function ( jqXHR, request ) {
								if ( null !== request ) {
									request.abort();
								}
							}
						);
					}
				});
			},
			/**
			 * Init accordion
			 *
			 * @returns {undefined}
			 */
			initAccordion: function () {
				var _accordion = __( 'accordion' );
				$.each( state._folder._accordion, function ( type, status ) {
					if ( status ) {
						_accordion.showSlide( type );
					} else {
						_accordion.hideSlide( type );
					}
				} );
			},
			/**
			 * Build items
			 *
			 * @returns {undefined}
			 */
			buildItems: function () {
				var count = state._step + state._count;
				for ( var i = state._count; i < count; i++ ) {
					_.each( state._types, function ( type ) {
						if ( !_.isUndefined( state._folder.childs[type] )
							&& !_.isNull( state._folder.childs[type] ) ) {
							var id = state._folder.childs[type][i];
							if ( !_.isUndefined( id ) ) {
								var $item = __( 'pg-content' ).getContent( id );
								__( 'grid' ).init( [ $item ], type, 'folder' );
							}
						}
					} );
				}
			},
			/**
			 * Check load more
			 *
			 * @returns {undefined}
			 */
			checkLoadMore: function ( ) {
				var _content = __( 'pg-content' ),
					_lenght = state._items.img.length + state._items.album.length;
				// check load more
				if ( !_.isEqual( state._folder.posts.length, _lenght ) ) {
					$( _content._loadMore ).show();
				} else {
					$( _content._loadMore ).hide();
				}
			},
			/**
			 * build folder
			 *
			 * @param {type} callback
			 * @returns {undefined}
			 */
			buildFolder: function ( callback ) {
				state.buildHeader();
				var _content = __( 'pg-content' ),
					_common = __( 'common' );
				// set folder content
				var count = state._step + state._count;
				for ( var i = state._count; i < count; i++ ) {
					_.each( state._types, function ( type ) {
						if ( !_.isUndefined( state._folder.childs[type] )
							&& !_.isNull( state._folder.childs[type] ) ) {
							var id = state._folder.childs[type][i];
							if ( !_.isUndefined( id ) ) {
								var $item = _content.getContent( id );
								if ( _.isUndefined( $item ) ) {
									state._loadIds.push( id );
								} else {
									state.setContent( id, $item, type );
								}
							}
						}
					} );
				}
				// load items
				if ( state._loadIds.length ) {
					// load data
					state.loadData( state._loadIds, function ( $data ) {
						state._loadIds = [ ];
						// set content
						if ( !_.isUndefined( $data ) ) {
							// render loaded data
							_.each( $data, function ( $item ) {
								var _item = _content.getContent( $data.id );
								state._items.img.push( $item );
								if ( _.isUndefined( _item ) ) {
									state._loadData = _common.prependArr( $item, state._loadData );
								}
								if ( !_.isNull( $item ) ) {
									_content.setContent( $item.id, '', $item, 'img' );
								}
							} );
							// load callback
							if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
								callback(  );
							}
						}
						// hide show load more
						state.checkLoadMore();
						_content.toggleDisable( false );
					} );
				} else {
					// load callback
					if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
						callback(  );
					}
				}
			},
			/**
			 * Load item
			 *
			 * @param {type} ids
			 * @param {type} callback
			 * @returns {undefined}
			 */
			loadData: function ( ids, callback ) {
				var $params = {
					ids: ids.join( ',' ),
					controller: 'media',
					action: 'load_data'
				},
				_content = __( 'pg-content' );
				if ( state._ID > 0 ) {
					$params.parent = state._ID;
				}
				// show loader
				_content.toggleDisable( true );
				__( 'common' ).wpAjax( $params, function ( $data ) {
					if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
						callback( $data );
					}
					_content.toggleDisable( false );
				} );
			},
			/**
			 * Remove content
			 *
			 * @param {type} $ids
			 * @param {type} term_id
			 * @returns {undefined}
			 */
			removeContent: function ( $ids, term_id ) {
				if ( _.isEqual( state._ID, term_id ) ) {
					var _grid = __( 'grid' ),
						_topBar = __( 'pg-top-bar' );
					// remove content if in folder
					_.each( $ids, function ( id ) {
						_grid.getItem( id ).parent().remove();
						state.deleteContent( id );
					} );
					// taggle select all
					_.each( state._types, function ( type ) {
						if ( !state.getLength( type ) ) {
							_grid.toggleSelectAllBtn( type );
						}
					} );
					// refresh top bar
					_topBar.addTopBar();
					// init right
					__( 'pg-right' ).init( [ state._ID ] );
					// unselect all
					_topBar.unselectAll();
				}
			},
			/**
			 * Build header
			 *
			 * @returns {undefined}
			 */
			buildHeader: function ( ) {
				// set title
				var title = state._folder.post.post_title,
					step = 40;
				$( state._title + ' h2' ).attr( 'title', title );
				if ( !_.isEqual( title.substring( 0, step ), title ) ) {
					title = title.substring( 0, step ) + '...';
				}
				// set folder title
				$( state._title + ' h2' ).text( title );
				// build breadcrumbs
				var $item = $( state._breadcrumbs.item ).children().clone(),
					$separator = $( state._breadcrumbs.separator ).children().clone(),
					$link = $( state._breadcrumbs.link ).children().clone();
				// add crumbs
				$( state._breadcrumbs.main ).append( $link );
				$( state._breadcrumbs.main ).append( $separator );
				// add parent crumbs
				if ( !_.isEmpty( state._parent ) ) {
					$link = $link.clone();
					$separator = $separator.clone();
					$( 'a', $link ).attr( 'data-id', state._parent.id ).text( state._parent.post_title );
					$( state._breadcrumbs.main ).append( $link );
					$( state._breadcrumbs.main ).append( $separator );
				}
				$item.text( state._folder.post.post_title );
				$( state._breadcrumbs.main ).append( $item );
			},
			/**
			 * Toggle view
			 *
			 * @returns {undefined}
			 */
			toggleView: function ( ) {
				var _grid = __( 'grid' );
				// toggle grid
				$( _grid._main.grid ).toggleClass( 'hidden' );
				$( _grid._main.folder ).toggleClass( 'hidden' );
				// toggle header
				$( state._back ).toggleClass( 'hidden' );
				$( state._title ).toggleClass( 'hidden' );
				$( state._breadcrumbs.main ).toggleClass( 'hidden' );
				__( 'pg-content' ).initScrollbar( );
			},
			/**
			 * Update folder
			 *
			 * @param {type} id
			 * @param {type} type
			 * @param {type} callback
			 * @returns {undefined}
			 */
			updateFolder: function ( id, type, callback ) {
				var _content = __( 'pg-content' );
				id = id || state._ID;
				type = type || _content.getType( id );
				/**
				 * Send ajax to server
				 */
				__( 'common' ).wpAjax(
					{
						id: id,
						type: type,
						controller: "folder",
						action: "update"
					},
					function ( data ) {
						// update folder content
						if ( _.isEqual( state._type, type ) ) {
							state._folder = data;
						}
						_content.setContent( id, '', data, type );
						// bild folder grid
						__( 'grid' ).buildItem( id );
						if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
							callback( data );
						}
					},
					function ( data ) {
						if ( console ) {
							console.error( data );
						}
					}
				);
			},
			/**
			 * Folder events
			 *
			 * @returns {undefined}
			 */
			folderEvents: function ( ) {
				// On click back
				$( document ).on( 'click', state._back + ' a', state.onClickBack.bind( this ) );
			},
			/**
			 * On click back
			 *
			 * @param {type} e - Mouse event.
			 * @returns {undefined}
			 */
			onClickBack: function ( e ) {
				e.preventDefault();
				state.destruct();
				var _content = __( 'pg-content' ),
					_grid = __( 'grid' );

				if ( state._parent ) {
					__( 'pg-folder' ).init( state._parent.id, 'set' );
					// init accordion
				} else {
					state.destruct();
					state.toggleView( );
					state._ID = 0;
					_content.renderGrid();
					// init content
					_.each( _content._types, function ( type ) {
						_grid.toggleSelectAllBtn( type, 'show' );
					} );
					if ( _content._hideLoadMore ) {
						$( _content._loadMore ).hide();
					} else {
						$( _content._loadMore ).show();
					}
					_content.initAccordion();
					_content.replacePager( _content.globalPager );
				}
				if ( $( '#plupload-upload-ui' ).length > 0 ) {
					__( 'upload' ).closeUploader();
				}
				// reset top bar
				__( 'pg-top-bar' ).addTopBar();
			},
			/**
			 * Destruct
			 *
			 * @returns {undefined}
			 */
			destruct: function () {
				var _content = __( 'pg-content' ),
					_topBar = __( 'pg-top-bar' ),
					_grid = __( 'grid' );
				$( _grid._selectAll ).removeClass( 'selected' );
				// if load all images
				if ( !_content._lastRequest ) {
					_.each( state._loadData, function ( data ) {
						if ( !_.isNull( data ) ) {
							// Delete content
							_content.deleteContent( data.id, 'img' );
						}
					} );
					// show load more
					$( _content._loadMore ).show();
				} else {
					// hide load more
					$( _content._loadMore ).hide();
				}
				state._loadData = [ ];
				state._count = 0;
				// refresh content
				state._items = {
					img: [ ],
					album: [ ]
				};
				// reset cover
				__( 'cover' ).resetCover();
				// show top bar appends
				$( _topBar._addContainer.set ).removeClass( 'hidden' );
				$( _topBar._addContainer.album ).removeClass( 'hidden' );
				// remove breadcrumbs
				$( state._breadcrumbs.main ).children().remove();
				// remove all content
				_.each( _content._types, function ( type ) {
					$( _grid.getSelector( 'folder', type, '_item' ) ).parent().remove();
					$( _grid.getSelector( 'grid', type, '_item' ) ).parent().remove();
				} );
				__( 'pg-right' ).refresh( );
			}
		};
	}

	return {
		getInstance: function ( ) {
			if ( !state ) {
				state = createInstance( );
			}
			return state;
		}
	};
} )( jQuery ) );
