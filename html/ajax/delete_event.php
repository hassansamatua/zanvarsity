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

// Check if ID is provided
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid event ID']);
    exit;
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

$eventId = (int)$_POST['id'];

try {
    // Begin transaction
    $conn->begin_transaction();
    
    // First, get the event to delete its image
    $stmt = $conn->prepare("SELECT image_url FROM events WHERE id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Event not found');
    }
    
    $event = $result->fetch_assoc();
    
    // Delete the event from the database
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $eventId);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to delete event from database');
    }
    
    // If the event had an image, delete it
    if (!empty($event['image_url'])) {
        $imagePath = __DIR__ . '/../uploads/events/' . basename($event['image_url']);
        if (file_exists($imagePath)) {
            @unlink($imagePath); // Suppress warning if file doesn't exist
        }
    }
    
    // Commit the transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Event deleted successfully'
    ]);
    
} catch (Exception $e) {
    // Rollback the transaction on error
    $conn->rollback();
    
    error_log("Error deleting event: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error deleting event: ' . $e->getMessage()
    ]);
}

$stmt->close();
$conn->close();
?>
