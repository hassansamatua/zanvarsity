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
header("Content-Security-Policy: " . 
    "default-src 'self'; " .
    "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.jsdelivr.net code.jquery.com cdn.datatables.net cdnjs.cloudflare.com; " .
    "style-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdn.datatables.net cdnjs.cloudflare.com fonts.googleapis.com unpkg.com; " .
    "img-src 'self' data: https:; " .
    "font-src 'self' data: fonts.gstatic.com unpkg.com; " .
    "connect-src 'self' https:; " .
    "frame-ancestors 'none';");
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Include necessary files using relative path from current file
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/auth_functions.php';

// Verify admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.0 403 Forbidden');
    die('Access Denied');
}

// Regenerate session ID to prevent session fixation
if (!isset($_SESSION['last_regeneration'])) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));

// Initialize response array
$response = ['success' => false, 'message' => ''];

// Get database connection
$conn = $GLOBALS['conn'] ?? null;

// Get user's first name from database
if (isset($_SESSION['user_id']) && $conn) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT first_name, role FROM users WHERE id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($user_data = $result->fetch_assoc()) {
                $first_name = $user_data['first_name'] ?? '';
                $user_role = $user_data['role'] ?? $user_role;
            }
        }
        $stmt->close();
    }
}

$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid CSRF token';
    } else {
        // Handle different actions
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'create_user':
                // Validate and create new user
                $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
                $role = $_POST['role'] ?? 'student';
                $password = $_POST['password'] ?? '';
                
                if (!$email) {
                    $error = 'Please enter a valid email address';
                } elseif (strlen($password) < 8) {
                    $error = 'Password must be at least 8 characters long';
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
                        $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
                        $stmt->bind_param('sss', $email, $hashed_password, $role);
                        
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
                $role = $_POST['role'] ?? 'student';
                
                if (!$email) {
                    $error = 'Please enter a valid email address';
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
                        $stmt = $conn->prepare("UPDATE users SET email = ?, role = ? WHERE id = ?");
                        $stmt->bind_param('ssi', $email, $role, $user_id);
                        
                        if ($stmt->execute()) {
                            $success = 'User updated successfully';
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
            'message' => $success ?: $error,
            'redirect' => $success ? '' : null
        ]);
        exit();
    }
    
    // For non-AJAX requests, redirect to prevent form resubmission
    if ($success) {
        $_SESSION['success'] = $success;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Pagination
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$offset = ($page - 1) * $per_page;

// Get total number of users
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];

// Get user information
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['role'] ?? 'student';
$user_email = $_SESSION['email'] ?? '';
$user_name = !empty($_SESSION['name']) ? $_SESSION['name'] : (explode('@', $user_email)[0] ?? 'User');
$is_admin = in_array($user_role, ['admin', 'super_admin']);

if (!$is_admin) {
    header("Location: /zanvarsity/html/403.php");
    exit();
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        // CSRF protection
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Invalid CSRF token');
        }
        switch ($_POST['action']) {
            case 'add_user':
                // Validate input
                $required = ['first_name', 'email', 'password', 'role'];
                foreach ($required as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception("$field is required");
                    }
                }
                
                // Sanitize input
                $first_name = trim($conn->real_escape_string($_POST['first_name']));
                $last_name = trim($conn->real_escape_string($_POST['last_name'] ?? ''));
                $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
                $role = in_array($_POST['role'], ['admin', 'instructor', 'student', 'staff', 'parent']) ? $_POST['role'] : 'student';
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                if (!$email) {
                    throw new Exception("Invalid email address");
                }
                
                if (strlen($_POST['password']) < 8) {
                    throw new Exception("Password must be at least 8 characters long");
                }
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                
                // Check if email already exists
                $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $check->bind_param("s", $email);
                $check->execute();
                if ($check->get_result()->num_rows > 0) {
                    throw new Exception("Email already exists");
                }
                
                // Insert new user
                $query = "INSERT INTO users (first_name, last_name, email, role, password, is_active, created_at, updated_at) 
                          VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssssi", $first_name, $last_name, $email, $role, $password, $is_active);
                
                if ($stmt->execute()) {
                    $response = ['success' => true, 'message' => 'User added successfully'];
                } else {
                    throw new Exception("Error adding user: " . $stmt->error);
                }
                break;
                
            case 'update_user':
                // Validate input
                if (empty($_POST['user_id'])) {
                    throw new Exception("User ID is required");
                }
                
                $user_id = (int)$_POST['user_id'];
                $first_name = trim($conn->real_escape_string($_POST['first_name']));
                $last_name = trim($conn->real_escape_string($_POST['last_name'] ?? ''));
                $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
                $role = in_array($_POST['role'], ['admin', 'instructor', 'student', 'staff', 'parent']) ? $_POST['role'] : 'student';
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                if (!$email) {
                    throw new Exception("Invalid email address");
                }
                
                // Check if email is being used by another user
                $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $check->bind_param("si", $email, $user_id);
                $check->execute();
                if ($check->get_result()->num_rows > 0) {
                    throw new Exception("Email already in use by another account");
                }
                
                // Update user
                if (!empty($_POST['password'])) {
                    if (strlen($_POST['password']) < 8) {
                        throw new Exception("Password must be at least 8 characters long");
                    }
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $query = "UPDATE users SET 
                                first_name = ?, 
                                last_name = ?, 
                                email = ?, 
                                role = ?, 
                                password = ?, 
                                is_active = ?,
                                updated_at = NOW()
                              WHERE id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("sssssii", $first_name, $last_name, $email, $role, $password, $is_active, $user_id);
                } else {
                    $query = "UPDATE users SET 
                                first_name = ?, 
                                last_name = ?, 
                                email = ?, 
                                role = ?, 
                                is_active = ?,
                                updated_at = NOW()
                              WHERE id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ssssii", $first_name, $last_name, $email, $role, $is_active, $user_id);
                }
                
                if ($stmt->execute()) {
                    $response = ['success' => true, 'message' => 'User updated successfully'];
                } else {
                    throw new Exception("Error updating user: " . $stmt->error);
                    $_SESSION['error'] = "Error updating user: " . $conn->error;
                }
                break;
                
            case 'delete_user':
                // Delete user
                $user_id = (int)$_POST['user_id'];
                
                // Don't allow deleting own account
                if ($user_id == $_SESSION['user_id']) {
                    throw new Exception("You cannot delete your own account!");
                }
                
                $sql = "DELETE FROM users WHERE id = $user_id";
                
                if ($conn->query($sql) === TRUE) {
                    // Log the action
                    $log_sql = "INSERT INTO user_logs (user_id, action, table_name, record_id) 
                               VALUES ({$_SESSION['user_id']}, 'delete', 'users', $user_id)";
                    $conn->query($log_sql);
                    
                    $_SESSION['success'] = "User deleted successfully!";
                } else {
                    $_SESSION['error'] = "Error deleting user: " . $conn->error;
                }
                break;
                
            case 'reset_password':
                // Reset user password
                $user_id = (int)$_POST['user_id'];
                $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                
                $sql = "UPDATE users SET password = '$new_password' WHERE id = $user_id";
                
                if ($conn->query($sql) === TRUE) {
                    // Log the action
                    $log_sql = "INSERT INTO user_logs (user_id, action, table_name, record_id, ip_address, user_agent) 
                               VALUES ({$_SESSION['user_id']}, 'password_reset', 'users', $user_id, '{$_SERVER['REMOTE_ADDR']}', '{$_SERVER['HTTP_USER_AGENT']}')";
                    $conn->query($log_sql);
                    
                    $_SESSION['success'] = "Password reset successfully!";
                } else {
                    $_SESSION['error'] = "Error resetting password: " . $conn->error;
                }
                break;
        }
        
        // Set success response
        $response = ['success' => true, 'message' => 'Operation completed successfully'];
    } catch (Exception $e) {
        // Set error response
        $response['message'] = $e->getMessage();
    }
    
    // Return JSON response for AJAX requests
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // For regular form submissions
    $_SESSION['flash_message'] = $response['message'];
    $_SESSION['flash_success'] = $response['success'];
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid CSRF token';
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'create_user':
                // Validate and create new user
                $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
                $role = $_POST['role'] ?? 'student';
                $password = $_POST['password'] ?? '';
                
                if (!$email) {
                    $error = 'Please enter a valid email address';
                } elseif (strlen($password) < 8) {
                    $error = 'Password must be at least 8 characters long';
                } else {
                    // Check if email exists
                    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                    $stmt->bind_param('s', $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $error = 'A user with this email already exists';
                    } else {
                        // Hash password and create user
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
                        $stmt->bind_param('sss', $email, $hashed_password, $role);
                        
                        if ($stmt->execute()) {
                            $success = 'User created successfully';
                        } else {
                            $error = 'Failed to create user: ' . $conn->error;
                        }
                    }
                }
                break;
                
            case 'update_user':
                // Update user
                $user_id = (int)($_POST['user_id'] ?? 0);
                $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
                $role = $_POST['role'] ?? 'student';
                
                if (!$email) {
                    $error = 'Please enter a valid email address';
                } else {
                    // Check if email is taken by another user
                    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                    $stmt->bind_param('si', $email, $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $error = 'A user with this email already exists';
                    } else {
                        $stmt = $conn->prepare("UPDATE users SET email = ?, role = ? WHERE id = ?");
                        $stmt->bind_param('ssi', $email, $role, $user_id);
                        
                        if ($stmt->execute()) {
                            $success = 'User updated successfully';
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
        }
    }
    
    // Handle AJAX requests
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => empty($error),
            'message' => $success ?: $error
        ]);
        exit();
    }
    
    // For non-AJAX requests, redirect to prevent form resubmission
    if ($success) {
        $_SESSION['success'] = $success;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Get all users with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;

// Ensure we have default values for user data
function getUserData($user) {
    // Extract first part of email as the display name
    $email = $user['email'] ?? '';
    $name = $email ? explode('@', $email)[0] : 'User';
    
    return [
        'id' => $user['id'] ?? 0,
        'email' => $email,
        'name' => $name,
        'role' => $user['role'] ?? 'student',
        'created_at' => $user['created_at'] ?? date('Y-m-d H:i:s'),
        'updated_at' => $user['updated_at'] ?? date('Y-m-d H:i:s')
    ];
}
$offset = ($page - 1) * $per_page;

// Get total number of users
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_pages = ceil($total_users / $per_page);

// Get users for current page with error checking
$query = "SELECT id, email, first_name, last_name, role, created_at, updated_at, status FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);

// Check if prepare was successful
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

// Check if the status column exists, if not, add it
$checkColumn = $conn->query("SHOW COLUMNS FROM `users` LIKE 'status'");
if ($checkColumn->num_rows == 0) {
    // Add the status column if it doesn't exist
    $conn->query("ALTER TABLE `users` ADD COLUMN `status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Inactive' AFTER `role`");
}

$stmt->bind_param("ii", $per_page, $offset);

if (!$stmt->execute()) {
    die('Execute failed: ' . htmlspecialchars($stmt->error));
}

$users = $stmt->get_result();

// Available roles
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
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
    <meta name="author" content="Theme Starz">
    <!-- BoxIcons CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet' crossorigin='anonymous'>
    
    <!-- Google Fonts -->
    <link href='https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap' rel='stylesheet' crossorigin='anonymous'>
    
    <link href="/zanvarsity/html/assets/css/font-awesome.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/bootstrap/css/bootstrap.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/selectize.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/owl.carousel.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/vanillabox/vanillabox.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/style.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/green-theme.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/admin-theme.css" type="text/css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <style>
        /* Action buttons styling */
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            padding-right: 10px;
        }
        .action-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .edit-btn {
            color: #28a745 !important;
            background-color: rgba(40, 167, 69, 0.1);
        }
        .action-btn:hover {
            transform: scale(1.1);
        }
        .edit-btn:hover {
            background-color: rgba(40, 167, 69, 0.2);
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    
    <title>User Management - Admin Panel</title>
    
    <style>
        /* Custom styles that override theme */
        .table-responsive {
            margin-top: 20px;
        }
        .toast-success { background-color: #51a351; }
        .toast-error { background-color: #bd362f; }
        .toast-info { background-color: #2f96b4; }
        .toast-warning { background-color: #f89406; }
    </style>
</head>

<body class="page-sub-page page-my-account">
<!-- Wrapper -->
<div class="wrapper">
    <!-- Header -->
    <div class="navigation-wrapper">
        <div class="secondary-navigation-wrapper">
            <div class="container">
                <div class="navigation-contact pull-left">
                    <i class="fa fa-phone"></i> Call Us: <span class="opacity-70">+255 123 456 789</span>
                </div>
                <ul class="secondary-navigation list-unstyled pull-right">
                    <li><a href="/zanvarsity/html/my-account.php"><i class="fa fa-user"></i> My Profile</a></li>
                    <li><a href="/zanvarsity/html/admin/dashboard.php">Dashboard</a></li>
                    <li><a href="/zanvarsity/logout.php">Log Out</a></li>
                </ul>
            </div>
        </div>
        <div class="primary-navigation-wrapper">
            <header class="navbar" id="top" role="banner">
                <div class="container">
                    <div class="navbar-header">
                        <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <div class="navbar-brand nav" id="brand">
                            <a href="/zanvarsity/html/index.php">
                                <img src="/zanvarsity/html/assets/img/logo.png" alt="Zanvarsity" class="logo">
                            </a>
                        </div>
                    </div>
                    <nav class="collapse navbar-collapse bs-navbar-collapse navbar-right" role="navigation">
                        <ul class="nav navbar-nav">
                            <li><a href="/zanvarsity/html/index.php">Home</a></li>
                            <li class="has-child">
                                <a href="#">Academics</a>
                                <ul class="list-unstyled child-navigation">
                                    <li><a href="/zanvarsity/html/academics.php">Programs</a></li>
                                    <li><a href="/zanvarsity/html/faculties.php">Faculties</a></li>
                                    <li><a href="/zanvarsity/html/departments.php">Departments</a></li>
                                    <li><a href="/zanvarsity/html/courses.php">Courses</a></li>
                                </ul>
                            </li>
                            <li><a href="/zanvarsity/html/admissions.php">Admissions</a></li>
                            <li><a href="/zanvarsity/html/campus-life.php">Campus Life</a></li>
                            <li><a href="/zanvarsity/html/research.php">Research</a></li>
                            <li><a href="/zanvarsity/html/about.php">About</a></li>
                            <li><a href="/zanvarsity/html/contact.php">Contact</a></li>
                        </ul>
                    </nav>
                </div>
            </header>
        </div>
    </div>
    <!-- end Header -->

    <!-- Page Content -->
    <div id="page-content">
        <div class="container">
            <div class="row">
                <!-- Breadcrumb -->
                <div class="container">
                    <ol class="breadcrumb">
                        <li><a href="/zanvarsity/html/index.php">Home</a></li>
                        <li class="active">Manage Users</li>
                    </ol>
                </div>
                <!-- End Breadcrumb -->

                <!-- Sidebar -->
                <aside class="col-md-3 col-sm-4">
                    <div class="sidebar">
                        <div class="sidebar-inner">
                            <div class="sidebar-widget">
                                <div class="user-avatar">
                                    <div style="width: 100px; height: 100px; margin: 0 auto 15px; background-color: #4caf50; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; font-weight: bold; text-transform: uppercase; text-align: center; padding: 5px; line-height: 1.2;">
                                        <?php echo !empty($user_role) ? ucfirst($user_role) : 'Admin'; ?>
                                    </div>
                                    <div class="text-center">
                                        <h4><?php echo !empty($first_name) ? htmlspecialchars($first_name) : 'hassan'; ?></h4>
                                        <span class="label label-primary"><?php echo ucfirst($user_role); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="sidebar-widget">
                                <ul class="nav nav-pills nav-stacked nav-dashboard">
                                    <li><a href="/zanvarsity/html/my-account.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                                    <?php if (in_array($user_role, ['super_admin', 'admin'])): ?>
                                    <li class="active"><a href="users.php"><i class="fa fa-users"></i> Manage Users</a></li>
                                    <li><a href="manage_content.php"><i class="fa fa-file-text"></i> Manage Contents</a></li>
                                    <?php endif; ?>
                                    <li><a href="/zanvarsity/html/my-courses.php"><i class="fa fa-book"></i> My Courses</a></li>
                                    <li><a href="/zanvarsity/html/my-profile.php"><i class="fa fa-user"></i> My Profile</a></li>
                                    <li><a href="/zanvarsity/html/settings.php"><i class="fa fa-cog"></i> Settings</a></li>
                                    <?php if (in_array($user_role, ['instructor', 'admin', 'super_admin'])): ?>
                                    <li><a href="/zanvarsity/html/instructor"><i class="fa fa-chalkboard-teacher"></i> Instructor Panel</a></li>
                                    <?php endif; ?>
                                    <li><a href="/zanvarsity/html/logout.php" onclick="return confirm('Are you sure you want to log out?')"><i class="fa fa-sign-out"></i> Logout</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </aside>
                <!-- end Sidebar -->

                <!-- Main Content -->
                <div class="col-md-9 col-sm-8">
                    <section class="block">
                        <div class="page-title">
                            <h2><i class='bx bx-user me-2'></i>Manage Users</h2>
                            <div class="pull-right">
                       <!-- Action Buttons -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>User Management</h4>
    <div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fa fa-plus"></i> Add New User
        </button>
    </div>
</div>                            </button>
                            </div>
                        </div>
                        
                        <!-- Users Table -->
                        <div class="card shadow-sm">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle" id="usersTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-4">ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th>Joined Date</th>
                                                <th class="text-end pe-4">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            // Reset the result set pointer to the beginning
                                            $users->data_seek(0);
                                            while ($user = $users->fetch_assoc()): 
                                                // Get first and last name from database
                                                $firstName = !empty($user['first_name']) ? htmlspecialchars($user['first_name']) : '';
                                                $lastName = !empty($user['last_name']) ? htmlspecialchars($user['last_name']) : '';
                                                
                                                // Generate display name
                                                $displayName = trim("$firstName $lastName");
                                                if (empty($displayName)) {
                                                    $displayName = 'User';
                                                }
                                            ?>
                                            <tr>
                                                <td class="ps-4">
                                                    <?php echo htmlspecialchars($user['id']); ?>
                                                </td>
                                                <td>
                                                    <?php echo $displayName; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span><?php echo htmlspecialchars($user['email']); ?></span>
                                                        <small class="text-muted">Last login: <?php echo !empty($user['last_login']) ? date('M d, Y h:i A', strtotime($user['last_login'])) : 'Never'; ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-label-<?php 
                                                        echo match($user['role'] ?? 'user') {
                                                            'admin' => 'primary',
                                                            'instructor' => 'info',
                                                            'student' => 'success',
                                                            default => 'secondary'
                                                        };
                                                    ?> me-1">
                                                        <?php echo ucfirst(htmlspecialchars($user['role'] ?? 'user')); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo ($user['status'] ?? 1) ? 'success' : 'danger'; ?>-light">
                                                        <?php echo ($user['status'] ?? 1) ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span><?php echo date('M d, Y', strtotime($user['created_at'])); ?></span>
                                                        <small class="text-muted"><?php echo time_elapsed_string($user['created_at']); ?></small>
                                                    </div>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <div class="action-buttons">
                                                        <a href="#" class="action-btn edit-btn edit-user" 
                                                           data-id="<?php echo $user['id']; ?>"
                                                           data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                                           data-role="<?php echo htmlspecialchars($user['role']); ?>"
                                                           data-first-name="<?php echo !empty($user['first_name']) ? htmlspecialchars($user['first_name']) : ''; ?>"
                                                           data-last-name="<?php echo !empty($user['last_name']) ? htmlspecialchars($user['last_name']) : ''; ?>"
                                                           data-status="<?php echo !empty($user['status']) ? htmlspecialchars($user['status']) : ''; ?>"
                                                           data-bs-toggle="tooltip" 
                                                           title="Edit User">
                                                            <i class='bx bxs-edit-alt'></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                        <div class="pagination-wrapper mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo ($page - 1); ?>">
                                            <i class="bx bx-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php 
                                // Show first page
                                if ($page > 3): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=1">1</a>
                                    </li>
                                    <?php if ($page > 4): ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php 
                                // Show page numbers around current page
                                $start = max(1, $page - 2);
                                $end = min($total_pages, $page + 2);
                                
                                for ($i = $start; $i <= $end; $i++): ?>
                                    <li class="page-item <?php echo ($i === $page) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages - 2): ?>
                                    <?php if ($page < $total_pages - 3): ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $total_pages; ?>">
                                            <?php echo $total_pages; ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo ($page + 1); ?>">
                                            <i class="bx bx-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>
            </main>
        </div>
    </div>

    <?php 
// Helper function to format time elapsed
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class='bx bx-edit me-2'></i>Edit User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" method="post" class="needs-validation" novalidate>
                <input type="hidden" name="action" value="update_user">
                <input type="hidden" name="user_id" id="edit_user_id">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class='bx bx-user'></i></span>
                                    <input type="text" class="form-control" id="edit_first_name" name="first_name" required autocomplete="given-name" aria-required="true" aria-describedby="firstNameHelp">
                                </div>
                                <div id="firstNameHelp" class="form-text">Enter the user's first name</div>
                                <div class="invalid-feedback">
                                    Please enter first name
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_last_name" class="form-label">Last Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class='bx bx-user'></i></span>
                                    <input type="text" class="form-control" id="edit_last_name" name="last_name" autocomplete="family-name" aria-describedby="lastNameHelp">
                                </div>
                                <div id="lastNameHelp" class="form-text">Enter the user's last name</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class='bx bx-envelope'></i></span>
                                    <input type="email" class="form-control" id="edit_email" name="email" required autocomplete="email" aria-required="true" aria-describedby="emailHelp">
                                </div>
                                <div id="emailHelp" class="form-text">Enter a valid email address</div>
                                <div class="invalid-feedback">
                                    Please enter a valid email address
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="edit_role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_role" name="role" required aria-required="true" aria-describedby="roleHelp">
                                    <option value="">Select Role</option>
                                    <?php foreach ($roles as $value => $label): ?>
                                        <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div id="roleHelp" class="form-text">Select the user's role</div>
                                <div class="invalid-feedback">
                                    Please select a role
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_status" name="status" required aria-required="true" aria-describedby="statusHelp">
                                    <option value="">Select Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <div id="statusHelp" class="form-text">Select the user's status</div>
                                <div class="invalid-feedback">
                                    Please select a status
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Cancel and close">
                        <i class='bx bx-x me-1'></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" aria-label="Save changes">
                        <i class='bx bx-save me-1'></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class='bx bx-user-plus me-2'></i>Add New User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addUserForm" method="post" class="needs-validation" novalidate>
                <input type="hidden" name="action" value="add_user">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="modal-body">
                    <div id="addUserAlert" class="alert d-none" role="alert"></div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_first_name" name="first_name" required autocomplete="given-name" aria-required="true" aria-describedby="firstNameHelp">
                                <div id="firstNameHelp" class="form-text">Enter the user's first name</div>
                                <div class="invalid-feedback">Please enter first name</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="add_last_name" name="last_name" autocomplete="family-name" aria-describedby="lastNameHelp">
                                <div id="lastNameHelp" class="form-text">Enter the user's last name</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="add_email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="add_email" name="email" required autocomplete="email" aria-required="true" aria-describedby="emailHelp">
                                <div id="emailHelp" class="form-text">Enter a valid email address</div>
                                <div class="invalid-feedback">Please enter a valid email address</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_password" class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="add_password" name="password" required minlength="8" autocomplete="new-password" aria-required="true" aria-describedby="passwordHelp">
                                    <button type="button" class="btn btn-outline-secondary" id="generatePassword" aria-label="Generate random password" title="Generate random password">
                                        <i class='bx bx-refresh' aria-hidden="true"></i> Generate
                                    </button>
                                </div>
                                <div id="passwordHelp" class="form-text">Minimum 8 characters</div>
                                <div class="invalid-feedback">Password must be at least 8 characters long</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="add_confirm_password" required autocomplete="new-password" aria-required="true" aria-describedby="confirmPasswordHelp">
                                <div id="confirmPasswordHelp" class="form-text">Re-enter the password</div>
                                <div class="invalid-feedback">Passwords do not match</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="add_role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select" id="add_role" name="role" required aria-required="true" aria-describedby="roleHelp">
                                    <option value="">Select Role</option>
                                    <option value="admin">Administrator</option>
                                    <option value="instructor">Instructor</option>
                                    <option value="student" selected>Student</option>
                                    <option value="staff">Staff</option>
                                    <option value="parent">Parent</option>
                                </select>
                                <div id="roleHelp" class="form-text">Select the user's role</div>
                                <div class="invalid-feedback">Please select a role</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Cancel and close">
                        <i class='bx bx-x me-1'></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" aria-label="Save user">
                        <i class='bx bx-save me-1'></i> Save User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Debounce function to prevent multiple rapid submissions
function debounce(func, wait) {
    let timeout;
    return function() {
        const context = this;
        const args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}

// Form submission handler for adding a new user
$(document).on('submit', '#addUserForm', debounce(function(e) {
    e.preventDefault();
    
    const $form = $(this);
    const $submitBtn = $form.find('button[type="submit"]');
    const originalBtnText = $submitBtn.html();
    
    // Disable the submit button immediately
    $submitBtn.prop('disabled', true);
    
    // Show loading state
    $submitBtn.html('<span class="spinner-border spinner-border-sm" role="status"></span> Processing...');
    
    // Reset validation and alerts
    $form[0].classList.remove('was-validated');
    $alert.addClass('d-none').removeClass('alert-danger alert-success');
    
    // Check form validity
    if (!$form[0].checkValidity()) {
        e.stopPropagation();
        $form.addClass('was-validated');
        $submitBtn.prop('disabled', false).html(originalBtnText);
        return;
    }
    
    // Check password match
    const password = $('#add_password').val();
    const confirmPassword = $('#add_confirm_password').val();
    
    if (password !== confirmPassword) {
        $alert.removeClass('d-none').addClass('alert-danger')
            .html('<i class="bx bx-error"></i> Passwords do not match');
        $submitBtn.prop('disabled', false).html(originalBtnText);
        return;
    }
    
    // Prepare form data
    const formData = new FormData(this);
    
    // Send request
    $.ajax({
        url: 'api/users.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                showToast('success', 'User added successfully');
                $('#addUserModal').modal('hide');
                $form.trigger('reset');
                if (typeof usersTable !== 'undefined') {
                    usersTable.ajax.reload(null, false);
                }
            } else {
                const errorMsg = response && response.message ? response.message : 'Failed to add user';
                $alert.removeClass('d-none').addClass('alert-danger')
                    .html(`<i class="bx bx-error"></i> ${errorMsg}`);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error adding user:', error);
            $alert.removeClass('d-none').addClass('alert-danger')
                .html('<i class="bx bx-error"></i> An error occurred while adding the user');
        },
        complete: function() {
            $submitBtn.prop('disabled', false).html(originalBtnText);
        }
    });
}, 500)); // 500ms debounce time

// Generate random password
$(document).on('click', '#generatePassword', function() {
    const length = 12;
    const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+~`|}{[]\:;?><,./-';
    let password = '';
    
    // Ensure at least one character from each character set
    password += 'abcdefghijklmnopqrstuvwxyz'.charAt(Math.floor(Math.random() * 26));
    password += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.charAt(Math.floor(Math.random() * 26));
    password += '0123456789'.charAt(Math.floor(Math.random() * 10));
    password += '!@#$%^&*()_+~`|}{[]\:;?><,./-'.charAt(Math.floor(Math.random() * 30));
    
    // Fill the rest of the password
    for (let i = password.length; i < length; i++) {
        password += charset.charAt(Math.floor(Math.random() * charset.length));
    }
    
    // Shuffle the password
    password = password.split('').sort(() => Math.random() - 0.5).join('');
    
    // Set the password fields
    const $passwordInput = $('#add_password');
    const $confirmPasswordInput = $('#add_confirm_password');
    
    $passwordInput.val(password);
    $confirmPasswordInput.val(password);
    
    // Trigger change event to update validation
    $passwordInput.trigger('change');
    $confirmPasswordInput.trigger('change');
    
    // Focus on password field
    $passwordInput.focus();
});

// Reset custom validation when password fields change
$('#add_password, #add_confirm_password').on('input', function() {
    this.setCustomValidity('');
    this.reportValidity();
});

// Show alert message
function showAlert($element, type, message) {
    $element.removeClass('d-none')
             .addClass(`alert-${type}`)
             .html(`<div class="d-flex align-items-center">
                       <i class="bx ${type === 'success' ? 'bx-check-circle' : 'bx-error'}" aria-hidden="true"></i>
                       <span class="ms-2">${message}</span>
                   </div>`)
             .attr('role', 'alert')
             .focus();
}

// Show toast notification
function showToast(type, message) {
    const toastId = 'toast-' + Date.now();
    const toast = `
        <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0" 
             role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bx ${type === 'success' ? 'bx-check-circle' : 'bx-error'}" aria-hidden="true"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" 
                        data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>`;
    
    // Add toast to container
    $('.toast-container').append(toast);
    
    // Initialize and show toast
    const toastElement = document.getElementById(toastId);
    const bsToast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 5000
    });
    
    bsToast.show();
    
    // Remove toast from DOM after it's hidden
    toastElement.addEventListener('hidden.bs.toast', function () {
        $(this).remove();
    });
}

// Handle edit form submission
$(document).on('submit', '#editUserForm', function(e) {
    e.preventDefault();
    
    const $form = $(this);
    const $submitBtn = $form.find('button[type="submit"]');
    const originalBtnText = $submitBtn.html();
    
    // Show loading state
    $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
    
    // Get form data
    const formData = new FormData(this);
    
    // Add action parameter
    formData.append('action', 'update_user');
    
    // Send request
    $.ajax({
        url: 'api/users.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                showToast('success', 'User updated successfully');
                $('#editUserModal').modal('hide');
                usersTable.ajax.reload();
            } else {
                const errorMsg = response && response.message ? response.message : 'Failed to update user';
                showToast('error', errorMsg);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error updating user:', error);
            showToast('error', 'An error occurred while updating the user');
        },
        complete: function() {
            $submitBtn.prop('disabled', false).html(originalBtnText);
        }
    });
});

// Initialize tooltips
$(function () {
    $('[data-bs-toggle="tooltip"]').tooltip({
        trigger: 'hover',
        container: 'body'
    });
    
    // Initialize popovers
    $('[data-bs-toggle="popover"]').popover({
        trigger: 'hover',
        container: 'body',
        html: true
    });
});
</script>

<!-- Move all scripts to the bottom of the page, before closing body tag -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<!-- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable with client-side processing
    var usersTable = $('#usersTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        order: [[0, 'desc']],
        language: {
            emptyTable: 'No users found',
            zeroRecords: 'No matching users found'
        },
        drawCallback: function() {
            // Re-initialize tooltips after table redraw
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });

    // Edit User Button Click Handler
    $(document).on('click', '.edit-user', function() {
        const userId = $(this).data('id');
        console.log('Edit clicked for user ID:', userId);
        
        // Show the modal
        const $modal = $('#editUserModal');
        const editModal = new bootstrap.Modal($modal[0]);
        
        // Show loading state
        $modal.find('.modal-body').html(`
            <div class="text-center p-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading user data...</p>
            </div>
        `);
        
        // Show the modal
        editModal.show();

        // Load user data
        $.ajax({
            url: 'api/users.php',
            type: 'GET',
            data: { id: userId },
            dataType: 'json',
            success: function(response) {
                console.log('API Response:', response);
                if (response && response.success && response.data) {
                    const user = response.data;
                    
                    // Update form fields
                    $('#edit_user_id').val(user.id);
                    $('#edit_first_name').val(user.first_name || '');
                    $('#edit_last_name').val(user.last_name || '');
                    $('#edit_email').val(user.email || '');
                    
                    // Set role and status values
                    if (user.role) {
                        $('#edit_role').val(user.role);
                    }
                    if (user.status !== undefined) {
                        $('#edit_status').val(user.status.toString());
                    }
                    
                    // Show the form
                    $modal.find('.modal-body').html($('#editUserForm').html());
                    
                    // Re-initialize form validation
                    const form = document.getElementById('editUserForm');
                    if (form) {
                        form.addEventListener('submit', function(event) {
                            event.preventDefault();
                            
                            if (!form.checkValidity()) {
                                event.stopPropagation();
                                form.classList.add('was-validated');
                                return;
                            }
                            
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
                            
                            // Send request
                            $.ajax({
                                url: 'api/users.php',
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                dataType: 'json',
                                success: function(response) {
                                    console.log('Update response:', response);
                                    if (response && response.success) {
                                        showToast('success', 'User updated successfully');
                                        editModal.hide();
                                        // Reload the page to see changes
                                        location.reload();
                                    } else {
                                        showToast('error', response?.message || 'Failed to update user');
                                    }
                                },
                                error: function(xhr) {
                                    console.error('Error updating user:', xhr);
                                    showToast('error', 'Error updating user. Please try again.');
                                },
                                complete: function() {
                                    $submitBtn.prop('disabled', false).html(originalBtnText);
                                }
                            });
                        });
                    }
                } else {
                    editModal.hide();
                    showToast('error', response?.message || 'Failed to load user data');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading user data:', error, xhr.responseText);
                editModal.hide();
                showToast('error', 'Error loading user data. Please try again.');
            }
        });
    });
}); // Close document.ready

// Function to get edit form HTML
function getEditForm(user = {}) {
    const csrfToken = $('meta[name="csrf-token"]').attr('content') || '';
    return `
    <form id="editUserForm" method="post" class="needs-validation" novalidate>
        <input type="hidden" name="action" value="update_user">
        <input type="hidden" name="user_id" id="edit_user_id" value="${user.id || ''}">
        <input type="hidden" name="csrf_token" value="${csrfToken}">
        <div class="modal-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="edit_first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_first_name" name="first_name" 
                               value="${user.first_name || ''}" required>
                        <div class="invalid-feedback">Please enter first name</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="edit_last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="edit_last_name" name="last_name"
                               value="${user.last_name || ''}">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="edit_email" name="email" 
                               value="${user.email || ''}" required>
                        <div class="invalid-feedback">Please enter a valid email</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="edit_role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_role" name="role" required>
                            <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Admin</option>
                            <option value="instructor" ${user.role === 'instructor' ? 'selected' : ''}>Instructor</option>
                            <option value="student" ${user.role === 'student' ? 'selected' : ''}>Student</option>
                            <option value="staff" ${user.role === 'staff' ? 'selected' : ''}>Staff</option>
                            <option value="parent" ${user.role === 'parent' ? 'selected' : ''}>Parent</option>
                        </select>
                        <div class="invalid-feedback">Please select a role</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="1" ${user.status == 1 ? 'selected' : ''}>Active</option>
                            <option value="0" ${user.status == 0 ? 'selected' : ''}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class='bx bx-x me-1'></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class='bx bx-save me-1'></i> Save Changes
                </button>
            </div>
        </div>
    </form>`;
}
// Handle form submission
$(document).on('submit', '#editUserForm', function(e) {
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
    
    // Send request
    $.ajax({
        url: 'api/users.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            console.log('Update response:', response);
            if (response && response.success) {
                showToast('success', 'User updated successfully');
                $('#editUserModal').modal('hide');
                // Reload the page to see changes
                location.reload();
            } else {
                showToast('error', response?.message || 'Failed to update user');
            }
        },
        error: function(xhr) {
            console.error('Error updating user:', xhr);
            showToast('error', 'Error updating user. Please try again.');
        },
        complete: function() {
            $submitBtn.prop('disabled', false).html(originalBtnText);
        }
    });
});
// Update the edit user click handler to use the new getEditForm function
$(document).on('click', '.edit-user', function() {
    const userId = $(this).data('id');
    console.log('Edit clicked for user ID:', userId);
    
    // Show the modal
    const $modal = $('#editUserModal');
    const editModal = new bootstrap.Modal($modal[0]);
    
    // Show loading state
    $modal.find('.modal-body').html(`
        <div class="text-center p-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading user data...</p>
        </div>
    `);
    
    // Show the modal
    editModal.show();

    // Load user data
    $.ajax({
        url: 'api/users.php',
        type: 'GET',
        data: { id: userId },
        dataType: 'json',
        success: function(response) {
            console.log('API Response:', response);
            if (response && response.success && response.data) {
                const user = response.data;
                // Update modal with form
                $modal.find('.modal-body').html(getEditForm(user));
            } else {
                editModal.hide();
                showToast('error', response?.message || 'Failed to load user data');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading user data:', error, xhr.responseText);
            editModal.hide();
            showToast('error', 'Error loading user data. Please try again.');
        }
    });
});
// Initialize DataTable
$(document).ready(function() {
    $('#usersTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        order: [[0, 'desc']],
        language: {
            emptyTable: 'No users found',
            zeroRecords: 'No matching users found'
        },
        drawCallback: function() {
            // Re-initialize tooltips after table redraw
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
});
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class='bx bx-x me-1'></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class='bx bx-save me-1'></i> Save Changes
                            </button>
                        </div>
                    </form>
                `;
                
                // Update modal content
                $modal.find('.modal-body').html(formHtml);
                
                // Populate form fields
                $('#edit_user_id').val(user.id);
                $('#edit_first_name').val(user.first_name || '');
                $('#edit_last_name').val(user.last_name || '');
                $('#edit_email').val(user.email || '');
                
                // Set role and status values
                if (user.role) {
                    $('#edit_role').val(user.role);
                }
                if (user.status !== undefined) {
                    $('#edit_status').val(user.status.toString());
                }
                
                // Initialize form validation
                const form = document.getElementById('editUserForm');
                if (form) {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();
                        
                        if (!form.checkValidity()) {
                            event.stopPropagation();
                            form.classList.add('was-validated');
                            return;
                        }
                        
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
                        
                        // Add action parameter
                        formData.append('action', 'update_user');
                        
                        // Send request
                        $.ajax({
                            url: 'api/users.php',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            dataType: 'json',
                            success: function(response) {
                                console.log('Update response:', response);
                                if (response && response.success) {
                                    showToast('success', 'User updated successfully');
                                    editModal.hide();
                                    if (typeof usersTable !== 'undefined') {
                                        usersTable.ajax.reload();
                                    }
                                } else {
                                    showToast('error', response?.message || 'Failed to update user');
                                }
                            },
                            error: function(xhr) {
                                console.error('Error updating user:', {
                                    status: xhr.status,
                                    statusText: xhr.statusText,
                                    responseText: xhr.responseText
                                });
                                showToast('error', 'Error updating user. Please try again.');
                            },
                            complete: function() {
                                $submitBtn.prop('disabled', false).html(originalBtnText);
                            }
                        });
                    });
                }
            } else {
                editModal.hide();
                showToast('error', response?.message || 'Failed to load user data');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            showToast('error', 'An error occurred while processing your request');
        }
    });

    // Handle delete user button click
    $(document).on('click', '.delete-user', function() {
        const userId = $(this).data('id');
        if (confirm('Are you sure you want to delete this user?')) {
            $.ajax({
                url: 'api/users.php',
                type: 'POST',
                data: {
                    action: 'delete',
                    id: userId,
                    csrf_token: '<?php echo $_SESSION['csrf_token']; ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response && response.success) {
                        showToast('success', 'User deleted successfully');
                        usersTable.ajax.reload();
                    } else {
                        const errorMsg = (response && response.message) || 'Failed to delete user';
                        showToast('error', errorMsg);
                    }
                },
                error: function() {
                    showToast('error', 'Error deleting user');
                }
            });
        }
    });
});
</script>

<!-- Toast Container -->
<script>
function showToast(type, message) {
    const toast = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    $('.toast-container').append(toast);
    const toastElement = $('.toast').last();
    const bsToast = new bootstrap.Toast(toastElement[0]);
    bsToast.show();
    
    // Remove toast after it's hidden
    toastElement.on('hidden.bs.toast', function () {
        $(this).remove();
    });
}
</script>
</body>
</html>
