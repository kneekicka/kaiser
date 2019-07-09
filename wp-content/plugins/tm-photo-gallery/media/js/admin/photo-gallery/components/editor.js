/* global Backbone, _, Registry */

Registry.register( "pg-editor", ( function ( $ ) {
	"use strict";
	function __( value ) {
		return Registry._get( value );
	}
	/**
	 * @type {Object}
	 */
	var instance,
		/**
		 * @type {Object}
		 */
		self,
		/**
		 * @type {object} Backbone.sync
		 */
		_sync = null,
		/**
		 * @type {Number} Animation duration
		 */
		_animationDuration = 300,
		/**
		 * @type {Object}
		 */
		ImageEditorModel,
		/**
		 * @type {Object}
		 */
		ImageEditorView,
		/**
		 * @type {Object}
		 */
		controls = { },
		/**
		 * @type {Object}
		 */
		$image = { },
		/**
		 * View
		 *
		 * @type String
		 */
		_view = '',
		/**
		 * Total ids
		 *
		 * @type Array
		 */
		totalIds = [ ],
		/**
		 * Temp content
		 *
		 * @type Array
		 */
		tempContent = [ ],
		/**
		 * Selected ids
		 *
		 * @type {array}
		 */
		_selected = [ ];

	function createInstance() {
		self = { };

		Object.defineProperties( self, {
			/**
			 * @type {boolean}
			 */
			initialized: {
				configurable: false,
				enumerable: false,
				writable: true,
				value: false
			},
			/**
			 * @type {Object}
			 */
			$editorContainer: {
				configurable: false,
				enumerable: true,
				writable: true,
				value: null
			},
			/**
			 * @type {Object}
			 */
			data: {
				configurable: false,
				enumerable: true,
				writable: true,
				value: { }
			},
			/**
			 * @type {Function}
			 */
			viewTpl: {
				configurable: false,
				enumerable: true,
				writable: true,
				value: null
			},
			/**
			 * @type {Function}
			 */
			onDestruct: {
				configurable: false,
				enumerable: true,
				writable: true,
				value: null
			}
		} );

		// Backup the default Backbone.sync method
		_sync = Backbone.sync;

		/**
		 * Get operation options
		 *
		 * @param  {string} operation  Operation name.
		 * @param  {object} optionsObj Options object, which contain all the options available.
		 * @return {object}            Resulting collection of required options
		 */
		function _prepareOptions( operation, optionsObj ) {
			var options = { };

			switch ( operation ) {
				case 'rotate':
					options.angle = optionsObj.rotation_angle;
					break;
				case 'focus_point':
					options.x = optionsObj.focus_point_x;
					options.y = optionsObj.focus_point_y;
					options.width = optionsObj.image.width();
					options.height = optionsObj.image.height();
					break;
			}

			return options;
		}

		/**
		 * @param  {string} method
		 * @param  {object} model
		 * @param  {object} options
		 */
		Backbone.sync = function ( method, model, options ) {
			var action = model.attributes.operation,
				$params = {
					action: action,
					controller: 'image',
					id: model.attributes.post_data.id
				},
			_content = __( 'pg-content' ),
				_notification = __( 'notification' );
			$.extend( $params, _prepareOptions( model.attributes.operation, model.attributes ) );
			// disable content
			_content.toggleDisable( );
			// send post
			console.log($params);
			__( 'common' ).wpAjax( $params, function ( $data ) {
				model.set( 'modified', false );
				self.view.updateImages( $data );
				// enable content
				_content.toggleDisable( false );
				// show notofication
				//_notification.show( action );
			}, function ( $data ) {
				alert( 'Error! Could not save image right now!' );
				model.set( 'modified', false );
				// enable content
				_content.toggleDisable( false );
				// show notofication
				_notification.show( 'error' );
			} );
		};

		/**
		 * Initialize editor
		 *
		 * @param  {jQuery}   $container       jQuery element.
		 * @param  {Function} viewTpl          Underscore template.
		 * @param  {Object}   data             Image data.
		 * @param  {Bool}     [initRightPanel] Initialize right panel. Set it to false to prevent the initialization.
		 * @param  {Array}    selected
		 * @returns {Object|editor_L3.self}
		 */
		self.initialize = function ( $container, viewTpl, data, initRightPanel, selected ) {
			var _grid = __( 'grid' ),
				_folder = __( 'pg-folder' );
			initRightPanel = initRightPanel || true;
			_selected = selected || _selected;
			_view = _grid.getView( );

			// hide top bar
			$( __( 'pg-top-bar' )._container ).hide();

			// hide top folder
			if ( _.isEqual( _view, 'folder' ) ) {
				$( _folder._breadcrumbs.main ).addClass( 'hidden' );
				$( _folder._back ).addClass( 'hidden' );
				$( _folder._title ).addClass( 'hidden' );
			}

			$container.show().animate( {
				'opacity': 1
			}, _animationDuration ).parent().addClass( 'editor-enabled' );

			self.$editorContainer = $container;
			self.viewTpl = viewTpl;
			self.data = data;
			// Initialize right panel
			if ( initRightPanel ) {
				__( 'pg-right' ).init( [ data.id ] );
			}

			// Image editor model
			ImageEditorModel = Backbone.Model.extend( {
				defaults: {
					'post_data': null,
					'real_rotation_angle': 0,
					'rotation_angle': 0,
					'focus_point_x': 0,
					'focus_point_y': 0,
					'image': null,
					'modified': false,
					'controls': { },
					'currentIndex': null,
					'totalCount': 0,
					'selectedImages': [ ]
				},
				initialize: function () {
					var _folder = __( 'pg-folder' ),
						_content = __( 'pg-content' ),
						count = 0,
						ids = [ ];
					if ( _folder._ID ) {
						ids = _folder._folder.childs.img,
							count = _folder._folder.childs.img.length;
					} else {
						ids = _content._allImages,
							count = _content._totalCount;
					}
					totalIds = ids;
					this.set( 'totalCount', count );
					this.set( 'currentIndex', totalIds.indexOf( this.attributes.post_data.id ) );
				},
				/**
				 * @return {number}
				 */
				getCurrentIndex: function () {
					return this.get( 'currentIndex' );
				},
				/**
				 * @return {number}
				 */
				getNextIndex: function () {
					var current = this.get( 'currentIndex' ),
						total = this.get( 'totalCount' );

					current = current + 1;

					if ( current > total - 1 ) {
						current = 0;
					}

					this.set( 'currentIndex', current );

					return current;
				},
				/**
				 * @return {number}
				 */
				getPreviousIndex: function () {
					var current = this.get( 'currentIndex' ),
						total = this.get( 'totalCount' );

					current = current - 1;

					if ( current < 0 ) {
						current = total - 1;
					}

					this.set( 'currentIndex', current );
					return current;
				},
				/**
				 * @return {number}
				 */
				getNextSelectedID: function () {
					return totalIds[ this.getNextIndex() ];
				},
				/**
				 * @return {number}
				 */
				getPreviousSelectedID: function () {
					return totalIds[ this.getPreviousIndex() ];
				},
				/**
				 * Rotate image
				 * @param  {String} direction Left or right.
				 */
				rotate: function ( direction ) {
					$( '.tm-pg_editor_focus-box_image-layer' ).addClass( 'tm-pg_editor_rotate' );
					var angle = this.get( 'real_rotation_angle' );
					angle = 'left' === direction ? angle - 90 :
						'right' === direction ? angle + 90 : angle;
					// set rotation
					this.set( 'real_rotation_angle', angle );
					// convert angle
					angle *= -1;
					this.set( 'rotation_angle', angle % 360 );
				}
			} );

			// Image editor view
			ImageEditorView = Backbone.View.extend( {
				/**
				 * @type {Function}
				 */
				template: self.viewTpl,
				/**
				 * @type {Object}
				 */
				events: {
					'click .tm-pg_editor_controls_navigate_previous': 'navigatePrevious',
					'click .tm-pg_editor_controls_navigate_next': 'navigateNext',
					'click .tm-pg_editor_controls_left-rotate': 'rotateLeft',
					'click .tm-pg_editor_controls_right-rotate': 'rotateRight',
					'click .tm-pg_editor_controls_focus': 'setFocusPoint',
					'click .tm-pg_editor_controls_cancel': 'cancel',
					'click .tm-pg_editor_controls_save': 'save'
				},
				/**
				 * Initialize view
				 */
				initialize: function () {
					this.listenTo( this.model, 'change:real_rotation_angle', this.rotate );
					this.listenTo( this.model, 'change:modified', this.onImageModified );

					this.unlockControls();
				},
				/**
				 * On load image
				 *
				 * @param {type} event
				 * @returns {undefined}
				 */
				onLoadImage: function ( event ) {
					console.log( "IN" );
				},
				/**
				 * Set coordinates
				 *
				 * @param {type} coords
				 * @param {type} $control
				 * @returns {editor_L3.self.initialize.editorAnonym$5.setCoords._coords}
				 */
				setCoords: function ( coords, $control ) {
					var _coords,
						offset = $image.offset( ),
						_top = Math.round( offset.top ),
						_left = Math.round( offset.left );
					_coords = {
						x: Math.round( coords.x ) - $control.width( ) / 2 - _left,
						y: Math.round( coords.y ) - $control.height( ) / 2 - _top
					};
					$control.css( {
						top: _coords.y,
						left: _coords.x
					} );
					//console.trace();
					$( '.tm-pg_editor_focus-box_image_visible-layer', $control ).css( {
						top: -_coords.y,
						left: -_coords.x
					} );
					return _coords;
				},
				/**
				 * Render the view
				 *
				 * @return {Object}
				 */
				render: function ( ) {
					var _this = this,
						controls = { },
						control,
						$image,
						$control,
						offset,
						$img,
						padding = 220,
						height = window.innerHeight - padding;
					_this.$el.html( _this.template( _this.model.attributes ) );
					$img = $( '#tm-pg-editor-image img', _this.$el );
					$img.css( 'opacity', '0' );
					$( '#tm-pg-editor' ).css( 'opacity', '0' );
					$( '#tm-pg-editor-image' ).height( height );
					$( '#tm-pg-editor-image' ).parent().height( height );
					$( '#tm-pg-editor-image img' ).one( 'load', function ( e ) {
						setTimeout( function ( ) {
							// show arrow if selected more that one
							if ( _.isEqual( _selected.length, 1 ) ) {
								$( '.tm-pg_editor_image-navigations' ).hide();
							} else {
								$( '.tm-pg_editor_image-navigations' ).show();
							}
							var imgHeight = window.innerHeight - padding;
							if ( $img.height( ) > ( imgHeight ) ) {
								$img.height( imgHeight );
							}
							$( '#tm-pg-editor-image' ).children( '.tm-pg_editor_focus-box_image-layer ' ).height( $img.height( ) );
							// show image
							$img.animate( {
								'opacity': 1
							}, _animationDuration, function () {
								$( '#tm-pg-editor' ).css( 'opacity', '1' );
								_this.model.set( 'image', $( '#tm-pg-editor-image', _this.$el ) );
								Object.keys( _this.events ).forEach( function ( selector ) {
									selector = selector.split( ' ' )[1];
									control = $( selector, _this.$el );
									controls[ selector.replace( '.tm-pg_editor_controls_', '' ) ] = control;
								} );
								_this.model.set( 'controls', controls );
								if ( 0 === _this.model.get( 'totalCount' ) ) {
									controls.navigate_previous.attr( 'disabled', true ).hide( );
									controls.navigate_next.attr( 'disabled', true ).hide( );
									$( '.tm-pg_editor_image-counter', _this.$el ).hide( );
								}

								_this.initFocusPointEvents( );
								_this.onImageModified( null );
							} );
						}, _animationDuration );
					} );
					return this;
				},
				/**
				 * Toggle controls on image modification
				 */
				onImageModified: function ( ) {
					var _this = this,
						controls = _this.model.get( 'controls' );
					if ( !_this.model.get( 'modified' ) ) {
						controls.save.attr( 'disabled', true );
					} else {
						controls.save.removeAttr( 'disabled' );
					}
				},
				/**
				 * Update all images and restart the editor
				 *
				 * @param {type} response
				 * @returns {undefined}
				 */
				updateImages: function ( response ) {
					var _this = this,
						_content = Registry._get( 'pg-content' ),
						_grid = Registry._get( 'grid' ),
						_folder = __( 'pg-folder' ),
						date = new Date( ),
						selector, src,
						result = response.posts[0],
						id = result.id;
					_content.setContent( id, '', result );
					_grid.buildItem( id );
					_this.resetEditor( function ( ) {
						self.initialize(
							$container,
							viewTpl,
							_content.getContent( id, 'img' )
						);
						//on load image
						$( '#tm-pg-editor-image img' ).one( 'load', function ( e ) {
							Object.keys( result.thumbnails ).forEach( function ( key ) {
								//selector = '="' + result.thumbnails[ key ]['url'] + '"';
								selector = result.thumbnails[ key ]['url'];
								src = result.thumbnails[ key ].url + '?' + date.getTime( );
								result.thumbnails[ key ].url = src;
								$( 'img[src' + '="' + selector + '"' + ']' ).attr( 'src', src );
								$( '.tm-pg_library_item[style*="' + selector + '"]').css('background-image', 'url("' + src + '")');
							} );
							$( '.tm-pg_editor_focus-box_image_visible-layer' ).css( 'background-image', 'url("' + result.thumbnails.copy.url + '?' + date.getTime( ) + '")' );
							var content = _content.getContent( id, 'img' );
							_.each( content.albums, function ( album_id ) {
								var album = _content.getContent( album_id, 'album' );
								if ( _.isEqual( album.cover_id, id ) ) {
									_folder.updateFolder( album_id, 'album', function ( data ) {
										_.each( data.sets, function ( set_id ) {
											var set = _content.getContent( set_id, 'set' );
											if ( _.isEqual( set.cover_id, id ) ) {
												_folder.updateFolder( set_id, 'set' );
											}
											if ( _.isEqual( set.cover_id, album_id ) ) {
												_folder.updateFolder( set_id, 'set' );
											}
										} );
									} );
								}
							} );
							_.each( content.sets, function ( set_id ) {
								var set = _content.getContent( set_id, 'set' );
								if ( _.isEqual( set.cover_id, id ) ) {
									_folder.updateFolder( set_id, 'set' );
								}
							} );
						} );

					} );

				},
				/**
				 * Initialize focus point events
				 */
				initFocusPointEvents: function () {
					$image = this.model.get( 'image' );

					var $control = $( '.tm-pg_editor_focus-box', $image ),
						_this = this;
					// init focus point
					/**
					 * Init draggable
					 */
					$control.draggable( {
						zIndex: 9000,
						revert: false,
						scroll: true,
						appendTo: "body",
						containment: '.tm-pg_editor_image img',
						drag: function ( event, ui ) {
							var coords = checkBorders( event, ui );
							_this.setCoords( coords, $control );
						},
						stop: function ( event, ui ) {
							var coords = checkBorders( event, ui ),
								_height = $( event.target ).height(),
								_width = $( event.target ).width(),
								_top = Math.round( ui.position.top ),
								_left = Math.round( ui.position.left );
							_this.setCoords( coords, $control );
							_this.model.set( 'focus_point_x', _left + ( _width / 2 ) );
							_this.model.set( 'focus_point_y', _top + ( _height / 2 ) );

						},
						cursor: "move",
						cursorAt: {
							top: 125,
							left: 125
						}
					} );

					/**
					 * Check Borders
					 *
					 * @param {type} event
					 * @param {type} ui
					 * @returns {editor_L3.self.initialize.editorAnonym$5.initFocusPointEvents.checkBorders.editorAnonym$6}
					 */
					function checkBorders( event, ui ) {
						var _height = $( event.target ).height(),
							_width = $( event.target ).width(),
							_clientY = 0,
							_clientX = 0,
							_top = Math.round( ui.position.top ),
							_left = Math.round( ui.position.left ),
							offet_top = Math.round( ui.offset.top ),
							offset_feft = Math.round( ui.offset.left );
						// set top Y border position
						if ( _.isEqual( _top, 0 ) ) {
							_clientY = offet_top + ( _height / 2 );
						} else if ( _top >= $image.height() - _height ) {
							_clientY = offet_top + ( _height / 2 );
						} else {
							_clientY = event.clientY;
						}

						if ( _.isEqual( _left, 0 ) ) {
							_clientX = offset_feft + ( _width / 2 );
						} else if ( _left >= $image.width() - _width ) {
							_clientX = offset_feft + ( _width / 2 );
						} else {
							_clientX = event.clientX;
						}

						return {
							x: _clientX,
							y: _clientY
						};
					}
				},
				/**
				 * Lock the controls
				 *
				 * @param  {Array} filter Filter the elements and do NOT lock the matched.
				 * @param {type} force
				 * @returns {undefined}
				 */
				lockControls: function ( filter, force ) {
					filter = filter || [ ];
					force = force || false;

					if ( !force ) {
						filter.push( 'cancel' );
						filter.push( 'save' );
						filter.push( 'navigate_previous' );
						filter.push( 'navigate_next' );
					}

					var _this = this;

					controls = _this.model.get( 'controls' );
					Object.keys( controls ).forEach( function ( index ) {
						if ( 0 > filter.indexOf( index ) ) {
							controls[ index ].attr( 'disabled', true );
						}
					} );
				},
				/**
				 * Unlock all the controls
				 *
				 * @param {Array} filter Filter the elements
				 */
				unlockControls: function ( filter ) {
					filter = filter || [ ];

					var _this = this;

					controls = _this.model.get( 'controls' );
					Object.keys( controls ).forEach( function ( index ) {
						if ( 0 > filter.indexOf( index ) ) {
							controls[ index ].removeAttr( 'disabled' );
						}
					} );
				},
				/**
				 * Handle navigation
				 *
				 * @param  {Object} event Mouse event.
				 */
				navigatePrevious: function ( event ) {
					event.preventDefault();

					var _this = this;

					if ( 0 === _this.model.get( 'totalCount' ) ) {
						return false;
					}
					_this.loadItem( _this.model.getPreviousSelectedID() );
				},
				/**
				 * Handle navigation
				 *
				 * @param  {Object} event Mouse event.
				 */
				navigateNext: function ( event ) {
					event.preventDefault();
					var _this = this;
					if ( 0 === _this.model.get( 'totalCount' ) ) {
						return false;
					}
					_this.loadItem( _this.model.getNextSelectedID() );
				},
				/**
				 * Load item
				 *
				 * @param {type} id
				 * @returns {undefined}
				 */
				loadItem: function ( id ) {
					var _content = __( 'pg-content' ),
						$item = _content.getContent( id, 'img' ),
						_this = this;
					if ( !_.isUndefined( $item ) ) {
						_this.resetEditor( function () {
							self.initialize(
								$container,
								viewTpl,
								$item
								);
						}, 'right' );
					} else {
						// load data
						__( 'pg-folder' ).loadData( [ id ], function ( $data ) {
							// render image
							__( 'grid' ).renderContent( $data, 'prepend', 'img', 'grid' );
							// set temp content
							tempContent.push( $data[0] );
							// init editor
							$item = _content.getContent( $data[0].id, 'img' );
							_this.resetEditor( function () {
								self.initialize(
									$container,
									viewTpl,
									$item
									);
							}, 'right' );
						} );
					}
				},
				/**
				 * Rotate image
				 */
				rotate: function () {
					var real_angle = this.model.get( 'real_rotation_angle' ),
						horizontal,
						transform,
						$image = this.model.get( 'image' ),
						width = $image.width(),
						height = $image.height();
					horizontal = 0 === Math.abs( real_angle ) % 180;
					transform = 'rotate(' + _.escape( real_angle || 0 ) + 'deg) ' +
						'scale(' + ( horizontal ? 1 : height / width ) + ')';
					$image.children().css( { transform: transform } );
					if ( _.isEqual( Math.abs( real_angle ) % 360, 0 ) ) {
						self.view.model.set( 'modified', false );
						this.model.set( 'modified', false );
						this.unlockControls( [ 'left-rotate', 'right-rotate', 'save' ] );
						this.lockControls( [ 'left-rotate', 'right-rotate', 'navigate_previous', 'navigate_next', 'focus', 'cancel' ], true );
					} else {
						this.model.set( 'modified', true );
						this.lockControls( [ 'left-rotate', 'right-rotate' ] );
					}
				},
				/**
				 * Handle image left rotation
				 *
				 * @param  {Object} event Mouse event.
				 */
				rotateLeft: function ( event ) {
					event.preventDefault();
					this.model.set( 'operation', 'rotate' );
					this.model.rotate( 'left' );
				},
				/**
				 * Handle image right rotation
				 *
				 * @param  {Object} event Mouse event.
				 */
				rotateRight: function ( event ) {
					event.preventDefault();
					this.model.set( 'operation', 'rotate' );
					this.model.rotate( 'right' );
				},
				/**
				 * Handle setting of the focus point
				 *
				 * @param  {Object} event Mouse event.
				 */
				setFocusPoint: function ( event ) {
					event.preventDefault();

					this.lockControls( [ 'controls_focus' ] );
					this.model.set( 'operation', 'focus_point' );

					$image = this.model.get( 'image' );
					var id = this.model.get( 'post_data' ).id,
						focusPoint = __( 'pg-content' ).getContent( id ).focus_point,
						$control = $( '.tm-pg_editor_focus-box', $image ),
						width = $( 'img', $image ).width(),
						height = $( 'img', $image ).height(),
						left = focusPoint.x || width / 2,
						top = focusPoint.y || height / 2;
					this.initFocusPointEvents();

					$image.addClass( 'tm-pg_editor_image_focus-effect' ).css( {
						width: width,
						height: height,
						margin: '0 auto'
					} );

					$( '.tm-pg_editor_focus-box_image-layer', $image ).css( {
						width: width,
						height: height
					} );
					// controll position
					$control.css( {
						position: 'absolute',
						left: left + 'px',
						top: top + 'px'
					} );
					// set focus coordinats
					this.model.set( 'focus_point_x', width / 2 );
					this.model.set( 'focus_point_y', height / 2 );
					this.setCoords( {
						x: $control.offset().left,
						y: $control.offset().top
					}, $control );

					this.model.set( 'modified', true );
				},
				/**
				 * Save handler
				 *
				 * @param  {Object} event Mouse event.
				 */
				save: function ( event ) {
					event.preventDefault();

					var _this = this;
					_this.lockControls( [ ], true );

					_this.model.save( {
						'operation': _this.model.get( 'operation' ),
						'rotation_angle': _this.model.get( 'rotation_angle' ),
						'focus_point_x': _this.model.get( 'focus_point_x' ),
						'focus_point_y': _this.model.get( 'focus_point_y' )
					} );
				},
				/**
				 * Cancel
				 *
				 * @param {type} event
				 * @returns {undefined}
				 */
				cancel: function ( event ) {
					event.preventDefault();
					var _grid = __( 'grid' ),
						_content = __( 'pg-content' ),
						_topBar = __( 'pg-top-bar' ),
						_this = this,
						id = _this.model.get( 'post_data' ).id;

					_this.resetEditor( function () {
						_.each( tempContent, function ( data ) {
							$( _grid.getSelector( 'folder', 'img', '_item' ) + '[data-id="' + data.id + '"]' ).parent().remove();
							$( _grid.getSelector( 'grid', 'img', '_item' ) + '[data-id="' + data.id + '"]' ).parent().remove();
							// Delete content
							_content.deleteContent( data.id, 'img' );
						} );
						tempContent = [ ];
						$( _topBar._container ).show();
						self.$editorContainer.hide();
						// init top bar
						_topBar.addTopBar();
						var $target = _grid.getItem( id );
						__( 'pg-right' ).init( [ id ] );
						_content.initScrollbar(  );
						if ( $target.length ) {
							setTimeout( function () {
								_content.scrollTo( $target, 10, 150 );
							}, 1000 );
						}
					} );
				},
				/**
				 * Reset Editor
				 *
				 * @param  {function} callback Optional callback.
				 * @param {type} side
				 * @returns {Object.initialize.editorAnonym$5.resetEditor.destruct|Boolean}
				 */
				resetEditor: function ( callback, side ) {
					var destruct = true,
						_folder = __( 'pg-folder' );

					if ( !self.initialized ) {
						return false;
					}

					if ( _.isEqual( _view, 'folder' ) ) {
						$( _folder._breadcrumbs.main ).addClass( 'hidden' );
						$( _folder._back ).addClass( 'hidden' );
						$( _folder._title ).addClass( 'hidden' );
					}

					// remove message before cancel
				   /* if ( self.view.model.get( 'modified' ) ) {
						//if ( !confirm( self.view.model.get( 'controls' ).cancel.data( 'message' ) ) ) {
							//destruct = false;
						//}
					}*/

					if ( destruct ) {
						// right right
						__( 'pg-right' ).init( _selected );
						self.destruct( callback, side );
					}

					return destruct;
				}
			} );
			/* Initialize the view */
			self.view = new ImageEditorView( {
				model: new ImageEditorModel( {
					'post_data': self.data,
					'selectedImages': _selected
				} ),
				el: self.$editorContainer
			} );

			self.initialized = true;
			self.render();

			$container.addClass( 'editor-visible' );

			return self;
		};

		/**
		 * Destruct the editor
		 *
		 * @param {function} callback
		 * @param {type} side
		 * @returns {Boolean}
		 */
		self.destruct = function ( callback, side ) {
			if ( !self.initialized ) {
				return false;
			}

			var props = {
				'opacity': 0
			},
			_folder = __( 'pg-folder' );

			if ( callback && side ) {
				props.left = ( 'right' === side ? '+' : '-' ) + '200px';
			}

			$( '#tm-pg-editor-image', self.$editorContainer ).animate( props, _animationDuration, function () {
				self.$editorContainer.removeClass( 'editor-visible' );
				self.view.undelegateEvents();
				self.$editorContainer.children( ':not(.hidden):not(.tm-pg_editor)' ).show();
				self.$editorContainer.parent().removeClass( 'editor-enabled' );
				self.initialized = false;

				if ( _.isEqual( _view, 'folder' ) ) {
					$( _folder._breadcrumbs.main ).removeClass( 'hidden' );
					$( _folder._back ).removeClass( 'hidden' );
					$( _folder._title ).removeClass( 'hidden' );
				}
				if ( callback ) {
					callback();
				}
				if ( _.isFunction( self.onDestruct ) ) {
					self.onDestruct.call( self );
				}
			} );
		};

		/**
		 * Alias
		 */
		self.init = self.initialize;

		/**
		 * Rendering function
		 * @return {bool}
		 */
		self.render = function () {

			if ( !self.view ) {
				return false;
			}

			self.view.render();

			return true;
		};

		return self;
	}

	return {
		/**
		 * Get instance
		 * @return {Object} Instance
		 */
		getInstance: function () {
			if ( !instance ) {
				instance = createInstance();
			}
			return instance;
		}
	};

} )( jQuery ) );
