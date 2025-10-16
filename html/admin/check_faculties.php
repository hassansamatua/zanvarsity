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
$page_title = 'Check Faculties Table';

// Define base URL for assets
$base_url = '/c/zanvarsity/html';

// Include header
include dirname(__DIR__) . '/includes/about_header.php';

// Check if faculties table exists
$table_check = $conn->query("SHOW TABLES LIKE 'faculties'");
$table_exists = $table_check && $table_check->num_rows > 0;

// Get table structure if it exists
$table_structure = [];
if ($table_exists) {
    $result = $conn->query("DESCRIBE faculties");
    if ($result) {
        $table_structure = $result->fetch_all(MYSQLI_ASSOC);
    }
}

// Get count of faculties
$faculty_count = 0;
if ($table_exists) {
    $count_result = $conn->query("SELECT COUNT(*) as count FROM faculties");
    if ($count_result) {
        $faculty_count = $count_result->fetch_assoc()['count'];
    }
}

// Get some sample data
$sample_data = [];
if ($table_exists && $faculty_count > 0) {
    $sample_result = $conn->query("SELECT * FROM faculties LIMIT 5");
    if ($sample_result) {
        $sample_data = $sample_result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4">Faculties Table Check</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Database Information</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>Check</th>
                        <th>Status</th>
                    </tr>
                    <tr>
                        <td>Faculties Table Exists</td>
                        <td>
                            <?php if ($table_exists): ?>
                                <span class="badge bg-success">Yes</span>
                            <?php else: ?>
                                <span class="badge bg-danger">No</span>
                                <p class="mt-2 text-muted">The 'faculties' table doesn't exist in the database.</p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Number of Faculties</td>
                        <td><?php echo $faculty_count; ?> records found</td>
                    </tr>
                </table>
            </div>
            
            <?php if ($table_exists && !empty($table_structure)): ?>
                <h5 class="mt-4">Table Structure</h5>
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
            <?php endif; ?>
            
            <?php if (!empty($sample_data)): ?>
                <h5 class="mt-4">Sample Data (First 5 Records)</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <?php foreach (array_keys($sample_data[0]) as $column): ?>
                                    <th><?php echo htmlspecialchars($column); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sample_data as $row): ?>
                                <tr>
                                    <?php foreach ($row as $value): ?>
                                        <td><?php echo htmlspecialchars($value); ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif ($table_exists && $faculty_count > 0): ?>
                <div class="alert alert-warning mt-4">
                    <strong>Note:</strong> The table exists and has <?php echo $faculty_count; ?> records, but we couldn't fetch the data.
                    There might be an issue with the table structure or data types.
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (!$table_exists): ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Create Faculties Table</h6>
            </div>
            <div class="card-body">
                <p>The faculties table doesn't exist in your database. Click the button below to create it.</p>
                <form method="post" action="create_faculties_table.php">
                    <button type="submit" class="btn btn-primary">Create Faculties Table</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
// Include footer
include __DIR__ . '/includes/footer.php';
?>
