<?php
$page_title = 'International Students';
$page_description = 'Information for international students at Zanvarsity';
$page_heading = 'International Students';
include_once 'includes/about_header.php';
?>

<!-- Page Content -->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="page-content">
                <h2>Welcome International Students!</h2>
                <p>Zanvarsity welcomes students from all over the world to join our diverse academic community. We are committed to providing a supportive environment for international students to achieve their academic and personal goals.</p>
                
                <div class="alert alert-info">
                    <p><strong>Important Notice:</strong> Due to COVID-19, some policies and procedures may have changed. Please check with the International Students Office for the latest updates.</p>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="info-section">
                            <h3>Why Choose Zanvarsity?</h3>
                            <p>Zanvarsity offers a world-class education with a global perspective. Here are some reasons why international students choose us:</p>
                            
                            <div class="row features">
                                <div class="col-md-6">
                                    <div class="feature-box">
                                        <i class="fa fa-graduation-cap"></i>
                                        <h4>Quality Education</h4>
                                        <p>Internationally recognized programs taught by experienced faculty members.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-box">
                                        <i class="fa fa-globe"></i>
                                        <h4>Global Community</h4>
                                        <p>Join a diverse community of students from over 30 countries.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-box">
                                        <i class="fa fa-money"></i>
                                        <h4>Affordable Tuition</h4>
                                        <p>Competitive tuition fees compared to other international institutions.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-box">
                                        <i class="fa fa-home"></i>
                                        <h4>Student Support</h4>
                                        <p>Dedicated international student support services.</p>
                                    </div>
                                </div>
                            </div>
                            
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once 'includes/about_footer.php'; ?>

</div>

<style>
.info-section {
    margin-bottom: 40px;
}

.features {
    margin: 20px 0;
}

.feature-box {
    text-align: center;
    padding: 20px 10px;
    margin-bottom: 20px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.feature-box:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transform: translateY(-5px);
}

.feature-box i {
    font-size: 40px;
    color: #004225;
    margin-bottom: 15px;
    display: block;
}

.feature-box h4 {
    color: #004225;
    margin-top: 0;
}

.panel {
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.panel-heading {
    padding: 0;
    border-top-left-radius: 4px;
    border-top-right-radius: 4px;
}

.panel-title {
    font-size: 15px;
    margin: 0;
}

.panel-title a {
    display: block;
    padding: 12px 15px;
    color: #333;
    text-decoration: none;
    transition: all 0.3s ease;
}

.panel-title a:hover, 
.panel-title a:focus {
    background-color: #f5f5f5;
    color: #004225;
    text-decoration: none;
}

.panel-title a:after {
    content: '\f107';
    font-family: 'FontAwesome';
    float: right;
    transition: all 0.3s ease;
}

.panel-title a.collapsed:after {
    content: '\f105';
}

.panel-body {
    padding: 15px;
    background-color: #fff;
}

.alert {
    margin: 20px 0;
    padding: 15px;
    border-radius: 4px;
}

.alert-info {
    background-color: #d9edf7;
    border-color: #bce8f1;
    color: #31708f;
}

.alert-warning {
    background-color: #fcf8e3;
    border-color: #faebcc;
    color: #8a6d3b;
}

.table > thead > tr > th {
    background-color: #f5f5f5;
    border-bottom: 2px solid #ddd;
}

.sidebar-widget {
    background-color: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.sidebar-widget h3 {
    color: #004225;
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
    font-size: 18px;
}

.list-group-item {
    border: 1px solid #eee;
    margin-bottom: -1px;
}

.list-group-item a {
    color: #555;
    text-decoration: none;
}

.list-group-item a:hover {
    color: #004225;
}

.event {
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
}

.event:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.event-date {
    width: 50px;
    text-align: center;
    margin-right: 15px;
    background-color: #f5f5f5;
    padding: 5px;
    border-radius: 4px;
}

.event-date .day {
    font-size: 18px;
    font-weight: bold;
    display: block;
    color: #004225;
}

.event-date .month {
    font-size: 12px;
    text-transform: uppercase;
    color: #777;
}

.event-details {
    flex: 1;
}

.event-details h5 {
    margin-top: 0;
    margin-bottom: 5px;
    font-size: 14px;
    color: #333;
}

.event-details p {
    font-size: 12px;
    color: #777;
    margin-bottom: 10px;
}

.testimonial {
    background-color: #f9f9f9;
    padding: 15px;
    border-left: 3px solid #004225;
    margin-bottom: 20px;
    font-style: italic;
}

.testimonial p {
    margin-bottom: 10px;
}

.testimonial-author {
    font-style: normal;
}

.testimonial-author strong {
    color: #004225;
}

.testimonial-author em {
    font-size: 12px;
    color: #777;
}

.social-links {
    margin-bottom: 20px;
}

.btn-social-icon {
    width: 35px;
    height: 35px;
    padding: 0;
    margin-right: 5px;
    margin-bottom: 5px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
}

.btn-facebook {
    background-color: #3b5998;
}

.btn-twitter {
    background-color: #1da1f2;
}

.btn-instagram {
    background-color: #e1306c;
}

.btn-linkedin {
    background-color: #0077b5;
}

.btn-youtube {
    background-color: #ff0000;
}

.newsletter h4 {
    font-size: 14px;
    margin-top: 0;
    margin-bottom: 15px;
    color: #555;
}

.newsletter .form-control {
    border-radius: 4px 0 0 4px;
}

.newsletter .btn {
    border-radius: 0 4px 4px 0;
}

.office-hours {
    background-color: #f9f9f9;
    padding: 15px;
    border-radius: 4px;
    margin-top: 20px;
}

.office-hours h4 {
    color: #004225;
    margin-top: 0;
    font-size: 16px;
}

@media (max-width: 768px) {
    .feature-box {
        margin-bottom: 20px;
    }
    
    .panel-title a {
        padding: 10px 12px;
        font-size: 14px;
    }
    
    .sidebar-widget {
        margin-bottom: 20px;
    }
}
</style>

