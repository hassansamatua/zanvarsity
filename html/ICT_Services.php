<?php
// Set the page title and description
$page_heading = "ICT Services | Zanzibar University";
$page_description = "Access comprehensive ICT services at Zanzibar University, including computer labs, network access, and technical support.";

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
                    <h1 class="page-title">ICT Services</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">ICT Services</li>
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
                            <h2 class="card-title">Information and Communication Technology</h2>
                            <div class="mb-4">
                                <img src="assets/img/ict-banner.jpg" alt="ICT Services" class="img-fluid rounded mb-3">
                            </div>
                            <p class="card-text">
                                The ICT Department at Zanzibar University provides cutting-edge technology solutions to support the university's academic and administrative functions.
                            </p>
                            
                            <h3 class="mt-4 mb-3">Our Services</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="service-item mb-4">
                                        <h4><i class="fas fa-network-wired text-primary me-2"></i>Network Services</h4>
                                        <p>High-speed internet access across campus with secure Wi-Fi connectivity.</p>
                                    </div>
                                    <div class="service-item mb-4">
                                        <h4><i class="fas fa-laptop-house text-primary me-2"></i>Computer Labs</h4>
                                        <p>Well-equipped computer labs with the latest software and hardware.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="service-item mb-4">
                                        <h4><i class="fas fa-headset text-primary me-2"></i>Technical Support</h4>
                                        <p>24/7 helpdesk support for all ICT-related issues.</p>
                                    </div>
                                    <div class="service-item mb-4">
                                        <h4><i class="fas fa-graduation-cap text-primary me-2"></i>E-Learning</h4>
                                        <p>Support for online learning platforms and digital education tools.</p>
                                    </div>
                                </div>
                            </div>

                            <h3 class="mt-4 mb-3">Service Hours</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Service</th>
                                            <th>Availability</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Helpdesk Support</td>
                                            <td>24/7</td>
                                        </tr>
                                        <tr>
                                            <td>Computer Labs</td>
                                            <td>7:00 AM - 10:00 PM (Daily)</td>
                                        </tr>
                                        <tr>
                                            <td>Technical Support Office</td>
                                            <td>8:00 AM - 5:00 PM (Mon-Fri)</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="sidebar-widget mb-4">
                        <h3 class="widget-title">Quick Links</h3>
                        <div class="list-group">
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-wifi me-2"></i> Wifi Access
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-desktop me-2"></i> Software Downloads
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-question-circle me-2"></i> Helpdesk
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-book me-2"></i> ICT Policies
                            </a>
                        </div>
                    </div>

                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h4 class="card-title">Contact ICT Department</h4>
                            <address>
                                <strong>ICT Department</strong><br>
                                Zanzibar University<br>
                                P.O. Box 2440<br>
                                Zanzibar, Tanzania<br>
                                <i class="fas fa-phone me-2"></i> +255 24 223 0724<br>
                                <i class="fas fa-envelope me-2"></i> ict@zanvarsity.ac.tz
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
