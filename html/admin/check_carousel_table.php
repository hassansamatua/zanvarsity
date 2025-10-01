<?php
// Start session and include database connection
session_start();
require_once __DIR__ . '/../includes/database.php';

// Set content type to HTML
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carousel Table Check</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Carousel Table Check</h1>
        
        <?php
        try {
            $conn = $GLOBALS['conn'];
            
            // Check if table exists
            $table_check = $conn->query("SHOW TABLES LIKE 'carousel'");
            
            if ($table_check->num_rows === 0) {
                echo '<div class="alert alert-warning">Carousel table does not exist. Please run setup_database.php first.</div>';
                exit;
            }
            
            // Get table structure
            echo '<h3 class="mt-4">Table Structure</h3>';
            $result = $conn->query("DESCRIBE carousel");
            
            if ($result === false) {
                throw new Exception('Error describing table: ' . $conn->error);
            }
            
            echo '<table class="table table-bordered">';
            echo '<thead><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr></thead>';
            echo '<tbody>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['Field']) . '</td>';
                echo '<td>' . htmlspecialchars($row['Type']) . '</td>';
                echo '<td>' . htmlspecialchars($row['Null']) . '</td>';
                echo '<td>' . htmlspecialchars($row['Key']) . '</td>';
                echo '<td>' . htmlspecialchars($row['Default'] ?? 'NULL') . '</td>';
                echo '<td>' . htmlspecialchars($row['Extra']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
            
            // Get sample data
            echo '<h3 class="mt-4">Sample Data (first 5 rows)</h3>';
            $result = $conn->query("SELECT * FROM carousel ORDER BY id DESC LIMIT 5");
            
            if ($result === false) {
                throw new Exception('Error fetching data: ' . $conn->error);
            }
            
            if ($result->num_rows > 0) {
                echo '<table class="table table-bordered table-striped">';
                echo '<thead><tr>';
                // Header row
                $fields = $result->fetch_fields();
                foreach ($fields as $field) {
                    echo '<th>' . htmlspecialchars($field->name) . '</th>';
                }
                echo '</tr></thead><tbody>';
                
                // Data rows
                $result->data_seek(0); // Reset pointer
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    foreach ($row as $value) {
                        echo '<td>' . htmlspecialchars(substr($value ?? 'NULL', 0, 100)) . '</td>';
                    }
                    echo '</tr>';
                }
                echo '</tbody></table>';
            } else {
                echo '<div class="alert alert-info">No data found in carousel table.</div>';
            }
            
            // Check delete_carousel.php
            echo '<h3 class="mt-4">Delete Script Check</h3>';
            echo '<div class="card">';
            echo '<div class="card-body">';
            echo '<h5 class="card-title">delete_carousel.php</h5>';
            echo '<pre class="bg-light p-3">';
            $delete_script = file_get_contents(__DIR__ . '/delete_carousel.php');
            echo htmlspecialchars($delete_script);
            echo '</pre>';
            echo '</div></div>';
            
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
        
        <div class="mt-4">
            <a href="/zanvarsity/html/admin/manage_courasel.php" class="btn btn-primary">Back to Carousel Manager</a>
        </div>
    </div>
</body>
</html>
