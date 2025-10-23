<!DOCTYPE html>

<html lang="en-US">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="author" content="Theme Starz" />
  <style>
    /* Event card styling - specific to featured events section */
    #featured-courses .event {
      height: 280px; /* Reduced height for the entire card */
      display: flex;
      flex-direction: column;
      margin-bottom: 30px;
      border: 1px solid #e0e0e0;
      border-radius: 4px;
      overflow: hidden;
      transition: box-shadow 0.3s ease;
      text-align: center; /* Center align all text */
    }
    
    .event:hover {
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    #featured-courses .event-thumbnail {
      height: 160px; /* Reduced height for the image area */
      overflow: hidden;
      position: relative;
      flex-shrink: 0; /* Prevent the thumbnail from shrinking */
    }
    
    #featured-courses .event-image {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    #featured-courses .event-image .image-wrapper {
      width: 100%;
      height: 100%;
      overflow: hidden;
    }
    
    #featured-courses .event-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center;
      transition: transform 0.3s ease;
    }
    
    #featured-courses .event-body {
      padding: 15px 20px;
      display: flex;
      flex-direction: column;
      flex-grow: 1;
      align-items: center; /* Center content horizontally */
      text-align: center; /* Center text */
    }
    
    #featured-courses .event-title {
      font-size: 16px;
      font-weight: 600;
      margin: 0 0 12px 0;
      line-height: 1.4;
    }
    
    #featured-courses .event-meta {
      margin-top: auto;
      padding: 15px 0 0 0;
      border-top: 1px solid #f0f0f0;
      font-size: 13px;
      color: #666;
      width: 100%;
      text-align: center;
    }
    
    /* Style for the View Details button */
    #featured-courses .event-actions {
      margin-top: 15px;
      width: 100%;
    }
    
    #featured-courses .btn {
      background-color: #006400; /* Dark green */
      color: white;
      border: none;
      padding: 8px 20px;
      border-radius: 4px;
      text-decoration: none;
      display: inline-block;
      transition: background-color 0.3s ease;
    }
    
    #featured-courses .btn:hover {
      background-color: #004d00; /* Slightly darker green on hover */
      color: white;
    }
    
    #featured-courses .event:hover .event-image img {
      transform: scale(1.05);
    }
    
    /* Custom styles for three-column layout */
    .three-columns {
      display: flex;
      flex-wrap: wrap;
      margin: 0 -15px;
    }

    .three-columns>div {
      flex: 1 0 33.333333%;
      padding: 0 15px;
      min-width: 300px;
    }

    .section-card {
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .section-content {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    /* Ensure all sections have the same height */
    #upcoming-events,
    #announcements,
    #about {
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    /* Responsive adjustments */
    @media (max-width: 991.98px) {
      .three-columns>div {
        flex: 0 0 100%;
        max-width: 100%;
      }
    }
  </style>
  <link href="assets/css/font-awesome.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.css" type="text/css" />
  <link rel="stylesheet" href="assets/css/selectize.css" type="text/css" />
  <link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css" />
  <link rel="stylesheet" href="assets/css/vanillabox/vanillabox.css" type="text/css" />
  <link rel="stylesheet" href="assets/css/vanillabox/vanillabox.css" type="text/css" />

  <link rel="stylesheet" href="assets/css/style.css" type="text/css" />
  <style>
    /* Navigation Bar */
    .navigation-wrapper,
    .primary-navigation-wrapper,
    .secondary-navigation-wrapper,
    .navbar-default,
    .navbar {
      background-color: #004225 !important; /* Dark green color */
    }
    
    /* Ensure dropdown menus also have the same background */
    .navbar-nav > li > .dropdown-menu,
    .child-navigation,
    .navbar-collapse {
      background-color: #004225 !important;
      border-color: #003319 !important;
    }
    
    .navbar-nav > li > a,
    .navbar-nav > li > .no-link,
    .navbar-nav .open .dropdown-menu > li > a {
      color: #ffffff !important;
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
    /* Mobile menu button */
    .navbar-toggle {
      border-color: #ffffff;
    }
    
{{ ... }}
    .navbar-toggle .icon-bar {
      background-color: #ffffff;
    }
    
    /* Secondary Navigation Bar */
    .secondary-navigation-wrapper {
      background-color: #004225; /* Same as footer bottom */
      color: white;
    }
    
    .secondary-navigation a, 
    .navigation-contact,
    .search .form-control,
    .search .btn,
    .secondary-navigation li a {
      color: white !important;
    }
    
    .search .form-control {
      background-color: #e8f5e9; /* Light green background */
      border-color: #a5d6a7; /* Slightly darker green border */
      color: #2e7d32 !important; /* Dark green text */
      border-radius: 4px 0 0 4px; /* Rounded left corners */
    }
    
    .search .form-control:focus {
      border-color: #81c784; /* Brighter green border on focus */
      box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25); /* Green glow */
    }
    
    .search .form-control::placeholder {
      color: #689f38; /* Medium green placeholder */
      opacity: 0.8;
    }
    
    .search .btn {
      background-color: #66bb6a; /* Light green button */
      border-color: #4caf50; /* Slightly darker green border */
      border-radius: 0 4px 4px 0; /* Rounded right corners */
      color: white !important;
      transition: all 0.3s ease;
    }
    
    .search .btn:hover {
      background-color: #4caf50; /* Darker green on hover */
      border-color: #43a047;
    }
    
    /* Slideshow */
    #homepage-carousel {
      background-color: #007848;
    }
    
    /* Footer Sections */
    #footer-content {
      background-color: #007848;
      color: white;
    }
    
    #footer-content a {
      color: #e0e0e0;
    }
    
    #footer-content a:hover {
      color: white;
      text-decoration: underline;
    }
    
    #footer-bottom {
      background-color: #004225 !important; /* Dark green background */
      color: white !important;
      padding: 8px 0; /* Reduced from 15px to 8px */
    }
    
    #footer-bottom .copyright {
      color: white !important;
      text-align: center;
      width: 100%;
      padding: 8px 0; /* Reduced from 15px to 8px */
      margin: 0;
    }
    
    #footer-bottom .footer-inner {
      display: flex;
      justify-content: center;
      align-items: center;
      width: 100%;
    }
    
    #footer-bottom .container {
      background-color: transparent !important;
    }
    
    /* Slider content styling */
    .slider-content {
      background-color: #004225; /* Same as footer bottom */
      color: white;
      padding: 30px;
      border-radius: 4px;
      height: 100%;
    }
    
    .slider-content h1 {
      color: white;
      margin-top: 0;
      margin-bottom: 20px;
    }
    
    /* Announcement styles */
    #announcements .event {
      width: 100%;
      max-width: 100%;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
      padding: 15px;
      margin: 5px 0;
      border-radius: 4px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      background-color: #f8f9fa;
    }
    
    #announcements .event:hover {
      background-color: #004225; /* Dark green on hover */
      color: white;
    }
    
    #announcements .event:hover a {
      color: white !important;
    }
    
    #announcements .event:hover .additional-info {
      color: rgba(255,255,255,0.8) !important;
    }
    
    #announcements .event a {
      display: inline-block;
      max-width: 100%;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    
    #announcements .event aside {
      width: 100%;
      max-width: 100%;
      box-sizing: border-box;
    }
    
    #announcements .event header {
      width: 100%;
      max-width: 100%;
      overflow: hidden;
    }
    
    /* Carousel Styling */
    .homepage-carousel-wrapper .col-md-6.col-sm-7 {
      height: 400px;
      position: relative;
    }
    
    .image-carousel {
      position: relative;
      height: 100%;
      width: 100%;
      overflow: hidden;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    /* Owl Carousel Styling */
    .owl-carousel {
      position: relative;
      width: 100%;
      height: 100%;
    }
    
    .owl-stage-outer, 
    .owl-stage, 
    .owl-item {
      height: 100%;
    }
    
    .owl-item .item {
      height: 400px;
      position: relative;
      overflow: hidden;
    }
    
    .owl-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center;
    }
    
    /* Navigation Arrows */
    .owl-nav {
      position: absolute;
      top: 50%;
      width: 100%;
      transform: translateY(-50%);
      pointer-events: none;
    }
    
    .owl-prev,
    .owl-next {
      position: absolute;
      width: 40px;
      height: 40px;
      background: rgba(0,0,0,0.5) !important;
      color: white !important;
      border-radius: 50% !important;
      text-align: center;
      line-height: 40px !important;
      font-size: 20px !important;
      pointer-events: auto;
      margin: 0 !important;
    }
    
    .owl-prev {
      left: 15px;
    }
    
    .owl-next {
      right: 15px;
    }
    
    /* Dots Navigation */
    .owl-dots {
      position: absolute;
      bottom: 15px;
      width: 100%;
      text-align: center;
    }
    
    .owl-dot {
      display: inline-block;
      width: 12px;
      height: 12px;
      margin: 0 5px;
      background: rgba(255,255,255,0.5) !important;
      border-radius: 50%;
    }
    
    .owl-dot.active {
      background: #fff !important;
    }
    
    .image-carousel-slide {
      position: relative;
    }
    
    .carousel-image-container {
      width: 100%;
      height: 300px;
      overflow: hidden;
      position: relative;
      display: flex;
      align-items: flex-end;
    }
    
    .carousel-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center;
      display: block;
    }
    
    @media (max-width: 767px) {
      .carousel-image-container {
        height: 300px;
      }
    }
    
    .carousel-caption {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
      color: white;
      padding: 20px;
      text-align: center;
      transition: all 0.3s ease;
    }
    
    .carousel-caption h3 {
      margin: 0 0 8px 0;
      font-size: 22px;
      font-weight: 600;
      color: #fff;
      text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }
    
    .carousel-caption p {
      margin: 0;
      font-size: 15px;
      opacity: 0.9;
      text-shadow: 1px 1px 1px rgba(0,0,0,0.5);
    }
    
    .carousel-caption p {
      margin: 0;
      font-size: 16px;
      line-height: 1.5;
      color: #f0f0f0;
      text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }
    
    /* Carousel Navigation */
    .owl-nav {
      position: absolute;
      top: 50%;
      width: 100%;
      display: flex;
      justify-content: space-between;
      transform: translateY(-50%);
      pointer-events: none;
    }
    
    .owl-prev, .owl-next {
      width: 40px;
      height: 60px;
      background: rgba(0, 66, 37, 0.7) !important;
      color: white !important;
      display: flex !important;
      align-items: center;
      justify-content: center;
      font-size: 24px !important;
      border-radius: 0;
      pointer-events: auto;
      transition: all 0.3s ease;
    }
    
    .owl-prev {
      border-top-right-radius: 4px;
      border-bottom-right-radius: 4px;
    }
    
    .owl-next {
      border-top-left-radius: 4px;
      border-bottom-left-radius: 4px;
    }
    
    .owl-prev:hover, .owl-next:hover {
      background: rgba(0, 86, 57, 0.9) !important;
    }
    
    /* Full width footer */
    #page-footer {
      width: 100%;
      max-width: 100%;
      margin: 0;
      padding: 0;
    }
    #page-footer .container {
      width: 100%;
      max-width: 100%;
      padding: 0;
      margin: 0;
    }
    #page-footer .footer-inner {
      max-width: 100%;
      margin: 0;
    }
    #page-footer .row {
      margin-left: 0;
      margin-right: 0;
    }
    #page-footer [class*="col-"] {
      padding-left: 0;
      padding-right: 0;
    }
    
    #footer-top,
    #footer-content,
    #footer-bottom {
      max-width: 100%;
      margin: 0;
      padding: 0;
    }
    
    /* Footer layout */
    .footer-inner {
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
    }
    
    /* Center Follow Us section */
    .footer-social {
      text-align: center;
      margin: 0 auto;
      position: relative;
      left: -120px; /* Adjust this value to fine-tune centering */
    }
    
    .footer-social figure {
      display: inline-block;
      margin: 0 15px 10px 0;
      font-weight: bold;
      vertical-align: middle;
    }
    
    .footer-social .icons {
      display: inline-block;
      vertical-align: middle;
    }
    
    .footer-social .icons a {
      margin: 0 8px;
      color: #fff;
      font-size: 18px;
      transition: all 0.3s ease;
    }
    
    .footer-social .icons a:hover {
      opacity: 0.8;
      transform: translateY(-2px);
    }
    
    /* Search section */
    .footer-inner > .search {
      float: none;
      margin-left: 20px;
    }
    
    .footer-inner > .search .form-control {
      width: 200px; /* Adjust width as needed */
      border-radius: 4px;
      border: 1px solid #ddd;
      padding: 6px 12px;
    }
    
    .footer-inner > .search .btn {
      background: #005a36; /* Dark green background to match footer */
      color: white; /* White icon color */
      border: 1px solid #004d2e; /* Slightly darker green border */
      border-left: none;
      border-radius: 0 4px 4px 0;
      transition: background 0.3s ease;
    }
    
    .footer-inner > .search .btn:hover {
      background: #004d00; /* Darker green on hover */
    }
    
    /* Infinite scroll for event cards */
    .events.images.featured {
      overflow: hidden;
      position: relative;
      width: 100%;
    }
    
    .events.images.featured .event {
      transition: transform 0.3s ease;
    }
    
    .scrolling-wrapper {
      display: flex;
      animation: scrollEvents 30s linear infinite;
      width: max-content;
    }
    
    @keyframes scrollEvents {
      0% { transform: translateX(0); }
      100% { transform: translateX(-50%); }
    }
    
    .scrolling-wrapper:hover {
      animation-play-state: paused;
    }
  </style>
  <link rel="stylesheet" href="assets/css/publications.css">
  <link rel="stylesheet" href="assets/css/events-carousel.css">
  <!-- Owl Carousel CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
  <!-- Custom Carousel CSS -->
  <link rel="stylesheet" href="assets/css/carousel.css">
  <title>Zanzibar University</title>
