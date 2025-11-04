<?php
// manage_announcements.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'C:\\xampp\\htdocs\\c\\zanvarsity\\php_errors.log');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/../includes/database.php';

// Check if database connection is available
if (!isset($conn) || !($conn instanceof mysqli)) {
    die("Database connection failed: " . ($conn->connect_error ?? 'Unknown error'));
}

// Include necessary files
require_once __DIR__ . '/../includes/auth_functions.php';

// Check if user is logged in and has admin/dean privileges
require_login();

// Get user information from session
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['role'] ?? 'student';
$is_admin = in_array($user_role, ['admin', 'super_admin']);
$is_dean = ($user_role === 'dean');

if (!($is_admin || $is_dean)) {
    $_SESSION['error'] = "You don't have permission to access this page.";
    header('Location: /c/zanvarsity/html/index.php');
    exit();
}

// Handle AJAX requests
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Function to send JSON response
function sendJsonResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Log the incoming request for debugging
    error_log('Announcement action: ' . $action);
    error_log('POST data: ' . print_r($_POST, true));
    error_log('FILES data: ' . print_r($_FILES, true));
    
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $error = 'Invalid CSRF token. Please refresh the page and try again.';
        error_log('CSRF token mismatch. Session token: ' . ($_SESSION['csrf_token'] ?? 'not set') . ', POST token: ' . ($_POST['csrf_token'] ?? 'not set'));
        if ($isAjax) {
            sendJsonResponse(['success' => false, 'message' => $error]);
        } else {
            $_SESSION['error'] = $error;
            header('Location: manage_announcements.php');
            exit();
        }
    }

    switch ($action) {
        case 'add_announcement':
        case 'update_announcement':
            // Validate required fields
            $required = ['title', 'content', 'start_date'];
            $errors = [];
            $announcement = [];
            
            foreach ($required as $field) {
                if (empty(trim($_POST[$field] ?? ''))) {
                    $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
                }
                $announcement[$field] = trim($_POST[$field] ?? '');
            }
            
            // Format dates
            $start_date = date('Y-m-d', strtotime($announcement['start_date']));
            $end_date = !empty($_POST['end_date']) ? date('Y-m-d', strtotime($_POST['end_date'])) : null;
            $is_important = isset($_POST['is_important']) ? 1 : 0;
            
            if ($end_date && $start_date > $end_date) {
                $errors[] = 'End date must be after start date';
            }
            
            // Handle file upload
            $attachment_url = null;
            $attachment_name = null;
            
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = str_replace('\', '/', __DIR__ . '/../uploads/announcements/');
                if (!file_exists($upload_dir)) {
                    if (!mkdir($upload_dir, 0777, true)) {
                        $errors[] = 'Failed to create upload directory. Please check permissions.';
                        error_log('Failed to create directory: ' . $upload_dir);
                    }
                }
                
                $file_name = $_FILES['attachment']['name'];
                $file_tmp = $_FILES['attachment']['tmp_name'];
                $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $allowed_extensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($file_extension, $allowed_extensions)) {
                    $file_new_name = 'announcement_' . time() . '_' . uniqid() . '.' . $file_extension;
                    $target_file = $upload_dir . $file_new_name;
                    
                    if (move_uploaded_file($file_tmp, $target_file)) {
                        $attachment_url = '/c/zanvarsity/html/uploads/announcements/' . $file_new_name;
                        $attachment_name = $file_name;
                    } else {
                        $errors[] = 'Failed to upload attachment';
                    }
                } else {
                    $errors[] = 'Invalid file type. Allowed types: ' . implode(', ', $allowed_extensions);
                }
            }
            
            if (!empty($errors)) {
                $error_message = implode('<br>', $errors);
                error_log('Validation errors: ' . $error_message);
                if ($isAjax) {
                    sendJsonResponse(['success' => false, 'message' => $error_message]);
                } else {
                    $_SESSION['error'] = $error_message;
                    header('Location: manage_announcements.php');
                    exit();
                }
            }
            
            try {
                $conn->begin_transaction();
                
                if ($action === 'add_announcement') {
                    $sql = "INSERT INTO announcements 
                            (title, content, attachment_url, attachment_name, start_date, end_date, is_important, created_by, status) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $status = 'active'; // Default status
                    $stmt->bind_param("ssssssiis", 
                        $announcement['title'],
                        $announcement['content'],
                        $attachment_url,
                        $attachment_name,
                        $start_date,
                        $end_date,
                        $is_important,
                        $user_id,
                        $status
                    );
                } else {
                    $announcement_id = (int)$_POST['announcement_id'];
                    $sql = "UPDATE announcements 
                            SET title = ?, 
                                content = ?, 
                                start_date = ?, 
                                end_date = ?, 
                                is_important = ?,
                                status = ?";
                    
                    $params = [
                        $announcement['title'],
                        $announcement['content'],
                        $start_date,
                        $end_date,
                        $is_important,
                        $_POST['status'] ?? 'active'
                    ];
                    
                    // Only update attachment if a new one was uploaded
                    if ($attachment_url) {
                        $sql .= ", attachment_url = ?, attachment_name = ?";
                        $params[] = $attachment_url;
                        $params[] = $attachment_name;
                    }
                    
                    $sql .= " WHERE id = ?";
                    $params[] = $announcement_id;
                    
                    $types = str_repeat('s', count($params) - 1) . 'i';
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param($types, ...$params);
                }
                
                if ($stmt->execute()) {
                    $conn->commit();
                    $message = $action === 'add_announcement' ? 'Announcement added successfully!' : 'Announcement updated successfully!';
                    
                    if ($isAjax) {
                        sendJsonResponse(['success' => true, 'message' => $message]);
                    } else {
                        $_SESSION['success'] = $message;
                        header('Location: manage_announcements.php');
                        exit();
                    }
                } else {
                    throw new Exception('Database error: ' . $conn->error);
                }
            } catch (Exception $e) {
                $conn->rollback();
                $error = 'Failed to ' . ($action === 'add_announcement' ? 'add' : 'update') . ' announcement: ' . $e->getMessage();
                
                if ($isAjax) {
                    sendJsonResponse(['success' => false, 'message' => $error]);
                } else {
                    $_SESSION['error'] = $error;
                    header('Location: manage_announcements.php');
                    exit();
                }
            }
            break;
            
        case 'delete_announcement':
            try {
                if (empty($_POST['id'])) {
                    throw new Exception('Announcement ID is required');
                }
                
                $announcement_id = (int)$_POST['id'];
                
                // Get announcement to delete
                $sql = "SELECT attachment_url FROM announcements WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $announcement_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    throw new Exception('Announcement not found');
                }
                
                $announcement = $result->fetch_assoc();
                
                // Delete the announcement
                $sql = "DELETE FROM announcements WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $announcement_id);
                
                if ($stmt->execute()) {
                    // Delete the associated attachment if it exists
                    if (!empty($announcement['attachment_url'])) {
                        $file_path = str_replace('/c/zanvarsity/html', __DIR__, $announcement['attachment_url']);
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                    
                    if ($isAjax) {
                        sendJsonResponse(['success' => true, 'message' => 'Announcement deleted successfully']);
                    } else {
                        $_SESSION['success'] = 'Announcement deleted successfully';
                        header('Location: manage_announcements.php');
                        exit();
                    }
                } else {
                    throw new Exception('Failed to delete announcement: ' . $conn->error);
                }
            } catch (Exception $e) {
                if ($isAjax) {
                    sendJsonResponse(['success' => false, 'message' => $e->getMessage()]);
                } else {
                    $_SESSION['error'] = $e->getMessage();
                    header('Location: manage_announcements.php');
                    exit();
                }
            }
            break;
    }
}

