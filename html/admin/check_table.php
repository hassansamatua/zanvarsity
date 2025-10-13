<?php
// Start session and check authentication
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'super_admin'])) {
    header("Location: /c/zanvarsity/html/sign-in.php");
    exit();
}

// Include database connection
require_once __DIR__ . '/../includes/database.php';

// Set page title
$page_title = 'Check Faculty Table';

// Include header
include __DIR__ . '/includes/header.php';

// Check if faculty_tbl exists
$table_check = $conn->query("SHOW TABLES LIKE 'faculty_tbl'");
$table_exists = $table_check && $table_check->num_rows > 0;

// Get table structure if it exists
$table_structure = [];
if ($table_exists) {
    $result = $conn->query("DESCRIBE faculty_tbl");
    if ($result) {
        $table_structure = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4">Check Faculty Table</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Table Status</h6>
        </div>
        <div class="card-body">
            <?php if ($table_exists): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> The table 'faculty_tbl' exists in the database.
                </div>
                
                <h5>Table Structure:</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Type</th>
                                <th>Null</th>
                                <th>Key</th>
                                <th>Default</th>
                                <th>Extra</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($table_structure as $column): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($column['Field']); ?></td>
                                    <td><?php echo htmlspecialchars($column['Type']); ?></td>
                                    <td><?php echo htmlspecialchars($column['Null']); ?></td>
                                    <td><?php echo htmlspecialchars($column['Key']); ?></td>
                                    <td><?php echo htmlspecialchars($column['Default'] ?? 'NULL'); ?></td>
                                    <td><?php echo htmlspecialchars($column['Extra']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <a href="manage_faculties.php" class="btn btn-primary">Back to Faculties</a>
                
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> The table 'faculty_tbl' does not exist in the database.
                </div>
                
                <p>Would you like to create the faculty table with sample data?</p>
                <a href="create_faculty_table.php" class="btn btn-primary">
                    <i class="fas fa-database"></i> Create Faculty Table
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Include footer
include __DIR__ . '/includes/footer.php';
?>
