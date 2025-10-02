<?php
// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/c/zanvarsity/html/includes/database.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'carousel'");
if ($tableCheck->num_rows === 0) {
    die("The 'carousel' table does not exist in the database.");
}

// Get current table structure
echo "<h2>Current Carousel Table Structure</h2>";
$result = $conn->query("DESCRIBE carousel");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
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

// Check for missing columns and add them if necessary
$requiredColumns = [
    'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
    'title' => 'VARCHAR(255) NOT NULL',
    'description' => 'TEXT',
    'button_text' => 'VARCHAR(100) DEFAULT NULL',
    'button_url' => 'VARCHAR(255) DEFAULT NULL',
    'image_path' => 'VARCHAR(512) NOT NULL',
    'display_order' => 'INT DEFAULT 0',
    'status' => "ENUM('active', 'inactive') DEFAULT 'active'",
    'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
];

echo "<h2>Checking and Adding Missing Columns</h2>";

foreach ($requiredColumns as $column => $definition) {
    $check = $conn->query("SHOW COLUMNS FROM carousel LIKE '$column'");
    
    if ($check->num_rows === 0) {
        echo "Adding column '$column'...<br>";
        $sql = "ALTER TABLE carousel ADD COLUMN $column $definition";
        
        // Special handling for the first column (primary key)
        if ($column === 'id') {
            $sql = "ALTER TABLE carousel MODIFY COLUMN $column $definition";
        }
        
        if ($conn->query($sql) === TRUE) {
            echo "Successfully added column '$column'<br>";
        } else {
            echo "Error adding column '$column': " . $conn->error . "<br>";
        }
    } else {
        echo "Column '$column' already exists<br>";
    }
}

echo "<h2>Table Structure After Updates</h2>";
$result = $conn->query("DESCRIBE carousel");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
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

echo "<h3>Database update complete.</h3>";
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>
