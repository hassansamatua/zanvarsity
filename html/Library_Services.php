<?php
// Set the page title and description
$page_heading = "Library Services | Zanzibar University";
$page_description = "Explore the comprehensive library services at Zanzibar University, offering vast resources, digital collections, and research support.";

// Include the header
include __DIR__ . '/includes/about_header.php';
?>

<!-- Page Content -->
<div class="page-content">
    <!-- Page Header -->
    <section class="page-header" style="background-color: #f8f9fa; padding: 60px 0 30px; margin-bottom: 40px; border-bottom: 1px solid #eaeaea;">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="page-title">Library Services</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Library Services</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="main-content py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h2 class="card-title">Welcome to Zanzibar University Library</h2>
                            <div class="mb-4">
                                <img src="assets/img/library-banner.jpg" alt="Zanzibar University Library" class="img-fluid rounded mb-3">
                            </div>
                            <p class="card-text">
                                The Zanzibar University Library is a central hub for academic resources, providing students and faculty with access to a wide range of materials to support teaching, learning, and research activities.
                            </p>
                            
                            <h3 class="mt-4 mb-3">Our Services</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="service-item mb-4">
                                        <h4><i class="fas fa-book text-primary me-2"></i>Book Lending</h4>
                                        <p>Borrow books from our extensive collection of academic texts, reference materials, and periodicals.</p>
                                    </div>
                                    <div class="service-item mb-4">
                                        <h4><i class="fas fa-laptop-code text-primary me-2"></i>Digital Resources</h4>
                                        <p>Access thousands of e-books, e-journals, and online databases from anywhere on campus.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="service-item mb-4">
                                        <h4><i class="fas fa-search text-primary me-2"></i>Research Support</h4>
                                        <p>Get assistance with research, citations, and accessing specialized academic resources.</p>
                                    </div>
                                    <div class="service-item mb-4">
                                        <h4><i class="fas fa-users text-primary me-2"></i>Study Spaces</h4>
                                        <p>Comfortable and quiet study areas for individual and group study sessions.</p>
                                    </div>
                                </div>
                            </div>

                            <h3 class="mt-4 mb-3">Library Hours</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Day</th>
                                            <th>Opening Hours</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Monday - Friday</td>
                                            <td>8:00 AM - 8:00 PM</td>
                                        </tr>
                                        <tr>
                                            <td>Saturday</td>
                                            <td>9:00 AM - 4:00 PM</td>
                                        </tr>
                                        <tr>
                                            <td>Sunday</td>
                                            <td>Closed</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="alert alert-info mt-4">
                                <h4><i class="fas fa-info-circle me-2"></i>Notice</h4>
                                <p class="mb-0">Special holiday hours may apply. Please check our notice board for updates.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="sidebar-widget mb-4">
                        <h3 class="widget-title">Quick Links</h3>
                        <div class="list-group">
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-book-reader me-2"></i> Library Catalog
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-database me-2"></i> Online Databases
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-question-circle me-2"></i> Ask a Librarian
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-file-alt me-2"></i> Research Guides
                            </a>
                        </div>
                    </div>

                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h4 class="card-title">Contact Us</h4>
                            <address>
                                <strong>Zanzibar University Library</strong><br>
                                P.O. Box 2440<br>
                                Zanzibar, Tanzania<br>
                                <i class="fas fa-phone me-2"></i> +255 24 223 0724<br>
                                <i class="fas fa-envelope me-2"></i> library@zanvarsity.ac.tz
                            </address>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
// Include the footer
include __DIR__ . '/includes/about_footer.php';
?>
