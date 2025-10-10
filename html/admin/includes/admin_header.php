<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    header('Location: /zanvarsity/html/login.php');
    exit();
}

// Get user data from session
$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['email'] ?? 'Guest';
$user_name = $_SESSION['first_name'] ?? 'User';
$user_role = $_SESSION['role'] ?? 'student';
$is_admin = in_array($user_role, ['admin', 'super_admin']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Zanvarsity</title>
    
    <!-- CSS -->
    <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <link href="/zanvarsity/html/assets/css/font-awesome.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/bootstrap/css/bootstrap.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/selectize.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/owl.carousel.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/vanillabox/vanillabox.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/style.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/green-theme.css" type="text/css">
    <style>
        /* Dark Green Admin Header Styles */
        .admin-header {
            background-color: #006400; /* Dark green */
            border-bottom: 1px solid #004d00;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .admin-header .secondary-navigation-wrapper {
            background-color: #004d00; /* Slightly darker green for the top bar */
            padding: 5px 0;
        }
        .admin-header .secondary-navigation a {
            color: #e0e0e0 !important;
            transition: color 0.3s ease;
        }
        .admin-header .secondary-navigation a:hover {
            color: #ffffff !important;
            text-decoration: none;
        }
        .admin-header .navigation-contact {
            color: #b3e6b3 !important;
        }
        .admin-header .primary-navigation-wrapper {
            background-color: #006400; /* Dark green */
            padding: 10px 0;
        }
        .admin-header .navbar-nav > li > a {
            color: #e0e0e0 !important;
            font-weight: 500;
            padding: 15px 20px;
            transition: all 0.3s ease;
        }
        .admin-header .navbar-nav > li > a:hover,
        .admin-header .navbar-nav > li.active > a {
            background-color: #005900 !important;
            color: #ffffff !important;
        }
        .admin-header .navbar-toggle {
            border-color: #ffffff;
        }
        .admin-header .icon-bar {
            background-color: #ffffff;
        }
        .admin-header .breadcrumb {
            background-color: transparent;
            padding: 15px 0;
            margin: 0;
        }
        .admin-header .breadcrumb > li + li:before {
            color: #b3e6b3;
        }
        .admin-header .breadcrumb > li > a {
            color: #b3e6b3;
        }
        .admin-header .breadcrumb > li.active {
            color: #ffffff;
        }
    </style>
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="page-sub-page page-my-account">
<!-- Wrapper -->
<div class="wrapper">
<!-- Header -->
<div class="navigation-wrapper admin-header">
    <div class="secondary-navigation-wrapper">
        <div class="container">
            <div class="navigation-contact pull-left">
                <i class="fa fa-phone"></i> Call Us: <span class="opacity-70">+255 123 456 789</span>
            </div>
            <ul class="secondary-navigation list-unstyled pull-right">
                <li><a href="#tab-profile" data-toggle="tab"><i class="fa fa-user"></i>My Profile</a></li>
                <li><a href="#tab-my-courses" data-toggle="tab">My Courses</a></li>
                <li><a href="#tab-change-password" data-toggle="tab">Change Password</a></li>
                <li><a href="/zanvarsity/html/logout.php" onclick="return confirm('Are you sure you want to log out?');"><i class="fa fa-sign-out"></i> Log Out</a></li>
            </ul>
        </div>
    </div><!-- /.secondary-navigation -->
    <div class="primary-navigation-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 style="color:white; margin:0; padding:10px 0; font-weight:600;">
                        <i class="fa fa-tachometer"></i> Admin Dashboard
                    </h2>
                </div>
            </div>
        </div>
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
                        <a href="/zanvarsity/html/index.html">
                            <img src="/zanvarsity/html/assets/img/logo.png" alt="Zanvarsity" class="logo">
                        </a>
                    </div>
                </div>
                <nav class="collapse navbar-collapse bs-navbar-collapse navbar-right" role="navigation">
                    <ul class="nav navbar-nav">
                        <li><a href="/zanvarsity/html/index.html">Home</a></li>
                        
                        <!-- Admission Dropdown -->
                        <li class="dropdown<?php echo in_array(basename($_SERVER['PHP_SELF']), ['admission.php', 'how-to-apply.php', 'entry-requirements.php', 'programmes.php', 'online-application.php', 'how-to-pay.php', 'fee-structure.php', 'student-transfers.php', 'postponement.php']) ? ' active' : ''; ?>">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admission <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li class="dropdown-header">Applications</li>
                                <li><a href="/zanvarsity/html/admission/how-to-apply.php"><i class="fa fa-arrow-right"></i> How to Apply</a></li>
                                <li><a href="/zanvarsity/html/admission/entry-requirements.php"><i class="fa fa-arrow-right"></i> Entry Requirements</a></li>
                                <li><a href="/zanvarsity/html/admission/programmes.php"><i class="fa fa-arrow-right"></i> Programmes Offered</a></li>
                                <li><a href="https://www.zumis.ac.tz/admission/data/register" target="_blank"><i class="fa fa-arrow-right"></i> Online Application</a></li>
                                <li role="separator" class="divider"></li>
                                <li class="dropdown-header">Fees</li>
                                <li><a href="/zanvarsity/html/admission/how-to-pay.php"><i class="fa fa-arrow-right"></i> How to Pay</a></li>
                                <li><a href="/zanvarsity/html/admission/fee-structure.php"><i class="fa fa-arrow-right"></i> Fee Structure</a></li>
                                <li role="separator" class="divider"></li>
                                <li class="dropdown-header">Transfers</li>
                                <li><a href="/zanvarsity/html/admission/student-transfers.php"><i class="fa fa-arrow-right"></i> Student Transfers</a></li>
                                <li><a href="/zanvarsity/html/admission/postponement.php"><i class="fa fa-arrow-right"></i> Postponement & Resumption of Studies</a></li>
                            </ul>
                        </li>

                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'courses.php' ? 'active' : ''; ?>">
                            <a href="courses.php">Courses</a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'events.php' ? 'active' : ''; ?>">
                            <a href="events.php">Events</a>
                        </li>
                        <?php if ($is_admin): ?>
                        <li class="active">
                            <a href="dashboard.php">Admin</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav><!-- /.navbar collapse-->
            </div><!-- /.container -->
        </header><!-- /.navbar -->
    </div><!-- /.primary-navigation -->
    <div class="background">
        <img src="/zanvarsity/html/assets/img/background-city.png" alt="background">
    </div>
</div>
<!-- end Header -->

<!-- Breadcrumb -->
<div class="container">
    <ol class="breadcrumb">
        <li><a href="/zanvarsity/html/index.html">Home</a></li>
        <li><a href="dashboard.php">Admin</a></li>
        <li class="active"><?php echo ucfirst(str_replace('.php', '', basename($_SERVER['PHP_SELF']))); ?></li>
    </ol>
</div>
<!-- end Breadcrumb -->

<!-- Page Content -->
<div id="page-content">
    <div class="container">
        <div class="row">
