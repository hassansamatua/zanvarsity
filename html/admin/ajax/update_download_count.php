<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set content type to JSON
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => 'Invalid request',
    'new_count' => 0
];

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $response['message'] = 'Invalid CSRF token';
        echo json_encode($response);
        exit;
    }
    
    // Check if download ID is provided
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        $response['message'] = 'Invalid download ID';
        echo json_encode($response);
        exit;
    }
    
    $download_id = (int)$_POST['id'];
    
    // Include database connection
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/includes/database.php';
    
    // Update download count
    $conn = $GLOBALS['conn'] ?? null;
    if ($conn) {
        try {
            // First, get the current count to return
            $stmt = $conn->prepare("SELECT download_count FROM downloads WHERE id = ?");
            $stmt->bind_param("i", $download_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $new_count = $row['download_count'] + 1;
                
                // Update the count
                $update = $conn->prepare("UPDATE downloads SET download_count = download_count + 1, updated_at = NOW() WHERE id = ?");
                $update->bind_param("i", $download_id);
                
                if ($update->execute()) {
                    $response = [
                        'success' => true,
                        'message' => 'Download count updated',
                        'new_count' => $new_count
                    ];
                } else {
                    $response['message'] = 'Failed to update download count';
                }
            } else {
                $response['message'] = 'Download not found';
            }
        } catch (Exception $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Database connection failed';
    }
} else {
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);
?>
