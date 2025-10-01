<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: /zanvarsity/html/403.php");
    exit();
}

// Set page title if not already set
if (!isset($page_title)) {
    $page_title = 'Admin Panel';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Zanvarsity Admin</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="/zanvarsity/favicon.ico" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="/zanvarsity/html/assets/css/admin.css" rel="stylesheet">
    
    <style>
        body {
            padding-top: 56px;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 56px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            background-color: #2c3e50;
            color: white;
        }
        
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 56px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.5rem 1rem;
        }
        
        .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .main-content {
            margin-left: 200px;
            padding: 20px;
        }
        
        @media (max-width: 767.98px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main-content {
                margin-left: 0;
            }
        }
        
        /* Dropdown Menu */
        .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            margin-top: 0.5rem;
            padding: 0.5rem 0;
        }
        
        .dropdown-item {
            padding: 0.5rem 1.5rem;
            font-size: 0.9rem;
        }
        
        .dropdown-item i {
            width: 20px;
            text-align: center;
            margin-right: 0.5rem;
        }
        
        /* User Dropdown */
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #dee2e6;
        }
        
        .user-info {
            margin-right: 0.75rem;
            text-align: right;
        }
        
        .user-info .name {
            font-weight: 600;
            display: block;
            line-height: 1.2;
        }
        
        .user-info .role {
            font-size: 0.75rem;
            color: #6c757d;
            display: block;
            line-height: 1.2;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 991.98px) {
            .search-form {
                margin: 1rem 0;
                max-width: 100%;
            }
            
            .user-info {
                display: none;
            }
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            background-color: #2c3e50;
            color: white;
            transition: all 0.3s;
        }
        
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            margin: 0.25rem 1rem;
            border-radius: 0.25rem;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover, 
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .sidebar .nav-link i {
            margin-right: 0.5rem;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }
        
        .sidebar .nav-link .menu-arrow {
            float: right;
            margin-top: 5px;
            transition: transform 0.3s;
        }
        
        .sidebar .nav-link[aria-expanded="true"] .menu-arrow {
            transform: rotate(90deg);
        }
        
        .sidebar .sub-menu {
            padding-left: 1.5rem;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        .navbar {
            position: fixed;
            top: 0;
            right: 0;
            left: 250px;
            z-index: 1030;
            background-color: #fff;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.3s;
        }
        
        .content-wrapper {
            margin-top: 56px;
            padding: 20px;
        }
        
        .stat-card {
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            color: white;
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .bg-primary { background: #3498db; }
        .bg-success { background: #2ecc71; }
        .bg-warning { background: #f39c12; }
        .bg-danger { background: #e74c3c; }
        .bg-info { background: #1abc9c; }
        .bg-secondary { background: #7f8c8d; }
        
        /* Toggle button for sidebar */
        #sidebarToggle {
            cursor: pointer;
            margin-right: 10px;
        }
        
        /* Responsive styles */
        @media (max-width: 991.98px) {
            .sidebar {
                left: -250px;
            }
            .main-content, .navbar {
                left: 0;
            }
            .sidebar.active {
                left: 0;
            }
            .main-content.active, .navbar.active {
                left: 250px;
            }
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Custom form styles */
        .form-control:focus, .form-select:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }
        
        /* Card styles */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 15px 20px;
            font-weight: 600;
        }
        
        /* Table styles */
        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-top: none;
            padding: 12px 15px;
        }
        
        .table td {
            padding: 12px 15px;
            vertical-align: middle;
        }
        
        /* Badge styles */
        .badge {
            padding: 6px 10px;
            font-weight: 500;
            border-radius: 4px;
        }
        
        /* Button styles */
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-sm {
            padding: 4px 10px;
            font-size: 0.8rem;
        }
        
        .btn i {
            font-size: 1rem;
            vertical-align: middle;
            margin-right: 5px;
        }
        
        /* Alert styles */
        .alert {
            border: none;
            border-radius: 4px;
            padding: 12px 20px;
            margin-bottom: 20px;
        }
        
        .alert-dismissible .btn-close {
            padding: 0.75rem 1rem;
        }
        
        /* Custom checkbox and radio */
        .form-check-input:checked {
            background-color: #3498db;
            border-color: #3498db;
        }
        
        /* Custom file upload */
        .form-file-button {
            cursor: pointer;
        }
        
        /* Custom tabs */
        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 10px 20px;
            border-bottom: 2px solid transparent;
        }
        
        .nav-tabs .nav-link.active {
            color: #3498db;
            background: none;
            border-bottom: 2px solid #3498db;
        }
        
        /* Custom pagination */
        .pagination .page-link {
            color: #3498db;
            border: 1px solid #dee2e6;
            margin: 0 3px;
            border-radius: 4px;
        }
        
        .pagination .page-item.active .page-link {
            background-color: #3498db;
            border-color: #3498db;
        }
        
        /* Custom tooltips */
        .tooltip-inner {
            font-size: 0.75rem;
            padding: 5px 10px;
            border-radius: 4px;
        }
        
        /* Custom modal */
        .modal-content {
            border: none;
            border-radius: 10px;
        }
        
        .modal-header {
            border-bottom: 1px solid #eee;
            padding: 15px 20px;
        }
        
        .modal-footer {
            border-top: 1px solid #eee;
            padding: 15px 20px;
        }
        
        /* Custom form switch */
        .form-switch .form-check-input {
            width: 2.5em;
            margin-left: -2.5em;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%280, 0, 0, 0.25%29'/%3e%3c/svg%3e");
        }
        
        .form-switch .form-check-input:checked {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
        <div class="position-sticky pt-3">
            <div class="text-center mb-4">
                <h4 class="text-white">Zanvarsity</h4>
                <p class="text-white-50 mb-0">Admin Panel</p>
            </div>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? ' active' : ''; ?>" href="dashboard.php">
                        <i class='bx bxs-dashboard'></i> Dashboard
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link<?php echo in_array(basename($_SERVER['PHP_SELF']), ['users.php', 'user-roles.php', 'user-permissions.php']) ? ' active' : ''; ?>" 
                       href="#userManagement" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="userManagement">
                        <i class='bx bxs-user-detail'></i> User Management
                        <i class='bx bx-chevron-right menu-arrow'></i>
                    </a>
                    <div class="collapse<?php echo in_array(basename($_SERVER['PHP_SELF']), ['users.php', 'user-roles.php', 'user-permissions.php']) ? ' show' : ''; ?>" id="userManagement">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? ' active' : ''; ?>" href="users.php">
                                    <i class='bx bx-user'></i> Users
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'user-roles.php' ? ' active' : ''; ?>" href="user-roles.php">
                                    <i class='bx bx-id-card'></i> Roles
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'user-permissions.php' ? ' active' : ''; ?>" href="user-permissions.php">
                                    <i class='bx bx-lock-alt'></i> Permissions
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link<?php echo in_array(basename($_SERVER['PHP_SELF']), ['news.php', 'events.php', 'announcements.php', 'downloads.php']) ? ' active' : ''; ?>" 
                       href="#contentManagement" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="contentManagement">
                        <i class='bx bxs-news'></i> Content Management
                        <i class='bx bx-chevron-right menu-arrow'></i>
                    </a>
                    <div class="collapse<?php echo in_array(basename($_SERVER['PHP_SELF']), ['news.php', 'events.php', 'announcements.php', 'downloads.php']) ? ' show' : ''; ?>" id="contentManagement">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'news.php' ? ' active' : ''; ?>" href="news.php">
                                    <i class='bx bx-news'></i> News
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'events.php' ? ' active' : ''; ?>" href="events.php">
                                    <i class='bx bx-calendar-event'></i> Events
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'announcements.php' ? ' active' : ''; ?>" href="announcements.php">
                                    <i class='bx bx-megaphone'></i> Announcements
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'downloads.php' ? ' active' : ''; ?>" href="downloads.php">
                                    <i class='bx bx-download'></i> Downloads
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link<?php echo in_array(basename($_SERVER['PHP_SELF']), ['faculties.php', 'departments.php', 'courses.php', 'staff.php']) ? ' active' : ''; ?>" 
                       href="#academicManagement" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="academicManagement">
                        <i class='bx bxs-graduation'></i> Academic Management
                        <i class='bx bx-chevron-right menu-arrow'></i>
                    </a>
                    <div class="collapse<?php echo in_array(basename($_SERVER['PHP_SELF']), ['faculties.php', 'departments.php', 'courses.php', 'staff.php']) ? ' show' : ''; ?>" id="academicManagement">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'faculties.php' ? ' active' : ''; ?>" href="faculties.php">
                                    <i class='bx bx-building-house'></i> Faculties
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'departments.php' ? ' active' : ''; ?>" href="departments.php">
                                    <i class='bx bx-buildings'></i> Departments
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'courses.php' ? ' active' : ''; ?>" href="courses.php">
                                    <i class='bx bx-book'></i> Courses
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'staff.php' ? ' active' : ''; ?>" href="staff.php">
                                    <i class='bx bx-user-voice'></i> Staff
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link<?php echo in_array(basename($_SERVER['PHP_SELF']), ['facilities.php', 'organizations.php']) ? ' active' : ''; ?>" 
                       href="#campusManagement" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="campusManagement">
                        <i class='bx bxs-building-house'></i> Campus Management
                        <i class='bx bx-chevron-right menu-arrow'></i>
                    </a>
                    <div class="collapse<?php echo in_array(basename($_SERVER['PHP_SELF']), ['facilities.php', 'organizations.php']) ? ' show' : ''; ?>" id="campusManagement">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'facilities.php' ? ' active' : ''; ?>" href="facilities.php">
                                    <i class='bx bx-building'></i> Facilities
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'organizations.php' ? ' active' : ''; ?>" href="organizations.php">
                                    <i class='bx bx-group'></i> Student Organizations
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? ' active' : ''; ?>" href="settings.php">
                        <i class='bx bxs-cog'></i> Settings
                    </a>
                </li>
            </ul>
            
            <div class="position-absolute bottom-0 start-0 end-0 p-3 text-center">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class='bx bxs-user-circle fs-4 me-2'></i>
                        <strong><?php echo htmlspecialchars($_SESSION['user_email']); ?></strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                        <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/zanvarsity/logout.php">Sign out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navigation -->
        <div class="navigation-wrapper">
            <div class="secondary-navigation-wrapper bg-dark py-1">
                <div class="container">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="navigation-contact text-white small">
                            <i class='bx bx-phone me-1'></i> Call Us: <span class="opacity-75">000-123-456-789</span>
                        </div>
                        <ul class="secondary-navigation list-unstyled d-flex mb-0">
                            <li class="me-3"><a href="/zanvarsity/html/index.php" class="text-white-50 small">Frontend</a></li>
                            <li class="me-3"><a href="dashboard.php" class="text-white-50 small">Dashboard</a></li>
                            <li class="me-0"><a href="/zanvarsity/logout.php" class="text-white-50 small">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="primary-navigation-wrapper bg-white border-bottom">
                <div class="container">
                    <nav class="navbar navbar-expand-lg p-0">
                        <div class="d-flex align-items-center w-100">
                            <button class="btn btn-link p-0 me-3 d-lg-none" id="sidebarToggle">
                                <i class='bx bx-menu fs-2'></i>
                            </button>
                            
                            <a class="navbar-brand p-0 me-4" href="dashboard.php">
                                <h4 class="mb-0 text-primary">Zanvarsity</h4>
                                <small class="text-muted d-none d-md-inline">Admin Panel</small>
                            </a>
                            
                            <div class="d-none d-lg-flex align-items-center flex-grow-1">
                                <form class="w-100 me-3" style="max-width: 400px;">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm" placeholder="Search...">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class='bx bx-search'></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="d-flex align-items-center ms-auto">
                                <!-- Notifications -->
                                <div class="dropdown me-3">
                                    <a href="#" class="nav-link p-2 text-dark" data-bs-toggle="dropdown">
                                        <i class='bx bx-bell fs-5 position-relative'>
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 9px; padding: 2px 4px;">
                                                3
                                            </span>
                                        </i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end shadow" style="min-width: 300px;">
                                        <div class="dropdown-header d-flex justify-content-between align-items-center">
                                            <span>Notifications</span>
                                            <span class="badge bg-primary rounded-pill">3 New</span>
                                        </div>
                                        <div class="dropdown-divider"></div>
                                        <a href="#" class="dropdown-item d-flex py-2">
                                            <div class="me-3 text-primary">
                                                <i class='bx bx-user-plus fs-4'></i>
                                            </div>
                                            <div>
                                                <div class="small text-muted">5 minutes ago</div>
                                                <div>New user registered</div>
                                            </div>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a href="#" class="dropdown-item text-center py-2">View all notifications</a>
                                    </div>
                                </div>
                                
                                <!-- User Menu -->
                                <div class="dropdown">
                                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown">
                                        <div class="me-2 d-none d-md-block text-end">
                                            <div class="fw-semibold"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></div>
                                            <small class="text-muted"><?php echo ucfirst($_SESSION['role'] ?? 'admin'); ?></small>
                                        </div>
                                        <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class='bx bxs-user-circle fs-4 text-muted'></i>
                                        </div>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end shadow">
                                        <a class="dropdown-item" href="profile.php">
                                            <i class='bx bx-user me-2'></i>Profile
                                        </a>
                                        <a class="dropdown-item" href="settings.php">
                                            <i class='bx bx-cog me-2'></i>Settings
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger" href="/zanvarsity/logout.php">
                                            <i class='bx bx-log-out me-2'></i>Logout
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="content-wrapper">
            <!-- Page header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0"><?php echo htmlspecialchars($page_title); ?></h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($page_title); ?></li>
                    </ol>
                </nav>
            </div>
