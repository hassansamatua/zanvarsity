<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once __DIR__ . '/includes/db_connect.php';

echo "<h2>Database Connection Test</h2>";

try {
    // Test PDO connection
    $pdo = getZanvarsityDbConnection();
    echo "<p style='color: green;'>✅ Successfully connected to database: " . DB_NAME . "</p>";
    
    // Check if publications table exists
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'publications'");
    
    if ($tableCheck->rowCount() > 0) {
        echo "<p style='color: green;'>✅ Publications table exists</p>";
        
        // Get table structure
        $structure = $pdo->query("DESCRIBE publications")->fetchAll(PDO::FETCH_COLUMN);
        echo "<h3>Table Structure:</h3>";
        echo "<pre>" . print_r($structure, true) . "</pre>";
        
        // Get sample data
        $query = "SELECT * FROM publications ORDER BY publication_date DESC LIMIT 5";
        $stmt = $pdo->query($query);
        $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Sample Publications:</h3>";
        if (count($publications) > 0) {
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>Title</th><th>Author</th><th>Date</th><th>Status</th></tr>";
            
            foreach ($publications as $pub) {
                echo sprintf(
                    "<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>",
                    htmlspecialchars($pub['id'] ?? ''),
                    htmlspecialchars(substr($pub['title'] ?? 'No title', 0, 50)),
                    htmlspecialchars(substr($pub['author'] ?? 'Unknown', 0, 30)),
                    $pub['publication_date'] ?? 'N/A',
                    $pub['status_desc'] ?? 'N/A'
                );
            }
            echo "</table>";
        } else {
            echo "<p>No publications found in the database.</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Publications table does not exist</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database Error: " . $e->getMessage() . "</p>";
}

// Show PHP and server info
echo "<h3>Environment Info:</h3>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "PDO Available: " . (class_exists('PDO') ? 'Yes' : 'No') . "\n";
if (class_exists('PDO')) {
    $drivers = PDO::getAvailableDrivers();
    echo "PDO Drivers: " . implode(', ', $drivers) . "\n";
}
echo "</pre>";
?>
