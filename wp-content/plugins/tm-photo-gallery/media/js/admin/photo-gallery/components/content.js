/*global _:false,Registry:false,upload:false, console:false, jQuery:false, confirm:false, wp:false*/
Registry.register( "pg-content", ( function ( $ ) {
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
	 * @returns {content_L2.createInstance.contentAnonym$0}
	 */
	function createInstance() {
		return {
			_scrollCotainer: '#tm-pg-scroll-cotainer',
			_body: '.tm-pg_body-sidebar_container',
			_preloader: '.tm-pg_library_loading',
			_types: [ 'img', 'album', 'set' ],
			_loadMore: '.tm-pg-load-more',
			// content
			_content: {
				img: [ ],
				album: [ ],
				set: [ ]
			},
			// count
			_lastRequest: false,
			_hideLoadMore: false,
			_imgCount: 0,
			_totalCount: 0,
			_allImages: [ ],
			_accordion: {
				img: true,
				album: true,
				set: true
			},
			pager: null,
			globalPager: null,
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
						$return = state._content[type];
					} else {
						$.each( state._content[type], function ( key, $item ) {
							if ( !_.isUndefined( $item ) ) {
								var _id = $item.id || $item.ID;
								if ( _.isEqual( id, Number( _id ) ) ) {
									$return = state._content[type][key];
								}
							}
						} );
					}
					return $return;
				} catch ( e ) {
					console.warn( e );
				}
			},
			/**
			 * Get ids
			 *
			 * @param {type} type
			 * @returns {undefined}
			 */
			getIds: function ( type ) {
				try {
					var ids = [ ],
						_folder = __( 'pg-folder' );
					if ( _folder._ID > 0 ) {
						_.each( _folder._folder.childs[type], function ( id ) {
							if ( !_.isUndefined( id ) ) {
								ids.push( id );
							}
						} );
					} else {
						_.each( state._content[type], function ( $item ) {
							if ( !_.isUndefined( $item ) ) {
								ids.push( $item.id || $item.ID );
							}
						} );
					}
					return ids;
				} catch ( e ) {
					if ( console ) {
						console.warn( e.message );
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
				try {
					return state._content[type].length;
				} catch ( e ) {
					if ( console ) {
						console.warn( e );
					}
				}
			},
			/**
			 * Set content
			 *
			 * @param {type} id
			 * @param {type} key
			 * @param {type} value
			 * @param {type} type
			 * @param {type} action
			 * @returns {undefined}
			 */
			setContent: function ( id, key, value, type, action ) {
				type = type || state.getType( id );
				action = action || 'append';
				// check isset key and item by key
				var $item = state.getContent( id, type );

				if ( _.isEmpty( key ) ) {
					if ( _.isUndefined( $item ) ) {
						if ( _.isEqual( action, 'append' ) ) {
							state._content[type].push( value );
						} else {
							state._content[type] = __( 'common' ).prependArr( value, state._content[type] );
						}
					} else {
						var index = state._content[type].indexOf( $item );
						state._content[type][index] = value;
					}
				} else {
					if ( !_.isUndefined( $item ) ) {
						var index = state._content[type].indexOf( $item );

						switch ( action ) {
							case 'prepend':
								state._content[type][index][key] = value.concat( state._content[type][index][key] );
								break;
							case 'unset':
								_.each( value, function( inputItem, inputIndex ) {
									_.each( state._content[type][index][key], function( existItem, existIndex ) {
										if ( _.isUndefined( existItem ) || _.isUndefined( inputItem ) ) {
											return;
										}
										if ( existItem.term_id === inputItem.term_id ) {
											state._content[type][index][key].splice( existIndex, 1 );
										}
									} );
								});
								break;
							default:
								state._content[type][index][key] = value;
								break;
						}

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
				try {
					var _type = false;
					id = Number( id );
					_.each( state._types, function ( type ) {
						_.each( state._content[type], function ( $item ) {
							if ( !_.isUndefined( $item ) ) {
								var _id = $item.id || $item.ID;
								if ( _.isEqual( id, Number( _id ) ) ) {
									_type = type;
								}
							}
						} );
					} );
					return _type;
				} catch ( e ) {
					console.warn( e );
				}
			},
			/**
			 * delete content
			 *
			 * @param {type} id
			 * @param {type} type
			 * @returns {undefined}
			 */
			deleteContent: function ( id, type ) {
				try {
					id = Number( id );
					type = type || state.getType( id );
					$.each( state._content[type], function ( key, $item ) {
						if ( !_.isUndefined( $item ) ) {
							var _id = $item.id || $item.ID;
							if ( _.isEqual( id, Number( _id ) ) ) {
								state._content[type].splice( key, 1 );
							}
						}
					} );
				} catch ( e ) {
					console.warn( e );
				}
			},
			 /**
			 * delete term
			 *
			 * @param {type} id
			 * @param {type} type
			 * @returns {undefined}
			 */
			deleteTerm: function ( type, id ) {
				try {
					$.each( state._content, function ( key, item ) {
						$.each( state._content[key], function ( key, item ) {
							$.each( item[type], function ( termKey, termItem ) {
								if( termItem ) {
									if ( id === termItem.term_id ) {
										item[type].splice( termKey, 1 );
										return;
									}
								}
							} );
						} );
					} );
				} catch ( e ) {
					console.warn( e );
				}
			},
			/**
			 * Init scrollbar
			 *
			 * @param {type} padding
			 * @returns {undefined}
			 */
			initScrollbar: function ( padding ) {
				padding = padding || 15;
				setTimeout( function () {
					var container = $( state._scrollCotainer ).offset().top,
						centerHeight = window.innerHeight - container - padding;
					$( state._scrollCotainer ).height( centerHeight );
				}, 1000 );
				$( state._scrollCotainer ).scrollTop( 0 );
			},
			/**
			 * Scroll to element
			 *
			 * @param {type} $target
			 * @param {type} time
			 * @param {type} padding
			 * @param {type} callback
			 * @returns {undefined}
			 */
			scrollTo: function ( $target, time, padding, callback ) {
				time = time || 500;
				padding = padding || 50;
				// get selector
				// reset scroll
				$( state._scrollCotainer ).scrollTop( 0 );
				var target = $target.offset().top,
					title = $( __( 'pg-folder' )._title ).offset().top,
					back = $( __( 'pg-folder' )._back ).offset().top,
					topBar = $( __( 'pg-top-bar' )._container ).offset().top;
				// scroll to element
				$( state._scrollCotainer ).animate( {
					scrollTop: target - topBar - title - back - padding
				}, time, function () {
					if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
						callback( );
					}
				} );
			},
			/**
			 * Init accordion
			 *
			 * @returns {undefined}
			 */
			initAccordion: function () {
				var _accordion = __( 'accordion' );
				$.each( state._accordion, function ( type, status ) {
					if ( status ) {
						_accordion.showSlide( type );
					} else {
						_accordion.hideSlide( type );
					}
				} );
			},
			/**
			 * Init content
			 */
			init: function () {
				// init
				var _common = __( 'common' ),
					_grid = __( 'grid' ),
					_accordion = __( 'accordion' );
				__( 'notification' ).init();
				__( 'pg-right' ).initScrollbar();
				state.initScrollbar();
				__( 'pg-top-bar' ).addTopBar();
				// init right events
				__( 'pg-right' ).initRightEvents();
				// init folder events
				__( 'pg-folder' ).folderEvents( );
				// init popup events
				__( 'pg-popup' ).initEvents();
				// init top bar
				__( 'pg-top-bar' ).init();
				// init set events
				__( 'set' ).initEvents();
				// init album events
				__( 'album' ).initEvents();
				// init cover events
				__( 'cover' ).initEvents();
				// Init events
				_grid.initEvents();
				// load sets
				state.toggleDisable( true );
				$( state._preloader ).hide();
				_common.loadFolders( { action: "get_sets" }, function ( $data ) {
					state.renderGrid( { type: 'set', data: $data.posts } );
					// toggle select all
					if ( state.getLength( 'set' ) ) {
						_grid.toggleSelectAllBtn( 'set', 'show' );
					}
					// show set grid
					state.showGrid( { type: 'set' } );
					// init accordion
					_accordion.accordion( $( _grid._parent.set + ' .accordion' ) );
					// load albums
					_common.loadFolders( { action: "get_albums" }, function ( $data ) {
						state.renderGrid( { type: 'album', data: $data.posts } );
						// toggle select all
						if ( state.getLength( 'album' ) ) {
							_grid.toggleSelectAllBtn( 'album', 'show' );
						}
						// set album grid
						state.showGrid( { type: 'album' } );
						// init accordion
						_accordion.accordion( $( _grid._parent.album + ' .accordion' ) );
						// load content
						_common.loadImages( { }, function ( $data ) {
							// data posts
							if ( $data.posts ) {
								state.renderGrid( { type: 'img', data: $data.posts } );
							}

							if ( undefined !== $data.images_count && 40 < $data.images_count ) {
								state._hideLoadMore = false;
							} else {
								state._hideLoadMore = true;
							}

							// hide load
							if ( $data.last ) {
								state._lastRequest = $data.last;
								$( state._loadMore ).hide();
							} else if ( $data.count ) {
								if ( _.isEqual( $data.images_count, $data.count ) ) {
									state._lastRequest = true;
									$( state._loadMore ).hide();
								} else {
									$( state._loadMore ).show();
								}
								if ( !_.isUndefined( $data.pager ) ) {
									state.replacePager( $data.pager, true );
								} else {
									state._hideLoadMore = true;
								}
								state._imgCount = $data.count;
							}
							state._totalCount = $data.images_count;
							state._allImages = $data.all_posts;
							// init accordion
							_accordion.accordion( $( _grid._parent.img + ' .accordion' ) );
							// show grid
							state.showGrid( { type: 'img' } );
							// toggle select all
							if ( state.getLength( 'img' ) ) {
								_grid.toggleSelectAllBtn( 'img', 'show' );
							}
							state.toggleDisable( false );
						} );
					} );
				} );
			},
			/**
			 * Replace old pager with new if exists, if not -append new
			 */
			replacePager: function( pager, isGlobal ) {
				isGlobal = isGlobal || false;
				if ( !_.isUndefined( state.pager ) && !_.isNull( state.pager ) ) {
					state.pager.remove();
				}
				if ( true === isGlobal ) {
					state.globalPager = pager;
				}
				state.pager = $( pager ).insertAfter( state._loadMore );
			},
			/**
			 * Toggle status
			 *
			 * @param {type} status
			 * @returns {undefined}
			 */
			toggleDisable: function ( status ) {
				if ( _.isUndefined( status ) ) {
					status = true;
				}
				if ( status ) {
					$( state._body ).addClass( 'tm-pg_disable' );
				} else {
					$( state._body ).removeClass( 'tm-pg_disable' );
				}
			},
			/**
			 * Render grid
			 *
			 * @param {type} $attr
			 * @returns {undefined}
			 */
			renderGrid: function ( $attr, action ) {
				$attr = $attr || { };
				action = action || 'append';
				$attr.type = $attr.type || false;
				var _grid = __( 'grid' ),
					_folder = __( 'folder' ),
					posts,
					from,
					to;

				if ( !$attr.type ) {
					_.each( state._types, function ( type ) {

						if ( 'img' === type ) {
							to = state._content[type].length;
							if ( 40 < to ) {
								from = to - 40;
							} else {
								from = 0;
							}
							posts = state._content[type].slice( from, to );
						} else {
							posts = state._content[type];
						}

						_grid.init( posts, type, 'grid', action );
						// render right albums or sets
						_folder.renderRightSelect( type );
						// pre check all
						_grid.preCheckedAll();
					} );
				} else {
					$attr.data = $attr.data || state._content[$attr.type];
					_grid.init( $attr.data, $attr.type, 'grid', action );
					// render right albums
					_folder.renderRightSelect( $attr.type );
					// pre check all
					_grid.preCheckedAll();
				}
			},
			/**
			 * Render grid
			 *
			 * @param {type} $attr
			 * @returns {undefined}
			 */
			showGrid: function ( $attr ) {
				var _grid = __( 'grid' );
				$attr = $attr || { };
				$attr.type = $attr.type || false;
				$attr.view = $attr.view || _grid.getView();
				if ( !$attr.type ) {
					_.each( state._types, function ( type ) {
						$( _grid.getSelector( $attr.view, type, '_container' ) ).show();
					} );
				} else {
					$( _grid.getSelector( $attr.view, $attr.type, '_container' ) ).show();
				}
			},
			/**
			 * Get selected ids
			 *
			 * @param {type} type
			 * @returns {Array}
			 */
			getSelectedIds: function ( type ) {
				var _return = [ ],
					_grid = __( 'grid' ),
					view = _grid.getView();
				if ( _.isUndefined( type ) ) {
					var selected = state.getAllSelectedIds();
					// delete selected
					_.each( state._types, function ( type ) {
						_.each( selected[type], function ( id ) {
							_return.push( id );
						} );
					} );
				} else {
					var $selected = $( _grid.getSelector( view, type, '_item', '.checked' ) );
					$.each( $selected, function ( key, value ) {
						_return[key] = $( value ).data( 'id' );
					} );
				}
				return _return;
			},
			/**
			 * Get all selected
			 *
			 * @returns {Array}
			 */
			getAllSelectedIds: function (  ) {
				var images = state.getSelectedIds( 'img' ),
					albums = state.getSelectedIds( 'album' ),
					sets = state.getSelectedIds( 'set' ),
					$return = {
						img: images,
						album: albums,
						set: sets,
						count: images.length + albums.length + sets.length
					};
				return $return;
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