</head>

<body class="page-homepage-carousel">
  <!-- Wrapper -->
  <div class="wrapper">
    <!-- Header -->
    <div class="navigation-wrapper">
      <div class="secondary-navigation-wrapper">
        <div class="container">
          <div class="navigation-contact pull-left">
            Call Us: <span class="opacity-70">+255 772 601 303</span>
          </div>
          <div class="search">
            <div class="input-group">
              <input type="search" class="form-control" name="search" placeholder="Search" />
              <span class="input-group-btn"><button type="submit" id="search-submit" class="btn">
                  <i class="fa fa-search"></i></button></span>
            </div>
            <!-- /.input-group -->
          </div>
          <ul class="secondary-navigation list-unstyled">
            <li><a href="https://zumis.ac.tz/">Zumis Portal</a></li>
            <li><a href="./uploads/doc/prospectus.pdf">Prospectus</a></li>
            <li><a href="./uploads/doc/ALMANAC-2023.pdf">Almanac</a></li>
            <li><a href="#">Fee Structure</a></li>

            <li><a href="#">Alumni</a></li>
            <li><a href="sign-in.php"><i class="fa fa-sign-in"></i> Admin Login</a></li>
          </ul>
        </div>
      </div>
      <!-- /.secondary-navigation -->
      <div class="primary-navigation-wrapper" style="background-color: #004225 !important;">
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
                <a href="index.php"><img src="assets/img/logo11.png" alt="brand" /></a>
              </div>
            </div>
            <nav class="collapse navbar-collapse bs-navbar-collapse navbar-right" role="navigation">
              <ul class="nav navbar-nav">
                <li class="active">
                  <a href="index.php">Home</a>
                </li>
                <li>
                  <a href="#" class="has-child no-link" style="color: #fff; transition: color 0.3s ease; padding: 15px 20px; display: block;">About</a>
                  <ul class="list-unstyled child-navigation" style="background-color: #004225; border: 1px solid #003319; min-width: 200px;">
                    <li><a href="Background_info.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Background Information</a></li>
                    <li><a href="vision_mission.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Vision & Mission</a></li>
                    <li><a href="board_of_trustees.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Board of Trustees</a></li>
                    <li><a href="darul_iman.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Darul Iman (DICA)</a></li>
                    <li><a href="council_board.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Council Board</a></li>
                    <li><a href="principal_officers.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Principal Officers</a></li>
                    <li><a href="senate_board.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Senate Board</a></li>
                    <!-- <li><a href="course-listing-images.html">Course Listing with Images</a></li>
                    <li>
                      <a href="course-detail-v1.html">Course Detail v1</a>
                    </li>
                      <a href="course-detail-v2.html">Course Detail v2</a>
                    </li> 
                    <li>
                      <a href="course-detail-v3.html">Course Detail v3</a>
                    </li> -->
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
                                
                          </ul>
                        </li>
                
                <!-- <li>
										<a href="about-us.html">About Us</a>
									</li> -->
                <li<?php 
                    $academic_pages = ['faculty.php', 'publications.php', 'exams_regulations.pdf'];
                    $current_page = basename($_SERVER['PHP_SELF']);
                    echo (in_array($current_page, $academic_pages) || strpos($_SERVER['REQUEST_URI'], 'faculty.php') !== false) ? ' class="active"' : ''; 
                ?>>
                  <a href="#" class="has-child no-link" style="color: #fff; transition: color 0.3s ease; padding: 15px 20px; display: block;">Academics</a>
                  <ul class="list-unstyled child-navigation" style="background-color: #004225; border: 1px solid #003319; min-width: 200px;">
                    <li class="has-child">
                      <a href="#" class="no-link" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease; position: relative;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">
                        Faculties & Institutes <i class="fa fa-chevron-right" style="float: right; margin-top: 5px; transition: transform 0.3s ease;"></i>
                      </a>
                      <ul class="list-unstyled child-navigation" style="position: absolute; left: 100%; top: 0; min-width: 220px; background-color: #006633; border: 1px solid #005229; display: none;">
                        <li><a href="http://localhost/c/zanvarsity/html/faculty.php?id=1" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Faculty Of Business Administration (FBA)</a></li>
                        <li><a href="faculty.php?id=2" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Faculty Of Law And Shariah (FLS)</a></li>
                        <li><a href="faculty.php?id=3" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Faculty Of Arts And Social Sciences (FASS)</a></li>
                        <li><a href="faculty.php?id=4" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Faculty Of Engineering (FOE)</a></li>
                        <li><a href="faculty.php?id=5" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Faculty Of Health And Allied Sciences (FOHAS)</a></li>
                        <li><a href="faculty.php?id=6" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Faculty Of Science (FOS)</a></li>
                        <li><a href="faculty.php?id=7" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Institute Of Postgraduate Studies & Research (IPGRS)</a></li>
                        <li><a href="faculty.php?id=8" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Institute Of Islamic Banking And Finance (IIBF)</a></li>
                        <li><a href="faculty.php?id=9" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Institute Of Continuing Education (ICE)</a></li>
                      </ul>
                    </li>
                    <li class="has-child">
                      <a href="#" class="no-link" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease; position: relative;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">
                        Research & Publications <i class="fa fa-chevron-right" style="float: right; margin-top: 5px; transition: transform 0.3s ease;"></i>
                      </a>
                      <ul class="list-unstyled child-navigation" style="position: absolute; left: 100%; top: 0; min-width: 220px; background-color: #006633; border: 1px solid #005229; display: none;">
                        <li><a href="publications.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Research & Publications</a></li>
                        <li><a href="repositories.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#007a3d'" onmouseout="this.style.backgroundColor='#006633'">Repositories</a></li>
                      </ul>
                    </li>
                    <li><a href="http://localhost/c/zanvarsity/html/uploads/doc/exams_regulations.pdf" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease; "target='_blank' onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Examination Regulations</a></li>
                  </ul>
                </li>
                <li<?php 
                    $directorates_pages = ['Library_Services.php', 'ICT_Services.php', 'Student_Services.php', 'Quality_Assurance.php', 'Distance_Learning.php'];
                    $current_page = basename($_SERVER['PHP_SELF']);
                    echo (in_array($current_page, $directorates_pages)) ? ' class="active"' : ''; 
                ?>>
                  <a href="#" class="has-child no-link" style="color: #fff; transition: color 0.3s ease; padding: 15px 20px; display: block;">Directorates</a>
                  <ul class="list-unstyled child-navigation" style="background-color: #004225; border: 1px solid #003319; min-width: 200px;">
                    <li><a href="Library_Services.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Library Services</a></li>
                    <li><a href="ICT_Services.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">ICT Services</a></li>
                    <li><a href="Student_Services.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Student Services</a></li>
                    <li><a href="Quality_Assurance.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Quality Assurance</a></li>
                    <li><a href="Distance_Learning.php" style="color: #fff; display: block; padding: 8px 20px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#006633'" onmouseout="this.style.backgroundColor='#004225'">Distance Learning</a></li>
                  </ul>
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
                <li<?php echo (basename($_SERVER['PHP_SELF']) == 'contact_us.php') ? ' class="active"' : ''; ?>>
                  <a href="contact_us.php" style="color: #fff; transition: color 0.3s ease; padding: 15px 20px; display: block;">Contact Us</a>
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
      <div class="background" >
        <img src="assets/img/images.jpg" alt="background" />
      </div>
    </div>
    <!-- end Header -->

    <!-- Page Content -->
    <div id="page-content">
      <!-- Slider -->
      <div id="homepage-carousel">
        <div class="container">
          <div class="homepage-carousel-wrapper">
            <div class="row">
              <div class="col-md-6 col-sm-7">
                <div class="image-carousel owl-carousel">
                  <?php
                  // Fetch active carousel items from database
                  try {
                      require_once __DIR__ . '/../includes/database.php';
                      $sql = "SELECT * FROM carousel WHERE is_active = 1 ORDER BY sort_order ASC";
                      $result = $conn->query($sql);
                      
                      if ($result && $result->num_rows > 0) {
                          while ($slide = $result->fetch_assoc()) {
                              echo '<div class="item">';
                              echo '  <div class="carousel-image-container">';
                              // Get and clean the image URL
                              $image_url = trim($slide['image_url']);
                              $image_url = str_replace('\\', '/', $image_url);
                              $image_url = ltrim($image_url, '/');
                              
                              // Build the full path for checking
                              $full_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $image_url;
                              
                              // Output the image with error handling
                              echo '<img src="/' . htmlspecialchars($image_url) . '" 
                                    alt="' . htmlspecialchars($slide['title']) . '" 
                                    class="carousel-image" 
                                    onerror="this.onerror=null; this.style.display=\'none\'; 
                                    var div=document.createElement(\'div\'); 
                                    div.style.background=\'#f0f0f0\'; 
                                    div.style.height=\'100%\'; 
                                    div.style.display=\'flex\'; 
                                    div.style.alignItems=\'center\'; 
                                    div.style.justifyContent=\'center\'; 
                                    div.style.padding=\'20px\'; 
                                    div.style.textAlign=\'center\'; 
                                    div.innerHTML=\'Image not found: ' . htmlspecialchars(addslashes($image_url)) . '\'; 
                                    this.parentNode.appendChild(div);" />';
                              echo '  </div>';
                              echo '  <div class="carousel-caption">';
                              echo '    <h3>' . htmlspecialchars($slide['title']) . '</h3>';
                              if (!empty($slide['description'])) {
                                  echo '<p class="mb-3">' . htmlspecialchars($slide['description']) . '</p>';
                              }
                              if (!empty($slide['button_text']) && !empty($slide['button_url'])) {
                                  echo '<a href="' . htmlspecialchars($slide['button_url']) . '" class="btn btn-primary btn-sm">' . htmlspecialchars($slide['button_text']) . '</a>';
                              }
                              echo '  </div>';
                              echo '</div>';
                          }
                      } else {
                          // Default slides if no data in database
                          echo '<div class="item">
                                  <div class="carousel-image-container">
                                    <img src="assets/img/slide-1.jpg" alt="Welcome to Zanvarsity" class="carousel-image" />
                                  </div>
                                  <div class="carousel-caption">
                                    <h3>Welcome to Zanvarsity</h3>
                                    <p>Join our community of modern thinking students</p>
                                  </div>
                                </div>';
                      }
                  } catch (Exception $e) {
                      error_log("Error loading carousel: " . $e->getMessage());
                      // Fallback to default slide
                      echo '<div class="item">
                              <div class="carousel-image-container">
                                <img src="assets/img/slide-1.jpg" alt="Welcome to Zanvarsity" class="carousel-image" />
                              </div>
                              <div class="carousel-caption">
                                <h3>Welcome to Zanvarsity</h3>
                                <p>Join our community of modern thinking students</p>
                              </div>
                            </div>';
                  }
                  ?>
                </div>
                <!-- /.slider-image -->
              </div>
              <!-- /.col-md-6 -->
              <div class="col-md-6 col-sm-5">
                <div class="slider-content">
                  <div class="row">
                    <div class="col-md-12">
                      <h1>Join the comunity of modern thinking students</h1>
                      <form id="slider-form" role="form" action="" method="post">
                        <!-- <div class="row">
														<div class="col-md-6">
															<div class="input-group">
																<input
																	class="form-control has-dark-background"
																	name="slider-name"
																	id="slider-name"
																	placeholder="Full Name"
																	type="text"
																	required
																/>
															</div>
														</div> -->
                        <!-- /.col-md-6 -->
                        <!-- <div class="col-md-6">
															<div class="input-group">
																<input
																	class="form-control has-dark-background"
																	name="slider-email"
																	id="slider-email"
																	placeholder="Email"
																	type="email"
																	required
																/>
															</div>
														</div> -->
                        <!-- /.col-md-6 -->
                        <!-- </div> -->
                        <!-- /.row -->
                        <div class="row">
                          <div class="col-md-6">
                            <div class="input-group">
                              <select name="slider-study-level" id="slider-study-level" class="has-dark-background">
                                <option value="- Not selected -">
                                  Study Level
                                </option>
                                <option value="Beginner">Beginner</option>
                                <option value="Advanced">Advanced</option>
                                <option value="Intermediate">
                                  Intermediate
                                </option>
                                <option value="Professional">
                                  Professional
                                </option>
                              </select>
                            </div>
                            <!-- /.form-group -->
                          </div>
                          <!-- /.col-md-6 -->
                          <div class="col-md-6">
                            <div class="input-group">
                              <select name="slider-course" id="slider-course" class="has-dark-background">
                                <option value="- Not selected -">
                                  Courses
                                </option>
                                <option value="Art and Design">
                                  Art and Design
                                </option>
                                <option value="Marketing">Marketing</option>
                                <option value="Science">Science</option>
                                <option value="History and Psychology"></option>
                              </select>
                            </div>
                            <!-- /.form-group -->
                          </div>
                          <!-- /.col-md-6 -->
                        </div>
                        <!-- /.row -->
                        <button type="submit" id="slider-submit" class="btn btn-framed pull-right">
                          Search
                        </button>
                        <div id="form-status"></div>
                      </form>
                    </div>
                    <!-- /.col-md-12 -->
                  </div>
                  <!-- /.row -->
                </div>
                <!-- /.slider-content -->
              </div>
              <!-- /.col-md-6 -->
            </div>
            <!-- /.row -->
            <div class="background"></div>
          </div>
          <!-- /.slider-wrapper -->
          <div class="slider-inner"></div>
        </div>
        <!-- /.container -->
      
    </div>
      <!-- end Slider -->
      <!-- News, Events, About -->
      <div class="block">
        <div class="container">
          <div class="row">
            <div class="col-md-4 col-sm-6">
              <section class="news-small" id="news-small">
                <header>
                  <h2>Upcoming Events</h2>
                </header>
                <div class="section-content">
                  <?php
                                        try {
                                            // Include database connection
                                            require_once __DIR__ . '/../includes/database.php';
                                            
                                            // Get today's date in Y-m-d format for comparison
                                            $today = date('Y-m-d');
                                            
                                            // Query to get events from today onwards
                                            $sql = "SELECT id, title, start_date, end_date, location, status 
                                                    FROM events 
                                                    WHERE DATE(start_date) >= ? 
                                                    AND status IN ('upcoming', 'ongoing')
                                                    ORDER BY start_date ASC 
                                                    LIMIT 3";
                                            
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bind_param('s', $today);
                                            $stmt->execute();
                                            $result = $stmt->get_result();
                                            
                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $start_date = new DateTime($row['start_date']);
                                                    $end_date = !empty($row['end_date']) ? new DateTime($row['end_date']) : null;
                                                    $now = new DateTime();
                                                    
                                                    // Check if event is ongoing
                                                    $is_ongoing = ($start_date <= $now && ($end_date === null || $end_date >= $now));
                                                    
                                                    // Format dates and times
                                                    $formatted_date = $start_date->format('d M Y');
                                                    $formatted_time = $start_date->format('h:i A');
                                                    $end_time = $end_date ? $end_date->format('h:i A') : '';
                                                    
                                                    // Determine status class and text
                                                    $status_class = '';
                                                    $status_text = '';
                                                    
                                                    if ($is_ongoing) {
                                                        $status_class = 'ongoing';
                                                        $status_text = ' (Ongoing)';
                                                    } elseif ($row['status'] === 'cancelled') {
                                                        $status_class = 'cancelled';
                                                        $status_text = ' (Cancelled)';
                                                    }
                                                    ?>
                  <article class="event-item <?php echo $status_class; ?>" style="border-left: 4px solid #006400; 
                                                                    padding: 12px 15px 12px 20px; 
                                                                    margin: 10px 0; 
                                                                    border-radius: 0 6px 6px 0;
                                                                    box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
                                                                    background-color: <?php echo $is_ongoing ? '#e8f5e9' : '#f8f9fa'; ?>;
                                                                    max-width: 100%;
                                                                    margin-left: 0;
                                                                    overflow: hidden;">
                    <div class="event-date" style="color: #006400; font-size: 0.9em; margin-bottom: 8px; font-weight: 500; display: flex; align-items: center; flex-wrap: wrap; gap: 10px;">
                      <i class="fa fa-calendar"></i> <?php echo htmlspecialchars($formatted_date); ?>
                      <?php if (!empty($formatted_time)): ?>
                      <span style="display: inline-flex; align-items: center; gap: 5px;">
                        <i class="fa fa-clock-o"></i>
                        <?php 
                                                                    echo htmlspecialchars($formatted_time);
                                                                    if (!empty($end_time) && $end_time !== $formatted_time) {
                                                                        echo ' - ' . htmlspecialchars($end_time);
                                                                    }
                                                                    ?>
                      </span>
                      <?php endif; ?>
                      <?php if ($status_text): ?>
                      <span class="status-badge" style="padding: 3px 10px; border-radius: 12px; font-size: 0.8em; background-color: #006400; color: white; font-weight: 500; white-space: nowrap;">
                        <?php echo $status_text; ?>
                      </span>
                      <?php endif; ?>
                    </div>
                    <h3 class="event-title" style="color: #004d00; font-weight: 600; margin: 8px 0 12px 0;">
                      <a href="event-detail.php?id=<?php echo $row['id']; ?>" style="color: #006400; text-decoration: none; transition: color 0.3s ease;">
                        <?php echo htmlspecialchars($row['title']); ?>
                      </a>
                    </h3>
                    <?php if (!empty($row['location'])): ?>
                    <div class="event-location" style="color: #006400; font-size: 0.9em; margin-bottom: 5px; font-weight: 500;">
                      <i class="fa fa-map-marker"></i> <?php echo htmlspecialchars($row['location']); ?>
                    </div>
                    <?php endif; ?>
                  </article>
                  <?php
                                                }
                                            } else {
                                                // Show a message if no upcoming events
                                                echo '<article class="event-item">
                                                    <div class="alert alert-info" style="margin: 0;">
                                                        <i class="fa fa-info-circle"></i> No upcoming events scheduled. Please check back later.
                                                    </div>
                                                </article>';
                                            }
                                        } catch (Exception $e) {
                                            // Log the error (in a real application, you'd want to log this properly)
                                            error_log('Error fetching events: ' . $e->getMessage());
                                            
                                            // Show user-friendly error message
                                            echo '<article class="event-item">
                                                <div class="alert alert-warning" style="margin: 0;">
                                                    <i class="fa fa-exclamation-triangle"></i> Unable to load events. Please try again later.
                                                </div>
                                            </article>';
                                        }
                                        ?>
                </div>
                <!-- /.section-content -->
                <a href="event.php" class="read-more stick-to-bottom">View All Events</a>
              </section>
              <!-- /.news-small -->
            </div>
            <!-- /.col-md-4 -->
            <div class="col-md-4 col-sm-6">
              <section class="events small" id="announcements">
                <header>
                  <h2>Announcements</h2>
                  <a href="announcements.php" class="link-calendar">View All</a>
                </header>
                <div class="section-content">
                  <?php
                                        // Query to get active announcements (active and within date range)
                                        $sql = "SELECT id, title, content, start_date 
                                                FROM announcements 
                                                WHERE status = 'active' 
                                                AND (start_date <= NOW() AND (end_date IS NULL OR end_date >= NOW()))
                                                ORDER BY start_date DESC 
                                                LIMIT 3";
                                        
                                        $result = $conn->query($sql);
                                        
                                        if ($result && $result->num_rows > 0) {
                                            $count = 0;
                                            while ($row = $result->fetch_assoc()) {
                                                $count++;
                                                $month = date('M', strtotime($row['start_date']));
                                                $day = date('d', strtotime($row['start_date']));
                                                $class = $count === 1 ? 'nearest' : ($count === 2 ? 'nearest-second' : '');
                                                ?>
                  <article class="event <?php echo $class; ?>">
                    <figure class="date">
                      <div class="month"><?php echo strtolower($month); ?></div>
                      <div class="day"><?php echo $day; ?></div>
                    </figure>
                    <aside>
                      <header>
                        <a href="announcement-detail.php?id=<?php echo $row['id']; ?>">
                          <?php echo htmlspecialchars($row['title']); ?>
                        </a>
                      </header>
                      <div class="additional-info">
                        <?php 
                                                            // Truncate content for preview
                                                            $content = strip_tags($row['content']);
                                                            echo strlen($content) > 50 ? substr($content, 0, 50) . '...' : $content;
                                                            ?>
                      </div>
                    </aside>
                  </article>
                  <?php
                                            }
                                        } else {
                                            // Show a message if no announcements
                                            echo '<article class="event">
                                                <aside>
                                                    <div class="additional-info">No current announcements.</div>
                                                </aside>
                                            </article>';
                                        }
                                        ?>
                </div>
              </section>
              <!-- /.events-small -->
            </div>
            <!-- /.col-md-4 -->
            <div class="col-md-4 col-sm-12">
              <?php
								// Include database connection
								require_once __DIR__ . '/../includes/database.php';

								// Get active VC notice
								$vc_notice = [
									'title' => 'Welcome to Our University',
									'message' => 'Welcome to our prestigious institution. We are committed to excellence in education and research.',
									'vc_image' => 'assets/img/students.jpg',
									'pdf_url' => ''
								];

								// Try to fetch from database if table exists
								$result = $conn->query("SHOW TABLES LIKE 'vc_notices'");
								if ($result && $result->num_rows > 0) {
									$notice_result = $conn->query("SELECT * FROM vc_notices WHERE status = 'active' ORDER BY created_at DESC LIMIT 1");
									if ($notice_result && $notice_result->num_rows > 0) {
										$vc_notice = $notice_result->fetch_assoc();
									}
								}
								?>
              <section id="about">
                <header>
                  <h2>Vice Chancellor's Message</h2>
                </header>
                <div class="section-content" style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                  <?php if (!empty($vc_notice['vc_image'])): ?>
                  <div class="text-center mb-3">
                    <img src="<?php echo htmlspecialchars($vc_notice['vc_image']); ?>" alt="Vice Chancellor" class="img-fluid rounded-circle" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #28a745;" />
                  </div>
                  <?php else: ?>
                  <div class="text-center mb-3">
                    <img src="assets/img/avatar.png" alt="Vice Chancellor" class="img-fluid rounded-circle" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #28a745;" />
                  </div>
                  <?php endif; ?>

                  <h4 class="mb-3"><?php echo htmlspecialchars($vc_notice['title']); ?></h4>
                  <div style="text-align: justify; line-height: 1.6;">
                    <?php echo nl2br(htmlspecialchars($vc_notice['message'])); ?>
                  </div>

                  <?php if (!empty($vc_notice['pdf_url'])): ?>
                  <a href="<?php echo htmlspecialchars($vc_notice['pdf_url']); ?>" class="read-more stick-to-bottom mt-3 d-inline-block" target="_blank">
                    Read Full Message (PDF)
                  </a>
                  <?php else: ?>
                  <a href="about.php" class="read-more stick-to-bottom mt-3 d-inline-block">
                    Read More
                  </a>
                  <?php endif; ?>

                  <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'super_admin'])): ?>
                  <div class="mt-3">
                    <a href="/zanvarsity/html/admin/manage_vc_notice.php" class="btn btn-sm btn-outline-secondary">
                      <i class='bx bx-edit-alt'></i> Edit VC Notice
                    </a>
                  </div>
                  <?php endif; ?>
                </div>
                <!-- /.section-content -->
              </section>
              <!-- /.about -->
            </div>
            <!-- /.col-md-4 -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.container -->
      </div>
      <!-- end News, Events, About -->

      <section id="featured-courses">
        <div class="block">
          <div class="container">
            <header>
              <h2>Our Latest Events</h2>
            </header>
            <div class="events-container">
              <div class="events-track" id="eventsTrack">
                <?php
                        try {
                            // Include database connection
                            require_once __DIR__ . '/../includes/database.php';
                            
                            // Get current date for comparison
                            $current_date = date('Y-m-d H:i:s');
                            
                            // Query to get all events, ordered by start date (newest first)
                            $query = "SELECT * FROM events 
                                     ORDER BY start_date DESC 
                                     LIMIT 12";
                            
                            $stmt = $conn->prepare($query);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            
                            // Store events in an array
                            $events = [];
                            if ($result && $result->num_rows > 0) {
                                while ($event = $result->fetch_assoc()) {
                                    $events[] = $event;
                                }
                            }
                            
                            // Duplicate the events array to create a seamless loop
                            $events = array_merge($events, $events);
                            
                            // Fallback image path
                            $fallbackImage = 'assets/img/no-image-available.jpg';
                            $imagePlaceholders = [
                                'assets/img/course-01.jpg',
                                'assets/img/course-02.jpg',
                                'assets/img/course-03.jpg',
                                'assets/img/course-04.jpg',
                                'assets/img/course-05.jpg',
                                'assets/img/course-06.jpg'
                            ];
                            $placeholderIndex = 0;
                            
                            if (!empty($events)) {
                                foreach ($events as $event) {
                                    // Format dates
                                    $start_date = new DateTime($event['start_date']);
                                    $day = $start_date->format('d');
                                    $month = strtolower($start_date->format('M'));
                                    
                                    // Debug: Show the raw image_url from database
                                    $debug_info = "<div style='display:none;'><strong>Debug Info for Event ID {$event['id']}:</strong><br>";
                                    $debug_info .= "Raw image_url: " . htmlspecialchars($event['image_url'] ?? '') . "<br>";
                                    
                                    // Initialize variables
                                    $image_url = '';
                                    $image_found = false;
                                    
                                    // Get image URL or use fallback
                                    if (!empty($event['image_url'])) {
                                        // If it's already a full URL, use as is
                                        if (strpos($event['image_url'], 'http') === 0) {
                                            $image_url = $event['image_url'];
                                            $debug_info .= "Using full URL: $image_url<br>";
                                            $image_found = true;
                                        } 
                                        // Handle local file paths
                                        else {
                                            // Get just the filename from the path
                                            $filename = basename($event['image_url']);
                                            $debug_info .= "Processing image path: {$event['image_url']}<br>";
                                            $debug_info .= "Base filename: $filename<br>";
                                            
                                            // Set the base URL for the website
                                            $base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/c/zanvarsity';
                                            
                                            // Set the path to the uploads directory
                                            $uploads_dir = 'uploads/events/' . $filename;
                                            $full_path = $_SERVER['DOCUMENT_ROOT'] . '/c/zanvarsity/' . $uploads_dir;
                                            
                                            $debug_info .= "Checking for image at: $full_path<br>";
                                            
                                            if (file_exists($full_path)) {
                                                $image_url = $base_url . '/' . $uploads_dir;
                                                $image_found = true;
                                                $debug_info .= "<span style='color:green;'>Image found at: $image_url</span><br>";
                                            } else {
                                                $debug_info .= "<span style='color:red;'>Image not found at: $full_path</span><br>";
                                                error_log('Image not found for event ' . $event['id'] . ': ' . $full_path);
                                            }
                                        }
                                    }
                                    
                                    // If no image found or no image URL, use a placeholder
                                    if (!$image_found) {
                                        $debug_info .= "Using fallback image<br>";
                                        $image_url = $imagePlaceholders[$placeholderIndex % count($imagePlaceholders)];
                                        $placeholderIndex++;
                                    }
                                    
                                    // Truncate title if too long
                                    $title = htmlspecialchars($event['title']);
                                    if (strlen($title) > 60) {
                                        $title = substr($title, 0, 57) . '...';
                                    }
                                    
                                    // Get location or use default
                                    $location = !empty($event['location']) ? htmlspecialchars($event['location']) : 'Location TBD';
                                    ?>
                <div class="event-col">
                  <article class="event">
                    <div class="event-thumbnail">
                      <figure class="event-image">
                        <div class="image-wrapper">
                          <?php
                          // Ensure the image URL is properly formatted
                          $img_src = '';
                          if (!empty($image_url)) {
                              $img_src = $image_url;
                              $debug_info .= "Using image: $img_src<br>";
                          } else {
                              $img_src = $fallbackImage;
                              $debug_info .= "<span style='color:orange;'>Using fallback image: $fallbackImage</span><br>";
                          }
                          ?>
                          <img src="<?php echo htmlspecialchars($img_src); ?>" 
                               onerror="console.log('Image failed to load: <?php echo htmlspecialchars($img_src); ?>'); this.onerror=null; this.src='<?php echo $fallbackImage; ?>'" 
                               alt="<?php echo htmlspecialchars($event['title']); ?>">
                          <?php if (isset($debug_info)) { echo $debug_info . "</div>"; } ?>
                        </div>
                      </figure>
                      <figure class="date">
                        <div class="month"><?php echo $month; ?></div>
                        <div class="day"><?php echo $day; ?></div>
                      </figure>
                    </div>
                    <aside>
                      <header>
                        <a href="event-detail.php?id=<?php echo $event['id']; ?>"><?php echo $title; ?></a>
                      </header>
                      <div class="additional-info">
                        <span class="fa fa-map-marker"></span> <?php echo $location; ?>
                      </div>
                      <a href="event-detail.php?id=<?php echo $event['id']; ?>" class="btn btn-framed btn-color-grey btn-small">View Details</a>
                    </aside>
                  </article>
                </div>
                <?php
                                }
                            } else {
                                // If no upcoming events, show a message
                                echo '<div class="col-12 text-center">
                                    <div class="alert alert-info">No upcoming events found. Please check back later.</div>
                                </div>';
                            }
                            
                            $stmt->close();
                            
                        } catch (Exception $e) {
                            // Log the error
                            error_log('Error fetching latest events: ' . $e->getMessage());
                            
                            // Show error message
                            echo '<div class="col-12 text-center">
                                <div class="alert alert-danger">Error loading events. Please try again later.</div>
                            </div>';
                        }
                        ?>
              </div><!-- /.events-track -->
            </div><!-- /.events-container -->
            
            <!-- View All Events Button -->
            <div class="text-center" style="margin-top: 15px;">
                <a href="event.php" class="btn btn-primary">View All Events</a>
            </div>
            
          </div><!-- /.container -->
          <div class="background background-color-grey-background"></div>
        </div><!-- /.block -->
      </section>
      <!-- /#featured-courses -->
