<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /zanvarsity/html/register-sign-in.php');
    exit();
}

// Database connection
require_once __DIR__ . '/../../includes/database.php';

// Verify database connection is established
if (!isset($conn) || !($conn instanceof mysqli)) {
    die('Database connection failed');
}

// Set page title
$pageTitle = 'User Management';
$bodyClass = 'page-admin';

// Include header
include '../new-header.php';

// Include sidebar
include '../includes/sidebar.php';
?>

<!-- Page specific CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<style>
    .action-buttons {
        display: flex;
        gap: 10px;
        justify-content: center;
    }
    .action-btn {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        transition: all 0.3s ease;
    }
    .btn-add-user {
        margin-bottom: 20px;
    }
    .table-responsive {
        margin-top: 20px;
    }
</style>

<!-- Page Content -->
<div class="page-content">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>User Management</h1>
                
                <!-- Add/Edit User Form -->
                <div class="card mb-4" id="userFormContainer" style="display: none;">
                    <div class="card-header">
                        <h5 id="formTitle">Add New User</h5>
                    </div>
                    <div class="card-body">
                        <form id="userForm" method="post" class="row g-3">
                            <input type="hidden" name="user_id" id="userId">
                            <input type="hidden" name="action" id="formAction" value="add">
                            
                            <div class="col-md-6">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="first_name" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="last_name" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="instructor">Instructor</option>
                                    <option value="student">Student</option>
                                </select>
                            </div>
                            
                            <div class="col-12" id="passwordField">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" minlength="6">
                                <div class="form-text">Leave blank to keep current password (when editing)</div>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="isActive">Active</label>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Save User</button>
                                <button type="button" class="btn btn-secondary" id="cancelEdit">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Add New User Button -->
                <button type="button" class="btn btn-primary btn-add-user" id="showAddForm">
                    <i class="fa fa-plus"></i> Add New User
                </button>

                <!-- Users Table -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="usersTable">
                                <thead class="table-dark">
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
                                    $current_user_id = $_SESSION['user_id'];
                                    $query = "SELECT id, first_name, last_name, email, role, status as is_active FROM users ORDER BY id DESC";
                                    
                                    // Debug: Output the query and connection status
                                    echo "<!-- Query: " . htmlspecialchars($query) . " -->\n";
                                    echo "<!-- Connection: " . ($conn ? 'Connected' : 'Not connected') . " -->\n";
                                    
                                    try {
                                        // First, check if the users table exists
                                        $table_check = $conn->query("SHOW TABLES LIKE 'users'");
                                        if (!$table_check) {
                                            throw new Exception("Error checking for users table: " . $conn->error);
                                        }
                                        
                                        if ($table_check->num_rows == 0) {
                                            echo "<tr><td colspan='6' class='text-center text-danger'>Users table does not exist</td></tr>";
                                        } else {
                                            // Get users from database
                                            $result = $conn->query($query);
                                            
                                            if ($conn->error) {
                                                throw new Exception("Database error: " . $conn->error);
                                            }
                                            
                                            if ($result && $result->num_rows > 0) {
                                                while ($user = $result->fetch_assoc()) {
                                                    $full_name = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
                                                    $email = htmlspecialchars($user['email']);
                                                    $role = ucfirst($user['role']);
                                                    $status = $user['is_active'] ? 'Active' : 'Inactive';
                                                    $status_class = $user['is_active'] ? 'text-success' : 'text-danger';
                                                    $is_current_user = ($user['id'] == $current_user_id);
                                    ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo $full_name; ?></td>
                                        <td><?php echo $email; ?></td>
                                        <td><span class="badge bg-primary"><?php echo $role; ?></span></td>
                                        <td><span class="<?php echo $status_class; ?>"><?php echo $status; ?></span></td>
                                        <td class="action-buttons">
                                            <button class="btn btn-sm btn-outline-primary edit-user" data-id="<?php echo $user['id']; ?>" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <?php if (!$is_current_user): ?>
                                            <button class="btn btn-sm btn-outline-danger delete-user" data-id="<?php echo $user['id']; ?>" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php
                                                }
                                            } else {
                                                echo "<tr><td colspan='6' class='text-center'>No users found</td></tr>";
                                            }
                                        }
                                    } catch (Exception $e) {
                                        error_log("Error in users-new.php: " . $e->getMessage());
                                        echo "<tr><td colspan='6' class='text-center text-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
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

<!-- Add/Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm" method="post">
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="userId">
                    <input type="hidden" name="action" id="formAction" value="add">
                    
                    <div class="mb-3">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstName" name="first_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="last_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="instructor">Instructor</option>
                            <option value="student">Student</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="passwordField">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" minlength="6">
                        <div class="form-text">Leave blank to keep current password (when editing)</div>
                    </div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1" checked>
                        <label class="form-check-label" for="isActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this user? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<?php include '../new-footer.php'; ?>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    const usersTable = $('#usersTable').DataTable({
        responsive: true,
        order: [[0, 'desc']]
    });
    
    // Show add user form
    $('#showAddForm').on('click', function() {
        $('#userForm')[0].reset();
        $('#userId').val('');
        $('#formAction').val('add');
        $('#formTitle').text('Add New User');
        $('#password').prop('required', true);
        $('#userFormContainer').slideDown();
        $('html, body').animate({
            scrollTop: $('#userFormContainer').offset().top - 100
        }, 500);
    });
    
    // Cancel edit
    $('#cancelEdit').on('click', function() {
        $('#userFormContainer').slideUp();
    });

    // Show add user modal
    $('.btn-add-user').click(function() {
        $('#modalTitle').text('Add New User');
        $('#formAction').val('add');
        $('#passwordField').show();
        $('#password').attr('required', true);
        $('#userForm')[0].reset();
        $('#userModal').modal('show');
    });

    // Show edit user modal
    $(document).on('click', '.edit-user', function() {
        const userId = $(this).data('id');
        
        // Fetch user data via AJAX
        $.ajax({
            url: 'get_user.php',
            type: 'GET',
            data: { id: userId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const user = response.data;
                    $('#modalTitle').text('Edit User');
                    $('#formAction').val('edit');
                    $('#userId').val(user.id);
                    $('#firstName').val(user.first_name);
                    $('#lastName').val(user.last_name);
                    $('#email').val(user.email);
                    $('#role').val(user.role);
                    $('#isActive').prop('checked', user.is_active == 1);
                    $('#passwordField').hide();
                    $('#password').removeAttr('required');
                    $('#userModal').modal('show');
                } else {
                    toastr.error(response.message || 'Failed to load user data');
                }
            },
            error: function() {
                toastr.error('Error loading user data');
            }
        });
    });

    // Handle form submission
    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const action = $('#formAction').val();
        
        $.ajax({
            url: 'save_user.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#userModal').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(response.message || 'Operation failed');
                }
            },
            error: function() {
                toastr.error('An error occurred. Please try again.');
            }
        });
    });

    // Show delete confirmation
    let userIdToDelete = null;
    $(document).on('click', '.delete-user', function() {
        userIdToDelete = $(this).data('id');
        $('#deleteModal').modal('show');
    });

    // Handle delete confirmation
    $('#confirmDelete').click(function() {
        if (!userIdToDelete) return;
        
        $.ajax({
            url: 'delete_user.php',
            type: 'POST',
            data: { 
                id: userIdToDelete,
                action: 'delete'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#deleteModal').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(response.message || 'Failed to delete user');
                }
            },
            error: function() {
                toastr.error('Error deleting user');
            },
            complete: function() {
                userIdToDelete = null;
            }
        });
    });

    // Show toast messages from PHP
    <?php if (isset($_SESSION['toast'])): ?>
        toastr.<?php echo $_SESSION['toast']['type']; ?>('<?php echo addslashes($_SESSION['toast']['message']); ?>');
        <?php unset($_SESSION['toast']); ?>
    <?php endif; ?>
});
</script>

