<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

register_activation_hook(TABLEON_PATH . 'index.php', function() {
    global $wpdb;

    if (!function_exists('dbDelta')) {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    }

    $charset_collate = '';
    if (method_exists($wpdb, 'has_cap') AND $wpdb->has_cap('collation')) {
        if (!empty($wpdb->charset)) {
            $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
        }
        if (!empty($wpdb->collate)) {
            $charset_collate .= " COLLATE $wpdb->collate";
        }
    }

    //***

    $tableon_tables = $wpdb->prefix . 'tableon_tables';
    if (!$wpdb->get_var("SHOW TABLES LIKE '{$tableon_tables}'") !== $tableon_tables) {
        $sql = "CREATE TABLE IF NOT EXISTS `{$tableon_tables}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(256) NOT NULL DEFAULT 'New Table',
                `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'is published',
                `thumbnail` int(11) NOT NULL DEFAULT '0',
                `table_action` varchar(32) DEFAULT NULL,
                `post_type` varchar(64) NOT NULL DEFAULT 'post',
                `skin` varchar(64) DEFAULT NULL,
                `custom_css` text,
                `filter` text,
                `filter_options` text,
                `predefinition` text,
                `options` text,
                `compiled_data` text,
                 PRIMARY KEY (`id`)
              ) {$charset_collate};";


        dbDelta($sql);
    }

    //+++

    $tableon_tables_columns = $wpdb->prefix . 'tableon_tables_columns';
    if (!$wpdb->get_var("SHOW TABLES LIKE '{$tableon_tables_columns}'") !== $tableon_tables_columns) {
        $sql = "CREATE TABLE IF NOT EXISTS `{$tableon_tables_columns}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `table_id` int(11) NOT NULL,
                `title` varchar(64) NOT NULL DEFAULT 'New Column',
                `field_key` varchar(48) DEFAULT NULL,
                `is_active` tinyint(1) NOT NULL DEFAULT '0',
                `show_on_mobile` tinyint(1) NOT NULL DEFAULT '1',
                `width` varchar(16) NOT NULL DEFAULT 'auto',
                `notes` text COMMENT 'tootip on the site front',
                `options` text,
                `pos_num` int(4) NOT NULL DEFAULT '0' COMMENT 'position in table',
                `created` int(12) DEFAULT NULL,
                 PRIMARY KEY (`id`),
                 KEY `table_id` (`table_id`),
                 KEY `is_active` (`is_active`)
              ) {$charset_collate};";


        dbDelta($sql);
    }

    //+++

    $tableon_tables_meta = $wpdb->prefix . 'tableon_tables_meta';
    if (!$wpdb->get_var("SHOW TABLES LIKE '{$tableon_tables_meta}'") !== $tableon_tables_meta) {
        $sql = "CREATE TABLE IF NOT EXISTS `{$tableon_tables_meta}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(32) DEFAULT 'new meta key',
                `table_id` int(11) NOT NULL,
                `meta_key` varchar(64) DEFAULT 'write_key_here',
                `meta_type` varchar(24) DEFAULT 'not_defined',
                `notes` text,                
                 PRIMARY KEY (`id`),
                 KEY `table_id` (`table_id`)
              ) {$charset_collate};";


        dbDelta($sql);
    }

    //+++

    $tableon_vocabulary = $wpdb->prefix . 'tableon_vocabulary';
    if (!$wpdb->get_var("SHOW TABLES LIKE '{$tableon_vocabulary}'") !== $tableon_vocabulary) {
        $sql = "CREATE TABLE IF NOT EXISTS `{$tableon_vocabulary}` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `title` text,
            `translations` text,
             PRIMARY KEY (`id`),
             FULLTEXT KEY `title` (`title`)
          ) {$charset_collate};";


        dbDelta($sql);
    }

    add_option('tableon_db_ver', '1.0');
});
