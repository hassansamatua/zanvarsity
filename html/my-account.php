<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define root path - point to zanvarsity directory
define('ROOT_PATH', dirname(dirname(dirname(__DIR__))));

// Include necessary files
require_once ROOT_PATH . '/zanvarsity/includes/auth_functions.php';
require_once ROOT_PATH . '/zanvarsity/includes/database.php';

// Check if user is logged in
require_login();

// Get user information from session
$user_id = $_SESSION['user_id'] ?? null;
$user_email = $_SESSION['email'] ?? 'Guest';
$user_name = $_SESSION['first_name'] ?? 'User';
$user_role = $_SESSION['role'] ?? 'student';
$is_admin = in_array($user_role, ['admin', 'super_admin']);

// Get user data from database
if (isset($conn) && $user_id) {
    $query = "SELECT id, email, first_name, last_name, role FROM users WHERE id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($user_data = $result->fetch_assoc()) {
                // Update session data from database
                if (!empty($user_data['first_name'])) {
                    $user_name = $user_data['first_name'];
                    $_SESSION['first_name'] = $user_name;
                }
                if (!empty($user_data['role'])) {
                    $user_role = $user_data['role'];
                    $_SESSION['role'] = $user_role;
                    $is_admin = in_array($user_role, ['admin', 'super_admin']);
                }
            }
        }
        $stmt->close();
    }
}

// Get user stats from database
$stats = [];
if (isset($conn)) {
    // Get user's course count
    $query = "SELECT COUNT(*) as count FROM user_courses WHERE user_id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['courses'] = $result->fetch_assoc()['count'] ?? 0;
        $stats['completed'] = 0; // Initialize other stats
        $stats['in_progress'] = 0;
        $stats['certificates'] = 0;
        $stmt->close();
    }
}

// Set page title for the header
$page_title = 'My Account - Zanvarsity';

// Include the admin header (must be after all session and variable setup)
require_once __DIR__ . '/admin/includes/header.php';
?>

<style>
/* Ensure the admin header is properly displayed */
body.page-my-account {
    padding-top: 0 !important;
    background-color: #f5f5f5;
}
/* Override any conflicting styles from other CSS */
#page-content {
    padding-top: 20px;
    margin-top: 0;
}
/* Force the admin header to be dark green */
.navigation-wrapper {
    background-color: #006400 !important;
}
.secondary-navigation-wrapper {
    background-color: #004d00 !important;
}
.navbar {
    background-color: #006400 !important;
    border: none !important;
}
.navbar-nav > li > a {
    color: #e0e0e0 !important;
}
.navbar-nav > li > a:hover,
.navbar-nav > li.active > a {
    background-color: #005900 !important;
    color: #ffffff !important;
}

/* Sidebar Styling */
.sidebar {
    width: auto;
    min-width: 200px;
    max-width: 250px;
    background: #ffffff;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 15px;
    margin-bottom: 20px;
}

.sidebar .sidebar-widget {
    margin-bottom: 20px;
}

.sidebar .nav-pills {
    display: flex;
    flex-direction: column;
    width: 100%;
}

.sidebar .nav-pills > li {
    float: none;
    display: block;
    margin: 0;
    width: 100%;
}

.sidebar .nav-pills > li > a {
    border-radius: 4px;
    padding: 10px 15px;
    margin-bottom: 5px;
    color: #333;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.sidebar .nav-pills > li > a:hover,
.sidebar .nav-pills > li.active > a {
    background-color: #f0f0f0;
    color: #006400;
}

.sidebar .nav-pills > li > a i {
    margin-right: 8px;
    width: 20px;
    text-align: center;
}

/* Make sure the sidebar takes minimum width on mobile */
@media (max-width: 767px) {
    .sidebar {
        width: 100%;
        max-width: 100%;
    }
}
</style>

<!-- Page Content -->
<!-- Start of page content -->
<!-- Start of page content -->
<div id="page-content" class="admin-page" style="margin-top: 0;">
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
<!-- Include the footer -->
<?php include_once __DIR__ . '/includes/footer.php'; ?>

<!-- JavaScripts are included in the admin_header.php -->
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