// Fetch announcements from database
$announcements = [];
$sql = "SELECT a.*, u.first_name, u.last_name 
        FROM announcements a
        LEFT JOIN users u ON a.created_by = u.id
        ORDER BY a.start_date DESC, a.created_at DESC";
        
if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Include header
$page_title = 'Manage Announcements | Zanvarsity';
include 'includes/header.php';
?>

<div class="dashboard-container">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <?php include 'includes/sidebar.php'; ?>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Manage Announcements</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#announcementModal" onclick="resetForm()">
                            <i class="bi bi-plus-circle"></i> Add New Announcement
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($_SESSION['success'])): ?>
                            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                        <?php endif; ?>
                        
                        <?php if (!empty($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php endif; ?>
                        
                        <?php if (!empty($announcements)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Important</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($announcements as $announcement): ?>
                                            <tr>
                                                <td>
                                                    <?php echo htmlspecialchars($announcement['title']); ?>
                                                    <?php if ($announcement['attachment_url']): ?>
                                                        <i class="bi bi-paperclip ms-1" title="Has attachment"></i>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('M j, Y', strtotime($announcement['start_date'])); ?></td>
                                                <td><?php echo $announcement['end_date'] ? date('M j, Y', strtotime($announcement['end_date'])) : 'N/A'; ?></td>
                                                <td>
                                                    <?php if ($announcement['is_important']): ?>
                                                        <span class="badge bg-danger">Important</span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $announcement['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                        <?php echo ucfirst($announcement['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info edit-announcement" 
                                                            data-id="<?php echo $announcement['id']; ?>"
                                                            data-title="<?php echo htmlspecialchars($announcement['title']); ?>"
                                                            data-content="<?php echo htmlspecialchars($announcement['content']); ?>"
                                                            data-start-date="<?php echo date('Y-m-d', strtotime($announcement['start_date'])); ?>"
                                                            data-end-date="<?php echo $announcement['end_date'] ? date('Y-m-d', strtotime($announcement['end_date'])) : ''; ?>"
                                                            data-is-important="<?php echo $announcement['is_important']; ?>"
                                                            data-status="<?php echo $announcement['status']; ?>"
                                                            data-attachment-name="<?php echo htmlspecialchars($announcement['attachment_name'] ?? ''); ?>"
                                                            data-attachment-url="<?php echo htmlspecialchars($announcement['attachment_url'] ?? ''); ?>">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-danger delete-announcement" 
                                                            data-id="<?php echo $announcement['id']; ?>"
                                                            data-title="<?php echo htmlspecialchars($announcement['title']); ?>">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-megaphone" style="font-size: 3rem; color: #6c757d;"></i>
                                <h4>No announcements found</h4>
                                <p>Get started by adding your first announcement.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Announcement Modal -->
<div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="announcementModalLabel">Add New Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="announcementForm" enctype="multipart/form-data">
                <input type="hidden" name="action" id="announcementAction" value="add_announcement">
                <input type="hidden" name="announcement_id" id="announcementId">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="end_date" class="form-label">End Date (Optional)</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date">
                                        <div class="form-text">Leave empty if the announcement has no end date</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_important" name="is_important" value="1">
                                            <label class="form-check-label" for="is_important">Mark as Important</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="active" selected>Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="attachment" class="form-label">Attachment (Optional)</label>
                                <input type="file" class="form-control" id="attachment" name="attachment">
                                <div class="form-text">Allowed file types: PDF, DOC, DOCX, JPG, JPEG, PNG, GIF</div>
                                <div id="currentAttachment" class="mt-2" style="display: none;">
                                    <span class="text-muted">Current: </span>
                                    <a href="#" id="attachmentLink" target="_blank"></a>
                                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" id="removeAttachment">
                                        <i class="bi bi-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Announcement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the announcement "<span id="announcementTitle"></span>"?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete_announcement">
                    <input type="hidden" name="id" id="deleteAnnouncementId">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Load jQuery first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Load Bootstrap 5.1.3 Bundle (includes Popper) from a different CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

<!-- Debug script to check Bootstrap initialization -->
<script>
console.log('Bootstrap version:', bootstrap ? bootstrap.Tooltip.VERSION : 'Bootstrap not loaded');
</script>

<!-- Load CKEditor -->
<script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>

<script>
// Debug function to check if Bootstrap is loaded properly
function checkBootstrap() {
    console.log('Bootstrap check:', {
        'bootstrap exists': typeof bootstrap !== 'undefined',
        'bootstrap.Modal': typeof bootstrap !== 'undefined' ? typeof bootstrap.Modal : 'undefined',
        'bootstrap.Tooltip': typeof bootstrap !== 'undefined' ? typeof bootstrap.Tooltip : 'undefined'
    });
}

// Wait for everything to be loaded
window.addEventListener('load', function() {
    console.log('Window loaded, checking Bootstrap...');
    checkBootstrap();
    
    // Initialize tooltips with error handling
    try {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            if (tooltipTriggerEl) {
                try {
                    new bootstrap.Tooltip(tooltipTriggerEl);
                } catch (e) {
                    console.error('Error initializing tooltip:', e);
                }
            }
        });
    } catch (e) {
        console.error('Error initializing tooltips:', e);
    }
    
    // Simple function to show a modal by ID
    window.showModal = function(modalId) {
        try {
            console.log('Attempting to show modal:', modalId);
            var modalElement = document.getElementById(modalId);
            console.log('Modal element found:', !!modalElement);
            
            if (modalElement) {
                // Try to get existing instance or create new one
                var modal = bootstrap.Modal.getInstance(modalElement) || 
                           new bootstrap.Modal(modalElement, {
                               backdrop: 'static',
                               keyboard: false
                           });
                modal.show();
                return true;
            }
            return false;
        } catch (e) {
            console.error('Error in showModal:', e);
            // Try alternative method if first one fails
            try {
                var modalEl = document.getElementById(modalId);
                if (modalEl) {
                    $(modalEl).modal({show: true, backdrop: 'static', keyboard: false});
                    return true;
                }
            } catch (e2) {
                console.error('Alternative modal show failed:', e2);
            }
            return false;
        }
    };
    
    console.log('Modal handler initialized');
});
</script>

<script>
$(document).ready(function() {
    // Initialize CKEditor
    let editor;
    
    ClassicEditor
        .create(document.querySelector('#content'), {
            toolbar: {
                items: [
                    'heading', '|',
                    'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                    'indent', 'outdent', '|',
                    'blockQuote', 'insertTable', 'undo', 'redo'
                ]
            }
        })
        .then(newEditor => {
            editor = newEditor;
        })
        .catch(error => {
            console.error('Error initializing CKEditor:', error);
        });
    
    // Set today's date as default for start date
    const today = new Date().toISOString().split('T')[0];
    $('#start_date').attr('min', today);
    
    // Update end date min date when start date changes
    $('#start_date').on('change', function() {
        $('#end_date').attr('min', $(this).val());
    });
    
    // Handle edit announcement button click
    $(document).on('click', '.edit-announcement', function() {
        const id = $(this).data('id');
        const title = $(this).data('title');
        const content = $(this).data('content');
        const startDate = $(this).data('start-date');
        const endDate = $(this).data('end-date');
        const isImportant = $(this).data('is-important');
        const status = $(this).data('status');
        const attachmentName = $(this).data('attachment-name');
        const attachmentUrl = $(this).data('attachment-url');
        
        $('#announcementModalLabel').text('Edit Announcement');
        $('#announcementAction').val('update_announcement');
        $('#announcementId').val(id);
        $('#title').val(title);
        
        if (editor) {
            editor.setData(content);
        } else {
            $('#content').val(content);
        }
        
        $('#start_date').val(startDate);
        $('#end_date').val(endDate || '');
        $('#is_important').prop('checked', isImportant == 1);
        $('#status').val(status);
        
        // Handle attachment
        if (attachmentUrl) {
            $('#currentAttachment').show();
            $('#attachmentLink').attr('href', attachmentUrl).text(attachmentName || 'View Attachment');
        } else {
            $('#currentAttachment').hide();
        }
        
        // Show the modal with fallback
        if (!showModal('announcementModal')) {
            // Fallback to jQuery if Bootstrap fails
            console.warn('Bootstrap modal failed, trying jQuery fallback');
            $('#announcementModal').modal({show: true, backdrop: 'static', keyboard: false});
        }
    });
    
    // Handle remove attachment button
    $('#removeAttachment').on('click', function() {
        $('#currentAttachment').hide();
        $('#attachmentLink').attr('href', '#').text('');
        // Add a hidden field to indicate attachment removal
        if ($('#removeAttachmentFlag').length === 0) {
            $('#announcementForm').append('<input type="hidden" id="removeAttachmentFlag" name="remove_attachment" value="1">');
        }
    });
    
    // Handle delete announcement button click
    $(document).on('click', '.delete-announcement', function() {
        const id = $(this).data('id');
        const title = $(this).data('title');
        
        $('#announcementTitle').text(title);
        $('#deleteAnnouncementId').val(id);
        
        // Show the delete modal with fallback
        if (!showModal('deleteModal')) {
            // Fallback to jQuery if Bootstrap fails
            console.warn('Bootstrap modal failed, trying jQuery fallback');
            $('#deleteModal').modal({show: true, backdrop: 'static', keyboard: false});
        }
    });
    
    // Handle form submission
    $('#announcementForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Add CKEditor content to form data
        if (editor) {
            formData.set('content', editor.getData());
        }
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalBtnText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
        
        $.ajax({
            url: 'manage_announcements.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (data.success) {
                        // Show success message and reload the page
                        window.location.href = 'manage_announcements.php?success=' + encodeURIComponent(data.message);
                    } else {
                        // Show error message
                        alert(data.message || 'An error occurred. Please try again.');
                        submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                } catch (e) {
                    console.error('Error parsing response:', e, response);
                    alert('An error occurred while processing the response. Please check the console for details.');
                    submitBtn.prop('disabled', false).html(originalBtnText);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('An error occurred while saving the announcement. Please try again.');
                submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });
    
    // Handle delete form submission
    $('#deleteForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const deleteBtn = $(this).find('button[type="submit"]');
        const originalBtnText = deleteBtn.html();
        
        deleteBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...');
        
        $.post('manage_announcements.php', formData, function(response) {
            try {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                
                if (data.success) {
                    // Show success message and reload the page
                    window.location.href = 'manage_announcements.php?success=' + encodeURIComponent(data.message);
                } else {
                    // Show error message
                    alert(data.message || 'An error occurred while deleting the announcement.');
                    deleteBtn.prop('disabled', false).html(originalBtnText);
                }
            } catch (e) {
                console.error('Error parsing response:', e, response);
                alert('An error occurred while processing the response. Please check the console for details.');
                deleteBtn.prop('disabled', false).html(originalBtnText);
            }
        }).fail(function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            alert('An error occurred while deleting the announcement. Please try again.');
            deleteBtn.prop('disabled', false).html(originalBtnText);
        });
    });
    
    // Show success message from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const successMessage = urlParams.get('success');
    if (successMessage) {
        alert(decodeURIComponent(successMessage));
        // Remove the success parameter from URL
        const newUrl = window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
    }
});

// Reset form when adding a new announcement
function resetForm() {
    $('#announcementForm')[0].reset();
    $('#announcementModalLabel').text('Add New Announcement');
    $('#announcementAction').val('add_announcement');
    $('#announcementId').val('');
    $('#currentAttachment').hide();
    $('#attachmentLink').attr('href', '#').text('');
    $('#removeAttachmentFlag').remove();
    
    // Set default start date to today
    const today = new Date().toISOString().split('T')[0];
    $('#start_date').val(today);
    
    // Reset CKEditor if it exists
    if (typeof editor !== 'undefined') {
        editor.setData('');
    }
}
</script>
