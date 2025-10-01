<?php
// Include common functions
require_once __DIR__ . '/common_functions.php';

// Check if constants are already defined before including db_connect
if (!defined('DB_HOST')) {
    require_once __DIR__ . '/db_connect.php';
}

// Function to get active announcements, limited to most recent
function getAnnouncements($limit = 5) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        return [];
    }
    
    // First, check if the announcements table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'announcements'");
    if ($tableCheck->num_rows == 0) {
        error_log("Table 'announcements' does not exist in the database");
        $conn->close();
        return [];
    }
    
    // Main query to get announcements
    $sql = "SELECT 
                id, 
                title, 
                content as description,
                start_date,
                end_date,
                is_important,
                status,
                created_at
            FROM announcements 
            WHERE status = 'active'
            ORDER BY start_date DESC, created_at DESC 
            LIMIT ?";
    
    error_log("Executing SQL: " . $sql);
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        $conn->close();
        return [];
    }
    
    $stmt->bind_param('i', $limit);
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        $stmt->close();
        $conn->close();
        return [];
    }
    
    $result = $stmt->get_result();
    $announcements = [];
    
    while ($row = $result->fetch_assoc()) {
        // Format dates for display
        $row['formatted_date'] = date('M j, Y', strtotime($row['start_date']));
        $row['start_time'] = date('g:i A', strtotime($row['start_date']));
        $row['end_time'] = !empty($row['end_date']) ? date('g:i A', strtotime($row['end_date'])) : '';
        
        // Add status class for styling
        $row['status_class'] = strtolower($row['status']);
        
        $announcements[] = $row;
    }
    
    error_log("Found " . count($announcements) . " announcements");
    
    $stmt->close();
    $conn->close();
    
    return $announcements;
}

// Get announcements for the current request
$announcements = getAnnouncements(5);

// Function to format day (e.g., 01, 15, 30)
function formatDay($date) {
    return date('d', strtotime($date));
}
?>
