<?php
// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'zanvarsity_db';

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("<h2>Connection failed:</h2> " . $conn->connect_error);
}

echo "<h2>Successfully connected to MySQL server!</h2>";

echo "<h3>Database Information:</h3>";
echo "<p>MySQL Server Version: " . $conn->server_info . "</p>";
echo "<p>Host Info: " . $conn->host_info . "</p>";

// List all databases
$result = $conn->query("SHOW DATABASES");

if ($result) {
    echo "<h3>Available Databases:</h3>";
    echo "<ul>";
    while ($row = $result->fetch_array()) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
    
    // Check if our database exists
    $result = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'");
    
    if ($result->num_rows > 0) {
        echo "<p>✅ Database '$dbname' exists.</p>";
        
        // Check if events table exists
        $conn->select_db($dbname);
        $result = $conn->query("SHOW TABLES LIKE 'events'");
        
        if ($result->num_rows > 0) {
            echo "<p>✅ 'events' table exists.</p>";
            
            // Get event count
            $result = $conn->query("SELECT COUNT(*) as count FROM events");
            $row = $result->fetch_assoc();
            echo "<p>Number of events: " . $row['count'] . "</p>";
            
            // Get some sample events
            $result = $conn->query("SELECT id, title, image_url FROM events ORDER BY id DESC LIMIT 5");
            
            echo "<h3>Sample Events:</h3>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Title</th><th>Image Path</th><th>File Exists</th></tr>";
            
            while ($event = $result->fetch_assoc()) {
                $image_path = __DIR__ . '/html/' . ltrim($event['image_url'], '/');
                $file_exists = file_exists($image_path) ? '✅' : '❌';
                
                echo "<tr>";
                echo "<td>" . $event['id'] . "</td>";
                echo "<td>" . htmlspecialchars($event['title']) . "</td>";
                echo "<td>" . htmlspecialchars($event['image_url']) . "</td>";
                echo "<td>$file_exists</td>";
                echo "</tr>";
                
                if (!$file_exists) {
                    echo "<tr><td colspan='4' style='color:red;'>File not found at: " . htmlspecialchars($image_path) . "</td></tr>";
                }
            }
            
            echo "</table>";
            
        } else {
            echo "<p>❌ 'events' table does not exist in database '$dbname'.</p>";
        }
    } else {
        echo "<p>❌ Database '$dbname' does not exist.</p>";
    }
} else {
    echo "<p>Error listing databases: " . $conn->error . "</p>";
}

// Close connection
$conn->close();

// Show server info
echo "<h3>Server Information:</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Current Directory: " . __DIR__ . "</p>";
?>
