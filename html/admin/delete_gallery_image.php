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
    'message' => ''
];

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit();
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $response['message'] = 'Invalid CSRF token';
    echo json_encode($response);
    exit();
}

// Check if image ID is provided
if (empty($_POST['id'])) {
    $response['message'] = 'Image ID is required';
    echo json_encode($response);
    exit();
}

$image_id = (int)$_POST['id'];

try {
    // Start transaction
    $GLOBALS['conn']->begin_transaction();
    
    // Get image info before deleting
    $stmt = $GLOBALS['conn']->prepare("SELECT id, event_id, image_url FROM event_galleries WHERE id = ?");
    $stmt->bind_param('i', $image_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Image not found');
    }
    
    $image = $result->fetch_assoc();
    $stmt->close();
    
    // Delete from database
    $stmt = $GLOBALS['conn']->prepare("DELETE FROM event_galleries WHERE id = ?");
    $stmt->bind_param('i', $image_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to delete image from database');
    }
    
    $stmt->close();
    
    // Delete the actual file
    if (!empty($image['image_url'])) {
        $filePath = __DIR__ . '/../..' . $image['image_url'];
        if (file_exists($filePath)) {
            @unlink($filePath);
            
            // Remove the directory if it's empty
            $dir = dirname($filePath);
            if (is_dir($dir) && count(glob("$dir/*")) === 0) {
                @rmdir($dir);
            }
        }
    }
    
    // Commit transaction
    $GLOBALS['conn']->commit();
    
    $response['success'] = true;
    $response['message'] = 'Image deleted successfully';
    
} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($GLOBALS['conn'])) {
        $GLOBALS['conn']->rollback();
    }
    
    $response['message'] = 'Error: ' . $e->getMessage();
    http_response_code(500);
}

echo json_encode($response);
?>
