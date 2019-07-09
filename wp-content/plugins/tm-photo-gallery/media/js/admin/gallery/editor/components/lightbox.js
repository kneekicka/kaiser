/*global _:false,Registry:false,jQuery:false, console:fale*/
Registry.register( "gl-editor-lightbox", ( function ( $ ) {
	"use strict";
	var state;

	function __( value ) {
		return Registry._get( value );
	}

	/**
	 * Create instance
	 *
	 * @returns {lightbox_L2.createInstance.lightboxAnonym$0}
	 */
	function createInstance() {
		return {
			_item: '.tm-pg_gallery_lightbox_item',
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
				_glEditor.showSection( 'lightbox' );
				// set content
				if ( _.isEmpty( state._content ) ) {
					state._content = $.extend( true, { }, _glEditor._gallery.lightbox );
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
				$( state._item + ' input:checkbox' ).each( function( ) {
					var $this = $( this ),

						name = $this.attr("name");

						if( state._content[name] )
							$this.prop( 'checked', true );
				} );
			},
			/**
			 * Init Event
			 *
			 * @returns {undefined}
			 */
			initEvents: function () {
				// on Change input
				$( document ).on( 'change', state._item + ' input', state.onChangeCheckbox.bind( this ) );
			},
			/**
			 * On change checkbox
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onChangeCheckbox: function ( e ) {
				var type = $( e.currentTarget ).parents( state._item ).data( 'type' );

				if ( $( e.currentTarget ).is( ":checked" ) ) {
					state._content[type] = 1;
				} else {
					state._content[type] = 0;
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
