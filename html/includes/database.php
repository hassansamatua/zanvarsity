<?php
// Database configuration
$servername = 'localhost';
$username = 'root';  // Default XAMPP username
$password = '';      // Default XAMPP password is empty
$dbname = 'zanvarsity';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    if (!headers_sent()) {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Database connection failed',
            'error' => $conn->connect_error
        ]);
    }
    exit();
}

// Set charset to utf8mb4 for full Unicode support
$conn->set_charset('utf8mb4');

/**
 * Safely escape string for SQL queries
 * 
 * @param mysqli $conn Database connection
 * @param string $string String to escape
 * @return string Escaped string
 */
function escape($conn, $string) {
    return $conn->real_escape_string($string);
}

/**
 * Normalize file path to use forward slashes and remove double slashes
 * 
 * @param string $path Path to normalize
 * @param bool $isRelative Whether the path is relative to the document root
 * @return string Normalized path
 */
function normalizePath($path, $isRelative = false) {
    // Convert backslashes to forward slashes
    $path = str_replace('\\', '/', $path);
    
    // Remove any double slashes
    $path = preg_replace('#/+#', '/', $path);
    
    // If it's a relative path, ensure it starts with a single slash
    if ($isRelative) {
        $path = '/' . ltrim($path, '/');
    }
    
    return $path;
}

/**
 * Get the full server path for a web-relative path
 * 
 * @param string $webPath Path relative to web root (should start with /)
 * @return string Full server path
 */
function getServerPath($webPath) {
    $webPath = normalizePath($webPath, true);
    $docRoot = normalizePath($_SERVER['DOCUMENT_ROOT']);
    
    // Ensure the document root ends with a slash
    $docRoot = rtrim($docRoot, '/') . '/';
    
    return $docRoot . ltrim($webPath, '/');
}

/**
 * Get the web-relative path from a full server path
 * 
 * @param string $serverPath Full server path
 * @return string Web-relative path
 */
function getWebPath($serverPath) {
    $serverPath = normalizePath($serverPath);
    $docRoot = normalizePath($_SERVER['DOCUMENT_ROOT']);
    
    // If the path starts with the document root, remove it
    if (strpos($serverPath, $docRoot) === 0) {
        return '/' . ltrim(substr($serverPath, strlen($docRoot)), '/');
    }
    
    return $serverPath;
}

// Set error handler to log all errors
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log(sprintf(
        'PHP Error: %s in %s on line %d',
        $errstr,
        $errfile,
        $errline
    ));
    
    // Don't execute PHP internal error handler
    return true;
});

// Set exception handler
set_exception_handler(function($e) {
    error_log(sprintf(
        'Uncaught Exception: %s in %s on line %d\nStack trace:\n%s',
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
    ));
    
    if (!headers_sent()) {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'An unexpected error occurred',
            'error' => 'Internal server error'
        ]);
    }
    
    exit();
});

// Enable error reporting
ini_set('display_errors', '0');
error_reporting(E_ALL);
?>