<!-- Academic Life, Campus Life, Newsletter -->
				<div class="block">
					<div class="container">
						<div class="row">
							<div class="col-md-6">
								<section id="academic-life">
									<header>
										<h2>Academic Life & Research</h2>
									</header>
									<div class="section-content">
										<ul class="list-links">
											<li><a href="#">Programs and Areas</a></li>
											<li><a href="#">Research</a></li>
											<li><a href="#">Graduate & Postdoctoral Programs</a></li>
											<li><a href="#">Continuing Studies</a></li>
											<li><a href="#">International Activities</a></li>
											<li><a href="#">Course Calendars & Listings</a></li>
										</ul>
									</div>
								</section>
							</div>
							<!-- /.col-md-6 -->
							
							<div class="col-md-6">
								<section id="campus-life">
									<header>
										<h2>Campus Life</h2>
									</header>
									<div class="section-content">
										<ul class="list-links">
											<li><a href="#">Athletics & Recreation</a></li>
											<li><a href="#">Clubs & Extra-curricular Activities</a></li>
											<li><a href="#">Health & Wellness</a></li>
											<li><a href="#">Housing & Residence</a></li>
											<li><a href="#">Arts & Culture</a></li>
											<li><a href="#">Student IT Services</a></li>
											<li><a href="#">Newsletter</a></li>
										</ul>
									</div>
								</section>
              </div>
            </div>
          </div>
        </div>


								
					

							<!-- Latest Publications Section -->
							<div class="col-12">
								<section class="pubs py-5 bg-light">
									<div class="container">
										<h2 class="text-center mb-4">Latest Publications</h2>
										<div class="publications-carousel">
											<div class="publications-track" id="publications-track">
												<!-- Publications will be loaded here by JavaScript -->
												<div class="col-12 text-center">
													<div class="spinner-border text-primary" role="status">
														<span class="visually-hidden">Loading...</span>
													</div>
												</div>
											</div>
											<div class="text-center mt-4">
												<a href="publications.php" class="btn btn-primary">
													View All Publications <i class="fas fa-arrow-right ms-2"></i>
												</a>
											</div>
										</div>
									</div>
								</section>
							</div>
							<!-- /.col-12 -->

						
					
				<!-- end Academic Life, Campus Life, Newsletter -->
   
