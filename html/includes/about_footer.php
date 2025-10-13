<?php
/**
 * About Footer Template
 * 
 * This is a reusable footer template for about-related pages.
 * It includes the site footer content and closing HTML tags.
 */

// Close the page content div if it was opened in the header
if (!isset($hide_page_content_div) || $hide_page_content_div !== true) {
    echo '        </div><!-- /#page-content -->';
}
?>

<!-- Footer -->
<footer id="page-footer" style="width: 100%; margin: 0; padding: 0;">
    <section id="footer-top" style="width: 100%;">
        <div class="container-fluid" style="width: 100%; max-width: 100%; padding: 0;">
            <div class="row" style="margin: 0;">
            <div class="footer-inner">
                <div class="footer-social">
                    <figure style="color: #004225; font-weight: bold; margin-bottom: 10px;">Follow us:</figure>
                    <div class="icons" style="display: inline-flex; gap: 20px;">
                        <a href="#" style="color: #000000; text-decoration: none; transition: color 0.3s ease; font-size: 16px; font-weight: 500;"><i class="fa fa-twitter" style="color: #004225; margin-right: 5px;"></i></a>
                        <a href="#" style="color: #000000; text-decoration: none; transition: color 0.3s ease; font-size: 16px; font-weight: 500;"><i class="fa fa-facebook" style="color: #004225; margin-right: 5px;"></i></a>
                        <a href="#" style="color: #000000; text-decoration: none; transition: color 0.3s ease; font-size: 16px; font-weight: 500;"><i class="fa fa-pinterest" style="color: #004225; margin-right: 5px;"></i></a>
                        <a href="#" style="color: blue; text-decoration: none; transition: color 0.3s ease; font-size: 16px; font-weight: 500;"><i class="fa fa-youtube-play" style="color: #004225; margin-right: 5px;"></i></a>
