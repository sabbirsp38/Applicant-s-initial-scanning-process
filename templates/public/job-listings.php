<?php
if (!defined('ABSPATH')) {
    exit;
}

$jobs = $this->get_open_jobs();
?>

<div class="job-listings-container">
    <style>
        .job-listings-container {
            padding: 20px;
        }
        .job-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .job-title {
            color: #333;
            font-size: 24px;
            margin-bottom: 15px;
        }
        .job-description {
            color: #666;
            margin-bottom: 20px;
        }
        .apply-button {
            display: inline-block;
            background: #00bcd9;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .apply-button:hover {
            background: #008c9e;
            color: #fff;
            text-decoration: none;
        }
    </style>

    <?php if (empty($jobs)): ?>
        <p><?php _e('No open positions at this time.', 'job-applicant-manager'); ?></p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($jobs as $job): ?>
                <div class="col-md-6">
                    <div class="job-card">
                        <h3 class="job-title"><?php echo esc_html($job->title); ?></h3>
                        <div class="job-description">
                            <?php echo wp_kses_post(wp_trim_words($job->description, 20)); ?>
                        </div>
                        <a href="<?php echo esc_url(add_query_arg('job_id', $job->id, get_permalink())); ?>" 
                           class="apply-button">
                            <?php _e('Apply Now', 'job-applicant-manager'); ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div> 