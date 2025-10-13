<?php
/**
 * About Header Template
 * 
 * This is a reusable header template for about-related pages.
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
    <link href="assets/css/font-awesome.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.css" type="text/css">
    <link rel="stylesheet" href="assets/css/selectize.css" type="text/css">
    <link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
    <link rel="stylesheet" href="assets/css/vanillabox/vanillabox.css" type="text/css">
    <link rel="stylesheet" href="assets/css/style.css" type="text/css">
    
    <!-- Page-specific CSS -->
    <?php if (isset($page_css)): ?>
    <link rel="stylesheet" href="<?php echo $page_css; ?>" type="text/css">
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
        .navbar-nav > .active > a:focus,
        .navbar-nav > .current-menu-item > a,
        .navbar-nav > .current-menu-parent > a,
        .navbar-nav > .current-page-ancestor > a,
        .navbar-nav > .current_page_item > a,
        .navbar-nav > .current_page_parent > a {
            background-color: transparent !important;
            color: #5cb85c !important; /* Success green text to match buttons */
            font-weight: bold !important;
        }
        
        /* Ensure dropdown active items are also styled */
        .dropdown-menu > .active > a,
        .dropdown-menu > .current-menu-item > a,
        .dropdown-menu > .current-menu-parent > a,
        .dropdown-menu > li > a.active {
            background-color: transparent !important;
            color: #5cb85c !important; /* Success green text to match buttons */
            font-weight: bold !important;
        }
        
        /* Ensure the active state is visible in dropdowns */
        .child-navigation > li > a.active {
            color: #5cb85c !important; /* Success green text to match buttons */
            font-weight: bold !important;
        }
    </style>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/img/favicon.ico" type="image/x-icon">
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script>
        // Disable retina.js
        window.Retina = { dontAddRetinaClass: true, checkForChange: function() {} };
        
        // Handle all dropdown menus
        document.addEventListener('DOMContentLoaded', function() {
            // Get all menu items with dropdowns
            const dropdownItems = document.querySelectorAll('.has-child');
            
            // Add event listeners to each dropdown item
            dropdownItems.forEach(item => {
                const link = item.querySelector('a');
                const submenu = item.querySelector('.child-navigation');
                
                if (link && submenu) {
                    // Show submenu on hover
                    item.addEventListener('mouseenter', function() {
                        submenu.style.display = 'block';
                        const chevron = link.querySelector('.fa-chevron-right');
                        if (chevron) {
                            chevron.style.transform = 'rotate(90deg)';
                        }
                    });
                    
                    // Hide submenu when mouse leaves the item
                    item.addEventListener('mouseleave', function() {
                        submenu.style.display = 'none';
                        const chevron = link.querySelector('.fa-chevron-right');
                        if (chevron) {
                            chevron.style.transform = 'rotate(0deg)';
                        }
                    });
                    
                    // Keep submenu open when hovering over it
                    submenu.addEventListener('mouseenter', function() {
                        this.style.display = 'block';
                        const chevron = link.querySelector('.fa-chevron-right');
                        if (chevron) {
                            chevron.style.transform = 'rotate(90deg)';
                        }
                    });
                    
                    submenu.addEventListener('mouseleave', function() {
                        this.style.display = 'none';
                        const chevron = link.querySelector('.fa-chevron-right');
                        if (chevron) {
                            chevron.style.transform = 'rotate(0deg)';
                        }
                    });
                }
            });
            
            // Close all dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.has-child')) {
                    document.querySelectorAll('.child-navigation').forEach(menu => {
                        menu.style.display = 'none';
                    });
                    document.querySelectorAll('.fa-chevron-right').forEach(chevron => {
                        chevron.style.transform = 'rotate(0deg)';
                    });
                }
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
                <li><a href="https://zumis.ac.tz/" target="_blank" style="color: #fff; text-decoration: none; transition: color 0.3s ease;"><i class="fa fa-user" style="color: #98FB98; margin-right: 5px;"></i>Zumis Portal</a></li>
                <li><a href="../html/uploads/doc/prospectus.pdf" target="_blank">Prospectus</a></li>
                <li><a href="../html/uploads/doc/almanac-2023.pdf" target="_blank">Almanac</a></li>
                <li><a href="fee-structure.php" target="_blank">Fee Structure</a></li>
                <li><a href="alumni.php" target="_blank">Alumni</a></li>
                <li><a href="sign-in.php" style="color: #fff; text-decoration: none; transition: color 0.3s ease;"><i class="fa fa-sign-in" style="color: #98FB98; margin-right: 5px;"></i>Admin Login</a></li>
            </ul>
        </div>
    </div><!-- /.secondary-navigation -->
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
                    <div class="navbar-brand nav" id="brand">
                        <a href="index.php"><img src="assets/img/logo11.png" alt="Zanvarsity"></a>
                    </div>
                </div>
                <nav class="collapse navbar-collapse bs-navbar-collapse navbar-right" role="navigation">
                    <ul class="nav navbar-nav">
                        <li<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? ' class="active"' : ''; ?>>
                            <a href="index.php">Home</a>
                        </li>
                        <li<?php echo (in_array(basename($_SERVER['PHP_SELF']), ['Background_info.php', 'vision_mission.php', 'board_of_trustees.php', 'principal_officers.php', 'council_board.php', 'senate_board.php', 'about-us.html', 'leadership.php', 'history.php', 'darul_iman.php'])) ? ' class="active"' : ''; ?>>
                            <a href="#" class="has-child no-link" style="color: #fff; transition: color 0.3s ease; padding: 15px 20px; display: block;">About</a>
                            <ul class="list-unstyled child-navigation" style="background-color: #004225; border: 1px solid #003319; min-width: 200px;">
                                <li><a href="Background_info.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;">Background Information</a></li>
                                <li><a href="vision_mission.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Vision & Mission</a></li>
                                <li><a href="board_of_trustees.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Board of Trustees</a></li>
                                <li><a href="darul_iman.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Darul Iman (DICA)</a></li>
                                <li><a href="council_board.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Council Board</a></li>
                                <li><a href="principal_officers.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Principal Officers</a></li>
                                <li><a href="senate_board.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Senate Board</a></li>
                                
                               
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="has-child no-link" style="color: #fff; transition: color 0.3s ease; padding: 15px 20px; display: block;">Admissions</a>
                            <ul class="list-unstyled child-navigation" style="background-color: #004225; border: 1px solid #003319; min-width: 200px;">
                                <li class="has-child">
                                    <a href="#" class="no-link" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease; position: relative;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">
                                        Applications <i class="fa fa-chevron-right" style="float: right; margin-top: 5px; transition: transform 0.3s ease;"></i>
                                    </a>
                                    <ul class="list-unstyled child-navigation" style="position: absolute; left: 100%; top: 0; min-width: 220px; background-color: #006633; border: 1px solid #005229; display: none;">
                                        <li><a href="how_to_apply.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">How to Apply</a></li>
                                        <li><a href="entry_requirements.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Entry Requirements</a></li>
                                        <li><a href="programmes_offered.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Programs Offered</a></li>
                                        <li><a href="https://www.zumis.ac.tz/admission/data/register" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Online Application</a></li>
                                    </ul>
                                </li>
                                <li class="has-child">
                                    <a href="#" class="no-link" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease; position: relative;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">
                                        Fees <i class="fa fa-chevron-right" style="float: right; margin-top: 5px; transition: transform 0.3s ease;"></i>
                                    </a>
                                    <ul class="list-unstyled child-navigation" style="position: absolute; left: 100%; top: 0; min-width: 220px; background-color: #006633; border: 1px solid #005229; display: none;">
                                        <li><a href="fee_structure.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Fee Structure</a></li>
                                        <li><a href="how_to_pay.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Payment Methods</a></li>
                                        <!-- <li><a href="scholarships.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Scholarships</a></li>
                                        <li><a href="financial_aid.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Financial Aid</a></li> -->
                                    </ul>
                                </li>
                                <li class="has-child">
                                    <a href="#" class="no-link" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease; position: relative;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">
                                        Transfers <i class="fa fa-chevron-right" style="float: right; margin-top: 5px; transition: transform 0.3s ease;"></i>
                                    </a>
                                    <ul class="list-unstyled child-navigation" style="position: absolute; left: 100%; top: 0; min-width: 220px; background-color: #006633; border: 1px solid #005229; display: none;">
                                        <li><a href="student_transfers.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Students Transfers</a></li>
                                        <li><a href="postponent_transfer.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Postponent & Resuption of Studies</a></li>
                                        <li><a href="credit_transfer.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Credit Transfer</a></li>
                                    </ul>
                                </li>
                                <li class="has-child">
                                    <a href="#" class="no-link" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease; position: relative;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">
                                        Others <i class="fa fa-chevron-right" style="float: right; margin-top: 5px; transition: transform 0.3s ease;"></i>
                                    </a>
                                    <ul class="list-unstyled child-navigation" style="position: absolute; left: 100%; top: 0; min-width: 220px; background-color: #006633; border: 1px solid #005229; display: none;">
                                        <li><a href="international_students.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">International Students</a></li>
                                        <li><a href="mature_age.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Mature Age Entry</a></li>
                                        <li><a href="special_admissions.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Special Admissions</a></li>
                                        <li><a href="faq.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">FAQs</a></li>
                                    </ul>
                                </li>
                                <li><a href="course-detail-v3.html">Course Detail v3</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="has-child no-link">Events</a>
                            <ul class="list-unstyled child-navigation" style="background-color: #004225; border: 1px solid #003319; min-width: 200px;">
                                <li><a href="event-listing-images.html">Events Listing with images</a></li>
                                <li><a href="event-listing.html">Events Listing</a></li>
                                <li><a href="event-grid.html">Events Grid</a></li>
                                <li><a href="event-detail.html">Event Detail</a></li>
                                <li><a href="event-calendar.html">Events Calendar</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="has-child no-link">Directorates</a>
                            <ul class="list-unstyled child-navigation" style="background-color: #004225; border: 1px solid #003319; min-width: 200px;">
                                <li><a href="#">Academic Affairs</a></li>
                                <li><a href="#">Administration</a></li>
                                <li><a href="#">Finance</a></li>
                                <li><a href="#">Research & Innovation</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="has-child no-link">Blog</a>
                            <ul class="list-unstyled child-navigation" style="background-color: #004225; border: 1px solid #003319; min-width: 200px;">
                                <li><a href="blog-listing.html">Blog Listing</a></li>
                                <li><a href="blog-detail.html">Blog Detail</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="has-child no-link" style="color: #fff; transition: color 0.3s ease; padding: 15px 20px; display: block;">Pages</a>
                            <ul class="list-unstyled child-navigation" style="background-color: #004225; border: 1px solid #003319; min-width: 200px;">
                                <li><a href="full-width.html" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;">Fullwidth</a></li>
                                <li><a href="left-sidebar.html" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;">Left Sidebar</a></li>
                                <li><a href="right-sidebar.html" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;">Right Sidebar</a></li>
                                <li><a href="my-account.html">My Account</a></li>
                                <li><a href="register-sign-in.html">Register & Sign In</a></li>
                                <li><a href="members.html">Members</a></li>
                                <li><a href="member-detail.html">Member Detail</a></li>
                                <li><a href="shortcodes.html">Shortcodes</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="has-child no-link" style="color: #fff; transition: color 0.3s ease; padding: 15px 20px; display: block;">Facilities</a>
                                    <ul class="list-unstyled child-navigation" style="background-color: #004225; border: 1px solid #003319; min-width: 200px;">
                                        <li><a href="full-width.html" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;">Fullwidth</a></li>
                                        <li><a href="left-sidebar.html" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;">Left Sidebar</a></li>
                                        <li><a href="right-sidebar.html" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;">Right Sidebar</a></li>
                                        <li><a href="microsite.html">Microsite</a></li>
                                        <li><a href="my-account.html">My Account</a></li>
                                        <li><a href="members.html">Members</a></li>
                                        <li><a href="member-detail.html">Member Detail</a></li>
                                        <li>
                                            <a href="register-sign-in.html">Register & Sign In</a>
                                        </li>
                                        <li><a href="shortcodes.html">Shortcodes</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="contact-us.html">Contact Us</a>
                                </li>
                            </ul>
                        </nav>
                        <!-- /.navbar collapse-->
                    </div>
                    <!-- /.container -->
                </header>
                <!-- /.navbar -->
            </div>
            <!-- /.primary-navigation -->
            <div class="background">
                <img src="assets/img/background-city.png" alt="background" />
            </div>
        </div>
        <!-- end Header -->

        <!-- Page Title Section -->
        <section class="page-title" style="background-color: #f8f9fa; padding: 40px 0; margin-bottom: 30px; border-bottom: 1px solid #eaeaea;">
            <div class="container">
                <header style="text-align: center;">
                    <h1 style="color: #004225; font-size: 2.5rem; font-weight: 600; margin: 0 0 15px 0; line-height: 1.2;">
                        <?php echo isset($page_heading) ? htmlspecialchars($page_heading) : 'About Us'; ?>
                    </h1>
                </header>
            </div>
        </section>
        <!-- end Page Title Section -->
        
        <!-- Page Content -->
  