<?php
$page_heading = 'Frequently Asked Questions';
$page_description = 'Find answers to common questions about Zanzibar University programs, admissions, and student life';

// Include header
include_once 'includes/about_header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-lg-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">FAQ</li>
                </ol>
            </nav>
            
            <h1 class="mb-4">Frequently Asked Questions</h1>
            
            <div class="accordion" id="faqAccordion">
                <!-- Admissions -->
                <div class="card mb-3">
                    <div class="card-header" id="headingAdmissions">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseAdmissions" aria-expanded="true" aria-controls="collapseAdmissions">
                                Admissions
                            </button>
                        </h2>
                    </div>
                    <div id="collapseAdmissions" class="collapse show" aria-labelledby="headingAdmissions" data-parent="#faqAccordion">
                        <div class="card-body">
                            <div class="faq-item mb-4">
                                <h5>What are the admission requirements for undergraduate programs?</h5>
                                <p>Admission requirements vary by program, but generally include a completed secondary education certificate with specific subject passes. Please check our <a href="admissions.php">admissions page</a> for specific program requirements.</p>
                            </div>
                            
                            <div class="faq-item mb-4">
                                <h5>How do I apply for admission?</h5>
                                <p>Applications can be submitted online through our <a href="apply.php">online application portal</a>. You'll need to create an account, fill out the application form, and upload the required documents.</p>
                            </div>
                            
                            <div class="faq-item">
                                <h5>What is the application deadline?</h5>
                                <p>Application deadlines vary by program and intake. Please refer to our <a href="academic_calendar.php">academic calendar</a> for specific dates.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Academic Programs -->
                <div class="card mb-3">
                    <div class="card-header" id="headingPrograms">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapsePrograms" aria-expanded="false" aria-controls="collapsePrograms">
                                Academic Programs
                            </button>
                        </h2>
                    </div>
                    <div id="collapsePrograms" class="collapse" aria-labelledby="headingPrograms" data-parent="#faqAccordion">
                        <div class="card-body">
                            <div class="faq-item mb-4">
                                <h5>What programs does Zanzibar University offer?</h5>
                                <p>We offer a wide range of undergraduate, graduate, and postgraduate programs across various faculties. Visit our <a href="programs.php">programs page</a> for a complete list of offerings.</p>
                            </div>
                            
                            <div class="faq-item">
                                <h5>Are there any online or distance learning programs available?</h5>
                                <p>Yes, we offer several programs through distance learning. Check our <a href="distance_learning.php">distance learning page</a> for available programs and requirements.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tuition & Financial Aid -->
                <div class="card mb-3">
                    <div class="card-header" id="headingTuition">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseTuition" aria-expanded="false" aria-controls="collapseTuition">
                                Tuition & Financial Aid
                            </button>
                        </h2>
                    </div>
                    <div id="collapseTuition" class="collapse" aria-labelledby="headingTuition" data-parent="#faqAccordion">
                        <div class="card-body">
                            <div class="faq-item mb-4">
                                <h5>What is the tuition fee for [specific program]?</h5>
                                <p>Tuition fees vary by program and student status (local/international). Please visit our <a href="fee_structure.php">fee structure page</a> for detailed information.</p>
                            </div>
                            
                            <div class="faq-item">
                                <h5>What financial aid options are available?</h5>
                                <p>We offer various financial aid options including scholarships, grants, and student loans. Visit our <a href="financial_aid.php">financial aid page</a> for more information.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Student Life -->
                <div class="card mb-3">
                    <div class="card-header" id="headingStudentLife">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseStudentLife" aria-expanded="false" aria-controls="collapseStudentLife">
                                Student Life
                            </button>
                        </h2>
                    </div>
                    <div id="collapseStudentLife" class="collapse" aria-labelledby="headingStudentLife" data-parent="#faqAccordion">
                        <div class="card-body">
                            <div class="faq-item mb-4">
                                <h5>What accommodation options are available for students?</h5>
                                <p>We offer both on-campus and off-campus housing options. Visit our <a href="accommodation.php">accommodation page</a> for more details and application procedures.</p>
                            </div>
                            
                            <div class="faq-item">
                                <h5>What student organizations and activities are available?</h5>
                                <p>There are numerous student organizations, clubs, and activities available. Check out our <a href="student_life.php">student life section</a> to learn more about getting involved.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- International Students -->
                <div class="card mb-3">
                    <div class="card-header" id="headingInternational">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseInternational" aria-expanded="false" aria-controls="collapseInternational">
                                International Students
                            </button>
                        </h2>
                    </div>
                    <div id="collapseInternational" class="collapse" aria-labelledby="headingInternational" data-parent="#faqAccordion">
                        <div class="card-body">
                            <div class="faq-item mb-4">
                                <h5>What are the English language requirements for international students?</h5>
                                <p>International students whose first language is not English must provide proof of English proficiency. Please see our <a href="international_students.php">international students page</a> for specific requirements.</p>
                            </div>
                            
                            <div class="faq-item">
                                <h5>What support services are available for international students?</h5>
                                <p>We offer various support services including orientation programs, visa assistance, and academic support. Visit our <a href="international_services.php">international services page</a> for more information.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-5">
                <h3 class="mb-3">Still have questions?</h3>
                <p>If you can't find the answer to your question in our FAQ, please feel free to <a href="contact.php">contact us</a> directly.</p>
            </div>
        </div>
    </div>
    <?php
// Include footer
include_once 'includes/about_footer.php';
?>
</div>


