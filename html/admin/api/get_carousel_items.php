<?php
header('Content-Type: application/json');

// Start session and include required files
require_once '../../../includes/auth_functions.php';
require_once '../../../includes/database.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'data' => []
];

try {
    // Check if user is logged in and has admin access
    if (!is_logged_in()) {
        throw new Exception('Unauthorized access. Please log in.');
    }

    // Get database connection
    $conn = $GLOBALS['conn'] ?? null;
    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    // Fetch all carousel items
    $query = "SELECT * FROM carousel ORDER BY sort_order ASC, created_at DESC";
    $result = $conn->query($query);
    
    if ($result === false) {
        throw new Exception('Failed to fetch carousel items: ' . $conn->error);
    }
    
    $carousel_items = [];
    while ($row = $result->fetch_assoc()) {
        $carousel_items[] = [
            'id' => (int)$row['id'],
            'title' => $row['title'],
            'description' => $row['description'],
            'image_url' => $row['image_url'],
            'button_text' => $row['button_text'],
            'button_url' => $row['button_url'],
            'is_active' => (bool)$row['is_active'],
            'sort_order' => (int)$row['sort_order'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at']
        ];
    }
    
    $response['success'] = true;
    $response['data'] = $carousel_items;
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(500);
}

echo json_encode($response);
?>
