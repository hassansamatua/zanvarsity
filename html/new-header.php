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
    <!-- Core CSS -->
    <link href="<?php echo ASSETS_URL; ?>/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="<?php echo ASSETS_URL; ?>/css/font-awesome.min.css" rel="stylesheet">
    
    <!-- Theme CSS -->
    <link href="<?php echo ASSETS_URL; ?>/css/style.css" rel="stylesheet">
    
    <!-- Additional CSS -->
    <link href="<?php echo ASSETS_URL; ?>/css/selectize.css" rel="stylesheet">
    <link href="<?php echo ASSETS_URL; ?>/css/owl.carousel.css" rel="stylesheet">
    <link href="<?php echo ASSETS_URL; ?>/css/vanillabox/vanillabox.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo ASSETS_URL; ?>/css/custom.css" rel="stylesheet">
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <!-- Custom CSS -->
    <style>
        /* Temporary debug styles */
        body { background-color: #f8f9fa; }
        .login-container { 
            max-width: 400px; 
            margin: 50px auto; 
            padding: 20px; 
            background: white; 
            border-radius: 5px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
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
