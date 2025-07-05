<?php
/**
 * Plugin Name: PD Elementor Widgets
 * Description: Custom Elementor widgets (TOC, Highlight, Image Comparison, etc.).
 * Version:     2.6
 * Author:      Jarek Wityk
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* -----------------------------------------------------------------------
 * 1.  Ajax handlers shared by several widgets
 * -------------------------------------------------------------------- */
require_once __DIR__ . '/assets/ajax.php';

/* -----------------------------------------------------------------------
 * 2.  Asset loader
 *
 * - Registers the per-widget scripts/styles so Elementor can enqueue them
 *   only when the widget is present.
 * - Enqueues the global “always-on” assets on every page.
 * -------------------------------------------------------------------- */
function pd_toc_load_assets() {

    // ─── 2-A ▸ Image-Comparison (register ONLY) ────────────────────────────
    wp_register_script(
        'pd-img-compare-events', // jquery.event.move
        plugins_url( 'assets/jquery.event.move.min.js', __FILE__ ),
        [ 'jquery' ],
        '2.0.0',
        true
    );
    wp_register_script(
        'pd-img-compare-vendor', // TwentyTwenty core
        plugins_url( 'assets/twentytwenty.js', __FILE__ ),
        [ 'jquery', 'pd-img-compare-events' ],
        '1.0.0',
        true
    );
    wp_register_script(
        'pd-img-compare',        // our tiny init helper
        plugins_url( 'assets/pd-img-compare.js', __FILE__ ),
        [ 'jquery', 'pd-img-compare-vendor' ],
        '1.2',
        true
    );
    wp_register_style(
        'pd-img-compare-vendor',
        plugins_url( 'assets/twentytwenty.css', __FILE__ ),
        [],
        '1.0.0'
    );
    wp_register_style(
        'pd-img-compare',
        plugins_url( 'assets/pd-img-compare.css', __FILE__ ),
        [],
        '1.2'
    );

    // ─── 2-B ▸ Global assets (enqueue ON EVERY PAGE) ───────────────────────
    wp_enqueue_style(
        'pd-toc-style',
        plugins_url( 'assets/pd-toc-style.css', __FILE__ ),
        [],
        '1.0'
    );
    wp_enqueue_script(
        'pd-txt-highlight-script',
        plugins_url( 'assets/pd-txt-highlight-script.js', __FILE__ ),
        [ 'jquery' ],
        '1.0',
        true
    );
    wp_enqueue_style(
        'pd-table-import-style',
        plugins_url( 'assets/pd-table-import.css', __FILE__ ),
        [],
        '1.0'
    );

    wp_enqueue_script(
        'pd-toc-script',
        plugins_url('assets/pd-toc-script.js', __FILE__),
        ['jquery'],
        '1.0',
        true
    );


    // Woo: Ajax product nav
    wp_register_script(
        'pd-products-ajax',
        plugins_url( 'assets/pd-products-ajax.js', __FILE__ ),
        [ 'jquery' ],
        '1.1',
        true
    );
    wp_localize_script( 'pd-products-ajax', 'pd_ajax_obj', [
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'pd_products_nonce' ),
    ] );
    wp_enqueue_script( 'pd-products-ajax' );

    // Woo: Filter form
    wp_enqueue_script(
        'pd-woo-filter',
        plugins_url( 'assets/pd-woo-filter.js', __FILE__ ),
        [ 'jquery' ],
        '1.0',
        true
    );
    wp_localize_script( 'pd-woo-filter', 'pd_filter_ajax', [
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
    ] );
}
// Front-end live pages:
add_action( 'wp_enqueue_scripts',                'pd_toc_load_assets' );
// Elementor preview iframe:
add_action( 'elementor/preview/enqueue_scripts', 'pd_toc_load_assets' );
add_action( 'elementor/preview/enqueue_styles',  'pd_toc_load_assets' );
// Main Elementor editor canvas:
add_action( 'elementor/editor/after_enqueue_scripts','pd_toc_load_assets' );
add_action( 'elementor/editor/after_enqueue_styles', 'pd_toc_load_assets' );

