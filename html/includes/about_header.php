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
    </script>
</head>

<body class="page-sub-page page-about">
<!-- Wrapper -->
<div class="wrapper">
<!-- Header -->
<div class="navigation-wrapper">
    <div class="secondary-navigation-wrapper">
        <div class="container">
            <div class="navigation-contact pull-left">Call Us:  <span class="opacity-70">+255 772 601 303</span></div>
            <ul class="secondary-navigation list-unstyled pull-right">
                <li><a href="https://zumis.ac.tz/" target="_blank"><i class="fa fa-user"></i> Zumis Portal</a></li>
                <li><a href="../html/uploads/doc/prospectus.pdf" target="_blank">Prospectus</a></li>
                <li><a href="../html/uploads/doc/almanac-2023.pdf" target="_blank">Almanac</a></li>
                <li><a href="fee-structure.php" target="_blank">Fee Structure</a></li>
                <li><a href="alumni.php" target="_blank">Alumni</a></li>
                <li><a href="admin/login.php"><i class="fa fa-sign-in"></i> Admin Login</a></li>
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
                        <a href="index.php"><img src="assets/img/logo11.png" alt="Zanvarsity"></a>
                    </div>
                </div>
                <nav class="collapse navbar-collapse bs-navbar-collapse navbar-right" role="navigation">
                    <ul class="nav navbar-nav">
                        <li<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? ' class="active"' : ''; ?>>
                            <a href="index.php">Home</a>
                        </li>
                        <li<?php echo (in_array(basename($_SERVER['PHP_SELF']), ['Background_info.php', 'vision_mission.php', 'board_of_trustees.php', 'principal_officers.php', 'council_board.php', 'senate_board.php', 'about-us.html', 'leadership.php', 'history.php', 'darul_iman.php'])) ? ' class="active"' : ''; ?>>
                            <a href="#" class="has-child no-link">About</a>
                            <ul class="list-unstyled child-navigation">
                                <li><a href="Background_info.php">Background Information</a></li>
                                <li><a href="vision_mission.php">Vision & Mission</a></li>
                                <li><a href="board_of_trustees.php">Board of Trustees</a></li>
                                <li><a href="principal_officers.php">Principal Officers</a></li>
                                <li><a href="council_board.php">Council Board</a></li>
                                <li><a href="senate_board.php">Senate Board</a></li>
                                <li><a href="darul_iman.php">Darul Iman (DICA)</a></li>
                                <li><a href="about-us.html">About University</a></li>
                                <li><a href="#">Leadership</a></li>
                                <li><a href="#">History</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="has-child no-link">Academics</a>
                            <ul class="list-unstyled child-navigation">
                                <li><a href="course-listing.php">Course Listing</a></li>
                                <li><a href="course-listing-images.html">Course Listing with Images</a></li>
                                <li><a href="course-detail-v1.html">Course Detail v1</a></li>
                                <li><a href="course-detail-v2.html">Course Detail v2</a></li>
                                <li><a href="course-detail-v3.html">Course Detail v3</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="has-child no-link">Events</a>
                            <ul class="list-unstyled child-navigation">
                                <li><a href="event-listing-images.html">Events Listing with images</a></li>
                                <li><a href="event-listing.html">Events Listing</a></li>
                                <li><a href="event-grid.html">Events Grid</a></li>
                                <li><a href="event-detail.html">Event Detail</a></li>
                                <li><a href="event-calendar.html">Events Calendar</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="has-child no-link">Directorates</a>
                            <ul class="list-unstyled child-navigation">
                                <li><a href="#">Academic Affairs</a></li>
                                <li><a href="#">Administration</a></li>
                                <li><a href="#">Finance</a></li>
                                <li><a href="#">Research & Innovation</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="has-child no-link">Blog</a>
                            <ul class="list-unstyled child-navigation">
                                <li><a href="blog-listing.html">Blog Listing</a></li>
                                <li><a href="blog-detail.html">Blog Detail</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="has-child no-link">Pages</a>
                            <ul class="list-unstyled child-navigation">
                                <li><a href="full-width.html">Fullwidth</a></li>
                                <li><a href="left-sidebar.html">Left Sidebar</a></li>
                                <li><a href="right-sidebar.html">Right Sidebar</a></li>
                                <li><a href="my-account.html">My Account</a></li>
                                <li><a href="register-sign-in.html">Register & Sign In</a></li>
                                <li><a href="members.html">Members</a></li>
                                <li><a href="member-detail.html">Member Detail</a></li>
                                <li><a href="shortcodes.html">Shortcodes</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="has-child no-link">Facilities</a>
                                    <ul class="list-unstyled child-navigation">
                                        <li><a href="full-width.html">Fullwidth</a></li>
                                        <li><a href="left-sidebar.html">Left Sidebar</a></li>
                                        <li><a href="right-sidebar.html">Right Sidebar</a></li>
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
  
