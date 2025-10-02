<?php
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/includes/auth.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Check if the migration has already been done
$result = $conn->query("SELECT COUNT(*) as count FROM carousel");
$row = $result->fetch_assoc();

if ($row['count'] > 0) {
    die("Migration already completed. Found {$row['count']} records in carousel table.");
}

// Begin transaction
$conn->begin_transaction();

try {
    // Move carousel items from announcements to carousel
    $sql = "
        INSERT INTO carousel 
            (title, description, image_url, button_text, button_url, is_active, sort_order, created_at, updated_at)
        SELECT 
            title, 
            description, 
            image_url, 
            COALESCE(button_text, 'Learn More') as button_text,
            COALESCE(button_url, '#') as button_url,
            COALESCE(is_active, 1) as is_active,
            COALESCE(sort_order, 0) as sort_order,
            COALESCE(created_at, NOW()) as created_at,
            COALESCE(updated_at, NOW()) as updated_at
        FROM announcements
        WHERE image_url LIKE '%/carousel/%' OR (title IS NOT NULL AND description IS NOT NULL)";
    
    $conn->query($sql);
    
    // Delete the migrated records from announcements
    $conn->query("DELETE FROM announcements WHERE image_url LIKE '%/carousel/%' OR (title IS NOT NULL AND description IS NOT NULL)");
    
    $conn->commit();
    
    echo "Migration completed successfully. " . $conn->affected_rows . " records migrated to carousel table.";
    
} catch (Exception $e) {
    $conn->rollback();
    die("Migration failed: " . $e->getMessage());
}

$conn->close();
?>