<script>
document.addEventListener('DOMContentLoaded', function() {
    const track = document.getElementById('publications-track');
    let currentPosition = 0;
    let publications = [];
    let itemsPerView = 3; // Default for large screens
    let autoScrollInterval;

    // Update items per view based on screen size
    function updateItemsPerView() {
        if (window.innerWidth < 768) {
            itemsPerView = 1;
        } else if (window.innerWidth < 992) {
            itemsPerView = 2;
        } else {
            itemsPerView = 3;
        }
        // Update CSS variable for responsive card width
        document.documentElement.style.setProperty('--items-per-view', itemsPerView);
        moveCarousel();
    }

    // Move carousel
    function moveCarousel() {
        if (!track || !track.children.length) return;
        
        // Calculate the maximum position to prevent empty space at the end
        const maxPosition = Math.max(0, Math.ceil(publications.length / itemsPerView) - 1);
        currentPosition = Math.min(currentPosition, maxPosition);
        
        const slideWidth = 100 / itemsPerView;
        track.style.transform = `translateX(-${currentPosition * slideWidth}%)`;
    }

    // Create publication card
    function createPublicationCard(pub) {
        // Handle date formatting with fallback
        let pubDate = 'Date not available';
        const dateString = pub.date || pub.created_at || pub.publication_date;
        
        if (dateString) {
            const dateObj = new Date(dateString);
            if (!isNaN(dateObj.getTime())) {
                pubDate = dateObj.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            }
        }

        return `
            <div class="publication-card">
                <div class="card h-100" style="box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <div style="height: 180px; overflow: hidden;">
                        <img src="${pub.image_url || '../assets/images/published.jpg'}" 
                             class="card-img-top h-100 w-100" 
                             alt="${pub.title}" 
                             onerror="this.onerror=null; this.src='../assets/images/published.jpg'"
                             style="object-fit: cover; transition: transform 0.3s ease;">
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title" style="font-size: 1rem; min-height: 3em; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">${pub.title}</h5>
                        <div class="publication-meta small text-muted mb-2">
                            <div class="author">
                                ${pub.firstName || ''} ${pub.middleName ? pub.middleName + ' ' : ''}${pub.lastName || 'ZUMI'}
                            </div>
                            <div class="date mt-1">
                                <i class="far fa-calendar-alt me-1"></i> ${pubDate}
                            </div>
                        </div>
                        <p class="card-text flex-grow-1" style="font-size: 0.9rem; color: #555; min-height: 3em; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">${(pub.description || '').substring(0, 100)}</p>
                        <a href="publication.php?id=${pub.id}" class="btn btn-primary mt-auto" style="background-color: #004225; border-color: #003319;">Read More</a>
                    </div>
                </div>
            </div>
        `;
    }

    // Auto-scroll functionality
    function startAutoScroll() {
        // Clear any existing interval
        stopAutoScroll();
        
        // Start new interval
        autoScrollInterval = setInterval(() => {
            const maxPosition = Math.max(0, Math.ceil(publications.length / itemsPerView) - 1);
            if (currentPosition >= maxPosition) {
                currentPosition = 0;
            } else {
                currentPosition++;
            }
            moveCarousel();
        }, 5000);
    }
    
    function stopAutoScroll() {
        if (autoScrollInterval) {
            clearInterval(autoScrollInterval);
            autoScrollInterval = null;
        }
    }
    
    // Initialize auto-scroll
    startAutoScroll();
    
    // Pause auto-scroll on hover
    const carouselContainer = document.querySelector('.publications-carousel');
    if (carouselContainer) {
        carouselContainer.addEventListener('mouseenter', stopAutoScroll);
        carouselContainer.addEventListener('mouseleave', startAutoScroll);
    }

    // Handle window resize
    window.addEventListener('resize', () => {
        const oldItemsPerView = itemsPerView;
        updateItemsPerView();
        // Recalculate position to maintain the first visible item
        if (oldItemsPerView !== itemsPerView) {
            currentPosition = Math.floor((currentPosition * oldItemsPerView) / itemsPerView);
            moveCarousel();
        }
    });

    // Initial setup
    updateItemsPerView();

    // Fetch publications from API
    fetch('https://www.zumis.ac.tz/academic/data/api/getAllPublications')
        .then(response => response.json())
        .then(data => {
            publications = Array.isArray(data) ? data : [];
            
            if (publications.length === 0) {
                track.innerHTML = `
                    <div class="col-12 text-center">
                        <p>No publications available at the moment.</p>
                    </div>
                `;
                return;
            }

            // Create and append publication cards
            track.innerHTML = publications.map(pub => createPublicationCard(pub)).join('');
            
            // Reset position and restart auto-scroll after loading new content
            currentPosition = 0;
            moveCarousel();
            startAutoScroll();
        })
        .catch(error => {
            console.error('Error loading publications:', error);
            track.innerHTML = `
                <div class="col-12 text-center text-danger">
                    <p>Failed to load publications. Please try again later.</p>
                </div>
            `;
        });
});
</script>
    <style>
      /* Publications Section */
      /* Publications Carousel */
