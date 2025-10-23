<?php
// Set the page title and description
$page_heading = "Student Services | Zanzibar University";
$page_description = "Comprehensive student support services at Zanzibar University including counseling, career guidance, and student welfare.";

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
                    <h1 class="page-title">Student Services</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Student Services</li>
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
                            <h2 class="card-title">Supporting Your Academic Journey</h2>
                            <div class="mb-4">
                                <img src="assets/img/student-services-banner.jpg" alt="Student Services" class="img-fluid rounded mb-3">
                            </div>
                            <p class="card-text">
                                At Zanzibar University, we are committed to providing comprehensive support services to ensure your academic success and personal development throughout your university journey.
                            </p>
                            
                            <h3 class="mt-4 mb-3">Our Services</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="service-item mb-4">
                                        <h4><i class="fas fa-graduation-cap text-primary me-2"></i>Academic Advising</h4>
                                        <p>Personalized guidance on course selection, academic planning, and degree requirements.</p>
                                    </div>
                                    <div class="service-item mb-4">
                                        <h4><i class="fas fa-heart text-primary me-2"></i>Counseling Services</h4>
                                        <p>Professional counseling for personal, academic, and career-related concerns.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="service-item mb-4">
                                        <h4><i class="fas fa-briefcase text-primary me-2"></i>Career Services</h4>
                                        <p>Career counseling, job search assistance, and internship opportunities.</p>
                                    </div>
                                    <div class="service-item mb-4">
                                        <h4><i class="fas fa-hand-holding-heart text-primary me-2"></i>Student Welfare</h4>
                                        <p>Support services for health, accommodation, and general well-being.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info mt-4">
                                <h4><i class="fas fa-info-circle me-2"></i>Important Notice</h4>
                                <p class="mb-0">All services are available to currently enrolled students. Please bring your student ID when accessing these services.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="sidebar-widget mb-4">
                        <h3 class="widget-title">Quick Links</h3>
                        <div class="list-group">
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-calendar-alt me-2"></i> Academic Calendar
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-file-alt me-2"></i> Forms & Documents
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-question-circle me-2"></i> FAQs
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-phone-alt me-2"></i> Emergency Contacts
                            </a>
                        </div>
                    </div>

                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h4 class="card-title">Contact Student Services</h4>
                            <address>
                                <strong>Student Affairs Office</strong><br>
                                Zanzibar University<br>
                                P.O. Box 2440<br>
                                Zanzibar, Tanzania<br>
                                <i class="fas fa-phone me-2"></i> +255 24 223 0724<br>
                                <i class="fas fa-envelope me-2"></i> studentservices@zanvarsity.ac.tz
                            </address>
                            <p class="mb-0"><strong>Office Hours:</strong><br>
                            Monday - Friday: 8:00 AM - 5:00 PM</p>
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
