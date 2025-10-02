<?php
// Database connection settings
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'zanvarsity';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Checking carousel table structure</h2>";

// Check if table exists
$result = $conn->query("SHOW TABLES LIKE 'carousel'");
if ($result->num_rows === 0) {
    die("Error: The 'carousel' table does not exist in the database.");
}

// Get table structure
echo "<h3>Table Structure:</h3>";
$result = $conn->query("DESCRIBE carousel");
echo "<table border='1'>
        <tr>
            <th>Field</th>
            <th>Type</th>
            <th>Null</th>
            <th>Key</th>
            <th>Default</th>
            <th>Extra</th>
        </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
    echo "<td>" . $row['Extra'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Show sample data
echo "<h3>Sample Data (first 5 rows):</h3>";
$result = $conn->query("SELECT * FROM carousel ORDER BY id LIMIT 5");
if ($result->num_rows > 0) {
    echo "<table border='1'><tr>";
    // Table headers
    while ($field = $result->fetch_field()) {
        echo "<th>" . $field->name . "</th>";
    }
    echo "</tr>";
    
    // Table data
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No data found in carousel table.";
}

$conn->close();
?>

<style>
table {
    border-collapse: collapse;
    margin: 15px 0;
}
table, th, td {
    border: 1px solid #ddd;
    padding: 8px;
}
th {
    background-color: #f2f2f2;
    text-align: left;
}
</style>
