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

    <link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <link href="/zanvarsity/html/assets/css/font-awesome.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/bootstrap/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/selectize.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/owl.carousel.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/vanillabox/vanillabox.css" type="text/css">
    <link rel="stylesheet" href="/zanvarsity/html/assets/css/style.css" type="text/css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <!-- Admin theme removed to use default blue header -->

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
                        <a href="/zanvarsity/html/admin/"><img src="/zanvarsity/html/assets/img/logo.png" alt="Zanvarsity"></a>
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
        <img src="/zanvarsity/html/assets/img/background-city.png" alt="background">
    </div>
</div>
