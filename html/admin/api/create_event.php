<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../includes/database.php';

// Set JSON content type
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

// Get POST data
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$start_date = trim($_POST['start_date'] ?? '');
$end_date = trim($_POST['end_date'] ?? '');
$location = trim($_POST['location'] ?? '');
$status = 'upcoming'; // Default status

// Validate required fields
$errors = [];
if (empty($title)) {
    $errors['title'] = 'Title is required';
}
if (empty($start_date)) {
    $errors['start_date'] = 'Start date is required';
}

// If there are validation errors
if (!empty($errors)) {
    http_response_code(400); // Bad Request
    $response['message'] = 'Validation failed';
    $response['errors'] = $errors;
    echo json_encode($response);
    exit;
}

// Handle file upload
$image_path = '';
if (!empty($_FILES['event_image']['name'])) {
    $upload_dir = __DIR__ . '/../../../uploads/events/';
    
    // Create upload directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $file_name = uniqid('event_') . '_' . basename($_FILES['event_image']['name']);
    $target_file = $upload_dir . $file_name;
    
    // Check file type (allow only images)
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (!in_array($imageFileType, $allowed_types)) {
        $errors['event_image'] = 'Only JPG, JPEG, PNG & GIF files are allowed';
    }
    
    // Check file size (max 5MB)
    if ($_FILES['event_image']['size'] > 5 * 1024 * 1024) {
        $errors['event_image'] = 'File is too large. Maximum size is 5MB';
    }
    
    // If no errors, move the file
    if (empty($errors)) {
        if (move_uploaded_file($_FILES['event_image']['tmp_name'], $target_file)) {
            $image_path = '/zanvarsity/uploads/events/' . $file_name;
        } else {
            $errors['event_image'] = 'Error uploading file';
        }
    }
}

// If there were file upload errors
if (!empty($errors)) {
    http_response_code(400); // Bad Request
    $response['message'] = 'File upload failed';
    $response['errors'] = $errors;
    echo json_encode($response);
    exit;
}

try {
    // Start transaction
    $conn->begin_transaction();
    
    // Prepare and execute the query
    $sql = "INSERT INTO events (title, description, start_date, end_date, location, image_url, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }
    
    $stmt->bind_param('sssssss', $title, $description, $start_date, $end_date, $location, $image_path, $status);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute statement: ' . $stmt->error);
    }
    
    $event_id = $conn->insert_id;
    
    // Commit transaction
    $conn->commit();
    
    // Success response
    $response['success'] = true;
    $response['message'] = 'Event created successfully';
    $response['event_id'] = $event_id;
    
    echo json_encode($response);
    
} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn) && $conn->begin_transaction) {
        $conn->rollback();
    }
    
    // Delete uploaded file if there was an error
    if (!empty($image_path) && file_exists($_SERVER['DOCUMENT_ROOT'] . $image_path)) {
        unlink($_SERVER['DOCUMENT_ROOT'] . $image_path);
    }
    
    http_response_code(500); // Internal Server Error
    $response['message'] = 'Error creating event: ' . $e->getMessage();
    echo json_encode($response);
}

$conn->close();
?>
