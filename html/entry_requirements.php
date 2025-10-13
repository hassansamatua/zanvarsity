<?php
$page_title = 'Entry Requirements';
$page_description = 'Detailed entry requirements for all programs at Zanzibar University';
$page_heading = 'Entry Requirements';
include_once 'includes/about_header.php';
?>

<!-- Page Content -->
<div class="container-fluid" style="padding: 0;">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="page-content">
                    <h2>Entry Qualifications for Joining Zanzibar University - 2025/2026</h2>
                    
                    <div class="alert alert-info">
                        <p>Please download the relevant guidebook for detailed information about entry requirements for each program category. All applicants must meet the minimum entry requirements set by the Tanzania Commission for Universities (TCU) and the National Council for Technical Education (NACTE) for their respective programs.</p>
                    </div>
                
                <div class="row">
                    <!-- Non-Degree Programmes -->
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="icon-box">
                                    <i class="fa fa-certificate fa-3x text-primary mb-3"></i>
                                </div>
                                <h5 class="card-title">Non-Degree Programmes</h5>
                                <p class="card-text">Certificate and Diploma Programs</p>
                                <a href="uploads/doc/GuideBook - Non Degree 2023 2024.pdf" target="_blank" class="btn btn-outline-primary">
                                    <i class="fa fa-download me-2"></i>Download Guidebook
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Undergraduate (Form 6) -->
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="icon-box">
                                    <i class="fa fa-graduation-cap fa-3x text-success mb-3"></i>
                                </div>
                                <h5 class="card-title">Undergraduate Programmes</h5>
                                <p class="card-text">Direct Entry - Form 6</p>
                                <a href="uploads/doc/Admission Guidebook - Form 6.pdf" target="_blank" class="btn btn-outline-success">
                                    <i class="fa fa-download me-2"></i>Download Guidebook
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Undergraduate (Diploma) -->
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="icon-box">
                                    <i class="fa fa-book fa-3x text-info mb-3"></i>
                                </div>
                                <h5 class="card-title">Undergraduate Programmes</h5>
                                <p class="card-text">Equivalent - Diploma/Foundation</p>
                                <a href="uploads/doc/Admissions Guidebook - Diploma.pdf" target="_blank" class="btn btn-outline-info">
                                    <i class="fa fa-download me-2"></i>Download Guidebook
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Postgraduate Programmes -->
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="icon-box">
                                    <i class="fa fa-user-graduate fa-3x text-warning mb-3"></i>
                                </div>
                                <h5 class="card-title">Postgraduate Programmes</h5>
                                <p class="card-text">Postgraduate Diploma, Masters and PhD</p>
                                <a href="uploads/doc/GuideBook - Postgraduate 2023 2024.pdf" target="_blank" class="btn btn-outline-warning">
                                    <i class="fa fa-download me-2"></i>Download Guidebook
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                
                <div class="section">
                    <h3>Contact Information</h3>
                    <p>For more information about entry requirements, please contact:</p>
                    <address>
                        Zanvarsity University<br>
                        P.O. Box 12345<br>
                        Dar es Salaam, Tanzania<br>
                        <abbr title="Phone">Phone:</abbr> +255 22 123 4567<br>
                        <abbr title="Email">Email:</abbr> admissions@zanvarsity.ac.tz<br>
                        <abbr title="Working Hours">Hours:</abbr> Monday - Friday, 8:00 AM - 4:00 PM
                    </address>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-box {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: rgba(0, 66, 37, 0.1);
}

.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 10px;
    overflow: hidden;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}

.section {
    margin-bottom: 40px;
    padding: 20px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.table {
    margin-bottom: 20px;
}

thead th {
    background-color: #004225;
    color: white;
}

.alert {
    border-radius: 5px;
    margin: 20px 0;
    padding: 20px;
}

.alert-info {
    background-color: #e7f4ff;
    border-left: 5px solid #0066cc;
}

.alert-warning {
    background-color: #fff8e6;
    border-left: 5px solid #ff9900;
}

address {
    font-style: normal;
    line-height: 1.6;
}

abbr[title] {
    border-bottom: 1px dotted #666;
    cursor: help;
}

h3 {
    color: #004225;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #eaeaea;
}

h4 {
    color: #006633;
    margin: 15px 0;
}

ul, ol {
    padding-left: 20px;
}

li {
    margin-bottom: 8px;
}
</style>

<?php include_once 'includes/about_footer.php'; ?>