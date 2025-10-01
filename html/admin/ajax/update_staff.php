<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(dirname(dirname(__FILE__))) . '/includes/database.php';
require_once dirname(dirname(dirname(__FILE__))) . '/includes/staff_functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'data' => null
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
    
    // Get staff ID
    $staff_id = filter_input(INPUT_POST, 'staff_id', FILTER_VALIDATE_INT);
    if (!$staff_id) {
        throw new Exception('Invalid staff ID');
    }
    
    // Get existing staff data
    $existing_staff = getStaffById($conn, $staff_id);
    if (!$existing_staff) {
        throw new Exception('Staff member not found');
    }
    
    // Validate required fields
    $required = ['first_name', 'last_name', 'email', 'position'];
    $missing = [];
    $staff_data = [
        'id' => $staff_id
    ];
    
    foreach ($required as $field) {
        if (empty(trim($_POST[$field] ?? ''))) {
            $missing[] = $field;
        } else {
            $staff_data[$field] = trim($_POST[$field]);
        }
    }
    
    if (!empty($missing)) {
        throw new Exception('Please fill in all required fields: ' . implode(', ', $missing));
    }
    
    // Validate email format
    if (!filter_var($staff_data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Please enter a valid email address');
    }
    
    // Check if email already exists for another staff member
    $stmt = $conn->prepare("SELECT id FROM staff WHERE email = ? AND id != ?");
    $stmt->bind_param('si', $staff_data['email'], $staff_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception('A staff member with this email already exists');
    }
    
    // Handle file upload if a new image is provided
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
            // Delete old image if it exists
            if (!empty($existing_staff['image_url']) && file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . $existing_staff['image_url'])) {
                unlink(dirname(dirname(dirname(dirname(__FILE__)))) . $existing_staff['image_url']);
            }
            $staff_data['image_url'] = "/uploads/staff/" . $new_filename;
        } else {
            throw new Exception('Sorry, there was an error uploading your file.');
        }
    } elseif (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
        // Remove existing image if requested
        if (!empty($existing_staff['image_url']) && file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . $existing_staff['image_url'])) {
            unlink(dirname(dirname(dirname(dirname(__FILE__)))) . $existing_staff['image_url']);
            $staff_data['image_url'] = null;
        }
    }
    
    // Prepare staff data
    $staff_data['title'] = $_POST['title'] ?? '';
    $staff_data['department_id'] = !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null;
    $staff_data['qualification'] = $_POST['qualification'] ?? '';
    $staff_data['bio'] = $_POST['bio'] ?? '';
    $staff_data['phone'] = $_POST['phone'] ?? '';
    $staff_data['is_teaching'] = isset($_POST['is_teaching']) ? 1 : 0;
    
    // Update staff in database
    $result = updateStaff($conn, $staff_data);
    
    if ($result) {
        $response['success'] = true;
        $response['message'] = 'Staff member updated successfully';
        $response['data'] = ['staff_id' => $staff_id];
    } else {
        // If database update fails, delete the newly uploaded file if any
        if (isset($staff_data['image_url']) && file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . $staff_data['image_url'])) {
            unlink(dirname(dirname(dirname(dirname(__FILE__)))) . $staff_data['image_url']);
        }
        throw new Exception('Failed to update staff member: ' . ($conn->error ?? 'Unknown error'));
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(400);
}

// Return JSON response
echo json_encode($response);
?>
