<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";

// Include database connection
require_once __DIR__ . '/includes/db.php';

try {
    // Test database connection
    if ($conn->ping()) {
        echo "<p style='color: green;'>✅ Database connection successful!</p>";
        
        // Test query
        $result = $conn->query("SHOW TABLES");
        if ($result) {
            echo "<h3>Tables in the database:</h3>";
            echo "<ul>";
            while ($row = $result->fetch_array()) {
                echo "<li>" . $row[0] . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No tables found or error: " . $conn->error . "</p>";
        }
    } else {
        throw new Exception("Database connection failed");
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    
    // Show connection details (for debugging only)
    echo "<h3>Connection Details:</h3>";
    echo "<pre>Host: localhost\nDatabase: zanvarsity_db\nUser: root\nPassword: (empty)</pre>";
}
?>
