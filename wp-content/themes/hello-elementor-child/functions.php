<?php
//Trigger an error for testing debug.log
//trigger_error("Test error for debug.log", E_USER_NOTICE);

// // Load parent and child theme stylesheets and scripts
add_action('wp_enqueue_scripts', 'hello_elementor_child_enqueue_assets');

function hello_elementor_child_enqueue_assets() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style', get_stylesheet_uri(), array('parent-style'), wp_get_theme()->get('Version'));
    wp_enqueue_script('jquery');
    wp_enqueue_script('wp-util');
    wp_enqueue_script('child-custom-js', get_stylesheet_directory_uri() . '/custom.js', array('jquery'), null, true);
}

// Redirect non-logged-in users from specific pages to the account page
add_action('template_redirect', 'custom_login_registration_redirect');

function custom_login_registration_redirect() {
    if (is_user_logged_in()) {
        return; // Exit if user is logged in
    }

    $page_slug = 'account'; // Your WooCommerce account page slug for redirection
    $uri = $_SERVER['REQUEST_URI'];
    $login_url_pattern = '/wp-login.php';
    $register_url_pattern = '/wp-login.php?action=register';

    // Redirect if current URI matches login or register patterns
    if (strpos($uri, $login_url_pattern) !== false || strpos($uri, $register_url_pattern) !== false) {
        $redirect_url = home_url($page_slug); // Fetch the redirection URL
        if ($redirect_url === false) {
            error_log('Home URL is not set correctly.');  // Log error for debugging
        } else {
            wp_redirect($redirect_url);
            exit;
        }
    }
}
 
// Redirect non-logged-in users attempting to access login/register to the account page
add_action('login_init', function() {
    $excluded_actions = ['logout', 'lostpassword', 'retrievepassword', 'resetpass', 'rp'];
    if (isset($_GET['action']) && in_array($_GET['action'], $excluded_actions, true)) {
        return; // Exit if action is in the excluded list
    }

    // Redirect to the custom login/registration page
    wp_redirect(home_url('/account/'));
    exit;
});

/**
 * Custom code for handling WooCommerce pre-orders to become purchasable when available
 */
add_filter('woocommerce_pre_orders_product_is_purchasable', '__return_true');

/**
 * Replace the home link URL in WooCommerce breadcrumbs
 */
add_filter('woocommerce_breadcrumb_home_url', 'woo_custom_breadcrumb_home_url');
function woo_custom_breadcrumb_home_url() {
    return 'https://projectdesign.io/shop/';
}

/**
 * Customize the sale badge text in WooCommerce
 */
add_filter('woocommerce_sale_flash', 'edit_sale_badge');
function edit_sale_badge() {
    return '<span class="onsale">Limited Offer</span>';
}

/**
 * Rename "home" in WooCommerce breadcrumbs
 */
add_filter('woocommerce_breadcrumb_defaults', 'wcc_change_breadcrumb_home_text');
function wcc_change_breadcrumb_home_text($defaults) {
    // Change the breadcrumb home text from 'Home' to 'Shop'
    $defaults['home'] = 'Shop';
    return $defaults;
}

// Add a custom endpoint for Kickbox email validation
add_action('rest_api_init', function () {
    register_rest_route('kickbox/v1', '/validate', array(
        'methods' => 'GET',
        'callback' => 'kickbox_email_validation',
        'permission_callback' => '__return_true',
    ));
});

