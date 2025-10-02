<?php
// Start output buffering to catch any accidental output
ob_start();

// Start session and include database connection
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/c/zanvarsity/html/includes/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to send JSON response
function sendJsonResponse($success, $message = '', $data = null, $statusCode = 200) {
    // Clear any output buffers
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    http_response_code($statusCode);
    header('Content-Type: application/json');
    
    $response = [
        'success' => $success,
        'message' => $message,
        'data' => $data
    ];
    
    echo json_encode($response);
    exit;
}

// Check if request is POST and user is logged in
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    sendJsonResponse(false, 'Unauthorized access', null, 403);
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    sendJsonResponse(false, 'Invalid CSRF token', null, 403);
}

try {
    if (empty($_POST['id']) || !is_numeric($_POST['id'])) {
        sendJsonResponse(false, 'Invalid carousel item ID', null, 400);
    }

    // Get form data
    $id = intval($_POST['id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description'] ?? '');
    $buttonText = trim($_POST['button_text'] ?? '');
    $buttonUrl = trim($_POST['button_url'] ?? '');
    $displayOrder = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
    $isActive = isset($_POST['is_active']) && $_POST['is_active'] === '1';

    // Validate required fields
    if (empty($title)) {
    }

    // Handle file upload if provided
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $_FILES['image']['tmp_name']);
        
        if (!array_key_exists($mimeType, $allowedTypes)) {
            sendJsonResponse(false, 'Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed.', null, 400);
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
        
        // Move the uploaded file
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $error = error_get_last();
            error_log('File move error: ' . print_r($error, true));
            throw new Exception('Failed to move uploaded file: ' . ($error['message'] ?? 'Unknown error'));
        }
        
        // Set the web-relative path for the database
        $imagePath = rtrim($uploadDirWeb, '/') . '/' . $filename;
        error_log('File uploaded successfully. Path stored in DB: ' . $imagePath);
    }

    // Start transaction
    $conn->begin_transaction();
    
    try {
        // If new image was uploaded, get old image path to delete it later
        $oldImagePath = '';
        if (!empty($imagePath)) {
            $stmt = $conn->prepare("SELECT image_path FROM carousel WHERE id = ?");
            if (!$stmt) {
                throw new Exception('Database error: ' . $conn->error);
            }
            
            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) {
                throw new Exception('Database error: ' . $stmt->error);
            }
            
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $oldImagePath = $row['image_path'] ?? '';
            }
            $stmt->close();
        }
        
        // Update carousel item with proper parameter binding
        if (!empty($imagePath)) {
            // Prepare the update query with image
            $sql = "UPDATE carousel SET title = ?, description = ?, button_text = ?, button_url = ?, image_path = ?, display_order = ?, status = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception('Database error: ' . $conn->error);
            }
            
            // Convert isActive to status string
            $status = $isActive ? 'active' : 'inactive';
            $stmt->bind_param('sssssisi', $title, $description, $buttonText, $buttonUrl, $imagePath, $displayOrder, $status, $id);
        } else {
            // Prepare the update query without image
            $sql = "UPDATE carousel SET title = ?, description = ?, button_text = ?, button_url = ?, display_order = ?, status = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception('Database error: ' . $conn->error);
            }
            
            // Convert isActive to status string
            $status = $isActive ? 'active' : 'inactive';
            $stmt->bind_param('ssssisi', $title, $description, $buttonText, $buttonUrl, $displayOrder, $status, $id);
        }
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to update carousel item: ' . $stmt->error . ' (SQL: ' . $sql . ')');
        }
        
        // If this was a new image and we have an old one, delete the old image
        if (!empty($imagePath) && !empty($oldImagePath)) {
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
        
        $conn->commit();
        sendJsonResponse(true, 'Carousel item updated successfully');
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if (isset($conn)) {
            $conn->rollback();
        }
        
        // Log detailed error information
        $errorDetails = [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'post_data' => $_POST,
            'files_data' => !empty($_FILES) ? array_keys($_FILES) : 'No files uploaded'
        ];
        
        error_log('Error in update_carousel (inner catch): ' . print_r($errorDetails, true));
        
        // Delete the uploaded file if there was an error
        if (!empty($imagePath)) {
            $imagePath = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($imagePath, '/'));
            if (file_exists($imagePath)) {
                if (!@unlink($imagePath)) {
                    $error = error_get_last();
                    error_log('Failed to delete uploaded file after error: ' . ($error['message'] ?? 'Unknown error'));
                } else {
                    error_log('Successfully deleted uploaded file after error: ' . $imagePath);
                }
            }
        }
        
        // Re-throw the exception to be caught by the outer catch block
        throw $e;
    }
} catch (Exception $e) {
    // Log the error for debugging
    error_log('Error in update_carousel.php: ' . $e->getMessage());
    
    // Return error response
} finally {
    // Close the database connection
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
    
    // Ensure no output after this point
    if (ob_get_level() > 0) {
        ob_end_clean();
    }
}
?>
