<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set security headers
header('Content-Type: text/html; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 0');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; " .
    "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.jsdelivr.net code.jquery.com cdn.datatables.net cdnjs.cloudflare.com; " .
    "style-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdn.datatables.net cdnjs.cloudflare.com fonts.googleapis.com; " .
    "img-src 'self' data: https:; " .
    "font-src 'self' data: fonts.gstatic.com; " .
    "connect-src 'self' https:; " .
    "frame-ancestors 'none';"
);
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Include necessary files
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/auth_functions.php';

// Verify admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.0 403 Forbidden');
    die('Access Denied');
}

// Regenerate session ID to prevent session fixation
if (!isset($_SESSION['last_regeneration']) || (time() - $_SESSION['last_regeneration'] > 1800)) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize database connection
$conn = $GLOBALS['conn'] ?? null;
if (!$conn || !($conn instanceof mysqli)) {
    die('Database connection error');
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die(json_encode(['success' => false, 'message' => 'Invalid CSRF token']));
    }

    $action = $_POST['action'] ?? '';
    $response = ['success' => false, 'message' => ''];

    try {
        switch ($action) {
            case 'add_user':
                // Validate required fields
                $required = ['first_name', 'email', 'password', 'role'];
                foreach ($required as $field) {
                    if (empty(trim($_POST[$field] ?? ''))) {
                        throw new Exception("Missing required field: $field");
                    }
                }

                // Validate email
                $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
                if (!$email) {
                    throw new Exception('Invalid email address');
                }

                // Check if email exists
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->bind_param('s', $email);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    throw new Exception('Email already exists');
                }
                $stmt->close();

                // Hash password
                $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
                
                // Insert new user
                $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role, status, created_at) VALUES (?, ?, ?, ?, ?, 1, NOW())");
                $firstName = trim($_POST['first_name']);
                $lastName = trim($_POST['last_name'] ?? '');
                $role = trim($_POST['role']);
                
                $stmt->bind_param('sssss', $firstName, $lastName, $email, $password, $role);
                
                if ($stmt->execute()) {
                    $response = ['success' => true, 'message' => 'User created successfully'];
                } else {
                    throw new Exception('Failed to create user: ' . $stmt->error);
                }
                $stmt->close();
                break;

            case 'update_user':
                // Validate required fields
                $required = ['user_id', 'first_name', 'email', 'role'];
                foreach ($required as $field) {
                    if (empty(trim($_POST[$field] ?? ''))) {
                        throw new Exception("Missing required field: $field");
                    }
                }

                // Validate email
                $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
                if (!$email) {
                    throw new Exception('Invalid email address');
                }

                // Check if email exists for another user
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->bind_param('si', $email, $_POST['user_id']);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    throw new Exception('Email already in use by another account');
                }
                $stmt->close();

                // Update user
                $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ?, status = ?, updated_at = NOW() WHERE id = ?");
                $firstName = trim($_POST['first_name']);
                $lastName = trim($_POST['last_name'] ?? '');
                $role = trim($_POST['role']);
                $status = isset($_POST['status']) ? 1 : 0;
                $userId = (int)$_POST['user_id'];
                
                $stmt->bind_param('ssssii', $firstName, $lastName, $email, $role, $status, $userId);
                
                if ($stmt->execute()) {
                    $response = ['success' => true, 'message' => 'User updated successfully'];
                } else {
                    throw new Exception('Failed to update user: ' . $stmt->error);
                }
                $stmt->close();
                break;
                
            case 'delete_user':
                $userId = (int)($_POST['user_id'] ?? 0);
                $currentUserId = $_SESSION['user_id'] ?? 0;
                
                // Prevent self-deletion
                if ($userId === $currentUserId) {
                    throw new Exception('You cannot delete your own account');
                }
                
                // Optional: Check if user has any related data before deletion
                // $stmt = $conn->prepare("SELECT COUNT(*) as count FROM some_related_table WHERE user_id = ?");
                // $stmt->bind_param('i', $userId);
                // $stmt->execute();
                // $result = $stmt->get_result()->fetch_assoc();
                // $stmt->close();
                // 
                // if ($result['count'] > 0) {
                //     throw new Exception('Cannot delete user with associated records');
                // }
                
                $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                $stmt->bind_param('i', $userId);
                
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        $response = ['success' => true, 'message' => 'User deleted successfully'];
                    } else {
                        throw new Exception('User not found or already deleted');
                    }
                } else {
                    throw new Exception('Failed to delete user: ' . $stmt->error);
                }
                $stmt->close();
                break;
                
            default:
                throw new Exception('Invalid action');
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    // Return JSON response for AJAX requests
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Get users for the table
$users = [];
$result = $conn->query("SELECT id, first_name, last_name, email, role, status, created_at, last_login FROM users ORDER BY created_at DESC");
if ($result) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
}

