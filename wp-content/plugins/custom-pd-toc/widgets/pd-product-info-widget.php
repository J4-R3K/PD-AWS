<?php
/**
 * PD Product Information Widget for Revit Products
 * Displays comprehensive product information for blog post headers
 */
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) { exit; }

class PD_Product_Info_Widget extends Widget_Base {

    public function get_name() { return 'pd_product_info'; }
    public function get_title() { return __( 'PD Product Information', 'pd-toc' ); }
    public function get_icon() { return 'eicon-info-box'; }
    public function get_categories() { return [ 'general' ]; }

    protected function register_controls() {

        // ==================== CONTENT TAB ====================
        $this->start_controls_section('content_section', [
            'label' => __( 'Product Information', 'pd-toc' ),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        // === Core Product Information ===
        $this->add_control('product_heading', [
            'label' => __( 'Core Product Information', 'pd-toc' ),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        // Product Selection with URL
        $this->add_control('product_selection', [
            'label' => __( 'Select Product', 'pd-toc' ),
            'type' => Controls_Manager::SELECT,
            'options' => $this->get_woocommerce_products(),
            'default' => '',
            'description' => __( 'Select a WooCommerce product to auto-fill information', 'pd-toc' ),
        ]);

        // Manual product name (fallback)
        $this->add_control('product_name_manual', [
            'label' => __( 'Product Name (Manual)', 'pd-toc' ),
            'type' => Controls_Manager::TEXT,
            'placeholder' => __( 'Enter product name if not using WooCommerce product', 'pd-toc' ),
            'condition' => ['product_selection' => ''],
        ]);

        // Product URL (manual)
        $this->add_control('product_url', [
            'label' => __( 'Product URL', 'pd-toc' ),
            'type' => Controls_Manager::URL,
            'placeholder' => __( 'https://your-site.com/product-page', 'pd-toc' ),
            'condition' => ['product_selection' => ''],
        ]);

        // Version (manual entry)
        $this->add_control('product_version', [
            'label' => __( 'Version', 'pd-toc' ),
            'type' => Controls_Manager::TEXT,
            'default' => '1.0',
            'placeholder' => '2.1.0',
        ]);

        // Product Type
        $this->add_control('product_type', [
            'label' => __( 'Product Type', 'pd-toc' ),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'template' => __( 'Template', 'pd-toc' ),
                'family' => __( 'Family', 'pd-toc' ),
                'extension' => __( 'Extension', 'pd-toc' ),
                'plugin' => __( 'Plugin', 'pd-toc' ),
                'toolkit' => __( 'Toolkit', 'pd-toc' ),
                'other' => __( 'Other', 'pd-toc' ),
            ],
            'default' => 'template',
        ]);

        // Release Date (auto from product or manual)
        $this->add_control('release_date', [
            'label' => __( 'Release Date', 'pd-toc' ),
            'type' => Controls_Manager::DATE_TIME,
            'picker_options' => ['dateFormat' => 'Y-m-d'],
            'description' => __( 'Will auto-populate from selected product if available', 'pd-toc' ),
        ]);

        // Update Date (auto from product or manual)
        $this->add_control('update_date', [
            'label' => __( 'Update Date', 'pd-toc' ),
            'type' => Controls_Manager::DATE_TIME,
            'picker_options' => ['dateFormat' => 'Y-m-d'],
            'description' => __( 'Will auto-populate from selected product if available', 'pd-toc' ),
        ]);

        $this->end_controls_section();

        // === User Context ===
        $this->start_controls_section('user_context_section', [
            'label' => __( 'User Context', 'pd-toc' ),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        // Target Discipline (default pre-filled)
        $this->add_control('target_discipline', [
            'label' => __( 'Target Discipline', 'pd-toc' ),
            'type' => Controls_Manager::SELECT2,
            'multiple' => true,
            'options' => [
                'electrical_engineers' => __( 'Electrical Engineers', 'pd-toc' ),
                'bim_managers' => __( 'BIM Managers', 'pd-toc' ),
                'electrical_designers' => __( 'Electrical Designers', 'pd-toc' ),
                'architects' => __( 'Architects', 'pd-toc' ),
                'mep_engineers' => __( 'MEP Engineers', 'pd-toc' ),
            ],
            'default' => ['electrical_engineers', 'bim_managers', 'electrical_designers'],
        ]);

        // Prerequisites
        $this->add_control('prerequisites', [
            'label' => __( 'Prerequisites', 'pd-toc' ),
            'type' => Controls_Manager::TEXTAREA,
            'default' => 'Basic Revit knowledge, electrical design fundamentals',
            'placeholder' => 'List required knowledge and skills',
        ]);

        // Setup Time
        $this->add_control('setup_time', [
            'label' => __( 'Setup Time', 'pd-toc' ),
            'type' => Controls_Manager::TEXTAREA,
            'default' => '5-10 minutes',
            'placeholder' => 'Estimated time to set up and use',
        ]);

        // Revit Skill Level
        $this->add_control('revit_skill_level', [
            'label' => __( 'Revit Skill Level', 'pd-toc' ),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'beginner' => __( 'Beginner', 'pd-toc' ),
                'intermediate' => __( 'Intermediate', 'pd-toc' ),
                'advanced' => __( 'Advanced', 'pd-toc' ),
            ],
            'default' => 'intermediate',
        ]);

        $this->end_controls_section();

        // === Technical Context ===
        $this->start_controls_section('technical_section', [
            'label' => __( 'Technical Context', 'pd-toc' ),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        // Dependencies (repeatable dropdown with memory)
        $this->add_control('dependencies', [
            'label' => __( 'Required Dependencies', 'pd-toc' ),
            'type' => Controls_Manager::REPEATER,
            'fields' => [
                [
                    'name' => 'dependency_type',
                    'label' => __( 'Dependency Type', 'pd-toc' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'pyrevit' => 'pyRevit',
                        'pdtool' => 'PDtool.extension',
                        'plugin' => 'Specific Plugin',
                        'revit_version' => 'Revit Version',
                        'other' => 'Other',
                    ],
                    'default' => 'pyrevit',
                ],
                [
                    'name' => 'dependency_version',
                    'label' => __( 'Version/Details', 'pd-toc' ),
                    'type' => Controls_Manager::TEXT,
                    'placeholder' => 'Version number or details',
                ],
            ],
            'default' => [
                ['dependency_type' => 'pyrevit', 'dependency_version' => '4.8+'],
                ['dependency_type' => 'pdtool', 'dependency_version' => 'Latest'],
            ],
            'title_field' => '{{{ dependency_type }}} - {{{ dependency_version }}}',
        ]);

        // Compatibility Notes
        $this->add_control('compatibility_notes', [
            'label' => __( 'Compatibility Notes', 'pd-toc' ),
            'type' => Controls_Manager::TEXTAREA,
            'placeholder' => 'Known issues with specific Revit versions or configurations',
        ]);

        // Regional Variants
        $this->add_control('regional_variants', [
            'label' => __( 'Regional Variants', 'pd-toc' ),
            'type' => Controls_Manager::SELECT2,
            'multiple' => true,
            'options' => [
                'uk_standards' => __( 'UK Standards', 'pd-toc' ),
                'polish_standards' => __( 'Polish Standards', 'pd-toc' ),
                'eu_standards' => __( 'EU Standards', 'pd-toc' ),
                'us_standards' => __( 'US Standards', 'pd-toc' ),
                'international' => __( 'International', 'pd-toc' ),
            ],
            'default' => ['uk_standards'],
        ]);

        $this->end_controls_section();

        // === Documentation & Support ===
        $this->start_controls_section('support_section', [
            'label' => __( 'Documentation & Support', 'pd-toc' ),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        // User Guide Status
        $this->add_control('user_guide_status', [
            'label' => __( 'User Guide Status', 'pd-toc' ),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'complete' => __( 'Complete', 'pd-toc' ),
                'partial' => __( 'Partial', 'pd-toc' ),
                'under_review' => __( 'Under Review', 'pd-toc' ),
                'none' => __( 'Not Available', 'pd-toc' ),
            ],
            'default' => 'complete',
        ]);

        // Video Tutorial
        $this->add_control('video_tutorial', [
            'label' => __( 'Video Tutorial Available', 'pd-toc' ),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'no',
        ]);

        $this->add_control('video_tutorial_url', [
            'label' => __( 'Video Tutorial URL', 'pd-toc' ),
            'type' => Controls_Manager::URL,
            'condition' => ['video_tutorial' => 'yes'],
        ]);

        // Support Method
        $this->add_control('support_method', [
            'label' => __( 'Support Method', 'pd-toc' ),
            'type' => Controls_Manager::SELECT2,
            'multiple' => true,
            'options' => [
                'email' => __( 'Email', 'pd-toc' ),
                'blog_forum' => __( 'Blog Forum', 'pd-toc' ),
                'documentation' => __( 'Documentation', 'pd-toc' ),
                'video_guides' => __( 'Video Guides', 'pd-toc' ),
            ],
            'default' => ['email', 'blog_forum'],
        ]);

        // FAQ Link
        $this->add_control('faq_link', [
            'label' => __( 'FAQ Link', 'pd-toc' ),
            'type' => Controls_Manager::URL,
            'placeholder' => 'https://projectdesign.io/faq',
        ]);

        $this->end_controls_section();

        // === Quality Assurance ===
        $this->start_controls_section('quality_section', [
            'label' => __( 'Quality Assurance', 'pd-toc' ),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        // Testing Status
        $this->add_control('testing_status', [
            'label' => __( 'Last Tested With', 'pd-toc' ),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'Revit 2024',
            'description' => 'Which Revit version was this last tested with',
        ]);

        // QA Compliance
        $this->add_control('qa_compliance', [
            'label' => __( 'QA Compliance', 'pd-toc' ),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'label_on' => __( 'Passes', 'pd-toc' ),
            'label_off' => __( 'Pending', 'pd-toc' ),
        ]);

        // User Feedback Score (auto from WooCommerce reviews)
        $this->add_control('user_feedback_score', [
            'label' => __( 'User Feedback Score', 'pd-toc' ),
            'type' => Controls_Manager::NUMBER,
            'min' => 0,
            'max' => 5,
            'step' => 0.1,
            'description' => 'Will auto-populate from WooCommerce reviews if product is selected',
        ]);

        $this->end_controls_section();

        // ==================== STYLE TAB ====================
        $this->start_controls_section('style_container', [
            'label' => __( 'Container Style', 'pd-toc' ),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        // Layout Style
        $this->add_control('layout_style', [
            'label' => __( 'Layout Style', 'pd-toc' ),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'minimal_card' => __( 'Minimal Card (Recommended)', 'pd-toc' ),
                'full_expanded' => __( 'Always Expanded', 'pd-toc' ),
                'accordion' => __( 'Accordion Style', 'pd-toc' ),
            ],
            'default' => 'minimal_card',
        ]);

        // Card styling controls
        $this->add_group_control(Group_Control_Background::get_type(), [
            'name' => 'container_background',
            'types' => ['classic', 'gradient'],
            'selector' => '{{WRAPPER}} .pd-product-info-container',
        ]);

        $this->add_group_control(Group_Control_Border::get_type(), [
            'name' => 'container_border',
            'selector' => '{{WRAPPER}} .pd-product-info-container',
        ]);

        $this->add_control('container_border_radius', [
            'label' => __( 'Border Radius', 'pd-toc' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .pd-product-info-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('container_padding', [
            'label' => __( 'Padding', 'pd-toc' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .pd-product-info-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        // Typography styles
        $this->start_controls_section('style_typography', [
            'label' => __( 'Typography', 'pd-toc' ),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'product_name_typography',
            'label' => __( 'Product Name', 'pd-toc' ),
            'selector' => '{{WRAPPER}} .pd-product-name',
        ]);

        $this->add_control('product_name_color', [
            'label' => __( 'Product Name Color', 'pd-toc' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .pd-product-name' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'content_typography',
            'label' => __( 'Content Text', 'pd-toc' ),
            'selector' => '{{WRAPPER}} .pd-product-info-content',
        ]);

        $this->end_controls_section();
    }

    /**
     * Get WooCommerce products for dropdown
     */
    private function get_woocommerce_products() {
        $products = [];

        if (!class_exists('WooCommerce')) {
            return $products;
        }

        $args = [
            'post_type' => 'product',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ];

        $query = new \WP_Query($args);

        if ($query->have_posts()) {
            $products[''] = __( 'Select a product...', 'pd-toc' );
            while ($query->have_posts()) {
                $query->the_post();
                $products[get_the_ID()] = get_the_title();
            }
            wp_reset_postdata();
        }

        return $products;
    }

    /**
     * Get product data from WooCommerce
     * FIXED: Now properly extracts attribute values
     */
    private function get_product_data($product_id) {
        if (!$product_id || !class_exists('WooCommerce')) {
            return [];
        }

        $product = wc_get_product($product_id);
        if (!$product) {
            return [];
        }

        // Enhanced attribute processing
        $attributes = [];
        $product_attributes = $product->get_attributes();

        foreach ($product_attributes as $attribute) {
            if (is_object($attribute)) {
                $attr_data = $attribute->get_data();
                $attribute_name = $attr_data['name'];

                // Clean up attribute name for display
                $display_name = ucfirst(str_replace(['_', '-', 'pa_'], ' ', $attribute_name));

                // Handle taxonomy-based attributes (global attributes)
                if ($attribute->is_taxonomy()) {
                    // Get attribute values using WooCommerce function
                    $attribute_values = wc_get_product_terms($product_id, $attribute_name, array('fields' => 'names'));
                    if (!empty($attribute_values) && !is_wp_error($attribute_values)) {
                        $attributes[$display_name] = implode(', ', $attribute_values);
                    }
                } else {
                    // Handle custom attributes (product-specific)
                    $attribute_value = $attr_data['value'];
                    if (!empty($attribute_value)) {
                        $attributes[$display_name] = $attribute_value;
                    }
                }
            }
        }

        return [
            'name' => $product->get_name(),
            'url' => get_permalink($product_id),
            'date_created' => $product->get_date_created(),
            'date_modified' => $product->get_date_modified(),
            'attributes' => $attributes, // Use processed attributes
            'average_rating' => $product->get_average_rating(),
            'meta_data' => $product->get_meta_data(),
        ];
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        // Enhanced inline CSS to prevent WordPress theme conflicts
        echo '<style>
        .pd-product-info-container .pd-expand-toggle {
            background: #f8f9fa !important;
            border: 1px solid #bdc3c7 !important;
            border-radius: 6px !important;
            padding: 8px 16px !important;
            font-size: 0.9em !important;
            color: #555 !important;
            cursor: pointer !important;
            margin-top: 15px !important;
            width: 100% !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 8px !important;
            transition: all 0.3s ease !important;
            box-sizing: border-box !important;
            font-family: inherit !important;
            text-decoration: none !important;
            outline: none !important;
            font-weight: normal !important;
            text-transform: none !important;
            text-shadow: none !important;
            box-shadow: none !important;
            background-image: none !important;
            border-style: solid !important;
            border-width: 1px !important;
            line-height: normal !important;
            min-height: auto !important;
            height: auto !important;
        }
        .pd-product-info-container .pd-expand-toggle:hover {
            background: #ecf0f1 !important;
            border-color: #95a5a6 !important;
            color: #555 !important;
        }
        .pd-product-info-container .pd-expand-toggle:focus {
            outline: none !important;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2) !important;
        }
        .pd-product-info-container .pd-expand-toggle:active {
            background: #e8f4f8 !important;
            border-color: #3498db !important;
        }
        </style>';

        // Get product data if WooCommerce product is selected
        $product_data = [];
        if (!empty($settings['product_selection'])) {
            $product_data = $this->get_product_data($settings['product_selection']);
        }

        // Determine product name and URL
        $product_name = !empty($product_data['name']) ? $product_data['name'] : $settings['product_name_manual'];
        $product_url = !empty($product_data['url']) ? $product_data['url'] : $settings['product_url']['url'];

        // Auto-populate dates if available
        $release_date = $settings['release_date'] ?: (!empty($product_data['date_created']) ? $product_data['date_created']->date('Y-m-d') : '');
        $update_date = $settings['update_date'] ?: (!empty($product_data['date_modified']) ? $product_data['date_modified']->date('Y-m-d') : '');

        // Auto-populate attributes if available - FIXED
        $attributes = [];
        if (!empty($product_data['attributes'])) {
            $attributes = $product_data['attributes']; // Use the properly processed attributes
        }

        // Auto-populate user feedback score
        $feedback_score = $settings['user_feedback_score'] ?: (!empty($product_data['average_rating']) ? $product_data['average_rating'] : 0);

        ?>
        <div class="pd-product-info-container" data-layout="<?php echo esc_attr($settings['layout_style']); ?>">

            <!-- Primary Zone: Always Visible -->
            <div class="pd-product-info-primary">
                <div class="pd-primary-row">
                    <div class="pd-primary-left">
                        <?php if ($product_name): ?>
                            <h3 class="pd-product-name">
                                <?php if ($product_url): ?>
                                    <a href="<?php echo esc_url($product_url); ?>" target="_blank" rel="noopener">
                                        <?php echo esc_html($product_name); ?>
                                    </a>
                                <?php else: ?>
                                    <?php echo esc_html($product_name); ?>
                                <?php endif; ?>
                            </h3>
                        <?php endif; ?>

                        <div class="pd-product-meta">
                            <?php if ($settings['product_version']): ?>
                                <span class="pd-version">v<?php echo esc_html($settings['product_version']); ?></span>
                            <?php endif; ?>

                            <?php if ($settings['product_type']): ?>
                                <span class="pd-type"><?php echo esc_html(ucfirst($settings['product_type'])); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="pd-primary-right">
                        <?php if ($settings['target_discipline']): ?>
                            <div class="pd-discipline">
                                <span class="pd-label"><?php _e('For:', 'pd-toc'); ?></span>
                                <?php echo esc_html(implode(', ', array_map('ucwords', str_replace('_', ' ', $settings['target_discipline'])))); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($settings['setup_time']): ?>
                            <div class="pd-setup-time">
                                <span class="pd-label"><?php _e('Setup:', 'pd-toc'); ?></span>
                                <?php echo esc_html($settings['setup_time']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($settings['layout_style'] === 'minimal_card'): ?>
                    <button class="pd-expand-toggle" type="button" data-product-widget="toggle">
                        <span class="pd-toggle-text"><?php _e('View Details', 'pd-toc'); ?></span>
                        <i class="pd-toggle-icon fas fa-chevron-down"></i>
                    </button>
                <?php endif; ?>
            </div>

            <!-- Secondary & Tertiary Zones: Expandable Content -->
            <div class="pd-product-info-expandable" <?php echo $settings['layout_style'] === 'minimal_card' ? 'style="display: none;"' : ''; ?>>

                <!-- Technical Details -->
                <div class="pd-section pd-technical">
                    <h4 class="pd-section-title"><?php _e('Technical Details', 'pd-toc'); ?></h4>
                    <div class="pd-section-content">
                        <div class="pd-detail-grid">
                            <?php if ($release_date): ?>
                                <div class="pd-detail-item">
                                    <span class="pd-detail-label"><?php _e('Released:', 'pd-toc'); ?></span>
                                    <span class="pd-detail-value"><?php echo esc_html(date('M j, Y', strtotime($release_date))); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($update_date): ?>
                                <div class="pd-detail-item">
                                    <span class="pd-detail-label"><?php _e('Updated:', 'pd-toc'); ?></span>
                                    <span class="pd-detail-value"><?php echo esc_html(date('M j, Y', strtotime($update_date))); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($settings['testing_status']): ?>
                                <div class="pd-detail-item">
                                    <span class="pd-detail-label"><?php _e('Tested with:', 'pd-toc'); ?></span>
                                    <span class="pd-detail-value"><?php echo esc_html($settings['testing_status']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Dependencies -->
                        <?php if (!empty($settings['dependencies'])): ?>
                            <div class="pd-dependencies">
                                <span class="pd-label"><?php _e('Dependencies:', 'pd-toc'); ?></span>
                                <div class="pd-dependency-list">
                                    <?php foreach ($settings['dependencies'] as $dep): ?>
                                        <span class="pd-dependency-item">
                                            <?php echo esc_html(ucfirst(str_replace('_', ' ', $dep['dependency_type']))); ?>
                                            <?php if ($dep['dependency_version']): ?>
                                                <small><?php echo esc_html($dep['dependency_version']); ?></small>
                                            <?php endif; ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Product Attributes (from WooCommerce) - FIXED -->
                        <?php if (!empty($attributes)): ?>
                            <div class="pd-attributes">
                                <span class="pd-label"><?php _e('Specifications:', 'pd-toc'); ?></span>
                                <div class="pd-attribute-grid">
                                    <?php foreach ($attributes as $name => $value): ?>
                                        <div class="pd-attribute-item">
                                            <span class="pd-attr-name"><?php echo esc_html($name); ?>:</span>
                                            <span class="pd-attr-value"><?php echo esc_html($value); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Support & Documentation -->
                <div class="pd-section pd-support">
                    <h4 class="pd-section-title"><?php _e('Support & Documentation', 'pd-toc'); ?></h4>
                    <div class="pd-section-content">
                        <div class="pd-support-items">
                            <div class="pd-support-item">
                                <span class="pd-support-label"><?php _e('User Guide:', 'pd-toc'); ?></span>
                                <span class="pd-status pd-status-<?php echo esc_attr($settings['user_guide_status']); ?>">
                                    <?php echo esc_html(ucfirst(str_replace('_', ' ', $settings['user_guide_status']))); ?>
                                </span>
                            </div>

                            <?php if ($settings['video_tutorial'] === 'yes' && !empty($settings['video_tutorial_url']['url'])): ?>
                                <div class="pd-support-item">
                                    <span class="pd-support-label"><?php _e('Video Tutorial:', 'pd-toc'); ?></span>
                                    <a href="<?php echo esc_url($settings['video_tutorial_url']['url']); ?>" class="pd-video-link" target="_blank">
                                        <i class="fas fa-play-circle"></i> <?php _e('Watch', 'pd-toc'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($settings['faq_link']['url'])): ?>
                                <div class="pd-support-item">
                                    <span class="pd-support-label"><?php _e('FAQ:', 'pd-toc'); ?></span>
                                    <a href="<?php echo esc_url($settings['faq_link']['url']); ?>" class="pd-faq-link" target="_blank">
                                        <?php _e('Common Issues', 'pd-toc'); ?> <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Quality Metrics -->
                <?php if ($feedback_score > 0 || $settings['qa_compliance'] === 'yes'): ?>
                    <div class="pd-section pd-quality">
                        <h4 class="pd-section-title"><?php _e('Quality Metrics', 'pd-toc'); ?></h4>
                        <div class="pd-section-content">
                            <div class="pd-quality-items">
                                <?php if ($feedback_score > 0): ?>
                                    <div class="pd-quality-item">
                                        <span class="pd-quality-label"><?php _e('User Rating:', 'pd-toc'); ?></span>
                                        <div class="pd-rating">
                                            <span class="pd-rating-value"><?php echo esc_html($feedback_score); ?></span>
                                            <div class="pd-stars">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star <?php echo $i <= $feedback_score ? 'filled' : 'empty'; ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($settings['qa_compliance'] === 'yes'): ?>
                                    <div class="pd-quality-item">
                                        <span class="pd-quality-label"><?php _e('QA Status:', 'pd-toc'); ?></span>
                                        <span class="pd-qa-badge pd-qa-pass">
                                            <i class="fas fa-check-circle"></i> <?php _e('Verified', 'pd-toc'); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}
