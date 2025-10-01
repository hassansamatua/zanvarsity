<?php
header('Content-Type: application/json');

// Start session and include required files
require_once '../../../includes/auth_functions.php';
require_once '../../../includes/database.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

try {
    // Check if user is logged in and has admin access
    if (!is_logged_in()) {
        throw new Exception('Unauthorized access. Please log in.');
    }

    // Get carousel item ID from request
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        throw new Exception('Invalid carousel item ID');
    }

    // Get database connection
    $conn = $GLOBALS['conn'] ?? null;
    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    // Prepare and execute query
    $query = "SELECT * FROM carousel WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }
    
    $stmt->bind_param('i', $id);
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute query: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception('Carousel item not found');
    }
    
    $carousel_item = $result->fetch_assoc();
    
    // Format response
    $response['success'] = true;
    $response['data'] = [
        'id' => (int)$carousel_item['id'],
        'title' => $carousel_item['title'],
        'description' => $carousel_item['description'],
        'image_url' => $carousel_item['image_url'],
        'button_text' => $carousel_item['button_text'],
        'button_url' => $carousel_item['button_url'],
        'is_active' => (bool)$carousel_item['is_active'],
        'sort_order' => (int)$carousel_item['sort_order'],
        'created_at' => $carousel_item['created_at'],
        'updated_at' => $carousel_item['updated_at']
    ];
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(400);
}

echo json_encode($response);
?>
