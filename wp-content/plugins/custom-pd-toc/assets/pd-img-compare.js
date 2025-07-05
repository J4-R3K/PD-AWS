/**
 * PD Image Comparison – Elementor
 * graceful loader that waits until EA’s   $.fn.eatwentytwenty   is ready
 * and then aliases it to   $.fn.twentytwenty   before initialising.
 */
( function ( $ ) {

    /* --------------------------------------------------------------
       1 · Make an alias whenever the vendor script finally arrives
    -------------------------------------------------------------- */
    function patchPlugin () {
        if ( ! $.fn.twentytwenty && $.fn.eatwentytwenty ) {
            $.fn.twentytwenty = $.fn.eatwentytwenty;          // one-line shim
        }
        return !! $.fn.twentytwenty;                          // true if ready
    }

    /* --------------------------------------------------------------
       2 · Initialise one comparison box
    -------------------------------------------------------------- */
    function runOne ( $el ) {

        if ( $el.data( 'init' ) ) return;                      // only once
        if ( ! patchPlugin() ) return;                        // library not ready yet

        $el.twentytwenty( {
            default_offset_pct    : parseFloat( $el.data( 'offset' ) ) || 0.7,
            orientation           : $el.data( 'orientation' ) || 'horizontal',
            before_label          : $el.data( 'before' )      || 'Before',
            after_label           : $el.data( 'after' )       || 'After',
            no_overlay            : false,
            move_slider_on_hover  : false,
            move_with_handle_only : true
        } );

        $el.data( 'init', 1 );
    }

    /* --------------------------------------------------------------
       3 · Scan a scope and (re)try until everything is ready
    -------------------------------------------------------------- */
    function init ( scope ) {

        var $scope = $( scope );

        $scope.find( '.pd-img-compare' ).each( function () {
            var $box = $( this );

            // first try immediately …
            if ( runOne( $box ) ) return;

            // … otherwise retry every 120 ms until the vendor file lands.
            var tries = 0, max = 20;                           // 20 × 120 ms ≈ 2.4 s
            var timer = setInterval( function () {
                if ( runOne( $box ) || ++tries >= max ) {
                    clearInterval( timer );
                }
            }, 120 );
        } );
    }

    /* --------------------------------------------------------------
       4 · Hooks
    -------------------------------------------------------------- */
    $( function () { init( document ); } );                   // front-end

    if ( window.elementorFrontend ) {                         // editor
        elementorFrontend.hooks.addAction(
            'frontend/element_ready/pd_image_comparison.default',
            init
        );
    }

} )( jQuery );