function pd_toc_enqueue_advanced_table_assets() {
    // DataTables CSS & JS
    wp_register_style(
        'pd-datatables',
        plugins_url('assets/jquery.dataTables.min.css', __FILE__),
        [],
        '1.13.8'
    );
    wp_register_script(
        'pd-datatables',
        plugins_url('assets/jquery.dataTables.min.js', __FILE__),
        ['jquery'],
        '1.13.8',
        true
    );

    // PapaParse for CSV parsing
    wp_register_script(
        'pd-papaparse',
        plugins_url('assets/papaparse.min.js', __FILE__),
        [],
        '5.4.1',
        true
    );

    // Your custom advanced data table JS
    wp_register_script(
        'pd-advanced-data-table',
        plugins_url('assets/advanced-data-table.min.js', __FILE__),
        ['jquery', 'pd-datatables', 'pd-papaparse'],
        '1.0.0',
        true
    );
    wp_register_style(
        'pd-advanced-data-table',
        plugins_url('assets/advanced-data-table.min.css', __FILE__),
        [],
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'pd_toc_enqueue_advanced_table_assets');
add_action('elementor/editor/after_enqueue_scripts', 'pd_toc_enqueue_advanced_table_assets');

/* -----------------------------------------------------------------------
 * 3.  Utility – DISTINCT meta values for the Downloads filter
 * -------------------------------------------------------------------- */
if ( ! function_exists( 'pd_get_distinct_field_values' ) ) {
    function pd_get_distinct_field_values( $meta_key ) {
        global $wpdb;
        $sql = $wpdb->prepare(
            "SELECT DISTINCT pm.meta_value
             FROM {$wpdb->postmeta} pm
             JOIN {$wpdb->posts} p ON pm.post_id = p.ID
             WHERE pm.meta_key   = %s
               AND p.post_type   = 'resources'
               AND p.post_status = 'publish'
               AND pm.meta_value <> ''
             ORDER BY pm.meta_value ASC",
            $meta_key
        );
        return $wpdb->get_col( $sql );
    }
}

/* -----------------------------------------------------------------------
 * 4.  Register all custom widgets
 * -------------------------------------------------------------------- */
function pd_register_widgets( $widgets_manager ) {
    require_once __DIR__ . '/widgets/pd-toc-widget.php';
    $widgets_manager->register( new \Elementor\PD_TOC_Widget() );

    require_once __DIR__ . '/widgets/pd-txt-highlight-widget.php';
    $widgets_manager->register( new \Elementor\PD_TXT_Highlight_CC_Widget() );

    require_once __DIR__ . '/widgets/pd-downloads-filter-widget.php';
    $widgets_manager->register( new \Elementor\PD_Downloads_Filter_Widget() );

    require_once __DIR__ . '/widgets/pd-table-import-widget.php';
    $widgets_manager->register( new \Elementor\PD_Table_Import_Widget() );

    require_once __DIR__ . '/widgets/pd-woo-products-filter-widget.php';
    $widgets_manager->register( new \Elementor\PD_Woo_Products_Filter_Widget() );

    require_once __DIR__ . '/widgets/pd-woo-products-widget.php';
    $widgets_manager->register( new \Elementor\PD_Woo_Products_Widget() );

    require_once __DIR__ . '/widgets/pd-image-comparison-widget.php';
    $widgets_manager->register( new \Elementor\PD_Image_Comparison_Widget() );

    require_once __DIR__ . '/widgets/pd-advanced_data_table.php';
    $widgets_manager->register( new \Elementor\PD_Advanced_Data_Table_Widget() );

    // Add to the pd_register_widgets function
    require_once __DIR__ . '/widgets/pd-product-info-widget.php';
    $widgets_manager->register( new \Elementor\PD_Product_Info_Widget() );

}
add_action( 'elementor/widgets/register', 'pd_register_widgets' );

/* -----------------------------------------------------------------------
 * 5.  Custom Elementor queries
 * -------------------------------------------------------------------- */
/* Woo products filter */
function pd_elementor_query_woo_filter( $query ) {
    if ( ! empty( $_GET['filter_cat'] ) ) {
        $query->set( 'tax_query', [
            [
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $_GET['filter_cat'] ),
            ],
        ] );
    }
    $query->set( 'post_type', 'product' );
}
add_action( 'elementor/query/my_woo_products', 'pd_elementor_query_woo_filter' );

/* Resource downloads filter */
function pd_elementor_query_my_downloads( $query ) {
    $meta_query = [ 'relation' => 'AND' ];
    if ( ! empty( $_GET['download_cat'] ) ) {
        $query->set( 'tax_query', [
            [
                'taxonomy' => 'resource_category',
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $_GET['download_cat'] ),
            ],
        ] );
    }
    $fields = [
        'download_faceplatesize', 'download_filesize', 'download_filetype',
        'download_gangno', 'download_host', 'download_parametric_behavior',
        'download_rating', 'download_revit_version', 'download_type',
    ];
    foreach ( $fields as $f ) {
        if ( ! empty( $_GET[ $f ] ) ) {
            $meta_query[] = [
                'key'     => $f,
                'value'   => sanitize_text_field( $_GET[ $f ] ),
                'compare' => 'LIKE',
            ];
        }
    }
    if ( count( $meta_query ) > 1 ) {
        $query->set( 'meta_query', $meta_query );
    }
    $query->set( 'post_type', 'resources' );
}
add_action( 'elementor/query/my_downloads', 'pd_elementor_query_my_downloads' );
