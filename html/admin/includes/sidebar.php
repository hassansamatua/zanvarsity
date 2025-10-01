<?php
// Ensure user role and name are set from session if not already set
$user_role = $user_role ?? $_SESSION['role'] ?? 'student';
$user_name = $user_name ?? $_SESSION['first_name'] ?? 'User';

// Set role display text
$role_display = ucfirst($user_role);
if ($user_role === 'super_admin') {
    $role_display = 'Super Admin';
} elseif ($user_role === 'admin') {
    $role_display = 'Administrator';
} elseif ($user_role === 'instructor') {
    $role_display = 'Instructor';
} elseif ($user_role === 'student') {
    $role_display = 'Student';
}

// Get base URL from session or use default
$base_url = isset($_SESSION['base_url']) ? rtrim($_SESSION['base_url'], '/') : '/c/zanvarsity/html';

// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF']);
$is_admin_page = (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false);
?>

<!-- Sidebar -->
<aside class="col-md-3 col-sm-4">
    <div class="sidebar">
        <div class="sidebar-inner">
            <div class="sidebar-widget text-center">
                <div class="user-avatar">
                    <div class="user-avatar-initials">
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
                    <h4><?php echo htmlspecialchars($user_name); ?></h4>
                    <span class="label label-primary"><?php echo $role_display; ?></span>
                </div>
            </div>
            
            <div class="sidebar-widget">
                <ul class="nav nav-pills nav-stacked nav-dashboard">
                    <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'my-account.php' ? 'active' : ''; ?>">
                        <a href="<?php echo $base_url; ?>/my-account.php">
                            <i class="fa fa-dashboard"></i> Dashboard
                        </a>
                    </li>
                    <?php if (in_array($user_role, ['super_admin', 'admin'])): ?>
                    <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                        <a href="<?php echo $base_url; ?>/admin/users.php">
                            <i class="fa fa-users"></i> Manage Users
                        </a>
                    </li>
                    <li class="<?php echo ($current_page == 'contents.php' || $current_page == 'content-edit.php' || $current_page == 'content-add.php') ? 'active' : ''; ?>">
                        <a href="<?php echo $base_url; ?>/admin/contents.php">
                            <i class="fa fa-file-text"></i> Manage Contents
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="<?php echo $current_page == 'my-courses.php' ? 'active' : ''; ?>">
                        <a href="<?php echo $base_url; ?>/my-courses.php">
                            <i class="fa fa-book"></i> My Courses
                        </a>
                    </li>
                    <li class="<?php echo $current_page == 'my-profile.php' ? 'active' : ''; ?>">
                        <a href="<?php echo $base_url; ?>/my-profile.php">
                            <i class="fa fa-user"></i> My Profile
                        </a>
                    </li>
                    <li class="<?php echo $current_page == 'settings.php' ? 'active' : ''; ?>">
                        <a href="<?php echo $base_url; ?>/settings.php">
                            <i class="fa fa-cog"></i> Settings
                        </a>
                    </li>
                    <?php if (in_array($user_role, ['instructor', 'admin', 'super_admin'])): ?>
                    <li class="<?php echo (strpos($_SERVER['PHP_SELF'], '/instructor/') !== false) ? 'active' : ''; ?>">
                        <a href="<?php echo $base_url; ?>/instructor/">
                            <i class="fa fa-graduation-cap"></i> Instructor Panel
                        </a>
                    </li>
                    <?php endif; ?>
                    <li>
                        <a href="<?php echo $base_url; ?>/logout.php" onclick="return confirm('Are you sure you want to log out?')">
                            <i class="fa fa-sign-out"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</aside>
<!-- End Sidebar -->