.publications-carousel {
    position: relative;
    overflow: hidden;
    width: 100%;
    padding: 20px 15px 40px;
    margin-bottom: 20px;
}

.publications-track {
    display: flex;
    transition: transform 0.5s ease;
    padding: 10px 0;
    width: 100%;
    flex-wrap: nowrap;
}

.publication-card {
flex: 0 0 calc(100% / var(--items-per-view, 1));
padding: 0 10px;
box-sizing: border-box;
transition: transform 0.3s ease;
}

.publication-card:hover {
transform: translateY(-5px);
}

.carousel-nav {
    text-align: center;
    margin-top: 20px;
}

.carousel-nav button {
    background: #004225;
    color: white;
    border: none;
    padding: 8px 20px;
    margin: 0 10px;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.3s;
}

.carousel-nav button:hover {
    background: #003319;
}

.carousel-nav button:disabled {
    background: #cccccc;
    cursor: not-allowed;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .publication-card {
        flex: 0 0 calc(100% / var(--items-per-view, 2));
    }
}

@media (max-width: 576px) {
    .publication-card {
        flex: 0 0 100%;
    }
}
.pubs {
    padding: 60px 0;
    background-color: #f8f9fa;
}

.publication-card .card {
    height: 100%;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    margin: 0 5px;
    height: 100%;
}

