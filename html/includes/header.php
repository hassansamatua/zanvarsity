<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Set default title if not set
$pageTitle = isset($page_title) ? $page_title : (isset($pageTitle) ? $pageTitle . ' - Zanvarsity' : 'Zanvarsity');
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Zanvarsity">

    <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <?php 
    // Determine the correct asset path based on where this file is being included from
    $current_dir = dirname($_SERVER['PHP_SELF']);
    $asset_path = '';
    
    // If we're in a subdirectory (like admin), we need to go up one level
    if (strpos($current_dir, '/admin') !== false) {
        $asset_path = '../';
    }
    
    $assets_url = $asset_path . 'assets';
    ?>
    <!-- Local CSS Files -->
    <link href="<?php echo $assets_url; ?>/css/font-awesome.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?php echo $assets_url; ?>/bootstrap/css/bootstrap.css" type="text/css">
    <link rel="stylesheet" href="<?php echo $assets_url; ?>/css/selectize.css" type="text/css">
    <link rel="stylesheet" href="<?php echo $assets_url; ?>/css/owl.carousel.css" type="text/css">
    <link rel="stylesheet" href="<?php echo $assets_url; ?>/css/vanillabox/vanillabox.css" type="text/css">
    <link rel="stylesheet" href="<?php echo $assets_url; ?>/css/style.css" type="text/css">
    <link rel="stylesheet" href="<?php echo $assets_url; ?>/css/green-theme.css" type="text/css">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo $assets_url; ?>/img/favicon.ico" type="image/x-icon">
    
    <!-- Additional CSS for admin area -->
    <style>
    /* Header Layout Styles */
    .primary-navigation-wrapper .container {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        flex-wrap: nowrap !important;
    }
    
    .navbar-brand {
        flex-shrink: 0 !important;
        margin-right: 60px !important;
        margin-bottom: 0 !important;
    }
    
    .navbar-collapse {
        display: flex !important;
        flex: 1 !important;
        border: none !important;
        box-shadow: none !important;
    }
    
    .nav.navbar-nav {
        display: flex !important;
        flex-direction: row !important;
        margin: 0 !important;
        padding: 0 !important;
        list-style: none !important;
    }
    
    .nav.navbar-nav > li {
        display: inline-block !important;
        float: none !important;
        margin: 0 !important;
        position: relative !important;
    }
    
    .nav.navbar-nav > li > a {
        display: block !important;
        padding: 15px 20px !important;
        color: #fff !important;
        text-decoration: none !important;
        transition: all 0.3s ease !important;
    }
    
    .nav.navbar-nav > li > a:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
        color: #e0f2e9 !important;
    }
    
    /* Disable retina images to prevent 404 errors */
    img {
        image-rendering: auto !important;
    }
    
    /* Prevent @2x image loading */
    @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
        .background img,
        .navbar-brand img,
        .logo img {
            /* Force normal resolution images */
            image-rendering: auto !important;
        }
    }
    
    /* Mobile responsive */
    @media (max-width: 768px) {
        .primary-navigation-wrapper .container {
            flex-direction: column !important;
            align-items: flex-start !important;
        }
        
        .navbar-toggle {
            display: block !important;
            margin-left: auto !important;
        }
        
        .navbar-collapse {
            display: none !important;
            width: 100% !important;
        }
        
        .navbar-collapse.in {
            display: block !important;
        }
        
        .nav.navbar-nav {
            flex-direction: column !important;
        }
        
        .nav.navbar-nav > li {
            display: block !important;
            width: 100% !important;
        }
    }
    
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
    
    <!-- Disable retina.js to prevent 404 errors -->
    <script>
        window.devicePixelRatio = 1;
        window.Retina = { 
            dontAddRetinaClass: true, 
            checkForChange: function() {},
            init: function() {}
        };
    </script>
</head>

