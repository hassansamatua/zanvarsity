<?php
require_once __DIR__ . '/../../../includes/auth_functions.php';
require_once __DIR__ . '/../../../includes/database.php';
require_login();
require_admin();

header('Content-Type: application/json');

// Verify CSRF token
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit();
}

// Validate required fields
$required = ['publication_id', 'title', 'publish_date'];
$missing = [];
$data = [];

foreach ($required as $field) {
    if (empty(trim($_POST[$field] ?? ''))) {
        $missing[] = $field;
    } else {
        $data[$field] = trim($_POST[$field]);
    }
}

if (!empty($missing)) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Missing required fields: ' . implode(', ', $missing)
    ]);
    exit();
}

// Get existing publication data
$stmt = $conn->prepare("SELECT * FROM publications WHERE id = ?");
$stmt->bind_param("i", $data['publication_id']);
$stmt->execute();
$publication = $stmt->get_result()->fetch_assoc();

if (!$publication) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Publication not found']);
    exit();
}

// Handle file upload if a new file is provided
$file_url = $publication['file_url'];
if (isset($_FILES['publication_file']) && $_FILES['publication_file']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = __DIR__ . '/../../../uploads/publications/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Delete old file if exists
    if ($file_url && file_exists(__DIR__ . '/../../..' . $file_url)) {
        unlink(__DIR__ . '/../../..' . $file_url);
    }
    
    $file_name = time() . '_' . basename($_FILES['publication_file']['name']);
    $target_file = $upload_dir . $file_name;
    
    if (move_uploaded_file($_FILES['publication_file']['tmp_name'], $target_file)) {
        $file_url = '/zanvarsity/uploads/publications/' . $file_name;
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
        exit();
    }
}

// Update publication in database
$stmt = $conn->prepare("UPDATE publications SET 
    title = ?, 
    author = ?, 
    description = ?, 
    publish_date = ?, 
    file_url = COALESCE(?, file_url),
    status = ?,
    updated_at = NOW()
    WHERE id = ?");

$stmt->bind_param(
    "ssssssi",
    $_POST['title'],
    $_POST['author'] ?? null,
    $_POST['description'] ?? null,
    $_POST['publish_date'],
    $file_url,
    $_POST['status'] ?? 'draft',
    $data['publication_id']
);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Publication updated successfully',
        'data' => [
            'id' => $data['publication_id'],
            'title' => $_POST['title'],
            'author' => $_POST['author'] ?? null,
            'publish_date' => $_POST['publish_date'],
            'status' => $_POST['status'] ?? 'draft'
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to update publication: ' . $conn->error
    ]);
}
?>
