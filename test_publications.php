<?php
// Include database connection
require_once __DIR__ . '/includes/db_connect.php';

// Test query to get publications
$query = "SELECT * FROM publications ORDER BY publication_date DESC LIMIT 5";
$result = $conn->query($query);

echo "<h2>Database Connection Test</h2>";
echo "<p>Connected to database: " . DB_NAME . "</p>";

echo "<h3>Publications in database:</h3>";
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Author</th>
            <th>Publication Date</th>
            <th>Has Image</th>
            <th>Has Document</th>
        </tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>" . $row['id'] . "</td>
            <td>" . htmlspecialchars($row['title']) . "</td>
            <td>" . htmlspecialchars($row['author']) . "</td>
            <td>" . $row['publication_date'] . "</td>
            <td>" . (!empty($row['image_url']) ? 'Yes' : 'No') . "</td>
            <td>" . (!empty($row['document_url']) ? 'Yes' : 'No') . "</td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No publications found in the database or error in query.</p>";
    if ($conn->error) {
        echo "<p>Error: " . $conn->error . "</p>";
    }
}

// Show server info
echo "<h3>Server Info:</h3>";
echo "<pre>";
print_r([
    'PHP Version' => phpversion(),
    'MySQLi Extension' => extension_loaded('mysqli') ? 'Loaded' : 'Not Loaded',
    'Database Connection' => $conn ? 'Connected' : 'Failed',
    'Database Error' => $conn->error ?? 'None'
]);
echo "</pre>";
?>
