/*global _:false,Registry:false,upload:false*/
Registry.register( "pg-right", ( function ( $ ) {
	"use strict";
	var state;
	function __( value ) {
		return Registry._get( value );
	}
	/**
	 * Create Instance
	 *
	 * @returns {right_L2.createInstance.rightAnonym$0}
	 */
	function createInstance() {
		return {
			// clone slidebar
			_clone: '#right-image-clone',
			// slide bar
			_slidebar: '#sidebar-content',
			// slidebar container
			_container: {
				album: '#sidebar-content .tm-pg_sidebar_image-albums',
				set: '#sidebar-content .tm-pg_sidebar_image-sets'
			},
			// scroll container
			_scrollContainer: '.tm-pg_sidebar_container',
			// disable status
			disable: false,
			// selected ids
			ids: [ ],
			/**
			 * Right init
			 *
			 * @param {type} ids
			 * @returns {undefined}
			 */
			init: function ( ids ) {
				if ( state.disable ) {
					return false;
				}
				var view = __( 'grid' ).getView();
				state.ids = ids || __( 'pg-content' ).getSelectedIds();
				state.showSlidebar( state.ids, function () {
					// add right item
					state.addItem( state.ids );
					if ( _.isEqual( state.ids.length, 1 ) ) {
						// build right item
						state.buildMediaDetails( state.ids.join( "," ) );
					} else if ( state.ids.length > 1 ) {
						// build milty items
						state.buildMultiDetails();
					} else {
						if ( _.isEqual( view, 'grid' ) ) {
							$( state._slidebar ).attr( 'data-id', 0 ).html( '' );
						} else {
							var folderID = __( 'pg-folder' )._ID;
							state.init( [ folderID ] );
						}
					}
					$( state._slidebar + " .select2" ).val('').select2({
						minimumResultsForSearch: 7
					});
				} );
			},
			/**
			 * Refresh slidebar
			 *
			 * @returns {undefined}
			 */
			refresh: function () {
				$( state._slidebar ).children().remove();
			},
			/**
			 * Init right events
			 *
			 * @returns {undefined}
			 */
			initRightEvents: function () {
				// init scroll bar
				state.initScrollbar();

				// init right bar
				__( 'tag' ).init( );
				__( 'category' ).init( );

				// On click label
				$( state._slidebar ).on( 'click', 'label span', state.onClickLabel.bind( this ) );
				// Save title on mouse leave
				$( state._slidebar ).on( 'change', '.tm-pg_sidebar_image_main-description input',
					state.onChangeTitle.bind( this ) );
				// Save description on mouse leave
				$( state._slidebar ).on( 'change', '.tm-pg_sidebar_image_main-description textarea',
					state.onChangeTitle.bind( this ) );
			},
			/**
			 * Save title on mouse leave
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onChangeTitle: function ( e ) {
				var $params = { },
					ids = state.ids,
					_content = __( 'pg-content' ),
					_folder = __( 'pg-folder' );
				$params.value = $( e.currentTarget ).val();
				$params.type = $( e.currentTarget ).attr( 'name' );
				$params.id = ids.join( "," );
				// disable rightbar
				_content.toggleDisable( true );
				state.saveDetails( $params, function ( $data ) {
					_.each( $data, function ( $item ) {
						_content.setContent( $item.id, '', $item );
						__( 'grid' ).buildItem( $item.id );
						if ( _.isEqual( _folder._ID, $item.id ) ) {
							_folder.init( _folder._ID, _folder._type, _folder._parent );
						}
					} );
					//__( 'notification' ).show( 'edit_' + $params.type );
				} );
			},
			/**
			 * On click label
			 *
			 * @param {Object} e - Mouse event.
			 * @returns {undefined}
			 */
			onClickLabel: function ( e ) {
				e.preventDefault();
				var $parent = $( e.currentTarget ).parent();
				if ( $parent.next( 'input[type="text"]' ).length ) {
					$parent.next( 'input[type="text"]' ).focus();
				} else if ( $( 'input[type="text"]', $parent.next( 'form' ) ).length ) {
					$( 'input[type="text"]', $parent.next( 'form' ) ).focus();
				}
			},
			/**
			 * Show slidebar
			 *
			 * @param {type} ids
			 * @param {type} callback
			 * @returns {undefined}
			 */
			showSlidebar: function ( ids, callback ) {
				$( state._scrollContainer ).show( 'fade', { }, 500, callback );
			},
			/**
			 * Save image details
			 *
			 * @param {type} $params
			 * @param {function} callback
			 */
			saveDetails: function ( $params, callback ) {
				var $args = {
					controller: "media",
					action: "save_details"
				},
				_content = __( 'pg-content' );
				$.extend( $args, $params );
				__( 'common' ).wpAjax( $args,
					function ( data ) {
						if ( !_.isUndefined( callback ) && _.isFunction( callback ) ) {
							callback( data );
						}
						// enable rightbar
						_content.toggleDisable( false );
					},
					function ( data ) {
						console.warn( 'Some error!!!' );
						console.warn( data );
					}
				);
			},
			/**
			 * Init scrollbar
			 *
			 * @returns {undefined}
			 */
			initScrollbar: function () {
				// init sidebar scrollbar
				var rightHeight = innerHeight - $( state._scrollContainer ).offset().top - 30;
				$( state._scrollContainer ).height( rightHeight );
			},
			/**
			 * Add new item
			 *
			 * @param {type} id
			 */
			addItem: function ( id ) {
				var item = $( state._clone ).clone();
				$( state._slidebar ).data( 'id', id ).html( item.children() );
			},
			/**
			 * Bild Media Details
			 *
			 * @param {type} id
			 */
			buildMediaDetails: function ( id ) {
				var content = __( 'pg-content' ).getContent( id ),
					type = __( 'pg-content' ).getType( id ),
					selector = state._slidebar,
					_folder = __( 'folder' ),
					date = new Date( );
				switch ( type ) {
					case 'img':
						// show right album set
						$( selector + ' div[data-type="album-container"]' ).removeClass( 'hidden' );
						$( selector + ' div[data-type="set-container"]' ).removeClass( 'hidden' );
						// add update albums
						_folder.updateSlidebar( content.albums, 'album' );
						// add update sets
						_folder.updateSlidebar( content.sets, 'set' );
						// set img src
						$( selector + ' .tm-pg_sidebar_image img' ).attr( 'src', content.thumbnails.right.url + '?' + date.getTime( ) );
						//$item.css('background-image', 'url(' + data.thumbnails.big.url + ')');
						// show image description
						$( selector + ' .tm-pg_sidebar_image-description' ).removeClass( 'hidden' );
						// set filename
						$( selector + ' p[data-type="filename"] span.value' ).text( content.filename );
						// set filetype
						$( selector + ' p[data-type="filetype"] span.value' ).text( content.post.post_mime_type );
						// set filedate
						$( selector + ' p[data-type="filedate"] span.value' ).text( content.date );
						// set filesize
						$( selector + ' p[data-type="filesize"] span.value' ).text( content.filesize );
						// set Dimensions
						$( selector + ' p[data-type="dimensions"] span.value' ).text( content.image.width + ' × ' + content.image.height );
						// show img
						$( selector + ' .tm-pg_sidebar_image_container' ).removeClass( 'hidden' );
						break;
					case 'album':
						// show right set
						$( selector + ' div[data-type="set-container"]' ).removeClass( 'hidden' );
						// add update sets
						_folder.updateSlidebar( content.sets, 'set' );

					case 'set':
						if ( !_.isUndefined( content.cover_img ) && !_.isEmpty( content.cover_img.right ) ) {
							// set img src
							$( selector + ' .tm-pg_sidebar_image img' ).attr( 'src', content.cover_img.right[0] + '?' + date.getTime( ) );
							// show img
							$( selector + ' .tm-pg_sidebar_image_container' ).removeClass( 'hidden' );
						}
						break;
				}
				if ( !_.isUndefined( content ) ) {
					// set img title and description
					$( selector + ' .tm-pg_sidebar_image_main-description input[name="post_title"]' ).val( content.post.post_title );
					$( selector + ' .tm-pg_sidebar_image_main-description textarea[name="post_content"]' ).val( content.post.post_content );
					// show existing tags
					__( 'tag' ).addTags( content.tags );
					// check exist cats
					__( 'category' ).selectCats( content.categories );

					// set status
					if ( _.isEqual( content.status, 'private' ) ) {
						$( selector + " #image-visibility_unlisted" ).prop( "checked", true );
					} else {
						$( selector + " #image-visibility_public" ).prop( "checked", true );
					}
				}
			},
			/**
			 * Build media details
			 *
			 * @returns {undefined}
			 */
			buildMultiDetails: function () {
				var selector = state._slidebar,
					ids = state.ids,
					_folder = __( 'folder' ),
					_term = __( 'term' );
				// hide cover img
				$( selector + ' .tm-pg_sidebar_image_container' ).addClass( 'hidden' );
				//if ( ids.length < 10 ) {
					// bild multi tags
					_term.addMultiTerms( 'tags' );
					// bild multi categories
					_term.addMultiTerms( 'categories' );
				//}
				// bild multi albums
				_folder.multyUpdateSlidebar( 'albums' );
				// bild multi sets
				_folder.multyUpdateSlidebar( 'sets' );
				// Multi Ids
				var titles = [ ], descriptions = [ ], statuses = [ ], types = [ ];
				$.each( ids, function ( key, id ) {
					var content = __( 'pg-content' ).getContent( id );
					types.push( __( 'pg-content' ).getType( id ) );
					if ( key > 0 ) {
						// calculate unique array of titles
						if ( _.isEqual( $.inArray( content.title, titles ), -1 ) ) {
							titles.push( content.title );
						}
						// calculate unique array of descriptions
						if ( _.isEqual( $.inArray( content.description, descriptions ), -1 ) ) {
							descriptions.push( content.description );
						}
						// calculate unique array of statuses
						if ( _.isEqual( $.inArray( content.status, statuses ), -1 ) ) {
							statuses.push( content.status );
						}
					} else {
						statuses.push( content.status );
						titles.push( content.title );
						descriptions.push( content.description );
					}
				} );
				// show album or set blocks
				if ( _.isEqual( $.inArray( 'set', types ), -1 ) ) {
					if ( !_.isEqual( $.inArray( 'album', types ), -1 ) ) {
						// show right set
						$( selector + ' div[data-type="set-container"]' ).removeClass( 'hidden' );
					} else {
						if ( !_.isEqual( $.inArray( 'img', types ), -1 ) ) {
							// show right album set
							$( selector + ' div[data-type="album-container"]' ).removeClass( 'hidden' );
							$( selector + ' div[data-type="set-container"]' ).removeClass( 'hidden' );
						}
					}
				}
				// set status
				if ( _.isEqual( statuses.length, 1 ) ) {
					if ( _.isEqual( statuses[0], 'private' ) ) {
						$( selector + " #image-visibility_unlisted" ).prop( "checked", true );
					} else {
						$( selector + " #image-visibility_public" ).prop( "checked", true );
					}
				}
				// set img title
				if ( _.isEqual( titles.length, 1 ) ) {
					$( selector + ' .tm-pg_sidebar_image_main-description input[name="title"]' ).val( titles[0] );
				}
				// set img description
				if ( _.isEqual( descriptions.length, 1 ) ) {
					$( selector + ' .tm-pg_sidebar_image_main-description input[name="description"]' ).val( descriptions[0] );
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
