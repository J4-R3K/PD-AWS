<?php
namespace Elementor;
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Widget_Base;

class PD_Image_Comparison_Widget extends Widget_Base {

    /* ───── meta ───── */
    public function get_name()        { return 'pd_image_comparison'; }
    public function get_title()       { return __( 'PD Image Comparison', 'pd-toc' ); }
    public function get_icon()        { return 'eicon-image-before-after'; }
    public function get_categories()  { return [ 'general' ]; }
    public function get_script_depends() {
        return [
            'pd-img-compare-events',   // jquery.event.move
            'pd-img-compare-vendor',   // twentytwenty.js
            'pd-img-compare',          // your init helper
        ];
    }
    public function get_style_depends() {
        return [
            'pd-img-compare',          // your comparison CSS
            'pd-img-compare-vendor',   // twentytwenty.css
        ];
    }

    /* ───── controls ───── */
    protected function register_controls() {
        $this->start_controls_section( 'images', [ 'label' => __( 'Images', 'pd-toc' ) ] );
        $this->add_control( 'before', [
            'label'   => __( 'Before Image', 'pd-toc' ),
            'type'    => Controls_Manager::MEDIA,
            'default' => [ 'url' => Utils::get_placeholder_image_src() ],
        ] );
        $this->add_control( 'before_label', [
            'label'   => __( 'Label Before', 'pd-toc' ),
            'type'    => Controls_Manager::TEXT,
            'default' => __( 'Before', 'pd-toc' ),
        ] );
        $this->add_control( 'after', [
            'label'   => __( 'After Image', 'pd-toc' ),
            'type'    => Controls_Manager::MEDIA,
            'default' => [ 'url' => Utils::get_placeholder_image_src() ],
        ] );
        $this->add_control( 'after_label', [
            'label'   => __( 'Label After', 'pd-toc' ),
            'type'    => Controls_Manager::TEXT,
            'default' => __( 'After', 'pd-toc' ),
        ] );
        $this->end_controls_section();

        $this->start_controls_section( 'options', [ 'label' => __( 'Options', 'pd-toc' ) ] );
        $this->add_control( 'orientation', [
            'label'   => __( 'Orientation', 'pd-toc' ),
            'type'    => Controls_Manager::SELECT,
            'options' => [
                'horizontal' => __( 'Horizontal', 'pd-toc' ),
                'vertical'   => __( 'Vertical',   'pd-toc' ),
            ],
            'default' => 'horizontal',
        ] );
        $this->add_control( 'start', [
            'label'      => __( 'Initial Split (%)', 'pd-toc' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ '%' ],
            'range'      => [ '%' => [ 'min' => 10, 'max' => 90 ] ],
            'default'    => [ 'size' => 70, 'unit' => '%' ],
        ] );
        $this->end_controls_section();
    }

    /* ───── render ───── */
    protected function render() {
        $s       = $this->get_settings_for_display();
        $id      = 'pd-img-compare-' . $this->get_id();
        $this->add_render_attribute( 'wrapper', [
            'id'               => $id,
            'class'            => 'pd-img-compare twentytwenty-container',
            'data-orientation' => $s['orientation'],
            'data-offset'      => ( $s['start']['size'] / 100 ),
            'data-before'      => $s['before_label'],
            'data-after'       => $s['after_label'],
        ] );
        echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';
        echo '<img src="' . esc_url( $s['before']['url'] ) . '" alt="">';
        echo '<img src="' . esc_url( $s['after']['url']  ) . '" alt="">';
        echo '</div>';
    }
}

