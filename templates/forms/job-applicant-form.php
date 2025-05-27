<?php
/**
 * Job applicant form template
 *
 * @package JobApplicantManager
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="jam-form-container">
    <div class="jam-form-header">
        <h2><?php echo esc_html__('Job Application Form', 'job-applicant-manager'); ?></h2>
        <p><?php echo esc_html__('Please fill out the form below to apply', 'job-applicant-manager'); ?></p>
    </div>

    <form method="post" action="" class="jam-form">
        <?php wp_nonce_field('save_job_applicant', 'job_applicant_nonce'); ?>

        <div class="jam-form-group">
            <label for="name"><?php echo esc_html__('Full Name', 'job-applicant-manager'); ?></label>
            <input type="text" name="name" id="name" class="jam-form-control" required>
        </div>

        <div class="jam-form-group">
            <label for="email"><?php echo esc_html__('Email Address', 'job-applicant-manager'); ?></label>
            <input type="email" name="email" id="email" class="jam-form-control" required>
        </div>

        <div class="jam-form-group">
            <label for="team"><?php echo esc_html__('Select Team', 'job-applicant-manager'); ?></label>
            <select name="team" id="team" class="jam-form-control jam-form-select" required>
                <option value=""><?php echo esc_html__('Choose a team...', 'job-applicant-manager'); ?></option>
                <option value="Digital Marketing"><?php echo esc_html__('Digital Marketing', 'job-applicant-manager'); ?></option>
                <option value="Web development"><?php echo esc_html__('Web Development', 'job-applicant-manager'); ?></option>
                <option value="Graphic Design"><?php echo esc_html__('Graphic Design', 'job-applicant-manager'); ?></option>
                <option value="Software development"><?php echo esc_html__('Software Development', 'job-applicant-manager'); ?></option>
                <option value="content writing"><?php echo esc_html__('Content Writing', 'job-applicant-manager'); ?></option>
                <option value="others"><?php echo esc_html__('Others', 'job-applicant-manager'); ?></option>
            </select>
        </div>

        <div id="otherdepartment-field" class="jam-form-group jam-conditional-field">
            <label for="otherdepartment"><?php echo esc_html__('Specify Other Department', 'job-applicant-manager'); ?></label>
            <input type="text" name="otherdepartment" id="otherdepartment" class="jam-form-control">
        </div>

        <div class="jam-form-group">
            <label for="department"><?php echo esc_html__('Select Department', 'job-applicant-manager'); ?></label>
            <select name="department" id="department" class="jam-form-control jam-form-select" required>
                <option value=""><?php echo esc_html__('Choose a department...', 'job-applicant-manager'); ?></option>
                <option value="Sales"><?php echo esc_html__('Sales', 'job-applicant-manager'); ?></option>
                <option value="Project management"><?php echo esc_html__('Project Management', 'job-applicant-manager'); ?></option>
                <option value="Technical team"><?php echo esc_html__('Technical Team', 'job-applicant-manager'); ?></option>
                <option value="Quality control"><?php echo esc_html__('Quality Control', 'job-applicant-manager'); ?></option>
                <option value="Research and development"><?php echo esc_html__('Research and Development', 'job-applicant-manager'); ?></option>
            </select>
        </div>

        <div class="jam-form-group">
            <label for="experience"><?php echo esc_html__('Years of Experience', 'job-applicant-manager'); ?></label>
            <select name="experience" id="experience" class="jam-form-control jam-form-select" required>
                <option value=""><?php echo esc_html__('Select experience...', 'job-applicant-manager'); ?></option>
                <option value="0-1">0-1 <?php echo esc_html__('years', 'job-applicant-manager'); ?></option>
                <option value="1-3">1-3 <?php echo esc_html__('years', 'job-applicant-manager'); ?></option>
                <option value="4-5">4-5 <?php echo esc_html__('years', 'job-applicant-manager'); ?></option>
                <option value="5-8">5-8 <?php echo esc_html__('years', 'job-applicant-manager'); ?></option>
                <option value="8+">8+ <?php echo esc_html__('years', 'job-applicant-manager'); ?></option>
            </select>
        </div>

        <div id="task-container" class="jam-task-container" style="display: none;">
            <h3 class="jam-task-title"><?php echo esc_html__('Assignment', 'job-applicant-manager'); ?></h3>
            <div id="task-description" class="jam-task-description"></div>
            <input type="hidden" name="assingedtask" id="assingedtask">
            
            <div class="jam-form-group jam-response-area">
                <label for="taskrespond"><?php echo esc_html__('Your Response', 'job-applicant-manager'); ?></label>
                <textarea name="taskrespond" id="taskrespond" class="jam-form-control jam-form-textarea" required></textarea>
            </div>
        </div>

        <div class="jam-form-group">
            <button type="submit" name="submit_form1" class="jam-btn jam-btn-primary">
                <?php echo esc_html__('Submit Application', 'job-applicant-manager'); ?>
            </button>
        </div>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Show/hide other department field
    $('#team').on('change', function() {
        if ($(this).val() === 'others') {
            $('#otherdepartment-field').show();
        } else {
            $('#otherdepartment-field').hide();
        }
    });

    // Load task based on selections
    function loadTask() {
        var team = $('#team').val();
        var department = $('#department').val();
        var experience = $('#experience').val();

        if (team && department && experience) {
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'get_task',
                    team: team,
                    department: department,
                    experience: experience
                },
                success: function(response) {
                    if (response.success && response.data) {
                        $('#task-description').html(response.data.task);
                        $('#assingedtask').val(response.data.task);
                        $('#task-container').show();
                    } else {
                        $('#task-container').hide();
                    }
                }
            });
        }
    }

    $('#team, #department, #experience').on('change', loadTask);
});
</script> 