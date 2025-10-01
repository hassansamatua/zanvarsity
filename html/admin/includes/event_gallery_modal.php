<!-- Event Gallery Modal -->
<div class="modal fade" id="eventGalleryModal" tabindex="-1" role="dialog" aria-labelledby="eventGalleryModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h2 class="modal-title h5" id="eventGalleryModalLabel">Manage Event Gallery</h2>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="eventGalleryForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_gallery_images">
                <input type="hidden" name="event_id" id="galleryEventId">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="modal-body">
                    <div class="form-group">
                        <label for="galleryEventSelect">Select Event</label>
                        <select class="form-control" id="galleryEventSelect" required>
                            <option value="">-- Select an event --</option>
                            <?php
                            // Fetch all events for the dropdown
                            $events = [];
                            $query = "SELECT id, title FROM events ORDER BY start_date DESC";
                            $result = $conn->query($query);
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['title']) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Upload Images</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="galleryImages" name="gallery_images[]" multiple accept="image/*" required>
                            <label class="custom-file-label" for="galleryImages">Choose files...</label>
                            <small class="form-text text-muted">You can select multiple images (JPG, PNG, GIF). Max 10MB per image.</small>
                        </div>
                    </div>
                    
                    <div id="imagePreviews" class="row mt-3">
                        <!-- Image previews will be shown here -->
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveGalleryBtn">
                        <i class="fa fa-save"></i> Save Images
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Gallery Modal Styles */
#imagePreviews {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.preview-container {
    position: relative;
    width: 100px;
    height: 100px;
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow: hidden;
}

.preview-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.remove-image {
    position: absolute;
    top: 2px;
    right: 2px;
    background: rgba(255,0,0,0.7);
    color: white;
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    line-height: 1;
    padding: 0;
    font-size: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.remove-image:hover {
    background: #dc3545;
}
</style>

<script>
$(document).ready(function() {
    // Update the label of the file input when files are selected
    $('#galleryImages').on('change', function() {
        const files = this.files;
        const $label = $(this.nextElementSibling);
        
        if (files.length > 0) {
            $label.text(files.length + ' file(s) selected');
            
            // Show image previews
            const $previewContainer = $('#imagePreviews');
            $previewContainer.empty();
            
            Array.from(files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = `
                            <div class="preview-container">
                                <img src="${e.target.result}" alt="Preview">
                                <input type="text" class="form-control form-control-sm mt-1" name="captions[]" placeholder="Caption (optional)">
                            </div>
                        `;
                        $previewContainer.append(preview);
                    };
                    reader.readAsDataURL(file);
                }
            });
        } else {
            $label.text('Choose files...');
            $('#imagePreviews').empty();
        }
    });
    
    // Handle form submission
    $('#eventGalleryForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const eventId = $('#galleryEventSelect').val();
        
        if (!eventId) {
            Swal.fire('Error', 'Please select an event', 'error');
            return false;
        }
        
        formData.set('event_id', eventId);
        
        // Show loading state
        const $submitBtn = $('#saveGalleryBtn');
        const originalText = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading...');
        
        // Submit form via AJAX
        $.ajax({
            url: 'manage_events.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    if (data.success) {
                        Swal.fire('Success', data.message || 'Images uploaded successfully', 'success');
                        $('#eventGalleryModal').modal('hide');
                        // Reset form
                        $('#eventGalleryForm')[0].reset();
                        $('#imagePreviews').empty();
                    } else {
                        Swal.fire('Error', data.message || 'Failed to upload images', 'error');
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    Swal.fire('Error', 'An error occurred while processing your request', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                Swal.fire('Error', 'An error occurred while uploading images', 'error');
            },
            complete: function() {
                $submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // When the modal is shown, update the event ID if a specific event is selected
    $('#eventGalleryModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const eventId = button.data('event-id');
        if (eventId) {
            $('#galleryEventSelect').val(eventId);
        }
    });
});
</script>