<body class="page-sub-page page-about">
<!-- Wrapper -->
<div class="wrapper">
<!-- Header -->
<div class="navigation-wrapper">
    <div class="secondary-navigation-wrapper" style="background-color: #004225;">
        <div class="container">
            <div class="navigation-contact pull-left">Call Us:  <span class="opacity-70">+255 772 601 303</span></div>
            <ul class="secondary-navigation list-unstyled pull-right" style="margin: 0; padding: 0;">
                <li><a href="https://zumis.ac.tz/" target="_blank" style="color: #fff; text-decoration: none; transition: color 0.3s ease;"><i class="fa fa-user" style="color: #98FB98; margin-right: 5px;"></i>Zumis Portal</a></li>
                <li><a href="<?php echo $asset_path; ?>uploads/doc/prospectus.pdf" target="_blank">Prospectus</a></li>
                <li><a href="<?php echo $asset_path; ?>uploads/doc/almanac-2023.pdf" target="_blank">Almanac</a></li>
                <li><a href="<?php echo $asset_path; ?>fee-structure.php" target="_blank">Fee Structure</a></li>
                <li><a href="<?php echo $asset_path; ?>alumni.php" target="_blank">Alumni</a></li>
                <li><a href="<?php echo $asset_path; ?>sign-in.php" style="color: #fff; text-decoration: none; transition: color 0.3s ease;"><i class="fa fa-sign-in" style="color: #98FB98; margin-right: 5px;"></i>Admin Login</a></li>
            </ul>
        </div>
    </div><!-- /.secondary-navigation -->
    <div class="primary-navigation-wrapper" style="background-color: #004225;">
        <header class="navbar" id="top" role="banner">
            <div class="container" style="display: flex; align-items: center; justify-content: flex-start;">
                <!-- Logo on the left -->
                <div class="navbar-brand" id="brand" style="margin-right: 60px; margin-bottom: 0;">
                    <a href="<?php echo $asset_path; ?>index.php">
                        <img src="<?php echo $assets_url; ?>/img/logo11.png" alt="Zanvarsity" style="height: 50px; width: auto;">
                    </a>
                </div>
                
                <!-- Mobile menu toggle -->
                <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse" style="margin-left: auto; display: none; background: transparent; border: 1px solid #fff; padding: 4px 6px;">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar" style="background-color: #fff; display: block; width: 22px; height: 2px; margin: 3px 0;"></span>
                    <span class="icon-bar" style="background-color: #fff; display: block; width: 22px; height: 2px; margin: 3px 0;"></span>
                    <span class="icon-bar" style="background-color: #fff; display: block; width: 22px; height: 2px; margin: 3px 0;"></span>
                </button>
                
                <!-- Navigation menu right after logo -->
                <nav class="navbar-collapse bs-navbar-collapse" role="navigation" style="display: flex; flex: 1;">
                    <ul class="nav navbar-nav">
                        <li<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? ' class="active"' : ''; ?>>
                            <a href="<?php echo $asset_path; ?>index.php">Home</a>
                        </li>
                        <li<?php echo (in_array(basename($_SERVER['PHP_SELF']), ['Background_info.php', 'vision_mission.php', 'board_of_trustees.php', 'principal_officers.php', 'council_board.php', 'senate_board.php', 'about-us.html', 'leadership.php', 'history.php', 'darul_iman.php'])) ? ' class="active"' : ''; ?>>
                            <a href="#" class="has-child no-link" style="color: #fff; transition: color 0.3s ease; padding: 15px 20px; display: block;">About</a>
                            <ul class="list-unstyled child-navigation" style="background-color: #004225; border: 1px solid #003319; min-width: 200px;">
                                <li><a href="<?php echo $asset_path; ?>Background_info.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;">Background Information</a></li>
                                <li><a href="<?php echo $asset_path; ?>vision_mission.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Vision & Mission</a></li>
                                <li><a href="<?php echo $asset_path; ?>board_of_trustees.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Board of Trustees</a></li>
                                <li><a href="<?php echo $asset_path; ?>darul_iman.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Darul Iman (DICA)</a></li>
                                <li><a href="<?php echo $asset_path; ?>council_board.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Council Board</a></li>
                                <li><a href="<?php echo $asset_path; ?>principal_officers.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Principal Officers</a></li>
                                <li><a href="<?php echo $asset_path; ?>senate_board.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Senate Board</a></li>
                            </ul>
                        </li>
                        <li<?php echo (in_array(basename($_SERVER['PHP_SELF']), ['how_to_apply.php', 'entry_requirements.php', 'programmes_offered.php', 'fee_structure.php', 'how_to_pay.php', 'student_transfers.php', 'postponent_transfer.php', 'credit_transfer.php', 'international_students.php', 'mature_age.php', 'special_admissions.php', 'faq.php'])) ? ' class="active"' : ''; ?>>
                         <a href="#" class="has-child no-link" style="color: #fff; transition: color 0.3s ease; padding: 15px 20px; display: block;">Admissions</a>
                            <ul class="list-unstyled child-navigation" style="background-color: #004225; border: 1px solid #003319; min-width: 200px;">
                                <li><a href="<?php echo $asset_path; ?>how_to_apply.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">How to Apply</a></li>
                                <li><a href="<?php echo $asset_path; ?>entry_requirements.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Entry Requirements</a></li>
                                <li><a href="<?php echo $asset_path; ?>programmes_offered.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Programs Offered</a></li>
                            </ul>
                        </li>
                        <li<?php echo (in_array(basename($_SERVER['PHP_SELF']), ['faculty.php'])) ? ' class="active"' : ''; ?>>
                         <a href="#" class="has-child no-link" style="color: #fff; transition: color 0.3s ease; padding: 15px 20px; display: block;">Academic</a>
                            <ul class="list-unstyled child-navigation" style="background-color: #004225; border: 1px solid #003319; min-width: 200px;">
                                <li><a href="<?php echo $asset_path; ?>faculty.php?id=1" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Faculty Of Business Administration</a></li>
                                <li><a href="<?php echo $asset_path; ?>research.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Research & Publications</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="has-child no-link">Directorates</a>
                            <ul class="list-unstyled child-navigation" style="background-color: #004225; border: 1px solid #003319; min-width: 200px;">
                                <li><a href="#" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Academic Affairs</a></li>
                                <li><a href="#" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Administration</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="has-child no-link" style="color: #fff; transition: color 0.3s ease; padding: 15px 20px; display: block;">Facilities</a>
                            <ul class="list-unstyled child-navigation" style="background-color: #004225; border: 1px solid #003319; min-width: 200px;">
                                <li><a href="#" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Library</a></li>
                                <li><a href="#" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Laboratories</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="<?php echo $asset_path; ?>contact-us.php">Contact Us</a>
                        </li>
                    </ul>
                </nav>
                <!-- /.navbar collapse-->
            </div>
            <!-- /.container -->
        </header>
    </div>
    <div class="background">
        <img src="<?php echo $assets_url; ?>/img/background-city.png" alt="background" />
    </div>
