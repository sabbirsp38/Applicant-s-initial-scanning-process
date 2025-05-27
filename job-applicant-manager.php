<?php
/**
 * Plugin Name: Job Applicant Manager
 * Plugin URI: https://example.com/plugins/job-applicant-manager
 * Description: A plugin to collect and manage job applicant information and assign tasks.
 * Version: 1.0.0
 * Requires at least: 5.2
 * Requires PHP: 7.2
 * Author: Sabbir Mahmud
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: job-applicant-manager
 * Domain Path: /languages
 *
 * @package JobApplicantManager
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('JAM_VERSION', '1.0.0');
define('JAM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('JAM_PLUGIN_URL', plugin_dir_url(__FILE__));

// Load required files
require_once JAM_PLUGIN_DIR . 'includes/class-job-applicant-manager.php';
require_once JAM_PLUGIN_DIR . 'includes/class-job-applicant-admin.php';
require_once JAM_PLUGIN_DIR . 'includes/class-job-applicant-form.php';

class JobApplicantManager {
    /**
     * Create plugin tables
     */
    private function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Jobs table
        $jobs_table = $wpdb->prefix . 'jam_jobs';
        $jobs_sql = "CREATE TABLE IF NOT EXISTS $jobs_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            description text NOT NULL,
            requirements text NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'open',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        // Job Questions table
        $questions_table = $wpdb->prefix . 'jam_job_questions';
        $questions_sql = "CREATE TABLE IF NOT EXISTS $questions_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            job_id mediumint(9) NOT NULL,
            question text NOT NULL,
            type varchar(50) NOT NULL,
            options text,
            required tinyint(1) DEFAULT 0,
            order_num int DEFAULT 0,
            PRIMARY KEY (id),
            KEY job_id (job_id)
        ) $charset_collate;";

        // Applications table
        $applications_table = $wpdb->prefix . 'jam_applications';
        $applications_sql = "CREATE TABLE IF NOT EXISTS $applications_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            job_id mediumint(9) NOT NULL,
            personal_info text NOT NULL,
            answers text NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY job_id (job_id)
        ) $charset_collate;";

        // Job Setting table
        $settings_table = $wpdb->prefix . 'jam_settings';
        $settings_sql = "CREATE TABLE IF NOT EXISTS $settings_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            notification_email varchar(100) DEFAULT '' NOT NULL,
            form_title varchar(255) DEFAULT '' NOT NULL,
            primary_color varchar(7) DEFAULT '' NOT NULL,
            success_message text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($jobs_sql);
        dbDelta($questions_sql);
        dbDelta($applications_sql);
        dbDelta($settings_sql);
    }

    /**
     * Activate plugin
     */
    public function activate() {
        $this->create_tables();
    }

    /**
     * Deactivate plugin
     */
    public function deactivate() {
        // Optional: Add any deactivation cleanup logic here
    }
}

// Initialize plugin
function jam_init() {
    $plugin = new Job_Applicant_Manager();
    $plugin->init();
}
add_action('plugins_loaded', 'jam_init');

// Plugin lifecycle hooks
$jobApplicantManager = new JobApplicantManager();
register_activation_hook(__FILE__, array($jobApplicantManager, 'activate'));
register_deactivation_hook(__FILE__, array($jobApplicantManager, 'deactivate'));


