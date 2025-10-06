<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="ZANVarsity">
    
    <!-- Google Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #4caf50;
            --secondary-color: #2e7d32;
            --accent-color: #8bc34a;
            --text-color: #333;
            --light-gray: #f8f9fa;
            --border-color: #dee2e6;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            color: var(--text-color);
            background-color: #f5f5f5;
        }
        
        .navigation-wrapper {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .secondary-navigation-wrapper {
            background: var(--primary-color);
            color: white;
            padding: 8px 0;
        }
        
        .navigation-contact {
            color: white;
            padding: 5px 0;
        }
        
        .secondary-navigation {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        
        .secondary-navigation li {
            display: inline-block;
            margin-left: 15px;
        }
        
        .secondary-navigation a {
            color: white;
            text-decoration: none;
        }
        
        .navbar {
            margin-bottom: 0;
            border: none;
            min-height: 70px;
        }
        
        .navbar-brand {
            padding: 10px 15px;
            height: auto;
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color) !important;
        }
        
        .navbar-toggle {
            margin-top: 18px;
        }
    </style>
    
    <title>ZANVarsity - Admin Dashboard</title>
</head>

<body class="page-sub-page">
<!-- Wrapper -->
<div class="wrapper">
<!-- Header -->
<div class="navigation-wrapper">
    <div class="secondary-navigation-wrapper">
        <div class="container">
            <div class="navigation-contact pull-left">ZANVarsity Admin Panel</div>
            <ul class="secondary-navigation list-unstyled pull-right">
                <li><a href="#"><i class="fa fa-user"></i> <?= htmlspecialchars($_SESSION['first_name'] ?? 'Admin') ?></a></li>
                <li><a href="../logout.php"><i class="fa fa-sign-out"></i> Log Out</a></li>
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
                        <a href="index.php">ZANVarsity</a>
                    </div>
                </div>
                <nav class="collapse navbar-collapse bs-navbar-collapse navbar-right" role="navigation">
                    <ul class="nav navbar-nav">
                        <li class="active">
                            <a href="index.php">Dashboard</a>
                        </li>
                        <li>
                            <a href="users.php">Users</a>
                        </li>
                        <li>
                            <a href="manage_courses.php">Courses</a>
                        </li>
                        <li>
                            <a href="manage_events.php">Events</a>
                        </li>
                        <li>
                            <a href="manage_news.php">News</a>
                        </li>
                        <li>
                            <a href="settings.php">Settings</a>
                        </li>
                    </ul>
                </nav><!-- /.navbar collapse-->
            </div><!-- /.container -->
        </header><!-- /.navbar -->
    </div><!-- /.primary-navigation -->
</div>
<!-- end Header -->

<!-- Main Content -->
<div class="container">
