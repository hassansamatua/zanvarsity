<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));

require_once ROOT_PATH . '/includes/auth_functions.php';
require_once ROOT_PATH . '/includes/database.php';
require_once ROOT_PATH . '/includes/publication_functions.php';

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

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid CSRF token';
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'add_publication':
                // Validate required fields
                $required = ['title', 'author', 'publication_date'];
                $missing = [];
                $publication = [];
                
                foreach ($required as $field) {
                    if (empty(trim($_POST[$field] ?? ''))) {
                        $missing[] = $field;
                    } else {
                        $publication[$field] = trim($_POST[$field]);
                    }
                }
                
                if (!empty($missing)) {
                    $error = 'Please fill in all required fields: ' . implode(', ', $missing);
                    break;
                }
                
                // Sanitize input
                $title = trim($conn->real_escape_string($_POST['title']));
                $author = trim($conn->real_escape_string($_POST['author']));
                $description = trim($conn->real_escape_string($_POST['description'] ?? ''));
                $publication_date = date('Y-m-d', strtotime($_POST['publication_date']));
                $is_featured = isset($_POST['is_featured']) ? 1 : 0;
                
                // Handle file uploads
                $image_url = '';
                $document_url = '';
                
                // Handle cover image upload
                if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = ROOT_PATH . '/html/uploads/publications/images/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_extension = strtolower(pathinfo($_FILES['image_url']['name'], PATHINFO_EXTENSION));
                    $filename = 'pub_cover_' . time() . '_' . uniqid() . '.' . $file_extension;
                    $target_file = $upload_dir . $filename;
                    
                    // Validate image
                    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                    if (!in_array($file_extension, $allowed_types)) {
                        $error = 'Only JPG, JPEG, PNG & GIF files are allowed for cover images.';
                        break;
                    }
                    
                    if (move_uploaded_file($_FILES['image_url']['tmp_name'], $target_file)) {
                        $image_url = '/zanvarsity/html/uploads/publications/images/' . $filename;
                    }
                }
                
                // Handle document upload
                if (isset($_FILES['document_url']) && $_FILES['document_url']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = ROOT_PATH . '/html/uploads/publications/documents/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_extension = strtolower(pathinfo($_FILES['document_url']['name'], PATHINFO_EXTENSION));
                    $filename = 'pub_doc_' . time() . '_' . uniqid() . '.' . $file_extension;
                    $target_file = $upload_dir . $filename;
                    
                    // Validate document
                    $allowed_doc_types = ['pdf', 'doc', 'docx', 'txt'];
                    if (!in_array($file_extension, $allowed_doc_types)) {
                        $error = 'Only PDF, DOC, DOCX, and TXT files are allowed for documents.';
                        break;
                    }
                    
                    if (move_uploaded_file($_FILES['document_url']['tmp_name'], $target_file)) {
                        $document_url = '/zanvarsity/html/uploads/publications/documents/' . $filename;
                    }
                }

                // Insert into database
                $stmt = $conn->prepare("INSERT INTO publications 
                    (title, author, description, publication_date, image_url, document_url, is_featured) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssi", 
                    $title,
                    $author,
                    $description,
                    $publication_date,
                    $image_url,
                    $document_url,
                    $is_featured
                );
                
                if ($stmt->execute()) {
                    $event_id = $conn->insert_id;
                    $_SESSION['success'] = 'Event added successfully!';
                    
                    // Redirect to prevent form resubmission
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    // If database insert fails, delete the uploaded file
                    if (!empty($image_url) && file_exists($target_file)) {
                        unlink($target_file);
                    }
                    $error = 'Failed to add event. Please try again. Error: ' . $conn->error;
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
}

// Publications will be loaded from the database

// Get user role display name
$role_display = '';
// Get all publications for display
$publications = [];
$publications_query = "SELECT * FROM publications ORDER BY publication_date DESC";
$result = $conn->query($publications_query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $publications[] = $row;
    }
}

