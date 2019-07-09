/* global Registry */

( function ( $ ) {
	"use strict";
	$( document ).ready( function () {

		$( '.tm-pg_frontend' ).each( function () {
			var id = $( this ).attr( 'data-id' );
			Registry._get( "grid" ).init( id );
		} );

		// filter-select
		var select = $( '.filter-select' );

		$( document ).on( 'click', '.filter-select .filter-select__panel', function () {
			if ( select.hasClass( 'open' ) ) {
				select.removeClass( 'open' );
			} else {
				select.addClass( 'open' );
			}
		} );

		$( document ).on( 'click', function ( event ) {
			if ( $( event.target ).closest( select ).length || $( event.target ).closest( select ).length ) {
				return;
			}
			if ( select.hasClass( 'open' ) ) {
				select.removeClass( 'open' );
			}
			event.stopPropagation();
		} );
	} );
} )( jQuery );
