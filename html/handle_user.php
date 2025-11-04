<?php
session_start();
require_once __DIR__ . '/../includes/database.php';

header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'super_admin'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

$action = $_POST['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];

try {
    switch ($action) {
        case 'add_user':
            $first_name = trim($_POST['first_name'] ?? '');
            $last_name = trim($_POST['last_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'student';
            $status = isset($_POST['is_active']) ? 1 : 0;
            
            // Validation
            if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
                throw new Exception('All fields are required');
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email address');
            }
            
            if (strlen($password) < 8) {
                throw new Exception('Password must be at least 8 characters long');
            }
            
            // Check if email exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                throw new Exception('Email already exists');
            }
            $stmt->close();
            
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role, status, created_at) 
                                   VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssssi", $first_name, $last_name, $email, $hashed_password, $role, $status);
            
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'User added successfully'];
            } else {
                throw new Exception('Failed to add user: ' . $conn->error);
            }
            break;
            
        case 'edit_user':
            $user_id = (int)($_POST['user_id'] ?? 0);
            $first_name = trim($_POST['first_name'] ?? '');
            $last_name = trim($_POST['last_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'student';
            $status = isset($_POST['is_active']) ? 1 : 0;
            
            // Validation
            if ($user_id <= 0) {
                throw new Exception('Invalid user ID');
            }
            
            if (empty($first_name) || empty($last_name) || empty($email)) {
                throw new Exception('All fields are required');
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email address');
            }
            
            // Check if email exists for another user
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $user_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                throw new Exception('Email already exists');
            }
            $stmt->close();
            
            // Update user
            if (!empty($password)) {
                if (strlen($password) < 8) {
                    throw new Exception('Password must be at least 8 characters long');
                }
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, password = ?, role = ?, status = ? WHERE id = ?");
                $stmt->bind_param("sssssii", $first_name, $last_name, $email, $hashed_password, $role, $status, $user_id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ?, status = ? WHERE id = ?");
                $stmt->bind_param("ssssii", $first_name, $last_name, $email, $role, $status, $user_id);
            }
            
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'User updated successfully'];
            } else {
                throw new Exception('Failed to update user: ' . $conn->error);
            }
            break;
            
        case 'delete':
            $user_id = (int)($_POST['user_id'] ?? 0);
            
            if ($user_id <= 0) {
                throw new Exception('Invalid user ID');
            }
            
            // Prevent deleting yourself
            if ($user_id === ($_SESSION['user_id'] ?? 0)) {
                throw new Exception('You cannot delete your own account');
            }
            
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            
            if ($stmt->execute()) {
                $response = ['success' => true, 'message' => 'User deleted successfully'];
            } else {
                throw new Exception('Failed to delete user: ' . $conn->error);
            }
            break;
            
        default:
            $response = ['success' => false, 'message' => 'Invalid action'];
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}

echo json_encode($response);
