<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if it doesn't exist
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1" role="dialog" aria-labelledby="addEventModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h2 class="modal-title h5" id="addEventModalLabel">
                    <i class='bx bx-calendar-plus me-2' aria-hidden="true"></i>Add New Event
                </h2>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="addEventForm" method="POST" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="action" value="add_event">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Event Title -->
                            <div class="mb-3">
                                <label for="eventTitle" class="form-label">Event Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="eventTitle" name="title" required>
                                <div class="invalid-feedback">Please provide a title for the event.</div>
                            </div>
                            
                            <!-- Event Description -->
                            <div class="mb-3">
                                <label for="eventDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="eventDescription" name="description" rows="3"></textarea>
                            </div>
                            
                            <!-- Date & Time -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="startDate" class="form-label">Start Date & Time <span class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control" id="startDate" name="start_date" required>
                                        <div class="invalid-feedback">Please select a start date and time.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="endDate" class="form-label">End Date & Time</label>
                                        <input type="datetime-local" class="form-control" id="endDate" name="end_date">
                                        <div class="form-text">Leave empty if same as start time</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Location -->
                            <div class="mb-3">
                                <label for="eventLocation" class="form-label">Location</label>
                                <input type="text" class="form-control" id="eventLocation" name="location" placeholder="e.g., Main Auditorium">
                            </div>
                            
                            <!-- Status -->
                            <div class="mb-3">
                                <label for="eventStatus" class="form-label">Status</label>
                                <select class="form-select" id="eventStatus" name="status">
                                    <option value="upcoming">Upcoming</option>
                                    <option value="ongoing">Ongoing</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Image Upload -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="eventImage" class="form-label">Event Image</label>
                                <div class="card mb-2">
                                    <div class="card-body text-center" id="imagePreview">
                                        <i class="bx bx-image fs-1 text-muted"></i>
                                        <p class="mb-0 text-muted small">No image selected</p>
                                    </div>
                                </div>
                                <input type="file" class="form-control" id="eventImage" name="event_image" accept="image/*">
                                <div class="form-text">Max size: 5MB. Allowed: JPG, PNG, GIF</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class='bx bx-x me-1'></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class='bx bx-plus me-1'></i> Add Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Image preview functionality
document.addEventListener('DOMContentLoaded', function() {
    const eventImageInput = document.getElementById('eventImage');
    const imagePreview = document.getElementById('imagePreview');
    
    if (eventImageInput && imagePreview) {
        eventImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreview.innerHTML = `
                        <img src="${e.target.result}" class="img-fluid rounded" alt="Preview" style="max-height: 200px;">
                        <p class="mt-2 mb-0 text-muted small">${file.name}</p>
                    `;
                };
                
                reader.readAsDataURL(file);
            } else {
                imagePreview.innerHTML = `
                    <i class="bx bx-image fs-1 text-muted"></i>
                    <p class="mb-0 text-muted small">No image selected</p>
                `;
            }
        });
    }
});
</script>
