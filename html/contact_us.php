<?php
// Set the page title and description
$page_heading = "Contact Us | Zanzibar University";
$page_description = "Get in touch with Zanzibar University. Find our contact information, location, and office hours.";

// Set active class for contact in navigation
$contact_active = 'active';

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
                    <h1 class="page-title">Contact Us</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="contact-info py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h2 class="card-title mb-4">Get In Touch</h2>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="contact-method">
                                        <div class="contact-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                            <i class="fas fa-map-marker-alt fa-2x"></i>
                                        </div>
                                        <h4>Our Location</h4>
                                        <address>
                                            Zanzibar University<br>
                                            P.O. Box 2440<br>
                                            Zanzibar, Tanzania
                                        </address>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="contact-method">
                                        <div class="contact-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                            <i class="fas fa-phone-alt fa-2x"></i>
                                        </div>
                                        <h4>Phone & Email</h4>
                                        <p>
                                            <i class="fas fa-phone me-2"></i> +255 24 223 0724<br>
                                            <i class="fas fa-fax me-2"></i> +255 24 223 3337<br>
                                            <i class="fas fa-envelope me-2"></i> info@zanvarsity.ac.tz
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="contact-method">
                                        <div class="contact-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                            <i class="far fa-clock fa-2x"></i>
                                        </div>
                                        <h4>Working Hours</h4>
                                        <p>
                                            <strong>Monday - Friday:</strong> 8:00 AM - 5:00 PM<br>
                                            <strong>Saturday:</strong> 9:00 AM - 1:00 PM<br>
                                            <strong>Sunday:</strong> Closed
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="contact-method">
                                        <div class="contact-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                            <i class="fas fa-map-marked-alt fa-2x"></i>
                                        </div>
                                        <h4>How to Find Us</h4>
                                        <p>
                                            Located in Tunguu area, about 10km from Zanzibar Town. Public transport and taxis are available from the city center.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="card-title mb-4">Send Us a Message</h3>
                            <form id="contactForm" action="#" method="POST">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Your Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject</label>
                                    <input type="text" class="form-control" id="subject" name="subject">
                                </div>
                                <div class="mb-3">
                                    <label for="message" class="form-label">Your Message <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Send Message</button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card bg-light">
                        <div class="card-body">
                            <h4 class="card-title">Emergency Contacts</h4>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-phone-alt text-danger me-2"></i> <strong>Security:</strong> +255 777 123 456</li>
                                <li class="mb-2"><i class="fas fa-ambulance text-danger me-2"></i> <strong>Medical Emergency:</strong> 112</li>
                                <li><i class="fas fa-shield-alt text-danger me-2"></i> <strong>Campus Safety:</strong> +255 777 789 012</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Map Section -->
    <section class="map-section py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title text-center mb-5">Our Location</h2>
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3979.818069321355!2d39.25821031532131!3d-6.20255086271231!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMTInMDkuMiJTIDM5wrAxNSczMy4xIkU!5e0!3m2!1sen!2stz!4v1620000000000!5m2!1sen!2stz" 
                                style="border:0; width: 100%; height: 100%;" 
                                allowfullscreen="" 
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                title="Zanzibar University Location - Tunguu Campus">
                        </iframe>
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
