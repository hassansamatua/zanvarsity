<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set JSON content type
header('Content-Type: application/json');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't show errors to the user

// Include required files
require_once __DIR__ . '/../../../includes/database.php';
require_once __DIR__ . '/../../../includes/auth_functions.php';

// Function to send JSON response and exit
function send_json($data, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data);
    exit;
}

try {
    // Check if user is logged in and is admin
    if (!is_logged_in()) {
        send_json(['success' => false, 'message' => 'Please log in to perform this action'], 401);
    }
    
    if (!is_admin()) {
        send_json(['success' => false, 'message' => 'Insufficient permissions'], 403);
    }

    // Verify CSRF token for POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            send_json(['success' => false, 'message' => 'Invalid or expired CSRF token'], 403);
        }
    }

    // Get action from request
    $action = $_GET['action'] ?? ($_POST['action'] ?? '');

    // Handle different actions
    switch ($action) {
        case 'get_event':
            getEvent();
            break;
        case 'add_event':
            addEvent();
            break;
        case 'update_event':
            updateEvent();
            break;
        case 'delete_event':
            deleteEvent();
            break;
        default:
            send_json(['success' => false, 'message' => 'Invalid action'], 400);
    }
} catch (Exception $e) {
    error_log('Error in events API: ' . $e->getMessage());
    send_json([
        'success' => false,
        'message' => 'An error occurred while processing your request'
    ], 500);
}

/**
 * Add a new event
 */
function addEvent() {
    global $conn;
    
    // Validate required fields
    $required = ['title', 'start_date'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            send_json(['success' => false, 'message' => ucfirst($field) . ' is required'], 400);
        }
    }
    
    // Log form data for debugging
    error_log('Form data received: ' . print_r($_POST, true));
    
    // Process file upload if present
    $imagePath = null;
    if (!empty($_FILES['event_image']['name'])) {
        error_log('File upload detected: ' . print_r($_FILES['event_image'], true));
        $uploadDir = __DIR__ . '/../../uploads/events/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = time() . '_' . basename($_FILES['event_image']['name']);
        $targetPath = $uploadDir . $fileName;
        
        // Check file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['event_image']['type'];
        if (!in_array($fileType, $allowedTypes)) {
            send_json(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG and GIF are allowed.'], 400);
        }
        
        // Check file size (max 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($_FILES['event_image']['size'] > $maxSize) {
            send_json(['success' => false, 'message' => 'File is too large. Maximum size is 5MB.'], 400);
        }
        
        // Move uploaded file
        if (move_uploaded_file($_FILES['event_image']['tmp_name'], $targetPath)) {
            $imagePath = '/zanvarsity/html/admin/uploads/events/' . $fileName;
        } else {
            error_log('Failed to move uploaded file: ' . $_FILES['event_image']['tmp_name'] . ' to ' . $targetPath);
            send_json(['success' => false, 'message' => 'Failed to upload image'], 500);
        }
    }
    
    // Prepare data
    $title = trim($_POST['title']);
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $status = in_array($_POST['status'] ?? 'upcoming', ['upcoming', 'ongoing', 'completed', 'cancelled']) ? $_POST['status'] : 'upcoming';
    $startDate = date('Y-m-d H:i:s', strtotime($_POST['start_date']));
    $endDate = !empty($_POST['end_date']) ? date('Y-m-d H:i:s', strtotime($_POST['end_date'])) : null;
    
    try {
        // Log database connection status
        if ($conn->connect_error) {
            error_log('Database connection failed: ' . $conn->connect_error);
            send_json(['success' => false, 'message' => 'Database connection failed'], 500);
        }
        
        $sql = "
            INSERT INTO events (title, description, start_date, end_date, location, image_url, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ";
        error_log('Preparing SQL: ' . $sql);
        
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            error_log('Prepare failed: ' . $conn->error);
            send_json(['success' => false, 'message' => 'Database error: ' . $conn->error], 500);
        }
        
        $stmt->bind_param(
            'sssssss',
            $title,
            $description,
            $startDate,
            $endDate,
            $location,
            $imagePath,
            $status
        );
        
        $executeResult = $stmt->execute();
        
        if ($executeResult) {
            $eventId = $conn->insert_id;
            error_log('Event added successfully with ID: ' . $eventId);
            send_json([
                'success' => true, 
                'message' => 'Event added successfully',
                'eventId' => $eventId
            ]);
        } else {
            // If we added an image but the database insert failed, try to delete the image
            if ($imagePath && file_exists($targetPath)) {
                @unlink($targetPath);
            }
            error_log('Database error: ' . $stmt->error);
            throw new Exception('Database error: ' . $stmt->error);
        }
    } catch (Exception $e) {
        error_log('Error adding event: ' . $e->getMessage());
        send_json(['success' => false, 'message' => 'Failed to add event: ' . $e->getMessage()], 500);
    }
}

/**
 * Get event by ID
 */
