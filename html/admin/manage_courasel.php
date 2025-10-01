<?php
// Start session and generate CSRF token if not exists
session_start();

// Generate CSRF token if it doesn't exist
if (empty($_SESSION['csrf_token'])) {
    if (function_exists('random_bytes')) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } else {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}

// Define root path
define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));

// Include necessary files
require_once ROOT_PATH . '/includes/auth_functions.php';
require_once ROOT_PATH . '/includes/database.php';

// Check if user is logged in and is admin
require_login();

// Get database connection
$conn = $GLOBALS['conn'] ?? null;

// Check if carousel table exists, if not create it with the correct schema
$table_check = $conn->query("SHOW TABLES LIKE 'carousel'");
if ($table_check->num_rows == 0) {
    $create_table_sql = "
    CREATE TABLE IF NOT EXISTS `carousel` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `description` text DEFAULT NULL,
        `image_url` varchar(255) NOT NULL,
        `button_text` varchar(100) DEFAULT 'Learn More',
        `button_url` varchar(255) DEFAULT '#',
        `is_active` tinyint(1) DEFAULT 1,
        `sort_order` int(11) DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    
    if (!$conn->multi_query($create_table_sql)) {
        die("Error creating carousel table: " . $conn->error);
    }
    
    // Clear any remaining results from multi_query
    while ($conn->more_results() && $conn->next_result()) {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    }
}

// Initialize response array
$response = ['success' => false, 'message' => ''];

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
            case 'add_carousel':
                // Validate required fields
                if (empty($_POST['title']) || empty($_FILES['image']['name'])) {
                    $error = 'Title and Image are required';
                    break;
                }
                
                // Process image upload
                $upload_dir = ROOT_PATH . '/html/uploads/carousel/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (!in_array($file_extension, $allowed_extensions)) {
                    $error = 'Only JPG, JPEG, PNG & GIF files are allowed.';
                    break;
                }
                
                $filename = 'carousel_' . time() . '_' . uniqid() . '.' . $file_extension;
                $target_file = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image_url = '/zanvarsity/html/uploads/carousel/' . $filename;
                    
                    // Prepare the insert statement
                    $stmt = $conn->prepare("INSERT INTO carousel 
                        (title, description, image_url, button_text, button_url, is_active, sort_order) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)");
                    
                    if ($stmt === false) {
                        unlink($target_file); // Clean up uploaded file
                        $error = 'Failed to prepare statement: ' . $conn->error;
                        break;
                    }
                    
                    $title = trim($conn->real_escape_string($_POST['title']));
                    $description = trim($conn->real_escape_string($_POST['description'] ?? ''));
                    $button_text = trim($conn->real_escape_string($_POST['button_text'] ?? 'Learn More'));
                    $button_url = trim($conn->real_escape_string($_POST['button_url'] ?? '#'));
                    $is_active = isset($_POST['is_active']) ? 1 : 0;
                    $sort_order = (int)($_POST['sort_order'] ?? 0);
                    
                    $stmt->bind_param("sssssii", 
                        $title, 
                        $description, 
                        $image_url, 
                        $button_text, 
                        $button_url, 
                        $is_active, 
                        $sort_order
                    );
                    
                    if ($stmt->execute()) {
                        $success = 'Carousel item added successfully!';
                    } else {
                        unlink($target_file);
                        $error = 'Failed to add carousel item: ' . $stmt->error;
                    }
                    
                    $stmt->close();
                } else {
                    $error = 'Error uploading file. Please try again.';
                }
                break;
                
            case 'delete_carousel':
                if (empty($_POST['id'])) {
                    $error = 'Invalid request';
                    break;
                }
                
                $id = (int)$_POST['id'];
                
                // Get image path before deleting
                $result = $conn->query("SELECT image_url FROM carousel WHERE id = $id");
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $image_url = ROOT_PATH . str_replace('/zanvarsity', '', $row['image_url']);
                    
                    // Delete from database
                    if ($conn->query("DELETE FROM carousel WHERE id = $id")) {
                        // Delete the image file
                        if (file_exists($image_url)) {
                            unlink($image_url);
                        }
                        $success = 'Carousel item deleted successfully!';
                    } else {
                        $error = 'Failed to delete carousel item: ' . $conn->error;
                    }
                }
                break;
                
            case 'toggle_status':
                if (empty($_POST['id'])) {
                    $error = 'Invalid request';
                    break;
                }
                
                $id = (int)$_POST['id'];
                $conn->query("UPDATE carousel SET is_active = NOT is_active WHERE id = $id");
                $success = 'Status updated successfully!';
                break;
        }
    }
}

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

