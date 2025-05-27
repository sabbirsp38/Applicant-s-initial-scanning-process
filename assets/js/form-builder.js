jQuery(document).ready(function($) {
    // Make fields draggable
    $('.field-item').draggable({
        helper: 'clone',
        connectToSortable: '#formFieldsContainer'
    });

    // Make form container sortable
    $('#formFieldsContainer').sortable({
        placeholder: 'field-placeholder',
        receive: function(event, ui) {
            var fieldType = ui.item.data('type');
            var fieldHtml = generateFieldHtml(fieldType);
            ui.item.replaceWith(fieldHtml);
        }
    });

    // Generate field HTML based on type
    function generateFieldHtml(type) {
        var fieldId = 'field_' + Math.random().toString(36).substr(2, 9);
        var html = '<div class="form-field" data-type="' + type + '">';
        html += '<div class="field-header">';
        html += '<input type="text" class="field-label" placeholder="Field Label">';
        html += '<button type="button" class="remove-field">×</button>';
        html += '</div>';

        switch(type) {
            case 'text':
                html += '<input type="text" disabled placeholder="Text input">';
                break;
            case 'textarea':
                html += '<textarea disabled placeholder="Text area"></textarea>';
                break;
            case 'select':
                html += '<input type="text" class="field-options" placeholder="Options (comma-separated)">';
                html += '<select disabled><option>Dropdown</option></select>';
                break;
            // Add other field types...
        }

        html += '<label><input type="checkbox" class="required-field"> Required</label>';
        html += '</div>';
        return html;
    }

    // Remove field
    $(document).on('click', '.remove-field', function() {
        $(this).closest('.form-field').remove();
    });

    // Save form
    $('#saveForm').click(function() {
        var formData = [];
        $('.form-field').each(function() {
            var field = {
                type: $(this).data('type'),
                label: $(this).find('.field-label').val(),
                required: $(this).find('.required-field').is(':checked')
            };
            
            if (field.type === 'select') {
                field.options = $(this).find('.field-options').val().split(',');
            }
            
            formData.push(field);
        });

        // Save via AJAX
        $.post(ajaxurl, {
            action: 'save_custom_form',
            form_data: formData,
            nonce: formBuilderNonce
        }, function(response) {
            if (response.success) {
                alert('Form saved successfully!');
            } else {
                alert('Error saving form');
            }
        });
    });
}); 