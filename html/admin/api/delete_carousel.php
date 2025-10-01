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

    // Get and validate input
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input');
    }

    // Verify CSRF token
    if (!isset($input['csrf_token']) || $input['csrf_token'] !== $_SESSION['csrf_token']) {
        throw new Exception('Invalid CSRF token');
    }

    // Get carousel item ID
    $id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT);
    if (!$id) {
        throw new Exception('Invalid carousel item ID');
    }

    // Get database connection
    $conn = $GLOBALS['conn'] ?? null;
    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // First, get the image path
        $stmt = $conn->prepare("SELECT image_url FROM carousel WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $conn->error);
        }
        
        $stmt->bind_param('i', $id);
        if (!$stmt->execute()) {
            throw new Exception('Failed to fetch carousel item: ' . $stmt->error);
        }
        
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception('Carousel item not found');
        }
        
        $carouselItem = $result->fetch_assoc();
        $imagePath = $carouselItem['image_url'] ?? '';
        
        // Delete the carousel item
        $deleteStmt = $conn->prepare("DELETE FROM carousel WHERE id = ?");
        if (!$deleteStmt) {
            throw new Exception('Failed to prepare delete statement: ' . $conn->error);
        }
        
        $deleteStmt->bind_param('i', $id);
        if (!$deleteStmt->execute()) {
            throw new Exception('Failed to delete carousel item: ' . $deleteStmt->error);
        }
        
        // If we got this far, commit the transaction
        $conn->commit();
        
        // Delete the associated image file if it exists
        if (!empty($imagePath) && file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $imagePath);
        }
        
        $response['success'] = true;
        $response['message'] = 'Carousel item deleted successfully';
        
    } catch (Exception $e) {
        // Something went wrong, rollback the transaction
        $conn->rollback();
        throw $e; // Re-throw the exception to be caught by the outer catch block
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(400);
}

echo json_encode($response);
?>
