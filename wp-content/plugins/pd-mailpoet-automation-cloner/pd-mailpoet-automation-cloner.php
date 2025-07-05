<?php
/*
Plugin Name: PD_MailPoet Automation Cloner
Description: Allows duplication of MailPoet automations.
Version: 1.1
Author: Jarek Wityk
*/

add_action('admin_init', function() {
    if (
        current_user_can('manage_options') &&
        isset($_GET['action']) &&
        $_GET['action'] === 'duplicate_mailpoet_automation' &&
        isset($_GET['automation_id']) &&
        check_admin_referer('duplicate_mailpoet_automation_' . $_GET['automation_id'])
    ) {
        $automation_id = intval($_GET['automation_id']);
        clone_mailpoet_automation($automation_id);
    }
});

function clone_mailpoet_automation($automation_id) {
    global $wpdb;

    $table = $wpdb->prefix . 'mailpoet_automations';
    $original = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $automation_id), ARRAY_A);
    if (!$original) return;

    unset($original['id']);
    $original['name'] .= ' (Copy)';
    $original['status'] = 'draft'; 

    $wpdb->insert($table, $original);
    $new_id = $wpdb->insert_id;

    // Clone trigger
    $trigger_table = $wpdb->prefix . 'mailpoet_automation_triggers';
    $original_trigger = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $trigger_table WHERE automation_id = %d",
        $automation_id
    ), ARRAY_A);

    if ($original_trigger) {
        unset($original_trigger['automation_id']);
        $original_trigger['automation_id'] = $new_id;
        $wpdb->insert($trigger_table, $original_trigger);
    }

    // Optional: Clone steps (for legacy table if present)
    $steps_table = $wpdb->prefix . 'mailpoet_automation_steps';
    if ($wpdb->get_var("SHOW TABLES LIKE '$steps_table'") === $steps_table) {
        $steps = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$steps_table} WHERE automation_id = %d", $automation_id), ARRAY_A);
        foreach ($steps as $step) {
            unset($step['id']);
            $step['automation_id'] = $new_id;
            $wpdb->insert($steps_table, $step);
        }
    }

    // Clone version (with steps JSON)
    $version_table = $wpdb->prefix . 'mailpoet_automation_versions';
    $original_version = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $version_table WHERE automation_id = %d ORDER BY id DESC LIMIT 1",
        $automation_id
    ), ARRAY_A);

    if ($original_version && !empty($original_version['steps'])) {
        unset($original_version['id']);
        $original_version['automation_id'] = $new_id;
        $original_version['created_at'] = current_time('mysql');
        $original_version['updated_at'] = current_time('mysql');

        $inserted = $wpdb->insert($version_table, $original_version);

        file_put_contents(
            WP_CONTENT_DIR . '/kickbox-debug.log',
            "[" . date('Y-m-d H:i:s') . "] Version insert for automation ID $new_id → " . ($inserted ? "SUCCESS" : "FAIL") .
            " | Error: " . $wpdb->last_error . "\n",
            FILE_APPEND
        );
    } else {
        file_put_contents(
            WP_CONTENT_DIR . '/kickbox-debug.log',
            "[" . date('Y-m-d H:i:s') . "] Skipped version clone — no original or empty steps for automation ID $automation_id\n",
            FILE_APPEND
        );
    }

    // Redirect back to cloner UI with success notice
    wp_redirect(admin_url("admin.php?page=pd-mailpoet-cloner&status=success"));
    exit;
}

// Add top-level admin menu
add_action('admin_menu', function () {
    add_menu_page(
        'PD Automation Cloner',
        'Automation Cloner',
        'manage_options',
        'pd-mailpoet-cloner',
        'pd_mailpoet_cloner_admin_page',
        'dashicons-controls-repeat',
        58
    );
});

// Admin UI
function pd_mailpoet_cloner_admin_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'mailpoet_automations';
    $automations = $wpdb->get_results("SELECT id, name FROM $table ORDER BY id DESC", ARRAY_A);

    echo '<div class="wrap"><h1>PD MailPoet Automation Cloner</h1>';

    if (isset($_GET['status']) && $_GET['status'] === 'success') {
        echo '<div class="notice notice-success is-dismissible"><p>✅ Automation duplicated successfully!</p></div>';
    }

    echo '<table class="widefat fixed striped" style="margin-top: 20px;">
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th>Automation Name</th>
                    <th width="15%">Action</th>
                </tr>
            </thead>
            <tbody>';

    foreach ($automations as $auto) {
        $id = $auto['id'];
        $nonce = wp_create_nonce('duplicate_mailpoet_automation_' . $id);
        $url = admin_url("admin.php?action=duplicate_mailpoet_automation&automation_id=$id&_wpnonce=$nonce");

        echo "<tr>
                <td>{$id}</td>
                <td>{$auto['name']}</td>
                <td><a href='{$url}' class='button'>🌀 Duplicate</a></td>
              </tr>";
    }

    echo '</tbody></table></div>';
}
