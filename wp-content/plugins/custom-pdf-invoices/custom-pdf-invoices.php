<?php
/**
 * Plugin Name: PD_Custom WooCommerce PDF Invoices
 * Description: Generate and attach PDF invoices to WooCommerce order emails, and allow both customers and admins to download them.
 * Version: 1.1.4
 * Author: Jarek Wityk
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Load Composer's autoloader for Dompdf
require_once __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;

// Function to generate invoice HTML
function generate_invoice_html($order) {
    ob_start();
    ?>
    <html>
    <head>
        <style>
            /* General invoice styling */
            body {
                font-family: Arial, sans-serif;
                color: #0E172D;
                margin: 0;
                padding: 10;
            }

            .invoice-container {
                border: 2px solid #0E172D;
                padding: 20px;
                border-radius: 10px;
                max-width: 800px;
                margin: 0 auto;
            }

            .invoice-header {
                text-align: center;
            }

            .invoice-title {
                font-size: 24px;
                font-weight: bold;
                margin-bottom: 20px;
            }

            /* Table styling */
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
                border: 1px solid #0E172D;
                border-radius: 10px;
                overflow: hidden;
            }

            table, th, td {
                border: 1px solid #0E172D;
                padding: 10px;
                text-align: left;
            }

            th {
                background-color: #0E172D;
                color: #ffffff;
            }

            /* Padding for text and right-aligned totals */
            .invoice-text {
                padding: 20px 0;
            }

            .totals-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            .totals-table td {
                padding: 10px;
                text-align: right;
            }

            .invoice-total {
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class="invoice-container">
            <!-- Invoice Header without logo -->
            <div class="invoice-header">
                <div class="invoice-title">Invoice #<?php echo $order->get_order_number(); ?></div>
            </div>

            <!-- Company details -->
            <div class="invoice-text">
                <p><strong>Company:</strong> PROJECT DESIGN (IO) LTD</p>
                <p><strong>Address:</strong> 99 Church Hill Road, Sutton, Surrey, SM3 8LL</p>
                <p><strong>VAT Number:</strong> GB4444669953</p>
            </div>

            <!-- Order details -->
            <div class="invoice-text">
                <p><strong>Date:</strong> <?php echo wc_format_datetime($order->get_date_created()); ?></p>

                <!-- Billing and shipping address -->
                <p><strong>Billing Address:</strong> <?php echo $order->get_formatted_billing_address(); ?></p>
                <p><strong>Shipping Address:</strong> <?php echo $order->get_formatted_shipping_address(); ?></p>
            </div>

            <!-- Order Items -->
            <h2>Order Items:</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price (Excl. VAT)</th>
                        <th>Tax (VAT)</th>
                        <th>Total (Incl. VAT)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order->get_items() as $item_id => $item) :
                        $quantity = $item->get_quantity();  // Get the quantity
                        $subtotal = $item->get_subtotal();  // Subtotal (Excl. tax)
                        $subtotal_tax = $item->get_subtotal_tax();  // Tax (VAT)
                        $total_incl_tax = $subtotal + $subtotal_tax;  // Total incl. tax
                        ?>
                        <tr>
                            <td><?php echo $item->get_name(); ?></td>
                            <td><?php echo $quantity; ?></td>
                            <td><?php echo wc_price($subtotal); ?></td>
                            <td><?php echo wc_price($subtotal_tax); ?></td>
                            <td><?php echo wc_price($total_incl_tax); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Order Totals with right alignment -->
            <table class="totals-table">
                <tr>
                    <td><strong>Subtotal (Excl. VAT):</strong></td>
                    <td><?php echo wc_price($order->get_subtotal()); ?></td>
                </tr>
                <tr>
                    <td><strong>Total Tax (VAT):</strong></td>
                    <td><?php echo wc_price($order->get_total_tax()); ?></td>
                </tr>
                <tr>
                    <td><strong>Total (Incl. VAT):</strong></td>
                    <td><?php echo wc_price($order->get_total()); ?></td>
                </tr>
            </table>
        </div>
    </body>
    </html>
    <?php
    return ob_get_clean();
}

// Generate and save PDF invoice
function generate_and_save_pdf($order) {
    $dompdf = new Dompdf();
    $dompdf->loadHtml(generate_invoice_html($order));
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $upload_dir = wp_upload_dir();
    $pdf_dir = $upload_dir['basedir'] . '/secure_invoices';

    if (!file_exists($pdf_dir)) {
        mkdir($pdf_dir, 0755, true);
    }

    $pdf_path = $pdf_dir . '/invoice-' . $order->get_order_number() . '.pdf';
    file_put_contents($pdf_path, $dompdf->output());

    return $pdf_path;
}

// Generate invoice when order is completed
add_action('woocommerce_order_status_completed', function ($order_id) {
    generate_and_save_pdf(wc_get_order($order_id));
});

// Add "Download Invoice" button for customers
add_filter('woocommerce_my_account_my_orders_actions', function ($actions, $order) {
    $pdf_path = wp_upload_dir()['basedir'] . '/secure_invoices/invoice-' . $order->get_order_number() . '.pdf';

    if (file_exists($pdf_path)) {
        $actions['download_invoice'] = array(
            'url' => wp_nonce_url(add_query_arg(['download_invoice' => $order->get_id()]), 'download_invoice'),
            'name' => __('Download Invoice', 'woocommerce'),
        );
    }

    return $actions;
}, 10, 2);

// Handle customer invoice download
add_action('init', function () {
    if (isset($_GET['download_invoice']) && wp_verify_nonce($_GET['_wpnonce'], 'download_invoice')) {
        $order = wc_get_order(absint($_GET['download_invoice']));
        $pdf_path = wp_upload_dir()['basedir'] . '/secure_invoices/invoice-' . $order->get_order_number() . '.pdf';

        if ($order && file_exists($pdf_path)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="invoice-' . $order->get_order_number() . '.pdf"');
            readfile($pdf_path);
            exit;
        }
    }
});

// Add "Download Invoice" button inside Admin Order Page
add_action('woocommerce_admin_order_data_after_order_details', function ($order) {
    $pdf_path = wp_upload_dir()['basedir'] . '/secure_invoices/invoice-' . $order->get_order_number() . '.pdf';

    if (file_exists($pdf_path)) {
        $download_url = wp_nonce_url(admin_url('admin-post.php?action=download_admin_invoice&order_id=' . $order->get_id()), 'download_admin_invoice');
        echo '<p><a href="' . esc_url($download_url) . '" class="button button-primary">' . __('Download Invoice', 'woocommerce') . '</a></p>';
    }
});

// Handle admin invoice download request
add_action('admin_post_download_admin_invoice', function () {
    if (!current_user_can('manage_woocommerce')) {
        wp_die(__('You do not have permission to access this page.', 'woocommerce'));
    }

    if (isset($_GET['order_id']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'download_admin_invoice')) {
        $order = wc_get_order(absint($_GET['order_id']));
        $pdf_path = wp_upload_dir()['basedir'] . '/secure_invoices/invoice-' . $order->get_order_number() . '.pdf';

        if ($order && file_exists($pdf_path)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="invoice-' . $order->get_order_number() . '.pdf"');
            readfile($pdf_path);
            exit;
        }
    }
    wp_die(__('Invoice not found.', 'woocommerce'));
});