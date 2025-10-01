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
    'message' => ''
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
    
    // Get staff data to delete the associated image
    $staff = getStaffById($conn, $staff_id);
    
    if (!$staff) {
        throw new Exception('Staff member not found');
    }
    
    // Delete the staff member
    $result = deleteStaff($conn, $staff_id);
    
    if ($result) {
        // Delete the associated image if it exists
        if (!empty($staff['image_url'])) {
            $image_path = dirname(dirname(dirname(dirname(__FILE__)))) . $staff['image_url'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        $response['success'] = true;
        $response['message'] = 'Staff member deleted successfully';
    } else {
        throw new Exception('Failed to delete staff member: ' . ($conn->error ?? 'Unknown error'));
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(400);
}

// Return JSON response
echo json_encode($response);
?>
