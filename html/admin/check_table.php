<?php
// Database connection settings
$servername = 'localhost';
$username = 'root';  // Default XAMPP username
$password = '';      // Default XAMPP password (empty)
$dbname = 'zanvarsity';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

echo "Successfully connected to database '$dbname'.\n\n";

// List all tables in the database
echo "Listing all tables in database '$dbname':\n";
$result = $conn->query("SHOW TABLES");
$tables = [];
$carousel_tables = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_array()) {
        $table_name = $row[0];
        $tables[] = $table_name;
        
        // Check if table name contains 'carousel' (case insensitive)
        if (stripos($table_name, 'carousel') !== false) {
            $carousel_tables[] = $table_name;
        }
    }
    
    echo "All tables: " . implode(', ', $tables) . "\n\n";
    
    if (empty($carousel_tables)) {
        die("No carousel-related tables found.\n");
    } else {
        echo "Found carousel-related tables: " . implode(', ', $carousel_tables) . "\n\n";
        // Get structure of the first carousel table found
        $table_name = $carousel_tables[0];
        echo "Checking structure of table '$table_name':\n";
        
        $result = $conn->query("DESCRIBE `$table_name`");
        if (!$result) {
            die("Error describing table: " . $conn->error . "\n");
        }
        
        echo str_pad("Field", 20) . str_pad("Type", 20) . str_pad("Null", 10) . str_pad("Key", 10) . str_pad("Default", 15) . "Extra\n";
        echo str_repeat("-", 80) . "\n";
        
        while ($row = $result->fetch_assoc()) {
            echo str_pad($row['Field'], 20) . 
                 str_pad($row['Type'], 20) . 
                 str_pad($row['Null'], 10) . 
                 str_pad($row['Key'], 10) . 
                 str_pad($row['Default'] ?? 'NULL', 15) . 
                 $row['Extra'] . "\n";
        }
        
        // Show sample data
        $result = $conn->query("SELECT * FROM `$table_name` LIMIT 1");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo "\nSample data from '$table_name':\n";
            print_r($row);
        } else {
            echo "\nNo data found in '$table_name' table.\n";
        }
    }
} else {
    die("No tables found in database.\n");
}

// Close the database connection
$conn->close();
?>
