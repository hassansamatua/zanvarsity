<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set JSON header
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'data' => []
];

try {
    // Include necessary files
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
    require_once ROOT_PATH . '/includes/auth_functions.php';
    require_once ROOT_PATH . '/includes/database.php';

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Authentication required');
    }

    // Get database connection
    global $conn;

    // Get announcement ID
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        throw new Exception('Invalid announcement ID');
    }

    // Prepare and execute query
    $query = "SELECT a.*, u.email as author_email, 
                     a.attachment_url, a.attachment_name
              FROM announcements a 
              LEFT JOIN users u ON a.created_by = u.id 
              WHERE a.id = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $response['success'] = true;
            $response['data'] = $result->fetch_assoc();
        } else {
            throw new Exception('Announcement not found');
        }
        
        $stmt->close();
    } else {
        throw new Exception('Database error: ' . $conn->error);
    }
} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);
?>
