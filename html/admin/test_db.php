<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering
ob_start();

// Include database connection
require_once __DIR__ . '/../includes/database.php';

// Function to send JSON response
function sendJson($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit;
}

try {
    // Check if connection is established
    if (!isset($GLOBALS['conn']) || !($GLOBALS['conn'] instanceof mysqli)) {
        throw new Exception('Database connection not established');
    }
    
    $conn = $GLOBALS['conn'];
    
    // Test connection
    $serverInfo = [
        'server_version' => $conn->server_info,
        'host_info' => $conn->host_info,
        'protocol_version' => $conn->protocol_version,
        'client_version' => $conn->client_info,
        'charset' => $conn->character_set_name()
    ];
    
    // Check if table exists
    $result = $conn->query("SHOW TABLES LIKE 'carousel'");
    $tableExists = $result->num_rows > 0;
    
    $tableInfo = [];
    if ($tableExists) {
        // Get table structure
        $result = $conn->query("DESCRIBE carousel");
        $tableInfo['columns'] = [];
        while ($row = $result->fetch_assoc()) {
            $tableInfo['columns'][] = $row;
        }
        
        // Get sample data
        $result = $conn->query("SELECT * FROM carousel LIMIT 5");
        $tableInfo['sample_data'] = [];
        while ($row = $result->fetch_assoc()) {
            $tableInfo['sample_data'][] = $row;
        }
    }
    
    // Get all tables in the database
    $result = $conn->query("SHOW TABLES");
    $tables = [];
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
    
    // Clear any output
    ob_end_clean();
    
    // Send success response
    sendJson([
        'success' => true,
        'connection' => $serverInfo,
        'tables' => $tables,
        'carousel_table' => [
            'exists' => $tableExists,
            'structure' => $tableInfo['columns'] ?? null,
            'sample_data' => $tableInfo['sample_data'] ?? null
        ]
    ]);
    
} catch (Exception $e) {
    // Clear any output
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    // Send error response
    sendJson([
        'success' => false,
        'error' => [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ], 500);
}
?>
