<?php
/**
 * Thank you page template
 *
 * @package JobApplicantManager
 */

if (!defined('ABSPATH')) {
    exit;
}

$thank_you_message = $this->get_setting('thank_you_message') ?: __('Thank you for your application! We will review your submission and contact you soon.', 'job-applicant-manager');
?>

<div class="thank-you-container">
    <style>
        .thank-you-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            text-align: center;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .thank-you-icon {
            color: #00bcd9;
            font-size: 48px;
            margin-bottom: 20px;
        }
        .thank-you-message {
            font-size: 18px;
            color: #333;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .back-to-jobs {
            display: inline-block;
            padding: 10px 20px;
            background: #00bcd9;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .back-to-jobs:hover {
            background: #008c9e;
            color: #fff;
        }
    </style>

    <div class="thank-you-icon">✓</div>
    <div class="thank-you-message">
        <?php echo wp_kses_post($thank_you_message); ?>
    </div>
    <a href="<?php echo esc_url(remove_query_arg(['job_id', 'step', 'application_submitted'])); ?>" class="back-to-jobs">
        <?php _e('View More Jobs', 'job-applicant-manager'); ?>
    </a>
</div> 