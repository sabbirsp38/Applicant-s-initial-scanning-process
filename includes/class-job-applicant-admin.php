<?php
/**
 * Admin functionality
 *
 * @package JobApplicantManager
 */

if (!defined('ABSPATH')) {
    exit;
}

class Job_Applicant_Admin {
    /**
     * Plugin instance.
     *
     * @var Job_Applicant_Manager
     */
    private $plugin;

    /**
     * Initialize the admin functionality.
     *
     * @param Job_Applicant_Manager $plugin Plugin instance.
     */
    public function __construct($plugin) {
        $this->plugin = $plugin;
        
        // Hook into WordPress
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_init', array($this, 'handle_actions'));
    }

    /**
     * Add admin menu items.
     */
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            esc_html__('Job Manager', 'job-applicant-manager'),
            esc_html__('Job Manager', 'job-applicant-manager'),
            'manage_options',
            'job_manager',
            array($this, 'render_jobs_page'),
            'dashicons-businessman',
            30
        );

        // Submenus
        add_submenu_page(
            'job_manager',
            esc_html__('All Jobs', 'job-applicant-manager'),
            esc_html__('All Jobs', 'job-applicant-manager'),
            'manage_options',
            'job_manager',
            array($this, 'render_jobs_page')
        );

        add_submenu_page(
            'job_manager',
            esc_html__('Add New Job', 'job-applicant-manager'),
            esc_html__('Add New Job', 'job-applicant-manager'),
            'manage_options',
            'add_new_job',
            array($this, 'render_add_job_page')
        );

        add_submenu_page(
            'job_manager',
            esc_html__('Applications', 'job-applicant-manager'),
            esc_html__('Applications', 'job-applicant-manager'),
            'manage_options',
            'job_applications',
            array($this, 'render_applications_page')
        );

        add_submenu_page(
            'job_manager',
            esc_html__('Settings', 'job-applicant-manager'),
            esc_html__('Settings', 'job-applicant-manager'),
            'manage_options',
            'job-applicant-settings',
            array($this, 'render_settings_page')
        );
    }

    /**
     * Enqueue admin scripts and styles.
     */
    public function enqueue_admin_scripts($hook) {
        // Debug output
        error_log('Current hook: ' . $hook);
        
        // Change this to match your actual admin page hooks
        $allowed_hooks = array(
            'job-manager_page_add_new_job',
            'toplevel_page_job_manager'
        );

        if (!in_array($hook, $allowed_hooks)) {
            return;
        }

        // Enqueue jQuery first
        wp_enqueue_script('jquery');

        // Enqueue your admin script
        wp_enqueue_script(
            'job-applicant-admin',
            JAM_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            time(), // Use time() for development to prevent caching
            true
        );

        // Enqueue admin styles
        wp_enqueue_style(
            'job-applicant-admin',
            JAM_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            time()
        );

        // Add debug information
        wp_localize_script('job-applicant-admin', 'jamDebug', array(
            'hook' => $hook,
            'pluginUrl' => JAM_PLUGIN_URL,
            'adminUrl' => admin_url()
        ));
    }

    /**
     * Handle admin actions.
     */
    public function handle_actions() {
        if (isset($_POST['submit_task']) && isset($_POST['task_nonce'])) {
            if (wp_verify_nonce($_POST['task_nonce'], 'save_task')) {
                $this->save_task($_POST);
            }
        }

        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'delete_applicant':
                    $this->delete_applicant();
                    break;
                case 'delete_task':
                    $this->delete_task();
                    break;
                case 'delete_application':
                    $this->delete_application();
                    break;
            }
        }

        if (isset($_POST['submit_job'])) {
            $this->save_job($_POST);
        }

        if (isset($_POST['update_status'])) {
            $this->update_application_status();
        }
    }

    /**
     * Render jobs listing page.
     */
    public function render_jobs_page() {
        if (isset($_GET['action']) && $_GET['action'] === 'delete_job') {
            $this->delete_job();
        }
        include JAM_PLUGIN_DIR . 'templates/admin/jobs-page.php';
    }

    /**
     * Render add/edit job page.
     */
    public function render_add_job_page() {
        $job_id = isset($_GET['job_id']) ? absint($_GET['job_id']) : 0;
        $job = $job_id ? $this->get_job($job_id) : null;
        $questions = $job_id ? $this->get_job_questions($job_id) : array();
        include JAM_PLUGIN_DIR . 'templates/admin/add-job.php';
    }

    /**
     * Render applications page.
     */
    public function render_applications_page() {
        if (isset($_GET['action']) && $_GET['action'] === 'view') {
            include JAM_PLUGIN_DIR . 'templates/admin/view-application.php';
            return;
        }

        $job_id = isset($_GET['job_id']) ? absint($_GET['job_id']) : 0;
        $applications = $this->get_applications($job_id);
        include JAM_PLUGIN_DIR . 'templates/admin/applications-page.php';
    }

    /**
     * Render settings page.
     */
    public function render_settings_page() {
        // Save settings if form is submitted
        if (isset($_POST['save_settings']) && check_admin_referer('save_jam_settings')) {
            $thank_you_message = wp_kses_post($_POST['thank_you_message']);
            $this->plugin->save_setting('thank_you_message', $thank_you_message);
            echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'job-applicant-manager') . '</p></div>';
        }

        // Get current settings
        $thank_you_message = $this->plugin->get_setting('thank_you_message') ?: __('Thank you for your application! We will review your submission and contact you soon.', 'job-applicant-manager');
        ?>
        <div class="wrap">
            <h1><?php _e('Job Application Settings', 'job-applicant-manager'); ?></h1>
            
            <form method="post" action="">
                <?php wp_nonce_field('save_jam_settings'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="thank_you_message"><?php _e('Thank You Message', 'job-applicant-manager'); ?></label>
                        </th>
                        <td>
                            <textarea name="thank_you_message" id="thank_you_message" class="large-text" rows="5"><?php echo esc_textarea($thank_you_message); ?></textarea>
                            <p class="description"><?php _e('This message will be shown to applicants after they submit their application.', 'job-applicant-manager'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="submit" name="save_settings" class="button button-primary">
                        <?php _e('Save Settings', 'job-applicant-manager'); ?>
                    </button>
                </p>
            </form>
        </div>
        <?php
    }

    /**
     * Get job by ID.
     */
    public function get_job($job_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'jam_jobs';
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $job_id
        ));
    }

    /**
     * Get job questions.
     */
    public function get_job_questions($job_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'jam_job_questions';
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE job_id = %d ORDER BY order_num ASC",
            $job_id
        ));
    }

    /**
     * Get applications.
     */
    public function get_applications($job_id = 0) {
        global $wpdb;
        $table = $wpdb->prefix . 'jam_applications';
        
        // Add debug logging
        error_log('Getting applications for job ID: ' . $job_id);
        
        if ($job_id) {
            $applications = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table WHERE job_id = %d ORDER BY created_at DESC",
                $job_id
            ));
        } else {
            $applications = $wpdb->get_results(
                "SELECT * FROM $table ORDER BY created_at DESC"
            );
        }

        // Debug output
        error_log('Applications query result: ' . print_r($applications, true));
        if ($wpdb->last_error) {
            error_log('Database error: ' . $wpdb->last_error);
        }

        return $applications;
    }

    /**
     * Save settings.
     */
    private function save_settings($data) {
        if (!isset($data['settings_nonce']) || !wp_verify_nonce($data['settings_nonce'], 'save_settings')) {
            return;
        }

        $settings = array(
            'notification_email' => sanitize_email($data['notification_email']),
            'form_title' => sanitize_text_field($data['form_title']),
            'primary_color' => sanitize_hex_color($data['primary_color']),
            'success_message' => wp_kses_post($data['success_message'])
        );

        update_option('jam_settings', $settings);
        wp_safe_redirect(add_query_arg('updated', '1'));
        exit;
    }

    /**
     * Delete job.
     */
    private function delete_job() {
        if (!isset($_GET['job_id']) || !isset($_GET['_wpnonce'])) {
            return;
        }

        $job_id = absint($_GET['job_id']);
        if (!wp_verify_nonce($_GET['_wpnonce'], 'delete_job_' . $job_id)) {
            return;
        }

        global $wpdb;
        $jobs_table = $wpdb->prefix . 'jam_jobs';
        $questions_table = $wpdb->prefix . 'jam_job_questions';

        // Delete job questions
        $wpdb->delete($questions_table, array('job_id' => $job_id), array('%d'));

        // Delete job
        $wpdb->delete($jobs_table, array('id' => $job_id), array('%d'));

        wp_safe_redirect(add_query_arg('deleted', '1'));
        exit;
    }

    /**
     * Get applicants from database.
     */
    public function get_applicants() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'job_applicants';
        return $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    }

    /**
     * Get tasks from database.
     */
    public function get_tasks() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'job_tasks';
        return $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    }

    /**
     * Save task to database.
     */
    private function save_task($data) {
        if (!current_user_can('manage_options')) {
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'job_tasks';
        
        $wpdb->insert(
            $table_name,
            array(
                'team' => sanitize_text_field($data['team']),
                'department' => sanitize_text_field($data['department']),
                'experience' => sanitize_text_field($data['experience']),
                'task' => sanitize_textarea_field($data['task']),
            ),
            array('%s', '%s', '%s', '%s')
        );

        wp_safe_redirect(add_query_arg('updated', '1'));
        exit;
    }

    /**
     * Delete task from database.
     */
    private function delete_task() {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'delete_task_nonce')) {
            return;
        }

        if (isset($_GET['task_id'])) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'job_tasks';
            $task_id = absint($_GET['task_id']);
            
            $wpdb->delete(
                $table_name,
                array('id' => $task_id),
                array('%d')
            );

            wp_safe_redirect(add_query_arg('deleted', '1'));
            exit;
        }
    }

    /**
     * Get all jobs
     */
    public function get_jobs() {
        global $wpdb;
        $table = $wpdb->prefix . 'jam_jobs';
        return $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");
    }

    /**
     * Get applications count for a job
     */
    public function get_applications_count($job_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'jam_applications';
        return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE job_id = %d", $job_id));
    }

    /**
     * Save job and its questions
     */
    private function save_job($data) {
        if (!isset($data['job_nonce']) || !wp_verify_nonce($data['job_nonce'], 'save_job')) {
            return;
        }

        global $wpdb;
        $jobs_table = $wpdb->prefix . 'jam_jobs';
        $questions_table = $wpdb->prefix . 'jam_job_questions';

        // Save job details
        $job_data = array(
            'title' => sanitize_text_field($data['job_title']),
            'description' => wp_kses_post($data['job_description']),
            'status' => 'open'
        );

        if (isset($data['job_id'])) {
            // Update existing job
            $wpdb->update(
                $jobs_table, 
                $job_data, 
                array('id' => absint($data['job_id']))
            );
            $job_id = absint($data['job_id']);

            // Delete existing questions
            $wpdb->delete(
                $questions_table,
                array('job_id' => $job_id),
                array('%d')
            );
        } else {
            // Insert new job
            $wpdb->insert($jobs_table, $job_data);
            $job_id = $wpdb->insert_id;
        }

        // Save questions
        if (isset($data['questions']) && is_array($data['questions'])) {
            foreach ($data['questions'] as $index => $question) {
                if (empty($question['text'])) {
                    continue;
                }

                $question_data = array(
                    'job_id' => $job_id,
                    'question' => sanitize_text_field($question['text']),
                    'type' => sanitize_text_field($question['type']),
                    'options' => isset($question['options']) ? sanitize_textarea_field($question['options']) : '',
                    'required' => isset($question['required']) ? 1 : 0,
                    'order_num' => $index
                );

                $wpdb->insert(
                    $questions_table,
                    $question_data,
                    array('%d', '%s', '%s', '%s', '%d', '%d')
                );
            }
        }

        wp_safe_redirect(admin_url('admin.php?page=job_manager&updated=1'));
        exit;
    }

    /**
     * Get plugin settings
     */
    public function get_settings() {
        global $wpdb;
        $table = $wpdb->prefix . 'jam_settings';
        $results = $wpdb->get_results("SELECT setting_key, setting_value FROM $table", OBJECT_K);
        
        $settings = array();
        foreach ($results as $key => $row) {
            $settings[$key] = $row->setting_value;
        }
        
        return $settings;
    }

    /**
     * Delete application
     */
    private function delete_application() {
        if (!isset($_GET['id']) || !isset($_GET['_wpnonce'])) {
            return;
        }

        $application_id = absint($_GET['id']);
        if (!wp_verify_nonce($_GET['_wpnonce'], 'delete_application_' . $application_id)) {
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'jam_applications';
        
        $wpdb->delete(
            $table,
            array('id' => $application_id),
            array('%d')
        );

        wp_safe_redirect(add_query_arg('deleted', '1', admin_url('admin.php?page=job_applications')));
        exit;
    }

    /**
     * Get single application
     */
    public function get_application($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'jam_applications';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $id
        ));
    }

    /**
     * Update application status
     */
    private function update_application_status() {
        if (!isset($_POST['update_status']) || !isset($_POST['status_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['status_nonce'], 'update_application_status')) {
            return;
        }

        $application_id = absint($_POST['application_id']);
        $new_status = sanitize_text_field($_POST['application_status']);

        global $wpdb;
        $table = $wpdb->prefix . 'jam_applications';
        
        $wpdb->update(
            $table,
            ['status' => $new_status],
            ['id' => $application_id],
            ['%s'],
            ['%d']
        );

        wp_safe_redirect(add_query_arg('updated', '1', admin_url('admin.php?page=job_applications&action=view&id=' . $application_id)));
        exit;
    }

    // Add other admin methods here...
} 