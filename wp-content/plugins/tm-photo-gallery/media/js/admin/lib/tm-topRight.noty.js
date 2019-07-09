( function ( $ ) {
    $.noty.layouts.tmPgTopRight = {
        name: 'tmPgTopRight',
        addClass: '',
        container: {
            object: '<ul id="noty_tmPgTopRight_layout_container" />',
            selector: 'ul#noty_tmPgTopRight_layout_container',
            style: function () {
                var right = ( window.innerWidth < 600 ) ? 5 : 20 ;
                $( this ).css( {
                    top: 40,
                    right: right,
                    position: 'fixed',
                    width: '410px',
                    height: 'auto',
                    margin: 0,
                    padding: 0,
                    listStyleType: 'none',
                    zIndex: 10000000
                } );
            }
        },
        parent: {
            object: '<li />',
            selector: 'li',
            css: { }
        },
        css: {
            display: 'none',
            width: '410px'
        }
    };
} )( jQuery );
