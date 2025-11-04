<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'C:\\xampp\\htdocs\\c\\zanvarsity\\php_errors.log');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log the start of the script
error_log('=== Starting manage_events.php ===');
error_log('POST data: ' . print_r($_POST, true));
error_log('FILES data: ' . print_r($_FILES, true));

// Include database connection
require_once __DIR__ . '/../includes/database.php';

// Initialize variables
$conn = $GLOBALS['conn'] ?? null;

// Check if database connection is available
if (!$conn) {
    die("Database connection failed: " . ($conn->connect_error ?? 'Unknown error'));
}

// Define root path
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__DIR__)));
}

// Include necessary files
require_once ROOT_PATH . '/zanvarsity/includes/auth_functions.php';
require_once ROOT_PATH . '/zanvarsity/includes/database.php';

// Check if user is logged in and has admin/dean privileges
require_login();

// Get user information from session
$user_id = $_SESSION['user_id'] ?? null;
$user_email = $_SESSION['email'] ?? 'Guest';
$user_name = $_SESSION['first_name'] ?? 'User';
$user_role = $_SESSION['role'] ?? 'student';
$is_admin = in_array($user_role, ['admin', 'super_admin']);
$is_dean = ($user_role === 'dean');

if (!($is_admin || $is_dean)) {
    $_SESSION['error'] = "You don't have permission to access this page.";
    header("Location: index.php");
    exit();
}

// Set page title
$page_title = 'Manage Events | Zanvarsity';

// Include header
include 'includes/about_header.php';

