<?php
// Ensure no output before headers
while (ob_get_level() > 0) {
    ob_end_clean();
}
ob_start();

// Enable error logging to a file
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/delete_errors.log');

// Function to log debug information
function logDebug($message, $data = null) {
    $log = '[' . date('Y-m-d H:i:s') . '] ' . $message . "\n";
    if ($data !== null) {
        $log .= 'Data: ' . print_r($data, true) . "\n";
    }
    error_log($log, 3, __DIR__ . '/delete_debug.log');
}

// Set headers for JSON response
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: 0');

// Function to send JSON response
function sendJsonResponse($success, $message = '', $data = null, $statusCode = 200) {
    http_response_code($statusCode);
    $response = [
        'success' => $success,
        'message' => $message,
        'data' => $data
    ];
    
    // Clear any output buffers
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    $json = json_encode($response);
    
    // Log the response being sent
    logDebug('Sending JSON response', [
        'statusCode' => $statusCode,
        'response' => $response,
        'json' => $json
    ]);
    
    echo $json;
    exit;
}

// Start session
session_start();

// Log the start of the request
logDebug('=== New Request ===', [
    'method' => $_SERVER['REQUEST_METHOD'],
    'post' => $_POST,
    'session' => $_SESSION
]);

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logDebug('Invalid request method', $_SERVER['REQUEST_METHOD']);
    sendJsonResponse(false, 'Invalid request method', null, 405);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    logDebug('User not logged in');
    sendJsonResponse(false, 'Unauthorized access', null, 403);
}

// Check CSRF token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    logDebug('Invalid CSRF token', [
        'post_token' => $_POST['csrf_token'] ?? 'not set',
        'session_token' => $_SESSION['csrf_token'] ?? 'not set'
    ]);
    sendJsonResponse(false, 'Invalid CSRF token', null, 403);
}

// Validate ID
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    logDebug('Invalid carousel item ID', $_POST['id'] ?? 'not set');
    sendJsonResponse(false, 'Invalid carousel item ID', null, 400);
}

$id = (int)$_POST['id'];
logDebug('Processing delete request', ['id' => $id]);

// Include database connection
try {
    require_once __DIR__ . '/../includes/database.php';
    
    if (!isset($GLOBALS['conn']) || !($GLOBALS['conn'] instanceof mysqli)) {
        throw new Exception('Database connection not established');
    }
    
    $conn = $GLOBALS['conn'];
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Get image URL before deletion
        $stmt = $conn->prepare("SELECT image_url FROM carousel WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Failed to prepare SELECT statement: ' . $conn->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Carousel item not found');
        }
        
        $carouselItem = $result->fetch_assoc();
        $imageUrl = $carouselItem['image_url'] ?? '';
        $stmt->close();
        
        // Delete the carousel item
        $stmt = $conn->prepare("DELETE FROM carousel WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Failed to prepare DELETE statement: ' . $conn->error);
        }
        
        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to delete carousel item: ' . $stmt->error);
        }
        
        $stmt->close();
        
        // Delete the associated image file if it exists
        if (!empty($imageUrl)) {
            $imagePath = $_SERVER['DOCUMENT_ROOT'] . parse_url($imageUrl, PHP_URL_PATH);
            if (file_exists($imagePath) && is_file($imagePath)) {
                if (!@unlink($imagePath)) {
                    logDebug('Warning: Failed to delete image file', $imagePath);
                } else {
                    logDebug('Successfully deleted image file', $imagePath);
                }
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        logDebug('Successfully deleted carousel item', ['id' => $id]);
        sendJsonResponse(true, 'Carousel item deleted successfully');
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($conn) {
            $conn->rollback();
        }
        throw $e;
    }
    
} catch (Exception $e) {
    logDebug('Error in delete_carousel.php', [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    
    // Ensure we have a clean output buffer
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    // Send error response
    header('Content-Type: application/json', true, 500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage(),
        'error' => [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
    exit;
}

// Ensure no output after this point
if (ob_get_level() > 0) {
    ob_end_flush();
}
?>
