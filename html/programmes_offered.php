<?php
$page_title = 'Programmes Offered';
$page_description = 'Explore the wide range of academic programs offered at Zanzibar University';
$page_heading = 'Programmes Offered';
include_once 'includes/about_header.php';
?>

<!-- Page Content -->
<div class="container-fluid" style="padding: 0;">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="page-content">
                    <h2>Programmes Offered</h2>
                    
                    <div class="alert alert-info">
                        <p>Zanzibar University offers many reputable programmes that are in demand for the needs of the vast changing world of today. The programmes were developed in different specializations with the sole intention of establishing professionals in various fields who will serve their communities effectively.</p>
                    </div>
                    
                    <div class="d-flex justify-content-center mb-4">
                        <a href="#postgraduate" class="btn btn-outline-primary mx-2">Postgraduate Programmes</a>
                        <a href="#undergraduate" class="btn btn-outline-success mx-2">Undergraduate Programmes</a>
                        <a href="#non-degree" class="btn btn-outline-info mx-2">Non-Degree Programmes</a>
                    </div>

                    <!-- Postgraduate Programmes -->
                    <div class="section" id="postgraduate">
                        <h3 class="mb-4"><i class="fa fa-user-graduate text-primary me-2"></i>Postgraduate Programmes</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Program Name</th>
                                        <th>Specializations</th>
                                        <th>Duration</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Master of Business Administration</td>
                                        <td>Finance and Investment, Marketing, Information Technology</td>
                                        <td>2 Years</td>
                                        <td><a href="https://www.zumis.ac.tz/admission/data/register" target="_blank" class="btn btn-sm btn-primary">Apply Now</a></td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Master of Public Administration</td>
                                        <td>Human Resource Management, Local Government Administration, Public Policy Management</td>
                                        <td>2 Years</td>
                                        <td><a href="https://www.zumis.ac.tz/admission/data/register" target="_blank" class="btn btn-sm btn-primary">Apply Now</a></td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Master of Science in Economics and Finance</td>
                                        <td>Project Planning and Management, Economic Policy and Planning, Economic Financial Analysis</td>
                                        <td>2 Years</td>
                                        <td><a href="https://www.zumis.ac.tz/admission/data/register" target="_blank" class="btn btn-sm btn-primary">Apply Now</a></td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>Master of Law in Comparative Laws</td>
                                        <td>None</td>
                                        <td>2 Years</td>
                                        <td><a href="https://www.zumis.ac.tz/admission/data/register" target="_blank" class="btn btn-sm btn-primary">Apply Now</a></td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>Master of Islamic Banking and Finance</td>
                                        <td>None</td>
                                        <td>2 Years</td>
                                        <td><a href="https://www.zumis.ac.tz/admission/data/register" target="_blank" class="btn btn-sm btn-primary">Apply Now</a></td>
                                    </tr>
                                    <tr>
                                        <td>6</td>
                                        <td>Postgraduate Diploma in Islamic Banking and Finance</td>
                                        <td>None</td>
                                        <td>1 Year</td>
                                        <td><a href="https://www.zumis.ac.tz/admission/data/register" target="_blank" class="btn btn-sm btn-primary">Apply Now</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Undergraduate Programmes -->
                    <div class="section" id="undergraduate">
                        <h3 class="mb-4"><i class="fa fa-graduation-cap text-success me-2"></i>Undergraduate Programmes</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Program Name</th>
                                        <th>Specializations</th>
                                        <th>Duration</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $undergradPrograms = [
                                        [1, 'Bachelor of Business Administration in Accounting and Finance', 'None', '3 Years'],
                                        [2, 'Bachelor of Business Administration in Marketing', 'None', '3 Years'],
                                        [3, 'Bachelor of Procurement and Logistics Management', 'None', '3 Years'],
                                        [4, 'Bachelor of Science in Business Information Technology', 'None', '3 Years'],
                                        [5, 'Bachelor of Arts in Economics', 'None', '3 Years'],
                                        [6, 'Bachelor of Arts in Public Administration', 'None', '3 Years'],
                                        [7, 'Bachelor of Arts in Languages', 'None', '3 Years'],
                                        [8, 'Bachelor of International Relations and Diplomacy', 'None', '3 Years'],
                                        [9, 'Bachelor of Mass Communication', 'None', '3 Years'],
                                        [10, 'Bachelor of Information Studies', 'Archives and Records, Library Management', '3 Years'],
                                        [11, 'Bachelor of Social Work', 'None', '3 Years'],
                                        [12, 'Bachelor of Islamic Banking and Finance', 'None', '3 Years'],
                                        [13, 'Bachelor of Science in Computer Engineering and Information Technology', 'None', '4 Years'],
                                        [14, 'Bachelor of Science in Telecommunications Engineering', 'None', '4 Years'],
                                        [15, 'Bachelor of Laws', 'None', '4 Years'],
                                        [16, 'Bachelor of Science with Education', 'Biology - Geography, Biology - Chemistry, Biology - Math, Biology - Physics, Biology - IT, Chemistry - Physics, Chemistry - IT, Chemistry - Mathematics, Physics - IT, Geography - IT, Mathematics - Physics, Mathematics - IT', '3 Years'],
                                        [17, 'Bachelor of Science in Nursing', 'None', '4 Years'],
                                        [18, 'Bachelor of Science in Counselling Psychology', 'None', '4 Years'],
                                        [19, 'Bachelor of Science in Public Health', 'None', '3 Years']
                                    ];
                                    
                                    foreach ($undergradPrograms as $program) {
                                        echo "<tr>";
                                        echo "<td>" . $program[0] . "</td>";
                                        echo "<td>" . $program[1] . "</td>";
                                        echo "<td>" . $program[2] . "</td>";
                                        echo "<td>" . $program[3] . "</td>";
                                        echo "<td><a href='https://www.zumis.ac.tz/admission/data/register' target='_blank' class='btn btn-sm btn-success'>Apply Now</a></td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Non-Degree Programmes -->
                    <div class="section" id="non-degree">
                        <h3 class="mb-4"><i class="fa fa-certificate text-info me-2"></i>Non-Degree Programmes</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Program Name</th>
                                        <th>Specializations</th>
                                        <th>Duration</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $nonDegreePrograms = [
                                        [1, 'Diploma in Business Administration', 'None', '2 Years'],
                                        [2, 'Diploma in Business Information Technology', 'None', '2 Years'],
                                        [3, 'Diploma in Law', 'None', '2 Years'],
                                        [4, 'Diploma in Counselling Psychology', 'None', '3 Years'],
                                        [5, 'Diploma in Nursing and Midwifery', 'None', '3 Years'],
                                        [6, 'Diploma in Clinical Medicine', 'None', '3 Years'],
                                        [7, 'Diploma in Medical Laboratory', 'None', '3 Years'],
                                        [8, 'Diploma in Science with Education - Primary Education', 'None', '3 Years'],
                                        [9, 'Diploma in Science with Education - Secondary Education', 'None', '3 Years'],
                                        [10, 'Certificate in Business Administration', 'None', '1 Year'],
                                        [11, 'Certificate in Business Information Technology', 'None', '1 Year'],
                                        [12, 'Certificate in Law', 'None', '1 Year']
                                    ];
                                    
                                    foreach ($nonDegreePrograms as $program) {
                                        echo "<tr>";
                                        echo "<td>" . $program[0] . "</td>";
                                        echo "<td>" . $program[1] . "</td>";
                                        echo "<td>" . $program[2] . "</td>";
                                        echo "<td>" . $program[3] . "</td>";
                                        echo "<td><a href='https://www.zumis.ac.tz/admission/data/register' target='_blank' class='btn btn-sm btn-info text-white'>Apply Now</a></td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="section mt-5">
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fa fa-file-alt fa-3x text-primary mb-3"></i>
                                        <h5 class="card-title">Entry Requirements</h5>
                                        <p class="card-text">View detailed entry requirements for all programs</p>
                                        <a href="entry_requirements.php" class="btn btn-outline-primary">
                                            <i class="fa fa-arrow-right me-2"></i>View Requirements
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fa fa-money-bill-wave fa-3x text-success mb-3"></i>
                                        <h5 class="card-title">Fee Structure</h5>
                                        <p class="card-text">Download fee structure for local and international students</p>
                                        <div class="btn-group" role="group">
                                            <a href="https://www.zanvarsity.ac.tz/site/important/FEE%20STRUCTURE%20FOR%20ACADEMIC%20YEAR%202024-2025.pdf" target="_blank" class="btn btn-outline-success btn-sm">TZS</a>
                                            <a href="https://www.zanvarsity.ac.tz/site/important/FEE%20STRUCTURE%202024-2025.pdf" target="_blank" class="btn btn-outline-success btn-sm">USD</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fa fa-credit-card fa-3x text-info mb-3"></i>
                                        <h5 class="card-title">Payment Mode</h5>
                                        <p class="card-text">Learn about our payment options and procedures</p>
                                        <a href="payment.php" class="btn btn-outline-info">
                                            <i class="fa fa-arrow-right me-2"></i>View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.section {
    margin: 60px 0;
    padding: 20px 0;
    border-bottom: 1px solid #eee;
}

.section:last-child {
    border-bottom: none;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #495057;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 66, 37, 0.03);
}

.btn-sm {
    padding: 0.25rem 0.75rem;
    font-size: 0.875rem;
}

/* Responsive table */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* Back to top button */
.back-to-top {
    position: fixed;
    bottom: 20px;
    right: 20px;
    display: none;
    z-index: 1000;
}

/* Navigation buttons */
.program-nav {
    position: sticky;
    top: 70px;
    background: white;
    z-index: 100;
    padding: 10px 0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }
    .table {
        font-size: 12px;
    }
}
</style>

<!-- Back to top button -->
<button onclick="topFunction()" id="backToTop" class="btn btn-primary back-to-top" title="Go to top">
    <i class="fa fa-arrow-up"></i>
</button>

<script>
// Back to top button
let mybutton = document.getElementById("backToTop");
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        mybutton.style.display = "block";
    } else {
        mybutton.style.display = "none";
    }
}

function topFunction() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
}
</script>

<?php include_once 'includes/about_footer.php'; ?>