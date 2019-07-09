/* global _, tm_pg_admin_lang, Registry, wp */
Registry.register( "media-popup", ( function ( $ ) {
	"use strict";
	var state;

	/**
	 * Get obj state
	 *
	 * @param {type} value
	 * @returns {wp.mce.View|*}
	 */
	function __( value ) {
		return Registry._get( value );
	}

	/**
	 * Create Instance
	 *
	 * @returns {media-popup_L2.createInstance.media-popupAnonym$0}
	 */
	function createInstance() {
		return {
			frame: false,
			selected: [ ],
			/**
			 * Init popup
			 *
			 * @returns {undefined}
			 */
			init: function ( ) {
				// If the media frame already exists, reopen it.
				if ( state.frame ) {
					state.frame.open();
					return;
				}
				// Create a new media frame
				state.frame = wp.media( {
					title: 'Select or Upload Media to selected folder',
					button: {
						text: 'Add media to folder'
					},
					library: { type: 'image' },
					multiple: true
				} );
				// on select images
				state.frame.on( 'select', function () {
					// appand items
					state.appendItems( );
				} );
				// Hook upload file
				$.extend( wp.Uploader.prototype, {
					success: state.onUploadImage.bind( this )
				} );
				// Finally, open the modal on click
				state.frame.open();
			},
			/**
			 * On upload image
			 * @param {type} $item
			 * @returns {undefined}
			 */
			onUploadImage: function ( $item ) {
				var _grid = __( 'grid' ),
					_folder = __( 'pg-folder' );
				if ( _.isEqual( $item.attributes.type, 'image' ) ) {
					// load data
					_folder.loadData( [ $item.id ], function ( $data ) {
						// render images
						_grid.renderContent( $data, 'prepend', 'img', 'grid' );
					} );
				}
			},
			/**
			 * Append items
			 *
			 * @returns {undefined}
			 */
			appendItems: function ( ) {
				var $items = state.frame.state().get( 'selection' ).toJSON(),
					ids = [ ];
				_.each( $items, function ( $item ) {
					ids.push( $item.id );
				} );
				// add selected to folder
				state.addToFolder( ids );
			},
			/**
			 * Add album term
			 *
			 * @param {type} ids
			 * @param {type} key
			 * @returns {undefined}
			 */
			addToFolder: function ( ids, key ) {
				key = key || 0;
				var _content = __( 'pg-content' ),
					_folder = __( 'pg-folder' ),
					id = _folder._ID,
					type = _folder._type,
					$params = {
						id: id,
						value: ids,
						action: 'add_to_folder',
						controller: 'folder'
					};
				// disable content
				_content.toggleDisable();
				__( 'common' ).wpAjax( $params, function ( ) {

					// update folder
					_folder.updateFolder( id, type, function () {
						_folder.destruct();
						if ( _folder._parent ) {
							_folder.updateFolder( _folder._parent.id );
						}
						__( 'pg-folder' ).init( _folder._ID, _folder._type, _folder._parent );
						// show notofication
						var title = _content.getContent( id, type ).post.post_title;
						__( 'notification' ).show( 'added_to_' + type, { name: title } );
					} );

					_content.toggleDisable( false );
				} );
			}
		};
	}

	return {
		getInstance: function ( ) {
			if ( !state ) {
				state = createInstance();
			}
			return state;
		}
	};
} )( jQuery ) );
