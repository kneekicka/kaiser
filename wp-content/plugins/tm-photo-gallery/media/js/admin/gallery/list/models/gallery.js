/* global _ */

Registry.register( "gallery", ( function ( $ ) {
	"use strict";
	var state;
	function __( value ) {
		return Registry._get( value );
	}
	/**
	 * Create instance
	 *
	 * @returns {gallery_L3.createInstance.galleryAnonym$0}
	 */
	function createInstance() {
		return {
			/**
			 * Init gallery
			 */
			gridInit: function (  ) {
				var _grid = __( 'grid' );
				// On input folder name save it
				$( document ).on( 'focusout', _grid._item.public + ' input', state.onFocusOutInput.bind( this ) );
				// On key up input
				$( document ).on( 'keyup', _grid._item.public + ' input', state.onKeyupInput.bind( this ) );
			},
			/**
			 * On keyup input
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onKeyupInput: function ( e ) {
				e.preventDefault();
				if ( e.keyCode === 13 ) {
					$( e.currentTarget ).blur();
				}
			},
			/**
			 * On input folder name save it
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onFocusOutInput: function ( e ) {
				e.preventDefault();
				var $this = $( e.currentTarget ),
					name = $this.attr( 'name' ),
					_grid = __( 'grid' ),
					_glContent = __( 'gl-content' );
				switch ( name ) {
					case 'new_gallery':
						if ( _.isEmpty( $this.val() ) ) {
							$this.val( 'Gallery name' );
						}
						state.addGallery( $this, function ( $data ) {
							_glContent.setContent( $data.id, '', $data, 'public' );
							_grid.buildItem( $data.id );
							_grid.initAjax( );
						} );
						break;
					case 'edit_gallery':
						state.editGallery( $this, function ( $data ) {
							_glContent.setContent( $data.id, '', $data, 'public' );
							_grid.buildItem( $data.id );
							_grid.initAjax( );
						} );
						break;
				}
			},
			/**
			 * Edit gallery
			 *
			 * @param {type} $this
			 * @param {type} callback
			 * @returns {undefined}
			 */
			editGallery: function ( $this, callback ) {
				var $parent = $this.parents( '.tm-pg_library_item' ),
					$args = {
						controller: "gallery",
						action: "edit",
						title: $this.val(),
						id: $parent.data( 'id' )
					}, _glContent = __( 'gl-content' ),
					_notification = __( 'notification' );
				// disable content
				_glContent.toggleDisable();
				__( 'common' ).wpAjax( $args,
					function ( data ) {
						$this.addClass( 'hidden' );
						// show footer
						$( '.tm-pg_library_item-content_footer_left', $parent ).removeClass( 'hidden' );
						$( '.tm-pg_library_item-content_footer_right', $parent ).removeClass( 'hidden' );
						// callback data
						if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
							callback( data );
						}
						// enable content
						_glContent.toggleDisable( false );
						// show notofication
						//_notification.show( 'edit_gallery' );
					},
					function ( data ) {
						if ( console ) {
							console.warn( data );
						}
						// enable content
						_glContent.toggleDisable( false );
						// show notofication
						_notification.show( 'error' );
					}
				);
			},
			/**
			 * Delete gallery
			 *
			 * @param {type} ids
			 * @param {type} action
			 * @param {type} callback
			 * @returns {undefined}
			 */
			deleteGallery: function ( ids, action, callback ) {
				var $args = {
					controller: "gallery",
					action: action,
					ids: ids.join( "," )
				}, _glContent = __( 'gl-content' ),
					_notification = __( 'notification' ),
					_grid = __( 'grid' );
				// disable content
				_glContent.toggleDisable();
				__( 'common' ).wpAjax( $args,
					function ( data ) {
						_.each( ids, function ( id ) {
							// remove item
							_grid.getItem( id ).parent().remove();
							_glContent.deleteContent( id );
							var type = _.isEqual( 'trash', action ) ? 'public' : 'trash';
							if ( !_.isEqual( action, 'delete' ) ) {
								// set item to trash
								_glContent.setContent( id, '', data[id], action );
							}
							// toggle select all
							if ( !_glContent.getLength( type ) ) {
								_grid.toggleSelectAllBtn( type );
							}
						} );
						// callback data
						if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
							callback( data );
						}
						// enable content
						_glContent.toggleDisable( false );
						// show notofication
						//_notification.show( action + '_gallery' );
					},
					function ( data ) {
						if ( console ) {
							console.warn( data );
						}
						// enable content
						_glContent.toggleDisable( false );
						// show notofication
						_notification.show( 'error' );
					}
				);
			},
			/**
			 * Add gallery
			 *
			 * @param {type} $this
			 * @param {type} callback
			 * @returns {undefined}
			 */
			addGallery: function ( $this, callback ) {
				var $args = {
					controller: "gallery",
					action: "add",
					title: $this.val()
				}, _glContent = __( 'gl-content' ),
					_notification = __( 'notification' ),
					$parent = $this.parents( '.tm-pg_library_item' );
				$this.remove();
				// disable content
				_glContent.toggleDisable();
				__( 'common' ).wpAjax( $args,
					function ( data ) {
						$parent.attr( 'data-id', data.id );
						// callback data
						if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
							callback( data );
						}
						// toggle select all
						if ( _glContent.getLength( 'public' ) ) {
							__( 'grid' ).toggleSelectAllBtn( 'public', 'show' );
						}
						// enable content
						_glContent.toggleDisable( false );
						// show notofication
						//_notification.show( 'add_gallery', { name: $args.title } );

					},
					function ( data ) {
						if ( console ) {
							console.warn( data );
						}
						// enable content
						_glContent.toggleDisable( false );
						// show notofication
						_notification.show( 'error' );
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
