/* global _ */

Registry.register( "folder", ( function ( $ ) {
	"use strict";
	var state;
	function __( value ) {
		return Registry._get( value );
	}
	/**
	 * Create instance
	 *
	 * @returns {folder_L3.createInstance.folderAnonym$0}
	 */
	function createInstance() {
		return {
			/**
			 * Save folder
			 *
			 * @param {type} $params
			 * @param {type} callback
			 */
			addFolder: function ( $params, callback ) {
				var $args = {
					controller: "folder",
					action: "add_folder"
				},
				_content = __( 'pg-content' );
				$.extend( $args, $params );
				// disable rightbar
				_content.toggleDisable( true );
				__( 'common' ).wpAjax( $args,
					function ( data ) {
						if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
							callback( data );
						}
						// disable rightbar
						_content.toggleDisable( false );
					},
					function ( data ) {
						if ( console ) {
							console.warn( data );
						}
						// disable rightbar
						_content.toggleDisable( false );
						// show notofication
						__( 'notification' ).show( 'error' );
					}
				);
			},
			/**
			 * Render right select
			 *
			 * @param {type} type
			 * @returns {Boolean}
			 */
			renderRightSelect: function ( type ) {
				try {
					if ( _.isEqual( type, 'img' ) ) {
						return false;
					}
					var items = __( 'pg-content' ).getContent( 0, type ),
						_right = __( 'pg-right' );
					if ( _.isEmpty( items ) ) {
						return false;
					}
					// set empty select
					$( _right._clone + ' .tm-pg_add-to-' + type + '_form select' ).html( '' );
					var html = '', $clone;
					_.each( items, function ( item ) {
						if ( !_.isUndefined( item ) ) {
							$clone = $( '#option-clone' ).clone();
							$clone.children().attr( 'value', item.id );
							$clone.children().text( item.post.post_title );
							html += $clone.html();
						}
					} );
					// render right select
					$( _right._clone + ' .tm-pg_add-to-' + type + '_form select' ).append( html );
				} catch ( e ) {
					console.warn( e );
				}
			},
			/**
			 * Add right folder
			 *
			 * @param {type} id
			 * @param {type} type
			 * @returns {Boolean}
			 */
			addRightFolder: function ( id, type ) {
				var $item = __( 'pg-content' ).getContent( id, type ),
					$clone = $( __( type )._clone ).clone();

				if ( _.isUndefined( $item ) ) {
					return false;
				}
				// set id
				$clone.children().attr( 'data-id', $item.id );
				// set name
				$( 'span.name', $clone ).text( $item.post.post_title );
				// add right album
				$( __( 'pg-right' )._container[type] ).append( $clone.children() );
				// disabled added folder
				$( __( 'pg-right' )._slidebar + ' .tm-pg_add-to-' + type + '_form select option[value="' + $item.id + '"]' ).prop( 'disabled', true );
			},
			/**
			 * Remove right folder
			 *
			 * @param {type} id
			 * @param {type} type
			 * @returns {undefined}
			 */
			removeRightFolder: function ( id, type ) {
				$( __( 'pg-right' )._container[type] + ' span[data-id="' + id + '"]' ).remove();
				$( __( 'pg-right' )._slidebar + ' .tm-pg_add-to-' + type + '_form select option[value="' + id + '"]' ).prop( 'disabled', false );
			},
			/**
			 * Update Slidebar
			 *
			 * @param {type} folders
			 * @param {type} type
			 * @returns {Boolean}
			 */
			updateSlidebar: function ( folders, type ) {+
				// set set folders
				_.each( folders, function ( folder ) {
					if ( !folder ) {
						return false;
					}
					state.addRightFolder( folder, type );
				} );
			},
			/**
			 * Multy update slide bar
			 *
			 * @param {type} type
			 * @returns {undefined}
			 */
			multyUpdateSlidebar: function ( type ) {
				var ids = __( 'pg-right' ).ids,
					folders = [ ];

				// create flatten array
				_.each( ids, function ( id ) {
					var _folder = __( 'pg-content' ).getContent( id )[type];
					if ( _.isArray( _folder ) ) {
						folders.push( _folder );
					}
				} );
				folders = _.flatten( folders );

				// calculate folder count and remove it if it not in selected attachments
				_.each( folders, function ( folder ) {
					var count = 0;
					_.each( ids, function ( id ) {
						var _folders = __( 'pg-content' ).getContent( id )[type];
						if ( !_.isEmpty( _folders ) ) {
							_.each( _folders, function ( _folder ) {
								if ( folder === _folder ) {
									count++;
								}
							} );
						}
					} );

					if ( !_.isEqual( count, ids.length ) ) {
						folders = _.without( folders, folder );
					}
				} );
				folders = _.uniq( folders );
				// update slidebar
				switch ( type ) {
					case 'albums':
						state.updateSlidebar( folders, 'album' );
						break;
					case 'sets':
						state.updateSlidebar( folders, 'set' );
						break;
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
