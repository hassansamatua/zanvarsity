<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));

// Include necessary files
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

// Initialize response array
$response = ['success' => false, 'message' => ''];
$error = '';
$success = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid CSRF token';
    } else {
        $action = $_POST['action'] ?? '';
        
        // Handle file upload
        $upload_dir = ROOT_PATH . '/html/uploads/vc_notices/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        if ($action === 'add_notice' || $action === 'update_notice') {
            $title = trim($conn->real_escape_string($_POST['title'] ?? ''));
            $message = trim($conn->real_escape_string($_POST['message'] ?? ''));
            $status = in_array($_POST['status'] ?? 'active', ['active', 'inactive']) ? $_POST['status'] : 'active';
            $pdf_url = '';
            $vc_image = '';
            
            // Handle PDF upload
            if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
                $file_extension = strtolower(pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION));
                if ($file_extension === 'pdf') {
                    $filename = 'notice_' . time() . '_' . uniqid() . '.pdf';
                    $target_file = $upload_dir . $filename;
                    if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $target_file)) {
                        $pdf_url = '/zanvarsity/html/uploads/vc_notices/' . $filename;
                    }
                }
            }
            
            // Handle VC image upload
            if (isset($_FILES['vc_image']) && $_FILES['vc_image']['error'] === UPLOAD_ERR_OK) {
                $file_extension = strtolower(pathinfo($_FILES['vc_image']['name'], PATHINFO_EXTENSION));
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($file_extension, $allowed_types)) {
                    $filename = 'vc_' . time() . '_' . uniqid() . '.' . $file_extension;
                    $target_file = $upload_dir . $filename;
                    if (move_uploaded_file($_FILES['vc_image']['tmp_name'], $target_file)) {
                        $vc_image = '/zanvarsity/html/uploads/vc_notices/' . $filename;
                    }
                }
            }
            
            if ($action === 'add_notice') {
                $stmt = $conn->prepare("INSERT INTO vc_notices (title, message, vc_image, pdf_url, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $title, $message, $vc_image, $pdf_url, $status);
                
                if ($stmt->execute()) {
                    $success = 'Notice added successfully';
                } else {
                    $error = 'Error adding notice: ' . $conn->error;
                }
            } else {
                $id = (int)$_POST['id'];
                $update_fields = "title = ?, message = ?, status = ?";
                $types = "sss";
                $params = array($title, $message, $status);
                
                if (!empty($pdf_url)) {
                    $update_fields .= ", pdf_url = ?";
                    $types .= "s";
                    $params[] = $pdf_url;
                }
                
                if (!empty($vc_image)) {
                    $update_fields .= ", vc_image = ?";
                    $types .= "s";
                    $params[] = $vc_image;
                }
                
                $params[] = $id;
                $types .= "i";
                
                $sql = "UPDATE vc_notices SET $update_fields, updated_at = NOW() WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param($types, ...$params);
                
                if ($stmt->execute()) {
                    $success = 'Notice updated successfully';
                } else {
                    $error = 'Error updating notice: ' . $conn->error;
                }
            }
        } elseif (isset($_POST['action']) && $_POST['action'] === 'delete_notice' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("DELETE FROM vc_notices WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $success = 'Notice deleted successfully';
            } else {
                $error = 'Error deleting notice: ' . $conn->error;
            }
        }
    }
}

// Get all notices
$notices = [];
$result = $conn->query("SELECT * FROM vc_notices ORDER BY created_at DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notices[] = $row;
    }
}

