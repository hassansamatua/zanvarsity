<?php
/**
 * Database Connection
 */

// Database configuration
$db_host = 'localhost';
$db_name = 'zanvarsity_db'; // Replace with your actual database name
$db_user = 'root';         // Default XAMPP username
$db_pass = '';             // Default XAMPP password is empty

// Create connection
try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8mb4
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    // Log the error
    error_log("Database connection error: " . $e->getMessage());
    
    // Display a user-friendly error message
    die("We're experiencing technical difficulties. Please try again later.");
}

/**
 * Function to safely escape strings
 * Uses prepared statements instead of direct escaping
 */
function esc_str($conn, $str) {
    return $conn->real_escape_string($str);
}

// Set timezone
date_default_timezone_set('Africa/Dar_es_Salaam');
?>
