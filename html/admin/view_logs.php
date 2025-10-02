<?php
// Show all errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Path to the PHP error log
$logFile = ini_get('error_log');

// If no specific log file is set, try common locations
if (empty($logFile)) {
    $logFile = 'C:\xampp\php\logs\php_error_log';
    if (!file_exists($logFile)) {
        $logFile = 'C:\xampp\php\logs\error.log';
    }
}

echo "<h2>PHP Error Log: " . htmlspecialchars($logFile) . "</h2>";

if (file_exists($logFile)) {
    // Read the last 100 lines of the log file
    $logContent = `tail -n 100 "$logFile"`;
    // If the above doesn't work, try alternative method
    if (empty($logContent)) {
        $logContent = file_get_contents($logFile);
        $logLines = explode("\n", $logContent);
        $logContent = implode("\n", array_slice($logLines, -100));
    }
    echo "<pre>" . htmlspecialchars($logContent) . "</pre>";
} else {
    echo "<p>Error log file not found at: " . htmlspecialchars($logFile) . "</p>";
    echo "<p>Please check your PHP configuration (php.ini) for the correct error log location.</p>";
}
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    pre { 
        background: #f4f4f4; 
        padding: 15px; 
        border: 1px solid #ddd; 
        border-radius: 4px; 
        max-height: 600px; 
        overflow-y: auto; 
    }
</style>
