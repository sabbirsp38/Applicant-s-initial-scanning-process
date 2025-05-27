<?php
/**
 * Main plugin class
 *
 * @package JobApplicantManager
 */

if (!defined('ABSPATH')) {
    exit;
}

class Job_Applicant_Manager {
    /**
     * Plugin instance.
     *
     * @var Job_Applicant_Manager
     */
    private static $instance = null;

    /**
     * Admin instance.
     *
     * @var Job_Applicant_Admin
     */
    private $admin;

    /**
     * Form instance.
     *
     * @var Job_Applicant_Form
     */
    private $form;

    /**
     * Initialize the plugin.
     */
    public function init() {
        $this->load_textdomain();
        $this->init_hooks();
        $this->init_components();
        $this->verify_tables();
    }

    /**
     * Initialize plugin components.
     */
    private function init_components() {
        // Initialize admin
        if (is_admin()) {
            $this->admin = new Job_Applicant_Admin($this);
        }

        // Initialize form handler
        $this->form = new Job_Applicant_Form();
    }

    /**
     * Load plugin textdomain.
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'job-applicant-manager',
            false,
            dirname(plugin_basename(JAM_PLUGIN_DIR)) . '/languages'
        );
    }

    /**
     * Initialize hooks.
     */
    private function init_hooks() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('init', array($this, 'register_shortcodes'));
        add_action('init', array($this, 'start_session'));
        add_action('init', array($this, 'handle_step1_submission'));
        add_action('init', array($this, 'handle_application_submission'));
    }

    /**
     * Enqueue scripts and styles.
     */
    public function enqueue_scripts() {
        // Bootstrap
        wp_enqueue_style(
            'job-applicant-bootstrap',
            JAM_PLUGIN_URL . 'assets/css/bootstrap.min.css',
            array(),
            JAM_VERSION
        );

        // Frontend styles
        wp_enqueue_style(
            'job-applicant-frontend',
            JAM_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            JAM_VERSION
        );

        wp_enqueue_script(
            'job-applicant-bootstrap',
            JAM_PLUGIN_URL . 'assets/js/bootstrap.bundle.min.js',
            array('jquery'),
            JAM_VERSION,
            true
        );
    }

    /**
     * Register shortcodes.
     */
    public function register_shortcodes() {
        add_shortcode('job_application_system', array($this, 'render_job_application_system'));
    }

    /**
     * Render the complete job application system
     */
    public function render_job_application_system() {
        ob_start();
        
        if (isset($_GET['application_submitted']) && $_GET['application_submitted'] === '1') {
            // Show thank you page
            include JAM_PLUGIN_DIR . 'templates/public/thank-you.php';
        } elseif (isset($_GET['job_id'])) {
            // Show application form
            include JAM_PLUGIN_DIR . 'templates/public/application-form.php';
        } else {
            // Show job listings
            include JAM_PLUGIN_DIR . 'templates/public/job-listings.php';
        }
        
        return ob_get_clean();
    }

    /**
     * Render thank you page.
     */
    public function render_thank_you() {
        ob_start();
        include JAM_PLUGIN_DIR . 'templates/public/thank-you.php';
        return ob_get_clean();
    }

    /**
     * Plugin activation.
     */
    public static function activate() {
        self::create_tables();
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation.
     */
    public static function deactivate() {
        flush_rewrite_rules();
    }

    /**
     * Create plugin tables.
     */
    private static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Jobs table
        $jobs_table = $wpdb->prefix . 'jam_jobs';
        $jobs_sql = "CREATE TABLE $jobs_table (
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
        $questions_sql = "CREATE TABLE $questions_table (
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

        // Job Applications table
        $applications_table = $wpdb->prefix . 'jam_applications';
        $applications_sql = "CREATE TABLE $applications_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            job_id mediumint(9) NOT NULL,
            personal_info text NOT NULL,
            answers text NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY job_id (job_id)
        ) $charset_collate;";

        // Plugin Settings table
        $settings_table = $wpdb->prefix . 'jam_settings';
        $settings_sql = "CREATE TABLE $settings_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            setting_key varchar(255) NOT NULL,
            setting_value text NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY setting_key (setting_key)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($jobs_sql);
        dbDelta($questions_sql);
        dbDelta($applications_sql);
        dbDelta($settings_sql);
    }

    /**
     * Get all open jobs
     * 
     * @return array Array of job objects with status 'open'
     */
    public function get_open_jobs() {
        global $wpdb;
        $table = $wpdb->prefix . 'jam_jobs';
        
        $jobs = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table WHERE status = %s ORDER BY created_at DESC",
                'open'
            )
        );

        return $jobs ? $jobs : array();
    }

    /**
     * Get a specific job by ID
     * 
     * @param int $job_id Job ID
     * @return object|null Job object or null if not found
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
     * Get questions for a specific job
     * 
     * @param int $job_id Job ID
     * @return array Array of question objects
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
     * Handle step 1 submission
     */
    public function handle_step1_submission() {
        if (!isset($_POST['submit_step1']) || !isset($_POST['personal_info_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['personal_info_nonce'], 'save_personal_info')) {
            wp_die(__('Security check failed', 'job-applicant-manager'));
        }

        $job_id = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;
        if (!$job_id || !$this->get_job($job_id)) {
            wp_die(__('Invalid job ID', 'job-applicant-manager'));
        }

        // Handle file upload
        $resume_url = '';
        if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
            $resume_url = $this->handle_resume_upload($_FILES['resume']);
        }

        // Store data in session
        $_SESSION['application_data'] = [
            'job_id' => $job_id,
            'name' => sanitize_text_field($_POST['name']),
            'email' => sanitize_email($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'cover_letter' => sanitize_textarea_field($_POST['cover_letter']),
            'resume_url' => $resume_url
        ];

        // Redirect to step 2
        wp_redirect(add_query_arg(['job_id' => $job_id, 'step' => 2], get_permalink()));
        exit;
    }

    /**
     * Modified handle_application_submission for final step
     */
    public function handle_application_submission() {
        if (!isset($_POST['submit_application']) || !isset($_POST['job_application_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['job_application_nonce'], 'submit_job_application')) {
            wp_die(__('Security check failed', 'job-applicant-manager'));
        }

        // Get stored data from session
        if (!isset($_SESSION['application_data'])) {
            wp_die(__('Application data not found. Please start over.', 'job-applicant-manager'));
        }

        $application_data = $_SESSION['application_data'];
        $answers = isset($_POST['answers']) ? $_POST['answers'] : array();

        // Save complete application
        $this->save_application($application_data['job_id'], [
            'personal_info' => $application_data,
            'answers' => $answers
        ]);

        // Clear session data
        unset($_SESSION['application_data']);

        // Redirect to thank you page
        wp_redirect(add_query_arg('application_submitted', '1', remove_query_arg(['job_id', 'step'], get_permalink())));
        exit;
    }

    /**
     * Handle resume file upload
     * 
     * @param array $file $_FILES array for the resume
     * @return string URL of uploaded file
     */
    private function handle_resume_upload($file) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $upload = wp_handle_upload($file, ['test_form' => false]);
        
        if (isset($upload['error'])) {
            wp_die($upload['error']);
        }

        return $upload['url'];
    }

    /**
     * Save job application to database
     * 
     * @param int $job_id Job ID
     * @param array $data Application data
     */
    private function save_application($job_id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'jam_applications';
        
        // Add debug logging
        error_log('Saving application for job ID: ' . $job_id);
        error_log('Application data: ' . print_r($data, true));
        
        $result = $wpdb->insert(
            $table,
            [
                'job_id' => $job_id,
                'personal_info' => json_encode($data['personal_info']),
                'answers' => json_encode($data['answers']),
                'status' => 'pending',
                'created_at' => current_time('mysql')
            ],
            ['%d', '%s', '%s', '%s', '%s']
        );
        
        // Log the result
        error_log('Application save result: ' . ($result ? 'success' : 'failed'));
        if (!$result) {
            error_log('Database error: ' . $wpdb->last_error);
        }
    }

    /**
     * Add a test job to the database
     */
    private function add_test_job() {
        global $wpdb;
        $table = $wpdb->prefix . 'jam_jobs';
        
        $wpdb->insert(
            $table,
            [
                'title' => 'Test Job Position',
                'description' => 'This is a test job description. We are looking for talented individuals to join our team.',
                'requirements' => 'Bachelor\'s degree, 2+ years experience',
                'status' => 'open',
                'created_at' => current_time('mysql')
            ],
            ['%s', '%s', '%s', '%s', '%s']
        );
        
        $job_id = $wpdb->insert_id;
        if ($job_id) {
            $this->add_test_questions($job_id);
        }
    }

    public function start_session() {
        if (!session_id()) {
            session_start();
        }
    }

    /**
     * Format question options before saving
     */
    private function format_question_options($options) {
        // If already an array, encode it
        if (is_array($options)) {
            return json_encode($options);
        }
        
        // If it's a JSON string, validate it
        $decoded = json_decode($options, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $options;
        }
        
        // If it's a comma-separated string, convert to array then encode
        $options_array = array_map('trim', explode(',', $options));
        return json_encode($options_array);
    }

    /**
     * Save job question
     */
    public function save_job_question($question) {
        global $wpdb;
        $table = $wpdb->prefix . 'jam_job_questions';
        
        // Format options if type is select
        if ($question['type'] === 'select') {
            $question['options'] = $this->format_question_options($question['options']);
        } else {
            $question['options'] = ''; // Empty string for non-select types
        }
        
        $result = $wpdb->insert(
            $table,
            [
                'job_id' => $question['job_id'],
                'question' => $question['question'],
                'type' => $question['type'],
                'options' => $question['options'],
                'required' => !empty($question['required']),
                'order_num' => isset($question['order_num']) ? $question['order_num'] : 0
            ],
            ['%d', '%s', '%s', '%s', '%d', '%d']
        );
        
        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Add test questions for a job
     */
    private function add_test_questions($job_id) {
        $questions = [
            [
                'job_id' => $job_id,
                'question' => 'What is your preferred work schedule?',
                'type' => 'select',
                'options' => ['Full-time', 'Part-time', 'Contract', 'Flexible'], // Array instead of JSON string
                'required' => true,
                'order_num' => 1
            ],
            [
                'job_id' => $job_id,
                'question' => 'Years of experience in this field',
                'type' => 'select',
                'options' => ['0-1 years', '1-3 years', '3-5 years', '5+ years'], // Array instead of JSON string
                'required' => true,
                'order_num' => 2
            ]
        ];
        
        foreach ($questions as $question) {
            $this->save_job_question($question);
        }
    }

    /**
     * Debug: Get question details
     */
    public function debug_get_question_details($job_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'jam_job_questions';
        
        $questions = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE job_id = %d",
            $job_id
        ));
        
        foreach ($questions as $question) {
            error_log(sprintf(
                "Question ID: %d, Type: %s, Options: %s",
                $question->id,
                $question->type,
                $question->options
            ));
        }
        
        return $questions;
    }

    /**
     * Get a plugin setting
     * 
     * @param string $key Setting key
     * @return string|null Setting value or null if not found
     */
    public function get_setting($key) {
        global $wpdb;
        $table = $wpdb->prefix . 'jam_settings';
        
        $value = $wpdb->get_var($wpdb->prepare(
            "SELECT setting_value FROM $table WHERE setting_key = %s",
            $key
        ));
        
        return $value;
    }

    /**
     * Save a plugin setting
     * 
     * @param string $key Setting key
     * @param string $value Setting value
     */
    public function save_setting($key, $value) {
        global $wpdb;
        $table = $wpdb->prefix . 'jam_settings';
        
        $wpdb->replace(
            $table,
            [
                'setting_key' => $key,
                'setting_value' => $value
            ],
            ['%s', '%s']
        );
    }

    /**
     * Send email notifications
     */
    public function send_notification($type, $data) {
        $admin_email = get_option('admin_email');
        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        switch ($type) {
            case 'new_application':
                // Email to admin
                $admin_subject = sprintf('New Job Application: %s', $data['position']);
                $admin_message = $this->get_email_template('admin-notification', array(
                    'applicant_name' => $data['name'],
                    'position' => $data['position'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'admin_url' => admin_url('admin.php?page=job_applications')
                ));
                
                wp_mail($admin_email, $admin_subject, $admin_message, $headers);
                
                // Email to applicant
                $applicant_subject = 'Your Job Application Has Been Received';
                $applicant_message = $this->get_email_template('applicant-confirmation', array(
                    'name' => $data['name'],
                    'position' => $data['position']
                ));
                
                wp_mail($data['email'], $applicant_subject, $applicant_message, $headers);
                break;
        }
    }

    /**
     * Get email template
     */
    private function get_email_template($template, $data) {
        ob_start();
        
        switch ($template) {
            case 'admin-notification':
                ?>
                <h2>New Job Application Received</h2>
                <p>A new application has been submitted for the position of <?php echo esc_html($data['position']); ?>.</p>
                
                <h3>Applicant Details:</h3>
                <ul>
                    <li>Name: <?php echo esc_html($data['applicant_name']); ?></li>
                    <li>Email: <?php echo esc_html($data['email']); ?></li>
                    <li>Phone: <?php echo esc_html($data['phone']); ?></li>
                </ul>
                
                <p><a href="<?php echo esc_url($data['admin_url']); ?>">View Application</a></p>
                <?php
                break;
                
            case 'applicant-confirmation':
                ?>
                <h2>Application Received</h2>
                <p>Dear <?php echo esc_html($data['name']); ?>,</p>
                
                <p>Thank you for applying for the position of <?php echo esc_html($data['position']); ?>. 
                We have received your application and will review it shortly.</p>
                
                <p>We will contact you if your qualifications match our requirements.</p>
                
                <p>Best regards,<br><?php echo esc_html(get_bloginfo('name')); ?></p>
                <?php
                break;
        }
        
        return ob_get_clean();
    }

    /**
     * Add rating functionality
     */
    public function add_rating_system() {
        global $wpdb;
        $table = $wpdb->prefix . 'jam_applications';
        
        // Add rating column if it doesn't exist
        $wpdb->query("
            ALTER TABLE $table 
            ADD COLUMN IF NOT EXISTS rating TINYINT DEFAULT 0,
            ADD COLUMN IF NOT EXISTS rating_notes TEXT
        ");
    }

    /**
     * Update application rating
     */
    public function update_rating() {
        if (!isset($_POST['update_rating']) || !isset($_POST['rating_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['rating_nonce'], 'update_application_rating')) {
            return;
        }

        $application_id = absint($_POST['application_id']);
        $rating = intval($_POST['rating']);
        $rating_notes = sanitize_textarea_field($_POST['rating_notes']);

        global $wpdb;
        $table = $wpdb->prefix . 'jam_applications';
        
        $wpdb->update(
            $table,
            [
                'rating' => $rating,
                'rating_notes' => $rating_notes
            ],
            ['id' => $application_id],
            ['%d', '%s'],
            ['%d']
        );

        wp_safe_redirect(add_query_arg('rated', '1', admin_url('admin.php?page=job_applications&action=view&id=' . $application_id)));
        exit;
    }

    /**
     * Verify database tables
     */
    public function verify_tables() {
        global $wpdb;
        $applications_table = $wpdb->prefix . 'jam_applications';
        
        // Check if table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$applications_table'") === $applications_table;
        error_log('Applications table exists: ' . ($table_exists ? 'yes' : 'no'));
        
        if ($table_exists) {
            // Check table structure
            $columns = $wpdb->get_results("SHOW COLUMNS FROM $applications_table");
            error_log('Applications table columns: ' . print_r($columns, true));
        }
    }
} 