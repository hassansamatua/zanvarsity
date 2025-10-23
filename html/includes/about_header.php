<?php
/**
 * About Header Template
 * 
 * This is a reusable header template for about-related pages.
 * It includes the site header, navigation, and page title section.
 */

// Determine the correct asset path based on where this file is being included from
$current_dir = dirname($_SERVER['PHP_SELF']);
$asset_path = '';

// If we're in a subdirectory (like admin), we need to go up one level
if (strpos($current_dir, '/admin') !== false) {
    $asset_path = '../';
} elseif (strpos($current_dir, '/includes') !== false) {
    $asset_path = '../';
}
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Zanvarsity">
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : 'Zanvarsity - Empowering Education, Enriching Lives' ?>">

    <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <link href="<?php echo $asset_path; ?>assets/css/font-awesome.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?php echo $asset_path; ?>assets/bootstrap/css/bootstrap.css" type="text/css">
    <link rel="stylesheet" href="<?php echo $asset_path; ?>assets/css/selectize.css" type="text/css">
    <link rel="stylesheet" href="<?php echo $asset_path; ?>assets/css/owl.carousel.css" type="text/css">
    <link rel="stylesheet" href="<?php echo $asset_path; ?>assets/css/vanillabox/vanillabox.css" type="text/css">
    <link rel="stylesheet" href="<?php echo $asset_path; ?>assets/css/style.css" type="text/css">
    
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
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
            background-color: transparent !important; /* Transparent background */
            color: #4caf50 !important; /* Light green text */
            font-weight: bold !important;
            text-shadow: 0 0 3px rgba(76, 175, 80, 0.3); /* Subtle glow effect */
        }
        
        /* Ensure dropdown active items are also styled */
        .dropdown-menu > .active > a,
        .dropdown-menu > .current-menu-item > a,
        .dropdown-menu > .current-menu-parent > a,
        .dropdown-menu > li > a.active {
            background-color: transparent !important;
            color: #4caf50 !important; /* Light green text */
            font-weight: bold !important;
            text-shadow: 0 0 3px rgba(76, 175, 80, 0.3); /* Subtle glow effect */
        }
        
        /* Ensure the active state is visible in dropdowns */
        .child-navigation > li > a.active {
            color: #4caf50 !important; /* Light green text */
            font-weight: bold !important;
            text-shadow: 0 0 3px rgba(76, 175, 80, 0.3); /* Subtle glow effect */
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
                        <li<?php echo (in_array(basename($_SERVER['PHP_SELF']), ['how_to_apply.php', 'entry_requirements.php', 'programmes_offered.php', 'fee_structure.php', 'how_to_pay.php', 'student_transfers.php', 'postponent_transfer.php', 'credit_transfer.php', 'international_students.php', 'mature_age.php', 'special_admissions.php', 'faq.php'])) ? ' class="active"' : ''; ?>>
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
                                
                            </ul>
                        </li>

                        <!-- Academic nav bar -->
                        <li<?php 
                            $academic_pages = ['faculty.php', 'publications.php', 'exams_regulations.pdf'];
                            $current_page = basename($_SERVER['PHP_SELF']);
                            echo (in_array($current_page, $academic_pages) || strpos($_SERVER['REQUEST_URI'], 'faculty.php') !== false) ? ' class="active"' : ''; 
                        ?>>
                         <a href="#" class="has-child no-link" style="color: #fff; transition: color 0.3s ease; padding: 15px 20px; display: block;">Academic</a>
                            <ul class="list-unstyled child-navigation" style="background-color: #004225; border: 1px solid #003319; min-width: 200px;">
                                <li class="has-child">
                                    <a href="#" class="no-link" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease; position: relative;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">
                                        Faculties & Institutes <i class="fa fa-chevron-right" style="float: right; margin-top: 5px; transition: transform 0.3s ease;"></i>
                                    </a>
                                    <ul class="list-unstyled child-navigation" style="position: absolute; left: 100%; top: 0; min-width: 220px; background-color: #006633; border: 1px solid #005229; display: none;">
                                        <li><a href="http://localhost/c/zanvarsity/html/faculty.php?id=1" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Faculty Of Business Administration (FBA)</a></li>
                                        <li><a href="how_to_apply.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Faculty Of Law And Shariah (FLS)</a></li>
                                        <li><a href="how_to_apply.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Faculty Of Arts And Social Sciences (FASS)</a></li>
                                        <li><a href="how_to_apply.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Faculty Of Engineering (FOE)</a></li>
                                        <li><a href="how_to_apply.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Faculty Of Health And Allied Sciences (FOHAS)</a></li>
                                        <li><a href="how_to_apply.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Faculty Of Science (FOS)</a></li>
                                        <li><a href="how_to_apply.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Institute Of Postgraduate Studies & Research (IPGRS)</a></li>
                                        <li><a href="how_to_apply.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Institute Of Islamic Banking And Finance (IIBF)</a></li>
                                        <li><a href="how_to_apply.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Institute Of Continuing Education (ICE)</a></li>
                                        
                                    </ul>
                                </li>
                                <li class="has-child">
                                    <a href="#" class="no-link" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease; position: relative;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">
                                        Research & Publications <i class="fa fa-chevron-right" style="float: right; margin-top: 5px; transition: transform 0.3s ease;"></i>
                                    </a>
                                    <ul class="list-unstyled child-navigation" style="position: absolute; left: 100%; top: 0; min-width: 220px; background-color: #006633; border: 1px solid #005229; display: none;">
                                        <li><a href="http://localhost/c/zanvarsity/html/publications.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Research & Publications</a></li>
                                        <li><a href="http://localhost/c/zanvarsity/html/publications.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Repositor</a></li>
                                        <!-- <li><a href="scholarships.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Scholarships</a></li>
                                        <li><a href="financial_aid.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Financial Aid</a></li> -->
                                    </ul>
                                </li>
                               <li><a href="http://localhost/c/zanvarsity/html/uploads/doc/exams_regulations.pdf" target="_blank">Examination Regulations</a>
                                    
                                </li>
                                
                                
                            </ul>
                        </li>

                        <!-- <li>
                            <a href="#" class="has-child no-link">Events</a>
                            <ul class="list-unstyled child-navigation" style="background-color: #004225; border: 1px solid #003319; min-width: 200px;">
                                <li><a href="event-listing-images.html">Events Listing with images</a></li>
                                <li><a href="event-listing.html">Events Listing</a></li>
                                <li><a href="event-grid.html">Events Grid</a></li>
                                <li><a href="event-detail.html">Event Detail</a></li>
                                <li><a href="event-calendar.html">Events Calendar</a></li>
                            </ul>
                        </li>
                        -->
                        <li<?php 
                            $directorates_pages = ['Library_Services.php', 'ICT_Services.php', 'Student_Services.php', 'Quality_Assurance.php', 'Distance_Learning.php'];
                            $current_page = basename($_SERVER['PHP_SELF']);
                            echo (in_array($current_page, $directorates_pages)) ? ' class="active"' : ''; 
                        ?>>
                            <a href="#" class="has-child no-link">Directorates</a>
                            <ul class="list-unstyled child-navigation" style="background-color: #004225; border: 1px solid #003319; min-width: 200px;">
                                <li><a href="http://localhost/c/zanvarsity/html/Library_Services.php">Library Services</a></li>
                                <li><a href="http://localhost/c/zanvarsity/html/ICT_Services.php">ICT Services</a></li>
                                <li><a href="http://localhost/c/zanvarsity/html/Student_Services.php">Student Services</a></li>
                                <li><a href="http://localhost/c/zanvarsity/html/Quality_Assurance.php">Quality Assurance</a></li>
                                <li><a href="http://localhost/c/zanvarsity/html/Distance_Learning.php">Distance Learning</a></li>
                            </ul>
                        </li>
                        <!-- <li>
                            <a href="#" class="has-child no-link">Blog</a>
                            <ul class="list-unstyled child-navigation" style="background-color: #004225; border: 1px solid #003319; min-width: 200px;">
                                <li><a href="blog-listing.html">Blog Listing</a></li>
                                <li><a href="blog-detail.html">Blog Detail</a></li>
                            </ul>
                        </li> -->
                        <li<?php echo (basename($_SERVER['PHP_SELF']) == 'register-sign-in.php' || basename($_SERVER['PHP_SELF']) == 'my-account.php') ? ' class="active"' : ''; ?>>
                            <a href="register-sign-in.php" style="color: #fff; transition: color 0.3s ease; padding: 15px 20px; display: block;">
                                <?php echo is_logged_in() ? 'My Account' : 'Login / Register'; ?>
                            </a>
                        </li>
                        <li<?php 
                            $facilities_pages = ['library.php', 'labs.php', 'sports.php', 'hostels.php', 'cafeteria.php'];
                            $current_page = basename($_SERVER['PHP_SELF']);
                            echo (in_array($current_page, $facilities_pages)) ? ' class="active"' : ''; 
                        ?>>
                            <a href="#" class="has-child no-link" style="color: #fff; transition: color 0.3s ease; padding: 15px 20px; display: block;">Facilities</a>
                            <ul class="list-unstyled child-navigation" style="background-color: #004225; border: 1px solid #003319; min-width: 200px;">
                                <li><a href="library.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">University Library</a></li>
                                <li><a href="labs.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Computer Labs</a></li>
                                <li><a href="sports.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Sports Facilities</a></li>
                                <li><a href="hostels.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Student Hostels</a></li>
                                <li><a href="cafeteria.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Cafeteria</a></li>
                            </ul>
                        </li>
                                <li class="<?php echo isset($contact_active) ? 'active' : ''; ?>">
                                    <a href="contact_us.php">Contact Us</a>
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
  