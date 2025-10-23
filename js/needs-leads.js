// Needs & Leads JavaScript functionality

$(document).ready(function() {
    console.log('Needs & Leads JavaScript loaded');
    
    // Show message function
    function showMessage(message, type = 'success') {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const messageHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fa ${icon}"></i> ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        $('#messageContainer').html(messageHtml);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            $('#messageContainer .alert').fadeOut();
        }, 5000);
    }
    
    // Handle Add Need Form
    $('#addNeedForm').on('submit', function(e) {
        console.log('Form submitted');
        e.preventDefault();
        
        const formData = {
            action: 'add_need',
            title: 'Business Requirement', // Default title
            description: $('#needDescription').val()
        };
        
        console.log('Form data:', formData);
        
        if (!formData.description.trim()) {
            console.log('Description is empty');
            showMessage('Please describe your requirement', 'error');
            return;
        }
        
        console.log('Sending AJAX request...');
        
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                $('#addNeedForm button[type="submit"]').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Posting...');
            },
            success: function(response) {
                if (response.success) {
                    showMessage(response.message, 'success');
                    $('#addNeedForm')[0].reset();
                    // Reload the page to show the new need
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                } else {
                    showMessage(response.message, 'error');
                }
            },
            error: function() {
                showMessage('An error occurred while posting your requirement', 'error');
            },
            complete: function() {
                $('#addNeedForm button[type="submit"]').prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Post Requirement');
            }
        });
    });
    
    // Handle Add Lead Form
    $(document).on('submit', '.addLeadForm', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const needId = $form.data('need-id');
        
        const formData = {
            action: 'add_lead',
            need_id: needId,
            response: $form.find('textarea[name="response"]').val()
        };
        
        if (!formData.response.trim()) {
            showMessage('Please enter your response', 'error');
            return;
        }
        
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                $form.find('button[type="submit"]').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');
            },
            success: function(response) {
                if (response.success) {
                    showMessage(response.message, 'success');
                    $form[0].reset();
                    $form.closest('.response-form').hide();
                    // Reload the page to show the new lead
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                } else {
                    showMessage(response.message, 'error');
                }
            },
            error: function() {
                showMessage('An error occurred while submitting your response', 'error');
            },
            complete: function() {
                $form.find('button[type="submit"]').prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Submit Response');
            }
        });
    });
    
    // Toggle response form
    $(document).on('click', '.toggle-response-form', function() {
        const needId = $(this).data('need-id');
        const $form = $('#responseForm' + needId);
        
        if ($form.is(':visible')) {
            $form.slideUp();
            $(this).html('<i class="fa fa-reply"></i> Submit Response');
        } else {
            // Hide all other forms first
            $('.response-form').slideUp();
            $('.toggle-response-form').html('<i class="fa fa-reply"></i> Submit Response');
            
            // Show this form
            $form.slideDown();
            $(this).html('<i class="fa fa-times"></i> Cancel');
        }
    });
    
    // Cancel response
    $(document).on('click', '.cancel-response', function() {
        const $form = $(this).closest('.response-form');
        const needId = $form.attr('id').replace('responseForm', '');
        
        $form.slideUp();
        $form.find('form')[0].reset();
        $(`.toggle-response-form[data-need-id="${needId}"]`).html('<i class="fa fa-reply"></i> Submit Response');
    });
    

    
    // Auto-resize textareas
    $(document).on('input', 'textarea', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
    
    // Initialize tooltips if Bootstrap is available
    if (typeof $().tooltip === 'function') {
        $('[data-toggle="tooltip"]').tooltip();
    }
    
    // Refresh data periodically (every 5 minutes)
    setInterval(function() {
        // You can implement auto-refresh here if needed
        // For now, we'll just show a subtle indicator that data might be outdated
        console.log('Consider refreshing for latest updates');
    }, 300000); // 5 minutes
    
});

// Additional utility functions
function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 1) {
        return 'Yesterday';
    } else if (diffDays < 7) {
        return `${diffDays} days ago`;
    } else {
        return date.toLocaleDateString();
    }
}

function truncateText(text, maxLength = 100) {
    if (text.length <= maxLength) {
        return text;
    }
    return text.substr(0, maxLength) + '...';
}