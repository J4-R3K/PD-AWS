<?php
/**
 * Plugin Name: PD_Generator_Load_Calculator
 * Description: A tool to calculate generator load with multiple equipment support, PDF export, and graph visualization.
 * Version: 1.7
 * Author: Jarek Wityk
 * Author URI: https://projectdesign.io/
 */

if (!defined('ABSPATH')) {
    exit;
}

// Shortcode: [generator_load_calculator]
function generator_load_calculator_shortcode() {
    ob_start();
    include(plugin_dir_path(__FILE__) . 'template.php');
    return ob_get_clean();
}
add_shortcode('generator_load_calculator', 'generator_load_calculator_shortcode');

// Enqueue CSS & JS
function generator_load_calculator_scripts() {
    wp_enqueue_style('generator-load-style', plugins_url('/style.css', __FILE__));
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), null, true);
    wp_enqueue_script('generator-load-script', plugins_url('/script.js', __FILE__), array('jquery'), null, true);

    // Localize the AJAX URL
    wp_localize_script('generator-load-script', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
    ));
}
add_action('wp_enqueue_scripts', 'generator_load_calculator_scripts');

// AJAX: Calculation
function generator_load_calculator_ajax() {
    include(plugin_dir_path(__FILE__) . 'calculate.php');
    wp_die();
}
add_action('wp_ajax_calculate_load', 'generator_load_calculator_ajax');
add_action('wp_ajax_nopriv_calculate_load', 'generator_load_calculator_ajax');

// AJAX: PDF Export
function generator_load_calculator_pdf() {
    include(plugin_dir_path(__FILE__) . 'export-pdf.php');
    wp_die();
}
add_action('wp_ajax_export_pdf', 'generator_load_calculator_pdf');
add_action('wp_ajax_nopriv_export_pdf', 'generator_load_calculator_pdf');
