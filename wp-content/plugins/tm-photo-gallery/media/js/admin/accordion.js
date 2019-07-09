/* global Registry */

Registry.register( "accordion", ( function ( $ ) {
	"use strict";
	var state;
	function createInstance( ) {
		return {
			/**
			 * Init
			 *
			 * @returns {undefined}
			 */
			init: function () {
				$( '.accordion' ).each( function () {
					state.accordion( $( this ) );
				} );
			},
			/**
			 * accordion
			 *
			 * Sets config options for each accordion, hides content and adds click handler
			 *
			 * @param {type} elementDOM - jQuery object of accordion.
			 * @returns {undefined}
			 */
			accordion: function ( elementDOM ) {
				// set config
				elementDOM.settings = state.populateOptions( elementDOM );
				// hide content
				state.hideAccordionContent( elementDOM );
				// handle clicks
				$( '.' + elementDOM.settings.triggerClass + ' a', elementDOM ).on( 'click.accordion', state.onClick.bind( this, elementDOM ) );
			},
			/**
			 * Show slide
			 *
			 * @param {type} type
			 * @returns {undefined}
			 */
			showSlide: function ( type ) {
				var _grid = Registry._get( 'grid' ),
					view = _grid.getView(),
					elementDOM = $( _grid.getSelector( view, type, '_parent' ) + ' .accordion' ),
					$trigger = $( _grid.getSelector( view, type, '_parent' ) + ' .accordion-trigger a' );
				elementDOM.settings = state.populateOptions( elementDOM );
				var $content = $( '.' + elementDOM.settings.contentClass, elementDOM );
				if ( !$content.is( ':visible' ) ) {
					$content.slideToggle();
					$content.addClass( elementDOM.settings.contentOpenClass );
					$trigger.addClass( elementDOM.settings.openClass );
				}
			},
			/**
			 * Show slide
			 *
			 * @param {type} type
			 * @returns {undefined}
			 */
			hideSlide: function ( type ) {
				var _grid = Registry._get( 'grid' ),
					view = _grid.getView(),
					elementDOM = $( _grid.getSelector( view, type, '_parent' ) + ' .accordion' ),
					$trigger = $( _grid.getSelector( view, type, '_parent' ) + ' .accordion-trigger a' );
				elementDOM.settings = state.populateOptions( elementDOM );
				var $content = $( '.' + elementDOM.settings.contentClass, elementDOM );
				if ( $content.is( ':visible' ) ) {
					$content.slideToggle();
					$content.removeClass( elementDOM.settings.contentOpenClass );
					$trigger.removeClass( elementDOM.settings.openClass );
				}
			},
			/**
			 * On click
			 *
			 * @param {type} e
			 * @param {type} elementDOM
			 * @returns {undefined}
			 */
			onClick: function ( elementDOM, e ) {
				e.preventDefault( );
				var $content = $( '.' + elementDOM.settings.contentClass, elementDOM ),
					page = Registry._get( 'common' ).getPage();
				if ( _.isEqual( page, 'tm_pg_media' ) ) {
					var _folder = Registry._get( 'pg-folder' ),
						_content = Registry._get( 'pg-content' ),
						type = $content.data( 'type' );
					if ( !$content.is( ':visible' ) ) {
						if ( _folder._ID > 0 ) {
							_folder._folder._accordion[type] = true;
						} else {
							_content._accordion[type] = true;
						}
					} else {
						if ( _folder._ID > 0 ) {
							_folder._folder._accordion[type] = false;
						} else {
							_content._accordion[type] = false;
						}
					}
				}
				$content.slideToggle( );
				$content.toggleClass( elementDOM.settings.contentOpenClass );
				$( e.currentTarget ).toggleClass( elementDOM.settings.openClass );
			},
			/**
			 * hideAccordionContent
			 *
			 * Hides all content and adds closed class. Skips any elements with open class
			 *
			 * @param {type} elementDOM - jQuery object of accordion container class
			 * @returns {undefined}
			 */
			hideAccordionContent: function ( elementDOM ) {
				$( '.' + elementDOM.settings.triggerClass + ' a', elementDOM ).each( function ( ) {
					var triggerDOM = $( this ),
						$content = $( '.' + elementDOM.settings.contentClass, elementDOM );
					// if trigger has open class set, leave it open
					if ( !triggerDOM.hasClass( elementDOM.settings.openClass ) ) {
						$content.removeClass( elementDOM.settings.contentOpenClass ).slideUp( );
					} else {
						$content.addClass( elementDOM.settings.contentOpenClass ).slideDown( );
					}
				} );
			},
			/**
			 * populateOptions
			 *
			 * Set config options from data attributes on accordion container
			 *
			 * @param {type} elementDOM - jQuery object of accordion container
			 * @returns {accordion_L1.createInstance.accordionAnonym$0.populateOptions.settings}
			 */
			populateOptions: function ( elementDOM ) {
				return  {
					contentClass: elementDOM.data( 'accordion-content-class' ) || 'accordion-content',
					contentOpenClass: elementDOM.data( 'accordion-content-open-class' ) || 'content-open',
					openClass: elementDOM.data( 'accordion-open-class' ) || 'open',
					speed: elementDOM.data( 'accordion-speed' ) || 300,
					triggerClass: elementDOM.data( 'accordion-trigger-class' ) || 'accordion-trigger'
				};
			}
		};
	}
	return {
		getInstance: function ( ) {
			if ( !state ) {
				state = createInstance( );
			}
			return state;
		}
	};
} )( jQuery ) );
