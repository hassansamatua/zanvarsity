<?php
// Debug: Show current session data (temporary)
echo '<!-- Debug: Session Data -->';
echo '<!-- ' . print_r($_SESSION, true) . ' -->';

// Get user information from session
$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['first_name'] ?? 'User';

// Debug: Show current role values
echo '<!-- Debug: user_role = ' . ($_SESSION['user_role'] ?? 'not set') . ' -->';
echo '<!-- Debug: role = ' . ($_SESSION['role'] ?? 'not set') . ' -->';

// Get role from session - use user_role if available, otherwise fall back to role
$user_role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? 'student';

// Debug: Show determined role
echo '<!-- Debug: Determined role = ' . $user_role . ' -->';

// Update session to use consistent role key if needed
if (isset($_SESSION['user_role'])) {
    $_SESSION['role'] = $user_role;
    unset($_SESSION['user_role']);
    // Force session write
    session_write_close();
    session_start();
}

// Set role display text
$role_display = ucfirst($user_role);
switch ($user_role) {
    case 'super_admin':
        $role_display = 'Super Admin';
        break;
    case 'admin':
        $role_display = 'Administrator';
        break;
    case 'instructor':
        $role_display = 'Instructor';
        break;
    case 'student':
    default:
        $role_display = 'Student';
}

// Get base URL from session or use default
$base_url = isset($_SESSION['base_url']) ? rtrim($_SESSION['base_url'], '/') : '';

// Ensure base_url doesn't end with a slash
$base_url = rtrim($base_url, '/');

// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF']);
$is_admin_page = (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false);
?>
<!-- Sidebar -->
<aside class="col-md-3 col-sm-4">
    <!-- User Profile Card -->
    <div class="card mb-4">
        <div class="card-body text-center">
            <div class="user-avatar mx-auto mb-3" style="width: 100px; height: 100px; font-size: 40px; background-color: #4caf50; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                <?php 
                $initials = '';
                $name_parts = explode(' ', $user_name);
                foreach($name_parts as $part) {
                    $initials .= strtoupper(substr($part, 0, 1));
                    if(strlen($initials) >= 2) break;
                }
                echo $initials;
                ?>
            </div>
            <h5 class="card-title"><?php echo htmlspecialchars($user_name); ?></h5>
            <p class="text-muted"><?php echo $role_display; ?></p>
            
            <!-- Navigation -->
            <div class="list-group">
                <a href="<?php echo $base_url; ?>/my-account.php" class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'my-account.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                
                <?php if (in_array($user_role, ['super_admin', 'admin'])): ?>
                <a href="/c/zanvarsity/html/logout.php?redirect=/c/zanvarsity/html/admin/users.php" 
                   class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>"
                   onclick="event.preventDefault(); window.location.href=this.href; return false;">
                    <i class="fas fa-users me-2"></i> Manage Users
                </a>
                <a href="/c/zanvarsity/html/logout.php?redirect=/c/zanvarsity/html/admin/contents.php"
                   class="list-group-item list-group-item-action <?php echo (in_array($current_page, ['contents.php', 'content-edit.php', 'content-add.php'])) ? 'active' : ''; ?>"
                   onclick="event.preventDefault(); window.location.href=this.href; return false;">
                    <i class="fas fa-file-text me-2"></i> Manage Contents
                </a>
                <?php endif; ?>
                
                <a href="<?php echo $base_url; ?>/my-courses.php" class="list-group-item list-group-item-action <?php echo $current_page == 'my-courses.php' ? 'active' : ''; ?>">
                    <i class="fas fa-book me-2"></i> My Courses
                </a>
                
                <a href="<?php echo $base_url; ?>/my-profile.php" class="list-group-item list-group-item-action <?php echo $current_page == 'my-profile.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user me-2"></i> My Profile
                </a>
                
                <a href="<?php echo $base_url; ?>/settings.php" class="list-group-item list-group-item-action <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>">
                    <i class="fas fa-cog me-2"></i> Settings
                </a>
                
                <?php if (in_array($user_role, ['instructor', 'admin', 'super_admin'])): ?>
                <a href="<?php echo $base_url; ?>/instructor/" class="list-group-item list-group-item-action <?php echo strpos($_SERVER['PHP_SELF'], '/instructor/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-graduation-cap me-2"></i> Instructor Panel
                </a>
                <?php endif; ?>
                
                <a href="<?php echo $base_url; ?>/logout.php" 
                   class="list-group-item list-group-item-action text-danger"
                   onclick="return confirm('Are you sure you want to log out?')">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats Card -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">Quick Stats</h6>
        </div>
        <div class="list-group list-group-flush">
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <span><i class="fas fa-book me-2"></i> My Courses</span>
                <span class="badge bg-primary rounded-pill">5</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <span><i class="fas fa-tasks me-2"></i> Assignments</span>
                <span class="badge bg-warning text-dark rounded-pill">3</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <span><i class="fas fa-bell me-2"></i> Notifications</span>
                <span class="badge bg-danger rounded-pill">2</span>
            </div>
        </div>
    </div>
</aside>
<!-- End Sidebar -->
