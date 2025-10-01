// Handle add event form submission
$(document).ready(function() {
    $('#addEventForm').on('submit', function(e) {
        e.preventDefault();
        
        // Reset validation
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        
        // Client-side validation
        let isValid = true;
        const requiredFields = ['title', 'start_date'];
        
        requiredFields.forEach(field => {
            const $field = $(`[name="${field}"]`);
            if (!$field.val().trim()) {
                $field.addClass('is-invalid');
                $field.after('<div class="invalid-feedback">This field is required</div>');
                isValid = false;
            }
        });
        
        // Validate end date is after start date if both are provided
        if ($('[name="start_date"]').val() && $('[name="end_date"]').val()) {
            const startDate = new Date($('[name="start_date"]').val());
            const endDate = new Date($('[name="end_date"]').val());
            
            if (endDate <= startDate) {
                $('[name="end_date"]').addClass('is-invalid');
                $('[name="end_date"]').after('<div class="invalid-feedback">End date must be after start date</div>');
                isValid = false;
            }
        }
        
        if (!isValid) {
            return false;
        }
        
        // Show loading state and prepare form data
        const $form = $(this);
        const $submitBtn = $form.find('button[type="submit"]');
        const originalBtnText = $submitBtn.html();
        
        // Disable submit button to prevent multiple submissions
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
        
        const formData = new FormData(this);
        
        // Submit form via AJAX to the new API endpoint
        $.ajax({
            url: 'api/create_event.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log('API Response:', response);
                
                // Always re-enable the button
                $submitBtn.prop('disabled', false).html(originalBtnText);
                
                if (response.success) {
                    // Show success message
                    // Create a timer element
                    const timerElement = document.createElement('div');
                    timerElement.className = 'swal2-timer';
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        html: `
                            <div class="text-center">
                                <i class="bx bx-check-circle display-4 text-success mb-3"></i>
                                <h4 class="mb-3">${response.message || 'Event added successfully!'}</h4>
                                <p class="text-muted">The page will refresh in <span class="fw-bold">5</span> seconds...</p>
                            </div>
                        `,
                        showConfirmButton: true,
                        confirmButtonText: 'Close & Refresh',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showCloseButton: true,
                        showCancelButton: false,
                        showDenyButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                        didOpen: (popup) => {
                            const timerElement = popup.querySelector('.swal2-timer span');
                            if (timerElement) {
                                let timeLeft = 5;
                                const timerInterval = setInterval(() => {
                                    timeLeft--;
                                    if (timeLeft >= 0) {
                                        timerElement.textContent = timeLeft;
                                    }
                                }, 1000);
                                
                                // Store the interval ID to clear it later
                                popup.setAttribute('data-timer-interval', timerInterval);
                            }
                        },
                        willClose: (popup) => {
                            // Clear the interval when the popup closes
                            const timerInterval = popup.getAttribute('data-timer-interval');
                            if (timerInterval) {
                                clearInterval(parseInt(timerInterval));
                            }
                        }
                    }).then((result) => {
                        // Always reload the page when the alert is closed or timer runs out
                        window.location.reload();
                    });
                } else {
                    // Show validation errors if any
                    if (response.errors && Object.keys(response.errors).length > 0) {
                        // Clear previous errors
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').remove();
                        
                        // Show new errors
                        $.each(response.errors, function(field, message) {
                            const $field = $(`[name="${field}"]`);
                            $field.addClass('is-invalid');
                            
                            // Special handling for file inputs
                            if ($field.attr('type') === 'file') {
                                $field.closest('.form-group').append(`<div class="invalid-feedback d-block">${message}</div>`);
                            } else {
                                $field.after(`<div class="invalid-feedback">${message}</div>`);
                            }
                        });
                        
                        // Scroll to the first error
                        $('html, body').animate({
                            scrollTop: $('.is-invalid').first().offset().top - 100
                        }, 500);
                        
                        // Show error message in alert
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: 'Please fix the errors in the form',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        // Show generic error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'An error occurred while adding the event',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error, xhr.responseText);
                
                // Reset button state
                $submitBtn.prop('disabled', false).html(originalBtnText);
                
                // Try to parse error response
                let errorMessage = 'An error occurred while adding the event. Please try again.';
                
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response && response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    console.error('Error parsing error response:', e);
                }
                
                // Show detailed error message
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: `
                        <div class="text-start">
                            <p>${errorMessage}</p>
                            ${xhr.status ? `<div class="small text-muted mt-2">Status: ${xhr.status}</div>` : ''}
                        </div>
                    `,
                    confirmButtonText: 'OK',
                    allowOutsideClick: false
                });
            }
        });
    });
});
