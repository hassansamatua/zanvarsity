<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(dirname(dirname(dirname(__FILE__)))) . '/logs/php_errors.log');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log the request data
error_log('POST data: ' . print_r($_POST, true));
if (isset($_FILES['image'])) {
    error_log('FILES data: ' . print_r($_FILES['image'], true));
}

// Include required files
require_once dirname(dirname(dirname(__FILE__))) . '/includes/database.php';
require_once dirname(dirname(dirname(__FILE__))) . '/includes/staff_functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'data' => null,
    'debug' => []
];

try {
    // Check if user is logged in and has admin role
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        throw new Exception('Unauthorized access');
    }
    
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        throw new Exception('Invalid CSRF token');
    }
    
    // Log all POST data for debugging
    $response['debug']['post_data'] = $_POST;
    
    // Validate required fields
    $required = ['first_name', 'last_name', 'email', 'position'];
    $missing = [];
    $staff_data = [];
    
    foreach ($required as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            $missing[] = $field;
        } else {
            $staff_data[$field] = trim($_POST[$field]);
        }
    }
    
    if (!empty($missing)) {
        $errorMsg = 'Please fill in all required fields: ' . implode(', ', $missing);
        $response['debug']['missing_fields'] = $missing;
        throw new Exception($errorMsg);
    }
    
    // Validate email format
    if (!filter_var($staff_data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Please enter a valid email address');
    }
    
    // Check if email already exists
    $response['debug']['email_check'] = 'Checking if email exists: ' . $staff_data['email'];
    $stmt = $conn->prepare("SELECT id FROM staff WHERE email = ?");
    if ($stmt === false) {
        $response['debug']['email_check_error'] = 'Prepare failed: ' . $conn->error;
        throw new Exception('Database error while checking email');
    }
    
    $stmt->bind_param('s', $staff_data['email']);
    if (!$stmt->execute()) {
        $response['debug']['email_check_error'] = 'Execute failed: ' . $stmt->error;
        throw new Exception('Database error while checking email');
    }
    
    $result = $stmt->get_result();
    if ($result === false) {
        $response['debug']['email_check_error'] = 'Get result failed: ' . $stmt->error;
        throw new Exception('Database error while checking email');
    }
    
    if ($result->num_rows > 0) {
        $response['debug']['email_exists'] = true;
        throw new Exception('A staff member with this email already exists');
    }
    
    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = dirname(dirname(dirname(dirname(__FILE__)))) . "/uploads/staff/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Check if image file is an actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            throw new Exception('File is not an image.');
        }
        
        // Check file size (5MB max)
        if ($_FILES["image"]["size"] > 5000000) {
            throw new Exception('Sorry, your file is too large. Maximum size is 5MB.');
        }
        
        // Allow certain file formats
        $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($file_extension, $allowed_extensions)) {
            throw new Exception('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');
        }
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $staff_data['image_url'] = "/uploads/staff/" . $new_filename;
        } else {
            throw new Exception('Sorry, there was an error uploading your file.');
        }
    }
    
    // Prepare staff data
    $staff_data['title'] = $_POST['title'] ?? '';
    $staff_data['department_id'] = !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null;
    $staff_data['qualification'] = $_POST['qualification'] ?? '';
    $staff_data['bio'] = $_POST['bio'] ?? '';
    $staff_data['phone'] = $_POST['phone'] ?? '';
    $staff_data['is_teaching'] = isset($_POST['is_teaching']) ? 1 : 0;
    
    // Log staff data before insertion
    $response['debug']['staff_data'] = $staff_data;
    
    // Insert into database
    try {
        $result = addStaff($conn, $staff_data);
        $response['debug']['insert_result'] = $result;
        
        if ($result) {
            $response['success'] = true;
            $response['message'] = 'Staff member added successfully';
            $response['data'] = ['staff_id' => $result];
        } else {
            $errorMsg = 'Failed to add staff member. ';
            $errorMsg .= 'MySQL Error: ' . ($conn->error ?? 'No error message');
            $errorMsg .= ' | Error No: ' . ($conn->errno ?? 'N/A');
            
            $response['debug']['mysql_error'] = $conn->error;
            $response['debug']['mysql_errno'] = $conn->errno;
            
            // If database insert fails, delete the uploaded file
            if (!empty($staff_data['image_url']) && file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . $staff_data['image_url'])) {
                $deleteResult = unlink(dirname(dirname(dirname(dirname(__FILE__)))) . $staff_data['image_url']);
                $response['debug']['file_deleted'] = $deleteResult;
            }
            
            throw new Exception($errorMsg);
        }
    } catch (Exception $e) {
        $response['debug']['exception'] = [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ];
        throw $e;
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(400);
}

// Return JSON response
echo json_encode($response);
?>
