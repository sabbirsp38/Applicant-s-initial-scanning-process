<?php
if (!defined('ABSPATH')) {
    exit;
}

$settings = $this->get_settings();
?>

<div class="wrap">
    <h1><?php echo esc_html__('Job Manager Settings', 'job-applicant-manager'); ?></h1>

    <form method="post" action="">
        <?php wp_nonce_field('save_settings', 'settings_nonce'); ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="notification_email"><?php echo esc_html__('Notification Email', 'job-applicant-manager'); ?></label>
                </th>
                <td>
                    <input type="email" name="notification_email" id="notification_email" class="regular-text" 
                           value="<?php echo esc_attr($settings['notification_email'] ?? get_option('admin_email')); ?>">
                    <p class="description">
                        <?php echo esc_html__('Email address to receive application notifications', 'job-applicant-manager'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="form_title"><?php echo esc_html__('Form Title', 'job-applicant-manager'); ?></label>
                </th>
                <td>
                    <input type="text" name="form_title" id="form_title" class="regular-text" 
                           value="<?php echo esc_attr($settings['form_title'] ?? __('Job Application Form', 'job-applicant-manager')); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="primary_color"><?php echo esc_html__('Primary Color', 'job-applicant-manager'); ?></label>
                </th>
                <td>
                    <input type="color" name="primary_color" id="primary_color" 
                           value="<?php echo esc_attr($settings['primary_color'] ?? '#4CAF50'); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="success_message"><?php echo esc_html__('Success Message', 'job-applicant-manager'); ?></label>
                </th>
                <td>
                    <textarea name="success_message" id="success_message" class="large-text" rows="3"><?php 
                        echo esc_textarea($settings['success_message'] ?? __('Thank you for your application!', 'job-applicant-manager')); 
                    ?></textarea>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit" name="submit_settings" class="button button-primary" 
                   value="<?php echo esc_attr__('Save Settings', 'job-applicant-manager'); ?>">
        </p>
    </form>
</div> 