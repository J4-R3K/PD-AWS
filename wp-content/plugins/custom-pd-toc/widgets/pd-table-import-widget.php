<?php
namespace Elementor;

if ( ! defined('ABSPATH') ) {
    exit;
}

class PD_Table_Import_Widget extends Widget_Base {

    public function get_name() {
        return 'pd_table_import';
    }

    public function get_title() {
        return __( 'PD Table Import', 'pd-toc' );
    }

    public function get_icon() {
        return 'eicon-table';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function register_controls() {
        // === CONTENT: Table Data ===
        $this->start_controls_section('content_section', [
            'label' => __( 'Table Data', 'pd-toc' ),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('table_raw', [
            'label' => __( 'Paste Table (Tab-Separated)', 'pd-toc' ),
            'type' => Controls_Manager::TEXTAREA,
            'rows' => 10,
            'placeholder' => "Col1\tCol2\tCol3\nVal1\tVal2\tVal3",
        ]);

        $this->add_control('notice', [
            'type' => Controls_Manager::RAW_HTML,
            'raw' => __( 'Paste from Excel or Sheets. Tabs will be parsed into columns. HTML and links allowed.', 'pd-toc' ),
            'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
        ]);

        $this->end_controls_section();

        // === CONTENT: Rich Text (Optional) ===
        $this->start_controls_section('text_editor_section', [
            'label' => __('Rich Text Content (Optional)', 'pd-toc'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('rich_text_block', [
            'label' => __('Editable Text Block', 'pd-toc'),
            'type' => Controls_Manager::WYSIWYG,
            'default' => __('You can enter text here and style it with Elementor toolbar, including <a href="#">links</a>.', 'pd-toc'),
        ]);

        $this->end_controls_section();

        // === STYLE: Whole Table ===
        $this->start_controls_section('style_section', [
            'label' => __( 'Table Style (All Cells)', 'pd-toc' ),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'cell_typography',
            'selector' => '{{WRAPPER}} .pd-table-import-table td, {{WRAPPER}} .pd-table-import-table th',
        ]);

        $this->add_control('cell_text_color', [
            'label' => __( 'Text Color', 'pd-toc' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pd-table-import-table td, {{WRAPPER}} .pd-table-import-table th' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('cell_bg_color', [
            'label' => __( 'Background Color', 'pd-toc' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pd-table-import-table td, {{WRAPPER}} .pd-table-import-table th' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), [
            'name' => 'table_border',
            'selector' => '{{WRAPPER}} .pd-table-import-table',
        ]);

        $this->end_controls_section();

        // === STYLE: Header ===
        $this->start_controls_section('style_header_section', [
            'label' => __('Header Style', 'pd-toc'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
            'name' => 'header_typography',
            'selector' => '{{WRAPPER}} .pd-table-import-table thead th',
        ]);

        $this->add_control('header_text_color', [
            'label' => __('Text Color', 'pd-toc'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pd-table-import-table thead th' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('header_bg_color', [
            'label' => __('Background Color', 'pd-toc'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pd-table-import-table thead th' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();

        // === STYLE: First Column ===
        $this->start_controls_section('style_first_col_section', [
            'label' => __('First Column Style', 'pd-toc'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('first_col_text_color', [
            'label' => __('Text Color', 'pd-toc'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pd-table-import-table td.pd-first-col' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('first_col_bg_color', [
            'label' => __('Background Color', 'pd-toc'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pd-table-import-table td.pd-first-col' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('first_col_text_bold', [
            'label' => __('Bold Text', 'pd-toc'),
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
        ]);

        $this->end_controls_section();

        // === STYLE: Last Row ===
        $this->start_controls_section('style_last_row_section', [
            'label' => __('Last Row Style', 'pd-toc'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('last_row_text_color', [
            'label' => __('Text Color', 'pd-toc'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pd-table-import-table tr.pd-last-row td' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('last_row_bg_color', [
            'label' => __('Background Color', 'pd-toc'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pd-table-import-table tr.pd-last-row td' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        // === Rich Text Block ===
        if ( ! empty( $settings['rich_text_block'] ) ) {
            echo '<div class="pd-rich-text-content">';
            echo wp_kses_post( $settings['rich_text_block'] );
            echo '</div>';
        }

        // === Table Output ===
        if ( empty( $settings['table_raw'] ) ) {
            echo '<div class="pd-table-import-empty">'.esc_html__('No table data provided.','pd-toc').'</div>';
            return;
        }

        $rows = explode("\n", trim($settings['table_raw']));
        $total_rows = count($rows);
        $bold_first_col = ! empty($settings['first_col_text_bold']) && $settings['first_col_text_bold'] === 'yes';

        echo '<div class="pd-table-import-wrapper"><table class="pd-table-import-table"><thead>';

        foreach ( $rows as $index => $line ) {
            $cols = explode("\t", trim($line));
            $is_header = ($index === 0);
            $is_last_row = ($index === $total_rows - 1);
            $row_class = $is_last_row ? ' class="pd-last-row"' : '';

            // === Handle full-span single-cell row ===
            if (count($cols) === 1) {
                echo "<tr><td colspan=\"100%\">" . wp_kses_post($cols[0]) . "</td></tr>";
                continue;
            }

            echo $is_header ? '<tr>' : "<tr{$row_class}>";
            foreach ( $cols as $col_index => $cell ) {
                $is_first_col = ($col_index === 0);
                $col_class = $is_first_col ? ' class="pd-first-col"' : '';
                $cell_tag = $is_header ? 'th' : 'td';
                $style_inline = ($bold_first_col && !$is_header && $is_first_col) ? ' style="font-weight:bold;"' : '';

                // Auto-link URLs
                $cell = preg_replace(
                    '#\b(https?://[^\s<]+)#i',
                    '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
                    $cell
                );

                echo "<{$cell_tag}{$col_class}{$style_inline}>".wp_kses_post($cell)."</{$cell_tag}>";
            }

            echo '</tr>';
            if ($is_header) echo '</thead><tbody>';
        }

        echo '</tbody></table></div>';
    }
}
