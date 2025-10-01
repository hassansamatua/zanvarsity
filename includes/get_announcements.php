<?php
require_once __DIR__ . '/db_connect.php';

// Function to get active announcements, limited to 3 most recent
function getAnnouncements($limit = 3) {
    $conn = require __DIR__ . '/db_connect.php';
    
    $sql = "SELECT id, title, location, announcement_date 
            FROM announcements 
            WHERE is_active = 1 
            ORDER BY announcement_date DESC 
            LIMIT ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $announcements = [];
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    return $announcements;
}

// Get announcements for the current request
$announcements = getAnnouncements(3);

// Function to format month in short format (e.g., Jan, Feb)
function formatMonth($date) {
    return strtolower(date('M', strtotime($date)));
}

// Function to format day (e.g., 01, 15, 30)
function formatDay($date) {
    return date('d', strtotime($date));
}
?>
