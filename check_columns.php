<?php
// Include database connection
require_once __DIR__ . '/includes/db_connect.php';

// Check if publications table exists
$result = $conn->query("SHOW COLUMNS FROM publications");

if ($result) {
    echo "<h2>Columns in publications table:</h2>";
    echo "<table border='1' cellpadding='5'>
        <tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>" . $row['Field'] . "</td>
            <td>" . $row['Type'] . "</td>
            <td>" . $row['Null'] . "</td>
            <td>" . $row['Key'] . "</td>
            <td>" . ($row['Default'] ?? 'NULL') . "</td>
            <td>" . $row['Extra'] . "</td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . $conn->error;
}

// Show sample data
$result = $conn->query("SELECT * FROM publications LIMIT 1");
if ($result && $result->num_rows > 0) {
    echo "<h2>Sample Publication Data:</h2>";
    echo "<pre>";
    print_r($result->fetch_assoc());
    echo "</pre>";
}
?>
