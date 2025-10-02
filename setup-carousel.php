<?php
// Include database connection
require_once __DIR__ . '/includes/database.php';

// Ensure carousel table exists
$createTableSQL = "CREATE TABLE IF NOT EXISTS carousel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_url VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (!$conn->query($createTableSQL)) {
    die("Error creating carousel table: " . $conn->error);
}

// Sample carousel data
$carouselItems = [
    [
        'title' => 'Welcome to Zanvarsity',
        'description' => 'Join our community of modern thinking students',
        'image_path' => 'assets/img/slide-1.jpg',
        'is_active' => 1
    ],
    [
        'title' => 'Excellence in Education',
        'description' => 'Providing quality education since 1998',
        'image_path' => 'assets/img/slide-2.jpg',
        'is_active' => 1
    ],
    [
        'title' => 'Innovative Learning',
        'description' => 'Experience cutting-edge learning facilities',
        'image_path' => 'assets/img/slide-3.jpg',
        'is_active' => 1
    ]
];

try {
    // Clear existing carousel items
    if (!$conn->query("TRUNCATE TABLE carousel")) {
        throw new Exception("Error clearing carousel: " . $conn->error);
    }
    
    // Prepare the insert statement
    $sql = "INSERT INTO carousel (title, description, image_url, is_active) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . $conn->error);
    }
    
    // Bind parameters and execute for each item
    $stmt->bind_param("sssi", $title, $description, $image_url, $is_active);
    
    foreach ($carouselItems as $item) {
        $title = $item['title'];
        $description = $item['description'];
        $image_url = $item['image_path'];
        
        if (!$stmt->execute()) {
            throw new Exception("Error inserting carousel item: " . $stmt->error);
        }
    }
    
    $stmt->close();
    
    echo "Carousel data has been successfully added to the database.\n";
    echo "<a href='index.php'>Go to Homepage</a>";
    
} catch (Exception $e) {
    die("Error setting up carousel: " . $e->getMessage());
}
?>