function getEvent() {
    global $conn;
    
    $eventId = intval($_GET['id'] ?? 0);
    
    if ($eventId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid event ID']);
        return;
    }
    
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->bind_param('i', $eventId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Event not found']);
        return;
    }
    
    $event = $result->fetch_assoc();
    
    // Format dates for datetime-local input
    $event['start_date'] = date('Y-m-d\TH:i', strtotime($event['start_date']));
    if ($event['end_date']) {
        $event['end_date'] = date('Y-m-d\TH:i', strtotime($event['end_date']));
    }
    
    echo json_encode(['success' => true, 'data' => $event]);
}

/**
 * Update event
 */
function updateEvent() {
    global $conn;
    
    $eventId = intval($_POST['id'] ?? 0);
    
    if ($eventId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid event ID']);
        return;
    }
    
    // Get form data
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $startDate = $_POST['start_date'] ?? '';
    $endDate = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $status = $_POST['status'] ?? 'upcoming';
    $removeImage = isset($_POST['remove_image']);
    
    // Validate required fields
    if (empty($title) || empty($startDate)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Title and start date are required']);
        return;
    }
    
    // Process image upload if provided
    $imageUrl = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../../html/uploads/events/';
        $uploadedFile = $_FILES['image'];
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($uploadedFile['tmp_name']);
        
        if (!in_array($fileType, $allowedTypes)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Only JPG, PNG and GIF images are allowed']);
            return;
        }
        
        // Generate unique filename
        $fileExt = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
        $fileName = 'event_' . time() . '_' . uniqid() . '.' . $fileExt;
        $targetPath = $uploadDir . $fileName;
        
        // Move uploaded file
        if (move_uploaded_file($uploadedFile['tmp_name'], $targetPath)) {
            $imageUrl = '/zanvarsity/html/uploads/events/' . $fileName;
        }
    }
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Get current image URL if exists
        $currentImage = null;
        $stmt = $conn->prepare("SELECT image_url FROM events WHERE id = ?");
        $stmt->bind_param('i', $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $currentImage = $result->fetch_assoc()['image_url'];
        }
        
        // Prepare update query
        $query = "UPDATE events SET title = ?, description = ?, location = ?, start_date = ?, " .
                "end_date = ?, status = ?, updated_at = NOW()";
        $params = [
            $title,
            $description,
            $location,
            $startDate,
            $endDate,
            $status
        ];
        $types = 'ssssss';
        
        // Handle image updates
        if ($imageUrl) {
            $query .= ", image_url = ?";
            $params[] = $imageUrl;
            $types .= 's';
            
            // Delete old image if exists
            if ($currentImage) {
                $oldImagePath = realpath('../../../' . parse_url($currentImage, PHP_URL_PATH));
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
        } elseif ($removeImage && $currentImage) {
            $query .= ", image_url = NULL";
            
            // Delete old image
            $oldImagePath = realpath('../../../' . parse_url($currentImage, PHP_URL_PATH));
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
        
        $query .= " WHERE id = ?";
        $params[] = $eventId;
        $types .= 'i';
        
        // Execute update
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception('No changes made or event not found');
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Event updated successfully']);
        
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update event: ' . $e->getMessage()]);
    }
}

/**
 * Delete event
 */
function deleteEvent() {
    global $conn;
    
    try {
        $eventId = intval($_POST['id'] ?? 0);
        
        if ($eventId <= 0) {
            throw new Exception('Invalid event ID');
        }
        
        // Begin transaction
        $conn->begin_transaction();
        
        // Get image URL before deleting
        $stmt = $conn->prepare("SELECT image_url FROM events WHERE id = ?");
        $stmt->bind_param('i', $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Event not found');
        }
        
        $imageUrl = $result->fetch_assoc()['image_url'];
        
        // Delete the event
        $deleteStmt = $conn->prepare("DELETE FROM events WHERE id = ?");
        $deleteStmt->bind_param('i', $eventId);
        $deleteStmt->execute();
        
        if ($deleteStmt->affected_rows === 0) {
            throw new Exception('No rows affected by DELETE query');
        }
        
        // If we got here, the delete was successful
        $conn->commit();
        
        // Delete the image file if it exists
        if (!empty($imageUrl)) {
            $imagePath = $_SERVER['DOCUMENT_ROOT'] . parse_url($imageUrl, PHP_URL_PATH);
            if (file_exists($imagePath) && is_writable($imagePath)) {
                unlink($imagePath);
            }
        }
        
        send_json([
            'success' => true,
            'message' => 'Event deleted successfully'
        ]);
        
    } catch (Exception $e) {
        if (isset($conn) && $conn instanceof mysqli) {
            $conn->rollback();
        }
        
        error_log('Error in deleteEvent: ' . $e->getMessage());
        send_json([
            'success' => false,
            'message' => 'Failed to delete event: ' . $e->getMessage()
        ], 500);
    }
}

// Close database connection
$conn->close();
?>
