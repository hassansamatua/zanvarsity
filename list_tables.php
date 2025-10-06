<?php
// Include database connection
require_once __DIR__ . '/includes/db_connect.php';

// Test connection to zanzivarsity_db
$conn->select_db('zanzivarsity_db');

// Get all tables
$result = $conn->query("SHOW TABLES");

if ($result) {
    echo "<h2>Tables in zanzivarsity_db:</h2>";
    echo "<ul>";
    while ($row = $result->fetch_array()) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
} else {
    echo "Error: " . $conn->error;
}

// Show current database being used
$result = $conn->query("SELECT DATABASE()");
$row = $result->fetch_row();
echo "<p>Currently using database: " . $row[0] . "</p>";
?>
