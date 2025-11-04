<?php
session_start();
require_once __DIR__ . '/../includes/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header for JSON response
header('Content-Type: application/json');

// Log directory
$log_dir = __DIR__ . '/../logs';
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0777, true);
}
$log_file = $log_dir . '/events_debug.log';

// Function to log debug information
function debug_log($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($log_file, $log_message, FILE_APPEND);
    error_log($message);
}

// Start logging
debug_log('=== New Request ===');
debug_log('Session data: ' . print_r($_SESSION, true));
debug_log('POST data: ' . print_r($_POST, true));
debug_log('FILES data: ' . print_r($_FILES, true));

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $error = 'User not logged in. Session user_id not set.';
    debug_log($error);
    echo json_encode(['success' => false, 'message' => 'You must be logged in to perform this action']);
    exit;
}

// Check if user has admin/dean privileges
$user_role = '';
if (isset($_SESSION['role'])) {
    $user_role = strtolower($_SESSION['role']);
} elseif (isset($_SESSION['user_role'])) {
    $user_role = strtolower($_SESSION['user_role']);
}

$allowed_roles = ['admin', 'super_admin', 'dean'];
if (!in_array($user_role, $allowed_roles)) {
    $error = "User does not have permission. User ID: {$_SESSION['user_id']}, Role: $user_role";
    debug_log($error);
    echo json_encode(['success' => false, 'message' => 'You do not have permission to perform this action']);
    exit;
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || empty($_POST['csrf_token'])) {
    $error = 'CSRF token is missing in the request';
    debug_log($error);
    echo json_encode(['success' => false, 'message' => 'Security token is missing. Please refresh the page and try again.']);
    exit;
}

if (!isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $error = 'CSRF token validation failed';
    debug_log($error);
    debug_log('Expected token: ' . ($_SESSION['csrf_token'] ?? 'Not set'));
    debug_log('Received token: ' . $_POST['csrf_token']);
    echo json_encode(['success' => false, 'message' => 'Invalid security token. Please refresh the page and try again.']);
    exit;
}

// Initialize response array
$response = ['success' => false, 'message' => 'Invalid request'];

try {
    debug_log('Starting event processing');
    
    // Validate required fields
    $required = ['title', 'start_date'];
    $missing_fields = [];
    
    foreach ($required as $field) {
        if (empty(trim($_POST[$field] ?? ''))) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        $error_msg = 'Missing required fields: ' . implode(', ', $missing_fields);
        debug_log($error_msg);
        throw new Exception("Please fill in all required fields");
    }

    // Sanitize and validate input
    $title = trim($_POST['title']);
    $start_date = $_POST['start_date'];
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    // Log the data being processed
    debug_log("Processing event - Title: $title, Start: $start_date, Location: $location");
    
    // Handle file upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/uploads/events/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($file_extension, $allowed_extensions)) {
            throw new Exception('Only JPG, JPEG, PNG & GIF files are allowed');
        }
        
        $filename = uniqid() . '_' . time() . '.' . $file_extension;
        $target_file = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = '/c/zanvarsity/html/uploads/events/' . $filename;
        } else {
            throw new Exception('Failed to upload image');
        }
    }
    
    // Insert event into database
    debug_log('Preparing to insert event into database');
    
    // First, check if the events table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'events'");
    if ($table_check->num_rows === 0) {
        throw new Exception('Events table does not exist in the database');
    }
    
    // Get the user ID
    $created_by = (int)$_SESSION['user_id'];
    
    // Log the data being inserted
    debug_log("Inserting event - Title: $title, Start: $start_date, Created By: $created_by");
    
    // Prepare the SQL query
    $sql = "INSERT INTO events (title, description, start_date, end_date, location, image_url, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    debug_log("SQL Query: $sql");
    
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("ssssssi", $title, $description, $start_date, $end_date, $location, $image_path, $created_by);
        
        // Execute the query
        if ($stmt->execute()) {
            $event_id = $stmt->insert_id;
            debug_log("Event inserted successfully. ID: $event_id");
            
            $response = [
                'success' => true, 
                'message' => 'Event created successfully',
                'event_id' => $event_id
            ];
            
            // Verify the event was actually inserted
            $verify = $conn->query("SELECT * FROM events WHERE id = $event_id");
            if ($verify->num_rows === 0) {
                debug_log("WARNING: Event with ID $event_id not found after insertion!");
            } else {
                debug_log("Event verification successful");
            }
        } else {
            $error = 'Database error: ' . $stmt->error;
            debug_log($error);
            throw new Exception('Failed to save event to database');
        }
        
        $stmt->close();
    } else {
        $error = 'Failed to prepare statement: ' . $conn->error;
        debug_log($error);
        throw new Exception('Failed to prepare database statement');
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    $error_message = $e->getMessage();
    debug_log("Exception: " . $error_message);
    $response = [
        'success' => false,
        'message' => $error_message
    ];
} finally {
    // Log the response being sent back
    debug_log("Sending response: " . json_encode($response));
    
    // Close database connection if it exists
    if (isset($conn)) {
        // Log database status before closing
        if (isset($stmt)) {
            $stmt->close();
        }
        $conn->close();
    }
    
    debug_log('=== End of Request ===' . PHP_EOL);
}

// Output the response
echo json_encode($response);
?>
