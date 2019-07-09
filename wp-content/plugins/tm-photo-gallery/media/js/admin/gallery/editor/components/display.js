/*global _:false,Registry:false,jQuery:false, console:fale*/
Registry.register( "gl-editor-display", ( function ( $ ) {
	"use strict";
	var state;

	function __( value ) {
		return Registry._get( value );
	}

	/**
	 * Create instance
	 *
	 * @returns {display_L2.createInstance.displayAnonym$0}
	 */
	function createInstance() {
		return {
			_types: [ 'gallery', 'set', 'album' ],
			_holder: {
				gallery: '.tm-pg_gallery_options_container_display ',
				set:     '.tm-pg_gallery_options_container_display_set ',
				album:   '.tm-pg_gallery_options_container_display_album '
			},
			_item: '.tm-pg_gallery_display_item',
			_colorpicker: false,
			/**
			 * Display content
			 */
			_content: { },
			/**
			 * Init display
			 *
			 * @returns {undefined}
			 */
			init: function ( ) {

				__( 'gl-editor' ).hideSection();
				__( 'gl-editor' ).showSection( 'display' );
				// set content
				if ( _.isEmpty( state._content ) ) {
					state._content = $.extend( true, { }, __( 'gl-editor' )._gallery.display );
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
				_.each( state._types, function( type ) {
					$( state._holder[type] + state._item + ' input:checkbox' ).each( function( ) {
						var $this = $( this ),
							name = $this.attr("name");
							if( state._content[name][type] )
								$this.prop( 'checked', true );
					} );
					$( state._holder[type] + state._item + ' input:text' ).each( function( ) {
						var $this = $( this ),
							name = $this.attr("name");

							if ( $this.hasClass( 'tm-color-picker' ) ) {
								if( state._content[name] ) {
									$this.val( state._content[name] );
									$this.wpColorPicker({
										change: function( event, ui ) {
											state._content[name] = ui.color.toString();
										}
									});
								}
							} else {
								if( state._content[name][type] ) {
									$this.val( state._content[name][type] );
								}
							}
					} );
					$( state._holder[type] + state._item + ' .tm-pg_number-item input' ).each( function( ) {
						var $this = $( this ),
							name = $this.attr("name");

							if( state._content[name][type] )
								$this.val( state._content[name][type] );
					} );
				} );
			},
			/**
			 * Init Event
			 *
			 * @returns {undefined}
			 */
			initEvents: function () {
				_.each( state._types, function( type ) {
					// add stepper numeral filter
					__( 'gl-editor' ).keydownStepper( $( state._holder[type] + state._item + ' .tm-pg_number-item input' ) );

					//On change colums
					$( state._holder[type] + state._properties + ' div[data-type="colums"] select' ).change(
						{ type: type },
						state.onChangeColums
					);

					// on Change checkbox
					$( state._holder[type] + state._item + ' input:checkbox' ).change(
						{ type: type },
						state.onChangeCheckbox
					);

					// on Change text
					$( state._holder[type] + state._item + ' input:text' ).change(
						{ type: type },
						state.onChangeText
					);

					// on Change text
					$( state._holder[type] + state._item + ' .tm-pg_number-item input' ).change(
						{ type: type },
						state.onChangeNumber
					);

				} );
			},
			/**
			 * On change checkbox
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onChangeCheckbox: function ( e ) {
				var name = $( e.currentTarget ).attr("name"),
					type = e.data.type;

				if ( $( e.currentTarget ).is( ":checked" ) ) {
					state._content[name][type] = 1;
				} else {
					state._content[name][type] = 0;
				}
			},
			/**
			 * On change text
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onChangeText: function ( e ) {
				var name = $( e.currentTarget ).parents( state._item ).data( 'type' ),
					type = e.data.type;

				state._content[name][type] = e.target.value;
			},
			/**
			 * On change number
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onChangeNumber: function ( e ) {
				var name = $( e.currentTarget ).parents( state._item ).data( 'type' ),
					type = e.data.type;

				state._content[name][type] = e.target.value;
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
