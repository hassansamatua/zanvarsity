<?php
// Include database connection
require_once __DIR__ . '/../includes/db_connect.php';

// Initialize carousel items array
$carouselItems = [];

// Get active carousel items
try {
    $carouselQuery = "SELECT id, title, description, image_url as image_path, button_text, button_url 
                     FROM carousel 
                     WHERE is_active = 1 
                     ORDER BY sort_order ASC";
    $carouselResult = $conn->query($carouselQuery);
    
    if ($carouselResult && $carouselResult->num_rows > 0) {
        while ($row = $carouselResult->fetch_assoc()) {
            $carouselItems[] = $row;
        }
    }
} catch (Exception $e) {
    // Log error and continue with empty carousel
    error_log('Carousel error: ' . $e->getMessage());
}
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
        <link rel="stylesheet" href="assets/css/selectize.css" type="text/css" />
        <link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css" />
        <link
            rel="stylesheet"
            href="assets/css/vanillabox/vanillabox.css"
            type="text/css"
        />
        <link rel="stylesheet" href="assets/css/style.css" type="text/css" />

        <title>ZANVarsity - Home</title>
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
                                <input
                                    type="search"
                                    class="form-control"
                                    name="search"
                                    placeholder="Search"
                                />
                                <span class="input-group-btn">
                                    <button type="submit" id="search-submit" class="btn">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                            <!-- /.input-group -->
                        </div>
                        <ul class="secondary-navigation list-unstyled">
                            <li><a href="#">Zumis Portal</a></li>
                            <li><a href="#">Prospectus</a></li>
                            <li><a href="#">Almanac</a></li>
                            <li><a href="#">Fee Structure</a></li>
                            <li><a href="#">Alumni</a></li>
                            <li><a href="register-sign-in.html"><i class="fa fa-sign-in"></i> Admin Login</a></li>
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
                                    <a href="index.php">
                                        <img src="assets/img/logo.png" alt="ZANVarsity" />
                                    </a>
                                </div>
                            </div>
                            <nav
                                class="collapse navbar-collapse bs-navbar-collapse navbar-right"
                                role="navigation"
                            >
                                <ul class="nav navbar-nav">
                                    <li class="active">
                                        <a href="#">Home</a>
                                    </li>
                                    <li>
                                        <a href="#">About</a>
                                    </li>
                                    <li>
                                        <a href="#">Admission</a>
                                    </li>
                                    <li>
                                        <a href="#">Academics</a>
                                    </li>
                                    <li>
                                        <a href="#">Directorates</a>
                                    </li>
                                    <li>
                                        <a href="#">Facilities</a>
                                    </li>
                                    <li>
                                        <a href="contact-us.php">Contact Us</a>
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
            <!-- Page Content -->
            <div id="page-content">
                <!-- Slider -->
                <div id="homepage-carousel">
                    <div class="container">
                        <div class="homepage-carousel-wrapper">
                            <div class="row">
                                <div class="col-md-6 col-sm-7">
                                    <div class="image-carousel">
                                        <?php if (!empty($carouselItems)): ?>
                                            <?php foreach ($carouselItems as $index => $item): ?>
                                                <?php 
                                                // Fix image path
                                                $imagePath = str_replace('/zanvarsity/html', '', $item['image_path']);
                                                $imagePath = ltrim($imagePath, '/');
                                                ?>
                                                <div class="image-carousel-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                                                    <img src="/<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" />
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <!-- Fallback in case no carousel items are found -->
                                            <div class="image-carousel-slide active">
                                                <img src="assets/img/slide-1.jpg" alt="Default Slide 1" />
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <!-- /.slider-image -->
                                </div>
                                <!-- /.col-md-6 -->
                                <div class="col-md-6 col-sm-5">
                                    <div class="slider-content">
                                        <?php if (!empty($carouselItems)): ?>
                                            <?php foreach ($carouselItems as $index => $item): ?>
                                                <div class="slider-content-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                                    <h1><?php echo htmlspecialchars($item['title']); ?></h1>
                                                    <?php if (!empty($item['description'])): ?>
                                                        <p class="lead"><?php echo htmlspecialchars($item['description']); ?></p>
                                                    <?php endif; ?>
                                                    <?php if (!empty($item['button_text']) && !empty($item['button_url'])): ?>
                                                        <div class="slider-buttons" style="margin-top: 20px;">
                                                            <a href="<?php echo htmlspecialchars($item['button_url']); ?>" class="btn btn-framed">
                                                                <?php echo htmlspecialchars($item['button_text']); ?>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="slider-content-item active">
                                                <h1>Welcome to ZANVarsity</h1>
                                                <p class="lead">Experience quality education with our expert faculty.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <!-- /.slider-content -->
                                </div>
                                <!-- /.col-md-6 -->
                            </div>
                            <!-- /.row -->
                            <?php if (count($carouselItems) > 1): ?>
                            <div class="slider-navigation">
                                <div class="slider-nav">
                                    <span class="slider-nav-prev"><i class="fa fa-chevron-left"></i></span>
                                    <span class="slider-nav-next"><i class="fa fa-chevron-right"></i></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="background"></div>
                        </div>
                        <!-- /.slider-wrapper -->
                        <div class="slider-inner"></div>
                    </div>
                    <!-- /.container -->
                </div>
                <!-- end Slider -->

                <!-- Rest of your content remains the same -->
                <!-- ... -->

            </div>
            <!-- end Page Content -->

            <!-- Footer -->
            <footer id="page-footer">
                <div class="footer-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="footer-section">
                                    <h4>About ZANVarsity</h4>
                                    <p>ZANVarsity is a premier institution dedicated to providing quality education and fostering academic excellence.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="footer-section">
                                    <h4>Quick Links</h4>
                                    <ul class="list-links">
                                        <li><a href="#">Home</a></li>
                                        <li><a href="#">About Us</a></li>
                                        <li><a href="#">Academics</a></li>
                                        <li><a href="#">Admissions</a></li>
                                        <li><a href="#">Contact</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="footer-section">
                                    <h4>Contact Us</h4>
                                    <address>
                                        <p><i class="fa fa-map-marker"></i> 123 University Ave, City, Country</p>
                                        <p><i class="fa fa-phone"></i> +255 123 456 789</p>
                                        <p><i class="fa fa-envelope"></i> info@zanvarsity.ac.tz</p>
                                    </address>
                                </div>
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.container -->
                </div>
                <!-- /.footer-body -->
                <div class="copyright">
                    <div class="container">
                        <p class="text-center">© <?php echo date('Y'); ?> ZANVarsity. All rights reserved.</p>
                    </div>
                </div>
                <!-- /.copyright -->
            </footer>
            <!-- end Footer -->
        </div>
        <!-- end Wrapper -->

        <!-- JavaScripts -->
        <script src="assets/js/jquery-2.1.0.min.js"></script>
        <script src="assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="assets/js/selectize.min.js"></script>
        <script src="assets/js/owl.carousel.min.js"></script>
        <script src="assets/js/jquery.placeholder.js"></script>
        <script src="assets/js/jQuery.equalHeights.js"></script>
        <script src="assets/js/custom.js"></script>
        
        <!-- Carousel Script -->
        <script type="text/javascript">
        $(document).ready(function() {
            // Initialize carousel
            let currentSlide = 0;
            const slides = $('.image-carousel-slide');
            const contentItems = $('.slider-content-item');
            const totalSlides = slides.length;
            
            // Function to show slide
            function showSlide(index) {
                // Hide all slides and content items
                slides.removeClass('active');
                contentItems.removeClass('active');
                
                // Show current slide and content
                $(slides[index]).addClass('active');
                $(contentItems[index]).addClass('active');
                currentSlide = index;
            }
            
            // Next slide
            $('.slider-nav-next').on('click', function() {
                const nextSlide = (currentSlide + 1) % totalSlides;
                showSlide(nextSlide);
            });
            
            // Previous slide
            $('.slider-nav-prev').on('click', function() {
                const prevSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                showSlide(prevSlide);
            });
            
            // Auto-advance slides every 5 seconds
            let slideInterval = setInterval(function() {
                if (totalSlides > 1) {
                    const nextSlide = (currentSlide + 1) % totalSlides;
                    showSlide(nextSlide);
                }
            }, 5000);
            
            // Pause on hover
            $('.image-carousel, .slider-content').hover(
                function() {
                    clearInterval(slideInterval);
                },
                function() {
                    slideInterval = setInterval(function() {
                        if (totalSlides > 1) {
                            const nextSlide = (currentSlide + 1) % totalSlides;
                            showSlide(nextSlide);
                        }
                    }, 5000);
                }
            );
        });
        </script>
    </body>
</html>
