<?php
/**
 * About Header Template for Admin
 * 
 * This is a reusable header template for admin about-related pages.
 * It includes the site header, navigation, and page title section.
 */
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Zanvarsity">
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : 'Zanvarsity - Empowering Education, Enriching Lives' ?>">

    <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <link href="../assets/css/font-awesome.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/selectize.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/vanillabox/vanillabox.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/style.css" type="text/css">
    
    <!-- Page-specific CSS -->
    <?php if (isset($page_css)): ?>
    <link rel="stylesheet" href="../<?php echo ltrim($page_css, '/'); ?>" type="text/css">
    <?php endif; ?>
    
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Zanvarsity</title>
    
    <style>
        /* Navigation Styles */
        .navbar-nav > li > a,
        .navbar-nav > li > .no-link,
        .navbar-nav .open .dropdown-menu > li > a {
            color: #ffffff !important;
            transition: color 0.3s ease;
        }
        
        /* Hover states */
        .navbar-nav > li > a:hover,
        .navbar-nav > li > a:focus {
            background-color: transparent !important;
            color: #e0f2e9 !important; /* Lighter green on hover */
        }
        
        /* Active menu item */
        .navbar-nav > .active > a,
        .navbar-nav > .active > a:hover,
        .navbar-nav > .active > a:focus {
            background-color: transparent !important;
            color: #e0f2e9 !important;
            font-weight: bold;
        }
        
        /* Dropdown menu */
        .dropdown-menu {
            background-color: #004225;
            border: 1px solid #003319;
            border-radius: 0;
            box-shadow: 0 6px 12px rgba(0,0,0,0.175);
        }
        
        .dropdown-menu > li > a {
            color: #fff !important;
            padding: 8px 20px;
            transition: all 0.3s ease;
        }
        
        .dropdown-menu > li > a:hover,
        .dropdown-menu > li > a:focus {
            background-color: #006633 !important;
            color: #fff !important;
        }
        
        /* Mobile menu button */
        .navbar-toggle {
            border-color: #fff;
        }
        
        .navbar-toggle .icon-bar {
            background-color: #fff;
        }
        
        /* Page title */
        .page-title {
            background-color: #f8f9fa;
            padding: 40px 0;
            margin-bottom: 30px;
            border-bottom: 1px solid #eaeaea;
        }
        
        .page-title h1 {
            color: #004225;
            margin: 0;
            font-weight: 700;
        }
        
        .breadcrumb {
            background: none;
            padding: 8px 0;
            margin: 0;
            font-size: 12px;
        }
        
        .breadcrumb > li + li:before {
            color: #999;
            content: ">\00a0";
            padding: 0 5px;
        }
        
        .breadcrumb > .active {
            color: #666;
        }
    </style>
    
    <script>
        // Disable retina.js
        window.Retina = { dontAddRetinaClass: true, checkForChange: function() {} };
        
        // Handle all dropdown menus
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle
            var navbarToggle = document.querySelector('.navbar-toggle');
            if (navbarToggle) {
                navbarToggle.addEventListener('click', function() {
                    var target = document.querySelector(this.getAttribute('data-target'));
                    if (target) {
                        target.classList.toggle('in');
                    }
                });
            }
            
            // Dropdown menus
            var dropdowns = document.querySelectorAll('.dropdown-toggle');
            dropdowns.forEach(function(dropdown) {
                dropdown.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var parent = this.parentElement;
                    var isOpen = parent.classList.contains('open');
                    
                    // Close all other open dropdowns
                    document.querySelectorAll('.dropdown').forEach(function(el) {
                        if (el !== parent) {
                            el.classList.remove('open');
                        }
                    });
                    
                    // Toggle current dropdown
                    if (!isOpen) {
                        parent.classList.add('open');
                    }
                });
            });
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', function() {
                document.querySelectorAll('.dropdown').forEach(function(el) {
                    el.classList.remove('open');
                });
            });
        });
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
                    <li><a href="#" style="color: #fff;"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="#" style="color: #fff;"><i class="fa fa-twitter"></i></a></li>
                    <li><a href="#" style="color: #fff;"><i class="fa fa-youtube"></i></a></li>
                    <li><a href="#" style="color: #fff;"><i class="fa fa-instagram"></i></a></li>
                    <li><a href="#" style="color: #fff;"><i class="fa fa-linkedin"></i></a></li>
                </ul>
            </div>
        </div>
        
        <div class="primary-navigation-wrapper" style="background-color: #004225;">
            <header class="navbar" id="top" role="banner">
                <div class="container">
                    <div class="navbar-header">
                        <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a href="../index.php" class="navbar-brand">
                            <img src="../assets/img/logo-white.png" alt="Zanvarsity" class="img-responsive">
                        </a>
                    </div>
                    
                    <nav class="collapse navbar-collapse bs-navbar-collapse navbar-right" role="navigation">
                        <ul class="nav navbar-nav">
                            <li<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? ' class="active"' : ''; ?>>
                                <a href="../index.php">Home</a>
                            </li>
                            <li<?php echo (in_array(basename($_SERVER['PHP_SELF']), ['about.php', 'vision_mission.php', 'board_of_trustees.php', 'principal_officers.php', 'council_board.php', 'senate_board.php', 'about-us.html', 'leadership.php', 'history.php', 'darul_iman.php'])) ? ' class="active"' : ''; ?>>
                                <a href="#" class="has-child no-link" style="color: #fff; transition: color 0.3s ease; padding: 15px 20px; display: block;">About Us</a>
                                <ul class="list-unstyled child-navigation" style="background-color: #004225; border: 1px solid #003319; min-width: 200px;">
                                    <li><a href="../about.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;">About Zanvarsity</a></li>
                                    <li><a href="../vision_mission.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;">Vision & Mission</a></li>
                                    <li><a href="../board_of_trustees.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;">Board of Trustees</a></li>
                                    <li><a href="../principal_officers.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;">Principal Officers</a></li>
                                    <li><a href="../council_board.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;">Council Board</a></li>
                                    <li><a href="../senate_board.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;">Senate Board</a></li>
                                    <li><a href="../leadership.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;">Leadership</a></li>
                                    <li><a href="../history.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;">History</a></li>
                                    <li><a href="../darul_iman.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;">Darul Iman</a></li>
                                </ul>
                            </li>
                            
                            <!-- Add other navigation items as needed for admin -->
                            <li><a href="dashboard.php">Dashboard</a></li>
                            <li><a href="users.php">Users</a></li>
                            <li><a href="settings.php">Settings</a></li>
                            
                            <!-- User dropdown -->
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-user"></i> 
                                    <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Account'; ?> 
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="profile.php"><i class="fa fa-user"></i> My Profile</a></li>
                                    <li><a href="settings.php"><i class="fa fa-cog"></i> Settings</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="../logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                </div>
                <!-- /.container -->
            </header>
            <!-- /.navbar -->
        </div>
        <!-- /.primary-navigation -->
        <div class="background">
            <img src="../assets/img/background-city.png" alt="background" />
        </div>
    </div>
    <!-- end Header -->

    <!-- Page Title Section -->
    <section class="page-title" style="background-color: #f8f9fa; padding: 40px 0; margin-bottom: 30px; border-bottom: 1px solid #eaeaea;">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h1><?php echo isset($page_title) ? $page_title : 'Admin Panel'; ?></h1>
                </div>
                <div class="col-md-6">
                    <ol class="breadcrumb pull-right">
                        <li><a href="../index.php">Home</a></li>
                        <?php if (isset($breadcrumbs)): ?>
                            <?php foreach ($breadcrumbs as $title => $url): ?>
                                <?php if ($url): ?>
                                    <li><a href="<?php echo $url; ?>"><?php echo $title; ?></a></li>
                                <?php else: ?>
                                    <li class="active"><?php echo $title; ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="active"><?php echo isset($page_title) ? $page_title : 'Admin'; ?></li>
                        <?php endif; ?>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Page Content -->
    <div id="page-content">
