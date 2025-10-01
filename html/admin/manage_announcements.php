<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    
    // Set JSON header for AJAX responses
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
    }
    
    // Set error reporting for development (remove in production)
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'data' => []
];

try {
    // Define root path and include necessary files
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
    require_once ROOT_PATH . '/includes/auth_functions.php';
    require_once ROOT_PATH . '/includes/database.php';

    // Check if user is logged in and is admin
    require_login();

    // Use the global database connection
    global $conn;

    // Generate CSRF token if not exists
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    $user_role = $_SESSION['role'] ?? 'admin';
    $user_name = $_SESSION['name'] ?? '';
    $user_email = $_SESSION['email'] ?? '';
    $first_name = '';

    // Get user's first name from database
    if (isset($_SESSION['user_id']) && $conn) {
        $user_id = $_SESSION['user_id'];
        $query = "SELECT first_name, role FROM users WHERE id = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($user_data = $result->fetch_assoc()) {
                    $first_name = $user_data['first_name'] ?? '';
                    $user_role = $user_data['role'] ?? $user_role;
                }
            }
            $stmt->close();
        }
    }

    $error = '';
    $success = '';

    // Process form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        try {
            if ($action === 'add_announcement') {
                // Process the form data
                $title = trim($_POST['title'] ?? '');
                $content = trim($_POST['content'] ?? '');
                $start_date = $_POST['start_date'] ?? '';
                $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
                $is_important = isset($_POST['is_important']) ? 1 : 0;
                $attachment_url = null;
                $attachment_name = null;

                // Handle file upload
                if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = ROOT_PATH . '/uploads/announcements/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $file_extension = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
                    $file_name = uniqid('announcement_') . '.' . $file_extension;
                    $file_path = $upload_dir . $file_name;

                    $allowed_types = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png', 'gif'];
                    if (!in_array(strtolower($file_extension), $allowed_types)) {
                        throw new Exception('File type not allowed. Allowed types: ' . implode(', ', $allowed_types));
                    }

                    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $file_path)) {
                        $attachment_url = '/uploads/announcements/' . $file_name;
                        $attachment_name = $_FILES['attachment']['name'];
                    } else {
                        throw new Exception('Failed to upload file. Please try again.');
                    }
                }

                // Validate required fields
                if (empty($title) || empty($content) || empty($start_date)) {
                    throw new Exception('Please fill in all required fields');
                }

                // Insert into database
                $query = "INSERT INTO announcements (title, content, attachment_url, attachment_name, start_date, end_date, is_important, created_by, status) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

                if ($stmt = $conn->prepare($query)) {
                    $status = 'active';  // Default status
                    $stmt->bind_param("ssssssiis", 
                        $title, 
                        $content,
                        $attachment_url,
                        $attachment_name,
                        $start_date, 
                        $end_date, 
                        $is_important, 
                        $_SESSION['user_id'],
                        $status
                    );

                    if ($stmt->execute()) {
                        $response['success'] = true;
                        $response['message'] = 'Announcement added successfully';
                        $response['data']['id'] = $stmt->insert_id;
                        $response['redirect'] = 'manage_announcements.php';
                    } else {
                        throw new Exception('Database error: ' . $stmt->error);
                    }
                    $stmt->close();
                } else {
                    throw new Exception('Database error: ' . $conn->error);
                }
            } elseif ($action === 'update_announcement') {
                // Process announcement update
                $id = $_POST['id'] ?? 0;
                $title = trim($_POST['title'] ?? '');
                $content = trim($_POST['content'] ?? '');
                $start_date = $_POST['start_date'] ?? '';
                $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
                $status = $_POST['status'] ?? 'active';
                $is_important = isset($_POST['is_important']) ? 1 : 0;

                if (empty($title) || empty($content) || empty($start_date)) {
                    throw new Exception('Please fill in all required fields');
                }

                // Handle file upload for update
                $update_attachment = "";
                $attachment_params = [];
                
                if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = ROOT_PATH . '/uploads/announcements/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $file_extension = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
                    $file_name = uniqid('announcement_') . '.' . $file_extension;
                    $file_path = $upload_dir . $file_name;

                    $allowed_types = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png', 'gif'];
                    if (!in_array(strtolower($file_extension), $allowed_types)) {
                        throw new Exception('File type not allowed. Allowed types: ' . implode(', ', $allowed_types));
                    }

                    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $file_path)) {
                        $attachment_url = '/uploads/announcements/' . $file_name;
                        $attachment_name = $_FILES['attachment']['name'];
                        $update_attachment = ", attachment_url = ?, attachment_name = ?";
                        $attachment_params = [$attachment_url, $attachment_name];
                    } else {
                        throw new Exception('Failed to upload file. Please try again.');
                    }
                }
                
                // Check if we need to remove the existing attachment
                if (isset($_POST['remove_attachment']) && $_POST['remove_attachment'] == '1') {
                    // Delete the old file if it exists
                    $query = "SELECT attachment_url FROM announcements WHERE id = ?";
                    if ($stmt = $conn->prepare($query)) {
                        $stmt->bind_param("i", $id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($row = $result->fetch_assoc() && !empty($row['attachment_url'])) {
                            $old_file = ROOT_PATH . $row['attachment_url'];
                            if (file_exists($old_file)) {
                                unlink($old_file);
                            }
                        }
                        $stmt->close();
                    }
                    $update_attachment = ", attachment_url = NULL, attachment_name = NULL";
                    $attachment_params = [];
                }
                
                $query = "UPDATE announcements SET 
                         title = ?, content = ?, start_date = ?, end_date = ?, 
                         status = ?, is_important = ?{$update_attachment}, updated_at = NOW() 
                         WHERE id = ?";
                          
                if ($stmt = $conn->prepare($query)) {
                    $params = array_merge([$title, $content, $start_date, $end_date, $status, $is_important], $attachment_params, [$id]);
                    $types = str_repeat('s', count($params) - 1) . 'i';
                    $stmt->bind_param($types, ...$params);
                    if ($stmt->execute()) {
                        $response['success'] = true;
                        $response['message'] = 'Announcement updated successfully';
                        $response['redirect'] = 'manage_announcements.php';
                    } else {
                        throw new Exception('Failed to update announcement: ' . $stmt->error);
                    }
                    $stmt->close();
                } else {
                    throw new Exception('Database error: ' . $conn->error);
                }
            } elseif ($action === 'delete_announcement') {
                // Process announcement deletion
                // First, get the attachment path to delete the file
                $query = "SELECT attachment_url FROM announcements WHERE id = ?";
                if ($stmt = $conn->prepare($query)) {
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($row = $result->fetch_assoc() && !empty($row['attachment_url'])) {
                        $file_path = ROOT_PATH . $row['attachment_url'];
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                    $stmt->close();
                }
                
                // Now delete the announcement
                $query = "DELETE FROM announcements WHERE id = ?";
                if ($stmt = $conn->prepare($query)) {
                    $stmt->bind_param("i", $id);
                    if ($stmt->execute()) {
                        $response['success'] = true;
                        $response['message'] = 'Announcement deleted successfully';
                        $response['redirect'] = 'manage_announcements.php';
                    } else {
                        throw new Exception('Failed to delete announcement: ' . $stmt->error);
                    }
                    $stmt->close();
                } else {
                    throw new Exception('Database error: ' . $conn->error);
                }
            } else {
                throw new Exception('Invalid action');
            }
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
            http_response_code(500);
        }
        
        // Output JSON response for AJAX requests
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode($response);
            exit;
        }
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    http_response_code(500);
    
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode($response);
        exit;
    }
    // For non-AJAX requests, you might want to handle the error differently
    $error = $e->getMessage();
}

