<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/../../includes/database.php';

// Set content type to JSON
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    $response['message'] = 'Invalid CSRF token';
    echo json_encode($response);
    exit;
}

// Validate required fields
$required = ['title', 'start_date'];
$event = [];
$errors = [];

foreach ($required as $field) {
    if (empty(trim($_POST[$field] ?? ''))) {
        $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
    } else {
        $event[$field] = trim($_POST[$field]);
    }
}

// Validate dates
try {
    $start_date = new DateTime($event['start_date']);
    $end_date = !empty($_POST['end_date']) ? new DateTime(trim($_POST['end_date'])) : null;
    
    if ($end_date && $end_date < $start_date) {
        $errors['end_date'] = 'End date must be after start date';
    }
} catch (Exception $e) {
    $errors['date'] = 'Invalid date format';
}

// Handle file upload
$uploaded_file = null;
if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Check file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $_FILES['event_image']['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_types)) {
        $errors['event_image'] = 'Only JPG, PNG, and GIF files are allowed';
    } elseif ($_FILES['event_image']['size'] > $max_size) {
        $errors['event_image'] = 'File size must be less than 5MB';
    } else {
        // Create uploads directory if it doesn't exist
        $upload_dir = __DIR__ . '/../../uploads/events/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Generate unique filename
        $file_extension = pathinfo($_FILES['event_image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('event_') . '.' . $file_extension;
        $destination = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['event_image']['tmp_name'], $destination)) {
            $event['image_url'] = '/zanvarsity/uploads/events/' . $filename;
        } else {
            $errors['event_image'] = 'Failed to upload file';
        }
    }
}

// If there are validation errors, return them
if (!empty($errors)) {
    $response['message'] = 'Validation failed';
    $response['errors'] = $errors;
    echo json_encode($response);
    exit;
}

try {
    $conn = $GLOBALS['conn'];
    
    // Prepare SQL query
    $sql = "INSERT INTO events (title, description, start_date, end_date, location, status, image_url) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }
    
    // Bind parameters
    $title = $event['title'];
    $description = $_POST['description'] ?? null;
    $start_date_str = $start_date->format('Y-m-d H:i:s');
    $end_date_str = $end_date ? $end_date->format('Y-m-d H:i:s') : null;
    $location = $_POST['location'] ?? null;
    $status = $_POST['status'] ?? 'upcoming';
    $image_url = $event['image_url'] ?? null;
    
    $stmt->bind_param(
        'sssssss',
        $title,
        $description,
        $start_date_str,
        $end_date_str,
        $location,
        $status,
        $image_url
    );
    
    // Execute query
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Event added successfully';
        $response['event_id'] = $stmt->insert_id;
    } else {
        throw new Exception('Failed to execute statement: ' . $stmt->error);
    }
    
    $stmt->close();
} catch (Exception $e) {
    // Clean up uploaded file if there was an error
    if (isset($event['image_url'])) {
        $file_path = __DIR__ . '/../..' . $event['image_url'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    $response['message'] = 'Database error: ' . $e->getMessage();
    error_log('Event creation error: ' . $e->getMessage());
}

// Return JSON response
echo json_encode($response);
