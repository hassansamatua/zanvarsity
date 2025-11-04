<?php
// Check if user has permission
if (!($is_admin || $is_dean)) {
    echo '<div class="alert alert-danger">You do not have permission to access this section.</div>';
    return;
}

// Process form submission for downloads
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Add CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Invalid CSRF token';
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    }
    
    $action = $_POST['action'];
    
    if ($action === 'add_download' || $action === 'update_download') {
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $id = $_POST['id'] ?? 0;
        
        // Basic validation
        if (empty($title) || empty($category)) {
            $_SESSION['error'] = 'Title and category are required';
        } else {
            try {
                if ($action === 'add_download') {
                    // Handle file upload
                    $file_path = '';
                    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                        $upload_dir = __DIR__ . '/../uploads/downloads/';
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0755, true);
                        }
                        
                        $file_name = time() . '_' . basename($_FILES['file']['name']);
                        $target_file = $upload_dir . $file_name;
                        
                        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
                            $file_path = 'uploads/downloads/' . $file_name;
                        } else {
                            throw new Exception('Failed to upload file');
                        }
                    } else {
                        throw new Exception('Please select a file to upload');
                    }
                    
                    // Insert into database
                    $stmt = $conn->prepare("INSERT INTO downloads (title, description, file_path, category, created_by) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssi", $title, $description, $file_path, $category, $user_id);
                    $stmt->execute();
                    $_SESSION['success'] = 'Download added successfully';
                } else {
                    // Update existing download
                    $update_sql = "UPDATE downloads SET title = ?, description = ?, category = ?";
                    $params = [$title, $description, $category];
                    $types = "sss";
                    
                    // Handle file update if new file is uploaded
                    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                        $upload_dir = __DIR__ . '/../uploads/downloads/';
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0755, true);
                        }
                        
                        $file_name = time() . '_' . basename($_FILES['file']['name']);
                        $target_file = $upload_dir . $file_name;
                        
                        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
                            $file_path = 'uploads/downloads/' . $file_name;
                            $update_sql .= ", file_path = ?";
                            $params[] = $file_path;
                            $types .= "s";
                            
                            // Delete old file if exists
                            $old_file = $conn->query("SELECT file_path FROM downloads WHERE id = $id")->fetch_assoc()['file_path'] ?? '';
                            if ($old_file && file_exists(__DIR__ . '/../' . $old_file)) {
                                unlink(__DIR__ . '/../' . $old_file);
                            }
                        }
                    }
                    
                    $update_sql .= " WHERE id = ?";
                    $params[] = $id;
                    $types .= "i";
                    
                    $stmt = $conn->prepare($update_sql);
                    $stmt->bind_param($types, ...$params);
                    $stmt->execute();
                    $_SESSION['success'] = 'Download updated successfully';
                }
                
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit();
                
            } catch (Exception $e) {
                $_SESSION['error'] = 'Error: ' . $e->getMessage();
            }
        }
    } elseif ($action === 'delete_download') {
        $id = $_POST['id'] ?? 0;
        
        try {
            // Get file path before deleting
            $file_path = $conn->query("SELECT file_path FROM downloads WHERE id = $id")->fetch_assoc()['file_path'] ?? '';
            
            // Delete from database
            $stmt = $conn->prepare("DELETE FROM downloads WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            // Delete file if exists
            if ($file_path && file_exists(__DIR__ . '/../' . $file_path)) {
                unlink(__DIR__ . '/../' . $file_path);
            }
            
            $_SESSION['success'] = 'Download deleted successfully';
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error deleting download: ' . $e->getMessage();
        }
        
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    }
}

// Fetch categories
$categories = [];
$result = $conn->query("SELECT DISTINCT category FROM downloads ORDER BY category");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

// Handle category filter
$category_filter = $_GET['category'] ?? '';
$where_clause = $category_filter ? "WHERE category = '" . $conn->real_escape_string($category_filter) . "'" : '';

// Fetch downloads
$downloads = [];
$result = $conn->query("SELECT * FROM downloads $where_clause ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $downloads[] = $row;
    }
}
?>

