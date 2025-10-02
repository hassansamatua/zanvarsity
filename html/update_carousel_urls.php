<?php
// Database connection
require_once __DIR__ . '/includes/database.php';

try {
    // Update all image URLs that start with /zanvarsity/ to c/zanvarsity/
    $sql = "UPDATE carousel 
            SET image_url = CONCAT('c', image_url)
            WHERE image_url LIKE '/zanvarsity/%'";
    
    $result = $conn->query($sql);
    
    if ($result) {
        echo "Successfully updated " . $conn->affected_rows . " records.<br>";
        
        // Show the updated records
        $select = "SELECT id, title, image_url FROM carousel";
        $result = $conn->query($select);
        
        echo "<h3>Updated Carousel Images:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Image URL</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
            echo "<td>" . htmlspecialchars($row['image_url']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Error updating records: " . $conn->error;
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

$conn->close();
?>
