<?php
// Database configuration
$db_host = 'localhost';
$db_name = 'zanvarsity_db';  // Using the updated database name
$db_user = 'root';
$db_pass = '';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Successfully connected to database: $db_name</h2>";

// Check if publications table exists
$result = $conn->query("SHOW TABLES LIKE 'publications'");
if ($result->num_rows > 0) {
    echo "<p>✅ Publications table exists.</p>";
    
    // Count publications
    $count = $conn->query("SELECT COUNT(*) as count FROM publications")->fetch_assoc()['count'];
    echo "<p>📊 Number of publications: " . $count . "</p>";
    
    // Show first 5 publications
    echo "<h3>Sample Publications:</h3>";
    $publications = $conn->query("SELECT * FROM publications ORDER BY publication_date DESC LIMIT 5");
    if ($publications->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Date</th>
                <th>Has Image</th>
            </tr>";
        while($pub = $publications->fetch_assoc()) {
            echo "<tr>
                <td>" . $pub['id'] . "</td>
                <td>" . htmlspecialchars($pub['title']) . "</td>
                <td>" . htmlspecialchars($pub['author']) . "</td>
                <td>" . $pub['publication_date'] . "</td>
                <td>" . (!empty($pub['image_url']) ? '✅' : '❌') . "</td>
            </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No publications found in the database.</p>";
    }
} else {
    echo "<p>❌ Publications table does not exist.</p>";
    
    // Show available tables
    echo "<h3>Available tables in $db_name:</h3>";
    $tables = $conn->query("SHOW TABLES");
    if ($tables->num_rows > 0) {
        echo "<ul>";
        while($table = $tables->fetch_array()) {
            echo "<li>" . $table[0] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No tables found in the database.</p>";
    }
}

$conn->close();
?>
