// Handle delete button click
document.addEventListener('DOMContentLoaded', function() {
    // Delete publication
    document.querySelectorAll('.delete-publication').forEach(button => {
        button.addEventListener('click', function() {
            const publicationId = this.getAttribute('data-id');
            document.getElementById('delete_publication_id').value = publicationId;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
            deleteModal.show();
        });
    });

    // Edit publication
    document.querySelectorAll('.edit-publication').forEach(button => {
        button.addEventListener('click', function() {
            const publicationId = this.getAttribute('data-id');
            
            // Show loading state
            const modal = document.getElementById('editPublicationModal');
            modal.innerHTML = `
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Loading...</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading publication data...</p>
                        </div>
                    </div>
                </div>`;
            
            const editModal = new bootstrap.Modal(modal);
            editModal.show();
            
            // Load edit form
            fetch(`/zanvarsity/html/admin/ajax/edit_publication_modal.php?id=${publicationId}`)
                .then(response => response.text())
                .then(html => {
                    modal.innerHTML = html;
                    // Re-initialize any JS components if needed
                    const tooltipTriggerList = [].slice.call(modal.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                    
                    // Handle form submission
                    const form = modal.querySelector('form');
                    if (form) {
                        form.addEventListener('submit', handleUpdatePublication);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modal.querySelector('.modal-body').innerHTML = `
                        <div class="alert alert-danger">
                            Error loading publication data. Please try again.
                        </div>`;
                });
        });
    });

    // Handle update publication form submission
    function handleUpdatePublication(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        
        // Show loading state
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Updating...
        `;
        
        // Send update request
        fetch('/zanvarsity/html/admin/ajax/update_publication.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showAlert('success', data.message || 'Publication updated successfully');
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                if (modal) {
                    modal.hide();
                }
                
                // Reload the page to show updated data
                setTimeout(() => window.location.reload(), 1000);
            } else {
                throw new Error(data.message || 'Failed to update publication');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', error.message || 'An error occurred while updating the publication');
        })
        .finally(() => {
            // Restore button state
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
        });
    }

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize date picker for add form
    if (document.getElementById('publish_date')) {
        // Set default date to today
        document.getElementById('publish_date').valueAsDate = new Date();
    }
});

// Show alert message
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    const container = document.querySelector('.container-fluid');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            const alert = bootstrap.Alert.getOrCreateInstance(alertDiv);
            if (alert) {
                alert.close();
            }
        }, 5000);
    }
}