// Set page title
$page_title = 'Announcements Management';
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <link href="/zanvarsity/html/assets/css/font-awesome.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/selectize.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/owl.carousel.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/vanillabox/vanillabox.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/style.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/green-theme.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/admin-theme.css" type="text/css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    
    <style>
        .announcement-card {
            transition: all 0.3s ease;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .announcement-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            border-color: #28a745;
        }
        .announcement-header {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            background-color: #f8f9fa;
        }
        .announcement-body {
            padding: 15px;
        }
        .announcement-footer {
            padding: 10px 15px;
            background-color: #f8f9fa;
            border-top: 1px solid #f0f0f0;
            font-size: 0.9em;
            color: #6c757d;
        }
        .status-badge {
            font-size: 0.8em;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: 500;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-scheduled {
            background-color: #fff3cd;
            color: #856404;
        }
        .audience-badge {
            font-size: 0.8em;
            padding: 3px 8px;
            border-radius: 12px;
            background-color: #e2e3e5;
            color: #383d41;
        }
        .action-buttons .btn {
            margin-right: 5px;
        }
        
        /* Sidebar Styles */
        .sidebar {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        
        .user-panel {
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
        }
        
        .user-avatar {
            text-align: center;
        }
        
        .user-avatar div {
            width: 100px;
            height: 100px;
            margin: 0 auto 15px;
            background-color: #4caf50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            padding: 5px;
            line-height: 1.2;
        }
        
        .user-name {
            font-size: 16px;
            font-weight: 600;
            color: #343a40;
        }
        
        .user-role {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .sidebar-menu {
            padding: 15px 0;
        }
        
        .sidebar-menu .nav-item {
            margin: 5px 0;
        }
        
        .sidebar-menu .nav-link {
            color: #495057;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .sidebar-menu .nav-link i {
            font-size: 20px;
            margin-right: 10px;
            width: 24px;
            text-align: center;
        }
        
        .sidebar-menu .nav-link:hover,
        .sidebar-menu .nav-link.active {
            background: #e9ecef;
            color: #0d6efd;
        }
        
        .sidebar-menu .nav-link.text-danger:hover {
            color: #dc3545 !important;
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
                    <li><a href="<?php echo ROOT_PATH; ?>/logout.php">Log Out</a></li>
                </ul>
            </div>
        </div>
        <div class="primary-navigation-wrapper">
            <header class="navbar" id="top" role="banner">
                <div class="container">
                    <div class="navbar-header">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="navbar-brand nav" id="brand">
                            <a href="/zanvarsity/html/index.html">
                                <img src="/zanvarsity/html/assets/img/logo.png" alt="Zanvarsity" class="logo">
                            </a>
                        </div>
                    </div>
                    <nav class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item"><a class="nav-link" href="/zanvarsity/html/index.html">Home</a></li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="academicsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Academics
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="academicsDropdown">
                                    <li><a class="dropdown-item" href="/zanvarsity/html/academics.php">Programs</a></li>
                                    <li><a class="dropdown-item" href="/zanvarsity/html/faculties.php">Faculties</a></li>
                                    <li><a class="dropdown-item" href="/zanvarsity/html/departments.php">Departments</a></li>
                                    <li><a class="dropdown-item" href="/zanvarsity/html/courses.php">Courses</a></li>
                                </ul>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="/zanvarsity/html/admissions.php">Admissions</a></li>
                            <li class="nav-item"><a class="nav-link" href="/zanvarsity/html/campus-life.php">Campus Life</a></li>
                            <li class="nav-item"><a class="nav-link" href="/zanvarsity/html/research.php">Research</a></li>
                            <li class="nav-item"><a class="nav-link" href="/zanvarsity/html/about.php">About</a></li>
                            <li class="nav-item"><a class="nav-link" href="/zanvarsity/html/contact.php">Contact</a></li>
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
                <!-- Breadcrumb -->
                <div class="container">
                    <ol class="breadcrumb">
                        <li><a href="/zanvarsity/html/index.html">Home</a></li>
                        <li><a href="/zanvarsity/html/admin/manage_content.php">Manage Content</a></li>
                        <li class="active"><?php echo $page_title; ?></li>
                    </ol>
                </div>
                <!-- End Breadcrumb -->

                <!-- Sidebar -->
                <aside class="col-md-3 col-sm-4">
                    <div class="sidebar">
                        <div class="sidebar-inner">
                            <div class="user-panel">
                                <div class="user-avatar">
                                    <div style="width: 100px; height: 100px; margin: 0 auto 15px; background-color: #4caf50; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; font-weight: bold; text-transform: uppercase; text-align: center; padding: 5px; line-height: 1.2;">
                                        <?php echo !empty($user_role) ? ucfirst($user_role) : 'Admin'; ?>
                                    </div>
                                    <!-- <div class="text-center">
                                        <h4><?php echo !empty($first_name) ? htmlspecialchars($first_name) : 'hassan'; ?></h4>
                                        <span class="label label-primary"><?php echo ucfirst($user_role); ?></span>
                                    </div> -->
                                </div>
                            </div>
                            <nav class="sidebar-navigation">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                                            <i class='bx bxs-dashboard'></i>
                                            <span>Dashboard</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : ''; ?>" href="users.php">
                                            <i class='bx bxs-user-detail'></i>
                                            <span>Manage Users</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) === 'manage_content.php' || basename($_SERVER['PHP_SELF']) === 'manage_announcements.php') ? 'active' : ''; ?>" href="manage_content.php">
                                            <i class='bx bxs-file'></i>
                                            <span>Manage Content</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'my-courses.php' ? 'active' : ''; ?>" href="my-courses.php">
                                            <i class='bx bxs-book'></i>
                                            <span>My Courses</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'my-profile.php' ? 'active' : ''; ?>" href="my-profile.php">
                                            <i class='bx bxs-user'></i>
                                            <span>My Profile</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                                            <i class='bx bxs-cog'></i>
                                            <span>Settings</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'instructor-panel.php' ? 'active' : ''; ?>" href="instructor-panel.php">
                                            <i class='bx bxs-dashboard'></i>
                                            <span>Instructor Panel</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-danger" href="logout.php">
                                            <i class='bx bx-log-out'></i>
                                            <span>Logout</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </aside>
                <!-- End Sidebar -->

                <!-- Main Content -->
                <div class="col-md-9 col-sm-8">
                    <section class="block">
                        <div class="page-title">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h2><i class='bx bxs-megaphone me-2'></i>Manage Announcements</h2>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
                                    <i class='bx bx-plus'></i> Add Announcement
                                </button>
                            </div>
                            
                            <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($success); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <!-- Filter and Search -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class='bx bx-search'></i></span>
                                            <input type="text" class="form-control" id="searchAnnouncements" placeholder="Search announcements...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" id="filterStatus">
                                            <option value="">All Status</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                            <option value="scheduled">Scheduled</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" id="filterAudience">
                                            <option value="">All Audiences</option>
                                            <option value="all">Everyone</option>
                                            <option value="students">Students</option>
                                            <option value="instructors">Instructors</option>
                                            <option value="staff">Staff</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Announcements Table -->
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle" id="announcementsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Title</th>
                                                <th>Status</th>
                                                <th>Audience</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Fetch announcements from database
                                            $query = "SELECT a.*, u.email as author_email 
                                                     FROM announcements a 
                                                     LEFT JOIN users u ON a.created_by = u.id 
                                                     ORDER BY a.start_date DESC, a.created_at DESC";
                                            $result = $conn->query($query);

                                            // Debug output
                                            echo "<!-- SQL Query: " . htmlspecialchars($query) . " -->\n";
                                            if ($result === false) {
                                                echo "<!-- Query Error: " . $conn->error . " -->\n";
                                            } else {
                                                echo "<!-- Number of rows: " . $result->num_rows . " -->\n";
                                                // Print column names for debugging
                                                if ($result->num_rows > 0) {
                                                    $row = $result->fetch_assoc();
                                                    echo "<!-- First row keys: " . implode(', ', array_keys($row)) . " -->\n";
                                                    // Reset pointer
                                                    $result->data_seek(0);
                                                }
                                            }

                                            if ($result && $result->num_rows > 0) {
                                                $current_date = date('Y-m-d H:i:s');
                                                
                                                while ($row = $result->fetch_assoc()) {
                                                    // Determine status
                                                    $status = $row['status'];
                                                    $status_class = '';
                                                    
                                                    if ($status === 'inactive') {
                                                        $status_class = 'status-inactive';
                                                        $status_text = 'Inactive';
                                                    } elseif ($row['start_date'] > $current_date) {
                                                        $status_class = 'status-scheduled';
                                                        $status_text = 'Scheduled';
                                                    } else {
                                                        $status_class = 'status-active';
                                                        $status_text = 'Active';
                                                    }
                                                    
                                                    // Format dates
                                                    $start_date = date('M j, Y', strtotime($row['start_date']));
                                                    $end_date = !empty($row['end_date']) ? date('M j, Y', strtotime($row['end_date'])) : 'N/A';
                                                    
                                                    // Audience mapping
                                                    $audience_map = [
                                                        'all' => 'Everyone',
                                                        'students' => 'Students',
                                                        'instructors' => 'Instructors',
                                                        'staff' => 'Staff'
                                                    ];
                                                    
                                                    $target_audience = $row['target_audience'] ?? 'all';
                                                    $audience_text = $audience_map[$target_audience] ?? ucfirst($target_audience);
                                            ?>
                                            <tr data-status="<?php echo strtolower($status_text); ?>" 
                                                data-audience="<?php echo htmlspecialchars($target_audience); ?>">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            <i class='bx bxs-megaphone text-primary'></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-0"><?php echo htmlspecialchars($row['title']); ?></h6>
                                                            <small class="text-muted">
                                                                By <?php echo htmlspecialchars($row['author_email']); ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="status-badge <?php echo $status_class; ?>">
                                                        <?php echo $status_text; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="audience-badge">
                                                        <?php echo $audience_text; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $start_date; ?></td>
                                                <td><?php echo $end_date; ?></td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <button type="button" class="btn btn-sm btn-outline-primary view-announcement" 
                                                                data-id="<?php echo $row['id']; ?>"
                                                                data-bs-toggle="tooltip" 
                                                                title="View">
                                                            <i class='bx bx-show'></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-warning edit-announcement" 
                                                                data-id="<?php echo $row['id']; ?>"
                                                                data-bs-toggle="tooltip" 
                                                                title="Edit">
                                                            <i class='bx bx-edit'></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger delete-announcement" 
                                                                data-id="<?php echo $row['id']; ?>"
                                                                data-title="<?php echo htmlspecialchars($row['title']); ?>"
                                                                data-bs-toggle="tooltip" 
                                                                title="Delete">
                                                            <i class='bx bx-trash'></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php 
                                                } 
                                            } else {
                                            ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class='bx bx-message-alt-x' style="font-size: 2rem;"></i>
                                                        <p class="mt-2 mb-0">No announcements found.</p>
                                                        <button type="button" class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
                                                            <i class='bx bx-plus me-1'></i> Add Announcement
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php 
                                            } 
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Pagination -->
                                <nav aria-label="Page navigation" class="mt-4">
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                                        </li>
                                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                                        <li class="page-item">
                                            <a class="page-link" href="#">Next</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </section>
                </div>
                <!-- End Main Content -->
            </div>
        </div>
    </div>
</div>
<!-- End Wrapper -->

<!-- Add Announcement Modal -->
<div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-labelledby="addAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAnnouncementModalLabel">Add New Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addAnnouncementForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="attachment" class="form-label">Attachment (Optional)</label>
                        <input type="file" class="form-control" id="attachment" name="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.gif">
                        <div class="form-text">Allowed file types: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, JPG, PNG, GIF (Max: 10MB)</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date (Optional)</label>
                            <input type="datetime-local" class="form-control" id="end_date" name="end_date">
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_important" name="is_important">
                        <label class="form-check-label" for="is_important">
                            Mark as Important
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Announcement Modal -->
<div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-labelledby="editAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAnnouncementModalLabel">Edit Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editAnnouncementForm" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_announcement">
                    <input type="hidden" name="id" id="edit_announcement_id">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_content" class="form-label">Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_content" name="content" rows="5" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_attachment" class="form-label">Attachment (Optional)</label>
                        <input type="file" class="form-control mb-2" id="edit_attachment" name="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.gif">
                        <div id="current_attachment" class="mb-2"></div>
                        <div class="form-text">Allowed file types: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, JPG, PNG, GIF (Max: 10MB)</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="edit_start_date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_end_date" class="form-label">End Date (Optional)</label>
                                <input type="datetime-local" class="form-control" id="edit_end_date" name="end_date">
                                <div class="form-text">Leave empty if the announcement has no end date.</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_target_audience" class="form-label">Target Audience <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_target_audience" name="target_audience" required>
                            <option value="all">Everyone</option>
                            <option value="students">Students Only</option>
                            <option value="instructors">Instructors Only</option>
                            <option value="staff">Staff Only</option>
                        </select>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edit_is_important" name="is_important" value="1">
                        <label class="form-check-label" for="edit_is_important">Mark as important</label>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Announcement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Announcement Modal -->
<div class="modal fade" id="viewAnnouncementModal" tabindex="-1" aria-labelledby="viewAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewAnnouncementModalLabel">Announcement Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="announcement-details">
                    <h4 id="view_title" class="mb-3"></h4>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <span class="badge bg-primary me-2" id="view_status"></span>
                            <span class="badge bg-secondary" id="view_audience"></span>
                        </div>
                        <div class="text-muted" id="view_dates"></div>
                    </div>
                    
                    <div class="announcement-content mb-4" id="view_content"></div>
                    
                    <div class="announcement-meta">
                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                            <div class="text-muted">
                                <small>Created by: <span id="view_author"></span></small>
                            </div>
                            <div class="text-muted">
                                <small>Last updated: <span id="view_updated_at"></span></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the announcement "<span id="announcementToDelete"></span>"? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                <form id="deleteAnnouncementForm" method="post" style="display: inline-block;">
                    <input type="hidden" name="action" value="delete_announcement">
                    <input type="hidden" name="id" id="delete_announcement_id">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded');
    
    // Initialize modals
    const addModalEl = document.getElementById('addAnnouncementModal');
    if (addModalEl) {
        // Initialize modal
        const addModal = new bootstrap.Modal(addModalEl);
        
        // Show modal when button is clicked
        document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#addAnnouncementModal"]').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Opening modal');
                addModal.show();
            });
        });
        
        // Handle form submission
        const form = document.getElementById('addAnnouncementForm');
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Show loading state
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
                
                try {
                    // Create form data
                    const formData = new FormData(form);
                    formData.append('action', 'add_announcement');
                    formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
                    
                    const response = await fetch('', {  // Use relative URL
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams(formData)
                    });
                    
                    const responseText = await response.text();
                    let result;
                    
                    try {
                        result = JSON.parse(responseText);
                    } catch (e) {
                        console.error('Failed to parse JSON response:', e);
                        console.error('Response text:', responseText);
                        throw new Error('Invalid response from server. Please check the console for details.');
                    }
                    
                    if (result.success) {
                        // Show success message
                        await Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: result.message || 'Announcement added successfully',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        // Close the modal and reload the page
                        const addModal = bootstrap.Modal.getInstance(document.getElementById('addAnnouncementModal'));
                        addModal.hide();
                        window.location.href = result.redirect || 'manage_announcements.php';
                    } else {
                        throw new Error(result.message || 'Failed to add announcement');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    await Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'An error occurred while saving the announcement. Please try again.'
                    });
                } finally {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            });
        }
        
        // Fix modal accessibility
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('shown.bs.modal', function () {
                this.removeAttribute('aria-hidden');
                this.setAttribute('aria-modal', 'true');
            });
            
            modal.addEventListener('hidden.bs.modal', function () {
                this.setAttribute('aria-hidden', 'true');
                this.removeAttribute('aria-modal');
            });
        });
    } else {
        console.error('Could not find modal element');
    }
});
</script>