function kickbox_email_validation(WP_REST_Request $request) {
    $email = $request->get_param('email');
    if (empty($email)) {
        return new WP_Error('invalid_email', 'Email parameter is required', array('status' => 400));
    }

    $api_key = KICKBOX_API_KEY; // Use the securely stored API key
    $url = "https://api.kickbox.com/v2/verify?email=" . urlencode($email) . "&apikey=" . $api_key;

    $response = wp_remote_get($url);
    if (is_wp_error($response)) {
        return new WP_Error('api_error', $response->get_error_message(), array('status' => 500));
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    return rest_ensure_response($data);
}

//automatically reject undeliverable emails before order processing.
add_action('woocommerce_checkout_process', 'validate_email_with_kickbox');

function validate_email_with_kickbox() {
    if (!defined('KICKBOX_API_KEY')) return;

    $email = $_POST['billing_email'];
    $response = wp_remote_get("https://api.kickbox.com/v2/verify?email=$email&apikey=" . KICKBOX_API_KEY);

    if (is_wp_error($response)) return;

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if ($body['result'] !== 'deliverable') {
        wc_add_notice(__('Invalid email address. Please use a real, working email.'), 'error');
    }
}


//Add tag to user who purchased ProDesign
add_action('woocommerce_order_status_completed', 'add_custom_user_meta_on_purchase');
function add_custom_user_meta_on_purchase($order_id) {
    if (!$order_id) return;

    $order = wc_get_order($order_id);
    $user_id = $order->get_user_id();
    $product_id_to_check = 9419; // The product ID

    foreach ($order->get_items() as $item) {
        $product_id = $item->get_product_id();
        if ($product_id == $product_id_to_check) {
            update_user_meta($user_id, '_purchased_product_9419', true); // Store a flag in user meta
            break;
        }
    }
}


/*
//Temp function to update existing customers
function update_existing_customers_with_meta() {
    $product_id_to_check = 9419;
    $orders = wc_get_orders(array(
        'status' => 'completed',
        'limit' => -1, // Get all orders
    ));

    $updated_users = array();

    foreach ($orders as $order) {
        $user_id = $order->get_user_id();
        if (!$user_id) continue;

        foreach ($order->get_items() as $item) {
            $product_id = $item->get_product_id();
            if ($product_id == $product_id_to_check) {
                update_user_meta($user_id, '_purchased_product_9419', true);
                $updated_users[] = $user_id;
                break;
            }
        }
    }

    if (!empty($updated_users)) {
        add_action('admin_notices', function() use ($updated_users) {
            echo '<div class="notice notice-success"><p>Updated user meta for users: ' . implode(', ', $updated_users) . '</p></div>';
        });
    }
}

add_action('init', 'update_existing_customers_with_meta');
function print_user_meta_for_debugging() {
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $meta_value = get_user_meta($current_user->ID, '_purchased_product_9419', true);
        echo '<div class="notice notice-info"><p>User Meta _purchased_product_9419: ' . esc_html($meta_value) . '</p></div>';
    }
}
add_action('wp_footer', 'print_user_meta_for_debugging');
*/

function add_vat_to_order_email( $total_rows, $order, $tax_display ) {
    $vat_total = $order->get_total_tax(); // Use WooCommerce's built-in method to get the total tax (VAT)

    // Only add VAT row if there's tax to show
    if ( $vat_total > 0 ) {
        $total_rows['vat_total'] = array(
            'label' => __( 'VAT (Tax)', 'woocommerce' ),
            'value' => wc_price( $vat_total ),
        );
    }

    return $total_rows;
}
add_filter( 'woocommerce_get_order_item_totals', 'add_vat_to_order_email', 10, 3 );

// ✅ Automatically create a WordPress user when a new MailPoet subscriber is added
add_action('init', function() {
    add_action('mailpoet_subscriber_subscribed', 'mailpoet_create_wp_user', 10, 2);
});

function mailpoet_create_wp_user($subscriber, $list_id) {
    $email = $subscriber['email'];
    $first_name = $subscriber['first_name'] ?? '';
    $last_name  = $subscriber['last_name'] ?? '';

    if (!email_exists($email)) {
        $password = wp_generate_password();
        $user_id = wp_create_user($email, $password, $email);

        if (!is_wp_error($user_id)) {
            // Update first/last name
            wp_update_user([
                'ID' => $user_id,
                'first_name' => $first_name,
                'last_name'  => $last_name
            ]);

            // Compose welcome email
            $subject = 'Your account has been created';
            $site_name = get_bloginfo('name');
            $login_url = wp_login_url();

            $message = "Hi {$first_name},\n\n";
            $message .= "You've successfully subscribed to our newsletter and we've also created an account for you on {$site_name}.\n\n";
            $message .= "👉 Login here: {$login_url}\n";
            $message .= "🧑 Username: {$email}\n";
            $message .= "🔐 Password: {$password}\n\n";
            $message .= "You can update your profile or reset your password at any time.\n\n";
            $message .= "Thanks,\nThe {$site_name} Team";

            // Send email
            wp_mail($email, $subject, $message);
        }
    }
}

add_action('admin_head', function() {
    echo '<style>
        /* 🔹 Increase Description Column Width */
        .wp-list-table .column-description {
            width: 500px !important;
            max-width: 600px !important;
            min-width: 400px !important;
            white-space: normal !important;
            word-wrap: break-word !important;
            display: table-cell !important;
        }

        /* 🔹 Hide "Search Filter Thumbnail Image" Column */
        th#product_search_image,
        td.column-product_search_image {
            display: none !important;
        }

        /* 🔹 Expand the Whole Table Width */
        .wrap .wp-list-table {
            width: 100% !important;
            max-width: 100% !important;
        }

        /* 🔹 Make the Left-Side Category Form Smaller */
        #col-left {
            width: 25% !important;  /* Adjust width of form */
            max-width: 250px !important;
        }
        #col-right {
            width: 73% !important;  /* Expand table space */
        }

    </style>';
});

