/* global _ */

Registry.register( "gl-top-bar", ( function ( $ ) {
	"use strict";
	var state;
	function __( value ) {
		return Registry._get( value );
	}
	function createInstance() {
		return {
			_clone: {
				selected: '#selected-topbar',
				default: '#default-topbar'
			},
			_container: '.tm-pg_library-filter',
			_addContainer: 'a.tm-pg_library-filter_add-gallery',
			_menu: {
				public: '.tm-pg_library-filter_all-galleries',
				trash: '.tm-pg_library-filter_trash'
			},
			status: 'public',
			/**
			 * Init top bar
			 *
			 * @returns {undefined}
			 */
			init: function ( ) {
				// set menu count
				$( state._menu.public + ' span' ).text( __( 'gl-content' ).getLength( 'public' ) );
				$( state._menu.trash + ' span' ).text( __( 'gl-content' ).getLength( 'trash' ) );
			},
			/**
			 * Init events
			 *
			 * @returns {undefined}
			 */
			initEvents: function () {
				// On click add gallery
				$( document ).on( 'click', state._addContainer, state.onClickAddGallery.bind( this ) );
				// On click menu public
				$( document ).on( 'click', state._menu.public, state.onClickMenuPublic.bind( this ) );
				// On click menu thash
				$( document ).on( 'click', state._menu.trash, state.onClickMenuThash.bind( this ) );
				// On click close selected
				$( document ).on( 'click', '.tm-pg_library-filter_selected-close',
					state.onClickClose.bind( this ) );
				// On click restore selected
				$( document ).on( 'click', '.tm-pg_library-filter_history_container > a',
					state.onClickRestore.bind( this ) );
				// On click delete selected
				$( document ).on( 'click', '.tm-pg_library-filter_selected-settings_delete > a',
					state.onClickDelete.bind( this ) );
			},
			/**
			 * On click menu trash
			 *
			 * @param {Object} e - Mouse event.
			 */
			onClickMenuThash: function ( e ) {
				e.preventDefault();
				__( 'gl-content' ).showGrid( 'trash' );
				state.addTopBar( 'trash' );
				// toggle select all
				if ( __( 'gl-content' ).getLength( 'trash' ) ) {
					__( 'grid' ).toggleSelectAllBtn( 'trash', 'show' );
				}
			},
			/**
			 * On click menu public
			 *
			 * @param {Object} e - Mouse event.
			 */
			onClickMenuPublic: function ( e ) {
				e.preventDefault();
				__( 'gl-content' ).showGrid( 'public' );
				state.addTopBar( 'public' );
				// toggle select all
				if ( __( 'gl-content' ).getLength( 'public' ) ) {
					__( 'grid' ).toggleSelectAllBtn( 'public', 'show' );
				}
			},
			/**
			 * On click add gallery
			 *
			 * @param {type} e - Mouse event.
			 * @returns {undefined}
			 */
			onClickAddGallery: function ( e ) {
				e.preventDefault();
				__( 'grid' ).addNewItem();
			},
			/**
			 * On click delete
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onClickDelete: function ( e ) {
				e.preventDefault();
				var ids = __( 'gl-content' ).getSelectedIds( state.status ),
					action = 'trash';
				if ( _.isEqual( state.status, 'trash' ) ) {
					action = 'delete';
					__( 'notification' ).showDialog( 'delete_gallery_dialog', '', function () {
						__( 'gallery' ).deleteGallery( ids, action, function ( ) {
							// unselect all
							state.unselectAll( state.status );
						} );
					} );
				} else {
					__( 'gallery' ).deleteGallery( ids, action, function ( ) {
						__( 'grid' ).renderContent( __( 'gl-content' )._content.trash, 'trash' );
						// unselect all
						state.unselectAll( state.status );
					} );
				}
			},
			/**
			 * On click restore selected
			 *
			 * @param {Object} e - Mouse event.
			 */
			onClickRestore: function ( e ) {
				e.preventDefault();
				var ids = __( 'gl-content' ).getSelectedIds( state.status );
				__( 'gallery' ).deleteGallery( ids, 'public', function ( ) {
					__( 'grid' ).renderContent( __( 'gl-content' )._content.public, 'public' );
					// unselect all
					state.unselectAll( state.status );
				} );
			},
			/**
			 * On clicl close
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onClickClose: function ( e ) {
				e.preventDefault();
				state.unselectAll( state.status );
			},
			/**
			 * Unselect all elemets
			 *
			 * @param {type} type
			 * @returns {undefined}
			 */
			unselectAll: function ( type ) {
				type = type || 'public';
				// uncheck selected items
				var $selected = __( 'gl-content' ).getSelectedIds( type );
				_.each( $selected, function ( id ) {
					__( 'grid' ).getItem( id, type ).toggleClass( 'checked' );
				} );
				// remove selected from select all
				$( __( 'grid' )._parent[type] + ' .tm-pg_library_title a' ).removeClass( 'selected' );

				// build topnar
				state.buildTopBar( type );
			},
			/**
			 * Add topbar
			 *
			 * @param {type} type
			 * @returns {undefined}
			 */
			addTopBar: function ( type ) {
				type = type || 'public';
				state.status = type;
				// get selected count
				var $selected = __( 'gl-content' ).getSelectedIds( type );
				// add selected top bar
				if ( $selected.length > 0 ) {
					state.buildSelectedTopBar( $selected, type );
				} else { // add default top bar
					state.buildTopBar( type );
				}
			},
			/**
			 * Build top bar
			 *
			 * @param {type} type
			 * @returns {undefined}
			 */
			buildTopBar: function ( type ) {
				type = type || 'public';
				var $clone = $( state._clone.default ).clone();
				// if trash hide add gallery
				if ( _.isEqual( type, 'trash' ) ) {
					$( '.tm-pg_library-filter_add-gallery', $clone ).parent().hide();
					$( '.tm-pg_library-filter_trash', $clone ).parent().hide();
					$( '.tm-pg_library-filter_all-galleries', $clone ).parent().show();
				}

				// hide selected top bar
				if ( $( state._container ).hasClass( 'tm-pg_library-filter_selected' ) ) {
					$( state._container ).addClass( 'hide-box' );
				}
				// build top bar
				setTimeout( function () {
					$( state._container )
						.removeClass( 'tm-pg_library-filter_selected' )
						.removeClass( 'hide-box' )
						.html( $clone.children() );
					state.init();
				}, 500 );
			},
			/**
			 * Add selected top bar
			 *
			 * @param {type} $selected
			 * @param {type} type
			 * @returns {undefined}
			 */
			buildSelectedTopBar: function ( $selected, type ) {
				type = type || 'public';
				$( state._container ).addClass( 'tm-pg_library-filter_selected' );
				var $clone = $( state._clone.selected ).clone();
				$( '.tm-pg_library-filter_selected-title span', $clone ).text( $selected.length );
				// if trash show restore
				if ( _.isEqual( type, 'trash' ) ) {
					$( '.tm-pg_library-filter_history_container', $clone ).show();
				}
				$( state._container ).html( $clone.children() );
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
