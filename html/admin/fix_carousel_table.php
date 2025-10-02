<?php
require_once __DIR__ . '/../includes/database.php';

// Check current table structure
echo "<h2>Current Carousel Table Structure:</h2>";
$result = $conn->query("SHOW COLUMNS FROM carousel");
if ($result) {
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
} else {
    echo "Error: " . $conn->error;
}

// Check if we need to alter the table
echo "<h2>Attempting to fix table structure...</h2>";

try {
    // Check if display_order column exists
    $result = $conn->query("SHOW COLUMNS FROM carousel LIKE 'display_order'");
    if ($result->num_rows == 0) {
        echo "Adding missing 'display_order' column...<br>";
        $conn->query("ALTER TABLE carousel ADD COLUMN display_order INT DEFAULT 0 AFTER status");
        echo "Column 'display_order' added successfully.<br>";
    }

    // Check if status column exists and is correct type
    $result = $conn->query("SHOW COLUMNS FROM carousel LIKE 'status'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (strpos($row['Type'], 'enum') === false) {
            echo "Updating 'status' column to ENUM type...<br>";
            $conn->query("ALTER TABLE carousel MODIFY COLUMN status ENUM('active', 'inactive') DEFAULT 'active'");
            echo "Column 'status' updated successfully.<br>";
        }
    } else {
        echo "Adding missing 'status' column...<br>";
        $conn->query("ALTER TABLE carousel ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' AFTER image_path");
        echo "Column 'status' added successfully.<br>";
    }

    echo "<h3 style='color: green;'>Table structure updated successfully!</h3>";
    echo "<p>You can now <a href='manage_carousel.php'>go back to manage carousel</a>.</p>";

} catch (Exception $e) {
    echo "<h3 style='color: red;'>Error updating table structure:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Please check your database permissions or run these SQL commands manually in phpMyAdmin:</p>";
    echo "<pre>
ALTER TABLE carousel ADD COLUMN IF NOT EXISTS display_order INT DEFAULT 0 AFTER status;
ALTER TABLE carousel MODIFY COLUMN status ENUM('active', 'inactive') DEFAULT 'active';
    </pre>";
}
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>
