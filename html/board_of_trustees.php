<?php
// Set page title and description
$page_title = "Board of Trustees";
$page_description = "Meet the members of the Board of Trustees of Zanzibar University";

// Include the about header
include_once 'includes/about_header.php';
?>

<!-- Page Content -->
<div id="page-content">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-md-9">
                <section class="block">
                    <div class="page-title">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="breadcrumb" style="margin: 0 0 15px 0; padding: 0; background: none;">
                                    <ul style="list-style: none; padding: 0; margin: 0; display: flex; align-items: center; font-size: 14px;">
                                        <li style="display: inline-block; margin-right: 10px;">
                                            <a href="index.php" style="color: #004225; text-decoration: none; transition: color 0.3s ease;">
                                                <i class="fa fa-home" style="margin-right: 5px;"></i>Home
                                            </a>
                                            <span style="margin: 0 8px; color: #999;">/</span>
                                        </li>
                                        <li style="display: inline-block; color: #777;" class="active">
                                            Board of Trustees
                                        </li>
                                    </ul>
                                </div>
                                <h1 style="margin-top: 0;">Board of Trustees</h1>
                            </div>
                        </div>
                    </div>

                    <!-- Board Members Section -->
                    <article class="page-content">
                        <div class="section-title clearfix">
                            <h2>
                                <i class='bx bx-group' style="color: #004225; margin-right: 10px;"></i>
                                <span>Our Esteemed Board Members</span>
                            </h2>
                        </div>
                        <div class="box" style="background: #fff; padding: 25px; border-radius: 4px; box-shadow: 0 1px 5px rgba(0,0,0,0.03);">
                            <div class="row">
                                <!-- Board Member 1 -->
                                <div class="col-md-6">
                                    <div class="team-member" style="margin-bottom: 30px; display: flex;">
                                        <div class="member-image" style="width: 120px; height: 120px; border-radius: 50%; overflow: hidden; margin-right: 20px; flex-shrink: 0;">
                                            <img src="assets/img/chancellor.png" alt="Trustee Name" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <div class="member-info">
                                            <h4 style="color: #004225; margin-top: 0; margin-bottom: 5px;">Eng. Abdulqader O.
                                            Hafez</h4>
                                            <p class="position" style="color: #666; font-style: italic; margin-bottom: 8px;">Chancellor</p>
                                            <p style="color: #555; font-size: 14px; line-height: 1.5; margin-bottom: 10px;">
                                                (Chairman)
                                            </p>
                                            <div class="social-links" style="display: flex; gap: 10px;">
                                                <a href="#" style="color: #004225; font-size: 18px; transition: all 0.3s ease;"><i class='bx bxl-linkedin-square'></i></a>
                                                <a href="#" style="color: #004225; font-size: 18px; transition: all 0.3s ease;"><i class='bx bxl-twitter'></i></a>
                                                <a href="#" style="color: #004225; font-size: 18px; transition: all 0.3s ease;"><i class='bx bxl-facebook-circle'></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Board Member 2 -->
                                <div class="col-md-6">
                                    <div class="team-member" style="margin-bottom: 30px; display: flex;">
                                        <div class="member-image" style="width: 120px; height: 120px; border-radius: 50%; overflow: hidden; margin-right: 20px; flex-shrink: 0;">
                                            <img src="assets/img/drabdulwahab.png" alt="Trustee Name" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <div class="member-info">
                                            <h4 style="color: #004225; margin-top: 0; margin-bottom: 5px;">Prof. Abdulwahab Nourwali</h4>
                                            <p class="position" style="color: #666; font-style: italic; margin-bottom: 8px;">Chairman of University Council</p>
                                            <p style="color: #555; font-size: 14px; line-height: 1.5; margin-bottom: 0;">
                                               (Member)
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Add more board members as needed -->
                                <div class="col-md-6">
                                    <div class="team-member" style="margin-bottom: 30px; display: flex;">
                                        <div class="member-image" style="width: 120px; height: 120px; border-radius: 50%; overflow: hidden; margin-right: 20px; flex-shrink: 0; background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                                        <img src="assets/img/haytham.jpg" alt="Trustee Name" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <div class="member-info">
                                            <h4 style="color: #004225; margin-top: 0; margin-bottom: 5px;">Mr. Haytham Suleiman
                                            Basahel</h4>
                                            <p class="position" style="color: #666; font-style: italic; margin-bottom: 8px;">Diral-Iman, Jeddah</p>
                                            <p style="color: #555; font-size: 14px; line-height: 1.5; margin-bottom: 10px;">
                                        (Member)
                                            </p>
                                            <div class="social-links" style="display: flex; gap: 10px;">
                                                <a href="#" style="color: #004225; font-size: 18px; transition: all 0.3s ease;"><i class='bx bxl-linkedin-square'></i></a>
                                                <a href="#" style="color: #004225; font-size: 18px; transition: all 0.3s ease;"><i class='bx bxl-twitter'></i></a>
                                                <a href="#" style="color: #004225; font-size: 18px; transition: all 0.3s ease;"><i class='bx bxl-facebook-circle'></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </article>

                    <!-- Board Responsibilities Section -->
                    <article class="page-content" style="margin-top: 40px;">
                        <div class="section-title clearfix">
                            <h2>
                                <i class='bx bx-task' style="color: #004225; margin-right: 10px;"></i>
                                <span>Board Responsibilities</span>
                            </h2>
                        </div>
                        <div class="box" style="background: #fff; padding: 25px; border-radius: 4px; box-shadow: 0 1px 5px rgba(0,0,0,0.03);">
                            <div style="color: #555; line-height: 1.8; font-size: 15px;">
                                <p>The Board of Trustees is the highest governing body of Zanzibar University, responsible for:</p>
                                <ul style="padding-left: 20px; margin: 15px 0;">
                                    <li>Setting the strategic direction and policies of the university</li>
                                    <li>Ensuring financial sustainability and proper use of resources</li>
                                    <li>Appointing and evaluating the Vice Chancellor</li>
                                    <li>Overseeing academic quality and institutional effectiveness</li>
                                    <li>Ensuring compliance with legal and regulatory requirements</li>
                                    <li>Representing the interests of stakeholders and the community</li>
                                </ul>
                                <p>The Board meets quarterly to review the university's performance and make strategic decisions for its growth and development.</p>
                            </div>
                        </div>
                    </article>
                </section>
            </div>
            <!-- end Main Content -->

            <!-- Sidebar -->
            <div class="col-md-3">
                <aside class="sidebar">
                    <!-- Quick Links -->
                    <div class="widget">
                        <h3 class="sidebar-title" style="color: #004225; font-size: 20px; margin-top: 0; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #eaeaea;">
                            <span>Quick Links</span>
                        </h3>
                        <ul class="list-links" style="padding-left: 0; list-style: none;">
                            <li><a href="Background_info.php" style="color: #555; display: block; padding: 8px 0; border-bottom: 1px solid #eee; text-decoration: none; transition: all 0.3s ease;">
                                <i class='bx bx-chevron-right' style="color: #004225; margin-right: 8px;"></i> Background Information
                            </a></li>
                            <li><a href="vision_mission.php" style="color: #555; display: block; padding: 8px 0; border-bottom: 1px solid #eee; text-decoration: none; transition: all 0.3s ease;">
                                <i class='bx bx-chevron-right' style="color: #004225; margin-right: 8px;"></i> Vision & Mission
                            </a></li>
                            <li><a href="board_of_trustees.php" style="color: #004225; font-weight: 600; display: block; padding: 8px 0; border-bottom: 1px solid #eee; text-decoration: none; transition: all 0.3s ease;">
                                <i class='bx bx-chevron-right' style="color: #004225; margin-right: 8px;"></i> Board of Trustees
                            </a></li>
                            <li><a href="#" style="color: #555; display: block; padding: 8px 0; text-decoration: none; transition: all 0.3s ease;">
                                <i class='bx bx-chevron-right' style="color: #004225; margin-right: 8px;"></i> Leadership
                            </a></li>
                        </ul>
                    </div>

                    <!-- Contact Information -->
                    <div class="widget">
                        <h3 class="sidebar-title" style="color: #004225; font-size: 20px; margin-top: 30px; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #eaeaea;">
                            <span>Contact Us</span>
                        </h3>
                        <div style="color: #555; line-height: 1.7; font-size: 14px;">
                            <p><i class='bx bx-map' style="color: #004225; margin-right: 8px;"></i> Tunguu, Zanzibar, Tanzania</p>
                            <p><i class='bx bx-phone' style="color: #004225; margin-right: 8px;"></i> +255 24 223 0724</p>
                            <p><i class='bx bx-envelope' style="color: #004225; margin-right: 8px;"></i> info@zanvarsity.ac.tz</p>
                            <p><i class='bx bx-globe' style="color: #004225; margin-right: 8px;"></i> www.zanvarsity.ac.tz</p>
                        </div>
                    </div>
                </aside>
            </div>
            <!-- end Sidebar -->
        </div>
    </div>
    <?php
// Include the footer
include_once 'includes/about_footer.php';
?>
</div>
<!-- end Page Content -->
