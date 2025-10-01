<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header for plain text
header('Content-Type: text/plain');

// Include database configuration
require_once __DIR__ . '/includes/db_connect.php';

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("ERROR: Connection failed: " . $conn->connect_error . "\n");
}

echo "✓ Database Connection Successful\n\n";

// Check if events table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'events'");
if ($tableCheck->num_rows == 0) {
    die("ERROR: 'events' table does not exist in the database.\n");
}
echo "✓ Events table exists\n\n";

// Get column information
echo "=== TABLE STRUCTURE ===\n";
$columns = $conn->query("SHOW COLUMNS FROM events");
while($col = $columns->fetch_assoc()) {
    echo str_pad($col['Field'], 20) . $col['Type'] . "\n";
}

// Try to get some data
echo "\n=== SAMPLE DATA (3 most recent events) ===\n";

$result = $conn->query("SELECT * FROM events ORDER BY start_date DESC LIMIT 3");

if ($result === false) {
    echo "Error executing query: " . $conn->error . "\n";
} else {
    // Display the event data
    if ($result->num_rows > 0) {
        // Get field names for headers
        $fields = $result->fetch_fields();
        
        // Print headers
        foreach ($fields as $field) {
            echo str_pad($field->name, 20);
        }
        echo "\n" . str_repeat("-", 20 * count($fields)) . "\n";
        
        // Print data
        while($row = $result->fetch_assoc()) {
            foreach ($row as $value) {
                $display = ($value === null) ? 'NULL' : substr($value, 0, 18);
                echo str_pad($display, 20);
            }
            echo "\n";
        }
    } else {
        echo "No events found in the database.\n";
    }
}

// Close connection
$conn->close();

echo "\n=== TEST COMPLETE ===\n";
