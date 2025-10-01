<?php
// Start session and include database connection
session_start();
require_once __DIR__ . '/../includes/database.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid carousel item ID'
    ]);
    exit;
}

$id = (int)$_GET['id'];
$conn = $GLOBALS['conn']; // Get the database connection from database.php

try {
    // First, check if the carousel item exists
    $sql = "SELECT * FROM carousel WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Carousel item not found'
        ]);
        exit;
    }
    
    $carouselItem = $result->fetch_assoc();
    
    // Prepare the response data
    $response = [
        'success' => true,
        'data' => [
            'id' => (int)$carouselItem['id'],
            'title' => $carouselItem['title'] ?? '',
            'description' => $carouselItem['description'] ?? '',
            'button_text' => $carouselItem['button_text'] ?? 'Learn More',
            'button_url' => $carouselItem['button_url'] ?? '#',
            'is_active' => (bool)($carouselItem['is_active'] ?? true),
            'display_order' => (int)($carouselItem['display_order'] ?? 0)
        ]
    ];
    
    // Check for image_url or image_path
    if (isset($carouselItem['image_url'])) {
        $response['data']['image_url'] = $carouselItem['image_url'];
    } elseif (isset($carouselItem['image_path'])) {
        $response['data']['image_url'] = $carouselItem['image_path'];
    } else {
        $response['data']['image_url'] = '';
    }
    
    // Output the JSON response
    echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // Log the error for debugging
    error_log('Error in get_carousel_item.php: ' . $e->getMessage());
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching the carousel item',
        'error' => $e->getMessage()
    ]);
} finally {
    // Close the statement if it was created
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close(); // Close the database connection
}
?>
