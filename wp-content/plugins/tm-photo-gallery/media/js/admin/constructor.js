/* global accordion */

( function ( $ ) {
	'use strict';
	$( document ).on( 'ready', function () {
		function __( value ) {
			return Registry._get( value );
		}
		var page = __( 'common' ).getPage();
		if ( page ) {
			switch ( page ) {
				case 'tm_pg_media':
					__( 'pg-content' ).init();
					break;
				case 'gallery':
					__( 'gl-content' ).init();
					break;
			}
		}
		$( '.tm-pg_page-title div' ).remove();
	} );
} )( jQuery );
