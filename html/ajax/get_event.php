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
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid event ID']);
    exit;
}

$eventId = (int)$_GET['id'];

try {
    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Event not found']);
        exit;
    }
    
    $event = $result->fetch_assoc();
    
    // Format dates for datetime-local input
    $event['start_date'] = date('Y-m-d\TH:i', strtotime($event['start_date']));
    if (!empty($event['end_date'])) {
        $event['end_date'] = date('Y-m-d\TH:i', strtotime($event['end_date']));
    } else {
        $event['end_date'] = '';
    }
    
    // Prepare image URL
    if (!empty($event['image_url'])) {
        $filename = basename($event['image_url']);
        $event['image_url'] = '/c/zanvarsity/html/uploads/events/' . $filename;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $event
    ]);
    
} catch (Exception $e) {
    error_log("Error fetching event: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching event: ' . $e->getMessage()
    ]);
}

$stmt->close();
$conn->close();
?>
