<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define root path - point to zanvarsity directory
define('ROOT_PATH', dirname(dirname(dirname(__DIR__))));

// Include necessary files
require_once ROOT_PATH . '/zanvarsity/includes/auth_functions.php';
require_once ROOT_PATH . '/zanvarsity/includes/database.php';

// Check if user is logged in
require_login();

// Get user information from session
$user_id = $_SESSION['user_id'] ?? null;
$user_email = $_SESSION['email'] ?? 'Guest';
$user_name = $_SESSION['first_name'] ?? 'User';
$user_role = $_SESSION['role'] ?? 'student';
$is_admin = in_array($user_role, ['admin', 'super_admin']);

// Verify admin access
if (!$is_admin) {
    header('HTTP/1.0 403 Forbidden');
    die('Access Denied: Admin privileges required');
}

// Initialize variables
$error = '';
$success = '';

// Handle form submissions for user management
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid CSRF token';
    } else {
        $action = $_POST['action'];
        
        switch ($action) {
            case 'create_user':
                // Validate and create new user
                $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
                $first_name = trim($_POST['first_name'] ?? '');
                $last_name = trim($_POST['last_name'] ?? '');
                $role = $_POST['role'] ?? 'student';
                $password = $_POST['password'] ?? '';
                
                if (!$email) {
                    $error = 'Please enter a valid email address';
                } elseif (strlen($password) < 8) {
                    $error = 'Password must be at least 8 characters long';
                } elseif (empty($first_name)) {
                    $error = 'First name is required';
                } else {
                    // Check if email already exists
                    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                    $stmt->bind_param('s', $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $error = 'A user with this email already exists';
                    } else {
                        // Hash password
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        
                        // Insert new user
                        $stmt = $conn->prepare("INSERT INTO users (email, first_name, last_name, password, role) VALUES (?, ?, ?, ?, ?)");
                        $stmt->bind_param('sssss', $email, $first_name, $last_name, $hashed_password, $role);
                        
                        if ($stmt->execute()) {
                            $success = 'User created successfully';
                        } else {
                            $error = 'Failed to create user: ' . $conn->error;
                        }
                    }
                }
                break;
                
            case 'update_user':
                // Update existing user
                $user_id = (int)($_POST['user_id'] ?? 0);
                $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
                $first_name = trim($_POST['first_name'] ?? '');
                $last_name = trim($_POST['last_name'] ?? '');
                $role = $_POST['role'] ?? 'student';
                
                if (!$email) {
                    $error = 'Please enter a valid email address';
                } elseif (empty($first_name)) {
                    $error = 'First name is required';
                } else {
                    // Check if email is already taken by another user
                    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                    $stmt->bind_param('si', $email, $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $error = 'A user with this email already exists';
                    } else {
                        // Update user
                        $stmt = $conn->prepare("UPDATE users SET email = ?, first_name = ?, last_name = ?, role = ? WHERE id = ?");
                        $stmt->bind_param('ssssi', $email, $first_name, $last_name, $role, $user_id);
                        
                        if ($stmt->execute()) {
                            $success = 'User updated successfully';
                            
                            // Update session if editing own profile
                            if ($user_id == $_SESSION['user_id']) {
                                $_SESSION['email'] = $email;
                                $_SESSION['first_name'] = $first_name;
                                $user_name = $first_name; // Update the displayed name
                            }
                        } else {
                            $error = 'Failed to update user: ' . $conn->error;
                        }
                    }
                }
                break;
                
            case 'delete_user':
                // Delete user
                $user_id = (int)($_POST['user_id'] ?? 0);
                
                // Prevent deleting own account
                if ($user_id === $_SESSION['user_id']) {
                    $error = 'You cannot delete your own account';
                } else {
                    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                    $stmt->bind_param('i', $user_id);
                    
                    if ($stmt->execute()) {
                        $success = 'User deleted successfully';
                    } else {
                        $error = 'Failed to delete user: ' . $conn->error;
                    }
                }
                break;
                
            case 'reset_password':
                // Reset user password
                $user_id = (int)($_POST['user_id'] ?? 0);
                $new_password = $_POST['new_password'] ?? '';
                
                if (strlen($new_password) < 8) {
                    $error = 'Password must be at least 8 characters long';
                } else {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->bind_param('si', $hashed_password, $user_id);
                    
                    if ($stmt->execute()) {
                        $success = 'Password reset successfully';
                    } else {
                        $error = 'Failed to reset password: ' . $conn->error;
                    }
                }
                break;
        }
    }
    
    // Return JSON response for AJAX requests
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => empty($error),
            'message' => empty($error) ? $success : $error
        ]);
        exit();
    }
}

// Get all users for the table
$users = [];
$stmt = $conn->prepare("SELECT id, email, first_name, last_name, role, created_at FROM users ORDER BY created_at DESC");
if ($stmt->execute()) {
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
}

