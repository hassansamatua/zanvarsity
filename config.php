<?php
// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Define base URL
if (php_sapi_name() !== 'cli') {
    if (!defined('BASE_URL')) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $script_name = dirname($_SERVER['SCRIPT_NAME']);
        
        // Remove the /html part if present in the script name
        $script_name = str_replace('/html', '', $script_name);
        
        // Ensure the script name ends with a slash
        if (substr($script_name, -1) !== '/') {
            $script_name .= '/';
        }
        
        define('BASE_URL', "$protocol://$host$script_name");
    }
} else {
    // For CLI usage
    define('BASE_URL', 'http://localhost/zanvarsity/');
}

// Set include path
set_include_path(get_include_path() . PATH_SEPARATOR . BASE_PATH . '/includes');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration - only define if not already defined
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_NAME')) define('DB_NAME', 'zanvarsity_db');

// Create connection if it doesn't exist
if (!isset($GLOBALS['conn'])) {
    $GLOBALS['conn'] = new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    // Check connection
    if ($GLOBALS['conn']->connect_error) {
        error_log("Database connection failed: " . $GLOBALS['conn']->connect_error);
        die("Database connection failed. Please try again later.");
    }
    
    // Set charset to utf8mb4 for full Unicode support
    $GLOBALS['conn']->set_charset("utf8mb4");
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($conn->query($sql) === FALSE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
if (!$conn->select_db(DB_NAME)) {
    die("Error selecting database: " . $conn->error);
}

// Create events table if not exists
$sql = "CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    start_date DATETIME NOT NULL,
    end_date DATETIME,
    location VARCHAR(255),
    image_url VARCHAR(512),
    status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === FALSE) {
    die("Error creating events table: " . $conn->error);
}

// Close the connection
$conn->close();
?>
