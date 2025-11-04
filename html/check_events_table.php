<?php
require_once __DIR__ . '/../includes/database.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if events table exists
$result = $conn->query("SHOW TABLES LIKE 'events'");
if ($result->num_rows === 0) {
    echo "Events table does not exist. Creating it now...<br>";
    
    // Create events table without foreign key first
    $sql = "CREATE TABLE IF NOT EXISTS `events` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT DEFAULT NULL,
        `start_date` DATETIME NOT NULL,
        `end_date` DATETIME DEFAULT NULL,
        `location` VARCHAR(255) DEFAULT NULL,
        `image_url` VARCHAR(512) DEFAULT NULL,
        `created_by` INT(11) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `created_by` (`created_by`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($sql) === TRUE) {
        echo "Events table created successfully<br>";
        
        // Now add the foreign key constraint
        $fk_sql = "ALTER TABLE `events` 
                  ADD CONSTRAINT `fk_events_created_by` 
                  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) 
                  ON DELETE CASCADE";
                  
        if ($conn->query($fk_sql) === TRUE) {
            echo "Foreign key constraint added successfully<br>";
        } else {
            echo "Warning: Could not add foreign key constraint: " . $conn->error . "<br>";
            echo "The table was created but without the foreign key constraint.<br>";
        }
    } else {
        die("Error creating table: " . $conn->error);
    }
} else {
    echo "Events table exists<br>";
}

// Check if users table exists
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows === 0) {
    die("Error: Users table does not exist. The events table requires a users table.");
}

// Get the first admin user
$admin_user = $conn->query("SELECT id FROM users WHERE role IN ('admin', 'super_admin') LIMIT 1");

if ($admin_user && $admin_user->num_rows > 0) {
    $user = $admin_user->fetch_assoc();
    $user_id = $user['id'];
    
    // Try to insert a test event
    $test_title = "Test Event " . time();
    $start_date = date('Y-m-d H:i:s');
    
    $sql = "INSERT INTO events (title, start_date, created_by) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    
    $stmt->bind_param("ssi", $test_title, $start_date, $user_id);
    
    if ($stmt->execute()) {
        echo "Test event inserted successfully<br>";
        echo "New event ID: " . $stmt->insert_id . "<br>";
    } else {
        echo "Error inserting test event: " . $stmt->error . "<br>";
    }
    
    $stmt->close();
} else {
    echo "No admin user found. Creating a test user...<br>";
    
    // Create a test user
    $test_email = "testuser_" . time() . "@example.com";
    $password_hash = password_hash("password123", PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (email, password, first_name, last_name, role, status) 
            VALUES (?, ?, 'Test', 'User', 'admin', 1)";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing user statement: " . $conn->error);
    }
    
    $stmt->bind_param("ss", $test_email, $password_hash);
    
    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;
        echo "Test user created with ID: $user_id<br>";
        
        // Now try to insert the event with the new user
        $test_title = "Test Event " . time();
        $start_date = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO events (title, start_date, created_by) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $test_title, $start_date, $user_id);
        
        if ($stmt->execute()) {
            echo "Test event inserted successfully with new user<br>";
            echo "New event ID: " . $stmt->insert_id . "<br>";
        } else {
            echo "Error inserting test event with new user: " . $stmt->error . "<br>";
        }
        
        $stmt->close();
    } else {
        echo "Error creating test user: " . $stmt->error . "<br>";
    }
}

// List all events with more details
$result = $conn->query("
    SELECT e.*, u.email as creator_email, CONCAT(u.first_name, ' ', u.last_name) as creator_name 
    FROM events e 
    LEFT JOIN users u ON e.created_by = u.id 
    ORDER BY e.start_date DESC
");

echo "<h3>Current events in database:</h3>";
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Title</th><th>Start Date</th><th>Location</th><th>Created By</th><th>Created At</th></tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . $row['start_date'] . "</td>";
        echo "<td>" . htmlspecialchars($row['location'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($row['creator_name'] . ' (' . $row['creator_email'] . ')') . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "No events found in database<br>";
    
    // Show the structure of the events table for debugging
    echo "<h3>Events table structure:</h3>";
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
    } else {
        echo "Could not retrieve table structure: " . $conn->error . "<br>";
    }
}

// Show any errors from the connection
if ($conn->error) {
    echo "<h3>Database Error:</h3>";
    echo $conn->error;
}

$conn->close();
?>