// =======================================================
//  Register "Downloads" (renaming labels) Custom Post Type
// =======================================================
function pd_register_resources_cpt() {

    // CHANGED "Resources" => "Downloads" in labels
    $labels = array(
        'name'               => _x( 'Downloads', 'Post Type General Name', 'pd-toc' ),
        'singular_name'      => _x( 'Download', 'Post Type Singular Name', 'pd-toc' ),
        'menu_name'          => __( 'Downloads', 'pd-toc' ),
        'all_items'          => __( 'All Downloads', 'pd-toc' ),
        'add_new_item'       => __( 'Add New Download', 'pd-toc' ),
        'edit_item'          => __( 'Edit Download', 'pd-toc' ),
        'new_item'           => __( 'New Download', 'pd-toc' ),
        'view_item'          => __( 'View Download', 'pd-toc' ),
        'search_items'       => __( 'Search Downloads', 'pd-toc' ),
        'not_found'          => __( 'No downloads found.', 'pd-toc' ),
    );

    $args = array(
        // Label now "Downloads"
        'label'               => __( 'Downloads', 'pd-toc' ),
        'labels'              => $labels,
        'public'              => true,
        'publicly_queryable'  => true,
        // changed to 'has_archive' => 'downloads' 
        // means archive is at yoursite.com/downloads
        'has_archive'         => 'downloads',
        // changed rewrite slug => 'downloads'
        'rewrite'             => array(
            'slug'       => 'downloads',   // No more /resources/
            'with_front' => false          // optional: no prefix from WP's "permalink structure"
        ),
        'menu_icon'           => 'dashicons-download',
        // Keep same internal supports
        'supports'            => array( 
            'title', 
            'editor', 
            'thumbnail',
			'comments',
            'elementor',
			'author'
        ),
        'show_in_rest'        => true,
    );

    // Keep internal name 'resources' so existing data is not lost
    register_post_type( 'resources', $args );
}
add_action( 'init', 'pd_register_resources_cpt' );

// =======================================================
//  Register "Download Category" taxonomy for "resources" CPT
// =======================================================
function pd_register_resource_category_taxonomy() {

    // Re-labeled from "Resource Category" => "Download Category"
    $labels = array(
        'name'              => _x( 'Download Categories', 'taxonomy general name', 'pd-toc' ),
        'singular_name'     => _x( 'Download Category', 'taxonomy singular name', 'pd-toc' ),
        'search_items'      => __( 'Search Download Categories', 'pd-toc' ),
        'all_items'         => __( 'All Download Categories', 'pd-toc' ),
        'edit_item'         => __( 'Edit Download Category', 'pd-toc' ),
        'update_item'       => __( 'Update Download Category', 'pd-toc' ),
        'add_new_item'      => __( 'Add New Download Category', 'pd-toc' ),
        'new_item_name'     => __( 'New Download Category Name', 'pd-toc' ),
        'menu_name'         => __( 'Download Categories', 'pd-toc' ),
    );

    $args = array(
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        // changed from 'resource-category' => 'download-category'
        'rewrite'           => array(
            'slug'       => 'download-category',
            'with_front' => false
        ),
        'show_in_rest'      => true,
    );

    // Keep internal name 'resource_category' so existing terms remain
    register_taxonomy( 'resource_category', array( 'resources' ), $args );
}
add_action( 'init', 'pd_register_resource_category_taxonomy' );

