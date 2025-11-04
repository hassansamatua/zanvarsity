<?php
require_once __DIR__ . '/../includes/database.php';

echo "<h2>Fixing Events Table</h2>";

// First, check if the events table exists
$result = $conn->query("SHOW TABLES LIKE 'events'");
if ($result->num_rows === 0) {
    die("Error: Events table does not exist");
}

echo "Events table exists<br>";

// Check if created_by column exists
$result = $conn->query("SHOW COLUMNS FROM events LIKE 'created_by'");
if ($result->num_rows === 0) {
    echo "Adding created_by column...<br>";
    
    // Add the created_by column
    $sql = "ALTER TABLE events 
            ADD COLUMN created_by INT(11) NOT NULL AFTER image_url,
            ADD INDEX (created_by)";
            
    if ($conn->query($sql) === TRUE) {
        echo "Successfully added created_by column<br>";
        
        // Set a default user ID (assuming admin user with ID 1 exists)
        $update_sql = "UPDATE events SET created_by = 1 WHERE created_by = 0 OR created_by IS NULL";
        if ($conn->query($update_sql) === TRUE) {
            echo "Set default created_by values<br>";
        } else {
            echo "Warning: Could not set default created_by values: " . $conn->error . "<br>";
        }
        
        // Now add the foreign key constraint
        $fk_sql = "ALTER TABLE events 
                  ADD CONSTRAINT fk_events_created_by 
                  FOREIGN KEY (created_by) REFERENCES users(id) 
                  ON DELETE CASCADE";
                  
        if ($conn->query($fk_sql) === TRUE) {
            echo "Successfully added foreign key constraint<br>";
        } else {
            echo "Warning: Could not add foreign key constraint: " . $conn->error . "<br>";
            echo "The column was added but without the foreign key constraint.<br>";
        }
    } else {
        die("Error adding created_by column: " . $conn->error);
    }
} else {
    echo "created_by column already exists<br>";
}

// Verify the table structure
echo "<h3>Current events table structure:</h3>";
$structure = $conn->query("DESCRIBE events");
if ($structure) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $structure->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Test inserting an event
$test_title = "Test Event " . time();
$start_date = date('Y-m-d H:i:s');
$user_id = 1; // Assuming admin user with ID 1 exists

$sql = "INSERT INTO events (title, start_date, created_by) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("ssi", $test_title, $start_date, $user_id);

if ($stmt->execute()) {
    echo "<p style='color: green;'>Test event inserted successfully! Event ID: " . $stmt->insert_id . "</p>";
} else {
    echo "<p style='color: red;'>Error inserting test event: " . $stmt->error . "</p>";
}

$stmt->close();

// Show current events
echo "<h3>Current events:</h3>";
$result = $conn->query("
    SELECT e.*, u.email as creator_email, CONCAT(u.first_name, ' ', u.last_name) as creator_name 
    FROM events e 
    LEFT JOIN users u ON e.created_by = u.id 
    ORDER BY e.start_date DESC
    LIMIT 10
");

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Title</th><th>Start Date</th><th>Created By</th><th>Created At</th></tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . $row['start_date'] . "</td>";
        echo "<td>" . htmlspecialchars(($row['creator_name'] ?? 'Unknown') . ' (' . ($row['creator_email'] ?? 'N/A') . ')') . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "No events found in database";
}

$conn->close();
?>
