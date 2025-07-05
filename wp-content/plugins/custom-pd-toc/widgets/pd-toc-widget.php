<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * PD_TOC_Widget
 */
class PD_TOC_Widget extends Widget_Base {

    public function get_name() {
        return 'PD_TOC';
    }

    public function get_title() {
        return __( 'PD TOC (Responsive)', 'pd-toc' );
    }

    public function get_icon() {
        return 'eicon-table-of-contents';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function register_controls() {

        // ====== CONTENT TAB ======
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'PD TOC', 'pd-toc' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        // TOC Title
        $this->add_control(
            'toc_title',
            [
                'label'       => __( 'Title', 'pd-toc' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => __( 'Table of Contents', 'pd-toc' ),
                'label_block' => true,
            ]
        );

        // Heading Levels
        $this->add_control(
            'heading_levels',
            [
                'label'    => __( 'Heading Levels', 'pd-toc' ),
                'type'     => Controls_Manager::SELECT2,
                'multiple' => true,
                'default'  => [ 'h2', 'h3' ],
                'options'  => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                ],
                'label_block' => true,
            ]
        );

        // Marker Style (4 options)
        $this->add_control(
            'marker_style',
            [
                'label'   => __( 'Marker Style', 'pd-toc' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'bullets',
                'options' => [
                    'bullets' => __( 'Bullets', 'pd-toc' ),
                    'numbers' => __( 'Numbers', 'pd-toc' ),
                    'hyphen'  => __( 'Hyphen', 'pd-toc' ),
                    'none'    => __( 'None', 'pd-toc' ),
                ],
            ]
        );

        // Toggle Icon
        $this->add_control(
            'toggle_icon',
            [
                'label'   => __( 'Toggle Icon', 'pd-toc' ),
                'type'    => Controls_Manager::ICONS,
                'default' => [
                    'value'   => 'fas fa-chevron-down',
                    'library' => 'fa-solid',
                ],
            ]
        );

        // Toggle Button Text
        $this->add_control(
            'toggle_button_text',
            [
                'label'   => __( 'Toggle Button Text', 'pd-toc' ),
                'type'    => Controls_Manager::TEXT,
                'default' => __( 'Toggle', 'pd-toc' ),
            ]
        );

        // Default State for Desktop
        $this->add_control(
            'default_state_desktop',
            [
                'label'   => __( 'Default State (Desktop)', 'pd-toc' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'expanded',
                'options' => [
                    'expanded'  => __( 'Expanded', 'pd-toc' ),
                    'collapsed' => __( 'Collapsed', 'pd-toc' ),
                ],
            ]
        );

        // Default State for Tablet
        $this->add_control(
            'default_state_tablet',
            [
                'label'   => __( 'Default State (Tablet)', 'pd-toc' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'collapsed',
                'options' => [
                    'expanded'  => __( 'Expanded', 'pd-toc' ),
                    'collapsed' => __( 'Collapsed', 'pd-toc' ),
                ],
            ]
        );

        // Default State for Mobile
        $this->add_control(
            'default_state_mobile',
            [
                'label'   => __( 'Default State (Mobile)', 'pd-toc' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'collapsed',
                'options' => [
                    'expanded'  => __( 'Expanded', 'pd-toc' ),
                    'collapsed' => __( 'Collapsed', 'pd-toc' ),
                ],
            ]
        );

        $this->end_controls_section();

        // ====== CONTENT TAB: No Headings Fallback ======
        $this->start_controls_section(
            'no_headings_section',
            [
                'label' => __( 'No Headings Fallback', 'pd-toc' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        // Fallback mode
        $this->add_control(
            'no_headings_fallback_type',
            [
                'label'   => __( 'Fallback Type', 'pd-toc' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'text',
                'options' => [
                    'none'       => __( 'None', 'pd-toc' ),
                    'text'       => __( 'Show Text', 'pd-toc' ),
                    'custom_url' => __( 'Custom URL', 'pd-toc' ),
                ],
            ]
        );

        // Fallback Text
        $this->add_control(
            'no_headings_fallback_text',
            [
                'label'     => __( 'Fallback Text', 'pd-toc' ),
                'type'      => Controls_Manager::TEXT,
                'default'   => __( 'No headings found.', 'pd-toc' ),
                'condition' => [
                    'no_headings_fallback_type' => 'text',
                ],
            ]
        );

        // Fallback URL
        $this->add_control(
            'no_headings_fallback_url',
            [
                'label'       => __( 'Fallback URL', 'pd-toc' ),
                'type'        => Controls_Manager::URL,
                'placeholder' => __( 'https://projectdesign.io/', 'pd-toc' ),
                'condition'   => [
                    'no_headings_fallback_type' => 'custom_url',
                ],
            ]
        );

        // Fallback URL Label
        $this->add_control(
            'no_headings_fallback_url_label',
            [
                'label'       => __( 'Link Label', 'pd-toc' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => __( 'Check out this link', 'pd-toc' ),
                'label_block' => true,
                'condition'   => [
                    'no_headings_fallback_type' => 'custom_url',
                ],
            ]
        );

        $this->end_controls_section();

        // ====== STYLE TAB: Container ======
        $this->start_controls_section(
            'container_style_section',
            [
                'label' => __( 'Container (Box)', 'pd-toc' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'container_background',
                'label'    => __( 'Background', 'pd-toc' ),
                'types'    => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .pd-toc-container',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'container_border',
                'label'    => __( 'Border', 'pd-toc' ),
                'selector' => '{{WRAPPER}} .pd-toc-container',
				'fields_options'=> [
					'border' => [
						'default' => 'none',
					],
					'width'  => [
						'default' => [
							'size' => '',
							'unit' => 'px',
						],
					],
				],
			]
		);

        $this->add_control(
            'container_border_radius',
            [
                'label'      => __( 'Border Radius', 'pd-toc' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .pd-toc-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'container_padding',
            [
                'label'      => __( 'Padding', 'pd-toc' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .pd-toc-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'container_box_shadow',
                'label'    => __( 'Box Shadow', 'pd-toc' ),
                'selector' => '{{WRAPPER}} .pd-toc-container',
            ]
        );

        $this->end_controls_section();

        // ====== STYLE TAB: TOC Elements ======
        $this->start_controls_section(
            'toc_style_section',
            [
                'label' => __( 'TOC Elements', 'pd-toc' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        // Title color
        $this->add_control(
            'title_color',
            [
                'label'     => __( 'Title Color', 'pd-toc' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pd-toc-header .pd-toc-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Title typography
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'label'    => __( 'Title Typography', 'pd-toc' ),
                'selector' => '{{WRAPPER}} .pd-toc-header .pd-toc-title',
            ]
        );

        // List Items color
        $this->add_control(
            'list_color',
            [
                'label'     => __( 'List Item Color', 'pd-toc' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pd-toc-list li a' => 'color: {{VALUE}};',
                ],
            ]
        );

        // List Items typography
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'list_typography',
                'label'    => __( 'List Typography', 'pd-toc' ),
                'selector' => '{{WRAPPER}} .pd-toc-list li a',
            ]
        );

        // Toggle button text color
        $this->add_control(
            'toggle_button_color',
            [
                'label'     => __( 'Toggle Button Text Color', 'pd-toc' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pd-toc-toggle' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Toggle button text typography
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'toggle_typography',
                'label'    => __( 'Toggle Button Typography', 'pd-toc' ),
                'selector' => '{{WRAPPER}} .pd-toc-toggle',
            ]
        );

        // Toggle Button Background (Normal & Hover)
        $this->start_controls_tabs( 'tabs_toggle_button_bg' );

            // Normal tab
            $this->start_controls_tab(
                'tab_toggle_button_bg_normal',
                [ 'label' => __( 'Normal', 'pd-toc' ) ]
            );

                // Background color
                $this->add_control(
                    'toggle_button_bg_color',
                    [
                        'label'     => __( 'Background Color', 'pd-toc' ),
                        'type'      => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .pd-toc-toggle' => 'background-color: {{VALUE}};',
                        ],
                    ]
                );

                // Border (default: none)
                $this->add_group_control(
                    Group_Control_Border::get_type(),
                    [
                        'name'      => 'toggle_button_border',
                        'label'     => __( 'Border', 'pd-toc' ),
                        'selector'  => '{{WRAPPER}} .pd-toc-toggle',
                        'separator' => 'before',
                    ]
                );

            $this->end_controls_tab();

            // Hover tab
            $this->start_controls_tab(
                'tab_toggle_button_bg_hover',
                [ 'label' => __( 'Hover', 'pd-toc' ) ]
            );

                // Hover background color
                $this->add_control(
                    'toggle_button_bg_hover_color',
                    [
                        'label'     => __( 'Hover Background Color', 'pd-toc' ),
                        'type'      => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .pd-toc-toggle:hover' => 'background-color: {{VALUE}} !important;',
                            '{{WRAPPER}} .pd-toc-toggle:focus' => 'background-color: {{VALUE}} !important;',
                        ],
                    ]
                );

                // Hover‐state border
                $this->add_group_control(
                    Group_Control_Border::get_type(),
                    [
                        'name'     => 'toggle_button_border_hover',
                        'label'    => __( 'Border', 'pd-toc' ),
                        'selector' => '{{WRAPPER}} .pd-toc-toggle:hover, {{WRAPPER}} .pd-toc-toggle:focus',
                    ]
                );

            $this->end_controls_tab();

        $this->end_controls_tabs();



        // Icon hover color
        $this->add_control(
            'icon_hover_color',
            [
                'label'     => __( 'Icon Hover Color', 'pd-toc' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pd-toc-toggle:hover .pd-toc-icon i, {{WRAPPER}} .pd-toc-toggle:hover .pd-toc-icon svg' => 'color: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        // Basic controls
        $marker_style     = $settings['marker_style'];
        $heading_levels   = ! empty( $settings['heading_levels'] ) ? $settings['heading_levels'] : [];
        $toc_title        = $settings['toc_title'];
        $toggle_icon      = $settings['toggle_icon'];
        $toggle_btn_text  = $settings['toggle_button_text'];

        // Responsive states
        $desktop_state = $settings['default_state_desktop'];
        $tablet_state  = $settings['default_state_tablet'];
        $mobile_state  = $settings['default_state_mobile'];

        // Fallback controls
        $fallback_type      = $settings['no_headings_fallback_type'];   // none | text | custom_url
        $fallback_text      = $settings['no_headings_fallback_text'];
        $fallback_url       = ! empty( $settings['no_headings_fallback_url']['url'] ) ? $settings['no_headings_fallback_url']['url'] : '';
        $fallback_url_label = $settings['no_headings_fallback_url_label'];

        // Convert heading levels to CSV
        $levels_csv = implode(',', $heading_levels);

        // Decide <ol> or <ul>, plus remove bullets if needed
        $list_tag = 'ul';         // default
        $list_css = '';           // inline style
        if ( $marker_style === 'numbers' ) {
            $list_tag = 'ol'; // show numbers
        } elseif ( $marker_style === 'hyphen' || $marker_style === 'none' ) {
            // force no bullets
            $list_css = 'list-style: none; padding-left: 1.5em;';
        }

        ?>
        <div class="pd-toc-container"
            data-levels="<?php echo esc_attr( $levels_csv ); ?>"
            data-desktop-state="<?php echo esc_attr( $desktop_state ); ?>"
            data-tablet-state="<?php echo esc_attr( $tablet_state ); ?>"
            data-mobile-state="<?php echo esc_attr( $mobile_state ); ?>"
            data-marker="<?php echo esc_attr( $marker_style ); ?>"
            data-fallback-type="<?php echo esc_attr( $fallback_type ); ?>"
            data-fallback-text="<?php echo esc_attr( $fallback_text ); ?>"
            data-fallback-url="<?php echo esc_url( $fallback_url ); ?>"
            data-fallback-url-label="<?php echo esc_attr( $fallback_url_label ); ?>"
        >
            <div class="pd-toc-header">
                <?php if ( ! empty( $toc_title ) ) : ?>
                    <div class="pd-toc-title"><?php echo esc_html( $toc_title ); ?></div>
                <?php endif; ?>

                <button class="pd-toc-toggle">
                    <span class="pd-toc-icon">
                        <?php
                        if ( $toggle_icon ) {
                            Icons_Manager::render_icon( $toggle_icon, [ 'aria-hidden' => 'true' ] );
                        }
                        ?>
                    </span>
                    <?php if ( ! empty( $toggle_btn_text ) ) : ?>
                        <span class="pd-toc-toggle-text"><?php echo esc_html( $toggle_btn_text ); ?></span>
                    <?php endif; ?>
                </button>
            </div>

            <?php
            // Output the list
            echo '<' . $list_tag . ' class="pd-toc-list" style="' . esc_attr($list_css) . '"></' . $list_tag . '>';
            ?>
        </div>
        <?php
    }
}
