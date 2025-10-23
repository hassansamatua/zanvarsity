<?php
// Start session
session_start();

// Include database connection
require_once __DIR__ . '/../includes/database.php';

// Query to get events with images
$query = "SELECT id, title, image_url FROM events WHERE image_url IS NOT NULL";
$result = $conn->query($query);

if (!$result) {
    die("Database query failed: " . $conn->error);
}

echo "<h2>Event Image Check</h2>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>
      <tr>
        <th>ID</th>
        <th>Title</th>
        <th>DB Image Path</th>
        <th>Resolved Path</th>
        <th>Exists</th>
        <th>Web Accessible URL</th>
      </tr>";

while ($event = $result->fetch_assoc()) {
    $image_url = $event['image_url'];
    $resolved_path = '';
    $exists = false;
    $web_url = '';
    $doc_root = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
    
    // Try to find the image in various locations
    $possible_paths = [
        $image_url,
        'c/zanvarsity/html/' . $image_url,
        'c/zanvarsity/html/uploads/events/' . basename($image_url),
        'c/zanvarsity/html/admin/' . $image_url,
        'c/zanvarsity/html/admin/uploads/events/' . basename($image_url),
        'zanvarsity/html/' . $image_url,
        'zanvarsity/html/uploads/events/' . basename($image_url),
        'html/' . $image_url,
        'html/uploads/events/' . basename($image_url),
        'uploads/events/' . basename($image_url),
        'admin/' . $image_url,
        'admin/uploads/events/' . basename($image_url)
    ];
    
    // Check each possible path
    foreach ($possible_paths as $path) {
        $full_path = $doc_root . '/' . ltrim($path, '/');
        if (file_exists($full_path)) {
            $resolved_path = $full_path;
            $exists = true;
            $web_url = '/' . ltrim($path, '/');
            break;
        }
    }
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($event['id']) . "</td>";
    echo "<td>" . htmlspecialchars($event['title']) . "</td>";
    echo "<td>" . htmlspecialchars($image_url) . "</td>";
    echo "<td>" . htmlspecialchars($resolved_path) . "</td>";
    echo "<td>" . ($exists ? '✅' : '❌') . "</td>";
    echo "<td>" . ($exists ? "<a href='$web_url' target='_blank'>View</a>" : 'N/A') . "</td>";
    echo "</tr>";
}

echo "</table>";
?>

<h3>Environment Information</h3>
<pre>
<?php
print_r([
    'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'],
    'SCRIPT_FILENAME' => $_SERVER['SCRIPT_FILENAME'],
    'PHP_SELF' => $_SERVER['PHP_SELF'],
    'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? null,
]);
?>
</pre>
