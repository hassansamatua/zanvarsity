<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
require_once __DIR__ . '/../includes/database.php';

// Set JSON header
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'images' => []
];

// Check if event ID is provided
if (empty($_GET['event_id'])) {
    $response['message'] = 'Event ID is required';
    echo json_encode($response);
    exit();
}

$event_id = (int)$_GET['event_id'];

try {
    // Check if event exists
    $stmt = $GLOBALS['conn']->prepare("SELECT id FROM events WHERE id = ?");
    $stmt->bind_param('i', $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $response['message'] = 'Event not found';
        echo json_encode($response);
        exit();
    }
    $stmt->close();
    
    // Check if event_galleries table exists
    $tableCheck = $GLOBALS['conn']->query("SHOW TABLES LIKE 'event_galleries'");
    
    if ($tableCheck && $tableCheck->num_rows > 0) {
        // Get gallery images for the event
        $stmt = $GLOBALS['conn']->prepare("
            SELECT id, event_id, image_url, caption, created_at 
            FROM event_galleries 
            WHERE event_id = ? 
            ORDER BY is_primary DESC, created_at DESC
        ");
        $stmt->bind_param('i', $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $images = [];
        while ($row = $result->fetch_assoc()) {
            // Convert relative URL to absolute if needed
            if (!empty($row['image_url']) && strpos($row['image_url'], 'http') !== 0) {
                $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
                $row['image_url'] = rtrim($baseUrl, '/') . '/' . ltrim($row['image_url'], '/');
            }
            $images[] = $row;
        }
        
        $response['success'] = true;
        $response['images'] = $images;
        $response['message'] = count($images) . ' images found';
        $stmt->close();
    } else {
        $response['message'] = 'Gallery table does not exist';
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>
