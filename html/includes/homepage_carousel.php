<?php
// Include database connection
require_once __DIR__ . '/../../includes/db_connect.php';

// Function to get active carousel items
function getCarouselItems($conn) {
    $query = "SELECT * FROM carousel WHERE status = 'active' ORDER BY display_order ASC";
    $result = $conn->query($query);
    
    $items = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    }
    return $items;
}

// Get database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get carousel items
$carouselItems = getCarouselItems($conn);

// Close connection
$conn->close();
?>

<!-- Slider -->
<div id="homepage-carousel">
    <div class="container">
        <div class="homepage-carousel-wrapper">
            <div class="row">
                <div class="col-md-6 col-sm-7">
                    <div class="image-carousel">
                        <?php if (!empty($carouselItems)): ?>
                            <?php foreach ($carouselItems as $index => $item): ?>
                                <div class="image-carousel-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <img src="<?php echo htmlspecialchars($item['image_path']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['title'] ?? ''); ?>" />
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
                                    <h1><?php echo htmlspecialchars($item['title'] ?? 'Welcome to ZANVarsity'); ?></h1>
                                    <?php if (!empty($item['description'])): ?>
                                        <p class="lead"><?php echo htmlspecialchars($item['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="slider-content-item active">
                                <h1>Join the community of modern thinking students</h1>
                                <p class="lead">Experience quality education with our expert faculty.</p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Form section remains unchanged -->
                        <form id="slider-form" role="form" action="" method="post">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <select name="slider-study-level" id="slider-study-level" class="has-dark-background">
                                            <option value="- Not selected -">Study Level</option>
                                            <option value="Beginner">Beginner</option>
                                            <option value="Advanced">Advanced</option>
                                            <option value="Intermediate">Intermediate</option>
                                            <option value="Professional">Professional</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- /.col-md-6 -->
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-framed pull-right" id="slider-apply">
                                        Apply now
                                    </button>
                                </div>
                                <!-- /.col-md-6 -->
                            </div>
                            <!-- /.row -->
                        </form>
                    </div>
                    <!-- /.slider-content -->
                </div>
                <!-- /.col-md-6 -->
            </div>
            <!-- /.row -->
            
            <!-- Carousel Controls -->
            <?php if (count($carouselItems) > 1): ?>
                <div class="slider-navigation">
                    <div class="slider-nav">
                        <span class="slider-nav-prev"><i class="fa fa-chevron-left"></i></span>
                        <span class="slider-nav-next"><i class="fa fa-chevron-right"></i></span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <!-- /.homepage-carousel-wrapper -->
    </div>
    <!-- /.container -->
</div>
<!-- /#homepage-carousel -->

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