// Include the events management code from admin/manage_events.php
// First, generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get database connection
$conn = $GLOBALS['conn'] ?? null;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    // Check if this is an AJAX request
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

    // Function to send JSON response
    function sendJsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

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
    
    // Log the incoming request for debugging
    error_log('POST data: ' . print_r($_POST, true));
    error_log('FILES data: ' . print_r($_FILES, true));
    
    switch ($action) {
        case 'add_event':
        case 'update_event':
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
                $allowed_types = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif'
                ];
                $max_size = 5 * 1024 * 1024; // 5MB
                
                // Check file type using finfo
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $_FILES['event_image']['tmp_name']);
                finfo_close($finfo);
                
                if (!array_key_exists($mime_type, $allowed_types)) {
                    $errors['event_image'] = 'Only JPG, PNG, and GIF files are allowed';
                } elseif ($_FILES['event_image']['size'] > $max_size) {
                    $errors['event_image'] = 'File size must be less than 5MB';
                } else {
                    // Create uploads directory if it doesn't exist
                    $upload_base = dirname(dirname(__DIR__)) . '/uploads/events/';
                    if (!is_dir($upload_base)) {
                        if (!mkdir($upload_base, 0777, true)) {
                            $errors['event_image'] = 'Failed to create upload directory';
                            error_log('Failed to create directory: ' . $upload_base);
                        } else {
                            // Set directory permissions
                            chmod($upload_base, 0777);
                        }
                    }
                    
                    if (!isset($errors['event_image']) && is_writable($upload_base)) {
                        // Generate unique filename with proper extension
                        $file_extension = $allowed_types[$mime_type];
                        $filename = 'event_' . uniqid() . '.' . $file_extension;
                        $destination = $upload_base . $filename;
                        
                        if (move_uploaded_file($_FILES['event_image']['tmp_name'], $destination)) {
                            // Set file permissions
                            chmod($destination, 0644);
                            $image_path = '/c/zanvarsity/uploads/events/' . $filename;
                            error_log('File uploaded successfully to: ' . $destination);
                        } else {
                            $upload_error = error_get_last();
                            $errors['event_image'] = 'Failed to upload file: ' . ($upload_error['message'] ?? 'Unknown error');
                            error_log('File upload failed: ' . print_r($upload_error, true));
                        }
                    } elseif (!is_writable($upload_base)) {
                        $errors['event_image'] = 'Upload directory is not writable';
                        error_log('Upload directory not writable: ' . $upload_base);
                    }
                }
            }

            // If there are validation errors, return them
            if (!empty($errors)) {
                error_log('Validation errors: ' . print_r($errors, true));
                if ($isAjax) {
                    sendJsonResponse([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $errors,
                        'debug' => [
                            'post' => $_POST,
                            'files' => $_FILES
                        ]
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
            $event_id = $_POST['event_id'] ?? 0;
            
            try {
                // Start transaction
                if (!isset($conn) || !($conn instanceof mysqli)) {
                    throw new Exception('Database connection is not properly initialized');
                }
                
                if (!$conn->begin_transaction()) {
                    throw new Exception('Failed to start transaction: ' . $conn->error);
                }
                
                if ($action === 'add_event' || $event_id === 0) {
                    // Add new event
                    $sql = "INSERT INTO events (title, description, start_date, end_date, location, image_url, status, created_at) 
                           VALUES (?, ?, ?, ?, ?, ?, 'upcoming', NOW())";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssss", $title, $description, $start_date, $end_date, $location, $image_path);
                    $success_message = 'Event added successfully';
                } else {
                    // Update existing event
                    if ($image_path) {
                        $sql = "UPDATE events SET title=?, description=?, start_date=?, end_date=?, location=?, image_url=?, updated_at=NOW() WHERE id=?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ssssssi", $title, $description, $start_date, $end_date, $location, $image_path, $event_id);
                    } else {
                        $sql = "UPDATE events SET title=?, description=?, start_date=?, end_date=?, location=?, updated_at=NOW() WHERE id=?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("sssssi", $title, $description, $start_date, $end_date, $location, $event_id);
                    }
                    $success_message = 'Event updated successfully';
                }
                
                if ($stmt->execute()) {
                    $last_id = $action === 'add_event' ? $conn->insert_id : $event_id;
                    $conn->commit();
                    
                    if ($isAjax) {
                        sendJsonResponse([
                            'success' => true, 
                            'message' => $success_message,
                            'event_id' => $last_id
                        ]);
                    } else {
                        $_SESSION['success'] = $success_message;
                        header('Location: ' . $_SERVER['PHP_SELF']);
                        exit();
                    }
                } else {
                    throw new Exception($stmt->error);
                }
            } catch (Exception $e) {
                // Rollback transaction on error
                if (isset($conn) && $conn instanceof mysqli) {
                    $conn->rollback();
                }
                
                $error_message = 'Event ' . ($action === 'add_event' ? 'creation' : 'update') . ' error: ' . 
                               $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine() . 
                               '\nStack trace: ' . $e->getTraceAsString();
                
                error_log($error_message);
                
                if ($isAjax) {
                    sendJsonResponse([
                        'success' => false, 
                        'message' => 'An error occurred while saving the event. Please try again.',
                        'error' => $e->getMessage()
                    ]);
                } else {
                    $_SESSION['error'] = 'An error occurred while saving the event. Please try again.';
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                }
            }
            break;
            
        case 'delete_event':
            // Handle delete event
            if (empty($_POST['id'])) {
                $error = 'Event ID is required';
                if ($isAjax) {
                    sendJsonResponse(['success' => false, 'message' => $error]);
                } else {
                    $_SESSION['error'] = $error;
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                }
            }
            
            $event_id = (int)$_POST['id'];
            
            try {
                // Start transaction
                if (!$conn->begin_transaction()) {
                    throw new Exception('Failed to start transaction: ' . $conn->error);
                }
                
                // First, get the image path to delete the file
                $stmt = $conn->prepare("SELECT image_url FROM events WHERE id = ?");
                $stmt->bind_param("i", $event_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($event = $result->fetch_assoc()) {
                    // Delete the event
                    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
                    $stmt->bind_param("i", $event_id);
                    
                    if ($stmt->execute()) {
                        // If event was deleted and had an image, try to delete the image file
                        if (!empty($event['image_url'])) {
                            $image_path = ROOT_PATH . $event['image_url'];
                            if (file_exists($image_path)) {
                                @unlink($image_path);
                            }
                        }
                        
                        $conn->commit();
                        
                        if ($isAjax) {
                            sendJsonResponse(['success' => true, 'message' => 'Event deleted successfully']);
                        } else {
                            $_SESSION['success'] = 'Event deleted successfully';
                            header('Location: ' . $_SERVER['PHP_SELF']);
                            exit();
                        }
                    } else {
                        throw new Exception($stmt->error);
                    }
                } else {
                    throw new Exception('Event not found');
                }
            } catch (Exception $e) {
                // Rollback transaction on error
                if (isset($conn) && $conn instanceof mysqli) {
                    $conn->rollback();
                }
                
                $error_message = 'Event deletion error: ' . $e->getMessage();
                error_log($error_message);
                
                if ($isAjax) {
                    sendJsonResponse(['success' => false, 'message' => 'Failed to delete event: ' . $e->getMessage()]);
                } else {
                    $_SESSION['error'] = 'Failed to delete event: ' . $e->getMessage();
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                }
            }
            
            $event_id = (int)$_POST['id'];
            
            try {
                // First, get the image path to delete the file
                $stmt = $conn->prepare("SELECT image_url FROM events WHERE id = ?");
                $stmt->bind_param("i", $event_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($event = $result->fetch_assoc()) {
                    // Delete the event
                    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
                    $stmt->bind_param("i", $event_id);
                    
                    if ($stmt->execute()) {
                        // If event was deleted and had an image, try to delete the image file
                        if (!empty($event['image_url'])) {
                            $image_path = $_SERVER['DOCUMENT_ROOT'] . $event['image_url'];
                            if (file_exists($image_path)) {
                                @unlink($image_path);
                            }
                        }
                        
                        if ($isAjax) {
                            sendJsonResponse(['success' => true, 'message' => 'Event deleted successfully']);
                        } else {
                            $_SESSION['success'] = 'Event deleted successfully';
                            header('Location: ' . $_SERVER['PHP_SELF']);
                            exit();
                        }
                    } else {
                        throw new Exception($stmt->error);
                    }
                } else {
                    throw new Exception('Event not found');
                }
            } catch (Exception $e) {
                $error = 'Failed to delete event: ' . $e->getMessage();
                if ($isAjax) {
                    sendJsonResponse(['success' => false, 'message' => $error]);
                } else {
                    $_SESSION['error'] = $error;
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                }
            }
            break;
    }
}

// Fetch events from database
$events = [];
$query = "SELECT * FROM events ORDER BY start_date DESC";
if ($result = $conn->query($query)) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}
?>

<style>
  /* Sidebar Styles */
  .sidebar {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    padding: 0;
    margin-bottom: 30px;
    overflow: hidden;
  }

  .profile-card {
    padding: 20px;
    text-align: center;
    background: #f8f9fa;
    border-bottom: 1px solid #eee;
  }

  .profile-image {
    width: 120px;
    height: 120px;
    margin: 0 auto 15px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid #fff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  }

  .profile-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .profile-name {
    margin: 10px 0 5px;
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
  }

  .profile-role {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 0;
  }

  .sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .sidebar-menu li {
    border-bottom: 1px solid #f0f0f0;
  }

  .sidebar-menu li:last-child {
    border-bottom: none;
  }

  .sidebar-menu a {
    display: block;
    padding: 12px 20px;
    color: #555;
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
  }

  .sidebar-menu a i {
    width: 20px;
    margin-right: 10px;
    text-align: center;
  }

  .sidebar-menu a:hover,
  .sidebar-menu a.active {
    background: #f8f9fa;
    color: #014421;
    padding-left: 25px;
  }

  /* Responsive adjustments */
  @media (max-width: 991px) {
    .sidebar {
      margin-bottom: 30px;
    }
  }
  
  /* Events Grid Styles */
  .events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
  }
  
  .event-card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  
  .event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  }
  
  .event-image {
    width: 100%;
    height: 180px;
    object-fit: cover;
  }
  
  .event-details {
    padding: 15px;
  }
  
  .event-date {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 10px;
  }
  
  .event-title {
    font-weight: 600;
    margin-bottom: 10px;
    color: #333;
  }
  
  .event-description {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 15px;
  }
  
  .event-actions {
    display: flex;
    gap: 10px;
  }
  
  .btn-edit {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 5px 12px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  
  .btn-delete {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 5px 12px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  
  .btn-edit:hover {
    background-color: #218838;
  }
  
  .btn-delete:hover {
    background-color: #c82333;
  }
  
  .btn-add-event {
    margin-bottom: 20px;
  }
</style>

<div class="dashboard-container">
  <div class="container">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-lg-3">
        <div class="sidebar">
          <div class="profile-card">
            <div class="profile-image">
              <img src="<?php echo !empty($_SESSION['profile_image']) ? $_SESSION['profile_image'] : 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMTAwIDEwMCI+PHJlY3Qgd2lkdGg9IjEwMCIgaGVpZ2h0PSIxMDAiIGZpbGw9IiNkZGQiLz48dGV4dCB4PSI1MCIgeT0iNTAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgYWxpZ25tZW50LWJhc2VsaW5lPSJtaWRkbGUiIGZpbGw9IiYjNjY2OyI+QXZhdGFyPC90ZXh0Pjwvc3ZnPg'; ?>" alt="Profile Image" id="sidebar-profile-img">
            </div>
            <h3 class="profile-name"><?php echo htmlspecialchars($user_name); ?></h3>
            <div class="profile-role"><?php echo ucfirst(htmlspecialchars($user_role)); ?></div>
          </div>
          <ul class="sidebar-menu">
            <li><a href="my-account.php?tab=dashboard"><i class="fa fa-tachometer"></i> Dashboard</a></li>
            <li><a href="my-account.php?tab=profile"><i class="fa fa-user"></i> My Profile</a></li>
            <?php if ($is_admin): ?>
            <li><a href="admin/users.php"><i class="fa fa-users"></i> Manage Users</a></li>
            <?php endif; ?>
            <?php if ($is_dean): ?>
            <li><a href="my-account.php?tab=faculty-content"><i class="fa fa-graduation-cap"></i> Faculty Content</a></li>
            <?php endif; ?>
            <li><a href="my-account.php?tab=messages"><i class="fa fa-envelope"></i> Messages</a></li>
            <li><a href="my-account.php?tab=settings"><i class="fa fa-cog"></i> Settings</a></li>
            <li class="active"><a href="manage_content.php"><i class="fa fa-edit"></i> Manage Content</a></li>
            <li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
          </ul>
        </div>
      </div>
      
      <!-- Main Content -->
      <div class="col-lg-9">
        <section class="block">
          <div class="page-title">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h2><i class='bx bx-calendar-event me-2'></i>Manage Events</h2>
              <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addEventModal">
                <i class='bx bx-plus'></i> Add New Event
              </button>
            </div>
          </div>

          <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <?php endif; ?>
          
          <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <?php endif; ?>

          <div class="card shadow-sm">
            <div class="card-body">
              <div class="events-grid">
                <?php if (!empty($events)): ?>
                  <?php foreach ($events as $event): ?>
                    <?php 
                    // Fallback image
                    $default_image = '/c/zanvarsity/html/assets/img/no-image-available.jpg';
                    $image_url = '';

                    if (!empty($event['image_url'])) {
                        // Get just the filename
                        $filename = basename($event['image_url']);

                        // Try different possible locations for the image
                        $possible_paths = [
                            // Try with the correct path pattern
                            '/c/zanvarsity/uploads/events/' . $filename,
                            // Try with the path from the database (in case it's already correct)
                            $event['image_url'],
                            // Try with the full path from the document root
                            '/c' . ltrim($event['image_url'], '/')
                        ];

                        foreach ($possible_paths as $path) {
                            // Ensure the path starts with a forward slash
                            $path = '/' . ltrim($path, '/');
                            $full_path = $_SERVER['DOCUMENT_ROOT'] . $path;

                            if (file_exists($full_path)) {
                                $image_url = $path;
                                break;
                            }
                        }
                    }

                    // If no image found, use default
                    if (empty($image_url) || !@getimagesize($_SERVER['DOCUMENT_ROOT'] . $image_url)) {
                        $image_url = $default_image;
                    }
                    ?>
                    <div class="event-card" 
                         data-description="<?php echo htmlspecialchars($event['description']); ?>"
                         data-start-date="<?php echo htmlspecialchars($event['start_date']); ?>"
                         data-end-date="<?php echo htmlspecialchars($event['end_date']); ?>"
                         data-location="<?php echo htmlspecialchars($event['location']); ?>">
                        <div class="event-image-container" style="width: 100%; height: 180px; overflow: hidden; background-color: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                            <img src="<?php echo htmlspecialchars($image_url); ?>" 
                                 alt="<?php echo htmlspecialchars($event['title']); ?>" 
                                 style="max-width: 100%; max-height: 100%; object-fit: cover;"
                                 onerror="this.onerror=null; this.src='<?php echo $default_image; ?>';">
                        </div>
                        
                        <div class="event-details">
                          <div class="event-date">
                            <?php 
                              $start_date = new DateTime($event['start_date']);
                              $end_date = !empty($event['end_date']) ? new DateTime($event['end_date']) : null;
                              
                              echo $start_date->format('M j, Y');
                              if ($end_date && $start_date->format('Y-m-d') !== $end_date->format('Y-m-d')) {
                                echo ' - ' . $end_date->format('M j, Y');
                              }
                              
                              if (!empty($event['location'])) {
                                echo ' • ' . htmlspecialchars($event['location']);
                              }
                            ?>
                          </div>
                          <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                          <?php if (!empty($event['description'])): ?>
                            <p class="event-description">
                              <?php 
                                $description = strip_tags($event['description']);
                                echo strlen($description) > 150 ? substr($description, 0, 150) . '...' : $description;
                              ?>
                            </p>
                          <?php endif; ?>
                          
                          <div class="event-actions">
                            <button class="btn-edit edit-event" data-id="<?php echo $event['id']; ?>">
                              <i class='bx bx-edit-alt'></i> Edit
                            </button>
                            <button class="btn-delete delete-event" data-id="<?php echo $event['id']; ?>">
                              <i class='bx bx-trash'></i> Delete
                            </button>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <div class="col-12 text-center py-5">
                      <i class='bx bx-calendar-x' style="font-size: 3rem; color: #6c757d; margin-bottom: 15px;"></i>
                      <h4>No events found</h4>
                      <p>Get started by adding your first event.</p>
                      <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addEventModal">
                        <i class='bx bx-plus'></i> Add Event
                      </button>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1" role="dialog" aria-labelledby="addEventModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h2 class="modal-title h5" id="addEventModalLabel">Add New Event</h2>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="eventForm" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add_event">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
        <div class="modal-body">
          <div class="form-group">
            <label for="eventTitle">Event Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="eventTitle" name="title" required>
          </div>
          
          <div class="form-group">
            <label for="eventDescription">Description</label>
            <textarea class="form-control" id="eventDescription" name="description" rows="3"></textarea>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="startDate">Start Date <span class="text-danger">*</span></label>
                <input type="datetime-local" class="form-control" id="startDate" name="start_date" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="endDate">End Date</label>
                <input type="datetime-local" class="form-control" id="endDate" name="end_date">
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label for="eventLocation">Location</label>
            <input type="text" class="form-control" id="eventLocation" name="location">
          </div>
          
          <div class="form-group">
            <label for="eventImage">Event Image</label>
            <div class="custom-file">
              <input type="file" class="custom-file-input" id="eventImage" name="event_image" accept="image/*">
              <label class="custom-file-label" for="eventImage">Choose file</label>
            </div>
            <small class="form-text text-muted">Max file size: 5MB. Allowed formats: JPG, PNG, GIF</small>
          </div>
          
          <div id="imagePreview" class="mt-3 text-center" style="display: none;">
            <img id="previewImage" src="#" alt="Preview" class="img-fluid" style="max-height: 200px;">
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success" id="saveEventBtn">Save Event</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Deletion</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this event? This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
      </div>
    </div>
  </div>
</div>

<!-- Include JavaScript libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/30.0.0/classic/ckeditor.js"></script>

<script>
$(document).ready(function() {
    // Initialize CKEditor for description field
    let editor;
    
    ClassicEditor
        .create(document.querySelector('#eventDescription'))
        .then(newEditor => {
            editor = newEditor;
        })
        .catch(error => {
            console.error('Error initializing CKEditor:', error);
        });
    
    // Image preview for file input
    $('#eventImage').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImage').attr('src', e.target.result);
                $('#imagePreview').show();
            }
            reader.readAsDataURL(file);
            
            // Update file label
            const fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        }
    });
    
    // Handle form submission
    $('#eventForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(this);
        
        // Add CSRF token
        formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
        
        // Get the action (add or update)
        const action = $('#eventForm input[name="action"]').val();
        
        // Set the action in form data
        formData.set('action', action);
        
        // Log form data for debugging
        console.log('Form Data:', Object.fromEntries(formData.entries()));
        
        // If it's an update, add the event ID
        if (action === 'update_event') {
            formData.set('event_id', $('#eventId').val());
        }
        
        // Show loading state
        const submitBtn = $('#saveEventBtn');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + (action === 'add_event' ? 'Saving...' : 'Updating...'));
        
        // Send AJAX request
        $.ajax({
            url: 'manage_events.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        console.log('Upload Progress: ' + percent + '%');
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                console.log('Server Response:', response);
                try {
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (data.success) {
                        // Show success message and reload the page
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message || (action === 'add_event' ? 'Event added successfully!' : 'Event updated successfully!'),
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        // Show error message with more details
                        console.error('Error details:', data);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: (data.message || 'An error occurred while saving the event.') + 
                                 '<br><br><small class="text-muted">Check console for more details.</small>'
                        });
                    }
                } catch (e) {
                    console.error('Error parsing response:', e, 'Response:', response);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: 'An error occurred while processing the response.<br><br>' +
                             '<small class="text-muted">' + e.message + '</small>'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    response: xhr.responseText,
                    statusCode: xhr.status,
                    statusText: xhr.statusText
                });
                
                let errorMessage = 'An error occurred while saving the event. ';
                if (xhr.responseText) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        errorMessage += 'Please check the console for details.';
                    }
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: errorMessage + '<br><br><small class="text-muted">Check console for more details.</small>'
                });
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Handle edit event
    $('.edit-event').on('click', function() {
        const eventId = $(this).data('id');
        const eventCard = $(this).closest('.event-card');
        
        // Get event data from data attributes or from the DOM
        const eventData = {
            id: eventId,
            title: eventCard.find('.event-title').text(),
            description: eventCard.data('description') || '',
            start_date: eventCard.data('start-date') || '',
            end_date: eventCard.data('end-date') || '',
            location: eventCard.data('location') || '',
            image_url: eventCard.find('.event-image img').attr('src')
        };
        
        // Populate the form with event data
        $('#eventForm').attr('action', 'manage_events.php');
        $('#eventForm').attr('method', 'POST');
        $('#eventId').val(eventData.id);
        $('#eventTitle').val(eventData.title);
        $('#eventDescription').val(eventData.description);
        $('#startDate').val(eventData.start_date);
        $('#endDate').val(eventData.end_date);
        $('#location').val(eventData.location);
        
        // Set the form action to update
        $('#eventForm').find('input[name="action"]').val('update_event');
        
        // Update the modal title and button text
        $('#eventModalLabel').text('Edit Event');
        $('#saveEventBtn').text('Update Event');
        
        // Show the modal
        $('#eventModal').modal('show');
        
        // Set CKEditor data
        if (editor) {
            editor.setData(eventData.description);
        }
        
        // Update image preview if exists
        if (eventData.image_url && !eventData.image_url.includes('no-image-available')) {
            $('#previewImage').attr('src', eventData.image_url);
            $('#imagePreview').show();
        } else {
            $('#previewImage').attr('src', '');
            $('#imagePreview').hide();
        }
    });
    
    // Reset form when add event button is clicked
    $('[data-target="#addEventModal"]').on('click', function() {
        $('#eventForm')[0].reset();
        $('#eventForm').find('input[name="action"]').val('add_event');
        $('#eventId').val('0');
        $('#eventModalLabel').text('Add New Event');
        $('#saveEventBtn').text('Save Event');
        $('#imagePreview').hide();
        if (editor) {
            editor.setData('');
        }
    });
    
    // Handle delete event
    let eventIdToDelete = null;
    
    // Function to show error message
    function showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message || 'An error occurred. Please try again.',
            confirmButtonText: 'OK'
        });
    }
    
    $('.delete-event').on('click', function() {
        eventIdToDelete = $(this).data('id');
        $('#deleteModal').modal('show');
    });
    
    $('#confirmDeleteBtn').on('click', function() {
        if (!eventIdToDelete) return;
        
        const deleteBtn = $(this);
        const originalText = deleteBtn.html();
        deleteBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...');
        
        // Send delete request
        $.ajax({
            url: 'manage_events.php',
            type: 'POST',
            data: {
                action: 'delete_event',
                id: eventIdToDelete,
                csrf_token: '<?php echo $_SESSION['csrf_token']; ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Show success message and reload the page
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message || 'Event deleted successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'An error occurred while deleting the event.'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while deleting the event. Please try again.'
                });
            },
            complete: function() {
                deleteBtn.prop('disabled', false).html(originalText);
                $('#deleteModal').modal('hide');
            }
        });
    });
    
    // Set minimum date for end date based on start date
    $('#startDate').on('change', function() {
        $('#endDate').attr('min', $(this).val());
    });
});
</script>

<?php
// Include footer
include 'includes/about_footer.php';
?>