// Set page title
$page_title = 'User Management';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Zanvarsity</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="/c/zanvarsity/assets/img/favicon.ico">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/c/zanvarsity/assets/css/style.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    
    <style>
        .profile-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .sidebar {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-menu li {
            margin-bottom: 10px;
        }
        .sidebar-menu a {
            display: block;
            padding: 10px 15px;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: #f8f9fa;
            color: #0d6efd;
        }
        .sidebar-menu a i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 15px 20px;
        }
        .card-body {
            padding: 20px;
        }
        .btn-action {
            padding: 5px 10px;
            margin: 0 2px;
            border-radius: 4px;
        }
        .btn-edit {
            color: #0d6efd;
            background: rgba(13, 110, 253, 0.1);
        }
        .btn-delete {
            color: #dc3545;
            background: rgba(220, 53, 69, 0.1);
        }
        .btn-reset {
            color: #ffc107;
            background: rgba(255, 193, 7, 0.1);
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include '../includes/about_header.php'; ?>
    
    <div class="dashboard-container">
        <div class="container">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-lg-3">
                    <div class="sidebar">
                        <div class="profile-card text-center mb-4">
                            <div class="mb-3">
                                <img src="/c/zanvarsity/<?php echo !empty($_SESSION['profile_image']) ? htmlspecialchars($_SESSION['profile_image']) : 'assets/img/avatar-placeholder.png'; ?>" 
                                     alt="Profile Image" 
                                     class="profile-image img-fluid">
                            </div>
                            <h5 class="mb-1"><?php echo htmlspecialchars($user_name); ?></h5>
                            <p class="text-muted mb-3"><?php echo ucfirst(htmlspecialchars($user_role)); ?></p>
                        </div>
                        
                        <ul class="sidebar-menu">
                            <li><a href="/c/zanvarsity/html/my-account.php"><i class="fa fa-home"></i> Back to Dashboard</a></li>
                            <li><a href="#" class="active"><i class="fa fa-users-cog"></i> Manage Users</a></li>
                            <li><a href="#"><i class="fa fa-cog"></i> Settings</a></li>
                            <li><a href="/c/zanvarsity/logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Main Content -->
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0"><i class="fa fa-users me-2"></i>User Management</h4>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="fa fa-plus me-1"></i> Add New User
                            </button>
                        </div>
                        <div class="card-body">
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            
                            <?php if ($success): ?>
                                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                            <?php endif; ?>
                            
                            <div class="table-responsive">
                                <table class="table table-hover" id="usersTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Joined</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?php echo (int)$user['id']; ?></td>
                                                <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo $user['role'] === 'admin' ? 'primary' : 
                                                            ($user['role'] === 'super_admin' ? 'danger' : 'secondary'); 
                                                    ?>">
                                                        <?php echo ucfirst(htmlspecialchars($user['role'])); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-action btn-edit edit-user" 
                                                            data-id="<?php echo (int)$user['id']; ?>"
                                                            data-bs-toggle="tooltip" 
                                                            title="Edit User">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    
                                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                        <button class="btn btn-sm btn-action btn-reset reset-password" 
                                                                data-id="<?php echo (int)$user['id']; ?>"
                                                                data-bs-toggle="tooltip" 
                                                                title="Reset Password">
                                                            <i class="fa fa-key"></i>
                                                        </button>
                                                        
                                                        <button class="btn btn-sm btn-action btn-delete delete-user" 
                                                                data-id="<?php echo (int)$user['id']; ?>"
                                                                data-bs-toggle="tooltip" 
                                                                title="Delete User">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addUserForm" method="post" class="needs-validation" novalidate>
                    <input type="hidden" name="action" value="create_user">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                            <div class="invalid-feedback">Please enter first name.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name">
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" minlength="8" required>
                            <div class="invalid-feedback">Password must be at least 8 characters long.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="student">Student</option>
                                <option value="lecturer">Lecturer</option>
                                <option value="admin">Administrator</option>
                                <?php if ($user_role === 'super_admin'): ?>
                                    <option value="super_admin">Super Administrator</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editUserForm" method="post" class="needs-validation" novalidate>
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                            <div class="invalid-feedback">Please enter first name.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="edit_last_name" name="last_name">
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_role" name="role" required>
                                <option value="student">Student</option>
                                <option value="lecturer">Lecturer</option>
                                <option value="admin">Administrator</option>
                                <?php if ($user_role === 'super_admin'): ?>
                                    <option value="super_admin">Super Administrator</option>
                                <?php endif; ?>
                            </select>
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

    <!-- Reset Password Modal -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="resetPasswordForm" method="post" class="needs-validation" novalidate>
                    <input type="hidden" name="action" value="reset_password">
                    <input type="hidden" name="user_id" id="reset_user_id">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle me-2"></i> A new password will be generated and displayed after reset.
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="new_password" name="new_password" readonly>
                                <button class="btn btn-outline-secondary" type="button" id="generatePassword">
                                    <i class="fa fa-sync-alt"></i> Generate
                                </button>
                            </div>
                            <div class="form-text">Password must be at least 8 characters long.</div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fa fa-exclamation-triangle me-2"></i>Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="deleteUserForm" method="post">
                    <input type="hidden" name="action" value="delete_user">
                    <input type="hidden" name="user_id" id="delete_user_id">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div class="modal-body">
                        <p>Are you sure you want to delete this user? This action cannot be undone.</p>
                        <p class="mb-0"><strong>Note:</strong> This will permanently remove all data associated with this user.</p>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include '../includes/about_footer.php'; ?>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#usersTable').DataTable({
                responsive: true,
                order: [[0, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [5] } // Disable sorting on actions column
                ]
            });
            
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Handle edit user button click
            $(document).on('click', '.edit-user', function() {
                var userId = $(this).data('id');
                
                // Find the user data
                var user = <?php echo json_encode($users); ?>.find(u => u.id == userId);
                
                if (user) {
                    // Populate the form
                    $('#edit_user_id').val(user.id);
                    $('#edit_first_name').val(user.first_name || '');
                    $('#edit_last_name').val(user.last_name || '');
                    $('#edit_email').val(user.email || '');
                    $('#edit_role').val(user.role || 'student');
                    
                    // Show the modal
                    var editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
                    editModal.show();
                }
            });
            
            // Handle reset password button click
            $(document).on('click', '.reset-password', function() {
                var userId = $(this).data('id');
                $('#reset_user_id').val(userId);
                
                // Generate a random password
                generatePassword();
                
                // Show the modal
                var resetModal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
                resetModal.show();
            });
            
            // Handle delete user button click
            $(document).on('click', '.delete-user', function(e) {
                e.preventDefault();
                var userId = $(this).data('id');
                $('#delete_user_id').val(userId);
                
                // Show the confirmation modal
                var deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
                deleteModal.show();
            });
            
            // Generate password button
            $('#generatePassword').on('click', function(e) {
                e.preventDefault();
                generatePassword();
            });
            
            // Form validation
            (function () {
                'use strict';
                
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.querySelectorAll('.needs-validation');
                
                // Loop over them and prevent submission
                Array.prototype.slice.call(forms).forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        
                        form.classList.add('was-validated');
                    }, false);
                });
            })();
            
            // Function to generate a random password
            function generatePassword() {
                var length = 12;
                var charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+~`|}{[]\:;?><,./-=';
                var password = '';
                
                // Ensure at least one of each character type
                var lowercase = 'abcdefghijklmnopqrstuvwxyz';
                var uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                var numbers = '0123456789';
                var symbols = '!@#$%^&*()_+~`|}{[]\:;?><,./-=';
                
                // Add one of each required character type
                password += lowercase.charAt(Math.floor(Math.random() * lowercase.length));
                password += uppercase.charAt(Math.floor(Math.random() * uppercase.length));
                password += numbers.charAt(Math.floor(Math.random() * numbers.length));
                password += symbols.charAt(Math.floor(Math.random() * symbols.length));
                
                // Fill the rest of the password with random characters
                for (var i = 4; i < length; i++) {
                    password += charset.charAt(Math.floor(Math.random() * charset.length));
                }
                
                // Shuffle the password to make it more random
                password = password.split('').sort(function() { return 0.5 - Math.random() }).join('');
                
                // Set the password field value
                $('#new_password').val(password);
                
                return password;
            }
            
            // Initialize with a generated password
            generatePassword();
            
            // Handle form submissions with AJAX
            $('#addUserForm, #editUserForm, #resetPasswordForm, #deleteUserForm').on('submit', function(e) {
                e.preventDefault();
                
                var $form = $(this);
                var $submitBtn = $form.find('button[type="submit"]');
                var $modal = $form.closest('.modal');
                
                // Disable submit button and show loading state
                $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
                
                // Submit form via AJAX
                $.ajax({
                    url: window.location.href,
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            showAlert('success', response.message);
                            
                            // If not a delete action, reset the form
                            if ($form.attr('id') !== 'deleteUserForm') {
                                $form[0].reset();
                                $form.removeClass('was-validated');
                            }
                            
                            // Close the modal
                            if ($modal.length) {
                                var bsModal = bootstrap.Modal.getInstance($modal[0]);
                                bsModal.hide();
                            }
                            
                            // Reload the page to show updated data
                            setTimeout(function() {
                                window.location.reload();
                            }, 1500);
                        } else {
                            // Show error message
                            showAlert('danger', response.message || 'An error occurred. Please try again.');
                        }
                    },
                    error: function() {
                        showAlert('danger', 'An error occurred. Please try again.');
                    },
                    complete: function() {
                        // Re-enable submit button
                        $submitBtn.prop('disabled', false);
                        
                        // Reset button text
                        if ($form.attr('id') === 'deleteUserForm') {
                            $submitBtn.text('Delete User');
                        } else if ($form.attr('id') === 'resetPasswordForm') {
                            $submitBtn.text('Reset Password');
                        } else if ($form.attr('id') === 'addUserForm') {
                            $submitBtn.text('Create User');
                        } else {
                            $submitBtn.text('Save Changes');
                        }
                    }
                });
            });
            
            // Show alert function
            function showAlert(type, message) {
                var $alert = $('<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                    message +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                    '</div>');
                
                // Add alert to the page
                $('.container:first').prepend($alert);
                
                // Auto-dismiss after 5 seconds
                setTimeout(function() {
                    $alert.alert('close');
                }, 5000);
            }
        });
    </script>
</body>
</html>
