<?php
/**
 * About Footer Template for Admin
 * 
 * This is a reusable footer template for admin about-related pages.
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
        </div>
    </section>

    <section id="footer-content" style="background-color: #004225; position: relative; padding: 30px 0; width: 100%;">
        <div class="container" style="width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 15px;">
            <div class="row">
                <div class="col-md-3 col-sm-12">
                    <aside class="logo">
                        <img src="../assets/img/logo-white.png" class="vertical-center" alt="Zanvarsity" />
                    </aside>
                    <p style="color: #fff; margin-top: 15px;">
                        Zanvarsity is a leading educational institution committed to academic excellence and holistic development.
                    </p>
                    <p style="color: #fff; margin-top: 15px;">
                        <i class="fa fa-map-marker"></i> P.O. Box 1240, Zanzibar, Tanzania<br>
                        <i class="fa fa-phone"></i> +255 772 601 303<br>
                        <i class="fa fa-envelope"></i> info@zanvarsity.ac.tz
                    </p>
                </div>
                
                <div class="col-md-3 col-sm-4">
                    <aside>
                        <header><h4>Quick Links</h4></header>
                        <ul class="list-links" style="list-style: none; padding: 0; margin: 0;">
                            <li><a href="../index.php" style="color: #fff; display: block; padding: 5px 0; text-decoration: none; transition: color 0.3s ease;">Home</a></li>
                            <li><a href="../about.php" style="color: #fff; display: block; padding: 5px 0; text-decoration: none; transition: color 0.3s ease;">About Us</a></li>
                            <li><a href="../programs.php" style="color: #fff; display: block; padding: 5px 0; text-decoration: none; transition: color 0.3s ease;">Programs</a></li>
                            <li><a href="../admissions.php" style="color: #fff; display: block; padding: 5px 0; text-decoration: none; transition: color 0.3s ease;">Admissions</a></li>
                            <li><a href="../research.php" style="color: #fff; display: block; padding: 5px 0; text-decoration: none; transition: color 0.3s ease;">Research</a></li>
                            <li><a href="../contact.php" style="color: #fff; display: block; padding: 5px 0; text-decoration: none; transition: color 0.3s ease;">Contact Us</a></li>
                        </ul>
                    </aside>
                </div>
                
                <div class="col-md-3 col-sm-4">
                    <aside>
                        <header><h4>Newsletter</h4></header>
                        <style>
                            .newsletter-form .form-control {
                                height: 40px;
                                border-radius: 4px 0 0 4px;
                                border: 1px solid #004225;
                                box-shadow: none;
                            }
                            .newsletter-form .btn {
                                height: 40px;
                                border-radius: 0 4px 4px 0;
                                background-color: #006633;
                                color: #fff;
                                border: 1px solid #005229;
                                transition: all 0.3s ease;
                            }
                            .newsletter-form .btn:hover {
                                background-color: #005229;
                            }
                            .newsletter-message {
                                margin-top: 10px;
                                padding: 10px;
                                border-radius: 4px;
                                display: none;
                            }
                            .newsletter-success {
                                background-color: #dff0d8;
                                color: #3c763d;
                                border: 1px solid #d6e9c6;
                            }
                            .newsletter-error {
                                background-color: #f2dede;
                                color: #a94442;
                                border: 1px solid #ebccd1;
                            }
                        </style>
                        
                        <?php if (isset($newsletter_success)): ?>
                            <div class="newsletter-message newsletter-success">
                                <?php echo $newsletter_success; ?>
                            </div>
                        <?php elseif (isset($newsletter_error)): ?>
                            <div class="newsletter-message newsletter-error">
                                <?php echo $newsletter_error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form id="newsletter-form" method="POST" action="#newsletter-form" class="newsletter-form">
                            <div class="form-group">
                                <input type="email" name="newsletter_email" class="form-control" 
                                       placeholder="Your email address" required 
                                       value="<?php echo isset($_POST['newsletter_email']) ? htmlspecialchars($_POST['newsletter_email']) : ''; ?>">
                            </div>
                            <button type="submit" class="btn btn-block">Subscribe</button>
                        </form>
                        
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const form = document.getElementById('newsletter-form');
                                if (form) {
                                    form.addEventListener('submit', function(e) {
                                        e.preventDefault();
                                        
                                        const email = this.querySelector('[name="newsletter_email"]').value;
                                        const messageDiv = document.querySelector('.newsletter-message');
                                        
                                        // Simple email validation
                                        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                                            showMessage('Please enter a valid email address', 'error');
                                            return;
                                        }
                                        
                                        // Simulate form submission (replace with actual AJAX call)
                                        setTimeout(function() {
                                            showMessage('Thank you for subscribing to our newsletter!', 'success');
                                            form.reset();
                                        }, 500);
                                    });
                                }
                                
                                function showMessage(message, type) {
                                    // Hide any existing messages
                                    const existingMessages = document.querySelectorAll('.newsletter-message');
                                    existingMessages.forEach(function(msg) {
                                        msg.style.display = 'none';
                                    });
                                    
                                    // Create new message element if it doesn't exist
                                    let messageDiv = document.querySelector('.newsletter-message');
                                    if (!messageDiv) {
                                        messageDiv = document.createElement('div');
                                        messageDiv.className = 'newsletter-message';
                                        form.parentNode.insertBefore(messageDiv, form.nextSibling);
                                    }
                                    
                                    // Set message content and style
                                    messageDiv.textContent = message;
                                    messageDiv.className = 'newsletter-message';
                                    messageDiv.classList.add('newsletter-' + type);
                                    messageDiv.style.display = 'block';
                                    
                                    // Auto-hide after 5 seconds
                                    setTimeout(function() {
                                        messageDiv.style.opacity = '0';
                                        messageDiv.style.transition = 'opacity 0.5s ease';
                                        setTimeout(function() {
                                            messageDiv.style.display = 'none';
                                            messageDiv.style.opacity = '1';
                                        }, 500);
                                    }, 5000);
                                }
                            });
                        </script>
                    </aside>
                </div>
                
                <div class="col-md-3 col-sm-4">
                    <aside>
                        <header><h4>Connect With Us</h4></header>
                        <div class="social-networks">
                            <a href="#" class="social-icon" style="color: #fff; font-size: 18px; margin-right: 15px; text-decoration: none;"><i class="fa fa-facebook"></i></a>
                            <a href="#" class="social-icon" style="color: #fff; font-size: 18px; margin-right: 15px; text-decoration: none;"><i class="fa fa-twitter"></i></a>
                            <a href="#" class="social-icon" style="color: #fff; font-size: 18px; margin-right: 15px; text-decoration: none;"><i class="fa fa-instagram"></i></a>
                            <a href="#" class="social-icon" style="color: #fff; font-size: 18px; margin-right: 15px; text-decoration: none;"><i class="fa fa-linkedin"></i></a>
                            <a href="#" class="social-icon" style="color: #fff; font-size: 18px; text-decoration: none;"><i class="fa fa-youtube"></i></a>
                        </div>
                        
                        <div style="margin-top: 20px;">
                            <a href="#" style="display: inline-block; margin-right: 10px; margin-bottom: 10px;">
                                <img src="../assets/img/app-store.png" alt="Download on App Store" style="height: 40px;">
                            </a>
                            <a href="#" style="display: inline-block;">
                                <img src="../assets/img/google-play.png" alt="Get it on Google Play" style="height: 40px;">
                            </a>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
        <div class="background">
            <img src="../assets/img/background-city.png" alt="Background" />
        </div>
    </section>

    <section id="footer-bottom" style="background-color: #004225 !important; box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.2); position: relative; z-index: 1; width: 100%;">
        <div class="footer-divider"></div>
        <div class="container" style="width: 100%; max-width: 1200px; margin: 0 auto; padding: 15px;">
            <div class="row">
                <div class="col-md-6" style="color: #fff; text-align: left; margin-top: 10px;">
                    &copy; <?php echo date('Y'); ?> Zanvarsity. All Rights Reserved.
                </div>
                <div class="col-md-6" style="text-align: right; margin-top: 10px;">
                    <a href="../privacy-policy.php" style="color: #fff; margin-left: 15px; text-decoration: none;">Privacy Policy</a>
                    <a href="../terms-conditions.php" style="color: #fff; margin-left: 15px; text-decoration: none;">Terms & Conditions</a>
                    <a href="../sitemap.php" style="color: #fff; margin-left: 15px; text-decoration: none;">Sitemap</a>
                </div>
            </div>
        </div>
    </section>
</footer>

<!-- JavaScript Files -->
<script src="../assets/js/jquery-2.1.0.min.js"></script>
<script src="../assets/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/js/selectize.min.js"></script>
<script src="../assets/js/owl.carousel.min.js"></script>
<script src="../assets/js/jquery.placeholder.js"></script>
<script src="../assets/js/jQuery.equalHeights.js"></script>
<script src="../assets/js/countdown.js"></script>
<script src="../assets/js/jquery.vanillabox-0.1.5.min.js"></script>
<script src="../assets/js/custom.js"></script>

<!-- Page-specific JavaScript -->
<?php if (isset($page_js)): ?>
<script src="../<?php echo ltrim($page_js, '/'); ?>"></script>
<?php endif; ?>

<!-- Back to top button -->
<a href="#" id="scroll-top" class="back-to-top" style="display: none; position: fixed; bottom: 20px; right: 20px; background-color: #004225; color: #fff; width: 40px; height: 40px; text-align: center; line-height: 40px; border-radius: 4px; z-index: 999; text-decoration: none; opacity: 0.8; transition: opacity 0.3s ease;">
    <i class="fa fa-chevron-up"></i>
</a>

<script>
// Back to top button
jQuery(document).ready(function($) {
    // Show/hide back to top button
    $(window).scroll(function() {
        if ($(this).scrollTop() > 300) {
            $('#scroll-top').fadeIn();
        } else {
            $('#scroll-top').fadeOut();
        }
    });
    
    // Smooth scroll to top
    $('#scroll-top').click(function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop: 0}, 800);
        return false;
    });
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Initialize popovers
    $('[data-toggle="popover"]').popover();
});
</script>

</div><!-- end Wrapper -->
</body>
</html>
