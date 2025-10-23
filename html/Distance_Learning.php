<?php
// Set the page title and description
$page_heading = "Distance Learning | Zanzibar University";
$page_description = "Flexible and accessible distance learning programs at Zanzibar University, designed for working professionals and remote students.";

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
                    <h1 class="page-title">Open Distance Learning Center (ODLC)</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Distance Learning</li>
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
                            <h2 class="card-title">Flexible Learning for Your Schedule</h2>
                            <div class="mb-4">
                                <img src="assets/img/distance-learning-banner.jpg" alt="Distance Learning" class="img-fluid rounded mb-3">
                            </div>
                            <p class="card-text">
                                Zanzibar University's Distance Learning programs provide quality education to students who require flexible study options. Our programs are designed to fit around your work and personal commitments while maintaining the same academic standards as our on-campus programs.
                            </p>
                            
                            <h3 class="mt-4 mb-3">Programs Offered</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Program</th>
                                            <th>Duration</n                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Bachelor of Business Administration (BBA)</td>
                                            <td>4 Years</td>
                                        </tr>
                                        <tr>
                                            <td>Bachelor of Education (B.Ed)</td>
                                            <td>3 Years</td>
                                        </tr>
                                        <tr>
                                            <td>Master of Business Administration (MBA)</td>
                                            <td>2 Years</td>
                                        </tr>
                                        <tr>
                                            <td>Postgraduate Diploma in Education</td>
                                            <td>1 Year</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <h3 class="mt-4 mb-3">How It Works</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="service-item mb-4">
                                        <h4><i class="fas fa-laptop text-primary me-2"></i>Online Learning</h4>
                                        <p>Access course materials, submit assignments, and participate in discussions through our learning management system.</p>
                                    </div>
                                    <div class="service-item mb-4">
                                        <h4><i class="fas fa-book-reader text-primary me-2"></i>Study Materials</h4>
                                        <p>Comprehensive study materials designed specifically for distance learners.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="service-item mb-4">
                                        <h4><i class="fas fa-users text-primary me-2"></i>Tutor Support</h4>
                                        <p>Dedicated tutors available for academic support and guidance.</p>
                                    </div>
                                    <div class="service-item mb-4">
                                        <h4><i class="fas fa-calendar-check text-primary me-2"></i>Examinations</h4>
                                        <p>Scheduled examinations at designated centers with flexible options.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="sidebar-widget mb-4">
                        <h3 class="widget-title">Quick Links</h3>
                        <div class="list-group">
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-laptop-house me-2"></i> Learning Portal
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-calendar-alt me-2"></i> Academic Calendar
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-question-circle me-2"></i> Student Support
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-file-alt me-2"></i> Application Forms
                            </a>
                        </div>
                    </div>

                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h4 class="card-title">Contact Distance Learning</h4>
                            <address>
                                <strong>Distance Learning Department</strong><br>
                                Zanzibar University<br>
                                P.O. Box 2440<br>
                                Zanzibar, Tanzania<br>
                                <i class="fas fa-phone me-2"></i> +255 24 223 0724<br>
                                <i class="fas fa-envelope me-2"></i> distancelearning@zanvarsity.ac.tz
                            </address>
                            <p class="mb-0"><strong>Office Hours:</strong><br>
                            Monday - Friday: 8:00 AM - 4:00 PM</p>
                        </div>
                    </div>

                    <div class="card bg-primary text-white mb-4">
                        <div class="card-body">
                            <h4 class="card-title">Ready to Apply?</h4>
                            <p>Start your distance learning journey today. Our admissions team is here to help you with the application process.</p>
                            <a href="#" class="btn btn-light">Apply Now</a>
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
