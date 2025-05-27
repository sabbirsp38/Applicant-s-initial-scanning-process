<?php
if (!defined('ABSPATH')) {
    exit;
}

$jobs = $this->get_jobs();
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html__('Jobs', 'job-applicant-manager'); ?></h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=add_new_job')); ?>" class="page-title-action">
        <?php echo esc_html__('Add New', 'job-applicant-manager'); ?>
    </a>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php echo esc_html__('Title', 'job-applicant-manager'); ?></th>
                <th><?php echo esc_html__('Applications', 'job-applicant-manager'); ?></th>
                <th><?php echo esc_html__('Status', 'job-applicant-manager'); ?></th>
                <th><?php echo esc_html__('Created', 'job-applicant-manager'); ?></th>
                <th><?php echo esc_html__('Actions', 'job-applicant-manager'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($jobs) : ?>
                <?php foreach ($jobs as $job) : ?>
                    <tr>
                        <td>
                            <strong>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=add_new_job&job_id=' . $job->id)); ?>">
                                    <?php echo esc_html($job->title); ?>
                                </a>
                            </strong>
                        </td>
                        <td>
                            <?php 
                            $count = $this->get_applications_count($job->id);
                            echo esc_html($count);
                            ?>
                        </td>
                        <td><?php echo esc_html(ucfirst($job->status)); ?></td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($job->created_at))); ?></td>
                        <td>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=add_new_job&job_id=' . $job->id)); ?>" class="button button-small">
                                <?php echo esc_html__('Edit', 'job-applicant-manager'); ?>
                            </a>
                            <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=job_manager&action=delete_job&job_id=' . $job->id), 'delete_job_' . $job->id)); ?>" 
                               class="button button-small button-link-delete" 
                               onclick="return confirm('<?php echo esc_js(__('Are you sure you want to delete this job?', 'job-applicant-manager')); ?>');">
                                <?php echo esc_html__('Delete', 'job-applicant-manager'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5"><?php echo esc_html__('No jobs found.', 'job-applicant-manager'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div> 