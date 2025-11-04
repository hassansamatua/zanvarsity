<?php
// Set the page title and description
$page_heading = "Quality Assurance | Zanzibar University";
$page_description = "Ensuring academic excellence through comprehensive quality assurance processes at Zanzibar University.";

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
                    <h1 class="page-title">Quality Assurance</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Quality Assurance</li>
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
                            <h2 class="card-title">Ensuring Academic Excellence</h2>
                            <div class="mb-4">
                                <img src="assets/img/quality-assurance-banner.jpg" alt="Quality Assurance" class="img-fluid rounded mb-3">
                            </div>
                            <p class="card-text">
                                The Quality Assurance Directorate at Zanzibar University is committed to maintaining and enhancing the quality of all academic programs and administrative services through systematic evaluation and continuous improvement processes.
                            </p>
                            
                            <h3 class="mt-4 mb-3">Our Key Functions</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="service-item mb-4">
                                        <h4><i class="fas fa-clipboard-check text-primary me-2"></i>Quality Audits</h4>
                                        <p>Regular assessment of academic and administrative units to ensure compliance with quality standards.</p>
                                    </div>
                                    <div class="service-item mb-4">
                                        <h4><i class="fas fa-chart-line text-primary me-2"></i>Performance Monitoring</h4>
                                        <p>Tracking and analyzing key performance indicators across all university functions.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="service-item mb-4">
                                        <h4><i class="fas fa-graduation-cap text-primary me-2"></i>Program Accreditation</h4>
                                        <p>Overseeing the accreditation process for all academic programs.</p>
                                    </div>
                                    <div class="service-item mb-4">
                                        <h4><i class="fas fa-users text-primary me-2"></i>Stakeholder Feedback</h4>
                                        <p>Collecting and analyzing feedback from students, staff, and employers.</p>
                                    </div>
                                </div>
                            </div>

                            <h3 class="mt-4 mb-3">Quality Policy</h3>
                            <div class="alert alert-light">
                                <p>Zanzibar University is committed to providing quality education and services that meet national and international standards through continuous improvement, innovation, and stakeholder engagement.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="sidebar-widget mb-4">
                        <h3 class="widget-title">Quick Links</h3>
                        <div class="list-group">
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-file-alt me-2"></i> Quality Manual
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-chart-pie me-2"></i> QA Reports
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-clipboard-list me-2"></i> Self-Assessment
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-download me-2"></i> QA Forms
                            </a>
                        </div>
                    </div>

                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h4 class="card-title">Contact Quality Assurance</h4>
                            <address>
                                <strong>Quality Assurance Directorate</strong><br>
                                Zanzibar University<br>
                                P.O. Box 2440<br>
                                Zanzibar, Tanzania<br>
                                <i class="fas fa-phone me-2"></i> +255 24 223 0724<br>
                                <i class="fas fa-envelope me-2"></i> qa@zanvarsity.ac.tz
                            </address>
                            <p class="mb-0"><strong>Office Hours:</strong><br>
                            Monday - Friday: 8:00 AM - 4:00 PM</p>
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