// Keep post_tag on 'resources'
function pd_attach_builtin_tags_to_resources() {
    register_taxonomy_for_object_type( 'post_tag', 'resources' );
}
add_action( 'init', 'pd_attach_builtin_tags_to_resources' );

/**
 * Add "Download Category" column to All Downloads admin list
 * (We're still calling it 'resource_category' internally)
 */
function pd_add_resource_category_column( $columns ) {
    $new = array();
    foreach ( $columns as $key => $value ) {
        $new[$key] = $value;
        if ( 'title' === $key ) {
            // Column label changed => "Download Category"
            $new['resource_category'] = __( 'Download Category', 'pd-toc' );
        }
    }
    return $new;
}
add_filter( 'manage_resources_posts_columns', 'pd_add_resource_category_column' );

/**
 * Display the terms of "Download Category" in custom column
 */
function pd_manage_resources_custom_column( $column, $post_id ) {
    if ( 'resource_category' === $column ) {
        $terms = get_the_terms( $post_id, 'resource_category' );
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            $term_names = wp_list_pluck( $terms, 'name' );
            echo esc_html( implode( ', ', $term_names ) );
        } else {
            echo '—';
        }
    }
}
add_action( 'manage_resources_posts_custom_column', 'pd_manage_resources_custom_column', 10, 2 );

/**
 * Add an 'Author' column to All Downloads admin list
 */
function pd_add_resources_author_column( $columns ) {
    $new_columns = array();
    foreach ( $columns as $key => $value ) {
        $new_columns[$key] = $value;
        if ( 'title' === $key ) {
            $new_columns['resource_author'] = __( 'Author', 'pd-toc' );
        }
    }
    return $new_columns;
}
add_filter( 'manage_resources_posts_columns', 'pd_add_resources_author_column' );

// Display the author name in that column
function pd_display_resources_author_column( $column, $post_id ) {
    if ( 'resource_author' === $column ) {
        $author_id = get_post_field( 'post_author', $post_id );
        $author_name = get_the_author_meta( 'display_name', $author_id );
        echo esc_html( $author_name );
    }
}
add_action( 'manage_resources_posts_custom_column', 'pd_display_resources_author_column', 10, 2 );

//Empty MailPoet Trash” Button in WP Admin
add_action('admin_menu', function() {
    add_menu_page(
    'MailPoet Trash Purge',
    'MailPoet Trash Purge',
    'manage_options',
    'mailpoet-trash-purge',
    'render_mailpoet_purge_page',
    'dashicons-trash',
    81
);

});

function render_mailpoet_purge_page() {
    if (!current_user_can('manage_options')) return;

    if (isset($_POST['mailpoet_purge_trash']) && check_admin_referer('mailpoet_trash_purge_action')) {
        $deleted = purge_mailpoet_trashed_subscribers();

        echo '<div class="notice notice-success"><p>🧹 Purged ' . $deleted . ' trashed MailPoet subscribers.</p></div>';
    }

    echo '<div class="wrap">';
    echo '<h1>MailPoet Trash Cleanup</h1>';
    echo '<form method="post">';
    wp_nonce_field('mailpoet_trash_purge_action');
    submit_button('🧹 Empty MailPoet Trash Now', 'primary', 'mailpoet_purge_trash');
    echo '</form>';
    echo '</div>';
}

