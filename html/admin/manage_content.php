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

$error = '';
$success = '';

// Include header
$page_title = 'Content Management Dashboard';
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
                        <li class="active">Content Management</li>
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
                                        <h4><?php echo !empty($first_name) ? htmlspecialchars($first_name) : 'hassan'; ?></h4>
                                        <span class="label label-primary"><?php echo ucfirst($user_role); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="sidebar-widget">
                                <ul class="nav nav-pills nav-stacked nav-dashboard">
                                    <li><a href="/zanvarsity/html/my-account.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                                    <?php if (in_array($user_role, ['super_admin', 'admin'])): ?>
                                    <li><a href="users.php"><i class="fa fa-users"></i> Manage Users</a></li>
                                    <li class="active"><a href="manage_content.php"><i class="fa fa-file-text"></i> Manage Contents</a></li>
                                    <?php endif; ?>
                                    <li><a href="/zanvarsity/html/my-courses.php"><i class="fa fa-book"></i> My Courses</a></li>
                                    <li><a href="/zanvarsity/html/my-profile.php"><i class="fa fa-user"></i> My Profile</a></li>
                                    <li><a href="/zanvarsity/html/settings.php"><i class="fa fa-cog"></i> Settings</a></li>
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
                    <section class="block">
                        <div class="page-title">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h2><i class='bx bxs-dashboard me-2'></i>Content Management Dashboard</h2>
                            </div>
                        </div>

                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $success; ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $error; ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <!-- Events Management -->
                            <div class="col-md-4 col-sm-6">
                                <div class="dashboard-card card h-100">
                                    <div class="card-body text-center">
                                        <div class="card-icon">
                                            <i class='bx bx-calendar-event'></i>
                                        </div>
                                        <h5 class="card-title">Events Management</h5>
                                        <p class="card-text">Manage university events, workshops, and important dates.</p>
                                    </div>
                                    <div class="card-footer text-center">
                                        <a href="manage_events.php" class="btn btn-success btn-sm">
                                            <i class='bx bx-edit me-1 '></i> Manage Events
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Announcements -->
                            <div class="col-md-4 col-sm-6">
                                <div class="dashboard-card card h-100">
                                    <div class="card-body text-center">
                                        <div class="card-icon">
                                            <i class='bx bx-megaphone'></i>
                                        </div>
                                        <h5 class="card-title">Announcements</h5>
                                        <p class="card-text">Create and manage important university announcements.</p>
                                    </div>
                                    <div class="card-footer text-center">
                                        <a href="manage_announcements.php" class="btn btn-success btn-sm">
                                            <i class='bx bx-edit me-1'></i> Manage Announcements
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Publications -->
                            <div class="col-md-4 col-sm-6">
                                <div class="dashboard-card card h-100">
                                    <div class="card-body text-center">
                                        <div class="card-icon">
                                            <i class='bx bx-book-open'></i>
                                        </div>
                                        <h5 class="card-title">Publications</h5>
                                        <p class="card-text">Manage research papers, articles, and publications.</p>
                                    </div>
                                    <div class="card-footer text-center">
                                        <a href="manage_publications.php" class="btn btn-success btn-sm">
                                            <i class='bx bx-edit me-1'></i> Manage Publications
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Downloads -->
                            <div class="col-md-4 col-sm-6">
                                <div class="dashboard-card card h-100">
                                    <div class="card-body text-center">
                                        <div class="card-icon">
                                            <i class='bx bx-download'></i>
                                        </div>
                                        <h5 class="card-title">Downloads</h5>
                                        <p class="card-text">Manage downloadable resources and documents.</p>
                                    </div>
                                    <div class="card-footer text-center">
                                        <a href="manage_downloads.php" class="btn btn-success btn-sm">
                                            <i class='bx bx-edit me-1'></i> Manage Downloads
                                        </a>
                                    </div>
                                </div>
                            </div>

                              <!-- Staff Management -->
                            <div class="col-md-4 col-sm-6">
                                <div class="dashboard-card card h-100">
                                    <div class="card-body text-center">
                                        <div class="card-icon">
                                            <i class='bx bx-group'></i>
                                        </div>
                                        <h3 class="card-title">Manage Staff</h3>
                                        <p class="card-text">Add, edit, or remove staff members and manage their details.</p>
                                        <a href="manage_staff.php" class="btn btn-primary btn-sm">
                                            <i class='bx bx-edit-alt me-1'></i> Manage Staff
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- VC Notice -->
                            <div class="col-md-4 col-sm-6">
                                <div class="dashboard-card card h-100">
                                    <div class="card-body text-center">
                                        <div class="card-icon">
                                            <i class='bx bx-book-open'></i>
                                        </div>
                                        <h5 class="card-title">Vc Notice</h5>
                                        <p class="card-text">Manage Vive Chancellor Message.</p>
                                    </div>
                                    <div class="card-footer text-center">
                                        <a href="manage_vc_notice.php" class="btn btn-success btn-sm">
                                            <i class='bx bx-edit me-1'></i> Manage Vc Notice
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Carousel Images -->
                            <div class="col-md-4 col-sm-6">
                                <div class="dashboard-card card h-100">
                                    <div class="card-body text-center">
                                        <div class="card-icon">
                                            <i class='bx bx-slideshow'></i>
                                        </div>
                                        <h5 class="card-title">Carousel Images</h5>
                                        <p class="card-text">Manage homepage slider/carousel images.</p>
                                    </div>
                                    <div class="card-footer text-center">
                                        <a href="manage_courasel.php" class="btn btn-success btn-sm">
                                            <i class='bx bx-edit me-1'></i> Manage Carousel
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Faculties -->
                            <div class="col-md-4 col-sm-6">
                                <div class="dashboard-card card h-100">
                                    <div class="card-body text-center">
                                        <div class="card-icon">
                                            <i class='bx bx-building-house'></i>
                                        </div>
                                        <h5 class="card-title">Faculties</h5>
                                        <p class="card-text">Manage university faculties and departments.</p>
                                    </div>
                                    <div class="card-footer text-center">
                                        <a href="manage_faculties.php" class="btn btn-success btn-sm">
                                            <i class='bx bx-edit me-1'></i> Manage Faculties
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Programs -->
                            <div class="col-md-4 col-sm-6">
                                <div class="dashboard-card card h-100">
                                    <div class="card-body text-center">
                                        <div class="card-icon">
                                            <i class='bx bx-book-alt'></i>
                                        </div>
                                        <h5 class="card-title">Programs</h5>
                                        <p class="card-text">Manage academic programs and courses.</p>
                                    </div>
                                    <div class="card-footer text-center">
                                        <a href="manage_programs.php" class="btn btn-success btn-sm">
                                            <i class='bx bx-edit me-1'></i> Manage Programs
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Video Gallery -->
                            <div class="col-md-4 col-sm-6">
                                <div class="dashboard-card card h-100">
                                    <div class="card-body text-center">
                                        <div class="card-icon">
                                            <i class='bx bx-video-recording'></i>
                                        </div>
                                        <h5 class="card-title">Video Gallery</h5>
                                        <p class="card-text">Manage video content and gallery.</p>
                                    </div>
                                    <div class="card-footer text-center">
                                        <a href="manage_videos.php" class="btn btn-success btn-sm">
                                            <i class='bx bx-edit me-1'></i> Manage Videos
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Facilities -->
                            <div class="col-md-4 col-sm-6">
                                <div class="dashboard-card card h-100">
                                    <div class="card-body text-center">
                                        <div class="card-icon">
                                            <i class='bx bx-building'></i>
                                        </div>
                                        <h5 class="card-title">Facilities</h5>
                                        <p class="card-text">Manage university facilities and infrastructure.</p>
                                    </div>
                                    <div class="card-footer text-center">
                                        <a href="manage_facilities.php" class="btn btn-success btn-sm">
                                            <i class='bx bx-edit me-1'></i> Manage Facilities
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Fee Structure -->
                            <div class="col-md-4 col-sm-6">
                                <div class="dashboard-card card h-100">
                                    <div class="card-body text-center">
                                        <div class="card-icon">
                                            <i class='bx bx-money'></i>
                                        </div>
                                        <h5 class="card-title">Fee Structure</h5>
                                        <p class="card-text">Manage tuition fees and payment structures.</p>
                                    </div>
                                    <div class="card-footer text-center">
                                        <a href="manage_fees.php" class="btn btn-success btn-sm">
                                            <i class='bx bx-edit me-1'></i> Manage Fees
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Statistics -->
                            <div class="col-md-4 col-sm-6">
                                <div class="dashboard-card card h-100">
                                    <div class="card-body text-center">
                                        <div class="card-icon">
                                            <i class='bx bx-bar-chart-alt-2'></i>
                                        </div>
                                        <h5 class="card-title">Statistics</h5>
                                        <p class="card-text">View and manage university statistics.</p>
                                    </div>
                                    <div class="card-footer text-center">
                                        <a href="manage_statistics.php" class="btn btn-success btn-sm">
                                            <i class='bx bx-edit me-1'></i> Manage Statistics
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="col-md-4 col-sm-6">
                                <div class="dashboard-card card h-100">
                                    <div class="card-body text-center">
                                        <div class="card-icon">
                                            <i class='bx bx-note'></i>
                                        </div>
                                        <h5 class="card-title">Study Materials</h5>
                                        <p class="card-text">Manage study materials and lecture notes.</p>
                                    </div>
                                    <div class="card-footer text-center">
                                        <a href="manage_notes.php" class="btn btn-success btn-sm">
                                            <i class='bx bx-edit me-1'></i> Manage Materials
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <!-- end Main Content -->
            </div>
        </div>
    </div>
</div>
<!-- end Wrapper -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- BoxIcons -->
<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

<script>
    // Activate tooltips
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

</body>
</html>
