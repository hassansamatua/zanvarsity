<?php
// Include the database connection file
require_once __DIR__ . '/includes/db_connect.php';

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// First, check if the table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'announcements'");
if ($tableCheck->num_rows == 0) {
    // Table doesn't exist, create it
    $createTable = "CREATE TABLE `announcements` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `content` text NOT NULL,
        `start_date` datetime NOT NULL,
        `end_date` datetime DEFAULT NULL,
        `is_important` tinyint(1) NOT NULL DEFAULT '0',
        `status` enum('active','inactive') NOT NULL DEFAULT 'active',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `status` (`status`),
        KEY `start_date` (`start_date`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    if ($conn->query($createTable) === FALSE) {
        die("Error creating table: " . $conn->error);
    }
    echo "Created announcements table successfully.<br>";
} else {
    // Table exists, check if status column exists
    $columnCheck = $conn->query("SHOW COLUMNS FROM `announcements` LIKE 'status'");
    if ($columnCheck->num_rows == 0) {
        // Add status column if it doesn't exist
        $alterTable = "ALTER TABLE `announcements` 
                      ADD COLUMN `status` ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER `is_important`,
                      ADD INDEX `status` (`status`);";
        
        if ($conn->query($alterTable) === FALSE) {
            die("Error adding status column: " . $conn->error);
        }
        echo "Added status column to announcements table.<br>";
    }
}

// Get first available user ID to satisfy foreign key constraint
$adminUserId = 1; // Default admin user ID
$result = $conn->query("SELECT id FROM users ORDER BY id LIMIT 1");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $adminUserId = $row['id'];
}

// Sample announcements data
$announcements = [
    [
        'title' => 'Welcome to the New Academic Year',
        'content' => 'We are excited to welcome all students to the new academic year. Classes begin on October 1st.',
        'start_date' => '2025-10-01 08:00:00',
        'end_date' => '2025-10-15 23:59:59',
        'is_important' => 1,
        'status' => 'active',
        'created_by' => $adminUserId
    ],
    [
        'title' => 'Library Opening Hours',
        'content' => 'The university library will have extended opening hours during the examination period.',
        'start_date' => '2025-09-28 09:00:00',
        'end_date' => '2025-10-30 22:00:00',
        'is_important' => 0,
        'status' => 'active',
        'created_by' => $adminUserId
    ],
    [
        'title' => 'Career Fair 2025',
        'content' => 'Join us for our annual career fair on October 20th. Meet top employers and explore job opportunities.',
        'start_date' => '2025-10-20 09:00:00',
        'end_date' => '2025-10-20 17:00:00',
        'is_important' => 1,
        'status' => 'active',
        'created_by' => $adminUserId
    ]
];

// Prepare and execute the insert statement
$sql = "INSERT INTO announcements (title, content, start_date, end_date, is_important, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$count = 0;
foreach ($announcements as $announcement) {
    // Convert boolean to integer for is_important
    $is_important = $announcement['is_important'] ? 1 : 0;
    
    $stmt->bind_param("ssssisi", 
        $announcement['title'],
        $announcement['content'],
        $announcement['start_date'],
        $announcement['end_date'],
        $is_important,
        $announcement['status'],
        $announcement['created_by']
    );
    
    if ($stmt->execute()) {
        $count++;
        echo "Added: " . htmlspecialchars($announcement['title']) . "<br>";
    } else {
        echo "Error inserting announcement '{$announcement['title']}': " . $stmt->error . "<br>";
    }
    
    // Reset the statement for the next iteration
    $stmt->reset();
}

echo "Successfully added $count test announcements to the database.\n";

// Close connection
$stmt->close();
$conn->close();
?>
