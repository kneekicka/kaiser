/* global _, tm_pg_options */

Registry.register( "category", ( function ( $ ) {
	"use strict";
	var state;
	function __( value ) {
		return Registry._get( value );
	}
	/**
	 * Create instance
	 *
	 * @returns {category_L3.createInstance.categoryAnonym$0}
	 */
	function createInstance() {
		return {
			_input: '.tm-pg_add-categories_form input',
			_container: '.tm-pg_sidebar_image-categories',
			/**
			 * Init
			 *
			 * @returns {undefined}
			 */
			init: function () {
				var selector = __( 'pg-right' )._slidebar;

				/**
				 * Add category On keyup "ENTER"
				 *
				 * @param {Object} e - Mouse event.
				 */
				$( selector ).on( "keyup", state._input, function ( e ) {
					if ( e.keyCode === 13 ) {
						$( 'button', $( this ).parent() ).trigger( 'click' );
						return false;
					}
				} );

				/**
				 * On click add category
				 *
				 * @param {Object} e - Mouse event.
				 */
				$( selector ).on( 'click', '.tm-pg_add-categories_form button.tm-pg_btn ', function ( e ) {
					e.preventDefault();
					var ids = __( 'pg-right' ).ids,
						$this = $( this ).parent(),
						value = $( 'input', $this ).val(),
						key = 0;
					state.categoryAction( ids, value, key, 'add_term', $this );
					$( 'input', $this ).val( '' );
				} );

				/**
				 * On change right category
				 */
				$( selector ).on( 'change', '.tm-pg_checkbox-item_categoty input[type="checkbox"]', function () {
					var ids = __( 'pg-right' ).ids,
						value = $( this ).val(),
						key = 0,
						action = 'delete_term';
					if ( $( this ).is( ':checked' ) ) {
						action = 'add_term';
					}
					state.categoryAction( ids, value, key, action, $( this ).parent() );
				} );

				/**
				 * On remove right category
				 */
				$( selector ).on( 'click', '.tm-pg_checkbox-item_categoty a.tm-pg_category-delete', function () {
					var $this = $( this ),
						id = $( 'input', $this.parent() ).val(),
						title = $( 'label[for="image-category_' + id + '"]:visible span.name' ).text(),
						_content = __( 'pg-content' ),
						_notification = __( 'notification' ),
						textID = 'delete_category';

					// show dialog
					_notification.showDialog( textID, '', function () {
						// disable rightbar
						//_content.toggleDisable( true );
						__( 'preloader' ).show( $this.parent() );
						__( 'term' ).termAction( {
							id: id,
							action: 'remove_term',
							type: tm_pg_options.tax_names.category
						}, function ( $data ) {
							if ( $data ) {
								$( 'label[for="image-category_' + id + '"]' ).parent().remove();
								state.checkCategoryBlock();
								__( 'preloader' ).hide( $this.parent() );
								__( 'pg-content' ).deleteTerm( 'categories', parseInt( id ) );
								//_content.toggleDisable( false );
								//__( 'notification' ).show( 'remove_category', { name: title } );
							}
						} );
					} );
				} );
			},
			/**
			 * Check category block
			 *
			 * @returns {undefined}
			 */
			checkCategoryBlock: function () {
				var $container = $( __( 'pg-right' )._slidebar + ' ' + __( 'category' )._container );
				if ( $container.children().length ) {
					$container.show();
				} else {
					$container.hide();
				}
			},
			/**
			 * Category action
			 *
			 * @param {type} ids
			 * @param {type} value
			 * @param {type} key
			 * @param {type} action
			 * @returns {undefined}
			 */
			categoryAction: function ( ids, value, key, action, $this ) {
				var $params = { }, title,
					_content = __( 'pg-content' );
				if ( !key ) {
					$( state._container ).addClass( 'disable' );
				}
				if ( value ) {
					$params.value = value;
					$params.id = ids;
					$params.action = action;
					$params.type = tm_pg_options.tax_names.category;
					title = $( 'label[for="image-category_' + value + '"]:visible span.name' ).text();
					if ( !title ) {
						$params.field = 'name';
						title = value;
					} else {
						$params.field = 'term_taxonomy_id';
					}
					if ( $this ) {
						__( 'preloader' ).show( $this.parent() );
					}
					// disable rightbar
					//_content.toggleDisable( true );
					__( 'term' ).termAction( $params, function ( $data ) {
						state.callback( $data, ids, key, action, title, $this );
					} );
				}
			},
			/**
			 * Multy Cat Callback
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
					_content.setContent( id, 'categories', $data[ key ], type, contentAction );
					key++;
				} );

				$( state._container ).removeClass( 'disable' );

				if ( ids.length > 1 ) {
					__( 'term' ).addMultiTerms( 'categories', action );
				} else {
					if ( ! _.isUndefined( _content.getContent( ids[0] ).categories ) ) {
						state.selectCats( _content.getContent( ids[0] ).categories, action );
					}

				}
				if ( $this ) {
					__( 'preloader' ).hide( $this.parent() );
				}
				// enable rightbar
				//_content.toggleDisable( false );
				// show notofication
				if ( _.isEqual( action, 'delete_term' ) ) {
					//__( 'notification' ).show( 'delete_category', { name: title } );
				} else {
					//__( 'notification' ).show( 'add_category', { name: title } );
				}

			},
			/**
			 * Selecte caregories
			 *
			 * @param {type} cats
			 * @param {type} action
			 * @returns {Boolean}
			 */
			selectCats: function ( cats, action ) {
				action = action || '';
				state.checkCategoryBlock();
				if ( _.isEmpty( cats ) ) {
					return false;
				}
				$.each( cats, function ( key, value ) {
					var selector = __( 'pg-right' )._slidebar,
						clone = __( 'pg-right' )._clone;
					if ( $( selector + ' #image-category_' + value.term_id ).length > 0 ) {
						$( selector + ' #image-category_' + value.term_id ).prop( 'checked', true );
					} else if ( _.isEqual( action, 'add_term' ) ) {
						// add new category if not exist
						var item = $( '#cat-clone' ).clone();
						$( 'label', item ).attr( 'for', 'image-category_' + value.term_id );
						$( 'input', item ).attr( 'id', 'image-category_' + value.term_id ).val( value.term_id );
						$( 'span.name', item ).text( value.name );
						var cloneItem = item.children().clone();
						$( selector + ' .tm-pg_sidebar_image-categories' ).prepend( item.children() );
						// add to hidden right item
						$( 'input', cloneItem ).prop( 'checked', false );
						$( clone + ' .tm-pg_sidebar_image-categories' ).prepend( cloneItem );
					}
				} );
				state.checkCategoryBlock();
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