// Get all carousel items
$carousel_items = $conn->query("SELECT * FROM carousel ORDER BY sort_order ASC, created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title><?php echo $page_title; ?> - Admin Panel</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
    
    <!-- Alternative Font Awesome CDN -->
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
          integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" 
          crossorigin="anonymous" 
          referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/zanvarsity/html/assets/bootstrap/css/bootstrap.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/selectize.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/owl.carousel.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/vanillabox/vanillabox.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/style.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/green-theme.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/admin-theme.css" type="text/css">
    
    <style>
        /* Custom styles for carousel management */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
            padding: 20px;
        }
        
        .carousel-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid #e9ecef;
            overflow: hidden;
        }
        
        .carousel-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        }
        
        .card-body {
            padding: 1.25rem;
        }
        
        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            padding-left: 0.5rem;
        }
        
        .card-text {
            padding: 0 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #6c757d;
            line-height: 1.5;
        }
        
        /* Action Buttons Container */
        .action-buttons {
            padding: 1rem;
            border-top: 1px solid #e9ecef;
            background-color: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* Order Badge */
        .sort-order-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            background-color: #e9ecef;
            color: #495057;
            font-weight: 500;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0 auto;
        }
        
        /* Button Group */
        .btn-group {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-group .btn {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        
        .btn-group .btn i {
            font-size: 1.1rem;
        }
        
        .btn-edit {
            color: #0d6efd;
            border: 2px solid #0d6efd;
            background-color: white;
        }
        
        .btn-edit:hover {
            background-color: #0d6efd;
            transform: translateY(-2px);
        }
        
        .btn-delete {
            color: #dc3545;
            border: 2px solid #dc3545;
            background-color: white;
        }
        
        .btn-delete:hover {
            background-color: #dc3545;
            transform: translateY(-2px);
        }
        
        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1;
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
        
        @media (max-width: 992px) {
            .card-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                padding: 15px;
            }
        }
        
        /* Ensure Font Awesome icons are properly aligned */
        .action-buttons .btn i {
            margin-right: 5px;
        }
        
        /* Style for status badges */
        .status-badge {
            font-size: 0.8rem;
            padding: 0.35em 0.65em;
            margin-right: 5px;
        }
        
        /* Style for sort order badge */
        .sort-order-badge {
            font-size: 0.8rem;
            padding: 0.5em 0.8em;
            margin-right: 10px;
            border: 1px solid #dee2e6;
        }
        
        /* Button group spacing */
        .btn-group .btn {
            margin-right: 5px;
        }
        
        /* Ensure consistent icon sizes */
        .fas, .far, .fab {
            width: 1em;
            text-align: center;
        }
        
        /* Hover effects for buttons */
        .btn-edit:hover {
            background-color: #0d6efd;
            color: white;
        }
        
        .btn-delete:hover {
            background-color: #dc3545;
            color: white;
        }
        
        /* Ensure proper spacing in the action buttons container */
        .action-buttons {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .btn-group {
                width: 100%;
                margin-top: 5px;
            }
            
            .btn-group .btn {
                flex: 1;
                text-align: center;
            }
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
                                <h2><i class='bx bx-slider me-2'></i>Manage Carousel</h2>
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addCarouselModal">
                                    <i class='bx bx-plus me-1'></i> Add New Carousel Item
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

                        <!-- Carousel Items Grid -->
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="card-grid" id="carouselGrid">
                                    <?php if (empty($carousel_items)): ?>
                                        <div class="col-12 text-center py-5">
                                            <div class="alert alert-info mb-0">No carousel items found. Add your first item using the button above.</div>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($carousel_items as $item): ?>
                                            <div class="card carousel-card h-100">
                                                <!-- Status Badge -->
                                                <?php if ($item['is_active']): ?>
                                                    <span class="badge bg-success status-badge">
                                                        <i class="fas fa-check-circle me-1"></i> Active
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary status-badge">
                                                        <i class="fas fa-pause-circle me-1"></i> Inactive
                                                    </span>
                                                <?php endif; ?>
                                                
                                                <!-- Image -->
                                                <div class="carousel-img-container" style="height: 180px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; padding: 15px;">
                                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                                         alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                                         class="img-fluid" 
                                                         style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                                </div>
                                                
                                                <!-- Card Body -->
                                                <div class="card-body d-flex flex-column">
                                                    <!-- Title -->
                                                    <h5 class="card-title">
                                                        <?php echo htmlspecialchars($item['title']); ?>
                                                    </h5>
                                                    
                                                    <!-- Description -->
                                                    <?php if (!empty($item['description'])): ?>
                                                        <div class="card-text">
                                                            <?php echo nl2br(htmlspecialchars($item['description'])); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <!-- Action Buttons -->
                                                <div class="action-buttons">
                                                    <span class="badge bg-light text-dark sort-order-badge">
                                                        <i class="fas fa-sort-numeric-down me-1"></i>
                                                        <span><?php echo (int)$item['sort_order']; ?></span>
                                                    </span>
                                                    <div class="btn-group">
                                                        <button type="button" 
                                                                class="btn btn-sm btn-edit edit-item" 
                                                                data-id="<?php echo $item['id']; ?>"
                                                                title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-delete delete-item" 
                                                                data-id="<?php echo $item['id']; ?>"
                                                                data-title="<?php echo htmlspecialchars($item['title']); ?>"
                                                                title="Delete">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
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

<!-- Add Carousel Modal -->
<div class="modal fade" id="addCarouselModal" tabindex="-1" role="dialog" aria-labelledby="addCarouselModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" value="add_carousel">
                
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addCarouselModalLabel">Add New Carousel Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image">Image <span class="text-danger">*</span></label>
                        <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
                        <small class="form-text text-muted">Recommended size: 1920x800px (max 5MB)</small>
                        <div id="imagePreview" class="mt-2"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="button_text">Button Text</label>
                                <input type="text" class="form-control" id="button_text" name="button_text" value="Learn More">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="button_url">Button URL</label>
                                <input type="url" class="form-control" id="button_url" name="button_url" value="#">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sort_order">Sort Order</label>
                                <input type="number" class="form-control" id="sort_order" name="sort_order" value="0" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-4 pt-2">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Carousel Modal -->
<div class="modal fade" id="editCarouselModal" tabindex="-1" role="dialog" aria-labelledby="editCarouselModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editCarouselForm" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" value="update_carousel">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editCarouselModalLabel">Edit Carousel Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_title">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_description">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_image">Image</label>
                        <input type="file" class="form-control-file" id="edit_image" name="image" accept="image/*">
                        <small class="form-text text-muted">Leave blank to keep current image</small>
                        <div id="currentImage" class="mt-2"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_button_text">Button Text</label>
                                <input type="text" class="form-control" id="edit_button_text" name="button_text">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_button_url">Button URL</label>
                                <input type="url" class="form-control" id="edit_button_url" name="button_url">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_sort_order">Sort Order</label>
                                <input type="number" class="form-control" id="edit_sort_order" name="sort_order" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-4 pt-2">
                                <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                                <label class="form-check-label" for="edit_is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
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
<script src="/zanvarsity/html/assets/js/jquery.vanillabox-0.1.5.min.js"></script>
<script src="/zanvarsity/html/assets/js/countdown.min.js"></script>
<script src="/zanvarsity/html/assets/js/jquery.equalheights.min.js"></script>
<script src="/zanvarsity/html/assets/js/custom.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Handle edit carousel button click
$(document).on('click', '.edit-item', function() {
    var id = $(this).data('id');
    
    // Show loading state
    $('#editCarouselForm input, #editCarouselForm textarea, #editCarouselForm button').prop('disabled', true);
    $('#editCarouselModal .modal-title').html('Loading...');
    
    // Make AJAX request to get carousel item data
    $.ajax({
        url: '/zanvarsity/html/admin/get_carousel_item.php',
        type: 'GET',
        dataType: 'json',
        data: { id: id },
        success: function(response) {
            if (response.success && response.data) {
                var item = response.data;
                
                // Populate the form with carousel item data
                $('#edit_id').val(item.id);
                $('#edit_title').val(item.title);
                $('#edit_description').val(item.description);
                $('#edit_button_text').val(item.button_text);
                $('#edit_button_url').val(item.button_url);
                $('#edit_sort_order').val(item.sort_order);
                $('#edit_is_active').prop('checked', item.is_active);
                
                // Handle image preview
                if (item.image_url) {
                    $('#currentImage').html('<img src="' + item.image_url + '" class="img-fluid mt-2" style="max-height: 200px;">');
                } else {
                    $('#currentImage').html('<div class="text-muted">No image</div>');
                }
                
                // Show the modal
                $('#editCarouselModal').modal('show');
                
                // Enable form fields
                $('#editCarouselForm input, #editCarouselForm textarea, #editCarouselForm button').prop('disabled', false);
                $('#editCarouselModal .modal-title').html('Edit Carousel Item');
                
            } else {
                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Failed to load carousel item data',
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function(xhr, status, error) {
            console.log('AJAX Error:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            
            let errorMessage = 'An error occurred while deleting the item.';
            
            // Try to extract error message from response
            if (xhr.responseText) {
                // Try to parse as JSON first
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMessage = response.message || errorMessage;
                } catch (e) {
                    // If not JSON, show raw response (trimmed)
                    const rawText = xhr.responseText.trim();
                    // Remove any HTML tags for cleaner error message
                    errorMessage = $('<div>').html(rawText).text().substring(0, 200);
                    if (rawText.length > 200) errorMessage += '...';
                }
            }
            
            resolve({
                success: false,
                message: errorMessage,
                rawResponse: xhr.responseText
            });
        }
    });
});

// Update other AJAX calls to use full paths
$('#addCarouselForm').on('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    $.ajax({
        url: '/zanvarsity/html/admin/add_carousel.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            // Handle response
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Failed to add carousel item: ' + error, 'error');
        }
    });
});

