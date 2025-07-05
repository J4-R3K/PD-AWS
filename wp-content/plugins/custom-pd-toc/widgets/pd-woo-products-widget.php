<?php
/**
 *  PD Woo Products – Elementor widget
 *  (complete version – includes attribute filtering and all controls)
 */
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) { exit; }

class PD_Woo_Products_Widget extends Widget_Base {

    /* ──────────────────── meta ──────────────────── */
    public function get_name()           { return 'pd_woo_products'; }
    public function get_title()          { return __( 'PD Woo Products', 'pd-toc' ); }
    public function get_icon()           { return 'eicon-products'; }
    public function get_categories()     { return [ 'general' ]; }
    public function get_script_depends() { return [ 'pd-products-ajax' ]; }

    /* ──────────────────── controls ──────────────────── */
    protected function register_controls() {

        /* ---------- Content ---------- */
        $this->start_controls_section( 'content_section', [
            'label' => __( 'Products Settings', 'pd-toc' ),
        ] );


        /* per-page */
        $this->add_control( 'posts_per_page', [
            'label'   => __( 'Number of Products', 'pd-toc' ),
            'type'    => Controls_Manager::NUMBER,
            'default' => 12,
            'min'     => 1,
        ] );

        /* image width */
        $this->add_control( 'image_size', [
            'label'   => __( 'Image Size (%)', 'pd-toc' ),
            'type'    => Controls_Manager::SLIDER,
            'range'   => [ '%' => [ 'min' => 40, 'max' => 100 ] ],
            'default' => [ 'size' => 100, 'unit' => '%' ],
            'selectors' => [
                '{{WRAPPER}} .pd-woo-products-grid .product img' => 'width: {{SIZE}}{{UNIT}};',
            ],
        ] );

        /* ---------- Exclusions ---------- */
        /* products */
        $products = [];
        foreach ( get_posts( [ 'post_type' => 'product', 'numberposts' => -1, 'fields' => [ 'ID', 'post_title' ] ] ) as $p ) {
            $products[ $p->ID ] = $p->post_title;
        }
        $this->add_control( 'exclude_products', [
            'label'       => __( 'Exclude Products', 'pd-toc' ),
            'type'        => Controls_Manager::SELECT2,
            'options'     => $products,
            'multiple'    => true,
            'description' => __( 'Choose individual products to hide.', 'pd-toc' ),
        ] );

        /* categories */
        $cats = [];
        foreach ( get_terms( [ 'taxonomy' => 'product_cat', 'hide_empty' => false ] ) as $cat ) {
            $cats[ $cat->term_id ] = $cat->name;
        }
        $this->add_control( 'exclude_categories', [
            'label'       => __( 'Exclude Categories', 'pd-toc' ),
            'type'        => Controls_Manager::SELECT2,
            'options'     => $cats,
            'multiple'    => true,
            'description' => __( 'Hide products in selected categories.', 'pd-toc' ),
        ] );

        $this->end_controls_section(); /* /content */

        /* ---------- Style ---------- */
        $this->start_controls_section( 'style_section', [
            'label' => __( 'Style Options', 'pd-toc' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        /* gap */
        $this->add_control( 'column_gap', [
            'label'   => __( 'Gap Between Products', 'pd-toc' ),
            'type'    => Controls_Manager::SLIDER,
            'range'   => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
            'default' => [ 'size' => 20, 'unit' => 'px' ],
            'selectors' => [
                '{{WRAPPER}} .pd-woo-products-grid' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ] );

        /* padding */
        $this->add_responsive_control( 'padding', [
            'label' => __( 'Card Padding', 'pd-toc' ),
            'type'  => Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .pd-woo-products-grid .product' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        /* Title colour/typo */
        $this->add_control( 'title_color', [
            'label'     => __( 'Title Color', 'pd-toc' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pd-woo-products-grid .woocommerce-loop-product__title' => 'color: {{VALUE}};',
            ],
        ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'title_typography',
            'selector' => '{{WRAPPER}} .pd-woo-products-grid .woocommerce-loop-product__title',
        ] );

        /* Price colour/typo */
        $this->add_control( 'price_color', [
            'label'     => __( 'Price Color', 'pd-toc' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pd-woo-products-grid .product .price' => 'color: {{VALUE}};',
            ],
        ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'price_typography',
            'selector' => '{{WRAPPER}} .pd-woo-products-grid .product .price',
        ] );

        /* VAT label colour */
        $this->add_control( 'vat_label_color', [
            'label'     => __( 'VAT Label Color', 'pd-toc' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pd-woo-products-grid .product .vat-label' => 'color: {{VALUE}};',
            ],
        ] );

        /* ---- Button normal / hover ---- */
        $this->start_controls_tabs( 'button_style_tabs' );

            /* normal */
            $this->start_controls_tab( 'button_tab_normal', [ 'label' => __( 'Normal', 'pd-toc' ) ] );
            $this->add_control( 'button_text', [
                'label'     => __( 'Text Color', 'pd-toc' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pd-woo-products-grid .product .button' => 'color: {{VALUE}};',
                ],
            ] );
            $this->add_control( 'button_bg', [
                'label'     => __( 'Background', 'pd-toc' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pd-woo-products-grid .product .button' => 'background-color: {{VALUE}};',
                ],
            ] );
            $this->end_controls_tab();

            /* hover */
            $this->start_controls_tab( 'button_tab_hover', [ 'label' => __( 'Hover', 'pd-toc' ) ] );
            $this->add_control( 'button_hover_text', [
                'label'     => __( 'Hover Text Color', 'pd-toc' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pd-woo-products-grid .product .button:hover' => 'color: {{VALUE}};',
                ],
            ] );
            $this->add_control( 'button_hover_bg', [
                'label'     => __( 'Hover Background', 'pd-toc' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pd-woo-products-grid .product .button:hover' => 'background-color: {{VALUE}};',
                ],
            ] );
            $this->end_controls_tab();

        $this->end_controls_tabs();

        /* button radius / typography / padding */
        $this->add_control( 'button_radius', [
            'label' => __( 'Button Border Radius', 'pd-toc' ),
            'type'  => Controls_Manager::SLIDER,
            'range' => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
            'selectors' => [
                '{{WRAPPER}} .pd-woo-products-grid .product .button' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ] );
        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'button_typography',
            'selector' => '{{WRAPPER}} .pd-woo-products-grid .product .button',
        ] );
        $this->add_responsive_control( 'button_padding', [
            'label' => __( 'Button Padding', 'pd-toc' ),
            'type'  => Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .pd-woo-products-grid .product .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );
		
		/* Mobile / Tablet column override */
		$this->add_responsive_control( 'columns_responsive', [
			'label'     => __( 'Columns (Mobile / Tablet)', 'pd-toc' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => [ '1' => '1', '2' => '2', '3' => '3', '4' => '4' ],
			'default'   => '1',   // phones start at 1 column
			'selectors' => [
				'{{WRAPPER}} .pd-woo-products-grid' =>
					'grid-template-columns: repeat({{VALUE}},1fr);',
			],
		] );

        $this->end_controls_section(); /* /style */
    }

    /* ──────────────────── render ──────────────────── */
    protected function render() {

        $s       = $this->get_settings_for_display();
        $columns = (int) ( $s['columns']        ?? 3  );
        $ppp     = (int) ( $s['posts_per_page'] ?? 12 );
        $paged   = max( 1, get_query_var( 'paged' ) );

        /* base query */
        $args = [
            'post_type'      => 'product',
            'posts_per_page' => $ppp,
            'paged'          => $paged,
        ];

        /* category filter */
        if ( ! empty( $_GET['filter_cat'] ) ) {
            $args['tax_query'][] = [
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $_GET['filter_cat'] ),
            ];
        }

        /* attribute filters */
        foreach ( wc_get_attribute_taxonomies() as $tax ) {
            $tax_name = wc_attribute_taxonomy_name( $tax->attribute_name ); // e.g. pa_colour
            if ( ! empty( $_GET[ $tax_name ] ) ) {
                $args['tax_query'][] = [
                    'taxonomy' => $tax_name,
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field( $_GET[ $tax_name ] ),
                ];
            }
        }

        /* exclusions */
        if ( ! empty( $s['exclude_products'] ) ) {
            $args['post__not_in'] = array_map( 'intval', $s['exclude_products'] );
        }
        if ( ! empty( $s['exclude_categories'] ) ) {
            $args['tax_query'][] = [
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => array_map( 'intval', $s['exclude_categories'] ),
                'operator' => 'NOT IN',
            ];
        }

        $q = new \WP_Query( $args );

        if ( $q->have_posts() ) {

            echo '<div class="pd-woo-products-wrapper"'
               . ' data-count="'   . esc_attr( $ppp     ) . '"'
               . ' data-cat="'     . ( $_GET['filter_cat'] ?? '' ) . '"'
               . ' data-columns="' . esc_attr( $columns ) . '">';

            printf( '<ul class="pd-woo-products-grid products columns-%1$d">', $columns );

            while ( $q->have_posts() ) {
                $q->the_post();
                wc_get_template_part( 'content', 'product' );
            }
            echo '</ul>';

            /* pagination */
            $total = $q->max_num_pages;
            if ( $total > 1 ) {
                echo '<nav class="pd-pagination-wrapper"><div class="pd-pagination">';
                for ( $i = 1; $i <= $total; $i++ ) {
                    printf(
                        '<a href="#" class="pd-ajax-page%s" data-page="%2$d">%2$d</a>',
                        ( $i === $paged ) ? ' current' : '',
                        $i
                    );
                }
                echo '</div></nav>';
            }
            echo '</div>';
        }
        wp_reset_postdata();
    }
}
