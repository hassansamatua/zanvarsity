<?php
/**
 * Database connection file
 */

// Database configuration
$db_host = 'localhost';     // Database host
$db_name = 'zanvarsity_db';    // Database name
$db_user = 'root';          // Database username (default for XAMPP)
$db_pass = '';              // Database password (default for XAMPP is empty)

// Set character set
$charset = 'utf8mb4';

// Set DSN (Data Source Name)
$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$charset";

// Set PDO options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Create PDO instance
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
    
    // Set the PDO instance to be available globally
    $GLOBALS['pdo'] = $pdo;
    
    // For backward compatibility
    $conn = $pdo;
    
} catch (\PDOException $e) {
    // Log error and display a user-friendly message
    error_log('Database connection failed: ' . $e->getMessage());
    die('Could not connect to the database. Please try again later.');
}

// Function to get database connection
if (!function_exists('getZanvarsityDbConnection')) {
    function getZanvarsityDbConnection() {
        global $pdo;
        if (!isset($pdo)) {
            // Reconnect if connection is lost
            include __DIR__ . '/db_connect.php';
        }
        return $pdo;
    }
}

// Alias for backward compatibility - only define if not already defined
if (!function_exists('getDbConnection')) {
    function getDbConnection() {
        return getZanvarsityDbConnection();
    }
}

// Close the connection when the script ends
register_shutdown_function(function() {
    global $pdo;
    $pdo = null;
});

// Set timezone if not already set
if (ini_get('date.timezone') === '') {
    date_default_timezone_set('Africa/Dar_es_Salaam');
}
?>
