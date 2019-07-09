/*global _:false,Registry:false,jQuery:false, console:fale*/
Registry.register( "gl-editor-animations", ( function ( $ ) {
	"use strict";
	var state;

	function __( value ) {
		return Registry._get( value );
	}

	/**
	 * Create instance
	 *
	 * @returns {animations_L2.createInstance.animationsAnonym$0}
	 */
	function createInstance() {
		return {
			_item: '.tm-pg_gallery_animations_item',
			_hoverItem: '.tm-pg_gallery_hover_item',
			/**
			 * Filter content
			 */
			_content: { },
			/**
			 * Init display
			 *
			 * @returns {undefined}
			 */
			init: function ( ) {
				var _glEditor = __( 'gl-editor' );
				_glEditor.hideSection();
				_glEditor.showSection( 'animations' );
				// set content
				if ( _.isEmpty( state._content ) ) {
					state._content = $.extend( true, { }, _glEditor._gallery.animation );
				}
				// init content
				state.initContent();
			},
			/**
			 * Init content
			 *
			 * @returns {undefined}
			 */
			initContent: function () {
				$( state._item + ' :input[value="' + state._content.type + '"]' ).prop( 'checked', true );
				$( state._hoverItem + ' :input[value="' + state._content.hover_type + '"]' ).prop( 'checked', true );
			},
			/**
			 * Init Event
			 *
			 * @returns {undefined}
			 */
			initEvents: function () {
				// on Change input
				$( document ).on( 'change', state._item + ' input', state.onChangeAnimation.bind( this ) );
				$( document ).on( 'change', state._hoverItem + ' input', state.onChangeHover.bind( this ) );
			},
			/**
			 * On change animation
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onChangeAnimation: function ( e ) {
				state._content.type = $( e.currentTarget ).val();
			},
			/**
			 * On change hover
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onChangeHover: function ( e ) {
				state._content.hover_type = $( e.currentTarget ).val();
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
