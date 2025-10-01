<?php
// Start output buffering
ob_start();

// Start session
session_start();

// Set content type to JSON
header('Content-Type: application/json');

// Disable error display in production
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Include database connection
try {
    require_once __DIR__ . '/../includes/database.php';
    
    // Check if connection is established
    if (!isset($GLOBALS['conn']) || !($GLOBALS['conn'] instanceof mysqli)) {
        throw new Exception('Database connection not properly initialized');
    }
    
    $conn = $GLOBALS['conn'];
    
    // Test query to check carousel table
    $result = $conn->query("SHOW TABLES LIKE 'carousel'");
    if ($result->num_rows === 0) {
        throw new Exception('Carousel table does not exist');
    }
    
    // Get table structure
    $columns = [];
    $result = $conn->query("DESCRIBE carousel");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $columns[] = [
                'field' => $row['Field'],
                'type' => $row['Type'],
                'null' => $row['Null'],
                'key' => $row['Key'],
                'default' => $row['Default'],
                'extra' => $row['Extra']
            ];
        }
    }
    
    // Get sample data
    $sampleData = [];
    $result = $conn->query("SELECT * FROM carousel LIMIT 5");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $sampleData[] = $row;
        }
    }
    
    // Clear any output
    ob_end_clean();
    
    // Send success response
    echo json_encode([
        'success' => true,
        'message' => 'Database connection successful',
        'table_structure' => $columns,
        'sample_data' => $sampleData,
        'server_info' => [
            'php_version' => phpversion(),
            'mysql_version' => $conn->server_info,
            'charset' => $conn->character_set_name()
        ]
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    // Clear any output
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    // Send error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database test failed',
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
}

// Ensure no further output
if (ob_get_level() > 0) {
    ob_end_flush();
}
?>
