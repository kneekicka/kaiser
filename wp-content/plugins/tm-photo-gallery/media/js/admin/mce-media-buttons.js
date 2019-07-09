/*global tinymce:false, wp:false, console: false, md5:false, jBox:false, _:false, CommonManager:false, PopupEvents:false,Registry, tm_pg_admin_lang:false*/
( function ( $ ) {
	"use strict";
	window.shortcodes = { };
	tinymce.PluginManager.add( 'tm_photo_gallery', function ( editor, url ) {
		var popup = '#tm-pg-popup-wraper';
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
		 * Load gallery popup
		 *
		 * @param {type} callback
		 * @returns {undefined}
		 */
		function loadPopup( callback ) {
			__( 'common' ).wpAjax(
				{
					controller: "gallery",
					action: "popup"
				},
				function ( data ) {
					// callback data
					if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
						callback( data );
					}
				},
				function ( data ) {
					console.warn( 'Some error!!!' );
					console.warn( data );
				}
			);
		}
		/**
		 * Init popup
		 *
		 * @returns {undefined}
		 */
		function initPopup( ) {
			// On click close popup
			$( popup ).on( 'click', '.tm-pg_library_popup-title a', onClickClose.bind( this ) );
			// On click gallery item
			$( popup ).on( 'click', '.tm-pg_library_popup-item', onClickItem.bind( this ) );
		}
		/**
		 * On click gallery item
		 *
		 * @param {type} e
		 * @returns {undefined}
		 */
		function onClickItem( e ) {
			e.preventDefault( );
			var shortcode = wp.shortcode.string( {
				tag: "tm-pg-gallery",
				attrs: {
					id: $( e.currentTarget ).data( 'id' )
				}
			} );
			editor.insertContent( shortcode );
			$( popup ).hide( );
			$( '#wpwrap' ).attr( 'style', '' );
		}
		/**
		 * On click close popup
		 *
		 * @param {type} e
		 * @returns {undefined}
		 */
		function onClickClose( e ) {
			e.preventDefault( );
			$( '#tm-pg-popup-wraper' ).hide( );
			$( '#wpwrap' ).attr( 'style', '' );
		}
		//Gallery Button
		editor.addButton( 'add_tm_photo_gallery', {
			title: tm_pg_admin_lang['add_photo_gallery'],
			image: url + '/../../img/icon.png',
			/**
			 * On click add gallery
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onclick: function ( e ) {
				e.preventDefault( );
				// load popup html
				loadPopup( function ( data ) {
					$( popup ).remove( );
					$( 'body' ).prepend( data.html );
					$( popup ).show( );
					$( '#wpwrap' ).height( innerHeight - 50 ).css( 'overflow', 'hidden' );
					initPopup( );
				} );
			}
		} );
	} );
} )( window.jQuery );
