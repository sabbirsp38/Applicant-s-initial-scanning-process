<?php
if (!defined('ABSPATH')) {
    exit;
}

$application_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
$application = $this->get_application($application_id);

if (!$application) {
    wp_die(__('Application not found', 'job-applicant-manager'));
}

$personal_info = json_decode($application->personal_info, true);
$answers = json_decode($application->answers, true);
$job = $this->get_job($application->job_id);
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php printf(__('Application Details - %s', 'job-applicant-manager'), esc_html($personal_info['name'] ?? '')); ?>
    </h1>
    
    <a href="<?php echo esc_url(admin_url('admin.php?page=job_applications')); ?>" class="page-title-action">
        <?php _e('← Back to Applications', 'job-applicant-manager'); ?>
    </a>

    <div class="application-details">
        <style>
            .application-details {
                margin-top: 20px;
                background: #fff;
                padding: 20px;
                border: 1px solid #ccd0d4;
                box-shadow: 0 1px 1px rgba(0,0,0,.04);
            }
            .detail-section {
                margin-bottom: 30px;
            }
            .detail-section h2 {
                border-bottom: 1px solid #eee;
                padding-bottom: 10px;
                margin-bottom: 20px;
            }
            .detail-row {
                margin-bottom: 15px;
            }
            .detail-label {
                font-weight: bold;
                margin-bottom: 5px;
            }
            .detail-value {
                background: #f8f9fa;
                padding: 10px;
                border-radius: 4px;
            }
            .status-controls {
                margin-top: 20px;
                padding-top: 20px;
                border-top: 1px solid #eee;
            }
            .resume-link {
                display: inline-block;
                margin-top: 5px;
                color: #0073aa;
                text-decoration: none;
            }
            .resume-link:hover {
                color: #00a0d2;
            }
            .rating-stars {
                display: inline-block;
                direction: rtl;
                margin-bottom: 20px;
            }
            .rating-stars input {
                display: none;
            }
            .rating-stars label {
                font-size: 30px;
                color: #ddd;
                cursor: pointer;
                display: inline-block;
                padding: 0 5px;
            }
            .rating-stars label:hover,
            .rating-stars label:hover ~ label,
            .rating-stars input:checked ~ label {
                color: #ffd700;
            }
            .rating-notes {
                margin-bottom: 20px;
            }
            .rating-notes label {
                display: block;
                margin-bottom: 5px;
                font-weight: bold;
            }
        </style>

        <!-- Personal Information Section -->
        <div class="detail-section">
            <h2><?php _e('Personal Information', 'job-applicant-manager'); ?></h2>
            
            <div class="detail-row">
                <div class="detail-label"><?php _e('Name', 'job-applicant-manager'); ?></div>
                <div class="detail-value"><?php echo esc_html($personal_info['name'] ?? ''); ?></div>
            </div>

            <div class="detail-row">
                <div class="detail-label"><?php _e('Email', 'job-applicant-manager'); ?></div>
                <div class="detail-value"><?php echo esc_html($personal_info['email'] ?? ''); ?></div>
            </div>

            <div class="detail-row">
                <div class="detail-label"><?php _e('Phone', 'job-applicant-manager'); ?></div>
                <div class="detail-value"><?php echo esc_html($personal_info['phone'] ?? ''); ?></div>
            </div>

            <?php if (!empty($personal_info['resume_url'])): ?>
            <div class="detail-row">
                <div class="detail-label"><?php _e('Resume/CV', 'job-applicant-manager'); ?></div>
                <div class="detail-value">
                    <a href="<?php echo esc_url($personal_info['resume_url']); ?>" 
                       class="resume-link" 
                       target="_blank">
                        <?php _e('Download Resume', 'job-applicant-manager'); ?>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($personal_info['cover_letter'])): ?>
            <div class="detail-row">
                <div class="detail-label"><?php _e('Cover Letter', 'job-applicant-manager'); ?></div>
                <div class="detail-value"><?php echo nl2br(esc_html($personal_info['cover_letter'])); ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Job Information Section -->
        <div class="detail-section">
            <h2><?php _e('Job Information', 'job-applicant-manager'); ?></h2>
            
            <div class="detail-row">
                <div class="detail-label"><?php _e('Position', 'job-applicant-manager'); ?></div>
                <div class="detail-value"><?php echo esc_html($job ? $job->title : __('Unknown Position', 'job-applicant-manager')); ?></div>
            </div>

            <div class="detail-row">
                <div class="detail-label"><?php _e('Application Date', 'job-applicant-manager'); ?></div>
                <div class="detail-value">
                    <?php echo esc_html(mysql2date(get_option('date_format') . ' ' . get_option('time_format'), $application->created_at)); ?>
                </div>
            </div>
        </div>

        <!-- Interview Questions Section -->
        <?php if (!empty($answers)): ?>
        <div class="detail-section">
            <h2><?php _e('Interview Questions', 'job-applicant-manager'); ?></h2>
            
            <?php 
            $questions = $this->get_job_questions($application->job_id);
            foreach ($questions as $question):
                $answer = $answers[$question->id] ?? '';
                if (empty($answer)) continue;
            ?>
                <div class="detail-row">
                    <div class="detail-label"><?php echo esc_html($question->question); ?></div>
                    <div class="detail-value"><?php echo nl2br(esc_html($answer)); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Status Controls -->
        <div class="status-controls">
            <form method="post" action="">
                <?php wp_nonce_field('update_application_status', 'status_nonce'); ?>
                <input type="hidden" name="application_id" value="<?php echo esc_attr($application_id); ?>">
                
                <select name="application_status" class="status-select">
                    <option value="pending" <?php selected($application->status, 'pending'); ?>>
                        <?php _e('Pending', 'job-applicant-manager'); ?>
                    </option>
                    <option value="approved" <?php selected($application->status, 'approved'); ?>>
                        <?php _e('Approved', 'job-applicant-manager'); ?>
                    </option>
                    <option value="rejected" <?php selected($application->status, 'rejected'); ?>>
                        <?php _e('Rejected', 'job-applicant-manager'); ?>
                    </option>
                </select>
                
                <button type="submit" name="update_status" class="button button-primary">
                    <?php _e('Update Status', 'job-applicant-manager'); ?>
                </button>
            </form>
        </div>

    </div>
</div> 