// Define roles for the form
$roles = [
    'admin' => 'Administrator',
    'instructor' => 'Instructor',
    'student' => 'Student',
    'staff' => 'Staff'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Panel</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- BoxIcons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <style>
        .action-btn {
            padding: 0.25rem 0.5rem;
            margin: 0 0.125rem;
            border-radius: 0.25rem;
            color: #6c757d;
            transition: all 0.2s;
        }
        .action-btn:hover {
            background-color: #f8f9fa;
            color: #0d6efd;
        }
        .badge {
            font-weight: 500;
        }
        .table-responsive {
            min-height: 400px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>User Management</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class='bx bx-plus'></i> Add New User
                    </button>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="usersTable">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): 
                                $displayName = trim(htmlspecialchars($user['first_name'] . ' ' . $user['last_name']));
                                if (empty($displayName)) $displayName = 'User';
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($user['id']) ?></td>
                                <td><?= $displayName ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <span class="badge bg-<?= 
                                        match($user['role']) {
                                            'admin' => 'primary',
                                            'instructor' => 'info',
                                            'student' => 'success',
                                            default => 'secondary'
                                        }
                                    ?>">
                                        <?= ucfirst(htmlspecialchars($user['role'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $user['status'] ? 'success' : 'danger' ?>-light">
                                        <?= $user['status'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                <td class="text-end pe-4">
                                    <div class="action-buttons">
                                        <a href="#" class="action-btn edit-user" 
                                           data-id="<?= $user['id'] ?>"
                                           data-first-name="<?= htmlspecialchars($user['first_name']) ?>"
                                           data-last-name="<?= htmlspecialchars($user['last_name']) ?>"
                                           data-email="<?= htmlspecialchars($user['email']) ?>"
                                           data-role="<?= htmlspecialchars($user['role']) ?>"
                                           data-status="<?= $user['status'] ?>"
                                           data-bs-toggle="tooltip" 
                                           title="Edit User">
                                            <i class='bx bxs-edit-alt'></i>
                                        </a>
                                        <a href="#" class="action-btn delete-user text-danger" 
                                           data-id="<?= $user['id'] ?>"
                                           data-bs-toggle="tooltip" 
                                           title="Delete User">
                                            <i class='bx bxs-trash-alt'></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
                    <input type="hidden" name="action" value="add_user">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                            <div class="invalid-feedback">Please enter first name</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name">
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback">Please enter a valid email</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" id="generatePassword">
                                    <i class='bx bx-refresh'></i>
                                </button>
                            </div>
                            <div class="form-text">Minimum 8 characters</div>
                            <div class="invalid-feedback">Please enter a password</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">Select Role</option>
                                <?php foreach ($roles as $value => $label): ?>
                                    <option value="<?= $value ?>"><?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a role</div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save User</button>
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
                <form id="editUserForm" class="needs-validation" novalidate>
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                            <div class="invalid-feedback">Please enter first name</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="edit_last_name" name="last_name">
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                            <div class="invalid-feedback">Please enter a valid email</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_role" name="role" required>
                                <option value="">Select Role</option>
                                <?php foreach ($roles as $value => $label): ?>
                                    <option value="<?= $value ?>"><?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a role</div>
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="edit_status" name="status" value="1" checked>
                            <label class="form-check-label" for="edit_status">Active</label>
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

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body"></div>
        </div>
    </div>

    <!-- Required JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
    
    <script>
    $(document).ready(function() {
        // Initialize DataTable
        const usersTable = $('#usersTable').DataTable({
            responsive: true,
            order: [[0, 'desc']],
            pageLength: 25,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search users..."
            }
        });
        
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Toast notification
        const toastEl = document.getElementById('toast');
        const toast = new bootstrap.Toast(toastEl, { autohide: true, delay: 5000 });
        
        function showToast(type, message) {
            const toastBody = $('.toast-body');
            const toastHeader = $('.toast-header');
            
            // Set toast style based on type
            toastEl.className = 'toast';
            toastEl.classList.add(`text-bg-${type}`);
            
            // Set message and show
            toastBody.text(message);
            toast.show();
        }
        
        // Generate random password
        $('#generatePassword').on('click', function() {
            const length = 12;
            const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+~`|}{[]\\:;?><,./-';
            let password = '';
            
            for (let i = 0; i < length; i++) {
                password += charset.charAt(Math.floor(Math.random() * charset.length));
            }
            
            $('#password').val(password);
        });
        
        // Form validation
        (function () {
            'use strict'
            
            const forms = document.querySelectorAll('.needs-validation')
            
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    
                    form.classList.add('was-validated')
                }, false)
            })
        })()
        
        // Handle add user form submission
        $('#addUserForm').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $submitBtn = $form.find('button[type="submit"]');
            const originalBtnText = $submitBtn.html();
            
            // Show loading state
            $submitBtn.prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Saving...
            `);
            
            // Get form data
            const formData = new FormData(this);
            
            // Hash password before submission
            const password = formData.get('password');
            if (password) {
                formData.set('password', CryptoJS.SHA256(password).toString());
            }
            
            // Submit form via AJAX
            $.ajax({
                url: '',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showToast('success', response.message || 'User created successfully');
                        $form[0].reset();
                        $form.removeClass('was-validated');
                        $('#addUserModal').modal('hide');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('danger', response.message || 'Error creating user');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    showToast('danger', 'An error occurred. Please try again.');
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).html(originalBtnText);
                }
            });
        });
        
        // Reset form when modal is closed
        $('#addUserModal, #editUserModal').on('hidden.bs.modal', function () {
            $(this).find('form')[0].reset();
            $(this).find('form').removeClass('was-validated');
        });
        
        // Handle edit user button click
        $(document).on('click', '.edit-user', function(e) {
            e.preventDefault();
            
            const userId = $(this).data('id');
            const firstName = $(this).data('first-name');
            const lastName = $(this).data('last-name');
            const email = $(this).data('email');
            const role = $(this).data('role');
            const status = $(this).data('status');
            
            // Populate the edit form
            $('#editUserForm input[name="user_id"]').val(userId);
            $('#editUserForm input[name="first_name"]').val(firstName);
            $('#editUserForm input[name="last_name"]').val(lastName);
            $('#editUserForm input[name="email"]').val(email);
            $('#editUserForm select[name="role"]').val(role);
            $('#editUserForm input[name="status"]').prop('checked', status == 1);
            
            // Show the edit modal
            $('#editUserModal').modal('show');
        });
        
        // Handle edit user form submission
        $('#editUserForm').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $submitBtn = $form.find('button[type="submit"]');
            const originalBtnText = $submitBtn.html();
            
            // Show loading state
            $submitBtn.prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Updating...
            `);
            
            // Get form data
            const formData = new FormData(this);
            formData.append('action', 'update_user');
            
            // Submit form via AJAX
            $.ajax({
                url: '',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showToast('success', response.message || 'User updated successfully');
                        $('#editUserModal').modal('hide');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast('danger', response.message || 'Error updating user');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    showToast('danger', 'An error occurred. Please try again.');
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).html(originalBtnText);
                }
            });
        });
        
        // Handle delete user button click
        $(document).on('click', '.delete-user', function(e) {
            e.preventDefault();
            
            const userId = $(this).data('id');
            const userName = $(this).closest('tr').find('td:eq(1)').text().trim();
            
            // Show confirmation dialog
            if (confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
                const $deleteBtn = $(this);
                const originalBtnHtml = $deleteBtn.html();
                
                // Show loading state
                $deleteBtn.html('<i class="bx bx-loader bx-spin"></i>');
                
                // Send delete request
                $.ajax({
                    url: '',
                    type: 'POST',
                    data: {
                        action: 'delete_user',
                        user_id: userId,
                        csrf_token: '<?= $_SESSION['csrf_token'] ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showToast('success', response.message || 'User deleted successfully');
                            // Remove the row from the table
                            $deleteBtn.closest('tr').fadeOut(400, function() {
                                usersTable.row($(this)).remove().draw(false);
                            });
                        } else {
                            showToast('danger', response.message || 'Error deleting user');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        showToast('danger', 'An error occurred. Please try again.');
                    },
                    complete: function() {
                        $deleteBtn.html(originalBtnHtml);
                    }
                });
            }
        });
    });
    </script>
</body>
</html>