<div class="manage-downloads-section">
    <div class="page-title">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class='bx bxs-download me-2'></i>Manage Downloads</h2>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addDownloadModal">
                <i class='bx bx-plus'></i> Add New Download
            </button>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <!-- Category Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3 align-items-center">
                <input type="hidden" name="tab" value="manage-downloads">
                <div class="col-md-4">
                    <label for="category" class="form-label">Filter by Category:</label>
                    <select name="category" id="category" class="form-select" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category); ?>" <?php echo $category_filter === $category ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($category_filter): ?>
                    <div class="col-md-2 d-flex align-items-end">
                        <a href="?tab=manage-downloads" class="btn btn-outline-secondary">Clear Filter</a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Downloads Grid -->
    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (empty($downloads)): ?>
                <div class="text-center py-5">
                    <i class='bx bx-download' style="font-size: 3rem; color: #6c757d; margin-bottom: 15px;"></i>
                    <h4>No downloads found</h4>
                    <p>Get started by adding your first download.</p>
                </div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php foreach ($downloads as $download): ?>
                        <div class="col">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($download['title']); ?></h5>
                                    <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($download['category']); ?></h6>
                                    <?php if ($download['description']): ?>
                                        <p class="card-text"><?php echo nl2br(htmlspecialchars($download['description'])); ?></p>
                                    <?php endif; ?>
                                    <div class="mt-3">
                                        <a href="<?php echo htmlspecialchars($download['file_path']); ?>" 
                                           class="btn btn-sm btn-primary" 
                                           download 
                                           target="_blank">
                                            <i class='bx bx-download'></i> Download
                                        </a>
                                        <button class="btn btn-sm btn-warning" 
                                                onclick="editDownload(<?php echo htmlspecialchars(json_encode($download)); ?>)">
                                            <i class='bx bx-edit'></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete(<?php echo $download['id']; ?>, '<?php echo addslashes($download['title']); ?>')">
                                            <i class='bx bx-trash'></i> Delete
                                        </button>
                                    </div>
                                </div>
                                <div class="card-footer text-muted">
                                    <small>Uploaded: <?php echo date('M d, Y', strtotime($download['created_at'])); ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add/Edit Download Modal -->
<div class="modal fade" id="downloadModal" tabindex="-1" aria-labelledby="downloadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="downloadForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" id="downloadAction" value="add_download">
                <input type="hidden" name="id" id="downloadId">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="downloadModalLabel">Add New Download</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="category" name="category" list="categories" required>
                                <datalist id="categories">
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo htmlspecialchars($category); ?>">
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="file" class="form-label">File <span class="text-danger" id="fileRequired">*</span></label>
                                <input class="form-control" type="file" id="file" name="file">
                                <div id="currentFile" class="mt-2"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteItemName"></strong>? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    <input type="hidden" name="action" value="delete_download">
                    <input type="hidden" name="id" id="deleteItemId">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Function to handle edit download
function editDownload(download) {
    const modal = new bootstrap.Modal(document.getElementById('downloadModal'));
    const form = document.getElementById('downloadForm');
    
    // Set form action and title
    document.getElementById('downloadModalLabel').textContent = 'Edit Download';
    document.getElementById('downloadAction').value = 'update_download';
    document.getElementById('downloadId').value = download.id;
    
    // Fill the form
    document.getElementById('title').value = download.title || '';
    document.getElementById('category').value = download.category || '';
    document.getElementById('description').value = download.description || '';
    
    // Handle file input
    const fileInput = document.getElementById('file');
    const fileRequired = document.getElementById('fileRequired');
    const currentFile = document.getElementById('currentFile');
    
    if (download.file_path) {
        const fileName = download.file_path.split('/').pop();
        currentFile.innerHTML = `
            <div class="alert alert-info p-2">
                <i class='bx bx-file'></i> Current file: 
                <a href="${download.file_path}" target="_blank">${fileName}</a>
            </div>
        `;
        fileRequired.textContent = ''; // Make file not required for updates
    } else {
        currentFile.innerHTML = '';
        fileRequired.textContent = '*';
    }
    
    // Show the modal
    modal.show();
}

// Function to confirm delete
function confirmDelete(id, name) {
    document.getElementById('deleteItemName').textContent = name;
    document.getElementById('deleteItemId').value = id;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Reset form when adding new download
document.getElementById('downloadModal').addEventListener('hidden.bs.modal', function () {
    const form = document.getElementById('downloadForm');
    form.reset();
    document.getElementById('currentFile').innerHTML = '';
    document.getElementById('fileRequired').textContent = '*';
    document.getElementById('downloadModalLabel').textContent = 'Add New Download';
    document.getElementById('downloadAction').value = 'add_download';
});

// Form validation
document.getElementById('downloadForm').addEventListener('submit', function(e) {
    const action = document.getElementById('downloadAction').value;
    const fileInput = document.getElementById('file');
    const title = document.getElementById('title').value.trim();
    const category = document.getElementById('category').value.trim();
    
    if (!title || !category) {
        e.preventDefault();
        alert('Please fill in all required fields');
        return false;
    }
    
    // For new downloads, file is required
    if (action === 'add_download' && fileInput.files.length === 0) {
        e.preventDefault();
        alert('Please select a file to upload');
        return false;
    }
    
    // For updates, file is optional
    return true;
});
</script>

<style>
/* Custom styles for downloads management */
.manage-downloads-section {
    padding: 20px 0;
}

.card {
    transition: transform 0.2s, box-shadow 0.2s;
    margin-bottom: 20px;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.card-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.card-subtitle {
    font-size: 0.9rem;
}

.card-text {
    font-size: 0.95rem;
    color: #555;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.bx {
    vertical-align: middle;
    margin-right: 3px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .row-cols-md-2 > * {
        flex: 0 0 100%;
        max-width: 100%;
    }
}
</style>
