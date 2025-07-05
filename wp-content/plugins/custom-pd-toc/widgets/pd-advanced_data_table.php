<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit;

class PD_Advanced_Data_Table_Widget extends Widget_Base {

    public function get_name() {
        return 'pd_advanced_data_table';
    }

    public function get_title() {
        return __( 'PD Advanced Data Table', 'pd-toc' );
    }

    public function get_icon() {
        return 'eicon-table';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function register_controls() {
        // === Data Source Selection ===
        $this->start_controls_section('data_source_section', [
            'label' => __('Data Source', 'pd-toc'),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control('data_source', [
            'label'   => __('Source', 'pd-toc'),
            'type'    => Controls_Manager::SELECT,
            'options' => [
                'static' => __('Static Data', 'pd-toc'),
                'csv'    => __('CSV Upload', 'pd-toc'),
                'gsheet' => __('Google Sheets', 'pd-toc'),
            ],
            'default' => 'static',
        ]);
        $this->add_control('table_raw', [
            'label' => __('Paste Table (Tab-Separated or Markdown)', 'pd-toc'),
            'type'  => Controls_Manager::TEXTAREA,
            'rows'  => 10,
            'condition' => ['data_source' => 'static'],
        ]);
        $this->add_control('csv_file', [
            'label' => __('Upload CSV', 'pd-toc'),
            'type'  => Controls_Manager::MEDIA,
            'media_type' => 'application/csv',
            'condition' => ['data_source' => 'csv'],
        ]);
        $this->add_control('gsheet_url', [
            'label' => __('Google Sheets URL', 'pd-toc'),
            'type'  => Controls_Manager::TEXT,
            'condition' => ['data_source' => 'gsheet'],
        ]);
        $this->end_controls_section();

        // === Table Features ===
        $this->start_controls_section('features_section', [
            'label' => __('Table Features', 'pd-toc'),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control('enable_search', [
            'label' => __('Enable Search', 'pd-toc'),
            'type'  => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);
        $this->add_control('enable_sort', [
            'label' => __('Enable Sorting', 'pd-toc'),
            'type'  => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);
        $this->add_control('enable_pagination', [
            'label' => __('Enable Pagination', 'pd-toc'),
            'type'  => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);
        $this->add_control('enable_export', [
            'label' => __('Enable Export (CSV/Excel)', 'pd-toc'),
            'type'  => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);
        $this->add_control('enable_inline_edit', [
            'label' => __('Enable Inline Editing', 'pd-toc'),
            'type'  => Controls_Manager::SWITCHER,
            'default' => 'no',
        ]);
        $this->end_controls_section();

        // === Style Controls (Global) ===
        $this->start_controls_section('style_section', [
            'label' => __('Table Style', 'pd-toc'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'cell_typography',
                'selector' => '{{WRAPPER}} .pd-adt-table td, {{WRAPPER}} .pd-adt-table th',
            ]
        );
        $this->add_control('cell_text_color', [
            'label' => __('Text Color', 'pd-toc'),
            'type'  => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pd-adt-table td, {{WRAPPER}} .pd-adt-table th' => 'color: {{VALUE}};',
            ],
        ]);
        $this->add_control('cell_bg_color', [
            'label' => __('Background Color', 'pd-toc'),
            'type'  => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pd-adt-table td, {{WRAPPER}} .pd-adt-table th' => 'background-color: {{VALUE}};',
            ],
        ]);
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name'     => 'table_border',
                'selector' => '{{WRAPPER}} .pd-adt-table',
            ]
        );
        $this->end_controls_section();

        // === Advanced Per-Cell Styling ===
        $this->start_controls_section('cell_style_section', [
            'label' => __('Per-Cell Styling', 'pd-toc'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);
        $repeater = new \Elementor\Repeater();

        $repeater->add_control('row_index', [
            'label' => __('Row', 'pd-toc'),
            'type'  => Controls_Manager::NUMBER,
            'min'   => 1,
            'default' => 1,
        ]);
        $repeater->add_control('col_index', [
            'label' => __('Column', 'pd-toc'),
            'type'  => Controls_Manager::NUMBER,
            'min'   => 1,
            'default' => 1,
        ]);
        $repeater->add_control('cell_text_color', [
            'label' => __('Text Color', 'pd-toc'),
            'type'  => Controls_Manager::COLOR,
        ]);
        $repeater->add_control('cell_bg_color', [
            'label' => __('Background Color', 'pd-toc'),
            'type'  => Controls_Manager::COLOR,
        ]);
        $repeater->add_control('cell_bold', [
            'label' => __('Bold', 'pd-toc'),
            'type'  => Controls_Manager::SWITCHER,
            'default' => '',
        ]);
        $repeater->add_control('cell_custom_css', [
            'label' => __('Custom CSS', 'pd-toc'),
            'type'  => Controls_Manager::TEXT,
            'placeholder' => 'e.g. font-size:18px;',
        ]);
        $this->add_control('cell_styles', [
            'label' => __('Cell Styles', 'pd-toc'),
            'type'  => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'title_field' => 'Row: {{row_index}}, Col: {{col_index}}',
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        // Enqueue required assets for the advanced data table
        wp_enqueue_style('pd-datatables');
        wp_enqueue_script('pd-datatables');
        wp_enqueue_script('pd-papaparse');
        wp_enqueue_style('pd-advanced-data-table');
        wp_enqueue_script('pd-advanced-data-table');

        $settings = $this->get_settings_for_display();

        $table_id = 'pd-adt-table-' . $this->get_id();
        $data = [
            'source'      => $settings['data_source'],
            'search'      => $settings['enable_search'] === 'yes',
            'sort'        => $settings['enable_sort'] === 'yes',
            'pagination'  => $settings['enable_pagination'] === 'yes',
            'export'      => $settings['enable_export'] === 'yes',
            'inline_edit' => $settings['enable_inline_edit'] === 'yes',
        ];
        if ($settings['data_source'] === 'static') {
            $data['table_raw'] = $settings['table_raw'];
        } elseif ($settings['data_source'] === 'csv' && !empty($settings['csv_file']['url'])) {
            $data['csv_url'] = $settings['csv_file']['url'];
        } elseif ($settings['data_source'] === 'gsheet') {
            $data['gsheet_url'] = $settings['gsheet_url'];
        }

        // Prepare per-cell styles as a lookup array: [row][col] = [styles]
        $cell_styles = [];
        if (!empty($settings['cell_styles'])) {
            foreach ($settings['cell_styles'] as $style) {
                $row = intval($style['row_index']) - 1;
                $col = intval($style['col_index']) - 1;
                $cell_styles[$row][$col] = $style;
            }
        }

        // Output table container for JS initialization
        echo '<div class="pd-adt-wrapper">';
        echo '<div id="' . esc_attr($table_id) . '" class="pd-adt-table" data-table=\'' . esc_attr(json_encode($data)) . '\'>';

        // If static data, render the table server-side for SEO/fallback
        if ($settings['data_source'] === 'static' && !empty($settings['table_raw'])) {
            $rows = explode("\n", trim($settings['table_raw']));
            $total_rows = count($rows);
            echo '<table><thead>';
            foreach ($rows as $row_idx => $line) {
                $cols = explode("\t", trim($line));
                $is_header = ($row_idx === 0);
                $row_class = ($row_idx === $total_rows - 1) ? ' class="pd-adt-last-row"' : '';
                echo $is_header ? '<tr>' : "<tr{$row_class}>";
                foreach ($cols as $col_idx => $cell) {
                    $cell_tag = $is_header ? 'th' : 'td';
                    $style_inline = '';
                    if (isset($cell_styles[$row_idx][$col_idx])) {
                        $s = $cell_styles[$row_idx][$col_idx];
                        $styles = [];
                        if (!empty($s['cell_text_color'])) $styles[] = 'color:' . $s['cell_text_color'];
                        if (!empty($s['cell_bg_color'])) $styles[] = 'background-color:' . $s['cell_bg_color'];
                        if (!empty($s['cell_bold']) && $s['cell_bold'] === 'yes') $styles[] = 'font-weight:bold';
                        if (!empty($s['cell_custom_css'])) $styles[] = $s['cell_custom_css'];
                        if ($styles) $style_inline = ' style="' . esc_attr(implode(';', $styles)) . '"';
                    }
                    // Auto-link URLs
                    $cell = preg_replace(
                        '#\b(https?://[^\s<]+)#i',
                        '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
                        $cell
                    );
                    echo "<{$cell_tag}{$style_inline}>".wp_kses_post($cell)."</{$cell_tag}>";
                }
                echo '</tr>';
                if ($is_header) echo '</thead><tbody>';
            }
            echo '</tbody></table>';
        }

        echo '</div></div>';
    }
}
