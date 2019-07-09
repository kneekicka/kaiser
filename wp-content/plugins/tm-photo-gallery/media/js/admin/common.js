/* global _:false,console:false,alert:false,$:false,jQuery:false,common:false,Registry:false, adjustOffset:false */

Registry.register( "common", ( function ( $ ) {
	"use strict";
	var state;
	function createInstance() {
		return {
			nonceAjax: false,
			/**
			 * Prepend array
			 *
			 * @param {type} value
			 * @param {type} array
			 * @returns {unresolved}
			 */
			prependArr: function ( value, array ) {
				var newArray = array.slice( 0 );
				newArray.unshift( value );
				return newArray;
			},
			/**
			 * Get page
			 *
			 * @returns {Boolean}
			 */
			getPage: function () {
				return ( !_.isUndefined( state.parseRequest().page ) ) ? state.parseRequest().page : false;
			},
			/**
			 * Parse Url Request
			 *
			 * @param {type} value - get params name
			 */
			parseRequest: function ( value ) {
				var request = location.search,
					array, result = { };
				if ( _.isEmpty( request ) || request === "?" ) {
					return result;
				}
				request = request.replace( "?", "" );
				array = request.split( "&" );
				$.each( array, function () {
					var value = this;
					value = value.split( "=" );
					result[value[0]] = value[1];
				} );
				if ( _.isUndefined( value ) ) {
					return result;
				} else {
					return result[value];
				}
			},
			/**
			 * Set image is favorite
			 *
			 * @param $params
			 * @param callback
			 */
			loadFolders: function ( $params, callback ) {
				var $args = {
					controller: "folder"
				};
				$.extend( $args, $params );
				state.wpAjax( $args,
					function ( data ) {
						if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
							callback( data );
						}
					},
					function ( data ) {
						if ( console ) {
							console.warn( data );
						}
					}
				);
			},
			/**
			 * Load content
			 *
			 * @param $params
			 * @param callback
			 */
			loadImages: function ( $params, callback ) {
				var $args = {
					controller: "media",
					action: "content",
					count: 0,
					step: 40
				};
				$.extend( $args, $params );
				state.wpAjax( $args,
					function ( data ) {
						// callback data
						if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
							callback( data );
						}
					},
					function ( data ) {
						if ( console ) {
							console.warn( data );
						}
					}
				);
			},
			/**
			 * WP Ajax
			 *
			 * @param {object} params
			 * @param {function} callbackSuccess
			 * @param {function} callbackError
			 * @returns {undefined}
			 */
			wpAjax: function ( params, callbackSuccess, callbackError, callbackBeforeSend ) {
				var sendStatus = true,
					request = null,
					action = window.tm_pg_options.action;
				params[action] = params.action;
				params.tm_pg_nonce = window.tm_pg_options.nonce;
				if ( sendStatus ) {
					request = wp.ajax.send( "tm_pg", {
						beforeSend: function( jqXHR ) {
							if ( undefined !== callbackBeforeSend ) {
								callbackBeforeSend( jqXHR, request );
							}
						},
						success: function ( data ) {
							callbackSuccess( data );
						},
						error: function ( data ) {
							if ( !_.isUndefined( callbackError ) && _.isFunction( callbackError ) ) {
								callbackError( data );
							} else if ( console ) {
								console.warn( data );
							}
						},
						data: params
					} );

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
