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
    <div class="sidebar" style="background: #fff; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 30px;">
        <div class="sidebar-inner">
            <div class="sidebar-widget text-center" style="padding: 20px 0;">
                <div class="user-avatar">
                    <div style="width: 100px; height: 100px; margin: 0 auto 15px; background-color: #4caf50; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; font-weight: bold; text-transform: uppercase; text-align: center; padding: 5px; line-height: 1.2;">
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
                    <div class="text-center">
                        <h4 style="margin: 10px 0 5px; color: #333; font-size: 18px;"><?php echo htmlspecialchars($user_name); ?></h4>
                        <span class="label label-primary" style="background-color: #4caf50; padding: 3px 10px; border-radius: 3px; font-size: 12px; font-weight: 600; text-transform: uppercase;"><?php echo $role_display; ?></span>
                    </div>
                </div>
            </div>
            
            <div class="sidebar-widget">
                <ul class="nav nav-pills nav-stacked" style="margin: 0; padding: 0; list-style: none;">
                    <li style="border-bottom: 1px solid #eee;">
                        <a href="<?php echo $base_url; ?>/my-account.php" style="display: block; padding: 12px 15px; color: #555; text-decoration: none; transition: all 0.3s ease;<?php echo basename($_SERVER['PHP_SELF']) == 'my-account.php' ? ' background-color: #f5f5f5; color: #4caf50; border-left: 3px solid #4caf50;' : ''; ?>">
                            <i class="fa fa-dashboard" style="width: 20px; margin-right: 10px; text-align: center;"></i> Dashboard
                        </a>
                    </li>
                    <?php if (in_array($user_role, ['super_admin', 'admin'])): ?>
                    <li style="border-bottom: 1px solid #eee;">
                        <a href="/c/zanvarsity/html/logout.php?redirect=/c/zanvarsity/html/admin/users.php"
                           style="display: block; padding: 12px 15px; color: #555; text-decoration: none; cursor: pointer; transition: all 0.3s ease;<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? ' background-color: #f5f5f5; color: #4caf50; border-left: 3px solid #4caf50;' : ''; ?>"
                           onclick="event.preventDefault(); window.location.href=this.href; return false;">
                            <i class="fa fa-users" style="width: 20px; margin-right: 10px; text-align: center;"></i> Manage Users
                        </a>
                    </li>
                    <li style="border-bottom: 1px solid #eee;">
                        <a href="/c/zanvarsity/html/logout.php?redirect=/c/zanvarsity/html/admin/contents.php"
                           style="display: block; padding: 12px 15px; color: #555; text-decoration: none; cursor: pointer; transition: all 0.3s ease;<?php echo ($current_page == 'contents.php' || $current_page == 'content-edit.php' || $current_page == 'content-add.php') ? ' background-color: #f5f5f5; color: #4caf50; border-left: 3px solid #4caf50;' : ''; ?>"
                           onclick="event.preventDefault(); window.location.href=this.href; return false;">
                            <i class="fa fa-file-text" style="width: 20px; margin-right: 10px; text-align: center;"></i> Manage Contents
                        </a>
                    </li>
                    <?php endif; ?>
                    <li style="border-bottom: 1px solid #eee;">
                        <a href="<?php echo $base_url; ?>/my-courses.php" style="display: block; padding: 12px 15px; color: #555; text-decoration: none; transition: all 0.3s ease;<?php echo $current_page == 'my-courses.php' ? ' background-color: #f5f5f5; color: #4caf50; border-left: 3px solid #4caf50;' : ''; ?>">
                            <i class="fa fa-book" style="width: 20px; margin-right: 10px; text-align: center;"></i> My Courses
                        </a>
                    </li>
                    <li style="border-bottom: 1px solid #eee;">
                        <a href="<?php echo $base_url; ?>/my-profile.php" style="display: block; padding: 12px 15px; color: #555; text-decoration: none; transition: all 0.3s ease;<?php echo $current_page == 'my-profile.php' ? ' background-color: #f5f5f5; color: #4caf50; border-left: 3px solid #4caf50;' : ''; ?>">
                            <i class="fa fa-user" style="width: 20px; margin-right: 10px; text-align: center;"></i> My Profile
                        </a>
                    </li>
                    <li style="border-bottom: 1px solid #eee;">
                        <a href="<?php echo $base_url; ?>/settings.php" style="display: block; padding: 12px 15px; color: #555; text-decoration: none; transition: all 0.3s ease;<?php echo $current_page == 'settings.php' ? ' background-color: #f5f5f5; color: #4caf50; border-left: 3px solid #4caf50;' : ''; ?>">
                            <i class="fa fa-cog" style="width: 20px; margin-right: 10px; text-align: center;"></i> Settings
                        </a>
                    </li>
                    <?php if (in_array($user_role, ['instructor', 'admin', 'super_admin'])): ?>
                    <li style="border-bottom: 1px solid #eee;">
                        <a href="<?php echo $base_url; ?>/instructor/" style="display: block; padding: 12px 15px; color: #555; text-decoration: none; transition: all 0.3s ease;<?php echo (strpos($_SERVER['PHP_SELF'], '/instructor/') !== false) ? ' background-color: #f5f5f5; color: #4caf50; border-left: 3px solid #4caf50;' : ''; ?>">
                            <i class="fa fa-graduation-cap" style="width: 20px; margin-right: 10px; text-align: center;"></i> Instructor Panel
                        </a>
                    </li>
                    <?php endif; ?>
                    <li style="border-bottom: 1px solid #eee;">
                        <a href="<?php echo $base_url; ?>/logout.php" 
                           style="display: block; padding: 12px 15px; color: #555; text-decoration: none; transition: all 0.3s ease;"
                           onclick="return confirm('Are you sure you want to log out?')">
                            <i class="fa fa-sign-out" style="width: 20px; margin-right: 10px; text-align: center;"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</aside>
<!-- End Sidebar -->
