<?php
// Database migration to create event_galleries table

// Get database connection
require_once __DIR__ . '/../../includes/database.php';

try {
    // Create event_galleries table
    $sql = "CREATE TABLE IF NOT EXISTS `event_galleries` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `event_id` int(11) NOT NULL,
        `image_url` varchar(255) NOT NULL,
        `caption` varchar(255) DEFAULT NULL,
        `is_primary` tinyint(1) DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `event_id` (`event_id`),
        CONSTRAINT `fk_event_gallery` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    if ($conn->query($sql) === TRUE) {
        echo "Table 'event_galleries' created successfully\n";
    } else {
        echo "Error creating table: " . $conn->error . "\n";
    }
    
    // Add a sample image to an event for testing (uncomment if needed)
    /*
    $sample_image = [
        'event_id' => 3, // Change this to an existing event ID
        'image_url' => 'uploads/events/sample-gallery.jpg',
        'caption' => 'Sample event gallery image',
        'is_primary' => 1
    ];
    
    $stmt = $conn->prepare("INSERT INTO event_galleries (event_id, image_url, caption, is_primary) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", 
        $sample_image['event_id'],
        $sample_image['image_url'],
        $sample_image['caption'],
        $sample_image['is_primary']
    );
    
    if ($stmt->execute()) {
        echo "Sample gallery image added successfully\n";
    } else {
        echo "Error adding sample image: " . $stmt->error . "\n";
    }
    $stmt->close();
    */
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$conn->close();
?>
