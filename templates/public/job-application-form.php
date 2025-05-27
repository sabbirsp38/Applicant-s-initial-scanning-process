<?php
$job = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}jam_jobs WHERE id = %d",
    $job_id
));

if (!$job) {
    return 'Job not found';
}

$questions = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}jam_job_questions WHERE job_id = %d ORDER BY order_num",
    $job_id
));

if ($step === 1): ?>
    <form method="post" class="jam-application-form step-1">
        <?php wp_nonce_field('job_application_step1', 'application_nonce'); ?>
        <input type="hidden" name="job_id" value="<?php echo esc_attr($job_id); ?>">
        
        <div class="form-group">
            <label for="name"><?php esc_html_e('Full Name', 'job-applicant-manager'); ?></label>
            <input type="text" name="name" id="name" required>
        </div>

        <div class="form-group">
            <label for="email"><?php esc_html_e('Email', 'job-applicant-manager'); ?></label>
            <input type="email" name="email" id="email" required>
        </div>

        <div class="form-group">
            <label for="phone"><?php esc_html_e('Phone', 'job-applicant-manager'); ?></label>
            <input type="tel" name="phone" id="phone" required>
        </div>

        <button type="submit" name="submit_step1">
            <?php esc_html_e('Next Step', 'job-applicant-manager'); ?>
        </button>
    </form>

<?php elseif ($step === 2): ?>
    <form method="post" class="jam-application-form step-2" enctype="multipart/form-data">
        <?php wp_nonce_field('job_application_step2', 'application_nonce'); ?>
        <input type="hidden" name="job_id" value="<?php echo esc_attr($job_id); ?>">
        
        <?php foreach ($questions as $question): ?>
            <div class="form-group">
                <label for="q_<?php echo esc_attr($question->id); ?>">
                    <?php echo esc_html($question->question); ?>
                </label>

                <?php if ($question->type === 'text'): ?>
                    <textarea name="answers[<?php echo $question->id; ?>]" 
                              id="q_<?php echo esc_attr($question->id); ?>"
                              <?php echo $question->required ? 'required' : ''; ?>></textarea>

                <?php elseif ($question->type === 'select'): ?>
                    <select name="answers[<?php echo $question->id; ?>]" 
                            id="q_<?php echo esc_attr($question->id); ?>"
                            <?php echo $question->required ? 'required' : ''; ?>>
                        <?php foreach (explode("\n", $question->options) as $option): ?>
                            <option value="<?php echo esc_attr(trim($option)); ?>">
                                <?php echo esc_html(trim($option)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                <?php elseif ($question->type === 'file'): ?>
                    <input type="file" name="answers[<?php echo $question->id; ?>]" 
                           id="q_<?php echo esc_attr($question->id); ?>"
                           <?php echo $question->required ? 'required' : ''; ?>>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <button type="submit" name="submit_application">
            <?php esc_html_e('Submit Application', 'job-applicant-manager'); ?>
        </button>
    </form>
<?php endif; ?> 