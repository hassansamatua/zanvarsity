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
    
    // Get staff ID from request
    $staff_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    if (!$staff_id) {
        throw new Exception('Invalid staff ID');
    }
    
    // Get staff details
    $staff = getStaffById($conn, $staff_id);
    
    if (!$staff) {
        throw new Exception('Staff member not found');
    }
    
    // Get department name if department_id exists
    if (!empty($staff['department_id'])) {
        $stmt = $conn->prepare("SELECT name FROM departments WHERE id = ?");
        $stmt->bind_param('i', $staff['department_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $department = $result->fetch_assoc();
        $staff['department_name'] = $department ? $department['name'] : null;
    }
    
    $response['success'] = true;
    $response['data'] = $staff;
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(400);
}

// Return JSON response
echo json_encode($response);
?>
