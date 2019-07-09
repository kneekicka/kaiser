/* global Registry */

Registry.register( "slider", ( function ( $ ) {
	"use strict";
	var state,
		prefix = '.tm-pg_frontend';

	/**
	 * Get Registry
	 *
	 * @param {type} value
	 * @returns {wp.mce.View|*}
	 */
	function __( value ) {
		return Registry._get( value );
	}
	/**
	 * get instace
	 *
	 * @returns {grid_L1.createInstance.gridAnonym$0}
	 */
	function createInstance() {
		return {
			container: '',
			gallery: { },
			_content: {
				grid: '.tm-pg_front_gallery-grid',
				masonry: '.tm-pg_front_gallery-masonry',
				justify: '.tm-pg_front_gallery-justify'
			},
			_item: 'div.tm_pg_gallery-item[data-type="img"] a',
			__$: function ( value ) {
				return $( state.container + ' ' + value );
			},
			/**
			 * Init gallery
			 *
			 * @param {type} parent_id
			 * @returns {undefined}
			 */
			init: function ( parent_id ) {
				state.container = prefix + '[data-id="' + parent_id + '"]';

				var view = $( state.container ).data( 'view' ),
					gallery = state.__$( state._content[view] );

				if ( undefined !== gallery.data('lightGallery') ) {
					gallery.data('lightGallery').destroy( true );
				}

				gallery.lightGallery( {
					autoplayControls: gallery.data( 'lightbox-autoplay' ) === false ? false : true,
					thumbnail: gallery.data( 'lightbox-thumbnails' ) === false ? false : true,
					fullScreen: gallery.data( 'lightbox-fullscreen' ) === false ? false : true,
					controls: gallery.data( 'lightbox-arrows' ) === false ? false : true,
					selector: state._item,
					animateThumb: true,
					showThumbByDefault: true,
					toogleThumb: true,
					thumbContHeight: 80
				} );
				// Perform any action just before opening the gallery
				gallery.on( 'onBeforeOpen.lg', function ( event ) {
					$( '#wpadminbar' ).css( 'z-index', '0' );
				} );
				gallery.on( 'onCloseAfter.lg', function ( event ) {
					$( '#wpadminbar' ).css( 'z-index', '' );
				} );
				state.gallery = gallery.data( 'lightGallery' );
			},
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
