<?php
/**
 * Admin responses page template
 *
 * @package JobApplicantManager
 */

if (!defined('ABSPATH')) {
    exit;
}

$delete_nonce = wp_create_nonce('delete_applicant_nonce');
$applicants = $this->get_applicants();
?>

<div class="wrap">
    <h1><?php echo esc_html__('Job Applicant Responses', 'job-applicant-manager'); ?></h1>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php echo esc_html__('ID', 'job-applicant-manager'); ?></th>
                <th><?php echo esc_html__('Submission Time & Date', 'job-applicant-manager'); ?></th>
                <th><?php echo esc_html__('Name', 'job-applicant-manager'); ?></th>
                <th><?php echo esc_html__('Email', 'job-applicant-manager'); ?></th>
                <th><?php echo esc_html__('Actions', 'job-applicant-manager'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($applicants) : ?>
                <?php foreach ($applicants as $applicant) : ?>
                    <tr>
                        <td><?php echo esc_html($applicant['id']); ?></td>
                        <td><?php echo esc_html($applicant['timeanddate']); ?></td>
                        <td><?php echo esc_html($applicant['name']); ?></td>
                        <td><?php echo esc_html($applicant['email']); ?></td>
                        <td>
                            <a href="<?php echo esc_url(add_query_arg(array('action' => 'delete_applicant', 'applicant_id' => $applicant['id'], '_wpnonce' => $delete_nonce))); ?>" 
                               class="button button-small button-link-delete">
                                <?php echo esc_html__('Delete', 'job-applicant-manager'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5"><?php echo esc_html__('No applicants found.', 'job-applicant-manager'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div> 