function purge_mailpoet_trashed_subscribers() {
    if (!class_exists('\\MailPoet\\DI\\ContainerWrapper')) return 0;

    $container = \MailPoet\DI\ContainerWrapper::getInstance();
    $entity_manager = $container->get(\MailPoetVendor\Doctrine\ORM\EntityManager::class);
    $repo = $container->get(\MailPoet\Subscribers\SubscribersRepository::class);

    $deleted = 0;
    foreach ($repo->findAll() as $subscriber) {
        if ($subscriber->deletedAt !== null) {
            $entity_manager->remove($subscriber);
            $deleted++;
        }
    }

    $entity_manager->flush();
    return $deleted;
}

/* -------------------------------------------------
 *  SpeedCurve LUX RUM snippet
 *  Docs: https://support.speedcurve.com/docs/rum-snippet
 * ------------------------------------------------ */
add_action( 'wp_head', function () { ?>
    <!-- SpeedCurve LUX stub -->
    <script>
        LUX=function(){function n(){return Date.now?Date.now():+new Date}var r,t=n(),a=window.performance||{},e=a.timing||{activationStart:0,navigationStart:(null===(r=window.LUX)||void 0===r?void 0:r.ns)||t};function i(){return a.now?(r=a.now(),Math.floor(r)):n()-e.navigationStart;var r}(LUX=window.LUX||{}).ac=[],LUX.addData=function(n,r){return LUX.cmd(["addData",n,r])},LUX.cmd=function(n){return LUX.ac.push(n)},LUX.getDebug=function(){return[[t,0,[]]]},LUX.init=function(){return LUX.cmd(["init"])},LUX.mark=function(){for(var n=[],r=0;r<arguments.length;r++)n[r]=arguments[r];if(a.mark)return a.mark.apply(a,n);var t=n[0],e=n[1]||{};void 0===e.startTime&&(e.startTime=i());LUX.cmd(["mark",t,e])},LUX.markLoadTime=function(){return LUX.cmd(["markLoadTime",i()])},LUX.measure=function(){for(var n=[],r=0;r<arguments.length;r++)n[r]=arguments[r];if(a.measure)return a.measure.apply(a,n);var t,e=n[0],o=n[1],u=n[2];t="object"==typeof o?n[1]:{start:o,end:u};t.duration||t.end||(t.end=i());LUX.cmd(["measure",e,t])},LUX.send=function(){return LUX.cmd(["send"])},LUX.ns=t;var o=LUX;return window.LUX_ae=[],window.addEventListener("error",(function(n){window.LUX_ae.push(n)})),o}();
    </script>

    <!-- Main LUX loader -->
    <script async defer
            src="https://cdn.speedcurve.com/js/lux.js?id=4666105141"
            crossorigin="anonymous"></script>
<?php } );

/**
 * GA-4 “file_download” – fires on any <a download> click
 * Added: 2025-06-26
 */
add_action( 'wp_footer', function () { ?>
<script>
document.addEventListener('click', function (e) {
  const link = e.target.closest('a[download]');
  if (!link || !window.gtag) return;

  const last = link.href.split('/').pop();     // part after last “/”
  const bits = last.split('.');
  const ext  = bits.length > 1 ? bits.pop() : '';

  gtag('event', 'file_download', {
    link_url:      link.href,        // full pretty URL
    file_name:     bits.join('.'),   // guessed file name (may be blank)
    file_extension: ext              // guessed extension (may be blank)
  });
});
</script>
<?php } );

// Add Last Updated Column ONLY to Posts (exclude WooCommerce Products)
add_filter('manage_posts_columns', 'add_last_updated_column');

function add_last_updated_column($columns) {
    if(get_post_type() === 'post'){
        $columns['last_updated'] = 'Last Updated';
    }
    return $columns;
}

add_action('manage_posts_custom_column', 'fill_last_updated_column', 10, 2);

function fill_last_updated_column($column_name, $post_id) {
    if ($column_name == 'last_updated') {
        echo get_the_modified_date('Y/m/d \a\t H:i', $post_id);
    }
}

add_filter('manage_edit-post_sortable_columns', 'last_updated_column_sortable');

function last_updated_column_sortable($columns) {
    $columns['last_updated'] = 'modified';
    return $columns;
}
/**
 * ------------------------------------------------------------------
 *  WooCommerce “My Account” – insert a custom top-level link
 *  (ProDesign course) & point it to a public page.
 * ------------------------------------------------------------------
 */
