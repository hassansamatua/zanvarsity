<?php
$page_title = "Facility Name | Zanvarsity";
$page_description = "Description of the facility at Zanvarsity";
include 'includes/header.php';
?>

<!-- Page Content -->
<div id="page-content">
    <!-- Breadcrumb -->
    <div class="container">
        <header><h1>Facility Name</h1></header>
        <div class="breadcrumb">
            <a href="index.php">Home</a> <span class="divider">/</span>
            <a href="#">Facilities</a> <span class="divider">/</span>
            <span class="last">Facility Name</span>
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
                            <img src="assets/img/facility-placeholder.jpg" alt="Facility Image">
                        </div>
                    </figure>
                    <div class="description">
                        <h3>About the Facility</h3>
                        <p>Detailed description of the facility will go here. This section can include information about the services offered, capacity, operating hours, and any other relevant details.</p>
                        
                        <h3>Features</h3>
                        <ul class="list-links">
                            <li>Feature 1</li>
                            <li>Feature 2</li>
                            <li>Feature 3</li>
                            <li>Feature 4</li>
                        </ul>
                        
                        <h3>Operating Hours</h3>
                        <p>Monday - Friday: 8:00 AM - 10:00 PM<br>
                        Saturday: 9:00 AM - 6:00 PM<br>
                        Sunday: Closed</p>
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
                            <li><a href="#">Booking Information</a></li>
                            <li><a href="#">Rules & Regulations</a></li>
                            <li><a href="#">Photo Gallery</a></li>
                            <li><a href="#">Contact Facility Manager</a></li>
                        </ul>
                    </section>
                    
                    <section>
                        <h2>Other Facilities</h2>
                        <ul class="list-links">
                            <li><a href="library.php">University Library</a></li>
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

<?php include 'includes/footer.php'; ?>