// Fix for equalHeights if needed
$(document).ready(function() {
    if (typeof $.fn.equalHeights === 'function') {
        $('.equal-height').equalHeights();
    }
});

// Delete carousel item
$(document).on('click', '.delete-item', function() {
    const id = $(this).data('id');
    const title = $(this).data('title');
    
    // Get CSRF token from meta tag
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    // Debug: Log the CSRF token
    console.log('CSRF Token:', csrfToken);
    
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete "${title}". This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return new Promise((resolve) => {
                // Show loading state
                Swal.showLoading();
                
                // Make the AJAX request
                $.ajax({
                    url: '/zanvarsity/html/admin/delete_carousel.php',
                    type: 'POST',
                    dataType: 'text',
                    data: {
                        id: id,
                        csrf_token: csrfToken
                    },
                    success: function(response) {
                        console.log('Raw response:', response);
                        
                        let jsonResponse;
                        try {
                            // Try to parse the response as JSON
                            jsonResponse = JSON.parse(response);
                        } catch (e) {
                            console.error('Failed to parse JSON response:', e);
                            console.log('Response text:', response);
                            throw new Error('Invalid JSON response from server');
                        }
                        
                        console.log('Parsed response:', jsonResponse);
                        resolve(jsonResponse);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            error: error
                        });
                        
                        let errorMessage = 'An error occurred while deleting the item.';
                        
                        // Try to parse error response if it's JSON
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            errorMessage = errorResponse.message || errorMessage;
                        } catch (e) {
                            // If not JSON, use the raw response
                            if (xhr.responseText) {
                                errorMessage = 'Server responded with: ' + xhr.responseText.substring(0, 200);
                            }
                        }
                        
                        resolve({
                            success: false,
                            message: errorMessage,
                            rawResponse: xhr.responseText
                        });
                    }
                });
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value && result.value.success) {
                Swal.fire({
                    title: 'Deleted!',
                    text: 'The carousel item has been deleted.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Reload the page to see changes
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: result.value ? result.value.message : 'Failed to delete the carousel item.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        }
    });
});

