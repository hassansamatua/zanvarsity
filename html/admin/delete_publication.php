<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once __DIR__ . '/../includes/database.php';

// Set JSON content type header
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => ''
];

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $response['message'] = 'Invalid CSRF token';
    echo json_encode($response);
    exit;
}

// Get publication ID
$publicationId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if (!$publicationId) {
    $response['message'] = 'Invalid publication ID';
    echo json_encode($response);
    exit;
}

try {
    // Start transaction
    $conn->begin_transaction();
    
    // Get publication details to delete associated files
    $stmt = $conn->prepare("SELECT image_url, document_url FROM publications WHERE id = ?");
    $stmt->bind_param("i", $publicationId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Publication not found');
    }
    
    $publication = $result->fetch_assoc();
    
    // Delete the publication
    $stmt = $conn->prepare("DELETE FROM publications WHERE id = ?");
    $stmt->bind_param("i", $publicationId);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to delete publication: ' . $conn->error);
    }
    
    // Delete associated files if they exist
    if (!empty($publication['image_url'])) {
        $imagePath = __DIR__ . '/../..' . $publication['image_url'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    if (!empty($publication['document_url'])) {
        $docPath = __DIR__ . '/../..' . $publication['document_url'];
        if (file_exists($docPath)) {
            unlink($docPath);
        }
    }
    
    // Commit transaction
    $conn->commit();
    
    $response['success'] = true;
    $response['message'] = 'Publication deleted successfully';
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    $response['message'] = $e->getMessage();
    error_log('Publication delete error: ' . $e->getMessage());
}

// Return JSON response
echo json_encode($response);
?>
