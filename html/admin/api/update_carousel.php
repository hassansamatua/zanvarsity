<?php
header('Content-Type: application/json');

// Start session and include required files
require_once '../../../includes/auth_functions.php';
require_once '../../../includes/database.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => ''
];

try {
    // Check if user is logged in and has admin access
    if (!is_logged_in()) {
        throw new Exception('Unauthorized access. Please log in.');
    }

    // Check if this is a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        throw new Exception('Invalid CSRF token');
    }

    // Get carousel item ID
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        throw new Exception('Invalid carousel item ID');
    }

    // Get database connection
    $conn = $GLOBALS['conn'] ?? null;
    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    // Check if carousel item exists
    $checkStmt = $conn->prepare("SELECT id, image_url FROM carousel WHERE id = ?");
    if (!$checkStmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }
    
    $checkStmt->bind_param('i', $id);
    if (!$checkStmt->execute()) {
        throw new Exception('Failed to check carousel item: ' . $checkStmt->error);
    }
    
    $result = $checkStmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception('Carousel item not found');
    }
    
    $existingItem = $result->fetch_assoc();
    $currentImagePath = $existingItem['image_url'];
    $newImagePath = $currentImagePath;
    $shouldDeleteOldImage = false;

    // Handle file upload if a new image is provided
    if (isset($_FILES['carousel_image']) && $_FILES['carousel_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/zanvarsity/html/uploads/carousel/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new Exception('Failed to create upload directory');
            }
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($_FILES['carousel_image']['tmp_name']);
        
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception('Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed.');
        }

        // Generate unique filename
        $fileExt = pathinfo($_FILES['carousel_image']['name'], PATHINFO_EXTENSION);
        $filename = 'carousel_' . time() . '_' . uniqid() . '.' . strtolower($fileExt);
        $targetPath = $uploadDir . $filename;

        // Move uploaded file
        if (!move_uploaded_file($_FILES['carousel_image']['tmp_name'], $targetPath)) {
            throw new Exception('Failed to upload image');
        }

        $newImagePath = '/zanvarsity/html/uploads/carousel/' . $filename;
        $shouldDeleteOldImage = true;
    }
    // Check if image should be removed
    elseif (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
        $newImagePath = '';
        $shouldDeleteOldImage = true;
    }

    // Prepare data for database
    $title = $conn->real_escape_string(trim($_POST['title'] ?? ''));
    $description = isset($_POST['description']) ? $conn->real_escape_string(trim($_POST['description'])) : '';
    $buttonText = isset($_POST['button_text']) ? $conn->real_escape_string(trim($_POST['button_text'])) : '';
    $buttonUrl = isset($_POST['button_url']) ? $conn->real_escape_string(trim($_POST['button_url'])) : '';
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $sortOrder = isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0;
    $now = date('Y-m-d H:i:s');

    // Update database
    $query = "UPDATE carousel SET 
              title = ?,
              description = ?,
              image_url = ?,
              button_text = ?,
              button_url = ?,
              is_active = ?,
              sort_order = ?,
              updated_at = ?
              WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }
    
    $stmt->bind_param(
        'sssssiisi',
        $title,
        $description,
        $newImagePath,
        $buttonText,
        $buttonUrl,
        $isActive,
        $sortOrder,
        $now,
        $id
    );
    
    if (!$stmt->execute()) {
        // Clean up newly uploaded file if database update fails
        if ($shouldDeleteOldImage && $newImagePath !== $currentImagePath && file_exists($_SERVER['DOCUMENT_ROOT'] . $newImagePath)) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $newImagePath);
        }
        throw new Exception('Failed to update carousel item: ' . $stmt->error);
    }
    
    // Delete old image if it was replaced or removed
    if ($shouldDeleteOldImage && !empty($currentImagePath) && file_exists($_SERVER['DOCUMENT_ROOT'] . $currentImagePath)) {
        unlink($_SERVER['DOCUMENT_ROOT'] . $currentImagePath);
    }
    
    $response['success'] = true;
    $response['message'] = 'Carousel item updated successfully';
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(400);
}

echo json_encode($response);
?>
