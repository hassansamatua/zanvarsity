<?php
// Start session and include database connection
session_start();
require_once __DIR__ . '/../includes/database.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /c/zanvarsity/html/admin/login.php');
    exit();
}

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to get the correct case of a file path
function getCorrectCasePath($path) {
    $basePath = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/';
    $parts = explode('/', trim($path, '/'));
    $currentPath = $basePath;
    
    foreach ($parts as $part) {
        if (empty($part)) continue;
        
        $found = false;
        if (is_dir($currentPath)) {
            if ($handle = opendir($currentPath)) {
                while (false !== ($file = readdir($handle))) {
                    if (strtolower($file) === strtolower($part)) {
                        $currentPath .= $file . '/';
                        $found = true;
                        break;
                    }
                }
                closedir($handle);
            }
        }
        
        if (!$found) {
            // If we can't find the exact case, use the original
            $currentPath .= $part . '/';
        }
    }
    
    // Convert to web path
    $webPath = str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $currentPath));
    return rtrim($webPath, '/');
}

// Get all carousel items with image paths
$result = $conn->query("SELECT id, image_path FROM carousel WHERE image_path IS NOT NULL AND image_path != ''");

if ($result) {
    $updates = [];
    
    while ($row = $result->fetch_assoc()) {
        $oldPath = $row['image_path'];
        $newPath = getCorrectCasePath($oldPath);
        
        if ($oldPath !== $newPath) {
            $updates[] = [
                'id' => $row['id'],
                'old' => $oldPath,
                'new' => $newPath
            ];
        }
    }
    
    // Process updates if any
    if (!empty($updates)) {
        $stmt = $conn->prepare("UPDATE carousel SET image_path = ? WHERE id = ?");
        
        foreach ($updates as $update) {
            $stmt->bind_param('si', $update['new'], $update['id']);
            if ($stmt->execute()) {
                echo "Updated ID {$update['id']}: <s>" . htmlspecialchars($update['old']) . "</s> → " . 
                     htmlspecialchars($update['new']) . "<br>\n";
            } else {
                echo "<span style='color:red'>Error updating ID {$update['id']}: " . $conn->error . "</span><br>\n";
            }
        }
        
        $stmt->close();
        echo "<p>All updates completed.</p>";
    } else {
        echo "<p>No path updates needed.</p>";
    }
}

// Show current carousel items
echo "<h2>Current Carousel Items</h2>";
$result = $conn->query("SELECT id, title, image_path FROM carousel ORDER BY id");

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Title</th><th>Image Path</th><th>Status</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($row['image_path'], '/');
        $exists = file_exists($fullPath) ? "<span style='color:green'>Exists</span>" : "<span style='color:red'>Not Found</span>";
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['image_path']) . "</td>";
        echo "<td>" . $exists . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No carousel items found.</p>";
}
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
    th { background-color: #f2f2f2; }
</style>
