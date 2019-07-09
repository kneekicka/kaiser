/* global _, Registry */

Registry.register( "term", ( function ( $ ) {
	"use strict";
	var state;
	function __( value ) {
		return Registry._get( value );
	}
	/**
	 * Create instance
	 *
	 * @returns {tag_L3.createInstance.tagAnonym$0}
	 */
	function createInstance() {
		return {
			/**
			 * Multi Add Tags
			 *
			 * @param {type} type
			 * @param {type} action
			 * @returns {undefined}
			 */
			addMultiTerms: function ( type, action ) {
				var $terms = [ ],
					_temp  = {},
					value = 'term_id',
					ids = __( 'pg-right' ).ids,
					length = ids.length,
					count = 0;

				// create all tags array
				_.each( ids, function ( id ) {
					var _terms = __( 'pg-content' ).getContent( id )[type];

					if ( !_.isEmpty( _terms ) ) {
						_.each( _terms, function ( term ) {
							if ( _.isUndefined( term.term_id ) ) {
								return;
							}
							if ( ! _temp.hasOwnProperty( term.term_id ) ) {
								_temp[ term.term_id ] = term;
								_temp[ term.term_id ].appears = 1;
							} else {
								_temp[ term.term_id ].appears = _temp[ term.term_id ].appears + 1;
							}
						} );
					} else {
						_temp  = {};
					}
				} );

				_.mapObject( _temp, function( storedTerm, termId ) {
					if ( length <= storedTerm.appears ) {
						$terms.push( storedTerm );
					}
				});

				// add term
				switch ( type ) {
					case 'tags':
						__( 'tag' ).addTags( $terms, action );
						break;
					case 'categories':
						__( 'category' ).selectCats( $terms, action );
						break;
				}
			},
			/**
			 *  Term action
			 *
			 * @param $params
			 * @param callback
			 */
			termAction: function ( $params, callback ) {
				var $args = {
					controller: "term",
					action: $params.action
				};
				$.extend( $args, $params );
				__( 'common' ).wpAjax( $args,
					function ( data ) {
						if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
							callback( data );
						}
					},
					function ( data ) {
						if ( console ) {
							console.warn( data );
						}
						// enable content
						__( 'pg-content' ).toggleDisable( false );
						// show notofication
						__( 'notification' ).show( 'error' );
					}
				);
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
