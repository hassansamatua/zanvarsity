<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Set default title if not set
$pageTitle = isset($pageTitle) ? $pageTitle . ' - Zanvarsity' : 'Zanvarsity';
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Zanvarsity">

    <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <?php 
    // Set base URL for assets
    $base_url = isset($_SESSION['base_url']) ? rtrim($_SESSION['base_url'], '/') : '';
    $assets_url = $base_url . '/assets';
    ?>
    <!-- Local CSS Files with Fallbacks -->
    <?php if (file_exists($_SERVER['DOCUMENT_ROOT'] . $assets_url . '/css/style.css')): ?>
    <link rel="stylesheet" href="<?php echo $assets_url; ?>/css/style.css" type="text/css">
    <?php endif; ?>
    
    <?php if (file_exists($_SERVER['DOCUMENT_ROOT'] . $assets_url . '/css/admin-theme.css')): ?>
    <link rel="stylesheet" href="<?php echo $assets_url; ?>/css/admin-theme.css" type="text/css">
    <?php endif; ?>
    
    <!-- Additional CSS for admin area -->
    <style>
    /* Fallback styles if local CSS files are not available */
    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        line-height: 1.6;
        color: #333;
        background-color: #f8f9fa;
    }
    
    .table {
        width: 100%;
        margin-bottom: 1rem;
        color: #212529;
        background-color: transparent;
    }
    
    .table th,
    .table td {
        padding: 0.75rem;
        vertical-align: top;
        border-top: 1px solid #dee2e6;
    }
    
    .table thead th {
        vertical-align: bottom;
        border-bottom: 2px solid #dee2e6;
    }
    
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.05);
    }
    
    .btn {
        display: inline-block;
        font-weight: 400;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        user-select: none;
        border: 1px solid transparent;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        border-radius: 0.25rem;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, 
                    border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .btn-primary {
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }
    
    .btn-primary:hover {
        color: #fff;
        background-color: #0069d9;
        border-color: #0062cc;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }
    
    .text-danger {
        color: #dc3545 !important;
    }
    
    .text-success {
        color: #28a745 !important;
    }
    
    .badge {
        display: inline-block;
        padding: 0.25em 0.4em;
        font-size: 75%;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
    }
    
    .badge-success {
        color: #fff;
        background-color: #28a745;
    }
    
    .badge-danger {
        color: #fff;
        background-color: #dc3545;
    }
    
    .spinner-border {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        vertical-align: text-bottom;
        border: 0.25em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spinner-border .75s linear infinite;
    }
    
    @keyframes spinner-border {
        to { transform: rotate(360deg); }
    }
    
    /* Modal styles */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1050;
        display: none;
        width: 100%;
        height: 100%;
        overflow: hidden;
        outline: 0;
    }
    
    .modal-dialog {
        position: relative;
        width: auto;
        margin: 0.5rem;
        pointer-events: none;
    }
    
    .modal-content {
        position: relative;
        display: flex;
        flex-direction: column;
        width: 100%;
        pointer-events: auto;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid rgba(0, 0, 0, 0.2);
        border-radius: 0.3rem;
        outline: 0;
    }
    
    .modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 1rem;
        border-bottom: 1px solid #dee2e6;
        border-top-left-radius: 0.3rem;
        border-top-right-radius: 0.3rem;
    }
    
    .modal-body {
        position: relative;
        flex: 1 1 auto;
        padding: 1rem;
    }
    
    .modal-footer {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        padding: 0.75rem;
        border-top: 1px solid #dee2e6;
        border-bottom-right-radius: 0.3rem;
        border-bottom-left-radius: 0.3rem;
    }
    
    .modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1040;
        width: 100vw;
        height: 100vh;
        background-color: #000;
    }
    
    .modal-backdrop.fade {
        opacity: 0;
    }
    
    .modal-backdrop.show {
        opacity: 0.5;
    }
    
    .fade {
        transition: opacity 0.15s linear;
    }
    
    @media (min-width: 576px) {
        .modal-dialog {
            max-width: 500px;
            margin: 1.75rem auto;
        }
    }
    
    /* Form styles */
    .form-control {
        display: block;
        width: 100%;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .form-control:focus {
        color: #495057;
        background-color: #fff;
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    label {
        display: inline-block;
        margin-bottom: 0.5rem;
    }
    
    /* Alert styles */
    .alert {
        position: relative;
        padding: 0.75rem 1.25rem;
        margin-bottom: 1rem;
        border: 1px solid transparent;
        border-radius: 0.25rem;
    }
    
    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }
    
    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    }
    
    /* Debug panel */
    #debug-info {
        position: fixed;
        bottom: 10px;
        right: 10px;
        background: white;
        padding: 10px;
        border: 1px solid #ccc;
        z-index: 999999;
        max-width: 400px;
        max-height: 300px;
        overflow-y: auto;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    
    #debug-output {
        margin-top: 10px;
        font-family: monospace;
        font-size: 12px;
        line-height: 1.4;
    }
    
    #debug-output div {
        padding: 3px 0;
        border-bottom: 1px solid #eee;
    }
    
    #debug-output div:first-child {
        font-weight: bold;
    }
    </style>
    
    <!-- Required JavaScript Libraries with Fallbacks -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>window.jQuery || document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"><\/script>');</script>
    
    <!-- Bootstrap 5 CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery Validation -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
    
    <!-- Toastr for Notifications -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <script>
    // Initialize Toastr
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 5000
    };
    
    // Debug function for console logging
    function debugLog(message) {
        console.log(message);
        if (typeof $('#debug-output').length !== 'undefined' && $('#debug-output').length > 0) {
            $('#debug-output').prepend('<div>' + message + '</div>');
        }
    }
    
    // Document ready function
    $(document).ready(function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Initialize popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    });
    </script>
    
    <!-- Admin Sidebar Styles -->
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
    
    .sidebar-widget.text-center {
        padding: 20px 0;
        text-align: center;
    }

    .sidebar-widget:not(:last-child) {
        border-bottom: 1px solid #eee;
    }
    
    .user-avatar {
        margin-bottom: 15px;
    }
    
    .user-avatar-initials {
        width: 100px;
        height: 100px;
        margin: 0 auto 15px;
        background-color: #4caf50;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
        font-weight: bold;
        text-transform: uppercase;
        line-height: 1.2;
    }
    
    .user-avatar h4 {
        margin: 10px 0 5px;
        color: #333;
        font-size: 18px;
    }
    
    .label {
        display: inline-block;
        padding: 3px 10px;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.4;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 3px;
    }
    
    .label-primary {
        background-color: #4caf50;
        color: white;
    }
    
    .nav-dashboard {
        margin: 0;
        padding: 0;
        list-style: none;
    }
    
    .nav-dashboard > li {
        border-bottom: 1px solid #eee;
    }
    
    .nav-dashboard > li > a {
        display: block;
        padding: 12px 15px;
        color: #555;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .nav-dashboard > li > a:hover,
    .nav-dashboard > li.active > a {
        background-color: #f5f5f5;
        color: #4caf50;
        border-left: 3px solid #4caf50;
        padding-left: 12px;
    }
    
    .nav-dashboard .fa {
        width: 20px;
        margin-right: 10px;
        text-align: center;
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
        padding: 0;
        list-style: none;
    }

    .nav-dashboard > li > a {
        padding: 12px 20px;
        color: #555;
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
        display: block;
        text-decoration: none;
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
    
    <!-- JavaScript files will be loaded at the end of the body -->

    <title><?php echo htmlspecialchars($pageTitle); ?></title>
</head>

<body class="page-sub-page <?php echo isset($bodyClass) ? $bodyClass : ''; ?>">
<!-- Wrapper -->
<div class="wrapper">
<!-- Header -->
<div class="navigation-wrapper">
    <div class="secondary-navigation-wrapper">
        <div class="container">
            <div class="navigation-contact pull-left">Call Us:  <span class="opacity-70">000-123-456-789</span></div>
            <ul class="secondary-navigation list-unstyled pull-right">
                <li><a href="#">Prospective Students</a></li>
                <li><a href="#">Current Students</a></li>
                <li><a href="#">Faculty & Staff</a></li>
                <li><a href="#">Alumni</a></li>
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
                        <a href="<?php echo $base_url; ?>/index.php">
                            <img src="<?php echo $assets_url; ?>/img/logo.png" alt="Zanvarsity">
                        </a>
                    </div>
                </div>
                <nav class="collapse navbar-collapse bs-navbar-collapse navbar-right" role="navigation">
                    <ul class="nav navbar-nav">
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                            <a href="index.php">Home</a>
                        </li>
                        <li class="<?php echo strpos($_SERVER['PHP_SELF'], 'courses') !== false ? 'active' : ''; ?>">
                            <a href="courses.php">Courses</a>
                        </li>
                        <li class="<?php echo strpos($_SERVER['PHP_SELF'], 'events') !== false ? 'active' : ''; ?>">
                            <a href="events.php">Events</a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'about-us.php' ? 'active' : ''; ?>">
                            <a href="about-us.php">About Us</a>
                        </li>
                        <li class="<?php echo strpos($_SERVER['PHP_SELF'], 'blog') !== false ? 'active' : ''; ?>">
                            <a href="blog.php">Blog</a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'contact-us.php' ? 'active' : ''; ?>">
                            <a href="contact-us.php">Contact Us</a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'register-sign-in.php' ? 'active' : ''; ?>">
                            <a href="register-sign-in.php">Sign In</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </header>
    </div>
    <div class="background">
        <img src="assets/img/background-city.png" alt="background">
    </div>
</div>


