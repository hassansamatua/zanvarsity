<?php
/**
 * Admin - User Management
 */

// Include configuration and authentication
require_once dirname(dirname(__DIR__)) . '/includes/config.php';
require_once dirname(dirname(__DIR__)) . '/includes/auth.php';

// Require admin access
requireAdmin();

// Set page title and body class
$pageTitle = 'User Management';
$bodyClass = 'page-admin';

// Include database connection
require_once dirname(dirname(__DIR__)) . '/includes/database.php';

// Verify database connection is established
if (!isset($conn) || !($conn instanceof mysqli)) {
    die('Database connection failed');
}

// Get any messages
$error = getError();
$success = getSuccess();

// Set user variables for header and sidebar
$user_name = $_SESSION['first_name'] ?? 'User';
$user_role = $_SESSION['role'] ?? 'student';
$role_display = ucfirst($user_role);
if ($user_role === 'super_admin') {
    $role_display = 'Super Admin';
} elseif ($user_role === 'admin') {
    $role_display = 'Administrator';
}

// Include header
include __DIR__ . '/../includes/header.php';
?>


<div class="container mt-4">
    <div class="row">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">User Management</h4>
                </div>
                <div class="card-body">
                    <!-- Add New User Button -->
                    <div class="mb-4">
                        <a href="users-new.php" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Add New User
                        </a>
                    </div>

                    <!-- Users Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    // Debug: Check database connection
                                    if (!$conn) {
                                        die("<tr><td colspan='6' class='text-center text-danger'><h4>Database Connection Failed</h4>" . 
                                            "<p>Error: " . mysqli_connect_error() . "</p>" .
                                            "<p>Host: $db_host<br>Database: $db_name</p></td></tr>");
                                    }
                                    
                                    
                                    // Check if users table exists
                                    $table_check = $conn->query("SHOW TABLES LIKE 'users'");
                                    if ($table_check->num_rows == 0) {
                                        $error = "<tr><td colspan='6' class='text-center text-danger'>" .
                                                "<h4>Users Table Not Found</h4>" .
                                                "<p>Available tables: " . implode(', ', $tables) . "</p>" .
                                                "<p>Database: " . htmlspecialchars($db_info['database']) . "</p>" .
                                                "<p>Server: " . htmlspecialchars($db_info['host_info']) . "</p>" .
                                                "<p>Version: " . htmlspecialchars($db_info['server_version']) . "</p>" .
                                                "</td></tr>";
                                        die($error);
                                    }

                                    // Debug: Show database info
                                    echo "<!-- Database Info:\n";
                                    echo "Host: " . htmlspecialchars($db_info['host_info']) . "\n";
                                    echo "Database: " . htmlspecialchars($db_info['database']) . "\n";
                                    echo "Server Version: " . htmlspecialchars($db_info['server_version']) . "\n";
                                    echo "Available Tables: " . implode(', ', $tables) . "\n";
                                    
                                    // First, let's check the structure of the users table
                                    $columns = [];
                                    $columns_result = $conn->query("DESCRIBE users");
                                    if ($columns_result) {
                                        echo "Users table columns:\n";
                                        while ($col = $columns_result->fetch_assoc()) {
                                            echo "- " . $col['Field'] . " (" . $col['Type'] . ")\n";
                                            $columns[] = $col['Field'];
                                        }
                                    } else {
                                        echo "Error describing users table: " . htmlspecialchars($conn->error) . "\n";
                                    }
                                    
                                    // Fetch users from database - using the actual column names from the table
                                    $query = "SELECT * FROM users ORDER BY id DESC";
                                    $result = $conn->query($query);

                                    // Debug information
                                    echo "\nQuery: " . htmlspecialchars($query) . "\n";
                                    if ($result === false) {
                                        echo "Query failed: " . htmlspecialchars($conn->error) . "\n";
                                    } else {
                                        echo "Number of rows found: " . $result->num_rows . "\n";
                                        if ($result->num_rows > 0) {
                                            $first_row = $result->fetch_assoc();
                                            $result->data_seek(0); // Reset pointer
                                            echo "First row sample: " . print_r($first_row, true) . "\n";
                                        }
                                    }
                                    echo "-->";

                                    if ($result && $result->num_rows > 0) {
                                        while ($user = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($user['id'] ?? 'N/A') . "</td>";
                                            echo "<td>" . htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) . "</td>";
                                            echo "<td>" . htmlspecialchars($user['email'] ?? 'N/A') . "</td>";
                                            echo "<td><span class='badge bg-info'>" . htmlspecialchars(ucfirst($user['role'] ?? 'user')) . "</span></td>";
                                            $is_active = $user['status'] ?? $user['is_active'] ?? 0;
                                            echo "<td><span class='badge " . ($is_active ? 'bg-success' : 'bg-secondary') . "'>" . 
                                                 ($is_active ? 'Active' : 'Inactive') . "</span></td>";
                                            echo "<td>";
                                            echo "<a href='users-edit.php?id=" . $user['id'] . "' class='btn btn-sm btn-outline-primary me-1' title='Edit'><i class='fa fa-edit'></i></a>";
                                            if ($_SESSION['user_id'] != $user['id']) { // Don't allow deleting own account
                                                echo "<a href='users-delete.php?id=" . $user['id'] . "' class='btn btn-sm btn-outline-danger' 
                                                     title='Delete' onclick='return confirm(\"Are you sure you want to delete this user?\")'><i class='fa fa-trash'></i></a>";
                                            }
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center'>No users found</td></tr>";
                                    }
                                } catch (Exception $e) {
                                    error_log("Error fetching users: " . $e->getMessage());
                                    echo "<tr><td colspan='6' class='text-center text-danger'>Error loading users. Please try again later.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include __DIR__ . '/../includes/footer.php';
?>



