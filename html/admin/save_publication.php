<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/publication_functions.php';

// Set JSON content type header
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
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

// Get action
$action = $_POST['action'] ?? '';
$publicationId = $_POST['publication_id'] ?? 0;

// Validate required fields
$requiredFields = ['title', 'author', 'publication_date'];
$missingFields = [];

foreach ($requiredFields as $field) {
    if (empty(trim($_POST[$field] ?? ''))) {
        $missingFields[] = $field;
    }
}

if (!empty($missingFields)) {
    $response['message'] = 'Please fill in all required fields: ' . implode(', ', $missingFields);
    $response['errors'] = $missingFields;
    echo json_encode($response);
    exit;
}

// Sanitize input
$title = trim($conn->real_escape_string($_POST['title']));
$author = trim($conn->real_escape_string($_POST['author']));
$description = trim($conn->real_escape_string($_POST['description'] ?? ''));
$publicationDate = date('Y-m-d', strtotime($_POST['publication_date']));
$isFeatured = isset($_POST['is_featured']) ? 1 : 0;

// Handle file uploads
$imageUrl = '';
$documentUrl = '';

// Handle cover image upload
if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../../uploads/publications/images/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $fileExtension = strtolower(pathinfo($_FILES['image_url']['name'], PATHINFO_EXTENSION));
    $filename = 'pub_cover_' . time() . '_' . uniqid() . '.' . $fileExtension;
    $targetFile = $uploadDir . $filename;
    
    // Validate image
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($fileExtension, $allowedTypes)) {
        if (move_uploaded_file($_FILES['image_url']['tmp_name'], $targetFile)) {
            $imageUrl = '/zanvarsity/html/uploads/publications/images/' . $filename;
            
            // Delete old image if updating
            if ($action === 'update_publication' && $publicationId) {
                $oldImage = $conn->query("SELECT image_url FROM publications WHERE id = $publicationId")->fetch_assoc()['image_url'] ?? '';
                if ($oldImage && file_exists(__DIR__ . '/../..' . $oldImage)) {
                    unlink(__DIR__ . '/../..' . $oldImage);
                }
            }
        }
    }
}

// Handle document upload
if (isset($_FILES['document_url']) && $_FILES['document_url']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../../uploads/publications/documents/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $fileExtension = strtolower(pathinfo($_FILES['document_url']['name'], PATHINFO_EXTENSION));
    $filename = 'pub_doc_' . time() . '_' . uniqid() . '.' . $fileExtension;
    $targetFile = $uploadDir . $filename;
    
    // Validate document
    $allowedDocTypes = ['pdf', 'doc', 'docx', 'txt'];
    if (in_array($fileExtension, $allowedDocTypes)) {
        if (move_uploaded_file($_FILES['document_url']['tmp_name'], $targetFile)) {
            $documentUrl = '/zanvarsity/html/uploads/publications/documents/' . $filename;
            
            // Delete old document if updating
            if ($action === 'update_publication' && $publicationId) {
                $oldDoc = $conn->query("SELECT document_url FROM publications WHERE id = $publicationId")->fetch_assoc()['document_url'] ?? '';
                if ($oldDoc && file_exists(__DIR__ . '/../..' . $oldDoc)) {
                    unlink(__DIR__ . '/../..' . $oldDoc);
                }
            }
        }
    }
}

try {
    if ($action === 'add_publication') {
        // Add new publication
        $stmt = $conn->prepare("INSERT INTO publications 
            (title, author, description, publication_date, image_url, document_url, is_featured) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", 
            $title,
            $author,
            $description,
            $publicationDate,
            $imageUrl,
            $documentUrl,
            $isFeatured
        );
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Publication added successfully';
            $response['id'] = $conn->insert_id;
        } else {
            throw new Exception('Failed to add publication: ' . $conn->error);
        }
        
    } elseif ($action === 'update_publication' && $publicationId) {
        // Update existing publication
        $updateFields = [];
        $params = [];
        $types = '';
        
        // Build dynamic query based on provided fields
        $updateFields[] = "title = ?";
        $params[] = $title;
        $types .= 's';
        
        $updateFields[] = "author = ?";
        $params[] = $author;
        $types .= 's';
        
        $updateFields[] = "description = ?";
        $params[] = $description;
        $types .= 's';
        
        $updateFields[] = "publication_date = ?";
        $params[] = $publicationDate;
        $types .= 's';
        
        $updateFields[] = "is_featured = ?";
        $params[] = $isFeatured;
        $types .= 'i';
        
        // Only update image_url if a new image was uploaded
        if (!empty($imageUrl)) {
            $updateFields[] = "image_url = ?";
            $params[] = $imageUrl;
            $types .= 's';
        }
        
        // Only update document_url if a new document was uploaded
        if (!empty($documentUrl)) {
            $updateFields[] = "document_url = ?";
            $params[] = $documentUrl;
            $types .= 's';
        }
        
        // Add publication ID to params
        $params[] = $publicationId;
        $types .= 'i';
        
        // Build and execute the query
        $sql = "UPDATE publications SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        // Bind parameters dynamically
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Publication updated successfully';
            $response['id'] = $publicationId;
        } else {
            throw new Exception('Failed to update publication: ' . $conn->error);
        }
    } else {
        throw new Exception('Invalid action or missing publication ID');
    }
    
} catch (Exception $e) {
    // Clean up uploaded files if there was an error
    if (!empty($imageUrl) && file_exists(__DIR__ . '/../..' . $imageUrl)) {
        unlink(__DIR__ . '/../..' . $imageUrl);
    }
    if (!empty($documentUrl) && file_exists(__DIR__ . '/../..' . $documentUrl)) {
        unlink(__DIR__ . '/../..' . $documentUrl);
    }
    
    $response['message'] = $e->getMessage();
    error_log('Publication save error: ' . $e->getMessage());
}

// Return JSON response
echo json_encode($response);
?>
