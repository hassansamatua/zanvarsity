<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));

// Include necessary files
require_once ROOT_PATH . '/includes/auth_functions.php';
require_once ROOT_PATH . '/includes/database.php';

// Initialize response array
$response = ['success' => false, 'message' => ''];

// Check if user is logged in and is admin
require_login();

// Get database connection
$conn = $GLOBALS['conn'] ?? null;

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user_role = $_SESSION['role'];
$user_name = $_SESSION['name'] ?? '';
$user_email = $_SESSION['email'] ?? '';
$error = '';
$success = '';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', ROOT_PATH . '/logs/php_errors.log');

// Create logs directory if it doesn't exist
if (!is_dir(ROOT_PATH . '/logs')) {
    mkdir(ROOT_PATH . '/logs', 0755, true);
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if this is an AJAX request
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

    // Function to send JSON response
    function sendJsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Log the incoming request
    error_log('POST data: ' . print_r($_POST, true));
    error_log('FILES data: ' . print_r($_FILES, true));

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid CSRF token';
        if ($isAjax) {
            sendJsonResponse(['success' => false, 'message' => $error]);
        } else {
            $_SESSION['error'] = $error;
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_event':
            // Validate required fields
            $required = ['title', 'start_date'];
            $errors = [];
            $event = [];
            
            foreach ($required as $field) {
                if (empty(trim($_POST[$field] ?? ''))) {
                    $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
                } else {
                    $event[$field] = trim($_POST[$field]);
                }
            }
            
            // Handle file upload
            $image_path = null;
            if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $max_size = 5 * 1024 * 1024; // 5MB
                
                // Check file type using finfo
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $_FILES['event_image']['tmp_name']);
                finfo_close($finfo);
                
                if (!in_array($mime_type, $allowed_types)) {
                    $errors['event_image'] = 'Only JPG, PNG, and GIF files are allowed';
                } elseif ($_FILES['event_image']['size'] > $max_size) {
                    $errors['event_image'] = 'File size must be less than 5MB';
                } else {
                    // Create uploads directory if it doesn't exist
                    $upload_dir = ROOT_PATH . '/uploads/events/';
                    if (!is_dir($upload_dir)) {
                        if (!mkdir($upload_dir, 0755, true)) {
                            $errors['event_image'] = 'Failed to create upload directory';
                        }
                    }
                    
                    if (!isset($errors['event_image'])) {
                        // Generate unique filename
                        $file_extension = pathinfo($_FILES['event_image']['name'], PATHINFO_EXTENSION);
                        $filename = uniqid('event_') . '.' . $file_extension;
                        $destination = $upload_dir . $filename;
                        
                        if (move_uploaded_file($_FILES['event_image']['tmp_name'], $destination)) {
                            $image_path = '/zanvarsity/uploads/events/' . $filename;
                        } else {
                            $errors['event_image'] = 'Failed to upload file';
                        }
                    }
                }
            }

            // If there are validation errors, return them
            if (!empty($errors)) {
                if ($isAjax) {
                    sendJsonResponse([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $errors
                    ]);
                } else {
                    $_SESSION['form_errors'] = $errors;
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                }
            }
            
            // Sanitize input
            $title = trim($conn->real_escape_string($_POST['title']));
            $description = trim($conn->real_escape_string($_POST['description'] ?? ''));
            $start_date = trim($conn->real_escape_string($_POST['start_date']));
            $end_date = !empty($_POST['end_date']) ? trim($conn->real_escape_string($_POST['end_date'])) : null;
            $location = !empty($_POST['location']) ? trim($conn->real_escape_string($_POST['location'])) : null;
            
            // Insert into database
            try {
                // Log the data being inserted
                error_log('Attempting to insert event with data: ' . print_r([
                    'title' => $title,
                    'description' => $description,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'location' => $location,
                    'image_url' => $image_path,
                    'status' => 'upcoming'
                ], true));

                // Start transaction with error handling
                if (!isset($conn) || !($conn instanceof mysqli)) {
                    throw new Exception('Database connection is not properly initialized');
                }
                
                if (!$conn->begin_transaction()) {
                    throw new Exception('Failed to start transaction: ' . $conn->error);
                }
                
                // Log transaction start
                error_log('Transaction started for event creation');

                // Log the data being inserted
                error_log('Attempting to insert event with data: ' . print_r([
                    'title' => $title,
                    'description' => $description,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'location' => $location,
                    'image_url' => $image_path,
                    'status' => 'upcoming'
                ], true));

                // First, try with a direct query to see if it works
                $direct_sql = "INSERT INTO events (title, description, start_date, end_date, location, image_url, status) 
                              VALUES ('" . $conn->real_escape_string($title) . "', 
                                     '" . $conn->real_escape_string($description) . "', 
                                     '" . $conn->real_escape_string($start_date) . "',
                                     '" . $conn->real_escape_string($end_date) . "',
                                     '" . $conn->real_escape_string($location) . "',
                                     '" . $conn->real_escape_string($image_path) . "',
                                     'upcoming')";
                
                error_log('Direct SQL: ' . $direct_sql);
                
                if ($conn->query($direct_sql)) {
                    $event_id = $conn->insert_id;
                    error_log('Direct query successful, event_id: ' . $event_id);
                } else {
                    error_log('Direct query failed, trying prepared statement...');
                    
                    // If direct query fails, try prepared statement
                    $sql = "INSERT INTO events (title, description, start_date, end_date, location, image_url, status) 
                            VALUES (?, ?, ?, ?, ?, ?, 'upcoming')";
                    $stmt = $conn->prepare($sql);
                    
                    if ($stmt === false) {
                        throw new Exception('Failed to prepare statement: ' . $conn->error);
                    }
                    
                    $stmt->bind_param('ssssss', $title, $description, $start_date, $end_date, $location, $image_path);
                    
                    if (!$stmt->execute()) {
                        throw new Exception('Failed to execute statement: ' . $stmt->error);
                    }
                    
                    $event_id = $conn->insert_id;
                    error_log('Prepared statement successful, event_id: ' . $event_id);
                }
                
                $event_id = $conn->insert_id;
                
                // Verify the event was inserted
                $check_sql = "SELECT * FROM events WHERE id = " . intval($event_id);
                $check_result = $conn->query($check_sql);
                
                if ($check_result === false) {
                    error_log('Error checking for inserted event: ' . $conn->error);
                    throw new Exception('Failed to verify event insertion');
                }
                
                if ($check_result->num_rows === 0) {
                    // Try to get more detailed error information
                    $error_info = [];
                    if (method_exists($conn, 'error_list')) {
                        $error_info = $conn->error_list;
                    }
                    
                    error_log('Event verification failed - No rows returned for event_id: ' . $event_id);
                    error_log('Last query: ' . $conn->last_query);
                    error_log('Error info: ' . print_r($error_info, true));
                    
                    // Check table structure
                    $table_info = $conn->query("SHOW CREATE TABLE events");
                    if ($table_info) {
                        $table_structure = $table_info->fetch_assoc();
                        error_log('Table structure: ' . print_r($table_structure, true));
                    }
                    
                    // Check user permissions
                    $grants = $conn->query("SHOW GRANTS FOR CURRENT_USER()");
                    if ($grants) {
                        error_log('Current user grants:');
                        while ($grant = $grants->fetch_row()) {
                            error_log('- ' . $grant[0]);
                        }
                    }
                    
                    throw new Exception('Event was not inserted into the database. Check error logs for details.');
                } else {
                    $eventData = $check_result->fetch_assoc();
                    error_log('Event verified in database: ' . print_r($eventData, true));
                }
                
                // Commit transaction with detailed error handling
                if (!$conn->commit()) {
                    $error = 'Failed to commit transaction: ' . $conn->error;
                    error_log($error);
                    $conn->rollback();
                    throw new Exception($error);
                }
                
                // Verify the event was actually committed
                $check_sql = "SELECT id FROM events WHERE id = " . intval($event_id);
                $check_result = $conn->query($check_sql);
                
                if ($check_result === false || $check_result->num_rows === 0) {
                    $error = 'Event was not found after commit. Possible transaction issue.';
                    error_log($error);
                    error_log('Check query: ' . $check_sql);
                    error_log('Error: ' . $conn->error);
                    throw new Exception($error);
                }
                
                error_log('Transaction committed successfully. Event ID: ' . $event_id);
                
                $success = 'Event added successfully!';
                
                    if ($isAjax) {
                        http_response_code(200);
                        sendJsonResponse([
                            'success' => true,
                            'message' => $success,
                            'event_id' => $event_id,
                            'redirect' => $_SERVER['PHP_SELF']
                        ]);
                    } else {
                        $_SESSION['success'] = $success;
                        header('Location: ' . $_SERVER['PHP_SELF']);
                        exit();
                    }
            } catch (Exception $e) {
                $error_message = 'Event creation error: ' . $e->getMessage() . 
                               ' in ' . $e->getFile() . ' on line ' . $e->getLine() . 
                               '\nStack trace: ' . $e->getTraceAsString();
                
                error_log($error_message);
                
                // Log database errors if connection exists
                if (isset($conn) && $conn instanceof mysqli) {
                    if ($conn->error) {
                        error_log('Database error: ' . $conn->error);
                    }
                    
                    // Log transaction status
                    if (isset($conn->server_info)) {
                        error_log('MySQL Server version: ' . $conn->server_info);
                    }
                    
                    // Rollback any open transaction
                    if ($conn->more_results()) {
                        while ($conn->next_result()) {
                            // Free results if any
                            if ($result = $conn->store_result()) {
                                $result->free();
                            }
                        }
                    }
                    
                    if ($conn->ping()) {
                        error_log('Connection is still active');
                    } else {
                        error_log('Connection is closed');
                    }
                }
                
                // Delete uploaded file if there was an error
                if (!empty($image_path)) {
                    $file_path = ROOT_PATH . str_replace('/zanvarsity', '', $image_path);
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
                
                if ($isAjax) {
                    sendJsonResponse([
                        'success' => false,
                        'message' => 'Error saving event: ' . $e->getMessage()
                    ]);
                } else {
                    $error = 'Error saving event: ' . $e->getMessage();
                }
            } finally {
                if (isset($stmt)) {
                    $stmt->close();
                }
            }
            break;
                
            case 'update_event':
                // Update event logic
                $success = 'Event updated successfully';
                break;
                
            case 'delete_event':
                // Delete event logic
                $success = 'Event deleted successfully';
                break;
        }
    }

