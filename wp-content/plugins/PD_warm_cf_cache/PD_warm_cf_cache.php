<?php
/*
Plugin Name: PD_Warm Cloudflare Cache
Description: Adds an admin-bar button (“⚡ Warm CF Cache”) that primes Cloudflare APO for a handful of key URLs.
Version: 1.0
Author: Jarek Wityk
*/

# --------------------------------------------------
# 1.  List of URLs to warm
# --------------------------------------------------
function PD_wcc_urls() {
    return array(
        home_url( '/' ),
        home_url( '/shop/' ),
		home_url( '/bim/' ),
		home_url( '/services/' ),
		home_url( '/downloads/' ),
        home_url( '/product-category/rvt/' ),
		home_url( '/product-category/rfa/' ),
        home_url( '/project-template_rvt_2022/' ),
        home_url( '/knowledgehub/' ),
    );
}

# --------------------------------------------------
# 2.  Warm-up function – non-blocking GETs
# --------------------------------------------------
function PD_wcc_warm() {
    foreach ( PD_wcc_urls() as $url ) {
        wp_remote_get( $url, array(
            'timeout'  => 15,
            'blocking' => false,          // don’t slow the click
            'headers'  => array(
                'User-Agent' => 'PD_Warm_CF/1.0'
            ),
        ) );
    }
}

# --------------------------------------------------
# 3.  Add admin-bar button for admins
# --------------------------------------------------
add_action( 'admin_bar_menu', function ( $bar ) {
    if ( current_user_can( 'manage_options' ) ) {
        $bar->add_node( array(
            'id'    => 'pd_warm_cf',
            'title' => '⚡ Warm CF Cache',
            'href'  => wp_nonce_url( admin_url( '?pd_wcc_warm=1' ), 'pd_wcc' ),
            'meta'  => array( 'title' => 'Prime Cloudflare edge cache' ),
        ) );
    }
}, 100 );

# --------------------------------------------------
# 4.  Handle the click
# --------------------------------------------------
add_action( 'admin_init', function () {
    if ( isset( $_GET['pd_wcc_warm'] ) && check_admin_referer( 'pd_wcc' ) ) {
        PD_wcc_warm();
        add_action( 'admin_notices', function () {
            echo '<div class="notice notice-success"><p>Cloudflare cache warmed 🚀</p></div>';
        } );
    }
} );

# --------------------------------------------------
# 5. (Optional) auto-warm when Cloudflare WP plugin purges cache
# --------------------------------------------------
if ( function_exists( 'cloudflare' ) ) {
    add_action( 'cloudflare_proxy_after_cache_purge', 'PD_wcc_warm' );
}

