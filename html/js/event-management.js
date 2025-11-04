// Event Management JavaScript
// This file contains all the JavaScript code for managing events

// Format date for datetime-local input
function formatDateForInput(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '';
    return date.toISOString().slice(0, 16);
}

// Show toast notification
function showToast(type, message) {
    const toast = `
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0 position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bx ${type === 'success' ? 'bx-check-circle' : 'bx-error-alt'} me-2"></i> ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    $('body').append(toast);
    const toastElement = $('.toast').last();
    const bsToast = new bootstrap.Toast(toastElement);
    bsToast.show();
    
    // Remove toast after it's hidden
    toastElement.on('hidden.bs.toast', function () {
        $(this).remove();
    });
}

$(document).ready(function() {
    // Handle delete event
    $(document).on('click', '.delete-event', function(e) {
        e.preventDefault();
        const eventId = $(this).data('id');
        const deleteBtn = $(this);
        
        if (confirm('Are you sure you want to delete this event?')) {
            // Show loading state
            deleteBtn.prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i> Deleting...');
            
            $.ajax({
                url: 'ajax/delete_event.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    id: eventId,
                    csrf_token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response && response.success) {
                        showToast('success', response.message || 'Event deleted successfully');
                        
                        // Remove the event card from the DOM
                        deleteBtn.closest('.col-md-6').fadeOut(300, function() {
                            $(this).remove();
                            // If no events left, show empty state
                            if ($('.event-card').length === 0) {
                                $('.events-grid').html(`
                                    <div class="col-12 text-center py-5">
                                        <i class='bx bx-calendar-x' style="font-size: 3rem; color: #6c757d; margin-bottom: 15px;"></i>
                                        <h4>No events found</h4>
                                        <p>Get started by adding your first event.</p>
                                        <button class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#addEventModal">
                                            <i class='bx bx-plus-circle'></i> Add Event
                                        </button>
                                    </div>
                                `);
                            }
                        });
                    } else {
                        const errorMsg = response && response.message ? response.message : 'Failed to delete event';
                        showToast('error', errorMsg);
                        deleteBtn.prop('disabled', false).html('<i class="bx bx-trash"></i>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', { status: status, error: error, response: xhr.responseText });
                    showToast('error', 'An error occurred while deleting the event. Please try again.');
                    deleteBtn.prop('disabled', false).html('<i class="bx bx-trash"></i>');
                }
            });
        }
    });

    // Handle edit event - single event handler
    $(document).on('click', '.edit-event', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const eventId = $(this).data('id');
        const editBtn = $(this);
        
        // Show loading state
        const originalHtml = editBtn.html();
        editBtn.html('<i class="bx bx-loader bx-spin"></i>');
        
        console.log('Loading event data for ID:', eventId);
        
        // Clean up any existing modal instances
        const modalElement = document.getElementById('editEventModal');
        let modal = bootstrap.Modal.getInstance(modalElement);
        
        if (modal) {
            modal.dispose();
        }
        
        // Remove any existing modal backdrops
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        $('body').css('overflow', '');
        $('body').css('padding-right', '');
        
        // Ensure the modal is visible and reset its state
        $(modalElement).removeClass('fade');
        $(modalElement).css('display', 'none');
        
        // Load the edit form via AJAX
        $.ajax({
            url: 'ajax/get_event.php',
            type: 'GET',
            data: { 
                id: eventId,
                _: new Date().getTime() // Prevent caching
            },
            cache: false,
            dataType: 'json',
            success: function(response) {
                console.log('Event data loaded:', response);
                editBtn.html(originalHtml);
                
                if (response.success && response.data) {
                    const event = response.data;
                    console.log('Event details:', event);
                    
                    // Basic validation
                    if (!event.id) {
                        console.error('Invalid event ID received');
                        throw new Error('Invalid event data received');
                    }
                    
                    // Set form values
                    $('#editEventId').val(event.id);
                    $('#editEventTitle').val(event.title || '');
                    $('#editEventDescription').val(event.description || '');
                    $('#editEventLocation').val(event.location || '');
                    $('#editStartDate').val(formatDateForInput(event.start_date));
                    $('#editEndDate').val(formatDateForInput(event.end_date));
                    
                    console.log('Form values set, showing modal');
                    
                    // Initialize a new modal instance
                    const modal = new bootstrap.Modal(modalElement, {
                        backdrop: 'static',
                        keyboard: true,
                        focus: true
                    });
                    
                    // Store the modal instance
                    $(modalElement).data('bs.modal', modal);
                    
                    // Show the modal using jQuery
                    $(modalElement).modal('show');
                    
                    // Focus on the first form element for better UX
                    setTimeout(() => {
                        const titleInput = document.getElementById('editEventTitle');
                        if (titleInput) {
                            titleInput.focus();
                        }
                    }, 100);
                    
                } else {
                    const errorMsg = response.message || 'Failed to load event data';
                    console.error(errorMsg);
                    showToast('error', errorMsg);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                editBtn.html(originalHtml);
                showToast('error', 'Failed to load event data. Please try again.');
            }
        });
    });
    
    // Handle edit form submission
    $('#editEventForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = $('#editEventForm button[type="submit"]');
        const originalBtnText = submitBtn.html();
        
        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i> Saving...');
        
        // Add CSRF token to form data
        formData.append('csrf_token', $('meta[name="csrf-token"]').attr('content'));
        
        $.ajax({
            url: 'ajax/update_event.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response && response.success) {
                    showToast('success', response.message || 'Event updated successfully');
                    
                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editEventModal'));
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Refresh the page after a short delay
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                    
                } else {
                    const errorMsg = response && response.message ? response.message : 'Failed to update event';
                    console.error('Update Error:', response);
                    showToast('error', errorMsg);
                    submitBtn.prop('disabled', false).html(originalBtnText);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', { status: status, error: error, response: xhr.responseText });
                showToast('error', 'An error occurred while updating the event. Please try again.');
                submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });
    
    // Update file input label
    $('.custom-file-input').on('change', function() {
        const fileName = $(this).val().split('\\\\').pop();
        $(this).next('.custom-file-label').html(fileName || 'Choose file');
    });
    
    // Initialize date pickers
    if ($.fn.datetimepicker) {
        $('#startDate, #endDate, #editStartDate, #editEndDate').datetimepicker({
            format: 'YYYY-MM-DD HH:mm',
            sideBySide: true
        });
    }
});

// Initialize event management when the document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Handle edit event button click
    $(document).on('click', '.edit-event', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const eventId = $(this).data('id');
        const editBtn = $(this);
        
        // Show loading state
        const originalHtml = editBtn.html();
        editBtn.html('<i class="bx bx-loader bx-spin"></i>');
        
        console.log('Loading event data for ID:', eventId);
        
        // Clean up any existing modal instances
        const existingModal = bootstrap.Modal.getInstance(document.getElementById('editEventModal'));
        if (existingModal) {
            existingModal.hide();
            existingModal.dispose();
        }
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        $('body').css('overflow', '');
        $('body').css('padding-right', '');
        
        // Load the edit form via AJAX
        $.ajax({
            url: 'ajax/get_event.php',
            type: 'GET',
            data: { 
                id: eventId,
                _: new Date().getTime() // Prevent caching
            },
            cache: false,
            dataType: 'json',
            success: function(response) {
                console.log('Event data loaded:', response);
                editBtn.html(originalHtml);
                
                if (response.success && response.data) {
                    const event = response.data;
                    console.log('Event details:', event);
                    
                    // Basic validation
                    if (!event.id) {
                        console.error('Invalid event ID received');
                        throw new Error('Invalid event data received');
                    }
                    
                    // Set form values
                    $('#editEventId').val(event.id);
                    $('#editEventTitle').val(event.title || '');
                    $('#editEventDescription').val(event.description || '');
                    $('#editEventLocation').val(event.location || '');
                    $('#editStartDate').val(formatDateForInput(event.start_date));
                    $('#editEndDate').val(formatDateForInput(event.end_date));
                    
                    console.log('Form values set, showing modal');
                    
                    // Get the modal element
                    const modalElement = document.getElementById('editEventModal');
                    
                    // Initialize the modal if it doesn't exist
                    let modal = bootstrap.Modal.getInstance(modalElement);
                    if (!modal) {
                        modal = new bootstrap.Modal(modalElement, {
                            backdrop: true,
                            keyboard: true
                        });
                    }
                    
                    // Show the modal
                    modal.show();
                    
                    // Focus on the first form element for better UX
                    setTimeout(() => {
                        const titleInput = document.getElementById('editEventTitle');
                        if (titleInput) {
                            titleInput.focus();
                        }
                    }, 100);
                    
                } else {
                    const errorMsg = response.message || 'Failed to load event data';
                    console.error(errorMsg);
                    showToast('error', errorMsg);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                editBtn.html(originalHtml);
                showToast('error', 'Failed to load event data. Please try again.');
            }
        });
    });
    
    // Handle edit form submission
    $('#editEventForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = $('#editEventForm button[type="submit"]');
        const originalBtnText = submitBtn.html();
        
        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i> Saving...');
        
        // Add CSRF token to form data
        formData.append('csrf_token', $('meta[name="csrf-token"]').attr('content'));
        
        $.ajax({
            url: 'ajax/update_event.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response && response.success) {
                    showToast('success', response.message || 'Event updated successfully');
                    
                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editEventModal'));
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Refresh the page after a short delay
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                    
                } else {
                    const errorMsg = response && response.message ? response.message : 'Failed to update event';
                    console.error('Update Error:', response);
                    showToast('error', errorMsg);
                    submitBtn.prop('disabled', false).html(originalBtnText);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', { status: status, error: error, response: xhr.responseText });
                showToast('error', 'An error occurred while updating the event. Please try again.');
                submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });
    
    // Show toast notification
    function showToast(type, message) {
        const toast = `
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0 position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bx ${type === 'success' ? 'bx-check-circle' : 'bx-error-alt'} me-2"></i> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        $('body').append(toast);
        $('.toast').toast('show');
        
        // Remove toast after it's hidden
        $('.toast').on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }
    
    // Update file input label
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName || 'Choose file');
    });
});
