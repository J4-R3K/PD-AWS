<?php
/**
 *  PD Woo Products – Filter bar
 */
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) { exit; }

class PD_Woo_Products_Filter_Widget extends Widget_Base {

    public function get_name()        { return 'pd_woo_products_filter'; }
    public function get_title()       { return __( 'PD Woo Products Filter', 'pd-toc' ); }
    public function get_icon()        { return 'eicon-filter'; }
    public function get_categories()  { return [ 'general' ]; }

    /* ---------------------------------------------------------------------
       Controls
    --------------------------------------------------------------------- */
    protected function register_controls() {

        $this->start_controls_section( 'section_content', [
            'label' => __( 'Filter Settings', 'pd-toc' ),
        ] );

            $this->add_control( 'filter_title', [
                'label'   => __( 'Title', 'pd-toc' ),
                'type'    => Controls_Manager::TEXT,
                'default' => __( 'Filter Products', 'pd-toc' ),
            ] );

            /* Mobile columns override (inherited by tablet if not set) */
            $this->add_responsive_control( 'columns_responsive', [
                'label'     => __( 'Columns (Mobile)', 'pd-toc' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [ '1' => '1', '2' => '2' ],
                'default'   => '1',
                'selectors' => [
                    '{{WRAPPER}} .pd-woo-products-grid' => 'grid-template-columns: repeat({{VALUE}},1fr);',
                ],
            ] );

        $this->end_controls_section();
    }

    /* ---------------------------------------------------------------------
       Render
    --------------------------------------------------------------------- */
    protected function render() {

        $s = $this->get_settings_for_display();

        // 1) Title is always on its own row, outside the flex container
        if ( ! empty( $s['filter_title'] ) ) {
            echo '<h4 class="pd-filter-title">'
               . esc_html( $s['filter_title'] )
               . '</h4>';
        }

        // 2) Everything else lives inside the flex wrapper
        echo '<div class="pd-woo-products-filter">';
            echo '<form id="pd-products-filter-form" method="get">';

                // --- Category dropdown ---
                echo '<label class="pd-filter-label">';
                    echo '<span>' . esc_html__( 'Category:', 'pd-toc' ) . '</span>';
                    echo '<select name="filter_cat" class="pd-filter-select" style="width:100%;min-width:0;box-sizing:border-box;">';
                        echo '<option value="">' . esc_html__( '-- Any category --', 'pd-toc' ) . '</option>';

                        // only non-empty categories, and include parents whose children have products
                        $cat_terms = get_terms( [
                            'taxonomy'     => 'product_cat',
                            'hide_empty'   => true,
                            'pad_counts'   => true,
                            'hierarchical' => true,
                        ] );
                        foreach ( $cat_terms as $term ) {
                            printf(
                                '<option value="%1$s"%3$s>%2$s</option>',
                                esc_attr( $term->slug ),
                                esc_html( $term->name ),
                                selected( ( $_GET['filter_cat'] ?? '' ), $term->slug, false )
                            );
                        }

                    echo '</select>';
                echo '</label>';

                // --- One dropdown per product-attribute taxonomy ---
                foreach ( wc_get_attribute_taxonomies() as $tax_obj ) {
                    $tax_slug  = wc_attribute_taxonomy_name( $tax_obj->attribute_name );
                    $tax_label = wc_attribute_label( $tax_obj->attribute_label );

                    // only show terms actually used by products
                    $terms = get_terms( [
                        'taxonomy'   => $tax_slug,
                        'hide_empty' => true,
                    ] );
                    if ( empty( $terms ) ) {
                        continue;
                    }

                    echo '<label class="pd-filter-label">';
                        echo '<span>' . esc_html( $tax_label ) . ':</span>';
                        echo '<select name="' . esc_attr( $tax_slug ) . '" class="pd-filter-select" style="width:100%;min-width:0;box-sizing:border-box;">';
                            echo '<option value="">' . esc_html__( '-- Any --', 'pd-toc' ) . '</option>';
                            foreach ( $terms as $t ) {
                                printf(
                                    '<option value="%1$s"%3$s>%2$s</option>',
                                    esc_attr( $t->slug ),
                                    esc_html( $t->name ),
                                    selected( ( $_GET[ $tax_slug ] ?? '' ), $t->slug, false )
                                );
                            }
                        echo '</select>';
                    echo '</label>';
                }

                // --- Apply & Reset buttons ---
                echo '<button type="submit" class="pd-filter-button">'
                   . esc_html__( 'Apply', 'pd-toc' )
                   . '</button>';

                echo '<button type="button" id="pd-filter-reset" class="pd-filter-reset">'
                   . esc_html__( 'Reset', 'pd-toc' )
                   . '</button>';

            echo '</form>';
        echo '</div>';
    }
}
