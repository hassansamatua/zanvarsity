<?php
// Include database configuration
require_once __DIR__ . '/includes/database.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if announcements table exists
$result = $conn->query("SHOW TABLES LIKE 'announcements'");

if ($result->num_rows > 0) {
    echo "Announcements table exists.\n";
    
    // Show table structure
    $result = $conn->query("DESCRIBE announcements");
    if ($result) {
        echo "\nTable structure:\n";
        while ($row = $result->fetch_assoc()) {
            echo "- {$row['Field']} ({$row['Type']})\n";
        }
    } else {
        echo "Could not get table structure: " . $conn->error . "\n";
    }
    
    // Show count of announcements
    $result = $conn->query("SELECT COUNT(*) as count FROM announcements");
    $count = $result->fetch_assoc()['count'];
    echo "\nNumber of announcements: $count\n";
    
} else {
    echo "Announcements table does not exist.\n";
    
    // Create the table if it doesn't exist
    echo "Attempting to create announcements table...\n";
    
    $sql = "CREATE TABLE IF NOT EXISTS announcements (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        attachment_url VARCHAR(255) DEFAULT NULL,
        attachment_name VARCHAR(255) DEFAULT NULL,
        start_date DATE NOT NULL,
        end_date DATE DEFAULT NULL,
        is_important TINYINT(1) DEFAULT 0,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_by INT(11) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    if ($conn->query($sql) === TRUE) {
        echo "Announcements table created successfully.\n";
    } else {
        echo "Error creating table: " . $conn->error . "\n";
    }
}

$conn->close();
?>
