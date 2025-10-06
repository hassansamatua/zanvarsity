<?php
// Connect without selecting a database first
$conn = new mysqli('localhost', 'root', '');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// List all databases
$result = $conn->query("SHOW DATABASES");

if ($result) {
    echo "<h2>Available Databases:</h2>";
    echo "<ul>";
    while ($row = $result->fetch_array()) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
