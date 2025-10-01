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

    // Get database connection
    $conn = $GLOBALS['conn'] ?? null;
    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    // Validate required fields
    $required = ['title'];
    $missing = [];
    $data = [];

    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $missing[] = $field;
        } else {
            $data[$field] = trim($_POST[$field]);
        }
    }

    if (!empty($missing)) {
        throw new Exception('Missing required fields: ' . implode(', ', $missing));
    }

    // Handle file upload
    $imagePath = '';
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

        $imagePath = '/zanvarsity/html/uploads/carousel/' . $filename;
    } else {
        throw new Exception('Please upload an image');
    }

    // Prepare data for database
    $title = $conn->real_escape_string($data['title']);
    $description = isset($_POST['description']) ? $conn->real_escape_string($_POST['description']) : '';
    $buttonText = isset($_POST['button_text']) ? $conn->real_escape_string($_POST['button_text']) : '';
    $buttonUrl = isset($_POST['button_url']) ? $conn->real_escape_string($_POST['button_url']) : '';
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $sortOrder = isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0;
    $now = date('Y-m-d H:i:s');

    // Insert into database
    $query = "INSERT INTO carousel 
              (title, description, image_url, button_text, button_url, is_active, sort_order, created_at, updated_at)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }
    
    $stmt->bind_param(
        'sssssiiss',
        $title,
        $description,
        $imagePath,
        $buttonText,
        $buttonUrl,
        $isActive,
        $sortOrder,
        $now,
        $now
    );
    
    if (!$stmt->execute()) {
        // Clean up uploaded file if database insert fails
        if (file_exists($targetPath)) {
            unlink($targetPath);
        }
        throw new Exception('Failed to save carousel item: ' . $stmt->error);
    }
    
    $response['success'] = true;
    $response['message'] = 'Carousel item added successfully';
    $response['id'] = $stmt->insert_id;
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(400);
}

echo json_encode($response);
?>