</div>
<!-- end Header -->

<!-- Page Title Section -->
<section class="page-title" style="background-color: #f8f9fa; padding: 40px 0; margin-bottom: 30px; border-bottom: 1px solid #eaeaea;">
    <div class="container">
        <header style="text-align: center;">
            <h1 style="color: #004225; font-size: 2.5rem; font-weight: 600; margin: 0 0 15px 0; line-height: 1.2;">
                <?php echo isset($page_heading) ? htmlspecialchars($page_heading) : 'Zanvarsity'; ?>
            </h1>
        </header>
    </div>
</section>
<!-- end Page Title Section -->

<!-- Page Content -->
<div id="page-content">

<script>
// Handle all dropdown menus
document.addEventListener('DOMContentLoaded', function() {
    // Get all menu items with dropdowns
    const dropdownItems = document.querySelectorAll('.has-child');
    
    // Function to handle dropdown behavior
    function setupDropdowns() {
        dropdownItems.forEach(item => {
            const link = item.querySelector('a');
            const submenu = item.querySelector('.child-navigation');
            
            if (link && submenu) {
                // Remove any existing event listeners to prevent duplicates
                link.removeEventListener('mouseenter', showSubmenu);
                link.removeEventListener('mouseleave', hideSubmenu);
                submenu.removeEventListener('mouseenter', showSubmenu);
                submenu.removeEventListener('mouseleave', hideSubmenu);
                
                // Add new event listeners
                link.addEventListener('mouseenter', showSubmenu);
                link.addEventListener('mouseleave', hideSubmenu);
                submenu.addEventListener('mouseenter', showSubmenu);
                submenu.addEventListener('mouseleave', hideSubmenu);
            }
        });
    }
    
    // Show submenu and handle chevron rotation
    function showSubmenu(e) {
        const parentLi = this.closest('li');
        if (!parentLi) return;
        
        const submenu = parentLi.querySelector('.child-navigation');
        const chevron = this.querySelector('.fa-chevron-right');
        
        if (submenu) {
            submenu.style.display = 'block';
        }
        if (chevron) {
            chevron.style.transform = 'rotate(90deg)';
        }
    }
    
    // Hide submenu and reset chevron
    function hideSubmenu(e) {
        // Only proceed if we're not hovering over the submenu or its parent
        if (e.relatedTarget && (this.contains(e.relatedTarget) || this.closest('li').contains(e.relatedTarget))) {
            return;
        }
        
        const parentLi = this.closest('li');
        if (!parentLi) return;
        
        const submenu = parentLi.querySelector('.child-navigation');
        const chevron = parentLi.querySelector('.fa-chevron-right');
        
        if (submenu) {
            submenu.style.display = 'none';
        }
        if (chevron) {
            chevron.style.transform = 'rotate(0deg)';
        }
    }
    
    // Initialize dropdowns
    setupDropdowns();
    
    // Re-initialize dropdowns after AJAX or other dynamic content loads
    document.addEventListener('click', function() {
        // Small delay to ensure any dynamic content has been added
        setTimeout(setupDropdowns, 100);
    });
    
    // Handle mobile menu toggle
    const mobileToggle = document.querySelector('.navbar-toggle');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    if (mobileToggle && navbarCollapse) {
        mobileToggle.addEventListener('click', function() {
            navbarCollapse.classList.toggle('in');
        });
    }
});
</script>