</div>
                </div>
                <div class="search pull-right">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search" style="border: 1px solid #004225; border-radius: 4px 0 0 4px; height: 40px; padding: 6px 12px;" />
                        <span class="input-group-btn">
                            <button type="submit" class="btn" style="background-color: #004225; color: white; border: 1px solid #004225; border-radius: 0 4px 4px 0; height: 40px; padding: 6px 15px;">
                                <i class="fa fa-search" style="color: white;"></i>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="footer-content" style="background-color: #004225; position: relative; padding: 30px 0; width: 100%;">
        <div class="container" style="width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 15px;">
            <div class="row">
                <div class="col-md-3 col-sm-12">
                    <aside class="logo">
                        <img src="assets/img/logo-white.png" class="vertical-center" alt="Zanvarsity" />
                    </aside>
                </div>
                <div class="col-md-3 col-sm-4">
                    <aside>
                        <header>
                            <h4>Contact Us</h4>
                        </header>
                        <address>
                            <strong>Zanvarsity</strong><br />
                            <span>P.O. Box 1234</span><br />
                            <span>Zanzibar, Tanzania</span><br /><br />
                            <abbr title="Phone">Phone:</abbr> +255 000 000 000<br />
                            <abbr title="Email">Email:</abbr> <a href="mailto:info@zanvarsity.ac.tz">info@zanvarsity.ac.tz</a>
                        </address>
                    </aside>
                </div>
                <div class="col-md-3 col-sm-4">
                    <aside>
                        <header>
                            <h4>Quick Links</h4>
                        </header>
                        <ul class="list-links" style="list-style: none; padding: 0; margin: 0;">
                            <li style="margin-bottom: 8px;"><a href="about.php" style="color: #fff; text-decoration: none; transition: color 0.3s ease;">About Us</a></li>
                            <li style="margin-bottom: 8px;"><a href="academics.php" style="color: #fff; text-decoration: none; transition: color 0.3s ease;">Academics</a></li>
                            <li style="margin-bottom: 8px;"><a href="admissions.php" style="color: #fff; text-decoration: none; transition: color 0.3s ease;">Admissions</a></li>
                            <li style="margin-bottom: 8px;"><a href="research.php" style="color: #fff; text-decoration: none; transition: color 0.3s ease;">Research</a></li>
                            <li style="margin-bottom: 8px;"><a href="campus-life.php" style="color: #fff; text-decoration: none; transition: color 0.3s ease;">Campus Life</a></li>
                            <li><a href="contact.php" style="color: #fff; text-decoration: none; transition: color 0.3s ease;">Contact Us</a></li>
                        </ul>
                    </aside>
                </div>
                <div class="col-md-3 col-sm-4">
                    <aside>
                        <header>
                            <h4>Newsletter</h4>
                        </header>
                        <style>
                            /* Scoped styles for newsletter form */
                            #page-footer .newsletter-form {
                                margin: 0;
                                padding: 0;
                                max-width: 100%;
                                box-sizing: border-box;
                            }
                            #page-footer .newsletter-form .form-group {
                                margin-bottom: 15px;
                                width: 100%;
                            }
                            #page-footer .newsletter-form .form-control {
                                width: 100% !important;
                                max-width: 100% !important;
                                margin: 0 0 10px 0 !important;
                                padding: 8px 12px !important;
                                border: 1px solid #ddd !important;
                                border-radius: 4px !important;
                                box-sizing: border-box !important;
                                height: auto !important;
                            }
                            #page-footer .newsletter-form .btn {
                                width: 100% !important;
                                padding: 8px 20px !important;
                                margin: 0 !important;
                                box-sizing: border-box !important;
                            }
                        </style>
                        
                        <p style="margin-bottom: 15px; color: #fff;">Subscribe to our newsletter to receive updates on news and events.</p>
                        <?php
                        // Process form submission
                        $subscribed = false;
                        $error = '';
                        
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newsletter_email'])) {
                            $email = filter_var($_POST['newsletter_email'], FILTER_VALIDATE_EMAIL);
                            
                            if ($email) {
                                try {
                                    // Simple mail function for now
                                    $to = 'info@zanvarsity.ac.tz';
                                    $subject = 'New Newsletter Subscription';
                                    $message = "A new user has subscribed to the newsletter.\n\nEmail: " . $email;
                                    $headers = 'From: newsletter@zanvarsity.ac.tz' . "\r\n" .
                                               'Reply-To: ' . $email . "\r\n" .
                                               'X-Mailer: PHP/' . phpversion();
                                    
                                    if (mail($to, $subject, $message, $headers)) {
                                        $subscribed = true;
                                    } else {
                                        $error = 'Failed to send subscription. Please try again later.';
                                    }
                                } catch (Exception $e) {
                                    error_log('Mail Error: ' . $e->getMessage());
                                    $error = 'Failed to send subscription. Please try again later.';
                                }
                            } else {
                                $error = 'Please enter a valid email address.';
                            }
                        }
                        ?>
                        
                        <div id="newsletter-message" class="alert" style="display: none; margin-bottom: 15px; padding: 10px; border-radius: 4px;"></div>
                        
                        <?php if ($subscribed): ?>
                            <div class="alert alert-success" style="background-color: #dff0d8; color: #3c763d; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                                Thank you for subscribing to our newsletter!
                            </div>
                        <?php else: ?>
                            <form id="newsletter-form" method="POST" action="#newsletter-form" class="newsletter-form">
                                <div class="form-group">
                                    <input type="email" name="newsletter_email" class="form-control" 
                                           placeholder="Your email address" required 
                                           style="margin-bottom: 10px; width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                                <button type="submit" class="btn btn-primary" 
                                        style="background-color: #98FB98; border: none; color: #004225; 
                                               padding: 8px 20px; transition: background-color 0.3s ease; width: 100%;">
                                    Subscribe
                                </button>
                            </form>
                            <?php if ($error): ?>
                                <div class="alert alert-danger" style="margin-top: 10px; padding: 8px; background-color: #f2dede; color: #a94442; border-radius: 4px;">
                                    <?php echo htmlspecialchars($error); ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <script>
                        // Handle form submission with AJAX for better user experience
                        document.addEventListener('DOMContentLoaded', function() {
                            const form = document.getElementById('newsletter-form');
                            if (form) {
                                form.addEventListener('submit', function(e) {
                                    e.preventDefault();
                                    const formData = new FormData(this);
                                    const messageDiv = document.getElementById('newsletter-message');
                                    
                                    fetch('', {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.text())
                                    .then(html => {
                                        // Create a temporary div to parse the response
                                        const tempDiv = document.createElement('div');
                                        tempDiv.innerHTML = html;
                                        
                                        // Extract the newsletter form or success message
                                        const newForm = tempDiv.querySelector('#newsletter-form');
                                        const successMessage = tempDiv.querySelector('.alert-success');
                                        const errorMessage = tempDiv.querySelector('.alert-danger');
                                        
                                        if (successMessage) {
                                            form.style.display = 'none';
                                            messageDiv.textContent = 'Thank you for subscribing to our newsletter!';
                                            messageDiv.className = 'alert alert-success';
                                            messageDiv.style.display = 'block';
                                            messageDiv.style.backgroundColor = '#dff0d8';
                                            messageDiv.style.color = '#3c763d';
                                            messageDiv.style.padding = '10px';
                                            messageDiv.style.borderRadius = '4px';
                                            messageDiv.style.marginBottom = '15px';
                                        } else if (errorMessage) {
                                            messageDiv.textContent = errorMessage.textContent;
                                            messageDiv.className = 'alert alert-danger';
                                            messageDiv.style.display = 'block';
                                            messageDiv.style.backgroundColor = '#f2dede';
                                            messageDiv.style.color = '#a94442';
                                            messageDiv.style.padding = '10px';
                                            messageDiv.style.borderRadius = '4px';
                                            messageDiv.style.marginBottom = '15px';
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        messageDiv.textContent = 'An error occurred. Please try again later.';
                                        messageDiv.className = 'alert alert-danger';
                                        messageDiv.style.display = 'block';
                                    });
                                });
                            }
                        });
                        </script>
                    </aside>
                </div>
            </div>
        </div>
        <div class="background">
            <img src="assets/img/background-city.png" alt="Background" />
        </div>
    </section>

    <section id="footer-bottom" style="background-color: #004225 !important; box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.2); position: relative; z-index: 1; width: 100%;">
        <div class="footer-divider"></div>
        <div class="container" style="width: 100%; max-width: 1200px; margin: 0 auto; padding: 15px;">
            <div class="footer-inner text-center">
                <div class="copyright">&copy; <?php echo date('Y'); ?> Zanvarsity. All rights reserved.</div>
            </div>
        </div>
    </section>
</footer>

<!-- JavaScript Files (already included in header) -->
</div><!-- end Wrapper -->
<?php include('scroll-to-top.php'); ?>
</body>
</html>