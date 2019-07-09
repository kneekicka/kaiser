/*global _:false,Registry:false,jQuery:false, console:fale*/
Registry.register( "gl-editor-grid-settings", ( function ( $ ) {
	"use strict";
	var state;

	function __( value ) {
		return Registry._get( value );
	}

	/**
	 * Create instance
	 *
	 * @returns {grid-settings_L2.createInstance.grid-settingsAnonym$0}
	 */
	function createInstance() {
		return {
			_types: [ 'gallery', 'set', 'album' ],
			_holder: {
				gallery: '.tm-pg_gallery_options_container_grid_settings ',
				set:     '.tm-pg_gallery_options_container_grid_settings_set ',
				album:   '.tm-pg_gallery_options_container_grid_settings_album '
			},
			_item:       '.tm-pg_gallery_grid-settings-type_item',
			_properties: '.tm-pg_gallery_grid-settings-type_properties',
			/**
			 * Display content
			 */
			_content: { },
			/**
			 * Init grid-settings
			 *
			 * @returns {undefined}
			 */
			init: function ( ) {
				__( 'gl-editor' ).hideSection();
				__( 'gl-editor' ).showSection( 'grid' );

				// set content
				if ( _.isEmpty( state._content ) ) {
					state._content = $.extend( true, { }, __( 'gl-editor' )._gallery.grid );
				}

				// refresh active
				$( state._item + ' a' ).removeClass( 'active' );
				// select current grid
				_.each( state._types, function( type ) {
					$( state._holder[type] + state._item + ' a.tm-pg_gallery_grid-settings-type_' + state._content.type[type] ).addClass( 'active' );
				} );

				// init properties block
				state.initProperties();
			},
			initGrid: function ( ) {
				_.each( state._types, function( type ) {
					// add stepper numeral filter
					__( 'gl-editor' ).keydownStepper( $( state._holder[type] + state._properties + ' input[name="gutter"]' ) );
					__( 'gl-editor' ).keydownStepper( $( state._holder[type] + state._properties + ' input[name="height"]' ) );

					// On click grid settings grid item
					$( state._holder[type] + state._item + ' a' ).click(
						{ type: type },
						state.onClickDisplayGrid
					);

					//On change colums
					$( state._holder[type] + state._properties + '  div[data-type="colums"] select' ).change(
						{ type: type },
						state.onChangeColums
					);

					//On change height
					$( state._holder[type] + state._properties + ' div[data-type="height"] input' ).change(
						{ type: type },
						state.onChangeHeight
					);

					//On change gutter
					$( state._holder[type] + state._properties + ' div[data-type="gutter"] input' ).change(
						{ type: type },
						state.onChangeGutter
					);

					//On grid images size
					$( state._holder[type] + state._properties + ' div[data-type="grid-images-size"] select' ).change(
						{ type: type },
						state.onChangeGridImagesSize
					);

					//On masonry images size
					$( state._holder[type] + state._properties + ' div[data-type="masonry-images-size"] select' ).change(
						{ type: type },
						state.onChangeMasonryImagesSize
					);

					//On justify images size
					$( state._holder[type] + state._properties + ' div[data-type="justify-images-size"] select' ).change(
						{ type: type },
						state.onChangeJustifyImagesSize
					);
				} );
			},
			/**
			 * On change colums
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onChangeColums: function ( e ) {
				state._content.colums[e.data.type] = parseInt( e.currentTarget.value, 10 );
			},
			/**
			 * On change colums
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onChangeGutter: function ( e ) {
				state._content.gutter[e.data.type] = parseInt( e.currentTarget.value, 10 );
			},
			/**
			 * On change height
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onChangeHeight: function ( e ) {
				state._content.height[e.data.type] = parseInt( e.currentTarget.value, 10 );
			},
			/**
			 * On change grid images size
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onChangeGridImagesSize: function ( e ) {
				state._content.grid_images_size[e.data.type] = e.currentTarget.value;
			},
			/**
			 * On change grid images size
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onChangeMasonryImagesSize: function ( e ) {
				state._content.masonry_images_size[e.data.type] = e.currentTarget.value;
			},
			/**
			 * On change grid images size
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onChangeJustifyImagesSize: function ( e ) {
				state._content.justify_images_size[e.data.type] = e.currentTarget.value;
			},
			/**
			 * On click grid settings grid item
			 *
			 * @param {Object} e - Mouse event.
			 */
			onClickDisplayGrid: function ( e ) {
				e.preventDefault();

				var type = e.data.type;

				// refresh active
				$( state._holder[type] + state._item + ' a' ).removeClass( 'active' );
				// active current
				$( e.currentTarget ).addClass( 'active' );
				state._content.type[type] = $( e.currentTarget ).data( 'type' );
				state.initProperties( [type] );
			},
			/**
			 * Init Properties
			 *
			 * @returns {undefined}
			 */
			initProperties: function ( type ) {
				var types = type || state._types;

				_.each( types, function( type ){
					var colums = state._holder[type] + state._properties + ' div[data-type="colums"]',
						gutter = state._holder[type] + state._properties + ' div[data-type="gutter"]',
						height = state._holder[type] + state._properties + ' div[data-type="height"]',
						$gridSize = $( state._holder[type] + state._properties + ' div[data-type="grid-images-size"]' ),
						$gridSizeSelect = $gridSize.find('#grid-images-size'),
						$masonrySize = $( state._holder[type] + state._properties + ' div[data-type="masonry-images-size"]' ),
						$masonrySizeSelect = $masonrySize.find('#masonry-images-size'),
						$justifySize = $( state._holder[type] + state._properties + ' div[data-type="justify-images-size"]' ),
						$justifySizeSelect = $justifySize.find('#justify-images-size');

					$( colums + ' .select2' ).val( state._content.colums[type] );
					$( height + ' input' ).val( state._content.height[type] );
					$( gutter + ' input' ).val( state._content.gutter[type] );

					if ( '' === state._content.grid_images_size[type] ) {
						state._content.grid_images_size[type] = $gridSizeSelect.val();
					}
					$gridSizeSelect.val( state._content.grid_images_size[type] );

					if ( '' === state._content.masonry_images_size[type] ) {
						state._content.masonry_images_size[type] = $masonrySizeSelect.val();
					}
					$masonrySizeSelect.val( state._content.masonry_images_size[type] );

					if ( '' === state._content.justify_images_size[type] ) {
						state._content.justify_images_size[type] = $justifySizeSelect.val();
					}
					$justifySizeSelect.val( state._content.justify_images_size[type] );

					// set colums
					if ( !_.isEqual( state._content.type[type], 'justify' ) ) {
						$( colums + ' .select2' ).select2( {
							minimumResultsForSearch: -1
						} );
						$( colums ).show();
						$( height ).hide();
					} else {
						$( colums ).hide();
						$( height ).show();
					}
					if ( _.isEqual( state._content.type[type], 'grid' ) ) {
						$gridSize.show();
						$masonrySize.hide();
						$justifySize.hide();
						$gridSize.find( '.select2' ).select2( {
							minimumResultsForSearch: -1
						} );
					}
					if ( _.isEqual( state._content.type[type], 'masonry' ) ) {
						$gridSize.hide();
						$masonrySize.show();
						$justifySize.hide();
						$masonrySize.find( '.select2' ).select2( {
							minimumResultsForSearch: -1
						} );
					}
					if ( _.isEqual( state._content.type[type], 'justify' ) ) {
						$gridSize.hide();
						$masonrySize.hide();
						$justifySize.show();
						$justifySize.find( '.select2' ).select2( {
							minimumResultsForSearch: -1
						} );
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
