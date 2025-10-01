<?php
/**
 * Database Setup Script
 * 
 * This script creates the necessary database tables and inserts sample data.
 * Run this script once to set up your database.
 */

// Include database connection
require_once __DIR__ . '/includes/db.php';

// Read the SQL file
$sql = file_get_contents(__DIR__ . '/database/create_tables.sql');

// Execute the SQL queries
if ($conn->multi_query($sql)) {
    echo "<h2>Database Setup Complete</h2>";
    echo "<p>The database tables have been created and populated with sample data.</p>";
    
    // Output success messages for each table
    $tables = ['carousel_slides', 'announcements', 'news', 'events'];
    foreach ($tables as $table) {
        $result = $conn->query("SELECT COUNT(*) as count FROM `$table`");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<p>✓ $table table created with " . $row['count'] . " records.</p>";
        } else {
            echo "<p>✓ $table table created.</p>";
        }
    }
    
    echo "<p><a href='index.php'>Go to Homepage</a></p>";
} else {
    echo "<h2>Error Setting Up Database</h2>";
    echo "<p>Error: " . $conn->error . "</p>";
}

// Close connection
$conn->close();
?>

<style>
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }
    h2 {
        color: #2c3e50;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
    p {
        margin: 10px 0;
    }
    a {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #3498db;
        color: white;
        text-decoration: none;
        border-radius: 4px;
    }
    a:hover {
        background-color: #2980b9;
    }
</style>
