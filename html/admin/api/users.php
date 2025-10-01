<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set headers for JSON response
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'data' => [],
    'debug' => [
        'session_id' => session_id(),
        'request_method' => $_SERVER['REQUEST_METHOD'],
        'get_params' => $_GET,
        'post_data' => $_POST,
        'session_data' => $_SESSION
    ]
];

try {
    // Include necessary files
    $dbFile = dirname(dirname(dirname(__FILE__))) . '/includes/database.php';
    $authFile = dirname(dirname(dirname(__FILE__))) . '/includes/auth_functions.php';
    
    if (!file_exists($dbFile)) {
        throw new Exception('Database file not found: ' . $dbFile);
    }
    if (!file_exists($authFile)) {
        throw new Exception('Auth functions file not found: ' . $authFile);
    }
    
    require_once $dbFile;
    require_once $authFile;

    // Check if user is logged in and has admin privileges
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Authentication required. Please log in.');
    }
    
    // Get database connection
    global $conn;
    if (!isset($conn) || !($conn instanceof mysqli)) {
        throw new Exception('Database connection not established');
    }
    
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    
    // Set character set
    if (!$conn->set_charset('utf8mb4')) {
        throw new Exception('Error setting charset: ' . $conn->error);
    }
    
    // Get request method
    $method = $_SERVER['REQUEST_METHOD'];
    $id = $_GET['id'] ?? null;
    
    // Handle GET request (fetch user data)
    if ($method === 'GET' && $id) {
        $stmt = $conn->prepare("SELECT id, first_name, last_name, email, role, status, created_at, last_login FROM users WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Database prepare failed: ' . $conn->error);
        }
        
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('User not found');
        }
        
        $response['data'] = $result->fetch_assoc();
        $response['success'] = true;
        $stmt->close();
    } 
    // Handle POST request (update user)
    elseif ($method === 'POST') {
        // Verify CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Invalid CSRF token');
        }

        $action = $_POST['action'] ?? '';
        
        if ($action === 'update_user') {
            // Validate required fields
            $required = ['user_id', 'first_name', 'email', 'role', 'status'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("Missing required field: $field");
                }
            }

            $user_id = (int)$_POST['user_id'];
            $first_name = trim($_POST['first_name']);
            $last_name = trim($_POST['last_name'] ?? '');
            $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
            $role = $_POST['role'];
            $status = (int)$_POST['status'];

            if (!$email) {
                throw new Exception("Invalid email address");
            }

            // Check if email already exists for another user
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $user_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                throw new Exception("Email already in use by another account");
            }

            // Update user
            $stmt = $conn->prepare("UPDATE users SET 
                first_name = ?,
                last_name = ?,
                email = ?,
                role = ?,
                status = ?,
                updated_at = NOW()
                WHERE id = ?");
            
            $stmt->bind_param("ssssii", 
                $first_name,
                $last_name,
                $email,
                $role,
                $status,
                $user_id
            );

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'User updated successfully';
            } else {
                throw new Exception("Failed to update user: " . $stmt->error);
            }
            
            $stmt->close();
        } else {
            throw new Exception('Invalid action');
        }
    } else {
        throw new Exception('Invalid request or missing ID');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    $response['message'] = $e->getMessage();
    $response['debug']['error'] = [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    
    // Log the error for debugging
    error_log('API Error: ' . $e->getMessage());
    error_log('File: ' . $e->getFile() . ' on line ' . $e->getLine());
    error_log('Stack trace: ' . $e->getTraceAsString());
}

// Output the response with debug info
if (isset($_GET['debug'])) {
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
} else {
    // Remove debug info from production response
    unset($response['debug']);
    echo json_encode($response);
}

// Close the database connection
if (isset($conn) && $conn) {
    $conn->close();
}
?>
