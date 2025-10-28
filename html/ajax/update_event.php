<?php
// Start session and include database connection
if (session_status() === PHP_SESSION_NONE) {
    session_name('zanvarsity_session');
    session_start();
}

// Set JSON content type
header('Content-Type: application/json');

// Include database connection
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

// Check if user is logged in and has permission
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Please log in.']);
    exit;
}

// Check if user has permission to manage events
$allowed_roles = ['admin', 'super_admin', 'dean'];
if (!in_array($_SESSION['role'], $allowed_roles)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Insufficient permissions']);
    exit;
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

// Check if required fields are present
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid event ID']);
    exit;
}

// Sanitize input
$eventId = (int)$_POST['id'];
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$location = trim($_POST['location'] ?? '');
$startDate = $_POST['start_date'] ?? '';
$endDate = $_POST['end_date'] ?? '';

// Validate required fields
if (empty($title) || empty($startDate)) {
    echo json_encode(['success' => false, 'message' => 'Title and start date are required']);
    exit;
}

try {
    // Begin transaction
    $conn->begin_transaction();
    
    // Check if event exists and user has permission to edit it
    $stmt = $conn->prepare("SELECT id FROM events WHERE id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Event not found');
    }
    
    // Handle file upload if a new image is provided
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/events/';
        
        // Create uploads directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate a unique filename
        $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = 'event_' . time() . '_' . uniqid() . '.' . $fileExtension;
        $targetPath = $uploadDir . $filename;
        
        // Move the uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = '/c/zanvarsity/html/uploads/events/' . $filename;
        } else {
            throw new Exception('Failed to upload image');
        }
    }
    
    // Update event in database
    if ($imagePath) {
        $stmt = $conn->prepare("UPDATE events SET title = ?, description = ?, location = ?, start_date = ?, end_date = ?, image_url = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $title, $description, $location, $startDate, $endDate, $imagePath, $eventId);
    } else {
        $stmt = $conn->prepare("UPDATE events SET title = ?, description = ?, location = ?, start_date = ?, end_date = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $title, $description, $location, $startDate, $endDate, $eventId);
    }
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update event in database');
    }
    
    // Commit the transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Event updated successfully',
        'data' => [
            'id' => $eventId,
            'title' => $title,
            'image_url' => $imagePath
        ]
    ]);
    
} catch (Exception $e) {
    // Rollback the transaction on error
    $conn->rollback();
    
    error_log("Error updating event: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error updating event: ' . $e->getMessage()
    ]);
}

$stmt->close();
$conn->close();
?>
