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
<footer id="page-footer">
    <section id="footer-top">
        <div class="container-fluid">
            <div class="footer-inner">
                <div class="footer-social">
                    <figure>Follow us:</figure>
                    <div class="icons">
                        <a href="#"><i class="fa fa-twitter"></i></a>
                        <a href="#"><i class="fa fa-facebook"></i></a>
                        <a href="#"><i class="fa fa-pinterest"></i></a>
                        <a href="#"><i class="fa fa-youtube-play"></i></a>
                    </div>
                </div>
                <div class="search pull-right">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search" />
                        <span class="input-group-btn">
                            <button type="submit" class="btn">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="footer-content">
        <div class="container">
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
                        <ul class="list-links">
                            <li><a href="about.php">About Us</a></li>
                            <li><a href="academics.php">Academics</a></li>
                            <li><a href="admissions.php">Admissions</a></li>
                            <li><a href="research.php">Research</a></li>
                            <li><a href="campus-life.php">Campus Life</a></li>
                            <li><a href="contact.php">Contact Us</a></li>
                        </ul>
                    </aside>
                </div>
                <div class="col-md-3 col-sm-4">
                    <aside>
                        <header>
                            <h4>Newsletter</h4>
                        </header>
                        <p>Subscribe to our newsletter to receive updates on news and events.</p>
                        <form class="newsletter-form">
                            <div class="form-group">
                                <input type="email" class="form-control" placeholder="Your email address" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Subscribe</button>
                        </form>
                    </aside>
                </div>
            </div>
        </div>
        <div class="background">
            <img src="assets/img/background-city.png" alt="Background" />
        </div>
    </section>

    <section id="footer-bottom">
        <div class="container">
            <div class="footer-inner">
                <div class="copyright">&copy; <?php echo date('Y'); ?> Zanvarsity. All rights reserved.</div>
            </div>
        </div>
    </section>
</footer>

<!-- JavaScript Files (already included in header) -->
</div><!-- end Wrapper -->
</body>
</html>
