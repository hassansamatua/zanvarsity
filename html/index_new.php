<?php
// Include database connection at the top of the file
require_once __DIR__ . '/../includes/db_connect.php';
?>
<!DOCTYPE html>

<html lang="en-US">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta name="author" content="Theme Starz" />

		<link
			href="http://fonts.googleapis.com/css?family=Montserrat:400,700"
			rel="stylesheet"
			type="text/css"
		/>
		<link href="assets/css/font-awesome.css" rel="stylesheet" type="text/css" />
		<link
			rel="stylesheet"
			href="assets/bootstrap/css/bootstrap.css"
			type="text/css"
		/>
		<link
			rel="stylesheet"
			href="assets/css/selectize.css"
			type="text/css"
		/>
		<link
			rel="stylesheet"
			href="assets/css/owl.carousel.css"
			type="text/css"
		/>
		<link
			rel="stylesheet"
			href="assets/css/vanillabox/vanillabox.css"
			type="text/css"
		/>

		<link rel="stylesheet" href="assets/css/style.css" type="text/css" />

		<title>ZANVarsity - Home</title>

		<!--[if lt IE 9]>
			<script type="text/javascript" src="assets/js/jquery-1.11.0.min.js"></script>
		<![endif]-->
		<!--[if gte IE 9]><!-->
		<script type="text/javascript" src="assets/js/jquery-2.1.0.min.js"></script>
		<!--<![endif]-->
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
						<ul class="secondary-navigation">
							<li><a href="#">My Account</a></li>
							<li><a href="#">Register</a></li>
							<li><a href="#">Login</a></li>
						</ul>
					</div>
				</div>
				<!-- /.secondary-navigation -->

				<div class="primary-navigation-wrapper">
					<header class="navbar" id="top" role="banner">
						<div class="container">
							<div class="navbar-header">
								<button
									class="navbar-toggle"
									type="button"
									data-toggle="collapse"
									data-target=".bs-navbar-collapse"
								>
									<span class="sr-only">Toggle navigation</span>
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
								</button>
								<div class="navbar-brand nav" id="brand">
									<a href="index.html">
										<img
											src="assets/img/logo.png"
											alt="brand"
										/>
									</a>
								</div>
							</div>
							<nav
								class="collapse navbar-collapse bs-navbar-collapse navbar-right"
								role="navigation"
							>
								<ul class="nav navbar-nav">
									<li class="active">
										<a href="index.html">Home</a>
									</li>
									<li class="has-submenu">
										<a href="#">Academics</a>
										<ul class="sub-menu">
											<li>
												<a href="course-listing.html">Course Listing</a>
											</li>
											<li>
												<a href="course-detail.html">Course Detail</a>
											</li>
											<li>
												<a href="course-detail-video.html"
													>Course Detail with Video</a
												>
											</li>
										</ul>
									</li>
									<li class="has-submenu">
										<a href="#">News & Events</a>
										<ul class="sub-menu">
											<li><a href="news.html">News</a></li>
											<li><a href="news-detail.html">News Detail</a></li>
											<li><a href="events.html">Events</a></li>
											<li><a href="event-detail.html">Event Detail</a></li>
										</ul>
									</li>
									<li class="has-submenu">
										<a href="#">Pages</a>
										<ul class="sub-menu">
											<li><a href="about-us.html">About Us</a></li>
											<li><a href="our-team.html">Our Team</a></li>
											<li><a href="faq.html">FAQ</a></li>
											<li><a href="404.html">404</a></li>
											<li><a href="coming-soon.html">Coming Soon</a></li>
											<li><a href="login.html">Login</a></li>
											<li><a href="register.html">Register</a></li>
										</ul>
									</li>
									<li><a href="contact-us.html">Contact</a></li>
								</ul>
							</nav>
							<!-- /.navbar collapse-->
						</div>
						<!-- /.container -->
					</header>
					<!-- /.navbar -->
				</div>
				<!-- /.primary-navigation -->
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
											<img
												src="assets/img/slide-1.jpg"
												alt=""
											/>
										</div>
										<div class="image-carousel-slide">
											<img
												src="assets/img/slide-2.jpg"
												alt=""
											/>
										</div>
										<div class="image-carousel-slide">
											<img
												src="assets/img/slide-3.jpg"
												alt=""
											/>
										</div>
									</div>
									<!-- /.image-carousel -->
								</div>
								<!-- /.col-md-6 -->
								<div class="col-md-6 col-sm-5">
									<div class="slider-content">
										<div class="slider-content-item">
											<h1>Join the community of modern thinking students</h1>
											<p class="lead">
												Experience quality education with our expert faculty.
											</p>
										</div>
										<div class="slider-content-item">
											<h1>Advance your career with our courses</h1>
											<p class="lead">
												Learn from industry experts and get certified.
											</p>
										</div>
										<div class="slider-content-item">
											<h1>Join us today and start learning</h1>
											<p class="lead">
												Unlock your potential with our comprehensive courses.
											</p>
										</div>
									</div>
									<!-- /.slider-content -->
									<form
										id="slider-form"
										role="form"
										action=""
										method="post"
									>
										<div class="form-group">
											<input
												type="text"
												class="form-control"
												id="name"
												placeholder="Your Name"
												required
											/>
										</div>
										<div class="form-group">
											<input
												type="email"
												class="form-control"
												id="email"
												placeholder="Your Email"
												required
											/>
										</div>
										<div class="form-group">
											<select
												class="form-control"
												id="course"
												required
											>
												<option value="">Select a Course</option>
												<option value="1">Computer Science</option>
												<option value="2">Business Administration</option>
												<option value="3">Engineering</option>
											</select>
										</div>
										<button type="submit" class="btn btn-framed">
											Apply Now
										</button>
									</form>
								</div>
								<!-- /.col-md-6 -->
							</div>
							<!-- /.row -->
						</div>
						<!-- /.homepage-carousel-wrapper -->
						<div class="slider-navigation">
							<button class="slider-nav-prev">
								<i class="fa fa-chevron-left"></i>
							</button>
							<button class="slider-nav-next">
								<i class="fa fa-chevron-right"></i>
							</button>
						</div>
						<!-- /.slider-navigation -->
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
										// Fetch upcoming events from database
										try {
											$today = date('Y-m-d');
											$eventsQuery = "SELECT * FROM event 
													   WHERE start_date > ? 
													   ORDER BY start_date ASC 
													   LIMIT 3";
											
											$hasEvents = false;
											if ($stmt = $conn->prepare($eventsQuery)) {
												$stmt->bind_param('s', $today);
												$stmt->execute();
												$result = $stmt->get_result();
												
												if ($result && $result->num_rows > 0) {
													while ($event = $result->fetch_assoc()) {
														$eventDate = !empty($event['start_date']) ? date('m-d-Y', strtotime($event['start_date'])) : date('m-d-Y');
														$eventTitle = !empty($event['title']) ? htmlspecialchars($event['title']) : 'New Event';
														$eventLink = !empty($event['url']) ? htmlspecialchars($event['url']) : '#';
														$hasEvents = true;
														?>
														<article>
															<figure class="date">
																<i class="fa fa-calendar"></i><?php echo $eventDate; ?>
															</figure>
															<header>
																<a href="<?php echo $eventLink; ?>"><?php echo $eventTitle; ?></a>
															</header>
														</article>
														<!-- /article -->
														<?php
													}
												}
												$stmt->close();
											}

											if (!$hasEvents) {
												?>
												<article>
													<figure class="date">
														<i class="fa fa-calendar"></i><?php echo date('m-d-Y'); ?>
													</figure>
													<header>
														<a href="#">No upcoming events scheduled</a>
													</header>
												</article>
												<?php
											}
										} catch (Exception $e) {
											error_log('Events query error: ' . $e->getMessage());
											?>
											<article>
												<figure class="date">
													<i class="fa fa-calendar"><?php echo date('m-d-Y'); ?></i>
												</figure>
												<header>
													<a href="#">Error loading events</a>
												</header>
											</article>
											<?php
										}
										?>
									</div>
									<!-- /.section-content -->
									<a href="events.html" class="read-more stick-to-bottom">All Events</a>
								</section>
								<!-- /.news-small -->
							</div>
							<!-- /.col-md-4 -->
							<div class="col-md-4 col-sm-6">
								<section class="events small" id="events-small">
									<header>
										<h2>Latest News</h2>
										<a href="news.html" class="link-calendar">News</a>
									</header>
									<div class="section-content">
										<article class="event nearest">
											<figure class="date">
												<div class="month">jan</div>
												<div class="day">18</div>
											</figure>
											<aside>
												<header>
													<a href="news-detail.html"
														>University welcomes new students at orientation</a
													>
												</header>
												<div class="additional-info">
													Main Campus
												</div>
											</aside>
										</article>
										<!-- /article -->
										<article class="event nearest-second">
											<figure class="date">
												<div class="month">feb</div>
												<div class="day">01</div>
											</figure>
											<aside>
												<header>
													<a href="news-detail.html"
														>Research team publishes groundbreaking study</a
													>
												</header>
												<div class="additional-info clearfix">
													Research Department
												</div>
											</aside>
										</article>
										<!-- /article -->
										<article class="event">
											<figure class="date">
												<div class="month">mar</div>
												<div class="day">15</div>
											</figure>
											<aside>
												<header>
													<a href="news-detail.html"
														>Alumni weekend celebration planned for spring</a
													>
												</header>
												<div class="additional-info">
													Alumni Association
												</div>
											</aside>
										</article>
										<!-- /article -->
									</div>
									<!-- /.section-content -->
								</section>
								<!-- /.events-small -->
							</div>
							<!-- /.col-md-4 -->
							<div class="col-md-4 col-sm-12">
								<section id="about">
									<header>
										<h2>About ZANVarsity</h2>
									</header>
									<div class="section-content">
										<img
											class="about-img"
											src="assets/img/student.jpg"
											alt="About Us"
										/>
										<p>
											ZANVarsity is a leading institution of higher learning
											committed to academic excellence, research, and community
											engagement. Our diverse programs and world-class faculty
											provide students with the knowledge and skills needed to
											succeed in today's global economy.
										</p>
										<a href="about-us.html" class="read-more">Read More</a>
									</div>
									<!-- /.section-content -->
								</section>
								<!-- /#about -->
							</div>
							<!-- /.col-md-4 -->
						</div>
						<!-- /.row -->
					</div>
					<!-- /.container -->
				</div>
				<!-- end News, Events, About -->

				<!-- Testimonials -->
				<section id="testimonials">
					<div class="block">
						<div class="container">
							<div class="author-carousel">
								<div class="author">
									<blockquote>
										<figure class="author-picture">
											<img
												src="assets/img/student-testimonial.jpg"
												alt=""
											/>
										</figure>
										<article class="paragraph-wrapper">
											<div class="inner">
												<header>
													ZANVarsity has provided me with an exceptional
													education and the skills I need to succeed in my
													career.
												</header>
												<footer>John Doe, Computer Science Graduate</footer>
											</div>
										</article>
									</blockquote>
								</div>
								<!-- /.author -->
								<div class="author">
									<blockquote>
										<figure class="author-picture">
											<img
												src="assets/img/student-testimonial-2.jpg"
												alt=""
											/>
										</figure>
										<article class="paragraph-wrapper">
											<div class="inner">
												<header>
													The faculty at ZANVarsity are truly dedicated to
													their students' success.
												</header>
												<footer>Jane Smith, Business Administration</footer>
											</div>
										</article>
									</blockquote>
								</div>
								<!-- /.author -->
							</div>
							<!-- /.author-carousel -->
						</div>
						<!-- /.container -->
					</div>
					<!-- /.block -->
				</section>
				<!-- end Testimonials -->

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
												<li><a href="#">Arts & Humanities</a></li>
												<li><a href="#">Business</a></li>
												<li><a href="#">Engineering</a></li>
												<li><a href="#">Health Sciences</a></li>
												<li><a href="#">Science & Technology</a></li>
												<li><a href="#">Social Sciences</a></li>
											</ul>
										</div>
										<!-- /.section-content -->
									</section>
									<!-- /#division -->
								</div>
								<!-- /.col-md-3 -->
								<div class="col-md-9 col-sm-8">
									<section id="connect" class="has-dark-background">
										<header>
											<h2>Connect</h2>
										</header>
										<div class="section-content">
											<div class="row">
												<div class="col-md-4">
													<article class="event">
														<figure class="date">
															<i class="fa fa-map-marker"></i>
														</figure>
														<aside>
															<header>
																<a href="#">Visit Us</a>
															</header>
															<div class="additional-info">
																123 University Avenue<br />
																City, State 12345
															</div>
														</aside>
													</article>
												</div>
												<!-- /.col-md-4 -->
												<div class="col-md-4">
													<article class="event">
														<figure class="date">
															<i class="fa fa-phone"></i>
														</figure>
														<aside>
															<header>
																<a href="#">Contact Us</a>
															</header>
															<div class="additional-info">
																Phone: (123) 456-7890<br />
																Fax: (123) 456-7891
															</div>
														</aside>
													</article>
												</div>
												<!-- /.col-md-4 -->
												<div class="col-md-4">
													<article class="event">
														<figure class="date">
															<i class="fa fa-envelope"></i>
														</figure>
														<aside>
															<header>
																<a href="#">Email Us</a>
															</header>
															<div class="additional-info">
																<a href="mailto:info@zanvarsity.edu"
																	>info@zanvarsity.edu</a
																>
															</div>
														</aside>
													</article>
												</div>
												<!-- /.col-md-4 -->
											</div>
											<!-- /.row -->
											<div class="row">
												<div class="col-md-12">
													<div class="social">
														<a href="#" class="social-btn"
															><i class="fa fa-twitter"></i
														></a>
														<a href="#" class="social-btn"
															><i class="fa fa-facebook"></i
														></a>
														<a href="#" class="social-btn"
															><i class="fa fa-linkedin"></i
														></a>
														<a href="#" class="social-btn"
															><i class="fa fa-youtube"></i
														></a>
														<a href="#" class="social-btn"
															><i class="fa fa-instagram"></i
														></a>
													</div>
													<!-- /.social -->
												</div>
												<!-- /.col-md-12 -->
											</div>
											<!-- /.row -->
										</div>
										<!-- /.section-content -->
									</section>
									<!-- /#connect -->
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

				<!-- Our Professors, Gallery -->
				<div class="block">
					<div class="container">
						<div class="row">
							<div class="col-md-4 col-sm-4">
								<section id="our-professors">
									<header>
										<h2>Our Professors</h2>
									</header>
									<div class="section-content">
										<div class="professors">
											<article class="professor">
												<figure class="professor-picture">
													<img
														src="assets/img/professor-1.jpg"
														alt=""
													/>
												</figure>
												<aside>
													<header>
														<h4>Dr. John Smith</h4>
														<figure>Computer Science</figure>
													</header>
													<p>
														Dr. Smith has over 15 years of experience in
														the field of artificial intelligence and machine
														learning.
													</p>
													<a href="#" class="read-more">View Profile</a>
												</aside>
											</article>
											<!-- /.professor -->
											<article class="professor">
												<figure class="professor-picture">
													<img
														src="assets/img/professor-2.jpg"
														alt=""
													/>
												</figure>
												<aside>
													<header>
														<h4>Dr. Sarah Johnson</h4>
														<figure>Business Administration</figure>
													</header>
													<p>
														Dr. Johnson specializes in marketing strategy
														and consumer behavior with over 10 years of
														industry experience.
													</p>
													<a href="#" class="read-more">View Profile</a>
												</aside>
											</article>
											<!-- /.professor -->
										</div>
										<!-- /.professors -->
									</div>
									<!-- /.section-content -->
								</section>
								<!-- /#our-professors -->
							</div>
							<!-- /.col-md-4 -->
							<div class="col-md-8 col-sm-8">
								<section id="gallery">
									<header>
										<h2>Gallery</h2>
									</header>
									<div class="section-content">
										<div class="gallery-carousel">
											<div class="gallery-item">
												<figure class="gallery-image">
													<a
														href="assets/img/gallery/gallery-1.jpg"
														class="image-link"
														data-rel="prettyPhoto[gallery]"
														><img
															src="assets/img/gallery/thumb-1.jpg"
															alt=""
													/></a>
												</figure>
											</div>
											<!-- /.gallery-item -->
											<div class="gallery-item">
												<figure class="gallery-image">
													<a
														href="assets/img/gallery/gallery-2.jpg"
														class="image-link"
														data-rel="prettyPhoto[gallery]"
														><img
															src="assets/img/gallery/thumb-2.jpg"
															alt=""
													/></a>
												</figure>
											</div>
											<!-- /.gallery-item -->
											<div class="gallery-item">
												<figure class="gallery-image">
													<a
														href="assets/img/gallery/gallery-3.jpg"
														class="image-link"
														data-rel="prettyPhoto[gallery]"
														><img
															src="assets/img/gallery/thumb-3.jpg"
															alt=""
													/></a>
												</figure>
											</div>
											<!-- /.gallery-item -->
											<div class="gallery-item">
												<figure class="gallery-image">
													<a
														href="assets/img/gallery/gallery-4.jpg"
														class="image-link"
														data-rel="prettyPhoto[gallery]"
														><img
															src="assets/img/gallery/thumb-4.jpg"
															alt=""
													/></a>
												</figure>
											</div>
											<!-- /.gallery-item -->
											<div class="gallery-item">
												<figure class="gallery-image">
													<a
														href="assets/img/gallery/gallery-5.jpg"
														class="image-link"
														data-rel="prettyPhoto[gallery]"
														><img
															src="assets/img/gallery/thumb-5.jpg"
															alt=""
													/></a>
												</figure>
											</div>
											<!-- /.gallery-item -->
											<div class="gallery-item">
												<figure class="gallery-image">
													<a
														href="assets/img/gallery/gallery-6.jpg"
														class="image-link"
														data-rel="prettyPhoto[gallery]"
														><img
															src="assets/img/gallery/thumb-6.jpg"
															alt=""
													/></a>
												</figure>
											</div>
											<!-- /.gallery-item -->
										</div>
										<!-- /.gallery-carousel -->
									</div>
									<!-- /.section-content -->
								</section>
								<!-- /#gallery -->
							</div>
							<!-- /.col-md-8 -->
						</div>
						<!-- /.row -->
					</div>
					<!-- /.container -->
				</div>
				<!-- end Our Professors, Gallery -->

				<!-- Partners, Make a Donation -->
				<div class="block">
					<div class="container">
						<div class="row">
							<div class="col-md-9 col-sm-9">
								<section id="partners">
									<header>
										<h2>Our Partners</h2>
									</header>
									<div class="section-content">
										<div class="partners">
											<div class="partner">
												<img
													src="assets/img/partner-1.png"
													alt="Partner 1"
												/>
											</div>
											<!-- /.partner -->
											<div class="partner">
												<img
													src="assets/img/partner-2.png"
													alt="Partner 2"
												/>
											</div>
											<!-- /.partner -->
											<div class="partner">
												<img
													src="assets/img/partner-3.png"
													alt="Partner 3"
												/>
											</div>
											<!-- /.partner -->
											<div class="partner">
												<img
													src="assets/img/partner-4.png"
													alt="Partner 4"
												/>
											</div>
											<!-- /.partner -->
											<div class="partner">
												<img
													src="assets/img/partner-5.png"
													alt="Partner 5"
												/>
											</div>
											<!-- /.partner -->
										</div>
										<!-- /.partners -->
									</div>
									<!-- /.section-content -->
								</section>
								<!-- /#partners -->
							</div>
							<!-- /.col-md-9 -->
							<div class="col-md-3 col-sm-3">
								<section id="donation" class="has-dark-background">
									<header>
										<h2>Make a Donation</h2>
									</header>
									<div class="section-content">
										<p>
											Your support helps us provide scholarships and
											enhance our facilities for future generations of
											students.
										</p>
										<a href="#" class="btn btn-framed">Donate Now</a>
									</div>
									<!-- /.section-content -->
								</section>
								<!-- /#donation -->
							</div>
							<!-- /.col-md-3 -->
						</div>
						<!-- /.row -->
					</div>
					<!-- /.container -->
				</div>
				<!-- end Partners, Make a Donation -->
			</div>
			<!-- end Page Content -->

			<!-- Footer -->
			<footer id="page-footer">
				<div class="container">
					<div class="row">
						<div class="col-md-3 col-sm-4">
							<section class="footer-logo">
								<figure>
									<a href="index.html">
										<img
											src="assets/img/logo-footer.png"
											alt="ZANVarsity"
										/>
									</a>
								</figure>
								<p>
									ZANVarsity is committed to providing quality
									education and fostering innovation and research.
								</p>
								<address>
									<p>123 University Avenue</p>
									<p>City, State 12345</p>
									<p>Phone: (123) 456-7890</p>
									<p>
										Email:
										<a href="mailto:info@zanvarsity.edu"
											>info@zanvarsity.edu</a
										>
									</p>
								</address>
							</section>
							<!-- /.footer-logo -->
						</div>
						<!-- /.col-md-3 -->
						<div class="col-md-3 col-sm-4">
							<section class="footer-links">
								<header>
									<h3>Quick Links</h3>
								</header>
								<ul class="list-links">
									<li><a href="about-us.html">About Us</a></li>
									<li><a href="academics.html">Academics</a></li>
									<li><a href="admissions.html">Admissions</a></li>
									<li><a href="research.html">Research</a></li>
									<li><a href="campus-life.html">Campus Life</a></li>
									<li><a href="alumni.html">Alumni</a></li>
									<li><a href="news.html">News & Events</a></li>
									<li><a href="contact-us.html">Contact Us</a></li>
								</ul>
							</section>
							<!-- /.footer-links -->
						</div>
						<!-- /.col-md-3 -->
						<div class="col-md-3 col-sm-4">
							<section class="footer-newsletter">
								<header>
									<h3>Newsletter</h3>
								</header>
								<p>
									Subscribe to our newsletter for the latest news and
									updates.
								</p>
								<form
									class="newsletter-form"
									id="newsletter-form"
									method="post"
									action="#"
								>
									<div class="form-group">
										<input
											type="email"
											class="form-control"
											placeholder="Your email address"
											required
										/>
									</div>
									<button type="submit" class="btn btn-framed">
										Subscribe
									</button>
								</form>
								<div class="social">
									<a href="#" class="social-btn"
										><i class="fa fa-twitter"></i
									></a>
									<a href="#" class="social-btn"
										><i class="fa fa-facebook"></i
									></a>
									<a href="#" class="social-btn"
										><i class="fa fa-linkedin"></i
									></a>
									<a href="#" class="social-btn"
										><i class="fa fa-youtube"></i
									></a>
									<a href="#" class="social-btn"
										><i class="fa fa-instagram"></i
									></a>
								</div>
								<!-- /.social -->
							</section>
							<!-- /.footer-newsletter -->
						</div>
						<!-- /.col-md-3 -->
						<div class="col-md-3 col-sm-12">
							<section class="footer-contact">
								<header>
									<h3>Contact Us</h3>
								</header>
								<div class="contact-info">
									<p><i class="fa fa-phone"></i> (123) 456-7890</p>
									<p>
										<i class="fa fa-envelope-o"></i>
										<a href="mailto:info@zanvarsity.edu"
											>info@zanvarsity.edu</a
										>
									</p>
									<p>
										<i class="fa fa-map-marker"></i> 123 University
										Avenue, City, State 12345
									</p>
								</div>
								<!-- /.contact-info -->
							</section>
							<!-- /.footer-contact -->
						</div>
						<!-- /.col-md-3 -->
					</div>
					<!-- /.row -->
				</div>
				<!-- /.container -->
				<div class="footer-bottom">
					<div class="container">
						<div class="row">
							<div class="col-md-6">
								<p class="copyright">
									&copy; 2023 ZANVarsity. All Rights Reserved.
								</p>
							</div>
							<!-- /.col-md-6 -->
							<div class="col-md-6">
								<nav class="footer-nav">
									<ul>
										<li><a href="privacy-policy.html">Privacy Policy</a></li>
										<li><a href="terms-conditions.html">Terms & Conditions</a></li>
										<li><a href="sitemap.html">Sitemap</a></li>
									</ul>
								</nav>
								<!-- /.footer-nav -->
							</div>
							<!-- /.col-md-6 -->
						</div>
						<!-- /.row -->
					</div>
					<!-- /.container -->
				</div>
				<!-- /.footer-bottom -->
			</footer>
			<!-- end Footer -->
		</div>
		<!-- end Wrapper -->

		<script type="text/javascript" src="assets/js/jquery-2.1.0.min.js"></script>
		<script
			src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
			integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
			crossorigin="anonymous"
		></script>
		<script
			src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
			integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
			crossorigin="anonymous"
		></script>
		<script
			src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"
			integrity="sha512-O/n4ZDho9IcdZ4hEteu9X9J6V7pJZ1zX0FyJw1lK+XqTBOF1oQ0p8UjUJ1e+A5BpZ8b6Hw0OypQ5p1eF5w5w=="
			crossorigin="anonymous"
		></script>
		<script
			src="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.js"
			integrity="sha512-9CWGXFSJ+9XWtKj6XJ5U5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5"
			crossorigin="anonymous"
		></script>
		<script
			src="https://cdnjs.cloudflare.com/ajax/libs/jquery.isotope/3.0.6/isotope.pkgd.min.js"
			integrity="sha512-Zq2BOxyhvnRFXu0+WE6ojpZLOU2jdn8B5PSnhXWlo/0J7J4pZ5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5"
			crossorigin="anonymous"
		></script>
		<script
			src="https://cdnjs.cloudflare.com/ajax/libs/jQuery.mmenu/8.5.24/mmenu.min.js"
			integrity="sha512-4b3Ff5i8+7w1t8V1+6O5k5q5k5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5"
			crossorigin="anonymous"
		></script>
		<script
			src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js"
			integrity="sha512-+Z6Y2rbB0PZt4q1PZ6yKlJkU8+2kYXFX5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5f5p5"
			crossorigin="anonymous"
		></script>
		<script
			src="https://cdnjs.cloudflare.com/ajax/libs/prettyPhoto/3.1.6/js/jquery.prettyPhoto.min.js"
			integrity="sha512-jm5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5"
			crossorigin="anonymous"
		></script>
		<script
			src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js"
			integrity="sha512-37b7h1f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5"
			crossorigin="anonymous"
		></script>
		<script
			src="https://cdnjs.cloudflare.com/ajax/libs/sticky-kit/1.1.3/sticky-kit.min.js"
			integrity="sha512-+1d4X+3Tt0X8f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5"
			crossorigin="anonymous"
		></script>
		<script type="text/javascript" src="assets/js/icheck.min.js"></script>
		<script
			src="https://cdnjs.cloudflare.com/ajax/libs/countdown/2.6.0/countdown.min.js"
			integrity="sha512-+2g3+6p5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5"
			crossorigin="anonymous"
		></script>
		<script
			src="https://cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/4.1.4/imagesloaded.pkgd.min.js"
			integrity="sha512-+2f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5"
			crossorigin="anonymous"
		></script>
		<script
			src="https://cdnjs.cloudflare.com/ajax/libs/jquery.touchSwipe/1.6.19/jquery.touchSwipe.min.js"
			integrity="sha512-9f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5"
			crossorigin="anonymous"
		></script>
		<script
			src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/additional-methods.min.js"
			integrity="sha512-+7f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5f5"
			crossorigin="anonymous"
		></script>
		<script type="text/javascript" src="assets/js/main.js"></script>

		<!-- Initialize carousel -->
		<script>
			$(document).ready(function () {
				// Initialize image carousel
				var $carousel = $(".image-carousel");
				var $slides = $(".image-carousel-slide");
				var $contentItems = $(".slider-content-item");
				var currentIndex = 0;
				var totalSlides = $slides.length;
				var slideInterval;

				// Show first slide
				function showSlide(index) {
					// Hide all slides and content items
					$slides.removeClass("active");
					$contentItems.removeClass("active");

					// Show current slide and content item
					$slides.eq(index).addClass("active");
					$contentItems.eq(index).addClass("active");

					// Update current index
					currentIndex = index;
				}

				// Next slide
				function nextSlide() {
					var newIndex = (currentIndex + 1) % totalSlides;
					showSlide(newIndex);
				}

				// Previous slide
				function prevSlide() {
					var newIndex = (currentIndex - 1 + totalSlides) % totalSlides;
					showSlide(newIndex);
				}

				// Start autoplay
				function startAutoplay() {
					stopAutoplay();
					slideInterval = setInterval(nextSlide, 5000);
				}

				// Stop autoplay
				function stopAutoplay() {
					clearInterval(slideInterval);
				}

				// Initialize carousel
				function initCarousel() {
					if (totalSlides > 0) {
						showSlide(0);
						startAutoplay();

						// Pause on hover
						$carousel.hover(
							function () {
								stopAutoplay();
							},
							function () {
								startAutoplay();
							}
						);

						// Navigation
						$(".slider-nav-next").on("click", function (e) {
							e.preventDefault();
							nextSlide();
							startAutoplay();
						});

						$(".slider-nav-prev").on("click", function (e) {
							e.preventDefault();
							prevSlide();
							startAutoplay();
						});

						// Keyboard navigation
						$(document).on("keydown", function (e) {
							switch (e.which) {
								case 37: // left arrow
									prevSlide();
									startAutoplay();
									break;
								case 39: // right arrow
									nextSlide();
									startAutoplay();
									break;
							}
						});
					}
				}

				// Initialize carousel
				initCarousel();

				// Form validation
				$("#slider-form").validate({
					rules: {
						name: "required",
						email: {
							required: true,
							email: true,
						},
						course: "required",
					},
					messages: {
						name: "Please enter your name",
						email: {
							required: "Please enter your email address",
							email: "Please enter a valid email address",
						},
						course: "Please select a course",
					},
					submitHandler: function (form) {
						// Form submission logic here
						alert("Thank you for your interest! We will contact you soon.");
						form.reset();
						return false;
					},
				});

				// Smooth scrolling for anchor links
				$('a[href*="#"]:not([href="#"])').on("click", function (e) {
					if (
						location.pathname.replace(/^\//, "") ==
							this.pathname.replace(/^\//, "") &&
						location.hostname == this.hostname
					) {
						var target = $(this.hash);
						target = target.length
							? target
							: $("[name=" + this.hash.slice(1) + "]");
						if (target.length) {
							e.preventDefault();
							$("html, body").animate(
								{
									scrollTop: target.offset().top - 100,
								},
								1000
							);
						}
					}
				});

				// Back to top button
				$(window).on("scroll", function () {
					if ($(this).scrollTop() > 100) {
						$(".back-to-top").fadeIn();
					} else {
						$(".back-to-top").fadeOut();
					}
				});

				$(".back-to-top").on("click", function (e) {
					e.preventDefault();
					$("html, body").animate({ scrollTop: 0 }, 1000);
				});
			});
		</script>
	</body>
</html>
