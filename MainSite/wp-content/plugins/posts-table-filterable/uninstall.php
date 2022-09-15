<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
$settings = get_option('tableon_settings', []);
if ($settings AND!is_array($settings)) {
    $settings = json_decode($settings, true);
}

if (isset($settings['delete_db_tables']) AND intval($settings['delete_db_tables'])) {
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}tableon_tables");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}tableon_tables_columns");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}tableon_tables_meta");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}tableon_vocabulary");
    delete_option('tableon_settings');
    delete_option('tableon_mime_types_association');
}

