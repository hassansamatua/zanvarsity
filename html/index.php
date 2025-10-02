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
    .navigation-wrapper {
      background-color: #007848;
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
      background-color: #004225; /* Even darker shade for copyright section */
      color: white;
      padding: 15px 0;
    }
    
    /* Announcement styles */
    #announcements .event {
      width: 100%;
      max-width: 100%;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
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
    
    /* Full width footer */
    #page-footer {
      width: 100%;
      max-width: 100%;
      margin: 0;
      padding: 0;
      overflow: hidden;
    }
    
    #page-footer .container {
      width: 100%;
      max-width: 100%;
      padding: 0;
      margin: 0;
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
  <title>Universo - Educational, Course and University Template</title>
</head>

<body class="page-homepage-carousel">
  <!-- Wrapper -->
  <div class="wrapper">
    <!-- Header -->
    <div class="navigation-wrapper">
      <div class="secondary-navigation-wrapper">
        <div class="container">
          <div class="navigation-contact pull-left">
            Call Us: <span class="opacity-70">000-123-456-789</span>
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
            <li><a href="#">Zumis Portal</a></li>
            <li><a href="#">Prospectus</a></li>
            <li><a href="#">Almanac</a></li>
            <li><a href="#">Fee Structure</a></li>

            <li><a href="#">Alumni</a></li>
            <li><a href="sign-in.php"><i class="fa fa-sign-in"></i> Admin Login</a></li>
          </ul>
        </div>
      </div>
      <!-- /.secondary-navigation -->
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
                <a href="index.html"><img src="assets/img/logo.png" alt="brand" /></a>
              </div>
            </div>
            <nav class="collapse navbar-collapse bs-navbar-collapse navbar-right" role="navigation">
              <ul class="nav navbar-nav">
                <li class="active">
                  <a href="#" class="has-child no-link">Home</a>
                  <ul class="list-unstyled child-navigation">
                    <li><a href="index.html">Homepage Education</a></li>
                    <li>
                      <a href="homepage-courses.html">Homepage Courses</a>
                    </li>
                    <li>
                      <a href="homepage-events.html">Homepage Events</a>
                    </li>
                  </ul>
                </li>
                <li>
                  <a href="#" class="has-child no-link">About</a>
                  <ul class="list-unstyled child-navigation">
                    <li>
                      <a href="course-landing-page.html">Course Landing Page</a>
                    </li>
                    <li><a href="course-listing.html">Course Listing</a></li>
                    <li>
                      <a href="course-listing-images.html">Course Listing with Images</a>
                    </li>
                    <li>
                      <a href="course-detail-v1.html">Course Detail v1</a>
                    </li>
                    <li>
                      <a href="course-detail-v2.html">Course Detail v2</a>
                    </li>
                    <li>
                      <a href="course-detail-v3.html">Course Detail v3</a>
                    </li>
                  </ul>
                </li>
                <li>
                  <a href="#" class="has-child no-link">Admission</a>
                  <ul class="list-unstyled child-navigation">
                    <li>
                      <a href="event-listing-images.html">Events Listing with images</a>
                    </li>
                    <li><a href="event-listing.html">Events Listing</a></li>
                    <li><a href="event-grid.html">Events Grid</a></li>
                    <li><a href="event-detail.html">Event Detail</a></li>
                    <li><a href="event-calendar.html">Events Calendar</a></li>
                  </ul>
                </li>
                <!-- <li>
										<a href="about-us.html">About Us</a>
									</li> -->
                <li>
                  <a href="#" class="has-child no-link">Academics</a>
                  <ul class="list-unstyled child-navigation">
                    <li><a href="blog-listing.html">Blog listing</a></li>
                    <li><a href="blog-detail.html">Blog Detail</a></li>
                  </ul>
                </li>
                <li>
                  <a href="#" class="has-child no-link">Directorates</a>
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

    <!-- Page Content -->
    <div id="page-content">
      <!-- Slider -->
      <div id="homepage-carousel">
        <div class="container">
          <div class="homepage-carousel-wrapper">
            <div class="row">
              <div class="col-md-6 col-sm-7">
                <div class="image-carousel">
                  <div class="image-carousel-slide">
                    <img src="assets/img/slide-1.jpg" alt="" />
                  </div>
                  <div class="image-carousel-slide">
                    <img src="assets/img/slide-2.jpg" alt="" />
                  </div>
                  <div class="image-carousel-slide">
                    <img src="assets/img/slide-3.jpg" alt="" />
                  </div>
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
            <div class="row">
              <div class="events images featured">
                <?php
                        try {
                            // Include database connection
                            require_once __DIR__ . '/../includes/database.php';
                            
                            // Get current date for comparison
                            $current_date = date('Y-m-d H:i:s');
                            
                            // Query to get all events, ordered by start date (newest first), limit to 4
                            $query = "SELECT * FROM events 
                                     ORDER BY start_date DESC 
                                     LIMIT 4";
                            
                            $stmt = $conn->prepare($query);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            
                            // Log the number of events found for debugging
                            error_log('Number of events found: ' . $result->num_rows);
                            
                            // Fallback image path
                            $fallbackImage = 'assets/img/no-image-available.jpg';
                            $imagePlaceholders = [
                                'assets/img/course-01.jpg',
                                'assets/img/course-02.jpg',
                                'assets/img/course-03.jpg',
                                'assets/img/course-04.jpg'
                            ];
                            $placeholderIndex = 0;
                            
                            if ($result && $result->num_rows > 0) {
                                while ($event = $result->fetch_assoc()) {
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
                <div class="col-md-3 col-sm-6">
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
              </div><!-- /.events -->
            </div><!-- /.row -->
          </div><!-- /.container -->
          <div class="background background-color-grey-background"></div>
        </div><!-- /.block -->
      </section>
      <!-- /#featured-courses -->

    </div>
    <!-- /.container -->
  </div>
  <!-- end Partners, Make a Donation -->
  </div>
  <!-- end Page Content -->

  <!-- Footer -->
  <footer id="page-footer">
    <section id="footer-top">
      <div class="container">
        <div class="footer-inner">
          <div class="footer-social">
            <figure>Follow us:</figure>
            <div class="icons">
              <a href=""><i class="fa fa-twitter"></i></a>
              <a href=""><i class="fa fa-facebook"></i></a>
              <a href=""><i class="fa fa-pinterest"></i></a>
              <a href=""><i class="fa fa-youtube-play"></i></a>
            </div>
            <!-- /.icons -->
          </div>
          <!-- /.social -->
          <div class="search pull-right">
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Search" />
              <span class="input-group-btn">
                <button type="submit" class="btn">
                  <i class="fa fa-search"></i>
                </button>
              </span>
            </div>
            <!-- /input-group -->
          </div>
          <!-- /.pull-right -->
        </div>
        <!-- /.footer-inner -->
      </div>
      <!-- /.container -->
    </section>
    <!-- /#footer-top -->

    <section id="footer-content">
      <div class="container">
        <div class="row">
          <div class="col-md-3 col-sm-12">
            <aside class="logo">
              <img src="assets/img/logo-white.png" class="vertical-center" />
            </aside>
          </div>
          <!-- /.col-md-3 -->
          <div class="col-md-3 col-sm-4">
            <aside>
              <header>
                <h4>Contact Us</h4>
              </header>
              <address>
                <strong>University of Universo</strong>
                <br />
                <span>4877 Spruce Drive</span>
                <br /><br />
                <span>West Newton, PA 15089</span>
                <br />
                <abbr title="Telephone">Telephone:</abbr> +1 (734) 123-4567
                <br />
                <abbr title="Email">Email:</abbr>
                <a href="#">questions@youruniversity.com</a>
              </address>
            </aside>
          </div>
          <!-- /.col-md-3 -->
          <div class="col-md-3 col-sm-4">
            <aside>
              <header>
                <h4>Important Links</h4>
              </header>
              <ul class="list-links">
                <li><a href="#">Future Students</a></li>
                <li><a href="#">Alumni</a></li>
                <li><a href="#">Give a Donation</a></li>
                <li><a href="#">Professors</a></li>
                <li><a href="#">Libary & Health</a></li>
                <li><a href="#">Research</a></li>
              </ul>
            </aside>
          </div>
          <!-- /.col-md-3 -->
          <div class="col-md-3 col-sm-4">
            <aside>
              <header>
                <h4>About Universo</h4>
              </header>
              <p>
                Aliquam feugiat turpis quis felis adipiscing, non pulvinar
                odio lacinia. Aliquam elementum pharetra fringilla. Duis
                blandit, sapien in semper vehicula, tellus elit gravida
                odio, ac tincidunt nisl mi at ante. Vivamus tincidunt nunc
                nibh.
              </p>
              <div>
                <a href="" class="read-more">All News</a>
              </div>
            </aside>
          </div>
          <!-- /.col-md-3 -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container -->
      <div class="background">
        <img src="assets/img/background-city.png" class="" alt="" />
      </div>
    </section>
    <!-- /#footer-content -->

    <section id="footer-bottom">
      <div class="container">
        <div class="footer-inner">
          <div class="copyright">© Theme Starz, All rights reserved</div>
          <!-- /.copyright -->
        </div>
        <!-- /.footer-inner -->
      </div>
      <!-- /.container -->
    </section>
    <!-- /#footer-bottom -->
  </footer>
  <!-- end Footer -->
  </div>
  <!-- end Wrapper -->

  <script type="text/javascript" src="assets/js/jquery-2.1.0.min.js"></script>
  <script type="text/javascript" src="assets/js/jquery-migrate-1.2.1.min.js"></script>
  <script type="text/javascript" src="assets/bootstrap/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="assets/js/selectize.min.js"></script>
  <script type="text/javascript" src="assets/js/owl.carousel.min.js"></script>
  <script type="text/javascript" src="assets/js/jquery.validate.min.js"></script>
  <script type="text/javascript" src="assets/js/jquery.placeholder.js"></script>
  <script type="text/javascript" src="assets/js/jQuery.equalHeights.js"></script>
  <script type="text/javascript" src="assets/js/icheck.min.js"></script>
  <script type="text/javascript" src="assets/js/jquery.vanillabox-0.1.5.min.js"></script>
  <script type="text/javascript" src="assets/js/retina-1.1.0.min.js"></script>

  <script type="text/javascript" src="assets/js/custom.js"></script>
  <script>
    // Initialize infinite scroll for event cards
    document.addEventListener('DOMContentLoaded', function() {
      const eventsContainer = document.querySelector('.events.images.featured');
      if (eventsContainer) {
        const events = eventsContainer.querySelectorAll('.event');
        
        // Create wrapper for scrolling
        const wrapper = document.createElement('div');
        wrapper.className = 'scrolling-wrapper';
        
        // Clone events for seamless looping
        const fragment1 = document.createDocumentFragment();
        const fragment2 = document.createDocumentFragment();
        
        events.forEach(event => {
          fragment1.appendChild(event.cloneNode(true));
          fragment2.appendChild(event.cloneNode(true));
          event.remove();
        });
        
        // Append both sets to the wrapper
        wrapper.appendChild(fragment1);
        wrapper.appendChild(fragment2);
        
        // Add the wrapper to the container
        eventsContainer.appendChild(wrapper);
      }
    });
  </script>
</body>

</html>

<script type="text/javascript" src="assets/js/icheck.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.vanillabox-0.1.5.min.js"></script>
<script type="text/javascript" src="assets/js/retina-1.1.0.min.js"></script>

<script type="text/javascript" src="assets/js/custom.js"></script>
</body>

</html>


</div>
<!-- end Wrapper -->

<script type="text/javascript" src="assets/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="assets/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/js/selectize.min.js"></script>
<script type="text/javascript" src="assets/js/owl.carousel.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.placeholder.js"></script>
<script type="text/javascript" src="assets/js/jQuery.equalHeights.js"></script>
<script type="text/javascript" src="assets/js/icheck.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.vanillabox-0.1.5.min.js"></script>
<script type="text/javascript" src="assets/js/retina-1.1.0.min.js"></script>

<script type="text/javascript" src="assets/js/custom.js"></script>
</body>

</html>

<script type="text/javascript" src="assets/js/icheck.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.vanillabox-0.1.5.min.js"></script>
<script type="text/javascript" src="assets/js/retina-1.1.0.min.js"></script>

<script type="text/javascript" src="assets/js/custom.js"></script>
</body>

</html>