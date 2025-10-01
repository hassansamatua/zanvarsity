<?php
require_once 'config.php';

// Check connection
if ($GLOBALS['conn']->connect_error) {
    die("Connection failed: " . $GLOBALS['conn']->connect_error);
}

// Select the database
if (!$GLOBALS['conn']->select_db(DB_NAME)) {
    die("Could not select database: " . $GLOBALS['conn']->error);
}

// Check if event_galleries table exists
$result = $GLOBALS['conn']->query("SHOW TABLES LIKE 'event_galleries'");
if ($result->num_rows > 0) {
    echo "event_galleries table exists.\n";
    
    // Get table structure
    $structure = $GLOBALS['conn']->query("DESCRIBE event_galleries");
    echo "\nTable structure:\n";
    while($row = $structure->fetch_assoc()) {
        echo "- {$row['Field']}: {$row['Type']} ({$row['Key']})\n";
    }
    
    // Get sample data
    $sample = $GLOBALS['conn']->query("SELECT * FROM event_galleries LIMIT 3");
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
}

// Check events table for image URLs
$events = $GLOBALS['conn']->query("SELECT id, title, image_url FROM events WHERE image_url IS NOT NULL AND image_url != '' LIMIT 3");
if ($events->num_rows > 0) {
    echo "\n\nEvents with images:\n";
    while($event = $events->fetch_assoc()) {
        echo "ID: {$event['id']}, Title: {$event['title']}\n";
        echo "Image URL: {$event['image_url']}\n";
        echo "Full path: " . BASE_PATH . "/" . ltrim($event['image_url'], '/') . "\n";
        echo "URL: " . BASE_URL . ltrim($event['image_url'], '/') . "\n\n";
    }
} else {
    echo "\nNo events with images found.\n";
}

// Check if uploads directory exists and is writable
$upload_dir = BASE_PATH . '/uploads/events';
echo "\nChecking uploads directory: $upload_dir\n";
if (is_dir($upload_dir)) {
    echo "- Directory exists\n";
    echo "- Is writable: " . (is_writable($upload_dir) ? 'Yes' : 'No') . "\n";
    
    // List files in uploads directory
    $files = scandir($upload_dir);
    if (count($files) > 2) { // More than . and ..
        echo "\nFiles in uploads directory:\n";
        foreach($files as $file) {
            if (!in_array($file, ['.', '..'])) {
                $filepath = $upload_dir . '/' . $file;
                echo "- $file (" . filesize($filepath) . " bytes, " . 
                     (is_readable($filepath) ? 'readable' : 'not readable') . ") \n";
            }
        }
    } else {
        echo "- Uploads directory is empty\n";
    }
} else {
    echo "- Uploads directory does not exist\n";
}
?>
