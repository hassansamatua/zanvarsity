<?php
// Check if user is logged in and has admin/dean privileges
if (!isset($is_admin) || !isset($is_dean) || (!$is_admin && !$is_dean)) {
    echo '<div class="alert alert-danger">You do not have permission to access this section.</div>';
    return;
}

// Include the database connection if not already included
if (!isset($conn)) {
    require_once __DIR__ . '/../includes/database.php';
    $conn = $GLOBALS['conn'] ?? null;
}
?>

<!-- Publications Management Section -->
<div class="publications-section">
    <div class="page-title">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class='bx bx-book me-2'></i>Manage Publications</h2>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPublicationModal">
                <i class='bx bx-plus'></i> Add New Publication
            </button>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="publicationsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Cover</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Publication Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch publications from the database
                        $publications = [];
                        $query = "SELECT * FROM publications ORDER BY publication_date DESC";
                        if ($result = $conn->query($query)) {
                            while ($row = $result->fetch_assoc()) {
                                $publications[] = $row;
                            }
                            $result->free();
                        }

                        if (!empty($publications)): 
                            foreach ($publications as $pub): 
                                $status_class = ($pub['status'] === 'published') ? 'badge bg-success' : 'badge bg-warning';
                        ?>
                            <tr>
                                <td>
                                    <?php if (!empty($pub['cover_image'])): ?>
                                        <img src="<?php echo htmlspecialchars($pub['cover_image']); ?>" alt="Cover" style="width: 50px; height: 70px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 70px;">
                                            <i class='bx bx-book text-muted'></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($pub['title']); ?></td>
                                <td><?php echo htmlspecialchars($pub['author']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($pub['publication_date'])); ?></td>
                                <td><span class="<?php echo $status_class; ?>"><?php echo ucfirst($pub['status']); ?></span></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary view-publication" data-id="<?php echo $pub['id']; ?>">
                                            <i class='bx bx-show'></i> View
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary edit-publication" data-id="<?php echo $pub['id']; ?>">
                                            <i class='bx bx-edit'></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-publication" data-id="<?php echo $pub['id']; ?>">
                                            <i class='bx bx-trash'></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php 
                            endforeach; 
                        else: 
                        ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class='bx bx-book-open display-4 d-block mb-3'></i>
                                        <h4>No publications found</h4>
                                        <p>Get started by adding your first publication</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Publication Modal -->
<div class="modal fade" id="addPublicationModal" tabindex="-1" aria-labelledby="addPublicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addPublicationModalLabel">Add New Publication</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addPublicationForm" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_publication">
                    <?php echo csrf_token_field(); ?>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="col-md-6">
                            <label for="author" class="form-label">Author <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="author" name="author" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="publication_date" class="form-label">Publication Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="publication_date" name="publication_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cover_image" class="form-label">Cover Image</label>
                        <input class="form-control" type="file" id="cover_image" name="cover_image" accept="image/*">
                        <div class="form-text">Recommended size: 800x1200 pixels, Max size: 2MB</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="document" class="form-label">Document (PDF)</label>
                        <input class="form-control" type="file" id="document" name="document" accept=".pdf">
                        <div class="form-text">Upload the full publication document (PDF only, Max size: 10MB)</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Publication</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Publication Modal -->
<div class="modal fade" id="viewPublicationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewPublicationModalLabel">View Publication</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="publicationDetails">
                <!-- Content will be loaded via AJAX -->
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" class="btn btn-primary" id="downloadPublicationBtn" target="_blank">
                    <i class='bx bx-download'></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deletePublicationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this publication? This action cannot be undone.</p>
                <input type="hidden" id="publicationIdToDelete" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Handle view publication
    $('.view-publication').on('click', function() {
        const pubId = $(this).data('id');
        const modal = new bootstrap.Modal(document.getElementById('viewPublicationModal'));
        const modalBody = $('#publicationDetails');
        
        // Show loading state
        modalBody.html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
        
        // Load publication details via AJAX
        $.get(`/c/zanvarsity/html/admin/publication_details.php?id=${pubId}`, function(response) {
            modalBody.html(response);
            
            // Update download button href
            $('#downloadPublicationBtn').attr('href', `/c/zanvarsity/html/admin/download_publication.php?id=${pubId}`);
            
            // Show the modal
            modal.show();
        }).fail(function() {
            modalBody.html(`
                <div class="alert alert-danger">
                    Failed to load publication details. Please try again later.
                </div>
            `);
        });
    });
    
    // Handle delete publication
    $('.delete-publication').on('click', function() {
        const pubId = $(this).data('id');
        $('#publicationIdToDelete').val(pubId);
        const modal = new bootstrap.Modal(document.getElementById('deletePublicationModal'));
        modal.show();
    });
    
    // Confirm delete action
    $('#confirmDeleteBtn').on('click', function() {
        const pubId = $('#publicationIdToDelete').val();
        
        // Show loading state
        const btn = $(this);
        const originalText = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...');
        
        // Send delete request
        $.post('/c/zanvarsity/html/admin/delete_publication.php', {
            id: pubId,
            csrf_token: '<?php echo $_SESSION['csrf_token'] ?? ''; ?>'
        }, function(response) {
            if (response.success) {
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('deletePublicationModal'));
                modal.hide();
                
                // Show success message
                showAlert('success', 'Publication deleted successfully!');
                
                // Reload the page after a short delay
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('danger', response.message || 'Failed to delete publication.');
                btn.prop('disabled', false).html(originalText);
            }
        }, 'json').fail(function() {
            showAlert('danger', 'An error occurred while deleting the publication.');
            btn.prop('disabled', false).html(originalText);
        });
    });
    
    // Handle form submission
    $('#addPublicationForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const formData = new FormData(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalBtnText = submitBtn.html();
        
        // Show loading state
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
        
        // Submit form via AJAX
        $.ajax({
            url: '/c/zanvarsity/html/admin/save_publication.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    const result = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (result.success) {
                        // Show success message
                        showAlert('success', 'Publication saved successfully!');
                        
                        // Close the modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addPublicationModal'));
                        modal.hide();
                        
                        // Reset form
                        form[0].reset();
                        
                        // Reload the page after a short delay
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showAlert('danger', result.message || 'Failed to save publication.');
                    }
                } catch (e) {
                    console.error('Error parsing response:', e, response);
                    showAlert('danger', 'An error occurred while processing your request.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                showAlert('danger', 'An error occurred while saving the publication. Please try again.');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });
    
    // Helper function to show alerts
    function showAlert(type, message) {
        const alert = $(`
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
        
        // Add alert to the page
        $('.page-title').after(alert);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            alert.alert('close');
        }, 5000);
    }
});
</script>
