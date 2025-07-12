<?php
/*
Plugin Name: PD Media Credit
Description: Adds a “Credit” column to the Media Library; stores to post-meta and embeds into every image file (all sizes) via ExifTool.
Version: 1.5
Author: Your Name
*/

if ( ! defined( 'ABSPATH' ) ) { exit; }
define( 'PD_MC_EXIFTOOL', '/usr/local/bin/exiftool' );   // you validated 13.32 here

class PD_MediaCredit {
    const META_KEY = 'pd_media_credit';

    public function __construct() {
        add_filter( 'manage_upload_columns',        [ $this, 'add_column' ] );
        add_action( 'manage_media_custom_column',   [ $this, 'render_column' ], 10, 2 );
        add_action( 'admin_enqueue_scripts',        [ $this, 'enqueue_js' ] );
        add_action( 'wp_ajax_pd_mc_update_credit',  [ $this, 'ajax_update_credit' ] );
    }

    public function add_column( $cols ) {
        $cols['pd_mc_credit'] = __( 'Credit', 'pd-mc' );
        return $cols;
    }

    public function render_column( $col, $post_id ) {
        if ( 'pd_mc_credit' !== $col ) { return; }

        $value = get_post_meta( $post_id, self::META_KEY, true );

        if ( ! current_user_can( 'edit_posts' ) ) {
            echo esc_html( $value );
            return;
        }

        wp_nonce_field( "pd_mc_credit_$post_id", "pd_mc_credit_$post_id" );

        printf(
            '<input type="text" class="pd-mc-credit" data-attach="%1$d" value="%2$s" placeholder="%3$s" style="width:70%%;" /> ',
            $post_id,
            esc_attr( $value ),
            esc_attr__( 'e.g. Jane Doe / Unsplash', 'pd-mc' )
        );
        printf(
            '<button type="button" class="pd-mc-save button button-small" data-attach="%d">%s</button>',
            $post_id,
            esc_html__( 'Save', 'pd-mc' )
        );
    }

    public function enqueue_js( $hook ) {
        if ( 'upload.php' !== $hook ) { return; }
        wp_enqueue_script(
            'pd-mc-js',
            plugins_url( 'pd-media-credit.js', __FILE__ ),
            [ 'jquery' ],
            '1.4',
            true
        );
        wp_localize_script( 'pd-mc-js', 'PD_MC', [
            'ajax' => admin_url( 'admin-ajax.php' ),
            'cap'  => current_user_can( 'edit_posts' )
        ] );
    }

    public function ajax_update_credit() {
        $att_id = isset( $_POST['attach'] ) ? (int) $_POST['attach'] : 0;
        $credit = sanitize_text_field( wp_unslash( $_POST['credit'] ?? '' ) );
        $nonce  = $_POST['nonce'] ?? '';

        if ( ! current_user_can( 'edit_posts' ) ||
            ! wp_verify_nonce( $nonce, "pd_mc_credit_$att_id" ) ) {
            wp_send_json_error( 'permission' );
        }

        $post = get_post( $att_id );
        if ( ! $post || 'attachment' !== $post->post_type ) {
            wp_send_json_error( 'invalid' );
        }

        update_post_meta( $att_id, self::META_KEY, $credit );
        $this->embed_credit_all_sizes( $att_id, $credit );

        wp_send_json_success( $credit );
    }

    /* ─── Embed into original + every generated size ─── */
    private function embed_credit_all_sizes( $att_id, $credit ) {

        $meta  = wp_get_attachment_metadata( $att_id );
        $paths = [];

        /* Original */
        if ( $file = get_attached_file( $att_id ) ) {
            $paths[] = $file;
        }

        /* Extra sizes */
        if ( ! empty( $meta['sizes'] ) && $base = dirname( get_attached_file( $att_id ) ) ) {
            foreach ( $meta['sizes'] as $size ) {
                $paths[] = $base . '/' . $size['file'];
            }
        }

        $tags = [
            '-overwrite_original',
            '-XMP:Artist='      . $credit,
            '-XMP-dc:Creator=' . $credit,
            '-IPTC:Credit='    . $credit,
        ];

        foreach ( $paths as $p ) {
            if ( ! is_writable( $p ) ) { continue; }

            $cmd = sprintf(
                '%s %s %s 2>&1',
                escapeshellcmd( PD_MC_EXIFTOOL ),
                implode( ' ', array_map( 'escapeshellarg', $tags ) ),
                escapeshellarg( $p )
            );
            exec( $cmd );
            $bak = $p . '-original';
            if ( file_exists( $bak ) ) { unlink( $bak ); }
        }
    }
}

new PD_MediaCredit();
