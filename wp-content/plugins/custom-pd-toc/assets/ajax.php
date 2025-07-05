<?php
// custom-pd-toc/assets/ajax.php
// -----------------------------------------------------------------------------
// Ajax “load more / paginate” handler for PD Woo Products widget
// -----------------------------------------------------------------------------

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------------------
   Hooks
--------------------------------------------------------------------------- */
add_action( 'wp_ajax_pd_load_more_products',        'pd_load_more_products' );
add_action( 'wp_ajax_nopriv_pd_load_more_products', 'pd_load_more_products' );

/* ---------------------------------------------------------------------------
   Callback
--------------------------------------------------------------------------- */
function pd_load_more_products() {

    /* Basic nonce check */
    check_ajax_referer( 'pd_products_nonce', 'nonce' );

    /* ─────────── sanitize incoming data ─────────── */
    $paged          = max( 1, intval( $_POST['paged'] ?? 1 ) );
    $posts_per_page = intval( $_POST['posts_per_page'] ?? 12 );
    $columns        = intval( $_POST['columns'] ?? 3 );
    $filter_cat     = sanitize_text_field( $_POST['filter_cat'] ?? '' );

    /* ---------- base WP_Query args ---------- */
    $args = [
        'post_type'      => 'product',
        'posts_per_page' => $posts_per_page,
        'paged'          => $paged,
    ];

    /* 1️⃣  category filter (if any) */
    if ( $filter_cat ) {
        $args['tax_query'][] = [
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $filter_cat,
        ];
    }

    /* 2️⃣  attribute filters (pa_colour, pa_size …) */
    foreach ( wc_get_attribute_taxonomies() as $tax ) {
        $tax_name = wc_attribute_taxonomy_name( $tax->attribute_name ); // e.g. pa_colour
        if ( ! empty( $_POST[ $tax_name ] ) ) {
            $args['tax_query'][] = [
                'taxonomy' => $tax_name,
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $_POST[ $tax_name ] ),
            ];
        }
    }

    /* (If you later post exclude_products / exclude_categories
       via Ajax, handle them here the same way as in render()) */

    $query = new WP_Query( $args );

    /* ---------------------------------------------------------------------
       Build markup
    --------------------------------------------------------------------- */
    ob_start();

    if ( $query->have_posts() ) {

        echo '<div class="pd-woo-products-wrapper"'
           . ' data-count="'   . esc_attr( $posts_per_page ) . '"'
           . ' data-cat="'     . esc_attr( $filter_cat )     . '"'
           . ' data-columns="' . esc_attr( $columns )        . '">';

        /* ---------- PRODUCT GRID ---------- */
        printf(
            '<ul class="pd-woo-products-grid products columns-%1$d">',
            $columns
        );

        while ( $query->have_posts() ) {
            $query->the_post();
            wc_get_template_part( 'content', 'product' ); // prints <li class="product">
        }
        echo '</ul>';

        /* ---------- PAGINATION ---------- */
        $total_pages = $query->max_num_pages;

        if ( $total_pages > 1 ) {
            echo '<nav class="pd-pagination-wrapper"><div class="pd-pagination">';
            for ( $i = 1; $i <= $total_pages; $i++ ) {
                $active = ( $i === $paged ) ? ' current' : '';
                printf(
                    '<a href="#" class="pd-ajax-page%s" data-page="%d">%d</a>',
                    $active,
                    $i,
                    $i
                );
            }
            echo '</div></nav>';
        }

        echo '</div>'; // /.pd-woo-products-wrapper
    }

    wp_reset_postdata();

    wp_send_json_success( ob_get_clean() );
}