<!-- Add this code after the existing script tag -->
<script>
// Handle Edit Announcement
$(document).on('click', '.edit-announcement', function(e) {
    e.preventDefault();
    const announcementId = $(this).data('id');
    
    // Show loading state
    Swal.fire({
        title: 'Loading...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Fetch announcement data
    $.ajax({
        url: 'get_announcement.php',
        type: 'GET',
        data: { id: announcementId },
        dataType: 'json',
        success: function(response) {
            Swal.close();
            
            if (response.success) {
                // Populate the edit form
                $('#edit_announcement_id').val(response.data.id);
                $('#edit_title').val(response.data.title);
                $('#edit_content').val(response.data.content);
                $('#edit_start_date').val(response.data.start_date.replace(' ', 'T'));
                $('#edit_end_date').val(response.data.end_date ? response.data.end_date.replace(' ', 'T') : '');
                $('#edit_is_important').prop('checked', response.data.is_important == 1);
                
                // Show the edit modal
                const editModal = new bootstrap.Modal(document.getElementById('editAnnouncementModal'));
                editModal.show();
            } else {
                Swal.fire('Error', response.message || 'Failed to load announcement', 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            console.error('Error:', error);
            Swal.fire('Error', 'Failed to load announcement data', 'error');
        }
    });
});

// Handle Edit Form Submission
$('#editAnnouncementForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'update_announcement');
    formData.append('id', $('#edit_announcement_id').val());
    
    // Add remove_attachment flag if checkbox is checked
    if ($('#remove_attachment').is(':checked')) {
        formData.append('remove_attachment', '1');
    }
    
    // Show loading state
    const submitBtn = $('#editAnnouncementModal').find('.btn-primary');
    const originalBtnText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
    
    // Submit form via AJAX
    $.ajax({
        url: 'manage_announcements.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message || 'Announcement updated successfully',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('Error', response.message || 'Failed to update announcement', 'error');
                submitBtn.prop('disabled', false).html(originalBtnText);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Failed to update announcement', 'error');
            submitBtn.prop('disabled', false).html(originalBtnText);
        }
    });
});

// Handle Delete Announcement
$(document).on('click', '.delete-announcement', function(e) {
    e.preventDefault();
    const announcementId = $(this).data('id');
    const announcementTitle = $(this).data('title');
    
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete "${announcementTitle}". This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Deleting...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Send delete request
            $.ajax({
                url: 'delete_announcement.php',
                type: 'POST',
                data: {
                    id: announcementId,
                    action: 'delete_announcement',
                    csrf_token: '<?php echo $_SESSION['csrf_token']; ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message || 'Announcement has been deleted.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(response.message || 'Failed to delete announcement');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Failed to delete announcement', 'error');
                }
            });
        }
    });
});

// Initialize tooltips
$(function () {
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>

<!-- Debug Info -->
<script>
console.log('jQuery version:', $.fn.jquery);
console.log('Bootstrap version:', $.fn.modal ? 'Bootstrap 5' : 'Bootstrap 4');
console.log('Modal element exists:', document.getElementById('addAnnouncementModal') ? 'Yes' : 'No');
</script>
</body>
</html>
