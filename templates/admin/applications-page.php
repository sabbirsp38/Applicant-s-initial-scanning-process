<?php
if (!defined('ABSPATH')) {
    exit;
}

$jobs = $this->get_jobs();
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Job Applications', 'job-applicant-manager'); ?></h1>

    <?php
    // Show success message if application was deleted
    if (isset($_GET['deleted'])) {
        echo '<div class="notice notice-success"><p>' . __('Application deleted successfully.', 'job-applicant-manager') . '</p></div>';
    }
    ?>

    <div class="jam-filters">
        <form method="get">
            <input type="hidden" name="page" value="job_applications">
            <select name="job_id" class="jam-select">
                <option value=""><?php echo esc_html__('All Jobs', 'job-applicant-manager'); ?></option>
                <?php foreach ($jobs as $job) : ?>
                    <option value="<?php echo esc_attr($job->id); ?>" <?php selected($job_id, $job->id); ?>>
                        <?php echo esc_html($job->title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="submit" class="button" value="<?php echo esc_attr__('Filter', 'job-applicant-manager'); ?>">
        </form>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Name', 'job-applicant-manager'); ?></th>
                <th><?php _e('Email', 'job-applicant-manager'); ?></th>
                <th><?php _e('Phone', 'job-applicant-manager'); ?></th>
                <th><?php _e('Job Position', 'job-applicant-manager'); ?></th>
                <th><?php _e('Applied Date', 'job-applicant-manager'); ?></th>
                <th><?php _e('Status', 'job-applicant-manager'); ?></th>
                <th><?php _e('Actions', 'job-applicant-manager'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($applications)):
                foreach ($applications as $application):
                    // Decode the JSON data
                    $personal_info = json_decode($application->personal_info, true);
                    $answers = json_decode($application->answers, true);
                    
                    // Get job details
                    $job = $this->get_job($application->job_id);
                    $job_title = $job ? $job->title : __('Unknown Position', 'job-applicant-manager');
                    
                    // Format the date
                    $applied_date = mysql2date(get_option('date_format'), $application->created_at);
            ?>
                <tr>
                    <td><?php echo esc_html($personal_info['name'] ?? ''); ?></td>
                    <td><?php echo esc_html($personal_info['email'] ?? ''); ?></td>
                    <td><?php echo esc_html($personal_info['phone'] ?? ''); ?></td>
                    <td><?php echo esc_html($job_title); ?></td>
                    <td><?php echo esc_html($applied_date); ?></td>
                    <td>
                        <span class="status-<?php echo esc_attr($application->status); ?>">
                            <?php echo esc_html(ucfirst($application->status)); ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=job_applications&action=view&id=' . $application->id)); ?>" 
                           class="button button-small">
                            <?php _e('View Details', 'job-applicant-manager'); ?>
                        </a>
                        
                        <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=job_applications&action=delete&id=' . $application->id), 'delete_application_' . $application->id); ?>" 
                           class="button button-small button-link-delete" 
                           onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete this application?', 'job-applicant-manager'); ?>');">
                            <?php _e('Delete', 'job-applicant-manager'); ?>
                        </a>
                    </td>
                </tr>
            <?php
                endforeach;
            else:
            ?>
                <tr>
                    <td colspan="7"><?php _e('No applications found.', 'job-applicant-manager'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <style>
        .status-pending {
            color: #f0ad4e;
            font-weight: bold;
        }
        .status-approved {
            color: #5cb85c;
            font-weight: bold;
        }
        .status-rejected {
            color: #d9534f;
            font-weight: bold;
        }
        .button-link-delete {
            color: #dc3545;
            margin-left: 5px;
        }
        .button-link-delete:hover {
            color: #bd2130;
        }
        .jam-filters {
            margin: 20px 0;
            padding: 15px;
            background: #fff;
            border: 1px solid #ccd0d4;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
        }

        .jam-select {
            min-width: 200px;
            margin-right: 10px;
        }
    </style>
</div> 