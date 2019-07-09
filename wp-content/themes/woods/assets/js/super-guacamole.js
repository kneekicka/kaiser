/**
 * super-guacamole - Super Guacamole! It's a script that hides your menu items if they don't fit the menu width.
 * @version v1.1.9
 * @link https://github.com/dkfiresky/super-guacamole#readme
 * @license GPL-3.0+
*/
( function( $, undefined ) {

	var defaultTemplates = {
		menu: '<li class="%1$s">' +
				'<a href="%2$s">%3$s</a>' +
				'%4$s' +
			'</li>',
		child_wrap: '<ul>%s</ul>',
		child: '<li class="%1$s" id="%5$s">' +
					'<a href="%2$s">%3$s</a>' +
					'<ul class="sub-menu">%4$s</ul>' +
				'</li>'
	};

	/**
	 * Menu constructor
	 *
	 * @access private
	 * @param {object} options Menu options.
	*/
	function Menu( options ) {
		var defaults,
			settings,
			self = this;

		defaults = {
			id: '',
			href: '',
			title: '&middot;&middot;&middot;',
			children: {},
			templates: {},
			container: null
		};

		settings = $.extend( defaults, options );

		self.id = settings.id;
		self.href = settings.href;
		self.title = settings.title;
		self.children = settings.children;
		self.templates = settings.templates;
		self.$container = settings.container;
		self.node = null;
		self.attachedNode = null;
		self.options = {}; // Shared options
		self.visible = true;
	}

	/**
	 * Set child
	 * @param	{Menu}	 child	 Child menu element
	 * @param	{number} [index] Optional index. If not specified, child will be added into the end.
	 * @return {Menu}
	*/
	Menu.prototype.set = function( child ) {
		if ( false === child instanceof Menu ) {
			throw new Error( 'Invalid argument type' );
		}

		this.children[ child.id ] = child;

		return this;
	};

	/**
	 * Alias of `Menu.prototype.set`
	*/
	Menu.prototype.push = function( child ) {
		return this.set( child );
	};

	/**
	 * Get menu item
	 * @param	{number} index
	 * @return {Menu}
	*/
	Menu.prototype.get = function( id ) {
		var menuItem = null;

		this.map( id, function( _, child ) {
			menuItem = child;
			return child;
		} );

		return menuItem;
	};

	/**
	 * Map through the items
	 * @return {Menu}
	*/
	Menu.prototype.map = function( id, callback, children ) {
		var menuItem = {
				id: id
			},
			self = this;

		if ( typeof id !== 'string' ) {
			menuItem = id;
		}

		children = children || this.children;

		if ( 0 >= children.length ) {
			return menuItem;
		}

		Object.keys( children ).forEach( function( index ) {
			child = children[ index ];
			if ( menuItem.id === child.id ) {
				children[ menuItem.id ] = callback( child );
			} else {
				if ( child.children && 0 < Object.keys( child.children ).length ) {
					menuItem = self.map( menuItem, callback, child.children );
				}
			}
		} );

		return menuItem;
	};

	/**
	 * Check if menu has children with the specified `index`
	 * @param	{number} index
	 * @return {boolean}
	*/
	Menu.prototype.has = function( index ) {
		return undefined !== this.children[ index ];
	};


	/**
	 * Return visibility state flag
	 *
	 * @access private
	 * @return {boolean} Visibility state flag
	*/
	Menu.prototype.isVisible = function() {
		return this.visible;
	}

	/**
	 * forEach wrapper
	 */
	Menu.prototype.forEach = function( callback ) {
		return this.children.forEach( callback );
	};

	/**
	 * Count the visible attached nodes
	 * @return {number}
	*/
	Menu.prototype.countVisibleAttachedNodes = function() {
		var self = this,
			count = -1;

		Object.keys( self.children ).forEach( function( index ) {
			if ( ! $( self.children[ index ].getAttachedNode() ).attr( 'hidden' ) ) {
				count++;
			}
		} );

		return count;
	};

	/**
	 * Count the visible nodes
	 * @return {number}
	*/
	Menu.prototype.countVisibleNodes = function() {
		var self = this,
			count = 0,
			child;

		Object.keys( self.children ).forEach( function( index ) {
			child = self.children[ index ];
			if ( ! $( child.getNode() ).attr( 'hidden' ) ) {
				count++;
			}
		} );

		return count;
	};

	/**
	 * Count the `{Menu}` nodes
	 * @return {number}
	*/
	Menu.prototype.countVisible = function() {
		var self = this,
			count = 0;

		Object.keys( self.children ).forEach( function( index ) {
			if ( self.children[ index ].isVisible() ) {
				count++;
			}
		} );

		return count;
	};


	/**
	 * Get menu `this.node`
	 * @return {jQuery}
	*/
	Menu.prototype.getNode = function() {
		return this.node;
	};

	/**
	 * Return attached node to the menu element
	 * @return {jQuery}
	*/
	Menu.prototype.getAttachedNode = function() {
		return this.attachedNode;
	};

	/**
	 * Set menu node
	 * @param	{jQuery} $node Menu node
	*/
	Menu.prototype.setNode = function( $node ) {
		this.node = $node;
	};

	/**
	 * Attach a node to the menu element
	 * @param	{jQuery} $node Node element
	*/
	Menu.prototype.attachNode = function( $node ) {
		this.attachedNode = $node;
	};

	/**
	 * Set options
	 * @param {Object} options Options object
	 * @return {Menu}
	*/
	Menu.prototype.setOptions = function( options ) {
		this.options = options;
		return this;
	};

	/**
	 * Get options
	 * @return {Object}
	*/
	Menu.prototype.getOptions = function() {
		return this.options;
	};

	/**
	 * Render the menu
	 *
	 * @access private
	 * @return {Menu}
	*/
	Menu.prototype.render = function() {
		var self = this,
			menuTpl = self.templates.menu,
			childTpl = self.templates.child,
			$container = self.$container,
			$menu = self.options.$menu,
			$el;

		function replace( str, num, value ) {
			var originalStr = str.replace( new RegExp( '\\%' + num + '\\$s', 'g' ), value );

			pipes = {
				replace: function( num, value ) {
					replace( originalStr, num, value );
					return pipes;
				},
				get: function() {
					return originalStr;
				}
			};

			return pipes;
		};

		function renderMenu( className, menu, isChild ) {
			var children = '',
				keys = Object.keys( menu.children );

			isChild = isChild || false;

			keys.forEach( function( key ) {
				children += renderMenu( 'super-guacamole__menu__child', menu.children[ key ] );
			} );

			return replace( isChild ? childTpl : menuTpl, 1, className + ' menu-item' + ( 0 < keys.length ? ' menu-item-has-children' : '' ) )
				.replace( 2, menu.href )
				.replace( 3, menu.title )
				.replace( 4, ( 0 < keys.length ? children : '' ) )
				.replace( 5, menu.id )
				.get()
				.replace( '<ul class="sub-menu"></ul>', '' );
		}

		function render( children ) {
			var render = '',
				id,
				$current_el;

			Object.keys( children ).forEach( function( key ) {
				render += renderMenu( 'super-guacamole__menu', children[ key ] );
			} );

			return render;
		}

		if ( 0 < $container.length ) {
			$container.append( render( [ self ] ) );

			$container.find( '.super-guacamole__menu__child' ).each( function() {
				$current_el = $( this );
				id = $( this ).attr( 'id' );
				$el = $container.find( '#' + id.replace( 'sg-', '' ) );

				if ( 0 === $el.length ) {
					$el = $container.find( '.' + id.replace( 'sg-', '' ) );
				}

				if ( 0 < $el.length ) {
					self.map( id, function( menuItem ) {
						menuItem.attachNode( $el );
						menuItem.setNode( $current_el );

						return menuItem;
					} );
				}
			} );
		}

		return this;
	};

	/**
	 * Extract elements
	 *
	 * @static
	 * @access private
	 * @param	{jQuery} $elements Collection of elements.
	 * @return {array}			Array of Menu elements
	*/
	Menu.extract = function( $elements ) {
		var obj = {},
			$element,
			$anchor,
			child,
			subChild,
			uniqueID;

		function getMenuID( $menuItem ) {
			var id = '',
				match = null,
				regexp = /menu\-item\-[0-9]+/i;
			$menuItem.attr( 'class' ).split( ' ' ).forEach( function( className ) {
				match = regexp.exec( className );
				if ( null !== match ) {
					id = match[0];
				}
			} );
			return id;
		}

		$elements.each( function( index, element ) {
			$element = $( element );
			$anchor = $element.find( 'a:first' );
			menuId = $element.attr( 'id' );

			if ( 'undefined' === typeof menuId ) {
				menuId = getMenuID( $element );
			}

			child = new Menu( {
				id: 'sg-' + menuId,
				href: $anchor.attr( 'href' ),
				title: $anchor.get( 0 ).childNodes[0].data
			} );
			child.attachNode( $element );

			if ( -1 < $element.children( '.sub-menu' ).length ) {
				subMenu = Menu.extract( $element.children( '.sub-menu' ).children( '.menu-item' ) );

				Object.keys( subMenu ).forEach( function( key ) {
					subChild = subMenu[ key ];
					child.set( subChild );
				} );
			}

			obj[ child.id ] = child;
		} );

		return obj;
	};

	/**
	 * Check if attached nodes fit parent container
	 * @return {boolean}
	*/
	Menu.prototype.attachedNodesFit = function() {
		var self = this,
		width = 0,
		_width = 0,
		$node,
		$attachNode,
		child,
		$headerContainer = $( '.header-container > .container' ).length > 0 ?
			$( '.header-container > .container' ) :
			$( '.header-container > div' ),
		maxWidth = self.$container.outerWidth( true ) -
			self.$container.find( '.super-guacamole__menu' ).outerWidth( true ) -
			(
				parseInt( $headerContainer.css( 'padding-left' ), 10 ) +
				parseInt( $headerContainer.css( 'padding-right' ), 10 )
			) / 2;

		Object.keys( self.children ).forEach( function( key ) {
			child = self.children[ key ];
			$attachedNode = $( child.getAttachedNode() );
			$node = $( child.getNode() );
			$attachedNode.removeAttr( 'hidden' );
			$node.attr( 'hidden', true );
		} );

		Object.keys( self.children ).forEach( function( index ) {
			child = child = self.children[ index ];
			$attachedNode = $( child.getAttachedNode() );
			$node = $( child.getNode() );

			_width = $attachedNode.outerWidth( true );
			if ( 0 < _width ) {
				$attachedNode.data( 'width', _width );
			}

			width += $attachedNode.data( 'width' );

			if ( width > maxWidth ) {
				$attachedNode.attr( 'hidden', true );
				$node.removeAttr( 'hidden' );
			}
		} );

		return true;
	};

	/**
	 * Check if menu fit & has children
	 * @param {bool}	[flag] Apply the class or return boolean.
	 * @return {bool}
	*/
	Menu.prototype.menuFit = function( flag ) {
		var self = this,
			fns = {
				removeAttr: function( el, attr ) {
					return el.removeAttr( attr );
				},
				attr: function( el, attr ) {
					return el.attr( attr, true );
				}
			},
			fn = 'removeAttr',
			child,
			threshold = self.options.threshold || 768;

		flag = flag || false;

		if ( 0 === self.countVisibleNodes() ) {
			fn = 'attr';
		}

		if ( $( window ).width() <= ( threshold - 1 ) ) {
			fn = 'attr';

			Object.keys( self.children ).forEach( function( index ) {
				child = self.children[ index ];
				$attachedNode = $( child.getAttachedNode() );
				$node = $( child.getNode() );
				$attachedNode.removeAttr( 'hidden' );
				$node.attr( 'hidden', true );
			} );
		}

		if ( ! flag ) {
			fns[ fn ]( self.$container.find( '.super-guacamole__menu' ), 'hidden' );
		}

		return fn === 'removeAttr';
	};

	/**
	 * Watch handler.
	 *
	 * @access private
	 * @return {Menu}
	*/
	Menu.prototype.watch = function( once ) {
		var self = this,
			node,
			_index = -1,
			_visibility = false,
			_attachedNodesCount = 0,
			$attachedNode;

		once = once || false;

		function watcher() {
			self.attachedNodesFit();
			self.menuFit();
		}

		if ( once ) {
			watcher();
			return self;
		}

		function _debounce( threshold ) {
			var _timeout;

			return function _debounced( $jqEvent ) {
				function _delayed() {
					watcher();
					timeout = null;
				}

				if ( _timeout ) {
					clearTimeout( _timeout );
				}

				_timeout = setTimeout( _delayed, threshold );
			};
		}

		$( window ).on( 'resize', _debounce( 10 ) );
		$( window ).on( 'orientationchange', _debounce( 10 ) );

		return self;
	};

	/**
	 * Super Guacamole!
	 *
	 * @access public
	 * @param	{object} options Super Guacamole menu options.
	*/
	$.fn.superGuacamole = function( options ) {
		var defaults,
			settings,
			$menu = $( this ),
			$main_menu = $menu.find( '#main-menu' ),
			$children,
			the_menu;

		defaults = {
			threshold:			544, // Minimal menu width, when this plugin activates
			minChildren: 		3, // Minimal visible children count
			childrenFilter: 	'li', // Child elements selector
			menuTitle:			'&middot;&middot;&middot;', // Menu title
			menuUrl:			'#', // Menu url
			templates:			defaultTemplates // Templates
		};

		settings = $.extend( defaults, options );

		$children = $main_menu.children( settings.childrenFilter + ':not(.super-guacamole__menu):not(.super-guacamole__menu__child)' );
		the_menu = new Menu( {
			title:		settings.menuTitle,
			href:		settings.menuUrl,
			templates:	settings.templates,
			children:	Menu.extract( $children ),
			container:	$main_menu
		} );

		settings.$menu = $main_menu;

		the_menu.setOptions( settings )
			.render()
			.watch( true )
			.watch();
	};

} ( jQuery ) );
