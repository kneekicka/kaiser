/*global _:false,Registry:false,jQuery:false, console:fale*/
Registry.register( "gl-editor", ( function ( $ ) {
	"use strict";
	var state;
	function __( value ) {
		return Registry._get( value );
	}
	/**
	 * Create instance
	 *
	 * @returns {editor_L2.createInstance.editorAnonym$0}
	 */
	function createInstance() {
		return {
			// scroll container
			_scrollCotainer: '.tm-pg-scroll-cotainer:visible',
			// types section
			_types: [ 'content', 'grid', 'display', 'animations', 'navigation', 'lightbox' ],
			// editor sections
			_section: {
				content: '#wp-gallery-editor div[data-view="content"]',
				grid: '#wp-gallery-editor div[data-view="grid-settings"]',
				display: '#wp-gallery-editor div[data-view="display"]',
				animations: '#wp-gallery-editor div[data-view="animations"]',
				navigation: '#wp-gallery-editor div[data-view="navigation"]',
				lightbox: '#wp-gallery-editor div[data-view="lightbox"]'
			},
			// container
			_container: '#wp-gallery-editor',
			// preloader
			_preloader: '.tm-pg_library_loading',
			// first load content
			_first: true,
			// loaded all content
			_loaded: false,
			// current gallery
			_gallery: { },
			// content
			_content: {
				img: { },
				album: { },
				set: { }
			},
			// content gallery childs
			_childs: {
				img: [ ],
				album: [ ],
				set: [ ],
				order: [ ]
			},
			//gallery order
			_order: { },
			// count
			_lastRequest: false,
			// img count
			_imgCount: 0,
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
					$( state._container ).addClass( 'tm-pg_disable' );
				} else {
					$( state._container ).removeClass( 'tm-pg_disable' );
				}
			},
			/**
			 * Init scrollbar
			 *
			 * @returns {undefined}
			 */
			initScrollbar: function () {
				setTimeout( function () {
					var container = $( state._scrollCotainer ).offset().top,
						centerHeight = window.innerHeight - container - 15;
					$( state._scrollCotainer ).height( centerHeight );
				}, 500 );
				$( state._scrollCotainer ).scrollTop( 0 );
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
					type = type || state.getType( id );
					// check isset id and item by id
					if ( _.isUndefined( id ) || _.isUndefined( state._content[type][id] ) ) {
						return state._content[type];
					} else {
						return state._content[type][id];
					}
				} catch ( e ) {
					if ( console ) {
						console.warn( e.message );
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
			 *
			 * @returns {undefined}
			 */
			setContent: function ( id, key, value, type ) {
				type = type || state.getType( id );
				// check isset key and item by key
				if ( !_.isEmpty( key ) ) {
					state._content[type][id][key] = value;
				} else {
					state._content[type][id] = value;
				}

			},
			/**
			 * Get lenght
			 *
			 * @param {type} type
			 * @returns {Number}
			 */
			getLength: function ( type ) {
				var content = state._content[type];

				if ( ! _.isEmpty( content ) ) {
					return Object.keys( state._content[type] ).length;
				} else {
					return false;
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
				_.each( state._types, function ( type ) {
					if ( !_.isUndefined( state._content[type][id] ) ) {
						_type = type;
					}
				} );
				return _type;
			},
			/**
			 * Init editor
			 *
			 * @param {type} id
			 * @returns {Boolean}
			 */
			init: function ( id ) {
				var _glEditor = __( 'gl-editor' ),
					_glContent = __( 'gl-content' ),
					_glEditorGrid = __( 'gl-editor-grid' );

				$( document ).on( 'reorder-gallery', state.setOrder );

				// set childs if open editor
				_glEditor.hideSection();
				_glEditor.showSection( 'content' );
				__( 'gl-editor-top-bar' ).refreshMenu();
				// unselect all btn
				$( _glEditorGrid._selectAll ).removeClass( 'selected' );
				if ( _.isUndefined( id ) || 0 == id ) {
					id = state._gallery.id;
				} else {
					state._childs = $.extend( true, { }, _glContent.getContent( id ).childs );
					state._order  = $.map( _glContent.getContent( id ).order, function( value, index ) {
						var id = parseInt( value );
						return [ id ];
					});
				}
				state._gallery = __( 'gl-content' ).getContent( id );
				// set gallery title
				var title = state._gallery.post.post_title,
					step = 40;
				$( '.tm-pg_gallery_title h2' ).attr( 'title', title );
				if ( !_.isEqual( title.substring( 0, step ), title ) ) {
					title = title.substring( 0, step ) + '...';
				}
				// set folder title
				$( '.tm-pg_gallery_title h2' ).text( title );
				// hide gallery list
				$( _glContent._container ).hide();
				// show editor
				$( state._container ).show();
				// init scrollbar
				state.initScrollbar();
				if ( !state._first ) {
					_glEditorGrid.selectItems();
					// render right items
					__( 'gl-editor-right' ).renderItems();
				} else {
					state._first = false;
					// load content
					state.loadContent();
				}
				// check select all btn
				_.each( _glEditorGrid._types, function ( type ) {
					_glEditorGrid.checkSelectAllBtn( type );
				} );
			},
			/**
			 * Set gallery order
			 */
			setOrder: function( event ) {
				state._order = event.order;
			},
			/**
			 * Load content
			 *
			 * @returns {undefined}
			 */
			loadContent: function () {
				var _common = __( 'common' ),
					_glEditorGrid = __( 'gl-editor-grid' ),
					_accordion = __( 'accordion' );
				// load sets
				state.toggleDisable( true );
				_common.loadFolders( { action: 'get_sets' }, function ( $data ) {
					state.renderGrid( 'set', $data.posts );
					// hide set block
					if ( _.isNull( $data.posts ) ) {
						$( _glEditorGrid._parent['set'] ).hide();
					}
					// toggle select all
					if ( state.getLength( 'set' ) ) {
						_glEditorGrid.toggleSelectAllBtn( 'set', 'show' );
					}
					// show content
					_glEditorGrid.showGrid( 'set' );
					// init accordion
					_accordion.accordion( $( _glEditorGrid._parent.set + ' .accordion' ) );
					// load albums
					_common.loadFolders( { action: 'get_albums' }, function ( $data ) {
						state.renderGrid( 'album', $data.posts );
						// hide set block
						if ( _.isNull( $data.posts ) ) {
							$( _glEditorGrid._parent['album'] ).hide();
						}
						// toggle select all
						if ( state.getLength( 'album' ) ) {
							_glEditorGrid.toggleSelectAllBtn( 'album', 'show' );
						}
						// show content
						_glEditorGrid.showGrid( 'album' );
						// init accordion
						_accordion.accordion( $( _glEditorGrid._parent.album + ' .accordion' ) );
						// load content
						_common.loadImages( { }, function ( $data ) {
							if ( $data.posts ) {
								state.renderGrid( 'img', $data.posts );
							}
							// hide load
							if ( $data.last ) {
								state._lastRequest = $data.last;
								$( '.tm-pg-load-more' ).hide();
							} else if ( $data.count ) {
								if ( _.isEqual( $data.images_count, $data.count ) ) {
									state._lastRequest = true;
									$( '.tm-pg-load-more' ).hide();
								} else {
									$( '.tm-pg-load-more' ).show();
								}
								state._imgCount = $data.count;
							}
							// toggle select all
							if ( state.getLength( 'img' ) ) {
								_glEditorGrid.toggleSelectAllBtn( 'img', 'show' );
							}
							// hide set block
							if ( !state.getLength( 'img' ) ) {
								$( _glEditorGrid._parent['album'] ).hide();
							}
							state._loaded = true;
							// init accordion
							_accordion.accordion( $( _glEditorGrid._parent.img + ' .accordion' ) );
							// show content
							_glEditorGrid.showGrid( 'img' );
							// hide preloader
							state.toggleDisable( false );
							// render right items
							__( 'gl-editor-right' ).renderItems();
						} );
					} );
				} );
			},
			/**
			 * Init events
			 *
			 * @returns {undefined}
			 */
			initEvents: function () {
				// On click back
				$( document ).on( 'click', '.tm-pg_back-btn a', state.onClickBack.bind( this ) );
				// On click save
				$( document ).on( 'click', '.tm-pg_gallery_save a', state.onClickSave.bind( this ) );
			},
			/**
			 * On click save
			 *
			 * @param {Object} e - Mouse event.
			 */
			onClickSave: function ( e ) {
				e.preventDefault();
				state.saveGallery( function ( $data ) {
					__( 'gl-content' ).setContent( $data.id, '', $data );
					__( 'grid' ).renderContent( __( 'gl-content' )._content['public'] );
					state.showList();
				}, $( e.target ) );
			},
			/**
			 * On click back
			 *
			 * @param {Object} e - Mouse event.
			 */
			onClickBack: function ( e ) {
				e.preventDefault();
				state.showList();
			},
			/**
			 * Show list
			 *
			 * @returns {undefined}
			 */
			showList: function () {
				__( 'gl-editor-grid' ).unCheckedAll();
				__( 'gl-editor-grid-settings' )._content  = { };
				__( 'gl-editor-display' )._content = { };
				__( 'gl-editor-animations' )._content = { };
				__( 'gl-editor-lightbox' )._content = { };
				__( 'gl-editor-navigation' )._contentFilter = { };
				__( 'gl-editor-navigation' )._contentPagination = { };
				$( state._container ).hide();

				// load content
				__( 'gl-content' ).loadContent( { }, function ( $data ) {
					_.each( __( 'gl-content' )._types, function ( type ) {
						__( 'gl-content' ).renderGrid( $data[type].data, type );
						// toggle select all
						if ( __( 'gl-content' ).getLength( type ) ) {
							__( 'grid' ).toggleSelectAllBtn( type, 'show' );
						}
					} );
					// show content
					__( 'gl-content' ).showGrid();
					$( __( 'gl-content' )._preloader ).hide();
					__( 'gl-top-bar' ).addTopBar();
					$( __( 'gl-content' )._container ).show();
				} );
				// end

				__( 'grid' ).initAjax( );
			},
			/**
			 * Save gallery
			 *
			 * @param {type} callback
			 * @returns {undefined}
			 */
			saveGallery: function ( callback, $this ) {
				state._gallery.childs = state._childs;
				state._gallery.order = state._order;
				state._gallery.grid = __( 'gl-editor-grid-settings' )._content;
				state._gallery.display = __( 'gl-editor-display' )._content;
				state._gallery.animation = __( 'gl-editor-animations' )._content;
				state._gallery.lightbox = __( 'gl-editor-lightbox' )._content;
				state._gallery.filter = __( 'gl-editor-navigation' )._contentFilter;
				state._gallery.pagination = __( 'gl-editor-navigation' )._contentPagination;
				var $args = {
					id: state._gallery.id,
					controller: "gallery",
					action: "save",
					content: state._childs,
					order: state._order,
					grid: state._gallery.grid,
					display: state._gallery.display,
					lightbox: state._gallery.lightbox,
					animation: state._gallery.animation,
					filter: state._gallery.filter,
					pagination: state._gallery.pagination
				},
					_glContent = __( 'gl-content' ),
					_notification = __( 'notification' ),
					_common = __( 'common' );
				// show preloader
				/*if ( $this ) {
					__( 'preloader' ).show( $this.parent() );
				}*/
				// disable content
				_glContent.toggleDisable();
				_common.wpAjax( $args,
					function ( data ) {
						// callback data
						if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
							callback( data );
						}
						// hide preloader
						if ( $this ) {
							__( 'preloader' ).hide( $this.parent() );
						}
						// enable content
						_glContent.toggleDisable( false );
						// show notofication
						//_notification.show( 'save_gallery', { name: state._gallery.post.post_title } );
					},
					function ( data ) {
						if ( console ) {
							console.warn( data );
						}
						// hide preloader
						if ( $this ) {
							__( 'preloader' ).hide( $this.parent() );
						}
						// enable content
						_glContent.toggleDisable( false );
						// show notofication
						_notification.show( 'error' );
					}
				);
			},
			/**
			 * Render grid
			 *
			 * @param {type} type
			 * @param {type} data
			 * @returns {undefined}
			 */
			renderGrid: function ( type, data ) {
				data = data || state._content[type];
				__( 'gl-editor-grid' ).init( data, type );
			},
			/**
			 * Show section
			 *
			 * @param {type} type
			 * @returns {undefined}
			 */
			showSection: function ( type ) {
				$( state._section[type] ).show();
			},
			/**
			 * Hide section
			 *
			 * @param {type} type
			 * @returns {undefined}
			 */
			hideSection: function ( type ) {
				if ( _.isUndefined( type ) ) {
					_.each( state._types, function ( type ) {
						$( state._section[type] ).hide();
					} );
				} else {
					$( state._section[type] ).hide();
				}
			},
			/**
			 * Stepper control keydown
			 *
			 * @param {type} type
			 * @returns {undefined}
			 */
			keydownStepper: function ( $stepper, value ) {
				var stepperMin = parseInt( $stepper.attr( 'min' ), 10 ),
					stepperMax = parseInt( $stepper.attr( 'max' ), 10 ),
					max_chars = parseInt( $stepper.attr( 'maxlength' ), 10 ) -1 || 2,
					notice,
					stepperValue,
					timeoutID;

				$stepper.keydown( function (e) {
					// Allow: backspace, delete, tab, escape, enter
					if ( $.inArray(e.keyCode, [8, 9, 27, 13, 110, 190] ) !== -1 ||
						// Allow: Ctrl+A
						( e.keyCode == 65 && e.ctrlKey === true ) ||
						// Allow: Ctrl+C
						( e.keyCode == 67 && e.ctrlKey === true ) ||
						// Allow: Ctrl+X
						( e.keyCode == 88 && e.ctrlKey === true ) ||
						// Allow: home, end, left, right
						( e.keyCode >= 35 && e.keyCode <= 39 ) ) {
							// let it happen, don't do anything
							return;
					}
					// Ensure that it is a number and stop the keypress
					if ( (e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105) ) {
						e.preventDefault();
					} else {
						if( $stepper.val().length > max_chars ) {
							$stepper.val( $stepper.val().substr( 0, max_chars ) );
						}
					}
				} );

				$stepper.focusout( function() {
					clearTimeout( timeoutID );
					stepperValue = parseInt( $stepper.val(), 10 );

					if( stepperValue < stepperMin || isNaN( stepperValue ) ) {
						if ( notice ) {
							notice.remove();
						}
						stepperValue = stepperMin;
						$stepper.after( '<div class="tm_pg_number-notice">Min value ' + stepperMin + '</div>' );
						notice = $stepper.siblings('.tm_pg_number-notice');
						timeoutID = setTimeout( removeNotice, 1500 );
					} else if( stepperValue > stepperMax ) {
						if ( notice ) {
							notice.remove();
						}
						stepperValue = stepperMax;
						$stepper.after( '<div class="tm_pg_number-notice">Max value ' + stepperMax + '</div>' );
						notice = $stepper.siblings('.tm_pg_number-notice');
						timeoutID = setTimeout( removeNotice, 1500 );
					}

					$stepper.val( stepperValue );
					$stepper.trigger( 'change' );

					function removeNotice() {
						notice.remove();
					}
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
