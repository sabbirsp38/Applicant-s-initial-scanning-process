<?php
/**
 * Form handling functionality
 *
 * @package JobApplicantManager
 */

if (!defined('ABSPATH')) {
    exit;
}

class Job_Applicant_Form {
    /**
     * Initialize form handling.
     */
    public function __construct() {
        add_action('init', array($this, 'handle_form_submission'));
        add_action('wp_ajax_get_job_questions', array($this, 'get_job_questions_ajax'));
        add_action('wp_ajax_nopriv_get_job_questions', array($this, 'get_job_questions_ajax'));
    }

    /**
     * Handle form submission.
     */
    public function handle_form_submission() {
        if (!isset($_POST['application_nonce']) || !wp_verify_nonce($_POST['application_nonce'], 'submit_application')) {
            return;
        }

        $step = isset($_POST['step']) ? absint($_POST['step']) : 1;

        if ($step === 1) {
            $this->handle_step_one();
        } elseif ($step === 2) {
            $this->handle_step_two();
        }
    }

    /**
     * Handle step one submission.
     */
    private function handle_step_one() {
        if (!isset($_POST['next_step'])) {
            return;
        }

        // Store personal info in session
        $personal_info = array(
            'name' => sanitize_text_field($_POST['name']),
            'email' => sanitize_email($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone'])
        );

        // Handle resume upload
        if (isset($_FILES['resume'])) {
            $resume = $this->handle_file_upload('resume');
            if (is_wp_error($resume)) {
                // Handle error
                return;
            }
            $personal_info['resume'] = $resume;
        }

        // Store in session
        WC()->session->set('job_application_personal_info', $personal_info);

        // Redirect to step 2
        wp_redirect(add_query_arg('step', 2));
        exit;
    }

    /**
     * Handle step two submission.
     */
    private function handle_step_two() {
        if (!isset($_POST['submit_application'])) {
            return;
        }

        $job_id = absint($_POST['job_id']);
        $personal_info = WC()->session->get('job_application_personal_info');

        if (!$personal_info) {
            wp_redirect(remove_query_arg('step'));
            exit;
        }

        // Handle answers
        $answers = array();
        if (isset($_POST['answers']) && is_array($_POST['answers'])) {
            foreach ($_POST['answers'] as $question_id => $answer) {
                if (is_array($answer)) {
                    $answers[$question_id] = array_map('sanitize_text_field', $answer);
                } else {
                    $answers[$question_id] = sanitize_text_field($answer);
                }
            }
        }

        // Handle file uploads in answers
        if (isset($_FILES['answers'])) {
            foreach ($_FILES['answers']['name'] as $question_id => $filename) {
                if (!empty($filename)) {
                    $file = $this->handle_file_upload('answers', $question_id);
                    if (!is_wp_error($file)) {
                        $answers[$question_id] = $file;
                    }
                }
            }
        }

        // Save application
        $this->save_application($job_id, $personal_info, $answers);

        // Clear session
        WC()->session->set('job_application_personal_info', null);

        // Send notification email
        $this->send_notification_email($job_id, $personal_info, $answers);

        // Redirect to thank you page
        wp_redirect(home_url('/thank-you/'));
        exit;
    }

    /**
     * Handle file upload.
     */
    private function handle_file_upload($field_name, $index = null) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // Setup upload directory
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['path'] . '/job-applications';
        if (!file_exists($upload_path)) {
            wp_mkdir_p($upload_path);
        }

        // Get file info
        $file = $index !== null ? $_FILES[$field_name]['name'][$index] : $_FILES[$field_name]['name'];
        $file_tmp = $index !== null ? $_FILES[$field_name]['tmp_name'][$index] : $_FILES[$field_name]['tmp_name'];

        // Generate unique filename
        $filename = wp_unique_filename($upload_path, $file);
        $new_file = $upload_path . '/' . $filename;

        // Move uploaded file
        if (@move_uploaded_file($file_tmp, $new_file)) {
            $file_url = $upload_dir['url'] . '/job-applications/' . $filename;
            return array(
                'file' => $new_file,
                'url' => $file_url,
                'name' => $filename
            );
        }

        return new WP_Error('upload_error', __('Failed to upload file.', 'job-applicant-manager'));
    }

    /**
     * Save application to database.
     */
    private function save_application($job_id, $personal_info, $answers) {
        global $wpdb;
        $table = $wpdb->prefix . 'jam_applications';

        $wpdb->insert(
            $table,
            array(
                'job_id' => $job_id,
                'personal_info' => maybe_serialize($personal_info),
                'answers' => maybe_serialize($answers),
                'status' => 'pending',
                'created_at' => current_time('mysql')
            ),
            array('%d', '%s', '%s', '%s', '%s')
        );

        return $wpdb->insert_id;
    }

    /**
     * Send notification email.
     */
    private function send_notification_email($job_id, $personal_info, $answers) {
        $settings = get_option('jam_settings', array());
        $to = isset($settings['notification_email']) ? $settings['notification_email'] : get_option('admin_email');
        $job = $this->get_job($job_id);

        $subject = sprintf(__('New Job Application: %s', 'job-applicant-manager'), $job->title);

        $message = sprintf(
            __('New application received for %s position.

Applicant Details:
Name: %s
Email: %s
Phone: %s

Please check admin panel for complete application details.', 'job-applicant-manager'),
            $job->title,
            $personal_info['name'],
            $personal_info['email'],
            $personal_info['phone']
        );

        wp_mail($to, $subject, $message);
    }

    public function get_job_questions_ajax() {
        $team = isset($_POST['team']) ? sanitize_text_field($_POST['team']) : '';
        $department = isset($_POST['department']) ? sanitize_text_field($_POST['department']) : '';
        $experience = isset($_POST['experience']) ? sanitize_text_field($_POST['experience']) : '';

        global $wpdb;
        $table_name = $wpdb->prefix . 'job_tasks';
        
        $task = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE team = %s AND department = %s AND experience = %s",
            $team,
            $department,
            $experience
        ));

        if ($task) {
            wp_send_json_success(array('task' => $task->task));
        } else {
            wp_send_json_error();
        }
    }
} 