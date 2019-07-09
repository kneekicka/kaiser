/* global _ */

Registry.register( "pg-top-bar", ( function ( $ ) {
	"use strict";
	var state;
	function __( value ) {
		return Registry._get( value );
	}
	/**
	 * Create Instance
	 *
	 * @returns {top-bar_L4.createInstance.top-barAnonym$0}
	 */
	function createInstance() {
		return {
			_clone: {
				selected: '#selected-topbar',
				default: '#default-topbar'
			},
			_container: '.tm-pg_library-filter',
			// title toolip
			_titleTooltip: '.tm-pg_library-filter_selected-objects-tooltip',
			// add container
			_addContainer: {
				photo: 'a.tm-pg_library-filter_add-photo',
				album: 'a.tm-pg_library-filter_add-album',
				set: 'a.tm-pg_library-filter_add-set'
			},
			// add to button
			_addTo: {
				main: '.tm-pg_library-filter_selected-settings_add',
				menu: '.tm-pg_library-filter_selected-settings_add-menu'
			},
			/**
			 * Init top bar
			 *
			 * @returns {undefined}
			 */
			init: function () {
				// On click add photo
				$( document ).on( 'click', state._addContainer.photo, state.onClickAddPhoto.bind( this ) );
				// On click add album
				$( document ).on( 'click', state._addContainer.album, state.onClickAddAlbum.bind( this ) );
				// On click add set
				$( document ).on( 'click', state._addContainer.set, state.onClickAddSet.bind( this ) );
				// On hover add To menu
				$( document ).on( 'hover', state._addTo.main + ' a', state.onHoverAddTo.bind( this ) );
				// On click add To menu
				$( document ).on( 'click', state._addTo.main + ' a', state.onClickAddTo.bind( this ) );
				// On hover show tooltip
				$( document ).on( 'hover', '.tm-pg_library-filter_selected-title', state.onHoverShowTooltip.bind( this ) );
				// On click close selected
				$( document ).on( 'click', '.tm-pg_library-filter_selected-close', state.onClickClose.bind( this ) );
				// On click delete selected
				$( document ).on( 'click', '.tm-pg_library-filter_selected-settings_delete > a', state.onClickDelete.bind( this ) );
			},
			/**
			 * On click close
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onClickClose: function ( e ) {
				e.preventDefault();
				state.unselectAll();
			},
			/**
			 * On hover show tooltip
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onHoverShowTooltip: function ( e ) {
				if ( $( state._titleTooltip ).hasClass( 'show' ) ) {
					$( state._titleTooltip ).removeClass( 'show' );
				} else {
					$( state._titleTooltip ).addClass( 'show' );
				}
			},
			/**
			 * On click add To menu
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onClickAddTo: function ( e ) {
				e.preventDefault();
				$( state._addTo.menu ).hide();
				var type = $( e.currentTarget ).data( 'type' );
				if ( !_.isUndefined( type ) ) {
					__( 'pg-popup' ).init( type );
				} else {
					$( state._addTo.menu ).show();
				}
			},
			/**
			 * On hover add To menu
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onHoverAddTo: function ( e ) {
				$( state._addTo.menu ).show();
			},
			/**
			 * On click add set
			 *
			 * @param {type} e - Mouse event.
			 * @returns {undefined}
			 */
			onClickAddSet: function ( e ) {
				e.preventDefault();
				var _grid = __( 'grid' ),
					view = _grid.getView(),
					$target = $( _grid.getSelector( view, 'set', '_parent' ) );
				__( 'pg-content' ).scrollTo( $target, 500, 50, function (  ) {
					_grid.addNewItem( 'set' );
				} );
			},
			/**
			 * On click add photo
			 *
			 * @param {type} e - Mouse event.
			 * @returns {undefined}
			 */
			onClickAddPhoto: function ( e ) {
				e.preventDefault();
				// show popup in folder
				if ( __( 'pg-folder' )._ID > 0 ) {
					__( 'media-popup' ).init();
				} else {
					var _grid = __( 'grid' ),
						view = _grid.getView(),
						$target = $( _grid.getSelector( view, 'img', '_parent' ) );
					__( 'pg-content' ).scrollTo( $target, 2000, 50, function (  ) {
						__( 'image' ).showUpload();
					} );
				}
				__( 'accordion' ).showSlide( 'img' );
			},
			/**
			 * On click add album
			 *
			 * @param {type} e - Mouse event.
			 * @returns {undefined}
			 */
			onClickAddAlbum: function ( e ) {
				e.preventDefault();
				var _grid = __( 'grid' ),
					view = _grid.getView(),
					$target = $( _grid.getSelector( view, 'album', '_parent' ) );
				__( 'pg-content' ).scrollTo( $target, 1000, 50, function (  ) {
					_grid.addNewItem( 'album' );
				} );
			},
			/**
			 * On click delete
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onClickDelete: function ( e ) {
				e.preventDefault();
				var _notification = __( 'notification' ),
					ids = __( 'pg-content' ).getSelectedIds(),
					$lang = _notification.getLangData( ),
					textID = 'delete_' + $lang.type + '_dialog';
				// show dialog
				_notification.showDialog( textID, $lang.replase, function () {
					state.deleteItem( ids, 0 );
				} );
			},
			/**
			 * Selected init
			 *
			 * @returns {undefined}
			 */
			selectedInit: function () {
				var $selected = __( 'pg-content' ).getAllSelectedIds();
				// set selected to tooltip
				$( state._titleTooltip + ' p[data-type="sets"] span' ).text( $selected.set.length );
				$( state._titleTooltip + ' p[data-type="albums"] span' ).text( $selected.album.length );
				$( state._titleTooltip + ' p[data-type="photos"] span' ).text( $selected.img.length );

				// show hide add On button and menu
				if ( $selected.set.length > 0 ) {
					$( state._addTo.main + ' a' ).hide();
				} else {
					$( state._addTo.main + ' a' ).show();
					if ( $selected.album.length > 0 ) {
						$( state._addTo.menu + ' a[data-type="album"]' ).hide();
					} else {
						$( state._addTo.menu + ' a[data-type="album"]' ).show();
					}
				}

				/**
				 * Init cover in folder
				 */
				if ( __( 'pg-folder' )._ID ) {
					__( 'cover' ).init();
				}

			},
			/**
			 * Set action
			 *
			 * @param {type} ids
			 * @param {type} key
			 * @returns {undefined}
			 */
			deleteItem: function ( ids, key ) {
				try {
					var _grid = __( 'grid' ),
						_pgFolder = __( 'pg-folder' ),
						_folder = __( 'folder' ),
						_content = __( 'pg-content' ),
						_notification = __( 'notification' ),
						_right = __( 'pg-right' ),
						id = ids[key],
						$lang = _notification.getLangData( undefined, ids );
					// disable content
					_content.toggleDisable( );
					_grid.deleteItem( id, function ( $data ) {
						// updata cover folders
						var sets = $data[id].sets,
							albums = $data[id].albums,
							index = _content._allImages.indexOf( id );
						if ( !_.isUndefined( sets ) ) {
							_.each( sets, function ( set ) {
								_pgFolder.updateFolder( set, 'set', function () {
									if ( _.isEqual( _pgFolder._ID, set ) ) {
										_right.init( [ set ] );
									}
								} );
							} );
						}
						if ( !_.isUndefined( albums ) ) {
							_.each( albums, function ( album ) {
								_pgFolder.updateFolder( album, 'album', function ( data ) {
									if ( _.isEqual( _pgFolder._ID, album ) ) {
										_right.init( [ album ] );
									}
									__( 'cover' ).removeCover( data, 'set' );
								} );
							} );
						}
						// delete selected items
						_content._imgCount--;
						_content._totalCount--;
						if ( !_.isEqual( index, -1 ) ) {
							_content._allImages.splice( index, 1 );
						}
						_grid.getItem( id ).parent().remove();
						if ( true === $data[id].permanently ) {
							_content.deleteContent( id );
						}
						// toggle select all
						if ( !$( _grid._showMore ).is( ':visible' ) && !_content.getLength( 'img' ) ) {
							_grid.toggleSelectAllBtn( 'img' );
						}
						_.each( _content._types, function ( type ) {
							if ( !_.isEqual( type, 'img' ) && !_content.getLength( type ) ) {
								_grid.toggleSelectAllBtn( type );
							}
						} );
						// in folder
						if ( _pgFolder._ID ) {
							_right.init( [ _pgFolder._ID ] );
							_pgFolder.deleteContent( id );
							if ( !$( _grid._showMore ).is( ':visible' ) && !_content.getLength( 'img' ) ) {
								_grid.toggleSelectAllBtn( 'img' );
							}
							_.each( _content._types, function ( type ) {
								if ( !_.isEqual( type, 'img' ) && !_content.getLength( type ) ) {
									_grid.toggleSelectAllBtn( type );
								}
							} );
						}
						state.addTopBar();
						if ( _.isEqual( key, ids.length - 1 ) ) {
							var imgLenght = _content.getLength( 'img' );
							if ( imgLenght < 40 && $( _grid._showMore ).is( ':visible' ) ) {
								__( 'common' ).loadImages( { count: _content._imgCount, step: ids.length }, function ( $data ) {
									// show grid
									_content.showGrid( { type: 'img' } );
									if ( $data.posts ) {
										_content.renderGrid( { type: 'img', data: $data.posts } );
									}
									if ( $data.last ) {
										_content._lastRequest = $data.last;
										$( _content._loadMore ).hide();
									} else {
										_content._imgCount = $data.count;

									}
									_grid.checkSelectAllBtn( 'img' );
								} );
							}

							// update folder
							if ( _pgFolder._ID ) {
								_pgFolder.updateFolder( );
							}
							// re-render right albums and sets
							_folder.renderRightSelect( 'set' );
							_folder.renderRightSelect( 'album' );
							// unselect all
							state.unselectAll();
							// enable content
							_content.toggleDisable( false );
							// show notification
							//_notification.show( $lang.type + '_deleted', $lang.replase );
						}
						// recursive call
						if ( !_.isUndefined( ids[++key] ) ) {
							state.deleteItem( ids, key );
						}
					} );
				} catch ( e ) {
					console.warn( e );
					// enable content
					_content.toggleDisable( false );
					// show notofication
					_notification.show( 'error' );
				}
			},
			/**
			 * Unselect all elemets
			 *
			 * @returns {undefined}
			 */
			unselectAll: function () {
				var _grid = __( 'grid' ),
					_content = __( 'pg-content' ),
					$selected = _content.getAllSelectedIds();
				// uncheck selected items
				_.each( _content._types, function ( type ) {
					_.each( $selected[type], function ( id ) {
						_grid.getItem( id ).removeClass( 'checked' );
					} );
					// remove selected from select all
					$( _grid._parent[type] + ':visible ' + _grid._selectAll ).removeClass( 'selected' );
					// pre check all
					_grid.preCheckedAll();
				} );
				// build topnar
				state.buildTopBar();
				// right init
				__( 'pg-right' ).init();
			},
			/**
			 * Add topbar
			 *
			 * @returns {undefined}
			 */
			addTopBar: function (  ) {
				// get selected count
				var $selected = __( 'pg-content' ).getAllSelectedIds();
				// add selected top bar
				if ( $selected.count > 0 ) {
					state.buildSelectedTopBar( $selected );
				} else { // add default top bar
					state.buildTopBar();
				}
			},
			/**
			 * Build top bar
			 *
			 * @returns {undefined}
			 */
			buildTopBar: function () {
				var $clone = $( state._clone.default ).clone();
				if ( $( state._container ).hasClass( 'tm-pg_library-filter_selected' ) ) {
					$( state._container ).addClass( 'hide-box' );
				}
				setTimeout( function () {
					$( state._container )
						.removeClass( 'tm-pg_library-filter_selected' )
						.removeClass( 'hide-box' )
						.html( $clone.children() );
				}, 500 );

			},
			/**
			 * Add selected top bar
			 *
			 * @param {type} $selected
			 * @returns {undefined}
			 */
			buildSelectedTopBar: function ( $selected ) {
				$( state._container ).addClass( 'tm-pg_library-filter_selected' );
				var $clone = $( state._clone.selected ).clone();
				$( '.tm-pg_library-filter_selected-title span', $clone ).text( $selected.count );
				$( state._container ).html( $clone.children() );
				state.selectedInit();
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
