<?php
// Include necessary files
require_once __DIR__ . '/html/includes/db_connect.php';
require_once __DIR__ . '/html/includes/publications_functions.php';

// Get database connection
$pdo = getZanvarsityDbConnection();

// Call the function to fetch and save publications
$result = fetchAndSavePublications();

// Display results
echo "<h2>Publication Import Results</h2>";
if ($result['success']) {
    echo "<div style='color: green;'>✅ Successfully imported {$result['count']} publications.</div>";
    
    // Show a sample of the imported data
    $stmt = $pdo->query("SELECT * FROM publications ORDER BY id DESC LIMIT 3");
    $sample = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($sample) > 0) {
        echo "<h3>Sample Imported Publications:</h3>";
        echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Title</th><th>Author</th><th>Date</th><th>Description</th></tr>";
        
        foreach ($sample as $pub) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($pub['title'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($pub['author'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($pub['publication_date'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars(substr($pub['description'] ?? 'No description', 0, 100)) . "...</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Add a link to view all publications
    echo "<p><a href='/c/zanvarsity/html/index.php' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background: #004225; color: white; text-decoration: none; border-radius: 4px;'>View Publications on Homepage</a></p>";
} else {
    echo "<div style='color: red;'>❌ Error: " . htmlspecialchars($result['message'] ?? 'Unknown error') . "</div>";
}

// Show database info
try {
    $dbName = $pdo->query('SELECT DATABASE()')->fetchColumn();
    $tableExists = $pdo->query("SHOW TABLES LIKE 'publications'")->rowCount() > 0;
    $count = $pdo->query("SELECT COUNT(*) as count FROM publications")->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "<h3>Debug Info:</h3>";
    echo "<pre>";
    print_r([
        'Database' => $dbName,
        'Publications Table Exists' => $tableExists ? 'Yes' : 'No',
        'Total Publications in DB' => $count
    ]);
    echo "</pre>";
    
} catch (PDOException $e) {
    echo "<div style='color: red;'>Error getting database info: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>