<!-- Create the required PHP handler files -->
<?php
// Create get_user.php
$getUserPhp = '<?php
// Start session
session_start();

// Check if user is logged in and has admin privileges
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "admin") {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit();
}

// Include database connection
require_once "../../includes/db.php";

// Get user ID from request
$userId = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$userId) {
    echo json_encode(["success" => false, "message" => "Invalid user ID"]);
    exit();
}

try {
    // Prepare and execute query
    $query = "SELECT id, first_name, last_name, email, role, is_active FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "User not found"]);
        exit();
    }
    
    $user = $result->fetch_assoc();
    echo json_encode(["success" => true, "data" => $user]);
    
} catch (Exception $e) {
    error_log("Error fetching user: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Database error"]);
}
?>
';

// Create save_user.php
$saveUserPhp = '<?php
// Start session
session_start();

// Check if user is logged in and has admin privileges
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "admin") {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit();
}

// Include database connection
require_once "../../includes/db.php";

// Initialize response array
$response = ["success" => false, "message" => ""];

// Get form data
$action = $_POST["action"] ?? "";
$userId = $_POST["user_id"] ?? 0;
$firstName = trim($_POST["first_name"] ?? "");
$lastName = trim($_POST["last_name"] ?? "");
$email = filter_var(trim($_POST["email"] ?? ""), FILTER_VALIDATE_EMAIL);
$role = in_array($_POST["role"] ?? "", ["admin", "instructor", "student"]) ? $_POST["role"] : "";
$isActive = isset($_POST["is_active"]) ? 1 : 0;
$password = $_POST["password"] ?? "";

