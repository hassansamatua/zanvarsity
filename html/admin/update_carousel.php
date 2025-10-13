<?php
// Start output buffering to catch any accidental output
ob_start();

// Start session and include database connection
session_start();
require_once __DIR__ . '/../includes/database.php';

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
    // Validate required fields
    if (empty($_POST['id']) || !is_numeric($_POST['id'])) {
        sendJsonResponse(false, 'Invalid carousel item ID', null, 400);
    }

    // Get form data
    $id = (int)$_POST['id'];
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $buttonText = trim($_POST['button_text'] ?? 'Learn More');
    $buttonUrl = trim($_POST['button_url'] ?? '#');
    $displayOrder = isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0;
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    // Validate required fields
    if (empty($title)) {
        sendJsonResponse(false, 'Title is required', null, 400);
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
        
        // Create uploads directory if it doesn't exist
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/zanvarsity/html/uploads/carousel/';
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new Exception('Failed to create upload directory');
            }
        }
        
        // Generate unique filename
        $extension = $allowedTypes[$mimeType];
        $filename = 'carousel_' . time() . '_' . uniqid() . '.' . $extension;
        $targetPath = $uploadDir . $filename;
        
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            throw new Exception('Failed to move uploaded file');
        }
        
        $imagePath = '/zanvarsity/html/uploads/carousel/' . $filename;
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
        
        // Update carousel item
        if (!empty($imagePath)) {
            $stmt = $conn->prepare("UPDATE carousel SET title = ?, description = ?, button_text = ?, button_url = ?, image_path = ?, display_order = ?, is_active = ?, updated_at = NOW() WHERE id = ?");
            if (!$stmt) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $stmt->bind_param("sssssiii", $title, $description, $buttonText, $buttonUrl, $imagePath, $displayOrder, $isActive, $id);
        } else {
            $stmt = $conn->prepare("UPDATE carousel SET title = ?, description = ?, button_text = ?, button_url = ?, display_order = ?, is_active = ?, updated_at = NOW() WHERE id = ?");
            if (!$stmt) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $stmt->bind_param("ssssiii", $title, $description, $buttonText, $buttonUrl, $displayOrder, $isActive, $id);
        }
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to update carousel item: ' . $stmt->error);
        }
        
        // Delete old image if a new one was uploaded and old one exists
        if (!empty($imagePath) && !empty($oldImagePath) && file_exists($_SERVER['DOCUMENT_ROOT'] . $oldImagePath)) {
            @unlink($_SERVER['DOCUMENT_ROOT'] . $oldImagePath);
        }
        
        $conn->commit();
        
        sendJsonResponse(true, 'Carousel item updated successfully', [
            'image_path' => $imagePath ?: $oldImagePath
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if (isset($conn)) {
            $conn->rollback();
        }
        
        // Delete the uploaded file if there was an error
        if (!empty($imagePath) && file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
            @unlink($_SERVER['DOCUMENT_ROOT'] . $imagePath);
        }
        
        throw $e;
    }
    
} catch (Exception $e) {
    // Log the error for debugging
    error_log('Error in update_carousel.php: ' . $e->getMessage());
    
    // Return error response
    sendJsonResponse(false, 'An error occurred while updating the carousel item: ' . $e->getMessage(), null, 500);
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
