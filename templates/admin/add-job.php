<?php
if (!defined('ABSPATH')) {
    exit;
}

$job_id = isset($_GET['job_id']) ? absint($_GET['job_id']) : 0;
$job = $job_id ? $this->get_job($job_id) : null;
$questions = $job_id ? $this->get_job_questions($job_id) : array();
?>

<div class="wrap">
    <h1><?php echo $job_id ? esc_html__('Edit Job', 'job-applicant-manager') : esc_html__('Add New Job', 'job-applicant-manager'); ?></h1>

    <form method="post" action="" class="jam-job-form">
        <?php wp_nonce_field('save_job', 'job_nonce'); ?>
        <?php if ($job_id) : ?>
            <input type="hidden" name="job_id" value="<?php echo esc_attr($job_id); ?>">
        <?php endif; ?>

        <div class="jam-form-section">
            <h2><?php echo esc_html__('Job Details', 'job-applicant-manager'); ?></h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="job_title"><?php echo esc_html__('Job Title', 'job-applicant-manager'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="job_title" id="job_title" class="regular-text" 
                               value="<?php echo $job ? esc_attr($job->title) : ''; ?>" required>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="job_description"><?php echo esc_html__('Job Description', 'job-applicant-manager'); ?></label>
                    </th>
                    <td>
                        <?php 
                        wp_editor(
                            $job ? $job->description : '',
                            'job_description',
                            array(
                                'media_buttons' => false,
                                'textarea_rows' => 10,
                                'teeny' => true
                            )
                        );
                        ?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="jam-form-section">
            <h2>
                <?php echo esc_html__('Interview Questions', 'job-applicant-manager'); ?>
                <button type="button" class="page-title-action" id="add-question">
                    <?php echo esc_html__('Add Question', 'job-applicant-manager'); ?>
                </button>
            </h2>
            
            <div id="interview-questions">
                <?php if ($questions) : ?>
                    <?php foreach ($questions as $index => $question) : ?>
                        <div class="question-item">
                            <div class="question-header">
                                <h3><?php echo esc_html__('Question', 'job-applicant-manager'); ?> #<?php echo $index + 1; ?></h3>
                                <button type="button" class="button remove-question">
                                    <?php echo esc_html__('Remove', 'job-applicant-manager'); ?>
                                </button>
                            </div>
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="question_<?php echo $index; ?>">
                                            <?php echo esc_html__('Question Text', 'job-applicant-manager'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="text" name="questions[<?php echo $index; ?>][text]" 
                                               id="question_<?php echo $index; ?>" class="regular-text" 
                                               value="<?php echo esc_attr($question->question); ?>" required>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="question_type_<?php echo $index; ?>">
                                            <?php echo esc_html__('Question Type', 'job-applicant-manager'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <select name="questions[<?php echo $index; ?>][type]" 
                                                id="question_type_<?php echo $index; ?>" class="question-type">
                                            <option value="text" <?php selected($question->type, 'text'); ?>>
                                                <?php echo esc_html__('Text Answer', 'job-applicant-manager'); ?>
                                            </option>
                                            <option value="select" <?php selected($question->type, 'select'); ?>>
                                                <?php echo esc_html__('Multiple Choice', 'job-applicant-manager'); ?>
                                            </option>
                                            <option value="file" <?php selected($question->type, 'file'); ?>>
                                                <?php echo esc_html__('File Upload', 'job-applicant-manager'); ?>
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr class="options-row" <?php echo $question->type !== 'select' ? 'style="display: none;"' : ''; ?>>
                                    <th scope="row">
                                        <label for="question_options_<?php echo $index; ?>">
                                            <?php echo esc_html__('Options', 'job-applicant-manager'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <textarea name="questions[<?php echo $index; ?>][options]" 
                                                  id="question_options_<?php echo $index; ?>" 
                                                  class="large-text" rows="4" 
                                                  placeholder="<?php echo esc_attr__('Enter each option on a new line', 'job-applicant-manager'); ?>"
                                        ><?php echo esc_textarea($question->options); ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="question_required_<?php echo $index; ?>">
                                            <?php echo esc_html__('Required', 'job-applicant-manager'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="checkbox" name="questions[<?php echo $index; ?>][required]" 
                                               id="question_required_<?php echo $index; ?>" value="1" 
                                               <?php checked($question->required, 1); ?>>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <p class="submit">
            <input type="submit" name="submit_job" class="button button-primary" 
                   value="<?php echo $job_id ? esc_attr__('Update Job', 'job-applicant-manager') : esc_attr__('Publish Job', 'job-applicant-manager'); ?>">
        </p>
    </form>
</div>

<!-- Question Template -->
<script type="text/html" id="question-template">
    <div class="question-item">
        <div class="question-header">
            <h3><?php echo esc_html__('Question', 'job-applicant-manager'); ?> #{{number}}</h3>
            <button type="button" class="button remove-question">
                <?php echo esc_html__('Remove', 'job-applicant-manager'); ?>
            </button>
        </div>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="question_{{number}}">
                        <?php echo esc_html__('Question Text', 'job-applicant-manager'); ?>
                    </label>
                </th>
                <td>
                    <input type="text" name="questions[{{number}}][text]" 
                           id="question_{{number}}" class="regular-text" required>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="question_type_{{number}}">
                        <?php echo esc_html__('Question Type', 'job-applicant-manager'); ?>
                    </label>
                </th>
                <td>
                    <select name="questions[{{number}}][type]" 
                            id="question_type_{{number}}" class="question-type">
                        <option value="text"><?php echo esc_html__('Text Answer', 'job-applicant-manager'); ?></option>
                        <option value="select"><?php echo esc_html__('Multiple Choice', 'job-applicant-manager'); ?></option>
                        <option value="file"><?php echo esc_html__('File Upload', 'job-applicant-manager'); ?></option>
                    </select>
                </td>
            </tr>
            <tr class="options-row" style="display: none;">
                <th scope="row">
                    <label for="question_options_{{number}}">
                        <?php echo esc_html__('Options', 'job-applicant-manager'); ?>
                    </label>
                </th>
                <td>
                    <textarea name="questions[{{number}}][options]" 
                              id="question_options_{{number}}" 
                              class="large-text" rows="4" 
                              placeholder="<?php echo esc_attr__('Enter each option on a new line', 'job-applicant-manager'); ?>"></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="question_required_{{number}}">
                        <?php echo esc_html__('Required', 'job-applicant-manager'); ?>
                    </label>
                </th>
                <td>
                    <input type="checkbox" name="questions[{{number}}][required]" 
                           id="question_required_{{number}}" value="1">
                </td>
            </tr>
        </table>
    </div>
</script>

<style>
.jam-form-section {
    margin: 20px 0;
    padding: 20px;
    background: #fff;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.question-item {
    margin: 20px 0;
    padding: 20px;
    background: #f9f9f9;
    border: 1px solid #e5e5e5;
}

.question-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.question-header h3 {
    margin: 0;
}

.error {
    border-color: #dc3232 !important;
}

#add-question {
    margin-left: 10px;
    cursor: pointer;
}

.remove-question {
    color: #dc3232;
    cursor: pointer;
}

.options-row {
    display: none;
}

.question-type {
    min-width: 200px;
}
</style>

<script type="text/javascript">
jQuery(document).ready(function($) {
    console.log('Page loaded');
    console.log('Template exists:', $('#question-template').length > 0);
    console.log('Add question button exists:', $('#add-question').length > 0);
    
    // Test click handler
    $('#add-question').on('click', function() {
        console.log('Add question clicked');
    });
});
</script>

<?php
// Debug output
echo "<!-- Debug Info:\n";
echo "Plugin URL: " . JAM_PLUGIN_URL . "\n";
echo "Admin JS URL: " . JAM_PLUGIN_URL . "assets/js/admin.js\n";
echo "-->";
?> 