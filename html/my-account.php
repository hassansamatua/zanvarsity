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

// Set page title and heading
$page_title = 'My Account | Zanvarsity';
$page_heading = 'My Dashboard';

// Include header
include 'includes/about_header.php';

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
    // Add your stats queries here
    // Example: $stats['total_courses'] = get_course_count($conn, $user_id);
}

// Add custom styles for the dashboard
?>
<style>
/* Dashboard Styles */
.dashboard-container {
    padding: 40px 0;
    background: #f8f9fa;
    min-height: calc(100vh - 200px);
}

.dashboard-header {
    margin-bottom: 30px;
    text-align: center;
}

.dashboard-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 25px;
    margin-bottom: 30px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}

.stat-card {
    text-align: center;
    padding: 20px;
    border-radius: 8px;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.stat-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
    color: #014421;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
    margin: 10px 0;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Sidebar Styles */
.sidebar {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    margin-bottom: 30px;
}

.profile-card {
    text-align: center;
    padding: 25px 15px;
    background: linear-gradient(135deg, #014421 0%, #027333 100%);
    color: white;
}

.profile-image {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    border: 4px solid rgba(255, 255, 255, 0.2);
    margin: 0 auto 15px;
    overflow: hidden;
}

.profile-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-name {
    font-size: 1.3rem;
    font-weight: 600;
    margin: 10px 0 5px;
}

.profile-role {
    font-size: 0.9rem;
    opacity: 0.9;
    text-transform: capitalize;
}

.sidebar-menu {
    padding: 0;
    margin: 0;
    list-style: none;
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
    transition: all 0.3s;
}

.sidebar-menu a:hover,
.sidebar-menu a.active {
    background: #f8f9fa;
    color: #014421;
    padding-left: 25px;
}

.sidebar-menu i {
    width: 20px;
    margin-right: 10px;
    text-align: center;
}

/* Responsive adjustments */
@media (max-width: 991px) {
    .sidebar {
        margin-bottom: 30px;
    }
}
</style>

<!-- Main Content -->
<div class="dashboard-container">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="sidebar">
                    <div class="profile-card">
                        <div class="profile-image">
                            <img src="assets/img/avatar-placeholder.png" alt="Profile Image">
                        </div>
                        <h3 class="profile-name"><?php echo htmlspecialchars($user_name); ?></h3>
                        <div class="profile-role"><?php echo ucfirst(htmlspecialchars($user_role)); ?></div>
                    </div>
                    <ul class="sidebar-menu">
                        <li><a href="#dashboard" class="active"><i class="fa fa-tachometer"></i> Dashboard</a></li>
                        <li><a href="#profile"><i class="fa fa-user"></i> My Profile</a></li>
                        <li><a href="#courses"><i class="fa fa-book"></i> My Courses</a></li>
                        <li><a href="#messages"><i class="fa fa-envelope"></i> Messages</a></li>
                        <li><a href="#settings"><i class="fa fa-cog"></i> Settings</a></li>
                        <?php if ($is_admin): ?>
                        <li><a href="/admin/"><i class="fa fa-lock"></i> Admin Panel</a></li>
                        <?php endif; ?>
                        <li><a href="/c/zanvarsity/logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="dashboard-header">
                    <h1>Welcome back, <?php echo htmlspecialchars($user_name); ?>!</h1>
                    <p class="lead">Here's what's happening with your account today.</p>
                </div>

                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fa fa-book"></i>
                            </div>
                            <div class="stat-number">12</div>
                            <div class="stat-label">My Courses</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fa fa-tasks"></i>
                            </div>
                            <div class="stat-number">8</div>
                            <div class="stat-label">In Progress</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fa fa-certificate"></i>
                            </div>
                            <div class="stat-number">4</div>
                            <div class="stat-label">Certificates</div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="dashboard-card">
                    <h3>Recent Activity</h3>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Activity</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Today</td>
                                    <td>Completed "Introduction to Web Development"</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                </tr>
                                <tr>
                                    <td>Yesterday</td>
                                    <td>Started "Advanced JavaScript" course</td>
                                    <td><span class="badge bg-primary">In Progress</span></td>
                                </tr>
                                <tr>
                                    <td>2 days ago</td>
                                    <td>Submitted assignment for "Database Design"</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/about_footer.php'; ?>

<!-- JavaScript -->
<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Add active class to current nav item
    const currentPage = window.location.pathname.split('/').pop() || 'index.php';
    $('.sidebar-menu a').each(function() {
        if ($(this).attr('href') === currentPage) {
            $(this).addClass('active');
        }
    });
    
    // Add smooth scrolling
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = this.hash;
        const $target = $(target);
        $('html, body').stop().animate({
            'scrollTop': $target.offset().top
        }, 900, 'swing', function() {
            window.location.hash = target;
        });
    });
});
</script>
</body>
</html>