// Get user role display name
switch($user_role) {
    case 'super_admin':
        $role_display = 'Super Admin';
        break;
    case 'admin':
        $role_display = 'Administrator';
        break;
    case 'editor':
        $role_display = 'Editor';
        break;
    case 'author':
        $role_display = 'Author';
        break;
    default:
        $role_display = 'User';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Manage Publications - Admin Panel</title>
    
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
        /* Custom styles for publications management */
        #publicationsGrid {
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
                                    <li><a href="manage_content.php"><i class="fa fa-file-text"></i> Manage Content</a></li>
                                    <?php endif; ?>
                                    <li><a href="/zanvarsity/html/my-courses.php"><i class="fa fa-book"></i> My Courses</a></li>
                                    <li><a href="/zanvarsity/html/my-profile.php"><i class="fa fa-user"></i> My Profile</a></li>
                                    <li><a href="/zanvarsity/html/change-password.php"><i class="fa fa-key"></i> Change Password</a></li>
                                    <li><a href="/zanvarsity/html/settings.php"><i class="fa fa-cog"></i> Settings</a></li>
                                </ul>
                            </div>
                            
                            <div class="sidebar-widget mt-4">
                                <ul class="nav nav-pills nav-stacked nav-dashboard ml-auto">
                                    <?php if (in_array($user_role, ['super_admin', 'admin'])): ?>
                                    <li class="active"><a href="manage_publications.php"><i class="fa fa-book-open"></i> Manage Publications</a></li>
                                    <?php endif; ?>
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
                                <h2><i class='bx bxs-book me-2'></i>Manage Publications</h2>
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addPublicationModal">
                                    <i class="fa fa-plus"></i> Add New Publication
                                </button>
                            </div>
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            <?php if (!empty($_SESSION['success'])): ?>
                                <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="publicationsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Cover</th>
                                                <th>Title</th>
                                                <th>Author</th>
                                                <th>Publication Date</th>
                                                <th>Featured</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($publications)): ?>
                                                <?php foreach ($publications as $publication): 
                                                    $pub_date = new DateTime($publication['publication_date']);
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?php if (!empty($publication['image_url'])): ?>
                                                            <img src="<?php echo $publication['image_url']; ?>" alt="Cover" class="img-thumbnail" style="width: 50px; height: 70px; object-fit: cover;">
                                                        <?php else: ?>
                                                            <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 70px;">
                                                                <i class="fas fa-book text-muted"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($publication['title']); ?></td>
                                                    <td><?php echo htmlspecialchars($publication['author']); ?></td>
                                                    <td><?php echo $pub_date->format('M j, Y'); ?></td>
                                                    <td>
                                                        <?php if ($publication['is_featured']): ?>
                                                            <span class="badge bg-success">
                                                                <i class="fa fa-star me-1"></i> Featured
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">No</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-1">
                                                            <button type="button" class="btn btn-sm btn-outline-primary view-publication" data-id="<?php echo $publication['id']; ?>" data-toggle="tooltip" title="View Details">
                                                                <i class="fa fa-eye"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-warning edit-publication" data-id="<?php echo $publication['id']; ?>" data-toggle="tooltip" title="Edit">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger delete-publication" data-id="<?php echo $publication['id']; ?>" data-toggle="tooltip" title="Delete">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-5">
                                                    <div class="text-muted">
                                                        <i class="fas fa-book-open fa-3x mb-3"></i>
                                                        <h5>No publications found</h5>
                                                        <p class="mb-0">Click the "Add New Publication" button to get started.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
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

<!-- Add Publication Modal -->
<div class="modal fade" id="addPublicationModal" tabindex="-1" role="dialog" aria-labelledby="addPublicationModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h2 class="modal-title h5" id="addPublicationModalLabel">Add New Publication</h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addPublicationForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_publication">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="author" class="form-label">Author <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="author" name="author" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="publication_date" class="form-label">Publication Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="publication_date" name="publication_date" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1">
                                <label class="form-check-label" for="is_featured">Feature this publication</label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Cover Image</h5>
                                    <div class="mb-3 text-center">
                                        <img id="coverPreview" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22280%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20200%20280%22%20preserveAspectRatio%3D%22none%22%3E%3Crect%20width%3D%22200%22%20height%3D%22280%22%20fill%3D%22%23e9ecef%22%3E%3C%2Frect%3E%3Ctext%20x%3D%22100%22%20y%3D%22140%22%20text-anchor%3D%22middle%22%20dominant-baseline%3D%22middle%22%20font-family%3D%22Arial%22%20font-size%3D%2214%22%20fill%3D%22%236c757d%22%3ECover%20Image%3C%2Ftext%3E%3C%2Fsvg%3E" 
                                         alt="Cover Preview" class="img-fluid mb-2" style="max-height: 200px; width: 100%; object-fit: cover;">
                                        <input type="file" class="form-control" id="image_url" name="image_url" accept="image/*">
                                        <div class="form-text">Recommended size: 800x1120px (3:4 ratio)</div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <h5 class="card-title">Document</h5>
                                        <div class="mb-3">
                                            <input type="file" class="form-control" id="document_url" name="document_url" accept=".pdf,.doc,.docx,.txt">
                                            <div class="form-text">PDF, DOC, DOCX, or TXT files only</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Publication</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Publication Modal -->