// Validate input
if (empty($firstName) || empty($lastName) || !$email || empty($role)) {
    $response["message"] = "All fields are required";
    echo json_encode($response);
    exit();
}

try {
    // Check if email already exists (for new users or when email is changed)
    if ($action === "add" || $action === "edit") {
        $query = "SELECT id FROM users WHERE email = ? AND id != ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $email, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $response["message"] = "Email already exists";
            echo json_encode($response);
            exit();
        }
    }
    
    // Prepare data for database
    if ($action === "add") {
        // Add new user
        if (empty($password)) {
            $response["message"] = "Password is required for new users";
            echo json_encode($response);
            exit();
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (first_name, last_name, email, password, role, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $firstName, $lastName, $email, $hashedPassword, $role, $isActive);
        $actionMessage = "User added successfully";
        
    } else {
        // Update existing user
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $query = "UPDATE users SET first_name = ?, last_name = ?, email = ?, password = ?, role = ?, is_active = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssssii", $firstName, $lastName, $email, $hashedPassword, $role, $isActive, $userId);
        } else {
            $query = "UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ?, is_active = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssii", $firstName, $lastName, $email, $role, $isActive, $userId);
        }
        $actionMessage = "User updated successfully";
    }
    
    // Execute the query
    if ($stmt->execute()) {
        $response["success"] = true;
        $response["message"] = $actionMessage;
    } else {
        $response["message"] = "Error saving user";
    }
    
} catch (Exception $e) {
    error_log("Error saving user: " . $e->getMessage());
    $response["message"] = "Database error";
}

echo json_encode($response);
?>
';

// Create delete_user.php
$deleteUserPhp = '<?php
// Start session
session_start();

// Check if user is logged in and has admin privileges
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "admin") {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit();
}

// Include database connection
require_once "../../includes/db.php";

// Initialize response array
$response = ["success" => false, "message" => ""];

// Get user ID from request
$userId = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$userId) {
    $response["message"] = "Invalid user ID";
    echo json_encode($response);
    exit();
}

// Prevent deleting the current user
if ($userId == $_SESSION["user_id"]) {
    $response["message"] = "You cannot delete your own account";
    echo json_encode($response);
    exit();
}

try {
    // Prepare and execute delete query
    $query = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        $response["success"] = true;
        $response["message"] = "User deleted successfully";
    } else {
        $response["message"] = "Error deleting user";
    }
    
} catch (Exception $e) {
    error_log("Error deleting user: " . $e->getMessage());
    $response["message"] = "Database error";
}

echo json_encode($response);
?>
';

// Create the handler files
file_put_contents('get_user.php', $getUserPhp);
file_put_contents('save_user.php', $saveUserPhp);
file_put_contents('delete_user.php', $deleteUserPhp);
?>
