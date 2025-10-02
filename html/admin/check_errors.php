<?php
// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test error logging
$testMessage = "Test error message at " . date('Y-m-d H:i:s');

echo "<h1>PHP Error Log Test</h1>";
echo "<p>Test message: $testMessage</p>";

// Log a test message
if (error_log($testMessage, 0)) {
    echo "<p>Error log written successfully.</p>";
} else {
    echo "<p>Failed to write to error log.</p>";
}

// Show current error log settings
echo "<h2>Error Log Settings</h2>";
echo "<pre>";
echo "error_log = " . ini_get('error_log') . "\n";
echo "log_errors = " . ini_get('log_errors') . "\n";
echo "display_errors = " . ini_get('display_errors') . "\n";
echo "error_reporting = " . error_reporting() . "\n";

// Try to create the log file if it doesn't exist
$logFile = 'C:/xampp/php/logs/php_errors.log';
if (!file_exists($logFile)) {
    if (touch($logFile)) {
        echo "<p>Created log file: $logFile</p>";
        chmod($logFile, 0666);
        echo "<p>Set permissions on log file.</p>";
    } else {
        echo "<p>Failed to create log file: $logFile</p>";
        echo "<p>Check directory permissions for: C:/xampp/php/logs/</p>";
    }
} else {
    echo "<p>Log file exists: $logFile</p>";
    echo "<p>File permissions: " . substr(sprintf('%o', fileperms($logFile)), -4) . "</p>";
    
    // Test writing to the log file
    if (is_writable($logFile)) {
        echo "<p>Log file is writable.</p>";
        file_put_contents($logFile, "Test write at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    } else {
        echo "<p>Log file is not writable.</p>";
    }
}

echo "</pre>";

// Show some PHP info
// phpinfo();
?>
