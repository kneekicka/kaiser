/* global _, tm_pg_options */

Registry.register( "tag", ( function ( $ ) {
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
			_clone: '#tag-clone',
			_item: '.tm-pg_tag_container',
			_itemCheckbox: '.tm-pg_checkbox-item_tag',
			/**
			 * Init
			 *
			 * @returns {undefined}
			 */
			init: function (  ) {
				var tagsStr = '', delayTimer,
					selector = __( 'pg-right' )._slidebar;

				/**
				 * On click Add tag
				 *
				 * @param {Object} e - Mouse event.
				 */
				$( selector ).on( 'click', '.tm-pg_add-tags_form > button.tm-pg_btn', function ( e ) {
					e.preventDefault();
					var $this = $( this ).parent(),
						ids = __( 'pg-right' ).ids,
						key = 0;
					state.tagAction( ids, $( 'input', $this ).val(), key, 'add_term', $this );
					$( 'input', $this ).val( '' );
				} );

				/**
				 * On change right tag
				 */
				$( selector ).on( 'change', '.tm-pg_checkbox-item_tag input[type="checkbox"]', function () {
					var ids = __( 'pg-right' ).ids,
						value = $( this ).val(),
						key = 0,
						action = 'delete_term';
					if ( $( this ).is( ':checked' ) ) {
						action = 'add_term';
					}
					state.tagAction( ids, value, key, action, $( this ).parent() );
				} );

				/**
				 * On remove right tag
				 */
				$( selector ).on( 'click', '.tm-pg_checkbox-item_tag a.tm-pg_tag-delete', function () {
					var $this = $( this ),
						id = $( 'input', $this.parent() ).val(),
						title = $( 'label[for="image-tag_' + id + '"]:visible span.name' ).text(),
						_notification = __( 'notification' ),
						textID = 'delete_tag';

					// show dialog
					_notification.showDialog( textID, '', function () {
						__( 'preloader' ).show( $this.parent() );
						__( 'term' ).termAction( {
							id: id,
							action: 'remove_term',
							type: tm_pg_options.tax_names.tag
						}, function ( $data ) {
							if ( $data ) {
								$( __( 'pg-right' )._slidebar + ' ' + state._item + '[data-id="' + id + '"]' ).remove();
								$( 'label[for="image-tag_' + id + '"]' ).parent().remove();
								__( 'pg-content' ).deleteTerm( 'tags', parseInt( id ) );
								__( 'preloader' ).hide( $this.parent() );
							}
						} );
					} );
				} );

				/**
				 * Tags list toggle
				 */
				$( selector ).on( 'click', '#tagslist-link', function () {
					$(this).siblings('.tm-pg_sidebar_image-tags_checkbox-group').toggle();
					return false;
				});

				/**
				 * Search tags on input keyup
				 *
				 * @param {Object} e - Mouse event.
				 */
				$( selector ).on( "keyup", '.tm-pg_add-tags_form > input', function ( e ) {
					if ( e.keyCode === 13 ) {
						$( 'button.tm-pg_btn', $( this ).parent() ).trigger( 'click' );
						return false;
					}
					clearTimeout( delayTimer );
					var $this = $( this );
					delayTimer = setTimeout( function () {
						if ( _.isEmpty( $this.val() ) ) {
							return false;
						}
						// search isset Tags
						state.searchTag( { q: $this.val() }, function ( $data ) {
							var $clone = $( '#terms-seach' ).children().clone();
							$this.parent().append( $clone );
							$( '.search-res ul.ac_results li:not(".hidden")' ).remove();
							if ( _.isEmpty( $data ) ) {
								return false;
							}
							// bild find tags
							$.each( $data, function ( key, term ) {
								state.buildSearchTags( term, $this.val() );
							} );
							$( '.search-res' ).removeClass( "hidden" );
							// On click find tags
							$( '.search-res ul.ac_results li' ).on( "click", function () {
								var array = $this.val().split( "," ),
									inputText = $this.val();
								tagsStr = $( '.tag-name', $( this ) ).text();
								inputText = inputText.replace( _.last( array ), tagsStr );
								$this.val( inputText ).focus();
								tagsStr = "";
								$( '.search-res' ).addClass( "hidden" );
								$( '.search-res ul.ac_results li:not(".hidden")' ).remove();
							} );
						} );
					}, 1000 );
				} );

				/**
				 * On delete tag
				 */
				$( selector ).on( 'click', state._item + ' .tm-pg_tag-delete', function () {
					var $this = $( this ).parent(),
						ids = __( 'pg-right' ).ids,
						key = 0;
					state.tagAction( ids, $this.data( 'id' ), key, 'delete_term', $this );
				} );
			},
			/**
			 * Tag action
			 *
			 * @param {type} ids
			 * @param {type} value
			 * @param {type} key
			 * @param {type} action
			 * @returns {undefined}
			 */
			tagAction: function ( ids, value, key, action, $this ) {
				var $params = { },
					_content = __( 'pg-content' ),
					title;

				if ( value ) {
					$params.value = value;
					$params.id = ids;
					$params.action = action;
					$params.type = tm_pg_options.tax_names.tag;
					if ( _.isEqual( action, 'delete_term' ) ) {
						$params.field = 'term_taxonomy_id';
						title = $( state._item + '[data-id="' + value + '"] span' ).text();
					} else {
						title = $( 'label[for="image-tag_' + value + '"]:visible span.name' ).text();
						if ( !title ) {
							$params.field = 'name';
							title = value;
						} else {
							$params.field = 'term_taxonomy_id';
						}
					}
					// disable rightbar
					//_content.toggleDisable( true );
					if ( $this ) {
						__( 'preloader' ).show( $this.parent() );
					}
					__( 'term' ).termAction( $params, function ( $data ) {
						state.callback( $data, ids, key, action, title, $this.parent() );
					} );
				}
			},
			/**
			 * Callback
			 *
			 * @param {type} $data
			 * @param {type} ids
			 * @param {type} key
			 * @param {type} action
			 * @param {type} title
			 * @returns {undefined}
			 */
			callback: function ( $data, ids, key, action, title, $this ) {
				var _content = __( 'pg-content' ),
					key = 0,
					contentAction = 'prepend',
					type = null;

				if ( 'delete_term' === action ) {
					contentAction = 'unset';
				}

				_.each( ids, function( id ) {
					type  = _content.getType( id );
					_content.setContent( id, 'tags', $data[ key ], type, contentAction );
					key++;
				} );

				//restore checked tags
				$( __( 'pg-right' )._slidebar + ' ' + state._item ).remove();
				$( __( 'pg-right' )._slidebar + ' ' + state._itemCheckbox + ' input:checkbox' ).prop( 'checked', false );
				if ( ids.length > 1 ) {
					__( 'term' ).addMultiTerms( 'tags', action );
				} else {
					if ( ! _.isUndefined( _content.getContent( ids[0] ).tags ) ) {
						state.addTags( _content.getContent( ids[0] ).tags, action );
					}
				}
				// show right
				//_content.toggleDisable( false );
				if ( $this ) {
					__( 'preloader' ).hide( $this );
				}
				// show notofication
				if ( _.isEqual( action, 'delete_term' ) ) {
					//__( 'notification' ).show( 'delete_tag', { name: title } );
				} else {
					//__( 'notification' ).show( 'add_tag', { name: title } );
				}

			},
			/**
			 * Search tag
			 *
			 * @param {type} $params
			 * @param {type} callback
			 * @returns {undefined}
			 */
			searchTag: function ( $params, callback ) {
				var $args = {
					controller: "term",
					action: "search_term",
					tax: 'post_tag'
				};
				$.extend( $args, $params );
				__( 'common' ).wpAjax( $args,
					function ( data ) {
						if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
							callback( data, $params );
						}
					},
					function ( data ) {
						console.warn( 'Some error!!!' );
						console.warn( data );
					}
				);
			},
			/**
			 * Add tags
			 *
			 * @param {type} tags
			 * @returns {undefined}
			 */
			addTags: function ( tags, action ) {
				if ( _.isEmpty( tags ) ) {
					return false;
				}
				// set tags
				_.each( tags, function ( value ) {
					var selector = __( 'pg-right' )._slidebar,
						clone = __( 'pg-right' )._clone,
						item = $( state._clone ).clone();

					// set id
					item.children().attr( 'data-id', value.term_id );
					// set name
					$( 'span.name', item ).text( value.name );
					// add tag
					$( selector + ' .tm-pg_sidebar_image-tags' ).append( item.children() );
					// check tag in list
					if ( $( selector + ' #image-tag_' + value.term_id ).length > 0 ) {
						$( selector + ' #image-tag_' + value.term_id ).prop( 'checked', true );
					} else if ( _.isEqual( action, 'add_term' ) ) {
						// add new tag if not exist
						var item = $( '#tag-list-clone' ).clone();
						$( 'label', item ).attr( 'for', 'image-tag_' + value.term_id );
						$( 'input', item ).attr( 'id', 'image-tag_' + value.term_id ).val( value.term_id );
						$( 'span.name', item ).text( value.name );
						var cloneItem = item.children().clone();
						$( selector + ' .tm-pg_sidebar_image-tags_checkbox-group' ).prepend( item.children() );
						// add to hidden right item
						$( 'input', cloneItem ).prop( 'checked', false );
						$( clone + ' .tm-pg_sidebar_image-tags_checkbox-group' ).prepend( cloneItem );
					}
				} );
			},
			/**
			 * Create search item element
			 *
			 * @param {type} term
			 * @param {type} searchString
			 * @returns {undefined}
			 */
			buildSearchTags: function ( term, searchString ) {
				var item = $( $( '.search-res ul.ac_results li.hidden' )[0] ).clone();
				item.removeClass( "hidden" );
				var startPosition = term.name.indexOf( searchString );
				var endPosition = startPosition + searchString.length;
				item.attr( 'data-id', term.term_id );
				$( '.tag-name', item ).text( term.name );
				$( '.count', item ).text( term.count );

				if ( startPosition === 0 ) {
					$( '.ac_match', item ).text( term.name.substring( startPosition, endPosition ) );
					$( '.after', item ).text( term.name.substring( endPosition ) );
				} else {
					$( '.ac_match', item ).text( term.name.substring( startPosition, endPosition ) );
					$( '.before', item ).text( term.name.substring( 0, startPosition ) );
					$( '.after', item ).text( term.name.substring( endPosition ) );
				}
				$( '.search-res ul.ac_results' ).append( item );
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
