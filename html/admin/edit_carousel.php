<?php
// Start output buffering to catch any accidental output
ob_start();

// Start session and include database connection
session_start();
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/includes/auth.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is admin
    header('Location: /c/zanvarsity/html/admin/login.php');
    exit();
}

// Initialize variables
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$carouselItem = [
    'id' => 0,
    'title' => '',
    'description' => '',
    'button_text' => 'Learn More',
    'button_url' => '#',
    'image_path' => '',
    'display_order' => 0,
    'status' => 'active'
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: Log POST data
    error_log('POST Data: ' . print_r($_POST, true));
    error_log('FILES Data: ' . print_r($_FILES, true));
    
    // Get form data
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $buttonText = trim($_POST['button_text'] ?? 'Learn More');
    $buttonUrl = trim($_POST['button_url'] ?? '#');
    $displayOrder = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
    $status = (isset($_POST['status']) && $_POST['status'] === 'active') ? 'active' : 'inactive';
    
    // Debug: Log processed form data
    error_log("Processed Data - ID: $id, Title: $title, Status: $status, Display Order: $displayOrder");
    
    // Validate required fields
    $errors = [];
    if (empty($title)) {
        $errors[] = 'Title is required';
    }
    
    // If this is a new item, require an image
    if ($id === 0 && (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE)) {
        $errors[] = 'Image is required for new carousel items';
    }
    
    if (empty($errors)) {
        try {
            $conn->begin_transaction();
            
            // Handle file upload if provided
            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
                $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($fileInfo, $_FILES['image']['tmp_name']);
                
                if (!array_key_exists($mimeType, $allowedTypes)) {
                    throw new Exception('Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed.');
                }
                
                // Define the web-relative upload directory
                $uploadDirWeb = '/c/zanvarsity/html/uploads/carousel/';
                
                // Get the full server path for the upload directory
                $uploadDir = getServerPath($uploadDirWeb);
                
                // Create uploads directory if it doesn't exist
                if (!file_exists($uploadDir)) {
                    if (!mkdir($uploadDir, 0755, true)) {
                        $error = error_get_last();
                        error_log('Failed to create upload directory: ' . $uploadDir . ' - ' . ($error['message'] ?? 'Unknown error'));
                        throw new Exception('Failed to create upload directory');
                    }
                    error_log('Created upload directory: ' . $uploadDir);
                }
                
                // Ensure the upload directory is writable
                if (!is_writable($uploadDir)) {
                    error_log('Upload directory is not writable: ' . $uploadDir);
                    throw new Exception('Upload directory is not writable');
                }
                
                // Generate unique filename with lowercase extension
                $extension = strtolower($allowedTypes[$mimeType]);
                $filename = 'carousel_' . uniqid() . '.' . $extension;
                $targetPath = rtrim($uploadDir, '/') . '/' . $filename;
                
                // Log upload details for debugging
                error_log('Attempting to move uploaded file:');
                error_log('Source: ' . $_FILES['image']['tmp_name']);
                error_log('Destination: ' . $targetPath);
                
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $error = error_get_last();
                    error_log('File move error: ' . print_r($error, true));
                    throw new Exception('Failed to move uploaded file: ' . ($error['message'] ?? 'Unknown error'));
                }
                
                // Set the web-relative path for the database
                $imagePath = rtrim($uploadDirWeb, '/') . '/' . $filename;
                error_log('File uploaded successfully. Path stored in DB: ' . $imagePath);
                
                // If this is an update and we have a new image, delete the old one
                if ($id > 0) {
                    $stmt = $conn->prepare("SELECT image_path FROM carousel WHERE id = ?");
                    $stmt->bind_param('i', $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $oldImagePath = $row['image_path'];
                        
                        if (!empty($oldImagePath)) {
                            try {
                                // Get the full server path for the old image
                                $oldImageFullPath = getServerPath($oldImagePath);
                                
                                // Log the deletion attempt
                                error_log('Attempting to delete old image: ' . $oldImageFullPath);
                                
                                // Check if the file exists and is writable
                                if (file_exists($oldImageFullPath)) {
                                    if (is_writable($oldImageFullPath)) {
                                        if (@unlink($oldImageFullPath)) {
                                            error_log('Successfully deleted old image: ' . $oldImageFullPath);
                                        } else {
                                            $error = error_get_last();
                                            error_log('Failed to delete old image (unlink failed): ' . 
                                                     ($error['message'] ?? 'Unknown error'));
                                        }
                                    } else {
                                        error_log('Old image is not writable: ' . $oldImageFullPath);
                                    }
                                } else {
                                    error_log('Old image not found: ' . $oldImageFullPath);
                                }
                            } catch (Exception $e) {
                                // Log the error but don't fail the entire operation
                                error_log('Error deleting old image: ' . $e->getMessage());
                            }
                        }
                    }
                    $stmt->close();
                }
            }
            
            if ($id > 0) {
                // Update existing carousel item
                if (!empty($imagePath)) {
                    $sql = "UPDATE carousel SET title = ?, description = ?, button_text = ?, button_url = ?, image_path = ?, display_order = ?, status = ?, updated_at = NOW() WHERE id = ?";
                    error_log("Update SQL with image: $sql");
                    error_log("Params: title=$title, button_text=$buttonText, display_order=$displayOrder, status=$status, id=$id");
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('sssssisi', $title, $description, $buttonText, $buttonUrl, $imagePath, $displayOrder, $status, $id);
                } else {
                    $sql = "UPDATE carousel SET title = ?, description = ?, button_text = ?, button_url = ?, display_order = ?, status = ?, updated_at = NOW() WHERE id = ?";
                    error_log("Update SQL without image: $sql");
                    error_log("Params: title=$title, button_text=$buttonText, display_order=$displayOrder, status=$status, id=$id");
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('ssssisi', $title, $description, $buttonText, $buttonUrl, $displayOrder, $status, $id);
                }
            } else {
                // Insert new carousel item (image is required for new items)
                if (empty($imagePath)) {
                    throw new Exception('Image is required for new carousel items');
                }
                $stmt = $conn->prepare("INSERT INTO carousel (title, description, button_text, button_url, image_path, display_order, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                $stmt->bind_param('sssssis', $title, $description, $buttonText, $buttonUrl, $imagePath, $displayOrder, $status);
            }
            
            // Debug: Log the SQL query and parameters
            $debugSql = $sql;
            $debugParams = [
                'title' => $title,
                'description' => $description,
                'button_text' => $buttonText,
                'button_url' => $buttonUrl,
                'display_order' => $displayOrder,
                'status' => $status,
                'id' => $id
            ];
            if (!empty($imagePath)) {
                $debugParams['image_path'] = $imagePath;
            }
            
            error_log('Executing SQL: ' . $debugSql);
            error_log('With parameters: ' . print_r($debugParams, true));
            
            if (!$stmt->execute()) {
                $error = $stmt->error;
                error_log('Database error: ' . $error);
                throw new Exception('Failed to save carousel item: ' . $error);
            } else {
                $affected = $stmt->affected_rows;
                error_log("Query executed successfully. Affected rows: $affected");
            }
            
            // If this was a new item, get the new ID
            if ($id === 0) {
                $id = $conn->insert_id;
            }
            
            $conn->commit();
            
            // Redirect to prevent form resubmission
            $_SESSION['success'] = 'Carousel item ' . ($id > 0 ? 'updated' : 'added') . ' successfully';
            header('Location: edit_carousel.php?id=' . $id);
            exit();
            
        } catch (Exception $e) {
            if (isset($conn)) {
                $conn->rollback();
            }
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}
} elseif ($id > 0) {
    // Load existing carousel item for editing
    $stmt = $conn->prepare("SELECT * FROM carousel WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $carouselItem = $result->fetch_assoc();
    } else {
        $_SESSION['error'] = 'Carousel item not found';
        header('Location: manage_carousel.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <title><?= $id > 0 ? 'Edit' : 'Add'; ?> Carousel Item - Admin Panel</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .image-preview {
            max-width: 300px;
            max-height: 200px;
{{ ... }}
            display: <?= !empty($carouselItem['image_path']) ? 'block' : 'none'; ?>;
        }
        .required:after {
            content: ' *';
            color: #dc3545;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?= $id > 0 ? 'Edit' : 'Add New'; ?> Carousel Item</h1>
                    <a href="manage_carousel.php" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Back to List
                    </a>
                </div>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= $id; ?>">
                            
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="title" class="form-label required">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?= htmlspecialchars($carouselItem['title']); ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="display_order" class="form-label">Display Order</label>
                                    <input type="number" class="form-control" id="display_order" name="display_order" value="<?= htmlspecialchars($carouselItem['display_order'] ?? '0'); ?>" min="0">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                          rows="3"><?= htmlspecialchars($carouselItem['description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="button_text" class="form-label">Button Text</label>
                                    <input type="text" class="form-control" id="button_text" name="button_text" 
                                           value="<?= htmlspecialchars($carouselItem['button_text'] ?? 'Learn More'); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="button_url" class="form-label">Button URL</label>
                                    <input type="url" class="form-control" id="button_url" name="button_url" 
                                           value="<?= htmlspecialchars($carouselItem['button_url'] ?? '#'); ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="image" class="form-label">
                                    <?= $id > 0 ? 'Change Image' : 'Image'; ?>
                                    <?php if ($id === 0): ?><span class="required"></span><?php endif; ?>
                                </label>
                                <input type="file" class="form-control" id="image" name="image" 
                                       accept="image/jpeg,image/png,image/gif,image/webp" <?= $id === 0 ? 'required' : ''; ?>>
                                <small class="form-text text-muted">
                                    Recommended size: 1200x500px. Allowed types: JPG, PNG, GIF, WebP.
                                </small>
                                
                                <?php if (!empty($carouselItem['image_path'])): ?>
                                    <div class="mt-2">
                                        <p>Current Image:</p>
                                        <img src="/<?= htmlspecialchars(ltrim($carouselItem['image_path'], '/')); ?>" 
                                             alt="Current Image" class="img-thumbnail image-preview" id="imagePreview">
                                    </div>
                                <?php else: ?>
                                    <img src="#" alt="Image Preview" class="img-thumbnail image-preview" id="imagePreview">
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="status" name="status" 
                                       value="active" <?= (($carouselItem['status'] ?? 'active') === 'active') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="status">Active</label>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Save
                                    </button>
                                    <a href="manage_carousel.php" class="btn btn-secondary">
                                        <i class="fa fa-times"></i> Cancel
                                    </a>
                                </div>
                                
                                <?php if ($id > 0): ?>
                                <div>
                                    <a href="#" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <i class="fa fa-trash"></i> Delete
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <?php if ($id > 0): ?>
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this carousel item? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="delete_carousel.php" method="post" style="display: inline;">
                        <input type="hidden" name="id" value="<?= $id; ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? ''; ?>">
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image preview
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result).show();
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#image").change(function() {
            readURL(this);
            $('.image-preview').show();
        });
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>
</html>

<?php
// Close the database connection
if (isset($conn)) {
    $conn->close();
}

// End output buffering and flush the output
if (ob_get_level() > 0) {
    ob_end_flush();
}
?>
