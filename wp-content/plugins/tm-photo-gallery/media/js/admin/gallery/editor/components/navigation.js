/*global _:false,Registry:false,jQuery:false, console:fale*/
Registry.register( "gl-editor-navigation", ( function ( $ ) {
	"use strict";
	var state;

	function __( value ) {
		return Registry._get( value );
	}

	/**
	 * Create instance
	 *
	 * @returns {filters_L2.createInstance.filtersAnonym$0}
	 */
	function createInstance() {
		return {
			_filterItem: '.tm-pg_gallery_filters_item',
			_paginationItem: '.tm-pg_gallery_pagination_item',
			/**
			 * Filter content
			 */
			_contentFilter: { },
			/**
			 * Pagination content
			 */
			_contentPagination: { },
			/**
			 * Init display
			 *
			 * @returns {undefined}
			 */
			init: function ( ) {
				var _glEditor = __( 'gl-editor' );
				_glEditor.hideSection();
				_glEditor.showSection( 'navigation' );
				// set filter content
				if ( _.isEmpty( state._contentFilter ) ) {
					state._contentFilter = $.extend( true, { }, _glEditor._gallery.filter );
				};
				// set pagination content
				if ( _.isEmpty( state._contentPagination ) ) {
					state._contentPagination = $.extend( true, { }, _glEditor._gallery.pagination );
				}
				// init content
				state.initContent();
			},
			/**
			 * Init content
			 *
			 * @returns {undefined}
			 */
			initContent: function () {
				// filter content
				$( state._filterItem + '[data-type="show"] #show-filter' ).prop( 'checked', state._contentFilter.show );
				$( state._filterItem + '[data-type="type"] .select2' ).val( state._contentFilter.type );
				$( state._filterItem + '[data-type="by"] .select2' ).val( state._contentFilter.by );
				$( state._filterItem + " .select2" ).select2( {
					minimumResultsForSearch: -1
				} );

				// pagination content
				$( state._paginationItem + '[data-type="images_per_page"] input' ).val( state._contentPagination.images_per_page );
				$( state._paginationItem + '[data-type="load_more_btn"] input' ).prop( 'checked', state._contentPagination.load_more_btn );
				$( state._paginationItem + '[data-type="load_more_grid"] input' ).prop( 'checked', state._contentPagination.load_more_grid );
				$( state._paginationItem + '[data-type="pagination_block"] input' ).prop( 'checked', state._contentPagination.pagination_block );
				$( state._paginationItem + " .select2" ).select2( {
					minimumResultsForSearch: -1
				} );
			},
			/**
			 * Init Event
			 *
			 * @returns {undefined}
			 */
			initEvents: function () {
				// filter events
				// on Change input
				$( document ).on( 'change', state._filterItem + ' input', state.onChangeFilterInput.bind( this ) );
				// on Change select
				$( document ).on( 'change', state._filterItem + ' select', state.onChangeFilterSelect.bind( this ) );

				// pagination events
				// On change checkbox
				$( document ).on( 'change', state._paginationItem + ' input', state.onChangePaginationCheckbox.bind( this ) );
				// On change number
				$( document ).on( 'change', state._paginationItem + '[data-type="images_per_page"] input', state.onChangePaginationNumber.bind( this ) );
			},
			/**
			 * On change filter input
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onChangeFilterInput: function ( e ) {
				var type = $( e.currentTarget ).parents( state._filterItem ).data( 'type' );
				state._contentFilter[type] = ( $( e.currentTarget ).is( ":checked" ) ) ? 1 : 0;
			},
			/**
			 * On change filter select
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onChangeFilterSelect: function ( e ) {
				var type = $( e.currentTarget ).parents( state._filterItem ).data( 'type' );
				state._contentFilter[type] = $( e.currentTarget ).val();
			},
			/**
			 * On change pagination select
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onChangePaginationNumber: function ( e ) {
				var type = $( e.currentTarget ).parents( state._paginationItem ).data( 'type' );
				state._contentPagination[type] = $( e.currentTarget ).val();
			},
			/**
			 * On change pagination checkbox
			 *
			 * @param {type} e
			 * @returns {undefined}
			 */
			onChangePaginationCheckbox: function ( e ) {
				var type = $( e.currentTarget ).parents( state._paginationItem ).data( 'type' );
				if ( $( e.currentTarget ).is( ":checked" ) ) {
					state._contentPagination[type] = 1;
				} else {
					state._contentPagination[type] = 0;
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
