<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
require_once __DIR__ . '/../includes/database.php';

// Set JSON header
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'files' => []
];

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit();
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $response['message'] = 'Invalid CSRF token';
    echo json_encode($response);
    exit();
}

// Check if event ID is provided
if (empty($_POST['event_id'])) {
    $response['message'] = 'Event ID is required';
    echo json_encode($response);
    exit();
}

$event_id = (int)$_POST['event_id'];

// Check if files were uploaded
if (empty($_FILES['gallery_images'])) {
    $response['message'] = 'No files were uploaded';
    echo json_encode($response);
    exit();
}

// Create uploads directory if it doesn't exist
$uploadDir = __DIR__ . '/../../uploads/events/' . $event_id . '/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Process each uploaded file
$uploadedFiles = [];
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
$maxFileSize = 5 * 1024 * 1024; // 5MB

foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmpName) {
    $fileName = $_FILES['gallery_images']['name'][$key];
    $fileSize = $_FILES['gallery_images']['size'][$key];
    $fileType = $_FILES['gallery_images']['type'][$key];
    $fileError = $_FILES['gallery_images']['error'][$key];
    
    // Skip if there was an error
    if ($fileError !== UPLOAD_ERR_OK) {
        $response['files'][] = [
            'name' => $fileName,
            'success' => false,
            'message' => 'Upload error: ' . $fileError
        ];
        continue;
    }
    
    // Validate file type
    if (!in_array($fileType, $allowedTypes)) {
        $response['files'][] = [
            'name' => $fileName,
            'success' => false,
            'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.'
        ];
        continue;
    }
    
    // Validate file size
    if ($fileSize > $maxFileSize) {
        $response['files'][] = [
            'name' => $fileName,
            'success' => false,
            'message' => 'File is too large. Maximum size is 5MB.'
        ];
        continue;
    }
    
    // Generate unique filename
    $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
    $newFileName = uniqid('img_') . '.' . $fileExt;
    $targetPath = $uploadDir . $newFileName;
    
    // Move uploaded file
    if (move_uploaded_file($tmpName, $targetPath)) {
        // Get relative path for database
        $relativePath = '/uploads/events/' . $event_id . '/' . $newFileName;
        
        // Insert into database
        try {
            $stmt = $GLOBALS['conn']->prepare("INSERT INTO event_galleries (event_id, image_url, created_at) VALUES (?, ?, NOW())");
            $stmt->bind_param('is', $event_id, $relativePath);
            
            if ($stmt->execute()) {
                $uploadedFiles[] = [
                    'id' => $stmt->insert_id,
                    'url' => $relativePath,
                    'name' => $fileName
                ];
                
                $response['files'][] = [
                    'name' => $fileName,
                    'success' => true,
                    'message' => 'Uploaded successfully',
                    'url' => $relativePath
                ];
            } else {
                // If database insert fails, delete the uploaded file
                @unlink($targetPath);
                $response['files'][] = [
                    'name' => $fileName,
                    'success' => false,
                    'message' => 'Failed to save file information to database'
                ];
            }
            
            $stmt->close();
        } catch (Exception $e) {
            // If there's an error, delete the uploaded file
            @unlink($targetPath);
            $response['files'][] = [
                'name' => $fileName,
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    } else {
        $response['files'][] = [
            'name' => $fileName,
            'success' => false,
            'message' => 'Failed to move uploaded file'
        ];
    }
}

// If any files were uploaded successfully, mark the overall operation as successful
if (!empty($uploadedFiles)) {
    $response['success'] = true;
    $response['message'] = count($uploadedFiles) . ' file(s) uploaded successfully';
    $response['uploaded_files'] = $uploadedFiles;
} else {
    $response['message'] = 'No files were uploaded successfully';
}

echo json_encode($response);
?>
