    </div><!-- /#page-content -->
    <!-- Footer -->
    <footer id="page-footer" style="width: 100%; margin: 0; padding: 0;">
        <section id="footer-top" style="width: 100%;">
            <div style="width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 15px;">
                <div class="footer-inner">
                    <div class="footer-social">
                        <figure>Follow us:</figure>
                        <div class="icons">
                            <a href="<?php echo $base_url; ?>/twitter" target="_blank"><i class="fa fa-twitter"></i></a>
                            <a href="<?php echo $base_url; ?>/facebook" target="_blank"><i class="fa fa-facebook"></i></a>
                            <a href="<?php echo $base_url; ?>/pinterest" target="_blank"><i class="fa fa-pinterest"></i></a>
                            <a href="<?php echo $base_url; ?>/youtube" target="_blank"><i class="fa fa-youtube-play"></i></a>
                        </div>
                    </div>
                    <div class="search pull-right">
                        <form action="<?php echo $base_url; ?>/search.php" method="get">
                            <div class="input-group">
                                <input type="text" name="q" class="form-control" placeholder="Search...">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <section id="footer-content" style="width: 100%;">
            <div style="width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 15px;">
                <div class="row">
                    <div class="col-md-3 col-sm-12">
                        <aside class="logo">
                            <img src="<?php echo $assets_url; ?>/img/logo-white.png" class="vertical-center" alt="Zanvarsity">
                        </aside>
                    </div>
                    <div class="col-md-3 col-sm-4">
                        <aside>
                            <header><h4>Contact Us</h4></header>
                            <address>
                                <strong>Zanvarsity</strong><br>
                                <span>123 University Avenue</span><br><br>
                                <span>City, Country</span><br>
                                <abbr title="Telephone">Phone:</abbr> +1 234 567 8900<br>
                                <abbr title="Email">Email:</abbr> <a href="mailto:info@zanvarsity.edu">info@zanvarsity.edu</a>
                            </address>
                        </aside>
                    </div>
                    <div class="col-md-3 col-sm-4">
                        <aside>
                            <header><h4>Quick Links</h4></header>
                            <ul class="list-links">
                                <li><a href="about-us.php">About Us</a></li>
                                <li><a href="courses.php">Courses</a></li>
                                <li><a href="admissions.php">Admissions</a></li>
                                <li><a href="faculty.php">Faculty</a></li>
                                <li><a href="research.php">Research</a></li>
                                <li><a href="contact-us.php">Contact</a></li>
                            </ul>
                        </aside>
                    </div>
                    <div class="col-md-3 col-sm-4">
                        <aside>
                            <header><h4>About Zanvarsity</h4></header>
                            <p>Zanvarsity is a leading educational institution committed to excellence in teaching, research, and community service.</p>
                            <div>
                                <a href="about-us.php" class="read-more">Learn More</a>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
            <div class="background">
                <img src="assets/img/background-city.png" alt="Background">
            </div>
        </section>

        <section id="footer-bottom" style="width: 100%;">
            <div style="width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 15px;">
                <div class="footer-inner">
                    <div class="copyright">
                        &copy; <?php echo date('Y'); ?> Zanvarsity. All rights reserved.
                    </div>
                </div>
            </div>
        </section>
    </footer>
    <!-- end Footer -->
</div>
<!-- end Wrapper -->

<!-- JavaScript -->
<script type="text/javascript" src="assets/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="assets/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/js/selectize.min.js"></script>
<script type="text/javascript" src="assets/js/owl.carousel.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.validate.min.js"></script>
<?php 
// Get base URL from session
$base_url = isset($_SESSION['base_url']) ? rtrim($_SESSION['base_url'], '/') : '';
$assets_url = $base_url . '/assets';
?>

<!-- JavaScript Libraries -->
<script type="text/javascript" src="<?php echo $assets_url; ?>/js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="<?php echo $assets_url; ?>/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="<?php echo $assets_url; ?>/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $assets_url; ?>/js/owl.carousel.min.js"></script>
<script type="text/javascript" src="<?php echo $assets_url; ?>/js/selectize.min.js"></script>
<script type="text/javascript" src="<?php echo $assets_url; ?>/js/jquery.placeholder.js"></script>
<script type="text/javascript" src="<?php echo $assets_url; ?>/js/jQuery.equalHeights.js"></script>
<script type="text/javascript" src="<?php echo $assets_url; ?>/js/icheck.min.js"></script>
<script type="text/javascript" src="<?php echo $assets_url; ?>/js/jquery.vanillabox-0.1.5.min.js"></script>
<script type="text/javascript" src="<?php echo $assets_url; ?>/js/retina-1.1.0.min.js">
<script type="text/javascript" src="assets/js/custom.js"></script>

</body>
</html>