.publication-card .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.publication-card .card:hover img {
    transform: scale(1.05);
}

.pubs .card-img-top {
    border-bottom: 1px solid #eee;
}

.pubs .btn-primary {
    background-color: #004225;
    border-color: #004225;
}

.pubs .btn-primary:hover {
    background-color: #003319;
    border-color: #003319;
}
    .featured-course {
        height: 400px; /* Fixed height for all cards */
        display: flex;
        flex-direction: column;
        margin-bottom: 30px;
        border: 1px solid #eee;
        border-radius: 4px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .featured-course:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transform: translateY(-5px);
    }
    .featured-course .image-wrapper {
        height: 160px;
        overflow: hidden;
        margin: 0;
        padding: 0;
    }
    .featured-course img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .featured-course:hover img {
        transform: scale(1.05);
    }
    .featured-course .description {
        padding: 15px;
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .featured-course h3 {
        font-size: 16px;
        margin: 0 0 8px 0;
        line-height: 1.4;
    }
    .featured-course .author {
        color: #666;
        font-size: 13px;
        margin-bottom: 10px;
    }
    .featured-course .excerpt {
        color: #666;
        font-size: 13px;
        margin: 10px 0;
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }
    .featured-course .course-meta {
        font-size: 12px;
        color: #888;
        margin: 10px 0;
        padding: 0;
    }
    .featured-course .stick-to-bottom {
        margin-top: auto;
    }
    .btn-color-grey {
        background-color: #f5f5f5;
        color: #333;
        border: 1px solid #ddd;
    }
    .btn-color-grey:hover {
        background-color: #e0e0e0;
        color: #000;
    }
    .modal-body img {
        max-width: 100%;
        height: auto;
    }
    </style>
    <!-- /#featured-courses -->
    </div>
    <!-- Divisions, Connect -->
    <div class="block">
					<div class="container">
						<div class="block-dark-background">
							<div class="row">
								<div class="col-md-3 col-sm-4">
									<section id="division" class="has-dark-background">
										<header>
											<h2>Divisions</h2>
										</header>
										<div class="section-content">
											<ul class="list-links">
												<li><a href="#">Accounting & Finance</a></li>
												<li><a href="#">Advertising & Marketing</a></li>
												<li><a href="#">Architecture & Interior</a></li>
												<li><a href="#">Arts & Design</a></li>
												<li><a href="#">Broadcasting & Journalism</a></li>
												<li><a href="#">Business & Management</a></li>
												<li><a href="#">Computing & IT</a></li>
											</ul>
										</div>
										<!-- /.section-content -->
									</section>
									<!-- #.divisions -->
								</div>
								<div class="col-md-9 col-sm-8">
									<section id="connect" class="has-dark-background">
										<header>
											<h2>Connect</h2>
										</header>
										<div class="connect-block">
											<div class="row">
												<div class="col-md-3">
													<ul class="nav nav-pills nav-stacked">
														<li class="active">
															<a href="#tab-twitter" data-toggle="pill"
																><i class="fa fa-twitter"></i>Twitter</a
															>
														</li>
														<li>
															<a href="#tab-facebook" data-toggle="pill"
																><i class="fa fa-facebook"></i>Facebook</a
															>
														</li>
													</ul>
												</div>
												<div class="tab-content">
													<div class="tab-pane fade in active" id="tab-twitter">
														<div class="col-md-3">
															<article class="social-post twitter-post">
																<header>15 minutes ago</header>
																<figure><a href="#">@universo</a></figure>
																<p>
																	Lorem ipsum dolor sit amet, consectetur
																	adipiscing elit. Nullam odio augue, accumsan
																	ut massa ut, faucibus gravida turpis.
																	<a href="http://bit.ly/1bMyz64"
																		>http://bit.ly/1bMyz64</a
																	>
																</p>
															</article>
															<!-- /.twitter-post -->
														</div>
														<div class="col-md-3">
															<article class="social-post twitter-post">
																<header>2 hours ago</header>
																<figure><a href="#">@universo</a></figure>
																<p>
																	Nullam odio augue, accumsan ut massa ut,
																	faucibus gravida turpis. Nulla eleifend libero
																	mi, at consequat tellus.
																	<a href="http://bit.ly/1bMyz64"
																		>http://bit.ly/1bMyz64</a
																	>
																</p>
															</article>
															<!-- /.twitter-post -->
														</div>
														<div class="col-md-3">
															<article class="social-post twitter-post">
																<header>February 02, 2014</header>
																<figure><a href="#">@universo</a></figure>
																<p>
																	Ut at arcu sed justo laoreet iaculis ut nec
																	leo. Aliquam laoreet orci eu egestas
																	fermentum.
																	<a href="http://bit.ly/1bMyz64"
																		>http://bit.ly/1bMyz64</a
																	>
																</p>
															</article>
															<!-- /.twitter-post -->
														</div>
													</div>
													<!-- /.tab-twitter -->
													<div class="tab-pane fade" id="tab-facebook">
														<div class="col-md-3">
															<article class="social-post facebook-post">
																<header>30 minutes ago</header>
																<figure><a href="#">@universo</a></figure>
																<p>
																	Ut at arcu sed justo laoreet iaculis ut nec
																	leo. Aliquam laoreet orci eu egestas
																	fermentum.
																	<a href="http://bit.ly/1bMyz64"
																		>http://bit.ly/1bMyz64</a
																	>
																</p>
															</article>
															<!-- /.twitter-post -->
														</div>
														<div class="col-md-3">
															<article class="social-post facebook-post">
																<header>4 days ago</header>
																<figure><a href="#">@universo</a></figure>
																<p>
																	Lorem ipsum dolor sit amet, consectetur
																	adipiscing elit. Nullam odio augue, accumsan
																	ut massa ut, faucibus gravida turpis.
																	<a href="http://bit.ly/1bMyz64"
																		>http://bit.ly/1bMyz64</a
																	>
																</p>
															</article>
															<!-- /.twitter-post -->
														</div>
														<div class="col-md-3">
															<article class="social-post facebook-post">
																<header>One week ago</header>
																<figure><a href="#">@universo</a></figure>
																<p>
																	Nullam odio augue, accumsan ut massa ut,
																	faucibus gravida turpis. Nulla eleifend libero
																	mi, at consequat tellus.
																	<a href="http://bit.ly/1bMyz64"
																		>http://bit.ly/1bMyz64</a
																	>
																</p>
															</article>
															<!-- /.twitter-post -->
														</div>
													</div>
													<!-- /.tab-twitter -->
												</div>
												<!-- /.tab-content -->
											</div>
											<!-- /.row -->
										</div>
										<!-- /.section-content -->
									</section>
									<!-- #.divisions -->
								</div>
								<!-- /.col-md-9 -->
							</div>
							<!-- /.row -->
						</div>
						<!-- /.block-dark-background -->
					</div>
					<!-- /.container -->
				</div>
				<!-- end Divisions, Connect -->

    <div class="block">
					<div class="container">
						<div class="row">
							<div class="col-md-4 col-sm-4">
              <section id="our-doctors">
    <header>
        <h2>Our Doctors</h2>
    </header>
    <div class="section-content">
        <div class="doctors">
            <?php include 'includes/professors_section.php'; ?>
        </div>
    </div>
</section>

								<!-- /.our-professors -->
							</div>
              
							<!-- /.col-md-4 -->
    <!-- /.container -->
  </div>
  
  <!-- end Partners, Make a Donation -->
  </div>
  <!-- end Page Content -->


  <?php include_once 'includes/about_footer.php'; ?>
 
  <!-- end Footer -->
  </div>
  <!-- end Wrapper -->

  <!-- jQuery and other JS files -->
  <!-- Load jQuery first -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Then load jQuery Migrate -->
  <script src="assets/js/jquery-migrate-1.2.1.min.js"></script>
  <!-- Then load Bootstrap -->
  <script src="assets/bootstrap/js/bootstrap.min.js"></script>
  <!-- Load Owl Carousel -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
  <!-- Other scripts that depend on jQuery -->
  <script src="assets/js/jquery.validate.min.js"></script>
  <script src="assets/js/jquery.placeholder.js"></script>
  <script src="assets/js/selectize.min.js"></script>
  <script src="assets/js/icheck.min.js"></script>
  <script src="assets/js/jQuery.equalHeights.js"></script>
  <script src="assets/js/jquery.vanillabox-0.1.5.min.js"></script>
  <!-- Custom scripts -->
  <script src="assets/js/carousel.js"></script>
  <script src="assets/js/custom.js"></script>
  <!-- Retina.js should be loaded last -->
  <script src="assets/js/retina-1.1.0.min.js"></script>
  
  <script type="text/javascript">
  // Initialize infinite scroll for event cards if the container exists
  document.addEventListener('DOMContentLoaded', function() {
    var eventsContainer = document.querySelector('.events.images.featured');
    if (eventsContainer) {
      var events = eventsContainer.querySelectorAll('.event');
      
      // Only proceed if there are events
      if (events.length > 0) {
        console.log('Initializing infinite scroll for events...');
      }
    }
  });
  </script>
  
  <!-- Add error handling for missing images -->
  <script>
    // Handle missing images
    document.addEventListener('error', function(e) {
      var img = e.target;
      if (img.tagName.toLowerCase() === 'img') {
        // Replace with a placeholder image or hide the element
        if (img.src.includes('default-publication.jpg')) {
          img.style.display = 'none'; // or set a placeholder: img.src = 'path/to/placeholder.jpg';
          console.warn('Image not found:', img.src);
        }
      }
    }, true);
  </script>
  <script>
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
    });
  </script>
</body>
</html>