// Handle form submission for adding new carousel item
$('#addCarouselForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    $.ajax({
        url: '/zanvarsity/html/admin/add_carousel.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            const result = typeof response === 'string' ? JSON.parse(response) : response;
            if (result.success) {
                Swal.fire('Success', result.message || 'Carousel item added successfully', 'success')
                    .then(() => {
                        window.location.reload();
                    });
            } else {
                Swal.fire('Error', result.message || 'Failed to add carousel item', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Failed to add carousel item: ' + error, 'error');
        }
    });
});

// Handle form submission for editing carousel item
$('#editCarouselForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    $.ajax({
        url: 'update_carousel.php',  
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',  
        success: function(response) {
            if (response.success) {
                Swal.fire('Success', response.message || 'Carousel item updated successfully', 'success')
                    .then(() => {
                        window.location.reload();
                    });
            } else {
                Swal.fire('Error', response.message || 'Failed to update carousel item', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            console.error('Response:', xhr.responseText);
            Swal.fire('Error', 'Failed to update carousel item. Please check console for details.', 'error');
        }
    });
});

// Image preview for add form
$('#image').on('change', function(e) {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#imagePreview').html(`
                <strong>Preview:</strong><br>
                <img src="${e.target.result}" class="img-fluid mt-2" style="max-height: 100px;">
            `).show();
        }
        reader.readAsDataURL(file);
    } else {
        $('#imagePreview').hide();
    }
});

// Image preview for edit form
$('#edit_image').on('change', function(e) {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#currentImage').html(`
                <strong>New Image Preview:</strong><br>
                <img src="${e.target.result}" class="img-fluid mt-2" style="max-height: 100px;">
            `).show();
        }
        reader.readAsDataURL(file);
    } else {
        $('#currentImage').hide();
    }
});

// Initialize tooltips
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

// Debug script for Font Awesome
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded');
    
    // Check if Font Awesome is loaded
    const styleSheets = Array.from(document.styleSheets);
    const hasFontAwesome = styleSheets.some(sheet => {
        try {
            return sheet.href && sheet.href.includes('font-awesome');
        } catch (e) {
            return false;
        }
    });
    
    console.log('Font Awesome loaded:', hasFontAwesome);
    
    // Check if Font Awesome font is loaded
    const testIcon = document.createElement('span');
    testIcon.className = 'fas fa-check';
    testIcon.style.position = 'absolute';
    testIcon.style.left = '-9999px';
    document.body.appendChild(testIcon);
    
    setTimeout(() => {
        const iconWidth = testIcon.offsetWidth;
        console.log('Icon width:', iconWidth);
        document.body.removeChild(testIcon);
        
        if (iconWidth === 0) {
            console.error('Font Awesome font failed to load');
        }
    }, 100);
});
</script>

</body>
</html>
