<?php
// Set page title and description
$page_title = "University Senate";
$page_description = "Meet the members of the Zanzibar University Senate";

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
                                            University Senate
                                        </li>
                                    </ul>
                                </div>
                                <h1 style="color: #004225; font-size: 32px; margin: 0 0 15px 0; font-weight: 600;">University Senate</h1>
                            </div>
                        </div>
                    </div>

                    <article class="page-content">
                        <div class="box" style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 15px rgba(0,0,0,0.05);">
                            <div class="section-title clearfix" style="margin-bottom: 30px;">
                                <h2 style="color: #004225; font-size: 24px; position: relative; padding-bottom: 10px; margin-top: 0;">
                                    <i class='bx bx-group' style="color: #004225; margin-right: 10px;"></i>
                                    <span>University Senate Members</span>
                                </h2>
                                <p style="color: #666; font-size: 15px; line-height: 1.6;">
                                    The University Senate is the supreme academic body of Zanzibar University, responsible for academic policies, regulations, and standards of the institution.
                                </p>
                            </div>

                            <div class="row" style="display: flex; flex-wrap: wrap; margin: 0 -15px;">
                                <?php
                                $members = [
                                    ['name' => 'Prof. Muhammed A. Elhussein', 'position' => 'Vice Chancellor', 'role' => 'Chairperson', 'img' => 'vc.jpg'],
                                    ['name' => 'Dr. Mamudu Daffay', 'position' => 'DVC for Academic Affairs', 'role' => 'Member', 'img' => 'daffay.jpeg'],
                                    ['name' => 'Mr. Iddi K. Haji', 'position' => 'Director, ZHELB', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Dr. Salama Yussuf', 'position' => 'Dean, FBA', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Dr. Muhiddin A. Khamis', 'position' => 'Dean, FLS', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Dr. Mpawenimana A. Said', 'position' => 'Dean, FASS', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Dr. Akly O. Babi', 'position' => 'Dean, FoS', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Dr. Khalfan Mohammed', 'position' => 'Dean, Fohas', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Dr. Amir K. Mwinyi', 'position' => 'Dean, FoE', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Mr. Hassan H. Saad', 'position' => 'Dean, Student\'s Affairs', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Dr. Abdallah U. Hamad', 'position' => 'Director, IIBF', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Dr. Yahya Kh. Hamad', 'position' => 'Senior Lecturer & Legal Advisor', 'role' => 'Member', 'img' => 'dryahya.png'],
                                    ['name' => 'Mr. Saleh S. Mwinyi', 'position' => 'Director, ICE', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'CPA. Bakar R. Bakar', 'position' => 'Director of Finance', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Ms. Haulath Tundamanyire', 'position' => 'Director, Library Services', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Dr. Suleiman M. Faki', 'position' => 'HoD Accounting & Finance', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Ms. Intisar O. Said', 'position' => 'HoD, BIT', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Mr. Sultan S. Omar', 'position' => 'HoD, Marketing', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Mr. Salem N. Hemed', 'position' => 'HoD, Telecommunication', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Ms. Nufaila A. Nassor', 'position' => 'HoD, Procurement', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Ms. Maryam M. Ali', 'position' => 'HoD, Public Administration', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Dr. Khatib M. Omar', 'position' => 'HoD, Languages', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Dr. Issa M. Hemed', 'position' => 'HoD, Economics', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Mr. Soud H. Ali', 'position' => 'HoD, Education', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Mr. Salim S. Ali', 'position' => 'HoD, BIS', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Dr. Bakari A. Mohammed', 'position' => 'HoD, Social Work', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Dr. Sikujua O. Hamdan', 'position' => 'HoD Common Law', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Mr. Ally U. Hamad', 'position' => 'HoD, Science', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Mr. Sultan Kh. Muki', 'position' => 'HoD, Counseling Psychology', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Mr. Seif S. Khalfan', 'position' => 'HoD, Nursing', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Mr. Nasib A. Wazir', 'position' => 'Examinations Officer', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Mr. Said H. Salum', 'position' => 'President, ZANUSO', 'role' => 'Member', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Dr. Abdallah H. Gharib', 'position' => 'Quality Assurance Coordinator', 'role' => 'Member', 'img' => 'dragharib.jpeg'],
                                    ['name' => 'Dr. Abdo H. Ali Guroob', 'position' => 'Coordinator, IT Services', 'role' => 'Invitee', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Ms. Nasra S. Mohammed', 'position' => 'Admission Officer', 'role' => 'Invitee', 'img' => 'placeholder.jpg'],
                                    ['name' => 'Dr. Rashid J. Rashid', 'position' => 'DVC for Administration', 'role' => 'Secretary/Member', 'img' => 'rashid.jpeg']
                                ];

                                foreach ($members as $member) {
                                    echo '<div class="col-md-6" style="margin-bottom: 30px; padding: 0 15px; display: flex;">';
                                    echo '    <div class="council-member" style="background: #f9f9f9; padding: 20px; border-radius: 6px; width: 100%; border-top: 3px solid #004225; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">';
                                    echo '        <div style="display: flex; align-items: center; margin-bottom: 15px;">';
                                    echo '            <div style="width: 80px; height: 80px; border-radius: 50%; background: #e0e0e0; margin-right: 15px; overflow: hidden;">';
                                    echo '                <img src="assets/img/' . $member['img'] . '" alt="' . htmlspecialchars($member['name']) . '" style="width: 100%; height: 100%; object-fit: cover;">';
                                    echo '            </div>';
                                    echo '            <div>';
                                    echo '                <h3 style="color: #004225; margin: 0 0 5px 0; font-size: 18px;">' . htmlspecialchars($member['name']) . '</h3>';
                                    echo '                <p style="color: #e67e22; font-weight: 600; margin: 0 0 5px 0; font-size: 14px;">' . $member['role'] . '</p>';
                                    echo '                <p style="color: #666; margin: 0; font-size: 13px;">' . htmlspecialchars($member['position']) . '</p>';
                                    echo '            </div>';
                                    echo '        </div>';
                                    echo '    </div>';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </article>
                </section>
            </div>
            <!-- end Main Content -->

            <!-- Sidebar -->
            <div class="col-md-3">
                <aside class="sidebar">
                    <div class="widget" style="background: #fff; border-radius: 8px; box-shadow: 0 2px 15px rgba(0,0,0,0.05); margin-bottom: 30px; overflow: hidden;">
                        <div class="widget-title" style="background: #004225; color: #fff; padding: 15px 20px; margin: 0;">
                            <h3 style="margin: 0; font-size: 16px; font-weight: 600;">Quick Links</h3>
                        </div>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="border-bottom: 1px solid #eee;">
                                <a href="about.php" style="display: block; padding: 12px 20px; color: #333; text-decoration: none; transition: all 0.3s ease; font-size: 14px;">
                                    <i class="fa fa-angle-right" style="margin-right: 8px; color: #e67e22;"></i> About Us
                                </a>
                            </li>
                            <li style="border-bottom: 1px solid #eee;">
                                <a href="mission-vision.php" style="display: block; padding: 12px 20px; color: #333; text-decoration: none; transition: all 0.3s ease; font-size: 14px;">
                                    <i class="fa fa-angle-right" style="margin-right: 8px; color: #e67e22;"></i> Mission & Vision
                                </a>
                            </li>
                            <li style="border-bottom: 1px solid #eee;">
                                <a href="council_board.php" style="display: block; padding: 12px 20px; color: #333; text-decoration: none; transition: all 0.3s ease; font-size: 14px;">
                                    <i class="fa fa-angle-right" style="margin-right: 8px; color: #e67e22;"></i> Council Board
                                </a>
                            </li>
                            <li style="border-bottom: 1px solid #eee;">
                                <a href="principal_officers.php" style="display: block; padding: 12px 20px; color: #333; text-decoration: none; transition: all 0.3s ease; font-size: 14px;">
                                    <i class="fa fa-angle-right" style="margin-right: 8px; color: #e67e22;"></i> Principal Officers
                                </a>
                            </li>
                            <li>
                                <a href="contact.php" style="display: block; padding: 12px 20px; color: #333; text-decoration: none; transition: all 0.3s ease; font-size: 14px;">
                                    <i class="fa fa-angle-right" style="margin-right: 8px; color: #e67e22;"></i> Contact Us
                                </a>
                            </li>
                        </ul>
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

<!-- Back to top -->
<a href="#" id="back-to-top" title="Back to top" style="display: none;"><i class="fa fa-chevron-up"></i></a>

<!-- JavaScripts -->
<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/plugins.js"></script>
<script src="assets/js/main.js"></script>
