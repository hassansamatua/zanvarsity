<?php
// Set page title and description
$page_title = "Senate Board";
$page_description = "Learn about the Senate Board of Zanzibar University";

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
                                            Senate Board
                                        </li>
                                    </ul>
                                </div>
                                <h1 style="margin-top: 0;">Senate Board</h1>
                            </div>
                        </div>
                    </div>

                    <!-- Senate Board Section -->
                    <article class="page-content">
                        <div class="section-title clearfix">
                            <h2>
                                <i class='bx bx-group' style="color: #004225; margin-right: 10px;"></i>
                                <span>University Senate Board</span>
                            </h2>
                        </div>
                        <div class="box" style="background: #fff; padding: 25px; border-radius: 4px; box-shadow: 0 1px 5px rgba(0,0,0,0.03);">
                            <div class="row">
                                <div class="col-md-12">
                                    <p style="margin-bottom: 20px; line-height: 1.7; color: #555;">
                                        The Senate is the supreme academic body of Zanzibar University, responsible for the academic governance of the University. It exercises general control over teaching, research, and examinations within the University.
                                    </p>
                                    
                                    <h3 style="color: #004225; margin-top: 25px; margin-bottom: 15px; font-size: 20px;">Composition of the Senate</h3>
                                    <p style="margin-bottom: 15px; line-height: 1.7; color: #555;">
                                        The Senate consists of the following members:
                                    </p>
                                    <ul style="padding-left: 20px; margin-bottom: 20px; color: #555; line-height: 1.7;">
                                        <li>The Vice-Chancellor (Chairperson)</li>
                                        <li>The Deputy Vice-Chancellor</li>
                                        <li>Deans of Faculties</li>
                                        <li>Heads of Academic Departments</li>
                                        <li>Director of Postgraduate Studies</li>
                                        <li>University Librarian</li>
                                        <li>Two members of the academic staff from each Faculty</li>
                                        <li>Two students (one male and one female) elected by the Students' Organization</li>
                                        <li>Registrar (Secretary to Senate)</li>
                                    </ul>

                                    <h3 style="color: #004225; margin-top: 25px; margin-bottom: 15px; font-size: 20px;">Key Functions</h3>
                                    <p style="margin-bottom: 15px; line-height: 1.7; color: #555;">
                                        The Senate is responsible for:
                                    </p>
                                    <ul style="padding-left: 20px; margin-bottom: 20px; color: #555; line-height: 1.7;">
                                        <li>Regulating and supervising the academic programs of the University</li>
                                        <li>Approving examination results and awarding of degrees, diplomas, and certificates</li>
                                        <li>Making recommendations to the University Council on academic matters</li>
                                        <li>Establishing and maintaining academic standards</li>
                                        <li>Promoting research within the University</li>
                                        <li>Considering and reporting on matters referred to it by the University Council</li>
                                        <li>Making regulations regarding the academic affairs of the University</li>
                                    </ul>

                                    <h3 style="color: #004225; margin-top: 25px; margin-bottom: 15px; font-size: 20px;">Current Members</h3>
                                    <div class="row">
                                        <?php
                                        $members = [
                                            ['name' => 'Prof. Muhammed A. Elhussein', 'position' => 'Vice Chancellor', 'role' => 'Chairperson'],
                                            ['name' => 'Dr. Mamudu Daffay', 'position' => 'DVC for Academic Affairs', 'role' => 'Member'],
                                            ['name' => 'Mr. Iddi K. Haji', 'position' => 'Director, ZHELB', 'role' => 'Member'],
                                            ['name' => 'Dr. Salama Yussuf', 'position' => 'Dean, FBA', 'role' => 'Member'],
                                            ['name' => 'Dr. Muhiddin A. Khamis', 'position' => 'Dean, FLS', 'role' => 'Member'],
                                            ['name' => 'Dr. Mpawenimana A. Said', 'position' => 'Dean, FASS', 'role' => 'Member'],
                                            ['name' => 'Dr. Akly O. Babi', 'position' => 'Dean, FoS', 'role' => 'Member'],
                                            ['name' => 'Dr. Khalfan Mohammed', 'position' => 'Dean, Fohas', 'role' => 'Member'],
                                            ['name' => 'Dr. Amir K. Mwinyi', 'position' => 'Dean, FoE', 'role' => 'Member'],
                                            ['name' => 'Mr. Hassan H. Saad', 'position' => 'Dean, Student\'s Affairs', 'role' => 'Member'],
                                            ['name' => 'Dr. Abdallah U. Hamad', 'position' => 'Director, IIBF', 'role' => 'Member'],
                                            ['name' => 'Dr. Yahya Kh. Hamad', 'position' => 'Senior Lecturer & Legal Advisor', 'role' => 'Member'],
                                            ['name' => 'Mr. Saleh S. Mwinyi', 'position' => 'Director, ICE', 'role' => 'Member'],
                                            ['name' => 'CPA. Bakar R. Bakar', 'position' => 'Director of Finance', 'role' => 'Member'],
                                            ['name' => 'Ms. Haulath Tundamanyire', 'position' => 'Director, Library Services', 'role' => 'Member'],
                                            ['name' => 'Dr. Suleiman M. Faki', 'position' => 'HoD Accounting & Finance', 'role' => 'Member'],
                                            ['name' => 'Ms. Intisar O. Said', 'position' => 'HoD, BIT', 'role' => 'Member'],
                                            ['name' => 'Mr. Sultan S. Omar', 'position' => 'HoD, Marketing', 'role' => 'Member'],
                                            ['name' => 'Mr. Salem N. Hemed', 'position' => 'HoD, Telecommunication', 'role' => 'Member'],
                                            ['name' => 'Ms. Nufaila A. Nassor', 'position' => 'HoD, Procurement', 'role' => 'Member'],
                                            ['name' => 'Ms. Maryam M. Ali', 'position' => 'HoD, Public Administration', 'role' => 'Member'],
                                            ['name' => 'Dr. Khatib M. Omar', 'position' => 'HoD, Languages', 'role' => 'Member'],
                                            ['name' => 'Dr. Issa M. Hemed', 'position' => 'HoD, Economics', 'role' => 'Member'],
                                            ['name' => 'Mr. Soud H. Ali', 'position' => 'HoD, Education', 'role' => 'Member'],
                                            ['name' => 'Mr. Salim S. Ali', 'position' => 'HoD, BIS', 'role' => 'Member'],
                                            ['name' => 'Dr. Bakari A. Mohammed', 'position' => 'HoD, Social Work', 'role' => 'Member'],
                                            ['name' => 'Dr. Sikujua O. Hamdan', 'position' => 'HoD Common Law', 'role' => 'Member'],
                                            ['name' => 'Mr. Ally U. Hamad', 'position' => 'HoD, Science', 'role' => 'Member'],
                                            ['name' => 'Mr. Sultan Kh. Muki', 'position' => 'HoD, Counseling Psychology', 'role' => 'Member'],
                                            ['name' => 'Mr. Seif S. Khalfan', 'position' => 'HoD, Nursing', 'role' => 'Member'],
                                            ['name' => 'Mr. Nasib A. Wazir', 'position' => 'Examinations Officer', 'role' => 'Member'],
                                            ['name' => 'Mr. Said H. Salum', 'position' => 'President, ZANUSO', 'role' => 'Member'],
                                            ['name' => 'Dr. Abdallah H. Gharib', 'position' => 'Quality Assurance Coordinator', 'role' => 'Member'],
                                            ['name' => 'Dr. Abdo H. Ali Guroob', 'position' => 'Coordinator, IT Services', 'role' => 'Invitee'],
                                            ['name' => 'Ms. Nasra S. Mohammed', 'position' => 'Admission Officer', 'role' => 'Invitee'],
                                            ['name' => 'Dr. Rashid J. Rashid', 'position' => 'DVC for Administration', 'role' => 'Secretary/Member']
                                        ];

                                        // Add custom CSS for the number badge
                                        echo '<style>
                                            .member-number {
                                                position: absolute;
                                                top: -10px;
                                                left: -10px;
                                                width: 30px;
                                                height: 30px;
                                                background: linear-gradient(135deg, #004225, #006d3a);
                                                color: white;
                                                border-radius: 50%;
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                font-weight: bold;
                                                font-size: 14px;
                                                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                                                border: 2px solid white;
                                                z-index: 1;
                                                transition: all 0.3s ease;
                                            }
                                            .member-container {
                                                position: relative;
                                            }
                                            .team-member:hover .member-number {
                                                transform: scale(1.1);
                                                box-shadow: 0 3px 8px rgba(0,0,0,0.3);
                                            }
                                        </style>';

                                        // Map member names to their image filenames
                                        $imageMap = [
                                            'Prof. Muhammed A. Elhussein' => 'vc.jpg',
                                            'Dr. Mamudu Daffay' => 'daffay.jpeg',
                                            'Dr. Yahya Kh. Hamad' => 'dryahya.png',
                                            'Dr. Abdallah H. Gharib' => 'dragharib.jpeg',
                                            'Dr. Rashid J. Rashid' => 'rashid.jpeg',
                                            'Dr. Fatma Kassim' => 'drfatma.png',
                                            'Dr. Afua Mohammed' => 'drafua.png'
                                            // Add more mappings as needed
                                        ];

                                        $counter = 1;
                                        foreach ($members as $member) {
                                            $role = $member['role'] === 'Chairperson' ? $member['role'] : $member['role'];
                                            $image = isset($imageMap[$member['name']]) ? $imageMap[$member['name']] : 'placeholder.jpg';
                                            
                                            echo '<div class="col-md-6">';
                                            echo '    <div class="team-member" style="margin-bottom: 30px; display: flex; position: relative;">';
                                            echo '        <div class="member-container" style="position: relative;">';
                                            echo '            <div class="member-number">' . $counter++ . '</div>';
                                            echo '            <div class="member-image" style="width: 100px; height: 100px; border-radius: 50%; overflow: hidden; margin-right: 20px; flex-shrink: 0; background: #f5f5f5; display: flex; align-items: center; justify-content: center; position: relative;">';
                                            echo '                <img src="assets/img/' . $image . '" alt="' . htmlspecialchars($member['name']) . '" style="width: 100%; height: 100%; object-fit: cover;">';
                                            echo '            </div>';
                                            echo '        </div>';
                                            echo '        <div class="member-details">';
                                            echo '            <h3 style="margin: 0 0 5px 0; font-size: 16px; color: #333;">' . htmlspecialchars($member['name']) . '</h3>';
                                            echo '            <p style="margin: 0 0 8px 0; color: #004225; font-weight: 600; font-size: 14px;">' . htmlspecialchars($member['position']) . ' (' . $role . ')</p>';
                                            echo '        </div>';
                                            echo '    </div>';
                                            echo '</div>';
                                        }
                                        ?>
                                    </div>

                                    <div class="alert alert-info" style="background-color: #e7f5f5; border-color: #b8e0e0; color: #31708f; padding: 15px; margin-top: 20px; border-radius: 4px;">
                                        <i class='bx bx-info-circle' style="margin-right: 10px; font-size: 20px; vertical-align: middle;"></i>
                                        <span>The Senate meets at least once every three months to discuss and decide on academic matters of the University.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Senate Committees Section -->
                    <article class="page-content" style="margin-top: 40px;">
                        <div class="section-title clearfix">
                            <h2>
                                <i class='bx bx-list-ul' style="color: #004225; margin-right: 10px;"></i>
                                <span>Senate Committees</span>
                            </h2>
                        </div>
                        <div class="box" style="background: #fff; padding: 25px; border-radius: 4px; box-shadow: 0 1px 5px rgba(0,0,0,0.03);">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="panel panel-default" style="border: 1px solid #e0e0e0; border-radius: 4px; margin-bottom: 20px;">
                                        <div class="panel-heading" style="background-color: #f5f5f5; padding: 10px 15px; border-bottom: 1px solid #e0e0e0;">
                                            <h4 style="margin: 0; font-size: 16px; color: #004225;">Academic Affairs Committee</h4>
                                        </div>
                                        <div class="panel-body" style="padding: 15px;">
                                            <p style="margin-bottom: 10px; color: #555; font-size: 14px; line-height: 1.5;">
                                                Oversees all academic programs, curriculum development, and academic policies.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="panel panel-default" style="border: 1px solid #e0e0e0; border-radius: 4px; margin-bottom: 20px;">
                                        <div class="panel-heading" style="background-color: #f5f5f5; padding: 10px 15px; border-bottom: 1px solid #e0e0e0;">
                                            <h4 style="margin: 0; font-size: 16px; color: #004225;">Research and Publications</h4>
                                        </div>
                                        <div class="panel-body" style="padding: 15px;">
                                            <p style="margin-bottom: 10px; color: #555; font-size: 14px; line-height: 1.5;">
                                                Promotes and coordinates research activities and publications within the University.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="panel panel-default" style="border: 1px solid #e0e0e0; border-radius: 4px; margin-bottom: 20px;">
                                        <div class="panel-heading" style="background-color: #f5f5f5; padding: 10px 15px; border-bottom: 1px solid #e0e0e0;">
                                            <h4 style="margin: 0; font-size: 16px; color: #004225;">Examinations Committee</h4>
                                        </div>
                                        <div class="panel-body" style="padding: 15px;">
                                            <p style="margin-bottom: 10px; color: #555; font-size: 14px; line-height: 1.5;">
                                                Oversees the conduct of examinations and ensures academic integrity.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="panel panel-default" style="border: 1px solid #e0e0e0; border-radius: 4px; margin-bottom: 20px;">
                                        <div class="panel-heading" style="background-color: #f5f5f5; padding: 10px 15px; border-bottom: 1px solid #e0e0e0;">
                                            <h4 style="margin: 0; font-size: 16px; color: #004225;">Quality Assurance</h4>
                                        </div>
                                        <div class="panel-body" style="padding: 15px;">
                                            <p style="margin-bottom: 10px; color: #555; font-size: 14px; line-height: 1.5;">
                                                Ensures quality standards in teaching, learning, and research.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                </section>
            </div>
            <!-- end Main Content -->

            <!-- Sidebar -->
            <div class="col-md-3">
                <aside class="sidebar">
                    <div class="box" style="background: #fff; padding: 25px; border-radius: 4px; box-shadow: 0 1px 5px rgba(0,0,0,0.03); margin-bottom: 30px;">
                        <h3 style="margin-top: 0; color: #004225; font-size: 18px; padding-bottom: 10px; border-bottom: 1px solid #eee; margin-bottom: 15px;">
                            <i class='bx bx-info-circle' style="margin-right: 8px;"></i>Quick Links
                        </h3>
                        <ul class="list-links" style="list-style: none; padding: 0; margin: 0;">
                            <li style="margin-bottom: 10px;">
                                <a href="about.php" style="color: #555; text-decoration: none; display: block; padding: 8px 0; border-bottom: 1px dashed #eee; transition: all 0.3s ease;">
                                    <i class='bx bx-chevron-right' style="margin-right: 8px; color: #004225;"></i> About Us
                                </a>
                            </li>
                            <li style="margin-bottom: 10px;">
                                <a href="vision_mission.php" style="color: #555; text-decoration: none; display: block; padding: 8px 0; border-bottom: 1px dashed #eee; transition: all 0.3s ease;">
                                    <i class='bx bx-chevron-right' style="margin-right: 8px; color: #004225;"></i> Vision & Mission
                                </a>
                            </li>
                            <li style="margin-bottom: 10px;">
                                <a href="board_of_trustees.php" style="color: #555; text-decoration: none; display: block; padding: 8px 0; border-bottom: 1px dashed #eee; transition: all 0.3s ease;">
                                    <i class='bx bx-chevron-right' style="margin-right: 8px; color: #004225;"></i> Board of Trustees
                                </a>
                            </li>
                            <li style="margin-bottom: 10px;">
                                <a href="principal_officers.php" style="color: #555; text-decoration: none; display: block; padding: 8px 0; border-bottom: 1px dashed #eee; transition: all 0.3s ease;">
                                    <i class='bx bx-chevron-right' style="margin-right: 8px; color: #004225;"></i> Principal Officers
                                </a>
                            </li>
                            <li style="margin-bottom: 10px;">
                                <a href="council_board.php" style="color: #555; text-decoration: none; display: block; padding: 8px 0; transition: all 0.3s ease;">
                                    <i class='bx bx-chevron-right' style="margin-right: 8px; color: #004225;"></i> Council Board
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="box" style="background: #f9f9f9; padding: 25px; border-radius: 4px; border-left: 4px solid #004225; margin-bottom: 30px;">
                        <h3 style="margin-top: 0; color: #004225; font-size: 18px; margin-bottom: 15px;">
                            <i class='bx bx-time' style="margin-right: 8px;"></i> Meeting Schedule
                        </h3>
                        <p style="margin: 0 0 15px 0; color: #555; font-size: 14px; line-height: 1.6;">
                            The Senate meets quarterly to discuss and decide on academic matters. Special meetings may be convened when necessary.
                        </p>
                        <ul style="list-style: none; padding: 0; margin: 0 0 15px 0; color: #555; font-size: 14px; line-height: 1.8;">
                            <li><strong>1st Quarter:</strong> March</li>
                            <li><strong>2nd Quarter:</strong> June</li>
                            <li><strong>3rd Quarter:</strong> September</li>
                            <li><strong>4th Quarter:</strong> December</li>
                        </ul>
                        <p style="margin: 0; color: #666; font-size: 13px; font-style: italic;">
                            Meeting minutes and resolutions are available at the University Secretariat.
                        </p>
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

<!-- Add any additional scripts here if needed -->

</body>
</html>
