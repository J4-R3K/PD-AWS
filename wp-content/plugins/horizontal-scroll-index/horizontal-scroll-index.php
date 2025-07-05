<?php
/*
Plugin Name: PD_Horizontal_Scroll_Index
Description: Adds a horizontal scroll index with a dial-like interaction to your WordPress site.
Version: 1.0
Author: Jarek Wityk
 */
function horizontal_scroll_index_html() {
    $html = '<div class="horizontal-scroll-index">';
    foreach (range('A', 'Z') as $letter) {
        $html .= '<button class="index-letter">' . $letter . '</button>';
    }
    $html .= '<button class="index-letter">ALL</button>';
    // Add Lessons Learned button
    $html .= '<button class="index-letter lessons-learned">Lessons Learned</button>';
    $html .= '</div>';
    return $html;
}

add_shortcode('horizontal_scroll_index', 'horizontal_scroll_index_html');
// Function to enqueue scripts and styles
function enqueue_horizontal_scroll_index_scripts() {
    wp_enqueue_script('horizontal-scroll-index-js', plugin_dir_url(__FILE__) . 'index.js', array('jquery'), null, true);
    wp_enqueue_style('horizontal-scroll-index-css', plugin_dir_url(__FILE__) . 'style.css');
}
add_action('wp_enqueue_scripts', 'enqueue_horizontal_scroll_index_scripts');

