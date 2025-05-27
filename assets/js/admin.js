jQuery(document).ready(function($) {
    console.log('Admin JS loaded');
    
    // Debug info
    console.log('jQuery version:', $.fn.jquery);
    console.log('Template content:', $('#question-template').html());
    
    // Question counter
    let questionCount = $('.question-item').length;
    console.log('Initial question count:', questionCount);

    // Add new question
    $('#add-question').on('click', function(e) {
        console.log('Add question clicked');
        e.preventDefault();
        e.stopPropagation();
        
        let template = $('#question-template').html();
        console.log('Template content:', template);
        
        if (!template) {
            console.error('Template not found!');
            return;
        }
        
        template = template.replace(/\{\{number\}\}/g, questionCount);
        $('#interview-questions').append(template);
        
        questionCount++;
        console.log('Question added. New count:', questionCount);
    });

    // Remove question
    $(document).on('click', '.remove-question', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).closest('.question-item').remove();
        
        // Recount questions
        questionCount = $('.question-item').length;
        console.log('Removed question. Remaining questions:', questionCount);
    });

    // Show/hide options based on question type
    $(document).on('change', '.question-type', function() {
        const optionsRow = $(this).closest('tr').next('.options-row');
        if ($(this).val() === 'select') {
            optionsRow.show();
        } else {
            optionsRow.hide();
        }
    });

    // Form submission handling
    $('.jam-job-form').on('submit', function(e) {
        // Validate required fields
        let isValid = true;
        $(this).find('[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('error');
            } else {
                $(this).removeClass('error');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields');
        }
    });
}); 