add_filter( 'woocommerce_account_menu_items', function ( $items ) {

    // Create our new entry. The key MUST be unique.
    $custom = [ 'prodesign-course' => __( 'ProDesign course', 'hello-elementor-child' ) ];

    // Prepend it to the existing array (puts it top of stack).
    $items = $custom + $items;

    return $items;
}, 15 );

/**
 * Tell Woo what URL that new endpoint should load.
 */
add_filter( 'woocommerce_get_endpoint_url', function ( $url, $endpoint ) {

    if ( 'prodesign-course' === $endpoint ) {
        // Absolute or relative URL is fine:
        return home_url( '/prodesign-introduction-course/' );
    }
    return $url;

}, 15, 2 );


// Output the empty progress‐bar DIV right after <body> opens
add_action('wp_body_open', 'pd_scroll_progress_markup');
function pd_scroll_progress_markup()
{
    echo '<div id="pd-scroll-progress"></div>';
}

// Enqueue the scroll‐listener JS in the footer
add_action( 'wp_footer', 'pd_scroll_progress_script', 20 );
function pd_scroll_progress_script() {
    ?>
    <script>
        (function(){
            var bar = document.getElementById('pd-scroll-progress');
            if (!bar) return;
            window.addEventListener('scroll', function(){
                var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                var docH = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                var pct  = docH > 0 ? (scrollTop / docH) * 100 : 0;
                bar.style.width = pct + '%';
            });
        })();
    </script>
    <?php
}

/**
 * Duplicate post as draft link in admin post/page list.
 */
function pd_duplicate_post_as_draft(){
    global $wpdb;
    if (! ( isset($_GET['post']) || isset($_POST['post'])  || ( isset($_REQUEST['action']) && 'pd_duplicate_post_as_draft' == $_REQUEST['action'] ) ) ) {
        wp_die('No post to duplicate has been supplied!');
    }
    $post_id = (isset($_GET['post']) ? absint($_GET['post']) : absint($_POST['post']));
    $post = get_post($post_id);

    $new_post_author = wp_get_current_user()->ID;

    if (isset($post) && $post != null) {
        $args = array(
            'comment_status' => $post->comment_status,
            'ping_status'    => $post->ping_status,
            'post_author'    => $new_post_author,
            'post_content'   => $post->post_content,
            'post_excerpt'   => $post->post_excerpt,
            'post_name'      => $post->post_name . '-copy',
            'post_parent'    => $post->post_parent,
            'post_password'  => $post->post_password,
            'post_status'    => 'draft',
            'post_title'     => $post->post_title . ' (Copy)',
            'post_type'      => $post->post_type,
            'to_ping'        => $post->to_ping,
            'menu_order'     => $post->menu_order
        );
        $new_post_id = wp_insert_post($args);

        // Copy taxonomies
        $taxonomies = get_object_taxonomies($post->post_type);
        foreach ($taxonomies as $taxonomy) {
            $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
            wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
        }

        // Copy meta
        $post_meta = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
        if (count($post_meta)!=0) {
            foreach ($post_meta as $meta_info) {
                if($meta_info->meta_key == '_wp_old_slug') continue;
                add_post_meta($new_post_id, $meta_info->meta_key, maybe_unserialize($meta_info->meta_value));
            }
        }

        wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
        exit;
    } else {
        wp_die('Post creation failed, could not find original post: ' . $post_id);
    }
}
add_action( 'admin_action_pd_duplicate_post_as_draft', 'pd_duplicate_post_as_draft' );

function pd_duplicate_post_link( $actions, $post ) {
    if (current_user_can('edit_posts')) {
        $actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=pd_duplicate_post_as_draft&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce' ) . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
    }
    return $actions;
}
add_filter( 'post_row_actions', 'pd_duplicate_post_link', 10, 2 );
add_filter( 'page_row_actions', 'pd_duplicate_post_link', 10, 2 );

/*this is to ensure icons are showing in PD widgets */
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
});


?>
