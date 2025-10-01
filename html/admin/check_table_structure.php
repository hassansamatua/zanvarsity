<?php
// Start output buffering
ob_start();

// Include database connection
require_once __DIR__ . '/../includes/database.php';

// Function to send JSON response
function sendJsonResponse($success, $message = '', $data = null, $statusCode = 200) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    http_response_code($statusCode);
    header('Content-Type: application/json');
    
    $response = [
        'success' => $success,
        'message' => $message,
        'data' => $data
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

try {
    // Check if connection is established
    if (!isset($GLOBALS['conn']) || !($GLOBALS['conn'] instanceof mysqli)) {
        throw new Exception('Database connection not established');
    }
    
    $conn = $GLOBALS['conn'];
    
    // Check if table exists
    $result = $conn->query("SHOW TABLES LIKE 'carousel'");
    if ($result->num_rows === 0) {
        sendJsonResponse(false, 'Carousel table does not exist');
    }
    
    // Get table structure
    $result = $conn->query("DESCRIBE carousel");
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row;
    }
    
    // Get sample data
    $result = $conn->query("SELECT * FROM carousel LIMIT 1");
    $sampleData = $result->num_rows > 0 ? $result->fetch_assoc() : null;
    
    // Get all column names
    $columnNames = array_column($columns, 'Field');
    
    sendJsonResponse(true, 'Table structure retrieved successfully', [
        'columns' => $columns,
        'column_names' => $columnNames,
        'sample_data' => $sampleData
    ]);
    
} catch (Exception $e) {
    sendJsonResponse(false, 'Error checking table structure: ' . $e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ], 500);
}

// Ensure no output after this point
if (ob_get_level() > 0) {
    ob_end_clean();
}
?>
