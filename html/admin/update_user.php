<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/../../includes/database.php';

// Set JSON content type
header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? '', ['admin', 'super_admin'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit();
}

// Validate required fields
$required = ['action', 'user_id', 'first_name', 'email', 'role'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
        exit();
    }
}

// Sanitize input
$userId = (int)$_POST['user_id'];
$firstName = trim($_POST['first_name']);
$lastName = trim($_POST['last_name'] ?? '');
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$role = $_POST['role'];
$status = isset($_POST['status']) ? 1 : 0;

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

// Validate role
$allowed_roles = ['admin', 'instructor', 'student', 'staff'];
if (!in_array($role, $allowed_roles)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid role']);
    exit();
}

try {
    // Check if email already exists for another user
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        throw new Exception('Email already exists');
    }
    
    // Update user
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ?, status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("ssssii", $firstName, $lastName, $email, $role, $status, $userId);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update user: ' . $stmt->error);
    }
    
    // If updating own profile, update session
    if ($_SESSION['user_id'] == $userId) {
        $_SESSION['email'] = $email;
        $_SESSION['role'] = $role;
        // You might want to update other session variables as needed
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'User updated successfully'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
