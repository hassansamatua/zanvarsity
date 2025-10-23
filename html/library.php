<?php
$page_title = "University Library | Zanvarsity";
$page_description = "Explore our extensive collection of books, journals, and digital resources at Zanvarsity's University Library.";

// Function to get a random image from Unsplash
function getUnsplashImage($query, $width = 1200, $height = 800) {
    $access_key = 'YOUR_UNSPLASH_ACCESS_KEY'; // Replace with your Unsplash Access Key
    $url = "https://api.unsplash.com/photos/random?query=" . urlencode($query) . "&client_id=" . $access_key . "&w=$width&h=$height&fit=crop";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($result, true);
    return $data['urls']['regular'] ?? 'path/to/default-image.jpg';
}

// Get library-related images
$libraryMainImage = getUnsplashImage('university library interior', 1200, 800);
$readingAreaImage = getUnsplashImage('modern library reading area', 800, 600);
$digitalResourcesImage = getUnsplashImage('digital library resources', 800, 600);

include 'includes/about_header.php';
?>

<!-- Page Content -->
<div id="page-content">
    <!-- Breadcrumb -->
    <div class="container">
        <header><h1>University Library</h1></header>
        <div class="breadcrumb">
            <a href="index.php">Home</a> <span class="divider">/</span>
            <a href="#">Facilities</a> <span class="divider">/</span>
            <span class="last">University Library</span>
        </div>
    </div>
    <!-- end Breadcrumb -->

    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-md-8">
                <article class="blog-listing-detail">
                    <figure class="image">
                        <div class="image-wrapper">
                            <img src="<?php echo $libraryMainImage; ?>" alt="Modern University Library at Zanvarsity" class="img-responsive">
                        </div>
                    </figure>
                    <div class="description">
                        <h3>About the University Library</h3>
                        <p>The Zanvarsity University Library is a modern facility designed to support the academic and research needs of our students and faculty. With a vast collection of books, journals, and digital resources, we provide a conducive environment for learning and research.</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h3>Our Facilities</h3>
                                <ul class="list-links">
                                    <li><i class="fa fa-book"></i> 100,000+ academic books and reference materials</li>
                                    <li><i class="fa fa-laptop"></i> 50+ computer workstations with high-speed internet</li>
                                    <li><i class="fa fa-users"></i> 10+ group study rooms with modern facilities</li>
                                    <li><i class="fa fa-print"></i> Full-service printing, scanning, and photocopying</li>
                                    <li><i class="fa fa-headphones"></i> Multimedia resources and audio-visual equipment</li>
                                    <li><i class="fa fa-wifi"></i> Campus-wide WiFi access with eduroam support</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <div class="thumbnail">
                                    <img src="<?php echo $readingAreaImage; ?>" alt="Comfortable reading area" class="img-responsive">
                                    <div class="caption">
                                        <p class="text-center">Our comfortable reading areas provide the perfect study environment</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row" style="margin-top: 30px;">
                            <div class="col-md-6">
                                <div class="thumbnail">
                                    <img src="<?php echo $digitalResourcesImage; ?>" alt="Digital resources" class="img-responsive">
                                    <div class="caption">
                                        <p class="text-center">Access to extensive digital resources and online databases</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h3>Digital Resources</h3>
                                <ul class="list-links">
                                    <li><i class="fa fa-database"></i> 50,000+ e-books and e-journals</li>
                                    <li><i class="fa fa-newspaper-o"></i> Access to major academic databases</li>
                                    <li><i class="fa fa-graduation-cap"></i> Online research tools and citation managers</li>
                                    <li><i class="fa fa-video-camera"></i> Video tutorials and research guides</li>
                                    <li><i class="fa fa-mobile"></i> Mobile app for library services</li>
                                    <li><i class="fa fa-clock-o"></i> 24/7 access to digital resources</li>
                                </ul>
                            </div>
                        </div>
                        
                        <h3>Operating Hours</h3>
                        <p>Monday - Friday: 8:00 AM - 10:00 PM<br>
                        Saturday: 9:00 AM - 6:00 PM<br>
                        Sunday: 1:00 PM - 6:00 PM</p>
                        
                        <h3>Contact Information</h3>
                        <p>Email: library@zanvarsity.ac.tz<br>
                        Phone: +255 22 215 0123<br>
                        Location: Main Campus, Administration Block, 1st Floor</p>
                    </div>
                </article>
            </div>
            <!-- end Main Content -->
            
            <!-- Sidebar -->
            <div class="col-md-4">
                <aside class="sidebar">
                    <section>
                        <h2>Quick Links</h2>
                        <ul class="list-links">
                            <li><a href="#">Library Catalog</a></li>
                            <li><a href="#">Online Resources</a></li>
                            <li><a href="#">E-Journals</a></li>
                            <li><a href="#">Library Services</a></li>
                            <li><a href="#">Library Rules</a></li>
                        </ul>
                    </section>
                    
                    <section>
                        <h2>Other Facilities</h2>
                        <ul class="list-links">
                            <li><a href="labs.php">Computer Labs</a></li>
                            <li><a href="sports.php">Sports Facilities</a></li>
                            <li><a href="hostels.php">Student Hostels</a></li>
                            <li><a href="cafeteria.php">Cafeteria</a></li>
                        </ul>
                    </section>
                </aside>
            </div>
            <!-- end Sidebar -->
        </div>
    </div>
</div>
<!-- end Page Content -->

<!-- Add some custom styles for the library page -->
<style>
    .list-links li {
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }
    .list-links li i {
        color: #006633;
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }
    .thumbnail {
        border: 1px solid #eee;
        padding: 10px;
        border-radius: 4px;
        transition: all 0.3s ease;
    }
    .thumbnail:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .thumbnail img {
        border-radius: 4px;
    }
    .thumbnail .caption {
        padding: 10px 0 0;
        color: #666;
    }
</style>

<?php include 'includes/about_footer.php'; ?>
