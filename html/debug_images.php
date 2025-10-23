<?php
// Debug script to check event images
require_once __DIR__ . '/../includes/database.php';

// Query to get events with images
$query = "SELECT id, title, image_url, start_date FROM events WHERE image_url IS NOT NULL ORDER BY start_date DESC";
$result = $conn->query($query);

echo "<h2>Event Images Debug</h2>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%;'>
      <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Image Path (DB)</th>
        <th>Status</th>
        <th>Preview</th>
      </tr>";

while ($event = $result->fetch_assoc()) {
    $image_path = $event['image_url'];
    $full_path = $_SERVER['DOCUMENT_ROOT'] . '/c/zanvarsity/' . ltrim($image_path, '/');
    $web_path = '/c/zanvarsity/' . ltrim($image_path, '/');
    $exists = file_exists($full_path);
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($event['id']) . "</td>";
    echo "<td>" . htmlspecialchars($event['title']) . "</td>";
    echo "<td>" . htmlspecialchars($image_path) . "</td>";
    echo "<td>" . ($exists ? '✅ Found' : '❌ Not Found') . "</td>";
    
    if ($exists) {
        echo "<td><img src='" . htmlspecialchars($web_path) . "' style='max-width: 100px; max-height: 100px;' /></td>";
    } else {
        echo "<td>Not found at: " . htmlspecialchars($full_path) . "</td>";
    }
    
    echo "</tr>";
}

echo "</table>";

// Show server information
echo "<h3>Server Information</h3>";
echo "<pre>";
print_r([
    'Document Root' => $_SERVER['DOCUMENT_ROOT'],
    'Script Name' => $_SERVER['SCRIPT_NAME'],
    'Current Directory' => __DIR__
]);
echo "</pre>";
?>
