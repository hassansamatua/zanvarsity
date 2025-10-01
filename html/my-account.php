<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files using document root
$auth_file = $_SERVER['DOCUMENT_ROOT'] . '/zanvarsity/includes/auth_functions.php';
if (!file_exists($auth_file)) {
    die('Auth file not found at: ' . $auth_file);
}
require_once $auth_file;

// Check if user is logged in and is admin
require_login();
$is_admin = ($_SESSION['role'] ?? '') === 'admin';

// Get database connection
$db_file = $_SERVER['DOCUMENT_ROOT'] . '/zanvarsity/includes/database.php';
if (!file_exists($db_file)) {
    die('Database file not found at: ' . $db_file);
}
require_once $db_file;

// Get user information from session
$user_id = $_SESSION['user_id'] ?? null;
$user_email = $_SESSION['user_email'] ?? 'Guest';
$user_name = $_SESSION['first_name'] ?? 'User';
$user_role = $_SESSION['role'] ?? 'student'; // Get role from session if available
$is_admin = in_array($user_role, ['admin', 'super_admin']);

// Get user data from database and ensure role is up to date
if (isset($conn) && $user_id) {
    $query = "SELECT id, email, first_name, last_name, role FROM users WHERE id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            
            if ($result && $user_data = $result->fetch_assoc()) {
                // Update role from database if different
                if (!empty($user_data['role']) && $user_data['role'] !== $user_role) {
                    $user_role = $user_data['role'];
                    $_SESSION['role'] = $user_role;
                    $is_admin = in_array($user_role, ['admin', 'super_admin']);
                }
                
                // Update name if available
                if (!empty($user_data['first_name'])) {
                    $user_name = $user_data['first_name'];
                    $_SESSION['first_name'] = $user_name;
                }
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en-US" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Theme Starz">

    <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <link href="assets/css/font-awesome.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.css" type="text/css">
    <link rel="stylesheet" href="assets/css/selectize.css" type="text/css">
    <link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
    <link rel="stylesheet" href="assets/css/vanillabox/vanillabox.css" type="text/css">
    <link rel="stylesheet" href="assets/css/style.css" type="text/css">
    <link rel="stylesheet" href="assets/css/green-theme.css" type="text/css">

    <title>My Account - Zanvarsity</title>
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
                <li><a href="#tab-profile" data-toggle="tab"><i class="fa fa-user"></i>My Profile</a></li>
                <li><a href="#tab-my-courses" data-toggle="tab">My Courses</a></li>
                <li><a href="#tab-change-password" data-toggle="tab">Change Password</a></li>
                <li><a href="<?php echo dirname(dirname($_SERVER['PHP_SELF'])); ?>/logout.php" onclick="return confirm('Are you sure you want to log out?');"><i class="fa fa-sign-out"></i> Log Out</a></li>
            </ul>
        </div>
    </div><!-- /.secondary-navigation -->
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
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'courses.php' ? 'active' : ''; ?>">
                            <a href="courses.php">Courses</a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'events.php' ? 'active' : ''; ?>">
                            <a href="events.php">Events</a>
                        </li>
                        <?php if ($is_admin): ?>
                        <li>
                            <a href="admin/dashboard.php">Admin</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav><!-- /.navbar collapse-->
            </div><!-- /.container -->
        </header><!-- /.navbar -->
    </div><!-- /.primary-navigation -->
    <div class="background">
        <img src="assets/img/background-city.png"  alt="background">
    </div>
</div>
<!-- end Header -->

<!-- Breadcrumb -->
<div class="container">
    <ol class="breadcrumb">
        <li><a href="/zanvarsity/html/index.html">Home</a></li>
        <li class="active">My Account</li>
    </ol>
</div>
<!-- end Breadcrumb -->

<!-- Page Content -->
<div id="page-content">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <aside class="col-md-3 col-sm-4">
                <div class="sidebar">
                    <div class="sidebar-inner">
                        <div class="sidebar-widget">
                            <div class="user-avatar">
                                <div style="width: 100px; height: 100px; margin: 0 auto 15px; background-color: #4caf50; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; font-weight: bold; text-transform: uppercase; text-align: center; padding: 5px; line-height: 1.2;">
                                    <?php 
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
                                    echo $role_display;
                                    ?>
                                </div>
                                <div class="text-center">
                                    <h4><?php echo htmlspecialchars($user_name); ?></h4>
                                    <span class="label label-primary"><?php echo $role_display; ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="sidebar-widget">
                            <ul class="nav nav-pills nav-stacked nav-dashboard">
                                <li class="active"><a href="my-account.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                                <?php if (in_array($user_role, ['super_admin', 'admin'])): ?>
                                <li><a href="admin/users.php"><i class="fa fa-users"></i> Manage Users</a></li>
                                <li><a href="admin/contents.php"><i class="fa fa-file-text"></i> Manage Contents</a></li>
                                <?php endif; ?>
                                <li><a href="my-courses.php"><i class="fa fa-book"></i> My Courses</a></li>
                                <li><a href="my-profile.php"><i class="fa fa-user"></i> My Profile</a></li>
                                <li><a href="settings.php"><i class="fa fa-cog"></i> Settings</a></li>
                                <?php if (in_array($user_role, ['instructor', 'admin', 'super_admin'])): ?>
                                <li><a href="instructor/"><i class="fa fa-graduation-cap"></i> Instructor Panel</a></li>
                                <?php endif; ?>
                                <li class="divider"></li>
                                <li><a href="/zanvarsity/html/logout.php" onclick="return confirm('Are you sure you want to log out?')"><i class="fa fa-sign-out"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </aside>
            <!-- End Sidebar -->
            
            <!-- Main Content -->
            <div class="col-md-9 col-sm-8">
                <section class="block">
                    <div class="text-center">
                        <h1>Welcome back, <?php echo htmlspecialchars($user_name); ?>!</h1>
                        <p class="lead">Here's what's happening with your account today.</p>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row">
                        <?php
                        // Get counts from database
                        $stats = [];
                        
                        // Total Users (only for admin)
                        if ($is_admin) {
                            // Total Active Users
                            $result = $conn->query("SELECT COUNT(*) as count FROM users WHERE status = 1");
                            $stats['users'] = $result ? $result->fetch_assoc()['count'] : 0;
                            
                            // Active Announcements
                            $result = $conn->query("SELECT COUNT(*) as count FROM announcements WHERE status = 'active' AND (end_date IS NULL OR end_date >= NOW())");
                            $stats['announcements'] = $result ? $result->fetch_assoc()['count'] : 0;
                            
                            // Upcoming Events
                            $result = $conn->query("SELECT COUNT(*) as count FROM events WHERE end_date >= CURDATE()");
                            $stats['events'] = $result ? $result->fetch_assoc()['count'] : 0;
                            
                            // Total Downloads (if downloads table exists)
                            $table_check = $conn->query("SHOW TABLES LIKE 'downloads'");
                            if ($table_check && $table_check->num_rows > 0) {
                                $result = $conn->query("SELECT SUM(download_count) as count FROM downloads");
                                $stats['downloads'] = $result ? ($result->fetch_assoc()['count'] ?: 0) : 0;
                            } else {
                                $stats['downloads'] = 0;
                            }
                            
                            // Total Programs (if programs table exists)
                            $table_check = $conn->query("SHOW TABLES LIKE 'programs'");
                            if ($table_check && $table_check->num_rows > 0) {
                                $result = $conn->query("SELECT COUNT(*) as count FROM programs WHERE status = 'active'");
                                $stats['programs'] = $result ? $result->fetch_assoc()['count'] : 0;
                            } else {
                                $stats['programs'] = 0;
                            }
                        }
                        
                        // Get today's logins (if user_logins table exists)
                        $today_logins = 0;
                        $table_check = $conn->query("SHOW TABLES LIKE 'user_logins'");
                        if ($table_check && $table_check->num_rows > 0) {
                            $result = $conn->prepare("SELECT COUNT(*) as count FROM user_logins WHERE user_id = ? AND DATE(login_time) = CURDATE()");
                            if ($result) {
                                $result->bind_param("i", $user_id);
                                $result->execute();
                                $today_logins = $result->get_result()->fetch_assoc()['count'];
                                $result->close();
                            }
                        }
                        ?>
                        
                        <!-- Today's Logins -->
                        <div class="col-md-3 col-sm-6">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fa fa-sign-in"></i>
                                </div>
                                <div class="stat-content">
                                    <span class="counter"><?php echo $today_logins; ?></span>
                                    <h4>Today's Logins</h4>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($is_admin): ?>
                        <!-- Total Users -->
                        <div class="col-md-3 col-sm-6">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fa fa-users"></i>
                                </div>
                                <div class="stat-content">
                                    <span class="counter"><?php echo $stats['users']; ?></span>
                                    <h4>Active Users</h4>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Active Announcements -->
                        <div class="col-md-3 col-sm-6">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fa fa-bullhorn"></i>
                                </div>
                                <div class="stat-content">
                                    <span class="counter"><?php echo $stats['announcements']; ?></span>
                                    <h4>Announcements</h4>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Upcoming Events -->
                        <div class="col-md-3 col-sm-6">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <div class="stat-content">
                                    <span class="counter"><?php echo $stats['events']; ?></span>
                                    <h4>Upcoming Events</h4>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total Programs -->
                        <div class="col-md-3 col-sm-6">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fa fa-graduation-cap"></i>
                                </div>
                                <div class="stat-content">
                                    <span class="counter"><?php echo $stats['programs']; ?></span>
                                    <h4>Programs</h4>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total Downloads -->
                        <div class="col-md-3 col-sm-6">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fa fa-download"></i>
                                </div>
                                <div class="stat-content">
                                    <span class="counter"><?php echo $stats['downloads']; ?></span>
                                    <h4>Total Downloads</h4>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <!-- End Statistics Cards -->
                    
                    <style>
                    .stat-card {
                        background: #fff;
                        border-radius: 5px;
                        padding: 20px;
                        margin-bottom: 20px;
                        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                        text-align: center;
                        transition: all 0.3s ease;
                    }
                    .stat-card:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                    }
                    .stat-icon {
                        font-size: 40px;
                        color: #4CAF50;
                        margin-bottom: 15px;
                    }
                    .stat-content .counter {
                        font-size: 32px;
                        font-weight: bold;
                        display: block;
                        color: #333;
                    }
                    .stat-content h4 {
                        margin: 10px 0 0;
                        color: #666;
                        font-size: 16px;
                    }
                    .progress {
                        height: 8px;
                        margin-top: 15px;
                        background: #f1f1f1;
                        border-radius: 4px;
                    }
                    .progress-bar {
                        background-color: #4CAF50;
                        border-radius: 4px;
                    }
                    </style>
                </section>
            </div>
            <!-- End Main Content -->
        </div>
    </div>
    <!-- end Page Content -->
</div>
<!-- end Wrapper -->

<!-- Footer -->
<footer id="page-footer">
    <section id="footer-top">
        <div class="container">
            <div class="footer-inner">
                <div class="footer-social">
                    <figure>Follow us:</figure>
                    <div class="icons">
                        <a href="#"><i class="fa fa-twitter"></i></a>
                        <a href="#"><i class="fa fa-facebook"></i></a>
                        <a href="#"><i class="fa fa-pinterest"></i></a>
                        <a href="#"><i class="fa fa-youtube-play"></i></a>
                    </div>
                </div>
                <div class="search pull-right">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search">
                        <span class="input-group-btn">
                            <button type="submit" class="btn"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="footer-content">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-12">
                    <aside class="logo">
                        <img src="assets/img/logo-white.png" class="vertical-center">
                    </aside>
                </div>
                <div class="col-md-3 col-sm-4">
                    <aside>
                        <header><h4>Contact Us</h4></header>
                        <address>
                            <strong>Zanvarsity</strong>
                            <br>
                            <span>Education City</span>
                            <br><br>
                            <span>Dar es Salaam, Tanzania</span>
                            <br>
                            <abbr title="Telephone">Phone:</abbr> +255 123 456 789
                            <br>
                            <abbr title="Email">Email:</abbr> <a href="mailto:info@zanvarsity.ac.tz">info@zanvarsity.ac.tz</a>
                        </address>
                    </aside>
                </div>
                <div class="col-md-3 col-sm-4">
                    <aside>
                        <header><h4>Important Links</h4></header>
                        <ul class="list-links">
                            <li><a href="#">Future Students</a></li>
                            <li><a href="#">Alumni</a></li>
                            <li><a href="#">Give a Donation</a></li>
                            <li><a href="#">Faculty & Staff</a></li>
                            <li><a href="#">Library</a></li>
                            <li><a href="#">Research</a></li>
                        </ul>
                    </aside>
                </div>
                <div class="col-md-3 col-sm-4">
                    <aside>
                        <header><h4>About Zanvarsity</h4></header>
                        <p>Zanvarsity is a leading educational institution committed to academic excellence, innovation, and community engagement. We provide quality education that transforms lives and communities.</p>
                        <div>
                            <a href="about.php" class="read-more">Learn More</a>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
        <div class="background"><img src="assets/img/background-city.png" class="" alt=""></div>
    </section>

    <section id="footer-bottom">
        <div class="container">
            <div class="footer-inner">
                <div class="copyright"> 2023 Zanvarsity. All rights reserved.</div>
            </div>
        </div>
    </section>
</footer>
<!-- end Footer -->

<!-- JavaScript -->
<script type="text/javascript" src="assets/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="assets/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/js/selectize.min.js"></script>
<script type="text/javascript" src="assets/js/owl.carousel.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.placeholder.js"></script>
<script type="text/javascript" src="assets/js/jQuery.equalHeights.js"></script>
<script type="text/javascript" src="assets/js/icheck.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.vanillabox-0.1.5.min.js"></script>
<script type="text/javascript" src="assets/js/countdown.js"></script>
<script type="text/javascript" src="assets/js/custom.js"></script>
<script>
    $(document).ready(function() {
        // Initialize counters
        $('.counter').each(function() {
            $(this).prop('Counter', 0).animate({
                Counter: $(this).text()
            }, {
                duration: 1000,
                easing: 'swing',
                step: function(now) {
                    $(this).text(Math.ceil(now));
                }
            });
        });
    });
</script>
<style>
    /* Sidebar Styles */
    .sidebar {
        background: #fff;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        overflow: hidden;
    }
    
    .sidebar-widget {
        padding: 20px;
    }
    
    .sidebar-widget:not(:last-child) {
        border-bottom: 1px solid #eee;
    }
    
    .user-avatar {
        text-align: center;
        padding: 20px 0;
    }
    
    .user-avatar h4 {
        margin: 10px 0 5px;
        font-weight: 600;
    }
    
    .label {
        display: inline-block;
        padding: 3px 8px;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.5;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 3px;
    }
    
    .label-primary {
        background-color: #4caf50;
    }
    
    /* Navigation */
    .nav-dashboard {
        margin: 0 -20px;
    }
    
    .nav-dashboard > li > a {
        padding: 12px 20px;
        color: #555;
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
    }
    
    .nav-dashboard > li > a:hover,
    .nav-dashboard > li.active > a {
        background-color: #f8f9fa;
        color: #4caf50;
        border-left-color: #4caf50;
    }
    
    .nav-dashboard > li > a i {
        margin-right: 8px;
        width: 20px;
        text-align: center;
    }
    
    .nav-dashboard > li.divider {
        height: 1px;
        margin: 9px 0;
        overflow: hidden;
        background-color: #e5e5e5;
    }
    
    /* Dashboard Content */
    .feature-box {
        background: #fff;
        padding: 20px;
        border-radius: 4px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
    }
    
    .feature-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .feature-box-icon {
        margin-bottom: 15px;
    }
    
    .feature-box-icon i {
        font-size: 36px;
    }
    
    .feature-box h3 {
        font-size: 16px;
        font-weight: 600;
        margin: 0 0 10px;
    }
    
    /* Responsive */
    @media (max-width: 991px) {
        .sidebar {
            margin-bottom: 30px;
        }
    }
    
    @media (max-width: 767px) {
        .feature-box {
            margin-bottom: 20px;
        }
    }
</style>
</body>
</html>
