<?php
if (!defined('ABSPATH')) {
    exit;
}

$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;
$job = $this->get_job($job_id);
$step = isset($_GET['step']) ? intval($_GET['step']) : 1;

if (!$job) {
    wp_die(__('Job not found', 'job-applicant-manager'));
}

$questions = $this->get_job_questions($job_id);

// Add this near the top of the file after getting $questions
foreach ($questions as $q) {
    if ($q->type === 'select') {
        error_log('Question ' . $q->id . ' options: ' . print_r($q->options, true));
    }
}
?>

<div class="application-form-container">
    <style>
        .application-form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-header {
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .submit-button {
            background: #00bcd9;
            color: #fff;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .submit-button:hover {
            background: #008c9e;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #00bcd9;
            text-decoration: none;
        }
        .steps-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .step {
            padding: 10px 20px;
            margin: 0 10px;
            background: #f5f5f5;
            border-radius: 20px;
            color: #666;
        }
        .step.active {
            background: #00bcd9;
            color: #fff;
        }
        .progress-bar {
            height: 4px;
            background: #f5f5f5;
            margin: 20px 0;
            border-radius: 2px;
        }
        .progress {
            width: <?php echo $step === 1 ? '50%' : '100%'; ?>;
            height: 100%;
            background: #00bcd9;
            border-radius: 2px;
            transition: width 0.3s ease;
        }
    </style>

    <div class="form-header">
        <a href="<?php echo esc_url(remove_query_arg(['job_id', 'step'])); ?>" class="back-link">
            ← <?php _e('Back to Jobs', 'job-applicant-manager'); ?>
        </a>
        <h2><?php printf(__('Apply for %s', 'job-applicant-manager'), esc_html($job->title)); ?></h2>
        
        <div class="steps-indicator">
            <div class="step <?php echo $step === 1 ? 'active' : ''; ?>">
                <?php _e('Step 1: Personal Information', 'job-applicant-manager'); ?>
            </div>
            <div class="step <?php echo $step === 2 ? 'active' : ''; ?>">
                <?php _e('Step 2: Interview Questions', 'job-applicant-manager'); ?>
            </div>
        </div>
        <div class="progress-bar">
            <div class="progress"></div>
        </div>
    </div>
    
    <?php if ($step === 1): ?>
    <form method="post" action="" enctype="multipart/form-data">
        <?php wp_nonce_field('save_personal_info', 'personal_info_nonce'); ?>
        <input type="hidden" name="job_id" value="<?php echo esc_attr($job_id); ?>">
        
        <div class="form-group">
            <label for="name"><?php _e('Full Name', 'job-applicant-manager'); ?> *</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="email"><?php _e('Email', 'job-applicant-manager'); ?> *</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="phone"><?php _e('Phone', 'job-applicant-manager'); ?> *</label>
            <input type="tel" class="form-control" id="phone" name="phone" required>
        </div>

        <div class="form-group">
            <label for="resume"><?php _e('Resume/CV', 'job-applicant-manager'); ?> *</label>
            <input type="file" class="form-control" id="resume" name="resume" required 
                   accept=".pdf,.doc,.docx">
        </div>

        <div class="form-group">
            <label for="cover_letter"><?php _e('Cover Letter', 'job-applicant-manager'); ?></label>
            <textarea class="form-control" id="cover_letter" name="cover_letter" rows="5"></textarea>
        </div>

        <button type="submit" name="submit_step1" class="submit-button">
            <?php _e('Next Step', 'job-applicant-manager'); ?>
        </button>
    </form>
    
    <?php else: ?>
    <form method="post" action="">
        <?php wp_nonce_field('submit_job_application', 'job_application_nonce'); ?>
        <input type="hidden" name="job_id" value="<?php echo esc_attr($job_id); ?>">
        
        <?php
        if (!empty($questions)):
            foreach ($questions as $question):
        ?>
            <div class="form-group">
                <label for="question_<?php echo esc_attr($question->id); ?>">
                    <?php echo esc_html($question->question); ?>
                    <?php if ($question->required): ?>*<?php endif; ?>
                </label>
                
                <?php if ($question->type === 'text'): ?>
                    <input type="text" class="form-control" 
                           id="question_<?php echo esc_attr($question->id); ?>"
                           name="answers[<?php echo esc_attr($question->id); ?>]"
                           <?php echo $question->required ? 'required' : ''; ?>>
                           
                <?php elseif ($question->type === 'textarea'): ?>
                    <textarea class="form-control" 
                             id="question_<?php echo esc_attr($question->id); ?>"
                             name="answers[<?php echo esc_attr($question->id); ?>]"
                             rows="4"
                             <?php echo $question->required ? 'required' : ''; ?>></textarea>
                             
                <?php elseif ($question->type === 'select'): ?>
                    <select class="form-control"
                            id="question_<?php echo esc_attr($question->id); ?>"
                            name="answers[<?php echo esc_attr($question->id); ?>]"
                            <?php echo $question->required ? 'required' : ''; ?>>
                        <option value=""><?php _e('Select an option', 'job-applicant-manager'); ?></option>
                        <?php 
                        // Debug output
                        error_log('Question options raw: ' . print_r($question->options, true));
                        
                        // Try parsing as JSON first
                        $options = json_decode($question->options, true);
                        
                        // If JSON parsing fails, try comma-separated string
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            $options = array_map('trim', explode(',', $question->options));
                        }
                        
                        // Debug parsed options
                        error_log('Parsed options: ' . print_r($options, true));
                        
                        if (!empty($options) && is_array($options)):
                            foreach ($options as $option):
                        ?>
                            <option value="<?php echo esc_attr($option); ?>">
                                <?php echo esc_html($option); ?>
                            </option>
                        <?php 
                            endforeach;
                        endif;
                        ?>
                    </select>
                <?php endif; ?>
            </div>
        <?php 
            endforeach;
        else:
        ?>
            <p><?php _e('No questions available for this position.', 'job-applicant-manager'); ?></p>
        <?php endif; ?>

        <div class="form-actions">
            <a href="<?php echo esc_url(add_query_arg('step', 1, remove_query_arg('application_data'))); ?>" 
               class="back-button">
                <?php _e('Previous Step', 'job-applicant-manager'); ?>
            </a>
            <button type="submit" name="submit_application" class="submit-button">
                <?php _e('Submit Application', 'job-applicant-manager'); ?>
            </button>
        </div>
    </form>
    <?php endif; ?>
</div> 