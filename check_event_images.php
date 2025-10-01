<?php
require_once 'config.php';

// Check if event_galleries table exists
$table_check = $conn->query("SHOW TABLES LIKE 'event_galleries'");

if ($table_check && $table_check->num_rows > 0) {
    echo "event_galleries table exists.\n";
    
    // Get table structure
    $structure = $conn->query("DESCRIBE event_galleries");
    echo "\nTable structure:\n";
    while($row = $structure->fetch_assoc()) {
        echo "- {$row['Field']}: {$row['Type']} ({$row['Key']})\n";
    }
    
    // Get sample data
    $sample = $conn->query("SELECT * FROM event_galleries LIMIT 3");
    if ($sample->num_rows > 0) {
        echo "\nSample data:\n";
        while($row = $sample->fetch_assoc()) {
            print_r($row);
        }
    } else {
        echo "\nNo data found in event_galleries table.\n";
    }
} else {
    echo "event_galleries table does not exist.\n";
    
    // Create the table if it doesn't exist
    echo "\nCreating event_galleries table...\n";
    $sql = "CREATE TABLE IF NOT EXISTS `event_galleries` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `event_id` int(11) NOT NULL,
        `image_url` varchar(255) NOT NULL,
        `caption` varchar(255) DEFAULT NULL,
        `is_primary` tinyint(1) DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `event_id` (`event_id`),
        CONSTRAINT `event_galleries_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    if ($conn->query($sql) === TRUE) {
        echo "event_galleries table created successfully.\n";
    } else {
        echo "Error creating table: " . $conn->error . "\n";
    }
}

// Check events with images
echo "\nEvents with images:\n";
$events = $conn->query("SELECT id, title, image_url FROM events WHERE image_url IS NOT NULL AND image_url != '' LIMIT 5");
if ($events->num_rows > 0) {
    while($event = $events->fetch_assoc()) {
        $image_path = __DIR__ . '/' . ltrim($event['image_url'], '/');
        $image_exists = file_exists($image_path) ? 'Yes' : 'No';
        
        echo "- ID: {$event['id']}, Title: {$event['title']}\n";
        echo "  Image URL: {$event['image_url']}\n";
        echo "  Full path: $image_path\n";
        echo "  File exists: $image_exists\n";
        echo "  URL: " . rtrim(BASE_URL, '/') . '/' . ltrim($event['image_url'], '/') . "\n\n";
    }
} else {
    echo "No events with images found.\n";
}

// Check uploads directory
$upload_dir = __DIR__ . '/uploads/events';
echo "\nChecking uploads directory: $upload_dir\n";
if (is_dir($upload_dir)) {
    echo "- Directory exists\n";
    echo "- Is writable: " . (is_writable($upload_dir) ? 'Yes' : 'No') . "\n";
    
    // List files in uploads directory
    $files = scandir($upload_dir);
    if (count($files) > 2) {
        echo "\nFiles in uploads directory (first 5):\n";
        $count = 0;
        foreach($files as $file) {
            if (!in_array($file, ['.', '..']) && $count < 5) {
                $filepath = $upload_dir . '/' . $file;
                echo "- $file (" . filesize($filepath) . " bytes, " . 
                     (is_readable($filepath) ? 'readable' : 'not readable') . 
                     ", " . (is_writable($filepath) ? 'writable' : 'not writable') . ")\n";
                $count++;
            }
        }
        if (count($files) > 7) {
            echo "... and " . (count($files) - 7) . " more files\n";
        }
    } else {
        echo "- Uploads directory is empty\n";
    }
} else {
    echo "- Uploads directory does not exist\n";
    
    // Try to create the directory
    if (mkdir($upload_dir, 0755, true)) {
        echo "- Created uploads directory\n";
    } else {
        echo "- Failed to create uploads directory\n";
    }
}
?>
