<?php
class Job_Applicant_Form_Builder {
    /**
     * Initialize the form builder
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_form_builder_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_form_builder_assets'));
    }

    /**
     * Add form builder menu
     */
    public function add_form_builder_menu() {
        add_submenu_page(
            'edit.php?post_type=job',
            __('Form Builder', 'job-applicant-manager'),
            __('Form Builder', 'job-applicant-manager'),
            'manage_options',
            'job-form-builder',
            array($this, 'render_form_builder_page')
        );
    }

    /**
     * Enqueue form builder assets
     */
    public function enqueue_form_builder_assets($hook) {
        if ('job_page_job-form-builder' !== $hook) {
            return;
        }

        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jam-form-builder', JAM_PLUGIN_URL . 'assets/js/form-builder.js', array('jquery', 'jquery-ui-sortable'), JAM_VERSION, true);
        wp_enqueue_style('jam-form-builder', JAM_PLUGIN_URL . 'assets/css/form-builder.css', array(), JAM_VERSION);
    }

    /**
     * Render form builder page
     */
    public function render_form_builder_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Custom Application Form Builder', 'job-applicant-manager'); ?></h1>
            
            <div class="form-builder-container">
                <div class="available-fields">
                    <h2><?php _e('Available Fields', 'job-applicant-manager'); ?></h2>
                    <div class="field-list">
                        <div class="field-item" data-type="text">
                            <?php _e('Text Field', 'job-applicant-manager'); ?>
                        </div>
                        <div class="field-item" data-type="textarea">
                            <?php _e('Text Area', 'job-applicant-manager'); ?>
                        </div>
                        <div class="field-item" data-type="select">
                            <?php _e('Dropdown', 'job-applicant-manager'); ?>
                        </div>
                        <div class="field-item" data-type="checkbox">
                            <?php _e('Checkbox', 'job-applicant-manager'); ?>
                        </div>
                        <div class="field-item" data-type="radio">
                            <?php _e('Radio Buttons', 'job-applicant-manager'); ?>
                        </div>
                        <div class="field-item" data-type="file">
                            <?php _e('File Upload', 'job-applicant-manager'); ?>
                        </div>
                    </div>
                </div>
                
                <div class="form-preview">
                    <h2><?php _e('Form Preview', 'job-applicant-manager'); ?></h2>
                    <div class="form-fields-container" id="formFieldsContainer">
                        <!-- Fields will be added here -->
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="button button-primary" id="saveForm">
                    <?php _e('Save Form', 'job-applicant-manager'); ?>
                </button>
            </div>
        </div>

        <style>
            .form-builder-container {
                display: flex;
                gap: 30px;
                margin-top: 20px;
            }
            .available-fields,
            .form-preview {
                flex: 1;
                background: #fff;
                padding: 20px;
                border: 1px solid #ccd0d4;
            }
            .field-item {
                padding: 10px;
                margin-bottom: 10px;
                background: #f8f9fa;
                border: 1px solid #ddd;
                cursor: move;
            }
            .form-fields-container {
                min-height: 200px;
                border: 2px dashed #ddd;
                padding: 20px;
            }
            .form-actions {
                margin-top: 20px;
            }
        </style>
        <?php
    }
} 