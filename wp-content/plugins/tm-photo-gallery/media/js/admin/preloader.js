/* global _, tm_pg_admin_lang */

Registry.register( "preloader", ( function ( $ ) {
	"use strict";
	var state;
	/**
	 *
	 *
	 * @param {type} value
	 * @returns {wp.mce.View|*}
	 */
	function __( value ) {
		return Registry._get( value );
	}
	/**
	 * Create instance
	 */
	function createInstance() {
		return {
			show: function ( element ) {
				if ( element ) {
					element.addClass( 'control-disable' );
					element.find('.spinner').addClass('is-active');
				}
			},
			hide: function ( element ) {
				if ( element ) {
					element.removeClass( 'control-disable' );
					element.find('.spinner').removeClass('is-active');
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