// Set page title
$page_title = 'Manage VC Notices';
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
        .dashboard-card {
            transition: all 0.3s ease;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            height: 100%;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            border-color: #28a745;
        }
        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #28a745;
        }
        .card-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        .card-text {
            color: #666;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        .card-footer {
            background: #f8f9fa;
            border-top: 1px solid #eee;
            padding: 10px 15px;
        }
        .badge-count {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 0.8rem;
            padding: 3px 8px;
        }
        
        /* Ensure all buttons are green */
        .btn-success, 
        .btn-success:active, 
        .btn-success:focus, 
        .btn-success:hover {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
            color: white !important;
            box-shadow: none !important;
        }
        
        /* Custom styles for VC Notices */
        .vc-notice-card {
            margin: 0 auto 30px; /* Center the card and add bottom margin */
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
            max-width: 600px; /* Limit card width for better readability */
            float: none; /* Ensure no floating behavior */
        }
        .vc-notice-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-3px);
        }
        .vc-notice-img {
            height: 250px; /* Slightly larger image */
            object-fit: cover;
            width: 100%;
            display: block; /* Remove any extra space below image */
        }
        .action-buttons {
            display: flex;
            justify-content: center; /* Center buttons horizontally */
            flex-wrap: wrap; /* Allow buttons to wrap on small screens */
            gap: 8px; /* Space between buttons */
            margin-top: 10px;
        }
        .action-buttons .btn {
            margin: 0 4px; /* Space between buttons */
            min-width: 80px; /* Ensure buttons have consistent width */
            text-align: center; /* Center text in buttons */
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
        }
        .action-buttons .btn i {
            margin-right: 5px; /* Space between icon and text */
        }
        .card-body {
            padding: 20px;
            text-align: center; /* Center text content */
        }
        .card-title {
            margin-bottom: 15px;
            font-size: 1.4rem;
            font-weight: 600;
            color: #2c3e50;
        }
        .card-text {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .card-footer {
            background: #f8f9fa;
            border-top: 1px solid #eee;
            padding: 15px 20px;
            text-align: center;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
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
                        <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <div class="navbar-brand nav" id="brand">
                            <a href="/zanvarsity/html/index.html">
                                <img src="/zanvarsity/html/assets/img/logo.png" alt="Zanvarsity" class="logo">
                            </a>
                        </div>
                    </div>
                    <nav class="collapse navbar-collapse bs-navbar-collapse navbar-right" role="navigation">
                        <ul class="nav navbar-nav">
                            <li><a href="/zanvarsity/html/index.html">Home</a></li>
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
                <!-- Breadcrumb -->
                <div class="container">
                    <ol class="breadcrumb">
                        <li><a href="/zanvarsity/html/index.html">Home</a></li>
                        <li><a href="/zanvarsity/html/admin/dashboard.php">Admin</a></li>
                        <li class="active"><?php echo $page_title; ?></li>
                    </ol>
                </div>
                <!-- End Breadcrumb -->

                <!-- Sidebar -->
                <aside class="col-md-3 col-sm-4">
                    <div class="sidebar">
                        <div class="sidebar-inner">
                            <div class="sidebar-widget">
                                <div class="user-avatar">
                                    <div style="width: 100px; height: 100px; margin: 0 auto 15px; background-color: #4caf50; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; font-weight: bold; text-transform: uppercase; text-align: center; padding: 5px; line-height: 1.2;">
                                        <?php echo !empty($user_role) ? ucfirst($user_role) : 'Admin'; ?>
                                    </div>
                                    <div class="text-center">
                                        <h4><?php echo !empty($first_name) ? htmlspecialchars($first_name) : 'Admin'; ?></h4>
                                        <span class="label label-primary"><?php echo ucfirst($user_role); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="sidebar-widget">
                                <ul class="nav nav-pills nav-stacked nav-dashboard">
                                    <li><a href="/zanvarsity/html/admin/dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                                    <?php if (in_array($user_role, ['super_admin', 'admin'])): ?>
                                    <li><a href="users.php"><i class="fa fa-users"></i> Manage Users</a></li>
                                    <li><a href="manage_content.php"><i class="fa fa-file-text"></i> Manage Contents</a></li>
                                    <li class="active"><a href="manage_vc_notice.php"><i class="fa fa-bullhorn"></i> VC Notices</a></li>
                                    <li><a href="manage_events.php"><i class="fa fa-calendar"></i> Manage Events</a></li>
                                    <?php endif; ?>
                                    <?php if (in_array($user_role, ['instructor', 'admin', 'super_admin'])): ?>
                                    <li><a href="/zanvarsity/html/instructor"><i class="fa fa-chalkboard-teacher"></i> Instructor Panel</a></li>
                                    <?php endif; ?>
                                    <li><a href="<?php echo ROOT_PATH; ?>/logout.php" onclick="return confirm('Are you sure you want to log out?')"><i class="fa fa-sign-out"></i> Logout</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </aside>
                <!-- end Sidebar -->

                <!-- Main Content -->
                <div class="col-md-9 col-sm-8">
                    <div class="content-wrapper">
                        <!-- Page header -->
                        <div class="page-title">
                            <div class="row">
                                <div class="col-md-6">
                                    <h3><?php echo $page_title; ?></h3>
                                </div>
                                <?php if (empty($notices)): ?>
                                <div class="col-md-6 text-right">
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addNoticeModal">
                                        <i class='bx bx-plus'></i> Add New Notice
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade in" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <?php echo $success; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade in" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <?php if (empty($notices)): ?>
                                <div class="col-12">
                                    <div class="dashboard-card">
                                        <div class="card-body text-center py-5">
                                            <i class='bx bx-info-circle text-muted' style="font-size: 3rem;"></i>
                                            <h5 class="mt-3">No VC Notices Found</h5>
                                            <p class="text-muted">Click the button above to add a new notice.</p>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($notices as $notice): ?>
                                    <div class="col-md-8 col-md-offset-2">
                                        <div class="vc-notice-card">
                                            <?php if (!empty($notice['vc_image'])): ?>
                                                <img src="<?php echo htmlspecialchars($notice['vc_image']); ?>" class="vc-notice-img" alt="VC Notice">
                                            <?php endif; ?>
                                            <div class="card-body">
                                                <h4 class="card-title"><?php echo htmlspecialchars($notice['title']); ?></h4>
                                                <p class="card-text">
                                                    <?php 
                                                    $message = strip_tags($notice['message']);
                                                    echo nl2br(htmlspecialchars(mb_substr($message, 0, 150) . (mb_strlen($message) > 150 ? '...' : ''))); 
                                                    ?>
                                                </p>
                                            </div>
                                            <div class="card-footer">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="status-badge status-<?php echo $notice['status']; ?>">
                                                        <?php echo ucfirst($notice['status']); ?>
                                                    </span>
                                                    <div class="action-buttons">
                                                        <button class="btn btn-xs btn-primary edit-notice" 
                                                                data-id="<?php echo $notice['id']; ?>"
                                                                data-title="<?php echo htmlspecialchars($notice['title']); ?>"
                                                                data-message="<?php echo htmlspecialchars($notice['message']); ?>"
                                                                data-status="<?php echo $notice['status']; ?>"
                                                                data-vc-image="<?php echo htmlspecialchars($notice['vc_image'] ?? ''); ?>"
                                                                data-pdf-url="<?php echo htmlspecialchars($notice['pdf_url'] ?? ''); ?>">
                                                            <i class='bx bx-edit-alt'></i> Edit
                                                        </button>
                                                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this notice?');">
                                                            <input type="hidden" name="action" value="delete_notice">
                                                            <input type="hidden" name="id" value="<?php echo $notice['id']; ?>">
                                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                            <button type="submit" class="btn btn-xs btn-danger">
                                                                <i class='bx bx-trash'></i> Delete
                                                            </button>
                                                        </form>
                                                        <?php if (!empty($notice['pdf_url'])): ?>
                                                            <a href="<?php echo htmlspecialchars($notice['pdf_url']); ?>" 
                                                               class="btn btn-xs btn-info" 
                                                               target="_blank"
                                                               title="View PDF">
                                                                <i class='bx bx-file'></i> PDF
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- end Main Content -->
            </div>
        </div>
    </div>
</div>

<!-- Add Notice Modal -->
<div class="modal fade" id="addNoticeModal" tabindex="-1" role="dialog" aria-labelledby="addNoticeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_notice">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="addNoticeModalLabel">Add New VC Notice</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title" class="control-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message" class="control-label">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vc_image" class="control-label">VC Image (Optional)</label>
                                <input type="file" class="form-control" id="vc_image" name="vc_image" accept="image/*">
                                <p class="help-block">Recommended size: 800x400px. Max size: 2MB</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pdf_file" class="control-label">PDF File (Optional)</label>
                                <input type="file" class="form-control" id="pdf_file" name="pdf_file" accept=".pdf">
                                <p class="help-block">Max size: 5MB</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label">Status</label>
                        <div class="radio">
                            <label>
                                <input type="radio" name="status" value="active" checked> Active
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="status" value="inactive"> Inactive
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save Notice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Notice Modal -->
<div class="modal fade" id="editNoticeModal" tabindex="-1" role="dialog" aria-labelledby="editNoticeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_notice">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="editNoticeModalLabel">Edit VC Notice</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_title" class="control-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_message" class="control-label">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_message" name="message" rows="5" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_vc_image" class="control-label">VC Image (Optional)</label>
                                <input type="file" class="form-control" id="edit_vc_image" name="vc_image" accept="image/*">
                                <div id="current_vc_image" class="mt-2"></div>
                                <p class="help-block">Leave empty to keep current image</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_pdf_file" class="control-label">PDF File (Optional)</label>
                                <input type="file" class="form-control" id="edit_pdf_file" name="pdf_file" accept=".pdf">
                                <div id="current_pdf_file" class="mt-2"></div>
                                <p class="help-block">Leave empty to keep current file</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label">Status</label>
                        <div class="radio">
                            <label>
                                <input type="radio" name="status" id="edit_status_active" value="active"> Active
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="status" id="edit_status_inactive" value="inactive"> Inactive
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Update Notice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="/zanvarsity/html/assets/js/jquery-2.1.0.min.js"></script>
<script src="/zanvarsity/html/assets/bootstrap/js/bootstrap.min.js"></script>
<script src="/zanvarsity/html/assets/js/selectize.min.js"></script>
<script src="/zanvarsity/html/assets/js/owl.carousel.min.js"></script>
<script src="/zanvarsity/html/assets/js/jquery.placeholder.js"></script>
<script src="/zanvarsity/html/assets/js/jshashtable-2.1_src.js"></script>
<script src="/zanvarsity/html/assets/js/jquery.numberformatter-1.2.3.js"></script>
<script src="/zanvarsity/html/assets/js/tmpl.js"></script>
<script src="/zanvarsity/html/assets/js/jquery.dependClass-0.1.js"></script>
<script src="/zanvarsity/html/assets/js/draggable-0.1.js"></script>
<script src="/zanvarsity/html/assets/js/jquery.slider.js"></script>
<script src="/zanvarsity/html/assets/js/wow.js"></script>
<script src="/zanvarsity/html/assets/js/custom.js"></script>
<script>
// Handle edit button click
document.querySelectorAll('.edit-notice').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const title = this.getAttribute('data-title');
        const message = this.getAttribute('data-message');
        const status = this.getAttribute('data-status');
        const vcImage = this.getAttribute('data-vc-image');
        const pdfUrl = this.getAttribute('data-pdf-url');
        
        // Set form values
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_title').value = title;
        document.getElementById('edit_message').value = message;
        
        // Set status radio button
        document.getElementById('edit_status_' + status).checked = true;
        
        // Show current VC image if exists
        const currentVcImage = document.getElementById('current_vc_image');
        if (vcImage) {
            currentVcImage.innerHTML = `
                <div class="alert alert-info">
                    <p>Current Image:</p>
                    <img src="${vcImage}" alt="Current Image" style="max-width: 100%; max-height: 150px;" class="img-thumbnail">
                </div>
            `;
        } else {
            currentVcImage.innerHTML = '<div class="alert alert-warning">No image uploaded</div>';
        }
        
        // Show current PDF file if exists
        const currentPdfFile = document.getElementById('current_pdf_file');
        if (pdfUrl) {
            const fileName = pdfUrl.split('/').pop();
            currentPdfFile.innerHTML = `
                <div class="alert alert-info">
                    <p>Current File: <a href="${pdfUrl}" target="_blank">${fileName}</a></p>
                </div>
            `;
        } else {
            currentPdfFile.innerHTML = '<div class="alert alert-warning">No file uploaded</div>';
        }
        
        // Show the modal
        $('#editNoticeModal').modal('show');
    });
});

// Initialize tooltips
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
</body>
</html>
