<?php
// Include the configuration file
require_once __DIR__ . '/config.php';

// Create a new database connection
$test_conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check if the connection was established
if ($test_conn->connect_error) {
    die("<h2>Database Connection Failed!</h2><p>Error: " . $test_conn->connect_error . "</p>");
}

echo "<h2>Database Connection Successful!</h2>";
    echo "<h2>Database Connection Successful!</h2>";
    
    // Check if the database exists
    $result = $test_conn->query("SHOW TABLES");
    
    if ($result) {
        echo "<p>Successfully connected to database '" . DB_NAME . "'.</p>";
        
        // Check if the events table exists
        $result = $test_conn->query("SHOW TABLES LIKE 'events'");
        
        if ($result->num_rows > 0) {
            echo "<p>Table 'events' exists.</p>";
            
            // Get the number of events
            $result = $test_conn->query("SELECT COUNT(*) as count FROM events");
            $row = $result->fetch_assoc();
            echo "<p>Number of events: " . $row['count'] . "</p>";
            
            // Get the first few events
            $result = $test_conn->query("SELECT id, title, start_date, image_url FROM events ORDER BY start_date DESC LIMIT 5");
                
                if ($result->num_rows > 0) {
                    echo "<h3>Recent Events:</h3>";
                    echo "<ul>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<li>" . htmlspecialchars($row['title']) . " (ID: " . $row['id'] . ") - " . $row['start_date'] . "</li>";
                        echo "<li>Image URL: " . htmlspecialchars($row['image_url']) . "</li>";
                        
                        // Check if the image file exists
                        if (!empty($row['image_url'])) {
                            $image_path = __DIR__ . '/html/' . ltrim($row['image_url'], '/');
                            echo "<li>Image path: " . htmlspecialchars($image_path) . "</li>";
                            echo "<li>Image exists: " . (file_exists($image_path) ? 'Yes' : 'No') . "</li>";
                            
                            // If the image doesn't exist, check common locations
                            if (!file_exists($image_path)) {
                                $possible_paths = [
                                    __DIR__ . '/html/admin/' . ltrim($row['image_url'], '/'),
                                    __DIR__ . '/html/uploads/events/' . basename($row['image_url']),
                                    __DIR__ . '/html/admin/uploads/events/' . basename($row['image_url'])
                                ];
                                
                                foreach ($possible_paths as $path) {
                                    if (file_exists($path)) {
                                        echo "<li>Found image at: " . htmlspecialchars($path) . "</li>";
                                        break;
                                    }
                                }
                            }
                        }
                        echo "<br>";
                    }
                    echo "</ul>";
                    echo "<p>No events found in the database.</p>";
                }
            } else {
                echo "<p>Table 'events' does not exist.</p>";
            }
        } else {
            echo "<p>Failed to query database. Error: " . $test_conn->error . "</p>";
        }
    } else {
        echo "<p>Database '" . DB_NAME . "' does not exist.</p>";
    }
} else {
    // Error already handled above
}
{{ ... }}
// Show PHP info for debugging
echo "<h2>PHP Info:</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Current Directory: " . __DIR__ . "</p>";
?>
