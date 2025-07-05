<?php
namespace Elementor;

if ( ! defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

/**
 * PD_Downloads_Filter_Widget
 *
 * This version ONLY outputs a filter form (no query).
 * We rely on an Elementor custom query hook to filter the "Posts" widget.
 * Includes style controls for:
 *   - Widget Title (Typography, Color)
 *   - Fields (Spacing, Typography, BG color, etc.)
 *   - Filter Button (Typography, color, hover, border-radius, box-shadow, etc.)
 */
class PD_Downloads_Filter_Widget extends Widget_Base {

    public function get_name() {
        return 'pd_downloads_filter';
    }

    public function get_title() {
        return __( 'PD Downloads Filter Form', 'pd-toc' );
    }

    public function get_icon() {
        return 'eicon-filters';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function register_controls() {
        // ====== CONTENT SECTION ======
        $this->start_controls_section(
            'filter_content_section',
            [
                'label' => __( 'Filter Form Settings', 'pd-toc' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'widget_title',
            [
                'label'       => __( 'Widget Title', 'pd-toc' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => __( 'Filter Downloads', 'pd-toc' ),
                'label_block' => true,
            ]
        );

        $this->end_controls_section();

        // ====== STYLE SECTION: Title ======
        $this->start_controls_section(
            'style_title_section',
            [
                'label' => __( 'Title Style', 'pd-toc' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        // Title Typography
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'label'    => __( 'Typography', 'pd-toc' ),
                'selector' => '{{WRAPPER}} .pd-filter-title',
            ]
        );

        // Title Color
        $this->add_control(
            'title_text_color',
            [
                'label'     => __( 'Text Color', 'pd-toc' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pd-filter-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ====== STYLE SECTION: Fields ======
        $this->start_controls_section(
            'style_fields_section',
            [
                'label' => __( 'Fields Style', 'pd-toc' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        // Horizontal spacing
        $this->add_responsive_control(
            'field_horizontal_spacing',
            [
                'label'     => __( 'Horizontal Spacing', 'pd-toc' ),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
                'default'   => [ 'size' => 8, 'unit' => 'px' ],
                'selectors' => [
                    '{{WRAPPER}} .pd-downloads-filter-form select, {{WRAPPER}} .pd-downloads-filter-form label' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Vertical spacing
        $this->add_responsive_control(
            'field_vertical_spacing',
            [
                'label'   => __( 'Vertical Spacing', 'pd-toc' ),
                'type'    => Controls_Manager::SLIDER,
                'range'   => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
                'default' => [ 'size' => 0, 'unit' => 'px' ],
                'selectors' => [
                    '{{WRAPPER}} .pd-downloads-filter-form .pd-field-group' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Fields Typography (select + label)
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'fields_typography',
                'label'    => __( 'Fields Typography', 'pd-toc' ),
                'selector' => '{{WRAPPER}} .pd-downloads-filter-form select, {{WRAPPER}} .pd-downloads-filter-form label',
            ]
        );

        // Fields Text Color
        $this->add_control(
            'fields_text_color',
            [
                'label'     => __( 'Fields Text Color', 'pd-toc' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pd-downloads-filter-form select, {{WRAPPER}} .pd-downloads-filter-form label' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Dropdown BG Color
        $this->add_control(
            'fields_bg_color',
            [
                'label'     => __( 'Dropdown BG Color', 'pd-toc' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pd-downloads-filter-form select' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ====== STYLE SECTION: Button ======
        $this->start_controls_section(
            'style_button_section',
            [
                'label' => __( 'Filter Button Style', 'pd-toc' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        // Button Typography
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'button_typography',
                'label'    => __( 'Typography', 'pd-toc' ),
                'selector' => '{{WRAPPER}} .pd-downloads-filter-form .pd-filter-button',
            ]
        );

        // Normal & Hover Tabs
        $this->start_controls_tabs('button_style_tabs');

        // Normal Tab
        $this->start_controls_tab('button_tab_normal', [ 'label' => __( 'Normal', 'pd-toc' ) ]);
        $this->add_control(
            'button_text_color',
            [
                'label'     => __( 'Text Color', 'pd-toc' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pd-downloads-filter-form .pd-filter-button' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'button_bg_color',
            [
                'label'     => __( 'Background Color', 'pd-toc' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pd-downloads-filter-form .pd-filter-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_tab();

        // Hover Tab
        $this->start_controls_tab('button_tab_hover', [ 'label' => __( 'Hover', 'pd-toc' ) ]);
        $this->add_control(
            'button_text_color_hover',
            [
                'label'     => __( 'Text Color (Hover)', 'pd-toc' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pd-downloads-filter-form .pd-filter-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'button_bg_color_hover',
            [
                'label'     => __( 'Background Color (Hover)', 'pd-toc' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pd-downloads-filter-form .pd-filter-button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        // Button Padding
        $this->add_responsive_control(
            'button_padding',
            [
                'label'      => __( 'Padding', 'pd-toc' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .pd-downloads-filter-form .pd-filter-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Button Margin
        $this->add_responsive_control(
            'button_margin',
            [
                'label'      => __( 'Margin', 'pd-toc' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .pd-downloads-filter-form .pd-filter-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Button Border
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name'     => 'button_border',
                'label'    => __( 'Button Border', 'pd-toc' ),
                'selector' => '{{WRAPPER}} .pd-downloads-filter-form .pd-filter-button',
            ]
        );

        // Button Border Radius
        $this->add_control(
            'button_border_radius',
            [
                'label'      => __( 'Border Radius', 'pd-toc' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .pd-downloads-filter-form .pd-filter-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Button Box Shadow
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'button_box_shadow',
                'label'    => __( 'Box Shadow', 'pd-toc' ),
                'selector' => '{{WRAPPER}} .pd-downloads-filter-form .pd-filter-button',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="pd-downloads-filter-container">
            <?php
            // Title with style controls
            if ( ! empty( $settings['widget_title'] ) ) {
                echo '<h4 class="pd-filter-title">' . esc_html( $settings['widget_title'] ) . '</h4>';
            }
            $this->render_filter_form();
            ?>
        </div>
        <?php
    }

    /**
     * Just output the form (no WP_Query).
     */
    private function render_filter_form() {
        ?>
        <form method="get" class="pd-downloads-filter-form" style="margin-bottom:1em;">
            <?php
            // 1) Category
            $download_cats = get_terms([
                'taxonomy'   => 'resource_category',
                'hide_empty' => true,
            ]);
            $cat_slug = isset( $_GET['download_cat'] ) ? sanitize_text_field( $_GET['download_cat'] ) : '';

            echo '<div class="pd-field-group">';
            echo '<label>' . esc_html__( 'Category:', 'pd-toc' ) . '</label>';
            echo '<select name="download_cat">';
            echo '<option value="">' . esc_html__( '-- Any Category --', 'pd-toc' ) . '</option>';
            foreach ( $download_cats as $term ) {
                echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( $cat_slug, $term->slug, false ) . '>'
                    . esc_html( $term->name ) . '</option>';
            }
            echo '</select>';
            echo '</div>';

            // 2) Our 9 fields
            $pods_fields = [
                'download_faceplatesize'       => 'Faceplate Size',
                'download_filesize'            => 'File Size',
                'download_filetype'            => 'File Type',
                'download_gangno'              => 'Gang No',
                'download_host'                => 'Host',
                'download_parametric_behavior' => 'Parametric Behavior',
                'download_rating'              => 'Rating',
                'download_revit_version'       => 'Revit Version',
                'download_type'                => 'Type',
            ];

            foreach ( $pods_fields as $field => $label ) {
                $vals = pd_get_distinct_field_values( $field );
                $sel  = isset( $_GET[ $field ] ) ? sanitize_text_field( $_GET[ $field ] ) : '';

                echo '<div class="pd-field-group">';
                echo '<label>' . esc_html( $label ) . ':</label>';
                echo '<select name="' . esc_attr( $field ) . '">';
                echo '<option value="">' . esc_html__( '-- Any --', 'pd-toc' ) . '</option>';
                foreach ( $vals as $v ) {
                    echo '<option value="' . esc_attr( $v ) . '" ' . selected( $sel, $v, false ) . '>'
                        . esc_html( $v ) . '</option>';
                }
                echo '</select>';
                echo '</div>';
            }

            // 3) Filter + Reset buttons
            // Calculate base URL (strip query string)
            $base_url = esc_url( strtok( $_SERVER['REQUEST_URI'], '?' ) );

            echo '<div class="pd-field-group">';
            echo '<button type="submit" class="pd-filter-button">' . esc_html__( 'Filter', 'pd-toc' ) . '</button>';
            echo '<button type="button" class="pd-filter-button" style="margin-left:4px;" onclick="window.location.href=\'' . $base_url . '\';">'
                . esc_html__( 'Reset', 'pd-toc' ) . '</button>';
            echo '</div>';
            ?>
        </form>
        <?php
    }
}
