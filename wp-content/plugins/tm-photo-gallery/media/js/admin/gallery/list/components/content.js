/*global _:false,Registry:false,jQuery:false, console:fale*/
Registry.register( "gl-content", ( function ( $ ) {
	"use strict";
	var state;
	function __( value ) {
		return Registry._get( value );
	}
	function createInstance() {
		return {
			_scrollCotainer: '.tm-pg_library-list_gallery',
			_container: '#wp-gallery-list',
			_preloader: '.tm-pg_library_loading',
			_types: [ 'public', 'trash' ],
			_body: '#wpbody-content',
			// content
			_content: {
				public: { },
				trash: { }
			},
			/**
			 * Init scrollbar
			 *
			 * @returns {undefined}
			 */
			initScrollbar: function () {
				setTimeout( function () {
					var container = $( state._scrollCotainer ).offset().top,
						centerHeight = window.innerHeight - container - 70;
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
			 * Get lenght
			 *
			 * @param {type} type
			 * @returns {Number}
			 */
			getLength: function ( type ) {
				return Object.keys( state._content[type] ).length;
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
			 * delete content
			 *
			 * @param {type} id
			 * @param {type} type
			 * @returns {undefined}
			 */
			deleteContent: function ( id, type ) {
				type = type || state.getType( id );
				if ( !_.isUndefined( state._content[type][id] ) ) {
					delete state._content[type][id];
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
			 * Toggle status
			 *
			 * @param {type} status
			 * @returns {undefined}
			 */
			toggleDisable: function ( status ) {
				$( state._body )[ !!status ? 'addClass' : 'removeClass' ]( 'tm-pg_disable' );
			},
			/**
			 * Content initialisation
			 */
			init: function ( ) {
				state.initScrollbar();
				// init events
				__( 'gl-editor' ).initEvents();
				// init top bar
				__( 'gl-editor-top-bar' ).init();
				// init right events
				__( 'gl-editor-right' ).initEvents();
				// init animations
				__( 'gl-editor-animations' ).initEvents();
				// init navigation
				__( 'gl-editor-navigation' ).initEvents();
				// init grid settings
				__( 'gl-editor-grid-settings' ).initGrid();
				// init display
				__( 'gl-editor-display' ).initEvents();
				// init lightbox
				__( 'gl-editor-lightbox' ).initEvents();
				// init gallery grid
				__( 'gallery' ).gridInit();
				// init top bar
				__( 'gl-top-bar' ).initEvents();
				// init dialog events
				__( 'notification' ).init();
				// init grid events
				__( 'grid' ).initEvents();
				// init editor grid
				_.each( __( 'gl-editor-grid' )._types, function ( type ) {
					__( 'gl-editor-grid' ).initAjax( type );
				} );
				// load content
				state.loadContent( { }, function ( $data ) {
					_.each( state._types, function ( type ) {
						state.renderGrid( $data[type].data, type );
						// toggle select all
						if ( state.getLength( type ) ) {
							__( 'grid' ).toggleSelectAllBtn( type, 'show' );
						}
					} );
					// show content
					state.showGrid();
					$( state._preloader ).hide();
					__( 'gl-top-bar' ).addTopBar();
				} );
			},
			/**
			 * Set image is favorite
			 *
			 * @param $params
			 * @param callback
			 */
			loadContent: function ( $params, callback ) {
				var $args = {
					controller: "gallery",
					action: "content"
				};
				$.extend( $args, $params );
				__( 'common' ).wpAjax( $args,
					function ( data ) {
						// callback data
						if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
							callback( data );
						}
					},
					function ( data ) {
						if ( console ) {
							console.warn( data );
						}
					}
				);
			},
			/**
			 * Get selected ids
			 *
			 * @param {type} type
			 * @returns {Array}
			 */
			getSelectedIds: function ( type ) {
				var _return = [ ];
				type = type || 'public';
				var $selected = $( __( 'grid' )._item[type] + '.checked' );
				$.each( $selected, function ( key, value ) {
					_return[key] = $( value ).data( 'id' );
				} );
				return _return;
			},
			/**
			 * Render grid
			 *
			 * @param {type} $data
			 * @param {type} type
			 * @returns {undefined}
			 */
			renderGrid: function ( $data, type ) {
				type = type || 'public';
				$data = $data || state._content[type];
				__( 'grid' ).init( $data, type );
			},
			/**
			 * Render grid
			 *
			 * @param {type} type
			 * @returns {undefined}
			 */
			showGrid: function ( type ) {
				type = type || 'public';
				var hideType = _.isEqual( type, 'public' ) ? 'trash' : 'public',
					_grid = __( 'grid' );
				$( _grid._parent[hideType] ).hide();
				$( _grid._container[hideType] ).hide();
				$( _grid._parent[type] ).show();
				$( _grid._container[type] ).show();
				_grid.initAjax( type );
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