<div class="modal fade" id="viewPublicationModal" tabindex="-1" role="dialog" aria-labelledby="viewPublicationModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h2 class="modal-title h5" id="viewPublicationModalLabel">Publication Details</h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="publicationDetails">
                <!-- Publication details will be loaded here via AJAX -->
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading publication details...</p>
                </div>
                
                <!-- Publication Details Template (hidden by default, shown when data loads) -->
                <div id="publicationTemplate" style="display: none;">
                    <div class="publication-details">
                        <div class="row">
                            <div class="col-md-4 text-center mb-4">
                                <div id="publicationImage" class="mb-3">
                                    <!-- Image will be inserted here -->
                                </div>
                                <div id="publicationStatus" class="mb-3">
                                    <!-- Status badges will be inserted here -->
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h3 id="publicationTitle" class="h4 mb-3"></h3>
                                
                                <div class="mb-3">
                                    <h5 class="h6 text-muted mb-2">
                                        <i class="fa fa-user-edit me-2"></i>Author(s)
                                    </h5>
                                    <p id="publicationAuthor" class="mb-0"></p>
                                </div>
                                
                                <div class="mb-3">
                                    <h5 class="h6 text-muted mb-2">
                                        <i class="fa fa-calendar me-2"></i>Publication Date
                                    </h5>
                                    <p id="publicationDate" class="mb-0"></p>
                                </div>
                                
                                <div class="mb-3" id="publicationDescriptionContainer" style="display: none;">
                                    <h5 class="h6 text-muted mb-2">
                                        <i class="fa fa-align-left me-2"></i>Description
                                    </h5>
                                    <div id="publicationDescription" class="border rounded p-3 bg-light"></div>
                                </div>
                                
                                <div class="mb-3" id="publicationDocumentContainer" style="display: none;">
                                    <h5 class="h6 text-muted mb-2">
                                        <i class="fa fa-file me-2"></i>Document
                                    </h5>
                                    <a id="documentLink" href="#" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="fa fa-download me-1"></i> Download Document
                                    </a>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                    <small id="publicationMeta" class="text-muted"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                <a href="#" id="viewDocumentBtn" class="btn btn-primary" target="_blank" style="display: none;">
                    <i class="fas fa-file-alt me-1"></i> View Document
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Edit Publication Modal -->
<div class="modal fade" id="editPublicationModal" tabindex="-1" role="dialog" aria-labelledby="editPublicationModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h2 class="modal-title h5" id="editPublicationModalLabel">Edit Publication</h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editPublicationForm" method="POST" enctype="multipart/form-data" action="save_publication.php">
                <input type="hidden" name="action" value="update_publication">
                <input type="hidden" name="publication_id" id="editPublicationId">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="editTitle" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editTitle" name="title" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editAuthor" class="form-label">Author <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editAuthor" name="author" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="editDescription" name="description" rows="4"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editPublicationDate" class="form-label">Publication Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="editPublicationDate" name="publication_date" required>
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="editIsFeatured" name="is_featured" value="1">
                                <label class="form-check-label" for="editIsFeatured">Feature this publication</label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div id="currentImage">
                                <!-- Current image will be displayed here -->
                            </div>
                            
                            <div class="mb-3">
                                <label for="editImageUrl" class="form-label">Update Image</label>
                                <input type="file" class="form-control" id="editImageUrl" name="image_url" accept="image/*">
                                <div class="form-text">Leave empty to keep current image</div>
                            </div>
                            
                            <div class="alert alert-info small">
                                <i class='bx bx-info-circle me-1'></i>
                                Recommended size: 800x450px (16:9 aspect ratio)
                            </div>
                            
                            <div id="currentDocument">
                                <!-- Current document will be displayed here -->
                            </div>
                            
                            <div class="mb-3">
                                <label for="editDocumentUrl" class="form-label">Update Document</label>
                                <input type="file" class="form-control" id="editDocumentUrl" name="document_url" accept=".pdf,.doc,.docx,.txt">
                                <div class="form-text">Leave empty to keep current document</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Changes</button>
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
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this publication? This action cannot be undone and will permanently remove the publication and all associated files.</p>
                <input type="hidden" id="deletePublicationId" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class='bx bx-x-circle me-1'></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class='bx bx-trash me-1'></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    // Wait for jQuery to be ready
    $(document).ready(function() {
        // Initialize DataTable
        const publicationsTable = $('#publicationsTable').DataTable({
            responsive: true,
            order: [[3, 'desc']], // Sort by date by default
            columnDefs: [
                { orderable: false, targets: [0, 4, 5] }, // Disable sorting on image, featured, and actions columns
                { searchable: false, targets: [0, 3, 4, 5] } // Disable search on image, date, featured, and actions columns
            ],
            language: {
                search: "Search publications:",
                zeroRecords: "No matching publications found",
                info: "Showing _START_ to _END_ of _TOTAL_ publications",
                infoEmpty: "No publications available",
                infoFiltered: "(filtered from _MAX_ total publications)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            drawCallback: function() {
                // Reinitialize tooltips after table redraw
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });

        // Store the DataTable instance for later use
        window.publicationsTable = publicationsTable;
        
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>

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
            <span class="sr-only">${text}</span>
        `);
        return originalText;
    }
    
    // Function to reset button state
    function resetButton(button, originalText) {
        button.prop('disabled', false).html(originalText);
    }

    $(document).ready(function() {
        // Handle success/error messages
        <?php if (!empty($_SESSION['success'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?php echo addslashes($_SESSION['success']); ?>',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo addslashes($error); ?>',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000
            });
        <?php endif; ?>

        // Initialize date picker for publication date
        $('#publication_date, #editPublicationDate').flatpickr({
            dateFormat: 'Y-m-d',
            allowInput: true
        });

        // Handle file input change for cover image
        $('#image_url, #editImageUrl').on('change', function() {
            const file = this.files[0];
            const preview = $(this).closest('form').find('img[id$="Preview"]');
            const fileLabel = $(this).siblings('.custom-file-label');
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.attr('src', e.target.result);
                }
                
                reader.readAsDataURL(file);
                fileLabel.text(file.name);
            } else {
                fileLabel.text('Choose file');
            }
        });

        // Handle document file input change
        $('#document_url, #editDocumentUrl').on('change', function() {
            const file = this.files[0];
            const fileLabel = $(this).siblings('.custom-file-label');
            
            if (file) {
                fileLabel.text(file.name);
            } else {
                fileLabel.text('Choose file');
            }
        });

        // View publication details
        $(document).off('click', '.view-publication').on('click', '.view-publication', function(e) {
            e.preventDefault();
            const pubId = $(this).data('id');
            
            // Show loading state
            const button = $(this);
            const originalText = showLoading(button, 'Loading...');
            
            // Fetch publication details via AJAX
            $.ajax({
                url: 'ajax/get_publication.php',
                type: 'GET',
                data: { id: pubId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const pub = response.data;
                        const pubDate = new Date(pub.publication_date);
                        
                        // Update the template with publication data
                        const template = $('#publicationTemplate').html();
                        $('#publicationDetails').html(template);
                        
                        // Show/hide loading spinner
                        $('.spinner-border, .text-center p').hide();
                        $('#publicationTemplate').show();
                        
                        // Set publication details
                        $('#publicationTitle').text(pub.title);
                        $('#publicationAuthor').text(pub.author);
                        $('#publicationDate').text(pubDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }));
                        
                        // Set publication image or placeholder
                        const imageContainer = $('#publicationImage');
                        if (pub.image_url) {
                            imageContainer.html(`
                                <img src="${pub.image_url}" 
                                     alt="${pub.title}" 
                                     class="img-fluid rounded shadow" 
                                     style="max-height: 300px; width: 100%; object-fit: contain;">
                            `);
                        } else {
                            imageContainer.html(`
                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                     style="height: 200px; width: 100%;">
                                    <i class="fa fa-book text-muted fa-4x"></i>
                                </div>
                            `);
                        }
                        
                        // Set featured status
                        const statusContainer = $('#publicationStatus');
                        if (pub.is_featured) {
                            statusContainer.html(`
                                <span class="badge bg-success">
                                    <i class="fa fa-star me-1"></i> Featured
                                </span>
                            `);
                        } else {
                            statusContainer.html(`
                                <span class="badge bg-secondary">
                                    <i class="fa fa-bookmark me-1"></i> Regular
                                </span>
                            `);
                        }
                        
                        // Set description if available
                        if (pub.description) {
                            $('#publicationDescription').html(pub.description);
                            $('#publicationDescriptionContainer').show();
                        }
                        
                        // Set document link if available
                        const viewDocBtn = $('#viewDocumentBtn');
                        if (pub.document_url) {
                            $('#documentLink').attr('href', pub.document_url);
                            $('#publicationDocumentContainer').show();
                            viewDocBtn.attr('href', pub.document_url).show();
                        } else {
                            viewDocBtn.hide();
                        }
                        
                        // Set metadata
                        const createdAt = new Date(pub.created_at);
                        $('#publicationMeta').html(`
                            <i class="fa fa-calendar-plus me-1"></i> Added: ${createdAt.toLocaleString()}
                        `);
                        
                        $('#viewPublicationModal').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to load publication details'
                        });
                    }
                    
                    resetButton(button, originalText);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching publication:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load publication details. Please try again.'
                    });
                    resetButton(button, originalText);
                }
            });
        });

        // Edit publication
        $(document).off('click', '.edit-publication').on('click', '.edit-publication', function() {
            const pubId = $(this).data('id');
            
            // Show loading state
            const button = $(this);
            const originalText = showLoading(button, 'Loading...');
            
            // Fetch publication details via AJAX
            $.ajax({
                url: 'ajax/get_publication.php',
                type: 'GET',
                data: { id: pubId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const pub = response.data;
                        
                        // Populate form fields
                        $('#editPublicationId').val(pub.id);
                        $('#editTitle').val(pub.title);
                        $('#editAuthor').val(pub.author);
                        $('#editDescription').val(pub.description || '');
                        $('#editPublicationDate').val(pub.publication_date);
                        
                        // Set featured checkbox
                        if (parseInt(pub.is_featured) === 1) {
                            $('#editIsFeatured').prop('checked', true);
                        } else {
                            $('#editIsFeatured').prop('checked', false);
                        }
                        
                        // Show current cover image if exists
                        const coverPreview = $('#editCoverPreview');
                        if (pub.image_url) {
                            coverPreview.attr('src', pub.image_url);
                        } else {
                            coverPreview.attr('src', 'data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22280%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20200%20280%22%20preserveAspectRatio%3D%22none%22%3E%3Crect%20width%3D%22200%22%20height%3D%22280%22%20fill%3D%22%23e9ecef%22%3E%3C%2Frect%3E%3Ctext%20x%3D%22100%22%20y%3D%22140%22%20text-anchor%3D%22middle%22%20dominant-baseline%3D%22middle%22%20font-family%3D%22Arial%22%20font-size%3D%2214%22%20fill%3D%22%236c757d%22%3ECover%20Image%3C%2Ftext%3E%3C%2Fsvg%3E');
                        }
                        
                        // Show current document if exists
                        const currentDoc = $('#currentDocument');
                        if (pub.document_url) {
                            const docName = pub.document_url.split('/').pop();
                            currentDoc.html(`
                                <strong>Current document:</strong> 
                                <a href="${pub.document_url}" target="_blank">${docName}</a>
                            `);
                        } else {
                            currentDoc.html('<span class="text-muted">No document uploaded</span>');
                        }
                        
                        // Show the edit modal
                        $('#editPublicationModal').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to load publication details for editing'
                        });
                    }
                    
                    resetButton(button, originalText);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching publication for edit:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load publication details. Please try again.'
                    });
                    resetButton(button, originalText);
                }
            });
        });
        
        // Handle add publication form submission
        $('#addPublicationForm').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const formData = new FormData(this);
            const submitButton = form.find('button[type="submit"]');
            const originalText = showLoading(submitButton, 'Saving...');
            
            // Add CSRF token to form data if not already present
            if (!formData.has('csrf_token')) {
                formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
            }
            
            // Add is_featured value
            formData.append('is_featured', $('#is_featured').is(':checked') ? '1' : '0');
            
            $.ajax({
                url: 'save_publication.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Close the modal
                        $('#addPublicationModal').modal('hide');
                        
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message || 'Publication added successfully',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        
                        // Reload the page to reflect changes
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to add publication'
                        });
                    }
                    
                    resetButton(submitButton, originalText);
                },
                error: function(xhr, status, error) {
                    console.error('Error adding publication:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while adding the publication. Please try again.'
                    });
                    resetButton(submitButton, originalText);
                }
            });
        });
        
        // Handle edit publication form submission
        $('#editPublicationForm').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const formData = new FormData(this);
            const submitButton = form.find('button[type="submit"]');
            const originalText = showLoading(submitButton, 'Updating...');
            
            // Add CSRF token to form data if not already present
            if (!formData.has('csrf_token')) {
                formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
            }
            
            // Add is_featured value
            formData.append('is_featured', $('#editIsFeatured').is(':checked') ? '1' : '0');
            
            $.ajax({
                url: 'save_publication.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Close the modal
                        $('#editPublicationModal').modal('hide');
                        
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message || 'Publication updated successfully',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        
                        // Reload the page to reflect changes
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to update publication'
                        });
                    }
                    
                    resetButton(submitButton, originalText);
                },
                error: function(xhr, status, error) {
                    console.error('Error updating publication:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating the publication. Please try again.'
                    });
                    resetButton(submitButton, originalText);
                }
            });
        });
        
        // Handle delete publication with confirmation
        $(document).off('click', '.delete-publication').on('click', '.delete-publication', function(e) {
            e.preventDefault();
            
            const button = $(this);
            const pubId = button.data('id');
            const row = button.closest('tr');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const originalText = showLoading(button, 'Deleting...');
                    
                    // Send delete request
                    $.ajax({
                        url: 'delete_publication.php',
                        type: 'POST',
                        data: {
                            id: pubId,
                            csrf_token: '<?php echo $_SESSION['csrf_token']; ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                // Remove row from DataTable
                                if (typeof window.publicationsTable !== 'undefined') {
                                    window.publicationsTable.row(row).remove().draw(false);
                                } else {
                                    // Fallback if DataTable not initialized
                                    row.fadeOut(300, function() {
                                        $(this).remove();
                                    });
                                }
                                
                                // Show success message
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message || 'Publication has been deleted.',
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                                
                                // If no publications left, show a message
                                const rowCount = window.publicationsTable ? 
                                    window.publicationsTable.rows().count() : 
                                    $('#publicationsTable tbody tr').length;
                                
                                if (rowCount === 0) {
                                    const noPubsHtml = `
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="fas fa-book-open fa-3x mb-3"></i>
                                                    <h5>No publications found</h5>
                                                    <p class="mb-0">Click the "Add New Publication" button to get started.</p>
                                                </div>
                                            </td>
                                        </tr>`;
                                    
                                    if (typeof window.publicationsTable !== 'undefined') {
                                        window.publicationsTable.destroy();
                                    }
                                    $('#publicationsTable tbody').html(noPubsHtml);
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to delete publication'
                                });
                            }
                            
                            resetButton(button, originalText);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error deleting publication:', error);
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while deleting the publication. Please try again.'
                            });
                            
                            resetButton(button, originalText);
                        }
                    });
                }
            });
        });
        
        // Enhanced tooltips with better styling
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            // Add custom class for better styling
            tooltipTriggerEl.setAttribute('data-bs-placement', 'top');
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Initialize popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
        tooltipTriggerEl.setAttribute('data-bs-placement', 'top');
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
        });
    });
</script>

<style>
    /* Action buttons styling */
    .btn-action {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border-radius: 4px;
        transition: all 0.2s ease-in-out;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .btn-action i {
        font-size: 14px;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }
    
    /* Custom tooltip styling */
    .tooltip {
        font-size: 0.8rem;
    }
    
    .tooltip-inner {
        padding: 0.25rem 0.5rem;
        background-color: #333;
    }
    
    .bs-tooltip-auto[data-popper-placement^=top] .tooltip-arrow::before, 
    .bs-tooltip-top .tooltip-arrow::before {
        border-top-color: #333;
    }
    
    /* Custom styles specific to manage_publications.php */
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
