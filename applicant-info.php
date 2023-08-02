<?php
/*
Plugin Name: Job Applicant Plugin
Description: A plugin to collect job applicant information and assign tasks.
Version: 1.0
Author: Sabbir Mahmud
*/

// Enqueue Bootstrap styles and scripts from the CDN
function job_applicant_enqueue_scripts() {
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css', array(), '5.3.0', 'all');
    wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', array('jquery'), '5.3.0', true);
}
add_action('wp_enqueue_scripts', 'job_applicant_enqueue_scripts');


function job_applicant_enqueue_admin_scripts() {
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css', array(), '5.3.0', 'all');
    wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', array('jquery'), '5.3.0', true);
}
add_action('admin_enqueue_scripts', 'job_applicant_enqueue_admin_scripts');


// Shortcode function to display the job applicant form
function job_applicant_form_shortcode() {
    ob_start();

    // Load the template file and get its contents
    $template_file = plugin_dir_path(__FILE__) . 'templates/job-applicant-form.php';
    if (file_exists($template_file)) {
        include $template_file;
    }

    return ob_get_clean();
}
add_shortcode('job_applicant_form', 'job_applicant_form_shortcode');


// Define the shortcode to display the assigned tasks
function assigned_tasks_shortcode() {
    ob_start();
    // Display the assigned tasks in table format
    global $wpdb;
    $table_name = $wpdb->prefix . 'job_tasks';
    $tasks = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    if ($tasks) {
        ?>
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th>Team</th>
                    <th>Department</th>
                    <th>Experience</th>
                    <th>Task</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($tasks as $task) {
                    echo '<tr>';
                    echo '<td>' . esc_html($task['team']) . '</td>';
                    echo '<td>' . esc_html($task['department']) . '</td>';
                    echo '<td>' . esc_html($task['experience']) . '</td>';
                    echo '<td>' . esc_html($task['task']) . '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
        <?php
    }
    return ob_get_clean();
}
add_shortcode('assigned_tasks', 'assigned_tasks_shortcode');

// Handle form submission and save data to the database
function save_job_applicant_data() {
    if (isset($_POST['submit_form1'])) {
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $team = sanitize_text_field($_POST['team']);
        $team = sanitize_text_field($_POST['team']);
        $otherdepartment = sanitize_text_field($_POST['otherdepartment']);
        $department = sanitize_text_field($_POST['department']);
        $experience = sanitize_text_field($_POST['experience']);
        $assingedtask =isset($_POST['assingedtask']) ? sanitize_text_field($_POST['assingedtask']) : '';
        $taskrespond = sanitize_textarea_field($_POST['taskrespond']);
        $currentDateTime = date('Y/m/d \a\t h:i a');
  
        global $wpdb;
        $table_name = $wpdb->prefix . 'job_applicants';
        $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'email' => $email,
                'team' => $team,
                'otherdepartment' => $otherdepartment,
                'department' => $department,
                'experience' => $experience,
                'task' => $assingedtask,
                'taskrespond' => $taskrespond,
                'timeanddate' => $currentDateTime,

            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );
        wp_redirect(home_url('/thank-you/')); 
        exit;
    }
}
add_action('init', 'save_job_applicant_data');





// Include the admin panel file
include_once(plugin_dir_path(__FILE__) . 'admin.php');

// Create the admin menu items
function job_applicant_admin_menu() {
    add_menu_page('Job Applicant Responses', 'Job Applicant', 'manage_options', 'job_applicant_responses', 'view_job_applicant_responses');
    add_submenu_page('job_applicant_responses', 'Assign Task', 'Assign Task', 'manage_options', 'assign_task', 'assign_task_page');
}
add_action('admin_menu', 'job_applicant_admin_menu');

function job_applicant_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'job_applicants';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        timeanddate varchar(255) NOT NULL,
        team varchar(255) NOT NULL,
        department varchar(255) NOT NULL,
        otherdepartment varchar(255) NOT NULL,
        experience varchar(255) NOT NULL,
        task text NOT NULL,
        taskrespond text NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'job_applicant_create_table');


function job_tasks_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'job_tasks';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        team varchar(255) NOT NULL,
        department varchar(255) NOT NULL,
        experience varchar(255) NOT NULL,
        task text NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'job_tasks_create_table');