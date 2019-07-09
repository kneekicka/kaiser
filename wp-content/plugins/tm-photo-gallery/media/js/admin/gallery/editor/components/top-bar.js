/* global _ */

Registry.register( "gl-editor-top-bar", ( function ( $ ) {
	"use strict";
	var state;
	function __( value ) {
		return Registry._get( value );
	}
	function createInstance() {
		return {
			_menu: '.tm-pg_gallery_tabs',
			/**
			 * Init top bar
			 *
			 * @returns {undefined}
			 */
			init: function ( ) {
				// On click menu tabs
				$( document ).on( 'click', state._menu + ' a', state.onClickTabs.bind( this ) );
			},
			/**
			 * On click menu tabs
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onClickTabs: function ( e ) {
				// refresh active
				_.each( $( state._menu + ' a' ), function ( item ) {
					$( item ).removeClass( 'active' );
				} );
				// active current
				$( e.currentTarget ).addClass( 'active' );
				// load selected content
				state.initSection( $( e.currentTarget ).data( 'type' ) );
			},
			/**
			 * Refresh menu
			 *
			 * @returns {undefined}
			 */
			refreshMenu: function () {
				// refresh active
				_.each( $( state._menu + ' a' ), function ( item ) {
					$( item ).removeClass( 'active' );
				} );
				// active current
				$( state._menu + ' a[data-type="images"]' ).addClass( 'active' );
			},
			/**
			 * Load content by type
			 *
			 * @param {type} type
			 * @returns {undefined}
			 */
			initSection: function ( type ) {
				type = type !== 'images' ? '-' + type : '';
				__( 'gl-editor' + type ).init();
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
