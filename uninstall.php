<?php
// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('job_applicant_manager_version');

// Drop custom tables
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}job_applicants");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}job_tasks"); 