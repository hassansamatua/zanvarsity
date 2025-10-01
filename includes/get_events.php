<?php
// Include common functions
require_once __DIR__ . '/common_functions.php';

// Check if constants are already defined before including db_connect
if (!defined('DB_HOST')) {
    require_once __DIR__ . '/db_connect.php';
}

/**
 * Get upcoming events
 * @param int $limit Number of events to return
 * @return array Array of events
 */
function getUpcomingEvents($limit = 5) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        return [];
    }
    
    // First, check if the events table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'events'");
    if ($tableCheck->num_rows == 0) {
        // Try alternative table name
        $tableCheck = $conn->query("SHOW TABLES LIKE 'event'");
        if ($tableCheck->num_rows == 0) {
            error_log("No events table found in the database");
            $conn->close();
            return [];
        } else {
            $tableName = 'event';
        }
    } else {
        $tableName = 'events';
    }
    
    // Main query to get events
    $sql = "SELECT 
                id, 
                title, 
                description,
                start_date,
                end_date,
                location,
                is_featured,
                created_at
            FROM $tableName 
            WHERE end_date >= CURDATE()
            ORDER BY start_date ASC 
            LIMIT ?";
    
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
    $events = [];
    
    while ($row = $result->fetch_assoc()) {
        // Add status class based on date
        $currentDate = new DateTime();
        $startDate = new DateTime($row['start_date']);
        $endDate = new DateTime($row['end_date']);
        
        if ($currentDate < $startDate) {
            $row['status'] = 'upcoming';
            $row['status_class'] = 'upcoming';
        } else if ($currentDate >= $startDate && $currentDate <= $endDate) {
            $row['status'] = 'ongoing';
            $row['status_class'] = 'ongoing';
        } else {
            $row['status'] = 'past';
            $row['status_class'] = 'past';
        }
        
        $events[] = $row;
    }
    
    $conn->close();
    
    return $events;
}
?>
