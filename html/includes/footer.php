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
                <img src="<?php echo $assets_url; ?>/img/background-city.png" alt="Background">
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

<?php 
// Get base URL from session
$base_url = isset($_SESSION['base_url']) ? rtrim($_SESSION['base_url'], '/') : '';
$assets_url = $base_url . '/assets';
?>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/js/standalone/selectize.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-placeholder/2.3.1/jquery.placeholder.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/icheck.min.js"></script>
<script src="<?php echo $assets_url; ?>/js/custom.js" type="text/javascript"></script></script>

</body>
</html>
