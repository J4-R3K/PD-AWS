<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class PD_TXT_Highlight_CC_Widget extends Widget_Base {

    public function get_name() {
        return 'pd_txt_highlight_cc';
    }

    public function get_title() {
        return __( 'PD TXT Highlight and CC', 'pd-txt-highlight' );
    }

    public function get_icon() {
        // Any Elementor icon, e.g., "eicon-code-highlight"
        return 'eicon-code-highlight';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function register_controls() {
        /**
         * CONTENT SECTION
         */
        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'pd-txt-highlight' ),
            ]
        );

        // WYSIWYG field
        $this->add_control(
            'highlighted_text',
            [
                'label'       => __( 'Text to Highlight / Copy', 'pd-txt-highlight' ),
                'type'        => Controls_Manager::WYSIWYG,
                'default'     => __( '<p>Your valuable text here...</p>', 'pd-txt-highlight' ),
                'label_block' => true,
            ]
        );

        // Copy button text
        $this->add_control(
            'copy_button_text',
            [
                'label'   => __( 'Copy Button Text', 'pd-txt-highlight' ),
                'type'    => Controls_Manager::TEXT,
                'default' => __( 'Copy', 'pd-txt-highlight' ),
            ]
        );

        // Message shown briefly after copy
        $this->add_control(
            'copied_message',
            [
                'label'   => __( 'Copied Message', 'pd-txt-highlight' ),
                'type'    => Controls_Manager::TEXT,
                'default' => __( 'Copied!', 'pd-txt-highlight' ),
            ]
        );

        $this->end_controls_section();

        /**
         * STYLE SECTION
         */
        // Container Style
        $this->start_controls_section(
            'style_section',
            [
                'label' => __( 'Container Style', 'pd-txt-highlight' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name'     => 'container_background',
                'label'    => __( 'Background', 'pd-txt-highlight' ),
                'types'    => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .pd-highlight-container',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name'     => 'container_border',
                'label'    => __( 'Border', 'pd-txt-highlight' ),
                'selector' => '{{WRAPPER}} .pd-highlight-container',
            ]
        );

        $this->add_control(
            'container_border_radius',
            [
                'label'      => __( 'Border Radius', 'pd-txt-highlight' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .pd-highlight-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'container_padding',
            [
                'label'      => __( 'Padding', 'pd-txt-highlight' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .pd-highlight-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Text Style
        $this->start_controls_section(
            'text_style_section',
            [
                'label' => __( 'Text Style', 'pd-txt-highlight' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label'     => __( 'Text Color', 'pd-txt-highlight' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pd-highlight-container' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'text_typography',
                'label'    => __( 'Typography', 'pd-txt-highlight' ),
                'selector' => '{{WRAPPER}} .pd-highlight-container',
            ]
        );

        $this->end_controls_section();

        // Copy Button Style
        $this->start_controls_section(
            'copy_button_style_section',
            [
                'label' => __( 'Copy Button', 'pd-txt-highlight' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        // Button Typography
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'copy_button_typography',
                'label'    => __( 'Typography', 'pd-txt-highlight' ),
                'selector' => '{{WRAPPER}} .pd-copy-button',
            ]
        );

        // Tabs for Normal & Hover
        $this->start_controls_tabs( 'copy_btn_style_tabs' );

            // Normal
            $this->start_controls_tab(
                'copy_btn_normal',
                [ 'label' => __( 'Normal', 'pd-txt-highlight' ) ]
            );

            $this->add_control(
                'copy_btn_text_color',
                [
                    'label'     => __( 'Text Color', 'pd-txt-highlight' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .pd-copy-button' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'copy_btn_bg_color',
                [
                    'label'     => __( 'Background Color', 'pd-txt-highlight' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .pd-copy-button' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            // Hover
            $this->start_controls_tab(
                'copy_btn_hover',
                [ 'label' => __( 'Hover', 'pd-txt-highlight' ) ]
            );

            $this->add_control(
                'copy_btn_text_color_hover',
                [
                    'label'     => __( 'Text Color (Hover)', 'pd-txt-highlight' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .pd-copy-button:hover' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'copy_btn_bg_color_hover',
                [
                    'label'     => __( 'Background Color (Hover)', 'pd-txt-highlight' ),
                    'type'      => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .pd-copy-button:hover' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

        $this->end_controls_tabs();
		/**
		 * NEW: Add responsive control for button padding
		 * Let the user adjust the space between the text and the border 
		 */
		$this->add_responsive_control(
			'copy_button_padding',
			[
				'label'      => __( 'Padding', 'pd-txt-highlight' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pd-copy-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        // ADDING BUTTON BORDER CONTROL
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name'     => 'copy_button_border',
                'label'    => __( 'Button Border', 'pd-txt-highlight' ),
                'selector' => '{{WRAPPER}} .pd-copy-button',
            ]
        );

        // Border Radius (slider)
        $this->add_control(
            'copy_btn_border_radius',
            [
                'label'      => __( 'Border Radius', 'pd-txt-highlight' ),
                'type'       => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range'      => [
                    'px' => [ 'min' => 0, 'max' => 50 ],
                    '%'  => [ 'min' => 0, 'max' => 100 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .pd-copy-button' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings         = $this->get_settings_for_display();
        $highlighted_text = $settings['highlighted_text'];
        $copy_button_text = $settings['copy_button_text'];
        $copied_message   = $settings['copied_message'];
        ?>
        <div class="pd-highlight-wrapper">
            <div class="pd-highlight-container">
                <!-- 1) The copy button is inside the container -->
                <button class="pd-copy-button"
                        data-copied-message="<?php echo esc_attr( $copied_message ); ?>">
                    <?php echo esc_html( $copy_button_text ); ?>
                </button>

                <!-- 2) The actual WYSIWYG text is in its own .pd-highlight-content -->
                <div class="pd-highlight-content">
                    <?php
                    // Print the user-entered WYSIWYG text
                    echo $this->parse_text_editor( $highlighted_text );
                    ?>
                </div>
            </div>
        </div>
        <?php
    }
}
