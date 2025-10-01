<?php
require_once __DIR__ . '/../../../includes/auth_functions.php';
require_once __DIR__ . '/../../../includes/database.php';
require_login();
require_admin();

// Get publication ID from the request
$publication_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$publication_id) {
    die('Invalid publication ID');
}

// Fetch publication data
$stmt = $conn->prepare("SELECT * FROM publications WHERE id = ?");
$stmt->bind_param("i", $publication_id);
$stmt->execute();
$publication = $stmt->get_result()->fetch_assoc();

if (!$publication) {
    die('Publication not found');
}
?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <form action="/zanvarsity/html/admin/manage_publications.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="action" value="update_publication">
            <input type="hidden" name="publication_id" value="<?php echo $publication['id']; ?>">
            
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Publication</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="edit_title" class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="edit_title" name="title" 
                           value="<?php echo htmlspecialchars($publication['title']); ?>" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="edit_author" class="form-label">Author</label>
                            <input type="text" class="form-control" id="edit_author" name="author"
                                   value="<?php echo htmlspecialchars($publication['author']); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="edit_publish_date" class="form-label">Publish Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_publish_date" name="publish_date"
                                   value="<?php echo $publication['publish_date']; ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="edit_description" class="form-label">Description</label>
                    <textarea class="form-control" id="edit_description" name="description" rows="3"><?php 
                        echo htmlspecialchars($publication['description']); 
                    ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="edit_publication_file" class="form-label">Publication File</label>
                    <input class="form-control" type="file" id="edit_publication_file" name="publication_file">
                    <div class="form-text">
                        <?php if ($publication['file_url']): ?>
                            Current file: <a href="<?php echo htmlspecialchars($publication['file_url']); ?>" target="_blank">View File</a>
                        <?php else: ?>
                            No file uploaded
                        <?php endif; ?>
                        <br>Leave empty to keep current file. PDF, DOC, DOCX files are allowed (max 10MB)
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="edit_status" class="form-label">Status</label>
                    <select class="form-select" id="edit_status" name="status">
                        <option value="draft" <?php echo $publication['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        <option value="published" <?php echo $publication['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                        <option value="archived" <?php echo $publication['status'] === 'archived' ? 'selected' : ''; ?>>Archived</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Publication</button>
            </div>
        </form>
    </div>
</div>
