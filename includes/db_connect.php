<?php
// Database configuration
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'root');      // Default XAMPP username
if (!defined('DB_PASSWORD')) define('DB_PASSWORD', '');   // Default XAMPP password is empty
if (!defined('DB_NAME')) define('DB_NAME', 'zanvarsity_db');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for full Unicode support
$conn->set_charset("utf8mb4");

// Return the database connection
return $conn;
?>
