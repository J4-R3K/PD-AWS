<?php
/*
Plugin Name: PD_notion
Description: Connect your WordPress site to Notion.
Version: 1.1
Author: Jarek Wityk
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants.
define( 'PDNOTION_PATH', plugin_dir_path( __FILE__ ) );
define( 'PDNOTION_URL', plugin_dir_url( __FILE__ ) );

// Load necessary files.
require_once PDNOTION_PATH . 'includes/class-pdnotion-admin.php';
require_once PDNOTION_PATH . 'includes/class-pdnotion-public.php';
require_once PDNOTION_PATH . 'includes/class-pdnotion-api.php';

// Register activation and deactivation hooks.
register_activation_hook( __FILE__, 'pdnotion_activate' );
register_deactivation_hook( __FILE__, 'pdnotion_deactivate' );

function pdnotion_activate() {
    // Code for plugin activation.
}

function pdnotion_deactivate() {
    // Code for plugin deactivation.
}

// Initialize admin and public functionality.
if ( is_admin() ) {
    PDNotion_Admin::get_instance(); // Correctly initialize using singleton.
} else {
    new PDNotion_Public();
}
