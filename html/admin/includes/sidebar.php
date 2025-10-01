<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get user role and name from session
$user_role = $_SESSION['role'] ?? '';
$user_name = $_SESSION['name'] ?? 'User';

// Get role display name
$role_display = ucfirst($user_role);
?>

<div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>" 
                 class="rounded-circle mb-2" 
                 width="80" 
                 alt="Profile">
            <h6 class="mb-1"><?php echo htmlspecialchars($user_name); ?></h6>
            <span class="badge bg-primary"><?php echo $role_display; ?></span>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class='bx bx-grid-alt me-2'></i>
                    Dashboard
                </a>
            </li>
            
            <?php if (in_array($user_role, ['super_admin', 'admin'])): ?>
            <li class="nav-item">
                <a class="nav-link" href="users.php">
                    <i class='bx bx-user me-2'></i>
                    Manage Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_content.php">
                    <i class='bx bx-file me-2'></i>
                    Manage Content
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="manage_vc_notice.php">
                    <i class='bx bx-message-detail me-2'></i>
                    VC's Notice
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_events.php">
                    <i class='bx bx-calendar-event me-2'></i>
                    Manage Events
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (in_array($user_role, ['instructor', 'admin', 'super_admin'])): ?>
            <li class="nav-item">
                <a class="nav-link" href="/zanvarsity/html/instructor">
                    <i class='bx bx-chalkboard me-2'></i>
                    Instructor Panel
                </a>
            </li>
            <?php endif; ?>
            
            <li class="nav-item">
                <a class="nav-link" href="/zanvarsity/html/my-profile.php">
                    <i class='bx bx-user me-2'></i>
                    My Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/zanvarsity/html/change-password.php">
                    <i class='bx bx-key me-2'></i>
                    Change Password
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="/zanvarsity/logout.php" 
                   onclick="return confirm('Are you sure you want to log out?')">
                    <i class='bx bx-log-out me-2'></i>
                    Log Out
                </a>
            </li>
        </ul>
    </div>
</div>