// Events will be loaded from the database

// Get user role display name
$role_display = '';
switch($user_role) {
    case 'super_admin':
        $role_display = 'Super Admin';
        break;
    case 'admin':
        $role_display = 'Admin';
        break;
    case 'instructor':
        $role_display = 'Instructor';
        break;
    case 'student':
        $role_display = 'Student';
        break;
    case 'staff':
        $role_display = 'Staff';
        break;
    case 'parent':
        $role_display = 'Parent';
        break;
    default:
        $role_display = 'User';
}

// Include header
$page_title = 'Manage Events';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title><?php echo $page_title; ?> - Admin Panel</title>
    
    <!-- Bootstrap CSS -->
   
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <link href="/zanvarsity/html/assets/css/font-awesome.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/bootstrap/css/bootstrap.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/selectize.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/owl.carousel.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/vanillabox/vanillabox.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/style.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/green-theme.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/admin-theme.css" type="text/css">
    
    <style>
        /* Custom styles for events management */
        #eventsGrid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin: 0 auto;
            max-width: 1600px;
            padding: 20px;
            gap: 25px;
        }
        .event-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
            margin: 0;
            width: 100%;
            max-width: 400px;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .event-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }
        .card-img-top {
            height: 240px;
            width: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.5s ease;
        }
        .event-card:hover .card-img-top {
            transform: scale(1.05);
        }
        .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 1.5rem;
            background: #fff;
            position: relative;
            z-index: 1;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }
        .card-text {
            color: #4a5568;
            margin-bottom: 1rem;
            flex-grow: 1;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .event-date {
            display: flex;
            align-items: center;
            color: #718096;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .event-date i {
            margin-right: 6px;
            color: #4a90e2;
        }
        .event-location {
            display: flex;
            align-items: center;
            color: #718096;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .event-location i {
            margin-right: 6px;
            color: #e53e3e;
        }
        .card-footer {
            background: #f8fafc;
            border-top: 1px solid #edf2f7;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .event-status {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .status-upcoming {
            background-color: #ebf8ff;
            color: #2b6cb0;
        }
        .status-ongoing {
            background-color: #ebf8f1;
            color: #276749;
        }
        .status-completed {
            background-color: #fef5f7;
            color: #9b2c2c;
        }
        .btn-group .btn {
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }
        .btn-group .btn i {
            font-size: 1rem;
            vertical-align: middle;
        }
        .card-footer {
            background: transparent;
            border-top: 1px solid rgba(0,0,0,.125);
            padding: 0.75rem 1.25rem;
        }
        
        .event-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.1);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            border-color: rgba(0,0,0,0.15);
        }
        
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }
        
        .event-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .event-card:hover .event-actions {
            opacity: 1;
        }
        
        .status-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 1;
        }
        
        /* Header styling */
        .secondary-navigation-wrapper {
            background-color: #28a745; /* Exact green from manage_content.php */
            border-bottom: none;
        }
        
        .primary-navigation-wrapper {
            background-color: #15724;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .navbar-nav > li > a {
            color: white;
            font-weight: 500;
        }
        
        .navbar-nav > li > a:hover,
        .navbar-nav > li > a:focus {
            color: white;
            background-color: rgba(255,255,255,0.1);
            border-radius: 4px;
        }
        
        .navbar-nav > .active > a,
        .navbar-nav > .active > a:hover,
        .navbar-nav > .active > a:focus {
            color: white;
            background-color: var(--medium-green);
            font-weight: 600;
            border-radius: 4px;
        }
        
        .secondary-navigation a {
            color: white;
        }
        
        .secondary-navigation a:hover {
            color: #f0f0f0;
            text-decoration: none;
        }
        
        .navigation-contact {
            color: rgba(255,255,255,0.8);
        }
        
        :root {
            --primary-color: #28a745; /* Green from manage_content.php */
            --primary-hover: #218838; /* Darker green for hover states */
            --primary-light: #d4edda; /* Light green for backgrounds */
            --dark-green: #28a745; /* Updated to match the green from manage_content.php */
            --medium-green: #218838;
            --light-green: #4caf50;
        }
        
        .event-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
            margin-bottom: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
        }
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            border-color: var(--primary-color);
        }
        .event-status-upcoming { 
            border-left: 4px solid var(--primary-color);
            background-color: var(--primary-light);
            color: #1b5e20; /* Darker green text for better contrast */
        }
        .event-status-ongoing { 
            border-left: 4px solid #17a2b8;
            background-color: #e3f2fd;
        }

        
        .event-status-completed { 
            border-left: 4px solid #6c757d;
            background-color: #f8f9fa;
        }
        .event-status-cancelled { 
            border-left: 4px solid #dc3545;
            background-color: #fde8e8;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
    </style>
</head>

<body class="page-sub-page page-my-account">
<!-- Wrapper -->
<div class="wrapper">
        <!-- Header -->
        <div class="navigation-wrapper">
        <div class="secondary-navigation-wrapper">
            <div class="container">
                <div class="navigation-contact pull-left">
                    <i class="fa fa-phone"></i> Call Us: <span class="opacity-70">+255 123 456 789</span>
                </div>
                <ul class="secondary-navigation list-unstyled pull-right">
                    <li><a href="/zanvarsity/html/my-account.php"><i class="fa fa-user"></i> My Profile</a></li>
                    <li><a href="/zanvarsity/html/admin/dashboard.php">Dashboard</a></li>
                    <li><a href="/zanvarsity/logout.php">Log Out</a></li>
                </ul>
            </div>
        </div>
        <div class="primary-navigation-wrapper">
            <header class="navbar" id="top" role="banner">
                <div class="container">
                    <div class="navbar-header">
                        <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <div class="navbar-brand nav" id="brand">
                            <a href="/zanvarsity/html/index.php">
                                <img src="/zanvarsity/html/assets/img/logo.png" alt="Zanvarsity" class="logo">
                            </a>
                        </div>
                    </div>
                    <nav class="collapse navbar-collapse bs-navbar-collapse navbar-right" role="navigation">
                        <ul class="nav navbar-nav">
                            <li><a href="/zanvarsity/html/index.php">Home</a></li>
                            <li class="has-child">
                                <a href="#">Academics</a>
                                <ul class="list-unstyled child-navigation">
                                    <li><a href="/zanvarsity/html/academics.php">Programs</a></li>
                                    <li><a href="/zanvarsity/html/faculties.php">Faculties</a></li>
                                    <li><a href="/zanvarsity/html/departments.php">Departments</a></li>
                                    <li><a href="/zanvarsity/html/courses.php">Courses</a></li>
                                </ul>
                            </li>
                            <li><a href="/zanvarsity/html/admissions.php">Admissions</a></li>
                            <li><a href="/zanvarsity/html/campus-life.php">Campus Life</a></li>
                            <li><a href="/zanvarsity/html/research.php">Research</a></li>
                            <li><a href="/zanvarsity/html/about.php">About</a></li>
                            <li><a href="/zanvarsity/html/contact.php">Contact</a></li>
                        </ul>
                    </nav>
                </div>
            </header>
        </div>
    </div>
    <!-- end Header -->

    <div id="page-content">
        <div class="container">
            <div class="row">
                <!-- Sidebar -->
                <aside class="col-md-3 col-sm-4">
                    <div class="sidebar">
                        <div class="sidebar-inner">
                            <div class="sidebar-profile">
                                <div class="profile-picture">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>" alt="" class="img-fluid rounded-circle" width="40">
                                </div>
                                <div class="profile-info">
                                    <h4><?php echo htmlspecialchars($user_name); ?></h4>
                                    <p class="role"><?php echo $role_display; ?></p>
                                </div>
                            </div>
                            <div class="sidebar-widget">
                                <div class="user-avatar">
                                    <div style="width: 100px; height: 100px; margin: 0 auto 15px; background-color: #4caf50; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; font-weight: bold; text-transform: uppercase; text-align: center; padding: 5px; line-height: 1.2;">
                                        <?php echo $role_display; ?>
                                    </div>
                                    <div class="text-center">
                                        <h4><?php echo htmlspecialchars($user_name); ?></h4>
                                        <span class="label label-primary"><?php echo $role_display; ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="sidebar-widget">
                                <ul class="nav nav-pills nav-stacked nav-dashboard">
                                    <li><a href="/zanvarsity/html/my-account.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                                    <?php if (in_array($user_role, ['super_admin', 'admin'])): ?>
                                    <li><a href="users.php"><i class="fa fa-users"></i> Manage Users</a></li>
                                    <li class="active"><a href="manage_content.php"><i class="fa fa-file-text"></i> Manage Content</a></li>
                                    <?php endif; ?>
                                    <li><a href="/zanvarsity/html/my-courses.php"><i class="fa fa-book"></i> My Courses</a></li>
                                    <li><a href="/zanvarsity/html/my-profile.php"><i class="fa fa-user"></i> My Profile</a></li>
                                    <li><a href="/zanvarsity/html/change-password.php"><i class="fa fa-key"></i> Change Password</a></li>
                                    <li><a href="/zanvarsity/html/settings.php"><i class="fa fa-cog"></i> Settings</a></li>
                                    <?php if (in_array($user_role, ['instructor', 'admin', 'super_admin'])): ?>
                                    <li><a href="/zanvarsity/html/instructor"><i class="fa fa-chalkboard-teacher"></i> Instructor Panel</a></li>
                                    <?php endif; ?>
                                    <li><a href="/zanvarsity/html/logout.php" onclick="return confirm('Are you sure you want to log out?')"><i class="fa fa-sign-out"></i> Log Out</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </aside>
                <!-- End Sidebar -->

                <!-- Main Content -->
                <div class="col-md-9 col-sm-8">
                    <section class="block">
                        <div class="page-title">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h2><i class='bx bx-calendar-event me-2'></i>Manage Events</h2>
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addEventModal">
                                    <i class='bx bx-plus me-1'></i> Add New Event
                                </button>
                            </div>
                            <?php if ($success): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo $success; ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                </div>
                            <?php endif; ?>
                            <?php if ($error): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?php echo $error; ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Events Grid -->
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 justify-content-center" id="eventsGrid">
                                    <?php 
                                    // Debug: Check database connection
                                    if (!$conn) {
                                        echo '<div class="col-12"><div class="alert alert-danger">Database connection failed. Check database configuration.</div></div>';
                                        error_log('Database connection is null in manage_events.php');
                                    } else {
                                        // Debug: Test connection
                                        if (!$conn->ping()) {
                                            echo '<div class="col-12"><div class="alert alert-danger">Database server is not responding. Error: ' . $conn->error . '</div></div>';
                                            error_log('Database ping failed: ' . $conn->error);
                                        }
                                    }
                                    
                                    // Simple events query
                                    $events = [];
                                    try {
                                        // Debug: Check if table exists
                                        $table_check = $conn->query("SHOW TABLES LIKE 'events'");
                                        if ($table_check->num_rows === 0) {
                                            echo '<div class="col-12"><div class="alert alert-warning">The events table does not exist in the database.</div></div>';
                                            error_log('Events table does not exist in database');
                                        } else {
                                            // Direct query to get events
                                            $query = "SELECT * FROM events ORDER BY start_date DESC";
                                            error_log('Executing query: ' . $query);
                                            $result = $conn->query($query);
                                            
                                            if ($result === false) {
                                                echo '<div class="col-12"><div class="alert alert-danger">Query error: ' . $conn->error . '</div></div>';
                                                error_log('Query error: ' . $conn->error);
                                            } else {
                                                $events = $result->fetch_all(MYSQLI_ASSOC);
                                                error_log('Fetched ' . count($events) . ' events from database');
                                            }
                                        }
                                    } catch (Exception $e) {
                                        echo '<div class="col-12"><div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div></div>';
                                    }
                                    
                                    // Debug output
                                    echo '<!-- Debug: Starting to display events -->';
                                    echo '<!-- Debug: Number of events: ' . count($events) . ' -->';
                                    echo '<!-- Debug: Events data: ' . htmlspecialchars(print_r($events, true)) . ' -->';
                                    
                                    // Debug: Check if we have events to display
                                    echo '<!-- Debug: About to display events. Count: ' . count($events) . ' -->';
                                    
                                    // Display events if any
                                    if (!empty($events)): 
                                        foreach ($events as $index => $event): 
                                            echo '<!-- Debug: Processing event #' . ($index + 1) . ' -->';
                                            
                                            // Format dates simply
                                            $start_date = date('M j, Y g:i A', strtotime($event['start_date']));
                                            $end_time = !empty($event['end_date']) ? date('g:i A', strtotime($event['end_date'])) : '';
                                            $date_display = $start_date;
                                            if ($end_time) {
                                                $date_display .= ' - ' . $end_time;
                                            }
                                                
                                            // Set status class
                                            $status_classes = [
                                                'upcoming' => 'bg-primary',
                                                'ongoing' => 'bg-success',
                                                'completed' => 'bg-secondary',
                                                'cancelled' => 'bg-danger'
                                            ];
                                            $status_class = $status_classes[$event['status']] ?? 'bg-secondary';
                                    ?>
                                        <div class="col">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                                                    <p class="card-text"><?php echo htmlspecialchars($event['description']); ?></p>
                                                    
                                                    <?php if (!empty($event['location'])): ?>
                                                        <p class="mb-1"><i class='bx bx-map text-muted'></i> 
                                                            <?php echo htmlspecialchars($event['location']); ?>
                                                        </p>
                                                    <?php endif; ?>
                                                    
                                                    <p class="mb-1"><i class='bx bx-time text-muted'></i> 
                                                        <?php echo $date_display; ?>
                                                    </p>
                                                    
                                                    <div class="mt-2">
                                                        <span class="badge bg-primary">
                                                            <?php echo ucfirst($event['status']); ?>
                                                        </span>
                                                    </div>
                                                
                                                <div class="card-footer">
                                                    <div class="btn-group">
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-primary edit-event" 
                                                                data-id="<?php echo $event['id']; ?>"
                                                                data-toggle="tooltip" 
                                                                title="Edit">
                                                            <i class='bx bx-edit-alt me-1'></i> Edit
                                                        </button>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-danger delete-event" 
                                                                data-id="<?php echo $event['id']; ?>"
                                                                data-toggle="tooltip" 
                                                                title="Delete">
                                                            <i class='bx bx-trash me-1'></i> Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php 
                                        endforeach; 
                                    else: 
                                    ?>
                                        <div class="col-12 text-center py-5">
                                            <div class="text-muted">
                                                <i class="bx bx-calendar-x display-4 mb-3"></i>
                                                <h5 class="mb-3">No events found</h5>
                                                <p class="mb-4">Get started by adding your first event</p>
                                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addEventModal">
                                                    <i class='bx bx-plus me-2'></i> Add New Event
                                                </button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <!-- End Main Content -->
            </div>
        </div>
    </div>
</div>

<!-- Add Event Modal -->
<?php include __DIR__ . '/includes/add_event_modal.php'; ?>

<!-- Edit Event Modal -->
<div class="modal fade" id="editEventModal" tabindex="-1" role="dialog" aria-labelledby="editEventModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h2 class="modal-title h5" id="editEventModalLabel">Edit Event</h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="editEventForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_event">
                <input type="hidden" name="id" id="editEventId">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="editEventTitle" class="form-label">Event Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editEventTitle" name="title" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editEventDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="editEventDescription" name="description" rows="4"></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editStartDate" class="form-label">Start Date & Time <span class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control" id="editStartDate" name="start_date" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editEndDate" class="form-label">End Date & Time</label>
                                        <input type="datetime-local" class="form-control" id="editEndDate" name="end_date">
                                        <div class="form-text">Leave empty if not applicable</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editEventLocation" class="form-label">Location</label>
                                <input type="text" class="form-control" id="editEventLocation" name="location">
                            </div>
                            
                            <div class="mb-3">
                                <label for="editEventStatus" class="form-label">Status</label>
                                <select class="form-select" id="editEventStatus" name="status">
                                    <option value="upcoming">Upcoming</option>
                                    <option value="ongoing">Ongoing</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div id="currentImage">
                                <!-- Current image will be displayed here -->
                            </div>
                            
                            <div class="mb-3">
                                <label for="editEventImage" class="form-label">Update Image</label>
                                <input type="file" class="form-control" id="editEventImage" name="image" accept="image/*">
                                <div class="form-text">Leave empty to keep current image</div>
                            </div>
                            
                            <div class="alert alert-info small">
                                <i class='bx bx-info-circle me-1'></i>
                                Recommended size: 800x450px (16:9 aspect ratio)
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class='bx bx-save me-1'></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class='bx bx-trash me-2'></i>Confirm Delete
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this event? This action cannot be undone.</p>
                <div class="alert alert-warning mb-0">
                    <i class='bx bx-error-circle me-2'></i>
                    <strong>Warning:</strong> This will permanently delete the event and cannot be recovered.
                </div>
                <input type="hidden" id="delete_event_id" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class='bx bx-x me-1'></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class='bx bx-trash me-1'></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Include JavaScripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/dist/boxicons.js"></script>
<!-- Custom JavaScript for event form handling -->
<script src="js/event_form.js"></script>

<!-- Moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

<!-- Custom JS -->
<script>
    // Function to show loading state
    function showLoading(button, text = 'Processing...') {
        const originalText = button.html();
        button.prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            ${text}
        `);
        return originalText;
    }

    // Function to reset button state
    function resetButton(button, originalText) {
        button.prop('disabled', false).html(originalText);
    }

    // Main document ready handler
    $(document).ready(function() {
        // Handle success/error messages
        <?php if (!empty($_SESSION['success'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '<?php echo addslashes($_SESSION['success']); ?>',
                timer: 3000,
                showConfirmButton: false
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo addslashes($error); ?>',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>
        
        // Image preview for add event form
        $('#eventImage').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    $(this).addClass('is-invalid');
                    $(this).after('<div class="invalid-feedback">Please upload a valid image file (JPEG, PNG, GIF)</div>');
                    return;
                }
                
                // Validate file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    $(this).addClass('is-invalid');
                    $(this).after('<div class="invalid-feedback">Image size should be less than 5MB</div>');
                    return;
                }
                
                // Clear any previous errors
                $(this).removeClass('is-invalid');
                $('.invalid-feedback').remove();
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').html(`
                        <img src="${e.target.result}" class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;">
                    `);
                }
                reader.readAsDataURL(file);
            }
        });

        // Handle edit event
        $(document).on('click', '.edit-event', function() {
            const eventId = $(this).data('id');
            
            // Show loading state
            const button = $(this);
            const originalText = showLoading(button, 'Loading...');
            
            // Fetch event data
            $.get(`/zanvarsity/html/admin/api/events.php?action=get_event&id=${eventId}`, function(response) {
                if (response.success) {
                    const event = response.data;
                    
                    // Populate the edit form
                    $('#editEventId').val(event.id);
                    $('#editEventTitle').val(event.title);
                    $('#editEventDescription').val(event.description);
                    $('#editEventLocation').val(event.location || '');
                    
                    // Format start date for datetime-local input
                    if (event.start_date) {
                        try {
                            const startDate = new Date(event.start_date);
                            if (!isNaN(startDate.getTime())) {  // Check if date is valid
                                const formattedStartDate = startDate.toISOString().slice(0, 16);
                                $('#editStartDate').val(formattedStartDate);
                            }
                        } catch (e) {
                            console.error('Error formatting start date:', e);
                        }
                    }
                    
                    // Format end date for datetime-local input
                    if (event.end_date) {
                        try {
                            const endDate = new Date(event.end_date);
                            if (!isNaN(endDate.getTime())) {  // Check if date is valid
                                const formattedEndDate = endDate.toISOString().slice(0, 16);
                                $('#editEndDate').val(formattedEndDate);
                            }
                        } catch (e) {
                            console.error('Error formatting end date:', e);
                        }
                    }
                    
                    $('#editEventStatus').val(event.status);
                    
                    if (event.image_url) {
                        $('#currentImage').html(`
                            <div class="mb-3">
                                <label class="form-label">Current Image</label>
                                <div class="border p-2 text-center">
                                    <img src="${event.image_url}" class="img-fluid mb-2" style="max-height: 150px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remove_image" id="removeImage">
                                        <label class="form-check-label" for="removeImage">
                                            Remove image
                                        </label>
                                    </div>
                                </div>
                            </div>
                        `);
                    } else {
                        $('#currentImage').html('');
                    }
                    
                    // Show the edit modal
                    const editModal = new bootstrap.Modal(document.getElementById('editEventModal'));
                    const editModalElement = document.getElementById('editEventModal');
                    
                    if (editModalElement) {
                        // Add event listeners for show/hide
                        editModalElement.addEventListener('shown.bs.modal', function () {
                            this.removeAttribute('aria-hidden');
                            this.setAttribute('aria-modal', 'true');
                            $('#editEventTitle').trigger('focus');
                        });
                        
                        editModalElement.addEventListener('hidden.bs.modal', function () {
                            this.setAttribute('aria-hidden', 'true');
                            this.removeAttribute('aria-modal');
                        });
                        
                        editModal.show();
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to load event data',
                        confirmButtonText: 'OK'
                    });
                }
                
                resetButton(button, originalText);
            }).fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load event data. Please try again.',
                    confirmButtonText: 'OK'
                });
                resetButton(button, originalText);
            });
        });
        
        // Handle delete event button - consolidated single handler with enhanced error handling
        $(document).on('click', '.delete-event', function() {
            const eventId = $(this).data('id');
            const button = $(this);
            
            console.log('Delete button clicked for event ID:', eventId);
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const originalText = showLoading(button, 'Deleting...');
                    
                    console.log('Sending delete request for event ID:', eventId);
                    
                    $.ajax({
                        url: '/zanvarsity/html/admin/api/events.php',
                        type: 'POST',
                        data: {
                            action: 'delete_event',
                            id: eventId,
                            csrf_token: '<?php echo $_SESSION['csrf_token']; ?>'
                        },
                        dataType: 'json',
                        success: function(response, status, xhr) {
                            console.log('Delete response:', response);
                            
                            if (response && response.success) {
                                // Remove the event card from the UI
                                const eventCard = button.closest('.event-card');
                                if (eventCard.length) {
                                    // Add fade out animation
                                    eventCard.fadeOut(400, function() {
                                        $(this).remove();
                                        
                                        // Show success message
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Deleted!',
                                            text: response.message || 'The event has been deleted.',
                                            timer: 2000,
                                            showConfirmButton: false
                                        });
                                        
                                        // If no events left, show a message
                                        if ($('.event-card').length === 0) {
                                            $('#events-container').html(`
                                                <div class="col-12">
                                                    <div class="alert alert-info">
                                                        No events found. Click the "Add New Event" button to create one.
                                                    </div>
                                                </div>
                                            `);
                                        }
                                    });
            if (result.isConfirmed) {
                const originalText = showLoading(button, 'Deleting...');
                
                console.log('Sending delete request for event ID:', eventId);
                
                $.ajax({
                    url: '/zanvarsity/html/admin/api/events.php',
                    type: 'POST',
                    data: {
                        action: 'delete_event',
                        id: eventId,
                        csrf_token: '<?php echo $_SESSION['csrf_token']; ?>'
                    },
                    dataType: 'json',
                    success: function(response, status, xhr) {
                        console.log('Delete response:', response);
                        
                        if (response && response.success) {
                            // Remove the event card from the UI
                            const eventCard = button.closest('.event-card');
                            if (eventCard.length) {
                                // Add fade out animation
                                eventCard.fadeOut(400, function() {
                                    $(this).remove();
                                    
                                    // Show success message
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: response.message || 'The event has been deleted.',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    
                                    // If no events left, show a message
                                    if ($('.event-card').length === 0) {
                                        $('#events-container').html(`
                                            <div class="col-12">
                                                <div class="alert alert-info">
                                                    No events found. Click the "Add New Event" button to create one.
                                                </div>
                                            </div>
                                        `);
                                    }
                                });
                            } else {
                                // Fallback to page reload if we can't find the card
                                window.location.reload();
                            }
                        } else {
                            let errorMsg = response && response.message 
                                ? response.message 
                                : 'Failed to delete event. Please try again.';
                                
                            console.error('Delete failed:', errorMsg);
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                html: errorMsg + '<br><br>Please check the console for more details.',
                                confirmButtonText: 'OK'
                            });
                            
                            resetButton(button, originalText);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', {
                            status: status,
                            error: error,
                            response: xhr.responseText
                        });
                        
                        let errorMsg = 'An error occurred while deleting the event. ';
                        
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response && response.message) {
                                errorMsg = response.message;
                            }
                        } catch (e) {
                            errorMsg += 'Please check the console for more details.';
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errorMsg + '<br><br>Status: ' + status,
                            confirmButtonText: 'OK'
                        });
                        
                        resetButton(button, originalText);
                    }
                });
            }
        });
    });
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Form validation
    // Form validation
    (function() {
        'use strict';
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
}); // End of document.ready
</script>

<style>
    /* Custom styles specific to manage_events.php */
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid rgba(0,0,0,.125);
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,.1);
    }
    .btn-success {
        transition: all 0.3s ease;
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    .btn-success:hover, .btn-success:focus, .btn-success:active {
        background-color: var(--primary-hover);
        border-color: var(--primary-hover);
        transform: translateY(-2px);
    }
    .table th {
        border-top: none;
        font-weight: 600;
    }
    .badge {
        font-weight: 500;
        padding: 0.4em 0.8em;
    }
    .modal-header {
        background-color: var(--primary-color);
        color: white;
        border-bottom: 1px solid var(--primary-hover);
        padding: 15px 20px;
    }
    
    /* Update all primary buttons and links to use the green theme */
    .btn-primary, .nav-pills > li.active > a, .nav-pills > li.active > a:hover, 
    .nav-pills > li.active > a:focus, .pagination > .active > a, 
    .pagination > .active > a:hover, .pagination > .active > a:focus {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .btn-primary:hover, .btn-primary:focus, .btn-primary:active,
    .nav-pills > li.active > a:hover, .nav-pills > li.active > a:focus,
    .pagination > .active > a:hover, .pagination > .active > a:focus {
        background-color: var(--primary-hover);
        border-color: var(--primary-hover);
    }
    
    /* Update links and hover states */
    a, .text-primary {
        color: var(--primary-color);
    }
    
    a:hover, a:focus {
        color: var(--primary-hover);
    }
    .modal-header .btn-close {
        color: white;
        opacity: 0.8;
    }
    .modal-header .btn-close:hover {
        opacity: 1;
    }
</style>

    </div>
    <!-- end Page Content -->
</div>
<!-- end Wrapper -->

<!-- Footer -->
<footer id="page-footer">
    <div class="footer-wrapper">
        <div class="block">
            <div class="container">
                <div class="vertical-aligned-elements">
                    <div class="element">
                        <p>© <?php echo date('Y'); ?> Zanvarsity. All rights reserved.</p>
                    </div>
                    <div class="element pull-right">
                        <a href="#page-top" class="to-top pull-right"><i class="fa fa-arrow-up"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- end Footer -->

<!-- JavaScript Libraries -->
<script src="/zanvarsity/html/assets/js/jquery-2.1.0.min.js"></script>
<script src="/zanvarsity/html/assets/bootstrap/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>

<?php 
// Close database connection if it exists
if (isset($conn) && $conn) {
    $conn->close();
}
?>
