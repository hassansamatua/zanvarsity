<?php
// Determine the correct asset path based on where this file is being included from
$current_dir = dirname($_SERVER['PHP_SELF']);
$asset_path = '';

// If we're in a subdirectory (like admin), we need to go up one level
if (strpos($current_dir, '/admin') !== false) {
    $asset_path = '../';
}
?>
</div><!-- /#page-content -->
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
                            <img src="<?php echo $assets_url; ?>/img/logo-white.png" class="vertical-center" alt="Zanvarsity" />
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
                                <li style="margin-bottom: 8px;"><a href="<?php echo $asset_path; ?>about.php" style="color: #fff; text-decoration: none; transition: color 0.3s ease;">About Us</a></li>
                                <li style="margin-bottom: 8px;"><a href="<?php echo $asset_path; ?>academics.php" style="color: #fff; text-decoration: none; transition: color 0.3s ease;">Academics</a></li>
                                <li style="margin-bottom: 8px;"><a href="<?php echo $asset_path; ?>admissions.php" style="color: #fff; text-decoration: none; transition: color 0.3s ease;">Admissions</a></li>
                                <li style="margin-bottom: 8px;"><a href="<?php echo $asset_path; ?>research.php" style="color: #fff; text-decoration: none; transition: color 0.3s ease;">Research</a></li>
                                <li style="margin-bottom: 8px;"><a href="<?php echo $asset_path; ?>campus-life.php" style="color: #fff; text-decoration: none; transition: color 0.3s ease;">Campus Life</a></li>
                                <li><a href="<?php echo $asset_path; ?>contact.php" style="color: #fff; text-decoration: none; transition: color 0.3s ease;">Contact Us</a></li>
                            </ul>
                        </aside>
                    </div>
                    <div class="col-md-3 col-sm-4">
                        <aside>
                            <header>
                                <h4>Newsletter</h4>
                            </header>
                            <p style="margin-bottom: 15px; color: #fff;">Subscribe to our newsletter to receive updates on news and events.</p>
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
                        </aside>
                    </div>
                </div>
            </div>
            <div class="background">
                <img src="<?php echo $assets_url; ?>/img/background-city.png" alt="Background" />
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
    <!-- end Footer -->
</div>
<!-- end Wrapper -->

<?php 
$assets_url = $asset_path . 'assets';
?>

<!-- JavaScript Libraries -->
<script src="<?php echo $assets_url; ?>/js/jquery-2.1.0.min.js"></script>
<script src="<?php echo $assets_url; ?>/js/jquery-migrate-1.2.1.min.js"></script>
<script src="<?php echo $assets_url; ?>/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo $assets_url; ?>/js/selectize.min.js"></script>
<script src="<?php echo $assets_url; ?>/js/owl.carousel.min.js"></script>
<script src="<?php echo $assets_url; ?>/js/jquery.validate.min.js"></script>
<script src="<?php echo $assets_url; ?>/js/jquery.placeholder.js"></script>
<script src="<?php echo $assets_url; ?>/js/jQuery.equalHeights.js"></script>
<script src="<?php echo $assets_url; ?>/js/icheck.min.js"></script>
<script src="<?php echo $assets_url; ?>/js/jquery.vanillabox-0.1.5.min.js"></script>
<!-- Retina.js disabled to prevent 404 errors for @2x images -->
<!-- <script src="<?php echo $assets_url; ?>/js/retina-1.1.0.min.js"></script> -->

<!-- Custom Scripts with error handling -->
<script>
// Load custom.js with error handling
(function() {
    var script = document.createElement('script');
    script.src = '<?php echo $assets_url; ?>/js/custom.js';
    script.onerror = function() {
        console.log('Custom.js failed to load, but site will continue to function');
    };
    document.head.appendChild(script);
})();

// Fallback for missing dependencies
if (typeof jQuery === 'undefined') {
    console.log('jQuery not loaded, some features may not work');
}

// Prevent JavaScript errors from breaking the site
window.onerror = function(msg, url, lineNo, columnNo, error) {
    console.log('JavaScript error caught: ' + msg);
    return true; // Prevent default browser error handling
};
</script>

<!-- Scroll to Top Button -->
<?php include('scroll-to-top.php'); ?>

</body>
</html>
