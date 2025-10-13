<?php
$page_title = 'Payment Instructions';
$page_description = 'Learn how to make payments for your studies at Zanzibar University';
$page_heading = 'Payment Instructions';
include_once 'includes/about_header.php';
?>

<!-- Page Content -->
<div class="container-fluid" style="padding: 0;">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="page-content">
                    <h2>Payment Instructions</h2>
                    <div class="alert alert-warning">
                        <h4>Please read them carefully.</h4>
                        <p>No Payment will be Allowed if not followed the Stipulated Channel.</p>
                        <p>The Fee Structure is subjected to change from time to time by the University Council.</p>
                        <p>Access to Studies and Examination is only granted to students who have paid their allocated installments accordingly.</p>
                    </div>
                
                    <!-- Step 1: Generate Control Number -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="step-number bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold; font-size: 1.2rem;">Step</div>
                                <h4 class="mb-0 ms-3">01</h4>
                            </div>
                            <h4>Generate Control Number</h4>
                            <p class="mb-0">The link for generating Control Number is: 
                                <a href="https://www.billing.zumis.ac.tz" target="_blank" class="fw-bold">www.billing.zumis.ac.tz</a>
                            </p>
                        </div>
                    </div>

                    <!-- Step 2: Choose Mode of Payment -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="step-number bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold; font-size: 1.2rem;">Step</div>
                                <h4 class="mb-0 ms-3">02</h4>
                            </div>
                            <h4>Choose Mode of Payment</h4>
                            <p class="mb-3">After generating the Control Number you can pay using one of the 3 following modes.</p>
                            
                            <!-- Payment Methods -->
                            <div class="row">
                                <!-- PBZ Bank -->
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="step-number bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold; font-size: 1.2rem;">Step</div>
                                                <h4 class="mb-0 ms-3">03</h4>
                                            </div>
                                            <h5>PBZ Bank</h5>
                                            <p class="mb-0">Visit any PBZ Bank Branch or agent and provide your money and Control Number.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Airtel Money -->
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="step-number bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold; font-size: 1.2rem;">Step</div>
                                                <h4 class="mb-0 ms-3">04</h4>
                                            </div>
                                            <h5>Airtel Money</h5>
                                            <p class="mb-3">You can pay directly using Airtel Money by following this link - 
                                                <a href="https://www.billing.zumis.ac.tz/verifyBill" target="_blank" class="fw-bold">Pay Using Airtel Money</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tigo Pesa -->
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="step-number bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold; font-size: 1.2rem;">Step</div>
                                                <h4 class="mb-0 ms-3">05</h4>
                                            </div>
                                            <h5>Tigo Pesa</h5>
                                            <ol class="ps-3">
                                                <li>Dial Tigo Pesa Menu *150*01#</li>
                                                <li>Select Number 4. Lipa Bili</li>
                                                <li>Select Number 3. Ingiza Number ya Kampuni</li>
                                                <li>Enter Our Company Number - 244099</li>
                                                <li>Enter the exact Amount as shown in the Control Number Details</li>
                                                <li>Enter the Control Number Provided</li>
                                                <li>Enter Your Tigo Pesa PIN to Authorize Payment</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <!-- Important Notes -->
                    <div class="alert alert-info mt-4">
                        <h5><i class="fa fa-info-circle me-2"></i>Important Notes:</h5>
                        <ul class="mb-0">
                            <li>Always keep your payment receipt as proof of payment.</li>
                            <li>Payments may take up to 24-48 hours to reflect in the system.</li>
                            <li>For any payment issues, contact the Finance Department at <a href="mailto:finance@zanvarsity.ac.tz" class="fw-bold">finance@zanvarsity.ac.tz</a> or call +255 24 223 2124.</li>
                        </ul>
                    </div>
                            <li>Installment plan attracts a 5% administrative fee</li>
                            <li>All fees must be cleared before taking final examinations</li>
                        </ul>
                    </div>
                </div>
                
                <div class="section">
                    <h3>Payment Deadlines</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Semester</th>
                                    <th>Registration Period</th>
                                    <th>50% Payment Deadline</th>
                                    <th>Full Payment Deadline</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Fall 2023</td>
                                    <td>August 15 - September 15</td>
                                    <td>September 15</td>
                                    <td>October 15</td>
                                </tr>
                                <tr>
                                    <td>Spring 2024</td>
                                    <td>January 5 - January 25</td>
                                    <td>January 25</td>
                                    <td>February 25</td>
                                </tr>
                                <tr>
                                    <td>Summer 2024</td>
                                    <td>May 10 - May 30</td>
                                    <td>May 30</td>
                                    <td>June 30</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h5>Late Payment Penalties:</h5>
                        <ul>
                            <li>A late payment fee of 5% will be charged for payments received after the deadline</li>
                            <li>Students with outstanding balances may be denied access to academic services</li>
                            <li>Transcripts and certificates will not be issued until all financial obligations are met</li>
                        </ul>
                    </div>
                </div>
                
                <div class="section">
                    <h3>Payment Receipts & Invoices</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Requesting Receipts</h5>
                                </div>
                                <div class="card-body">
                                    <p>Official receipts are automatically generated for all payments made through our online portal. For other payment methods:</p>
                                    <ol>
                                        <li>Email a copy of your payment proof to <a href="mailto:receipts@zanvarsity.ac.tz">receipts@zanvarsity.ac.tz</a></li>
                                        <li>Include your full name and student ID in the email</li>
                                        <li>Allow 3-5 business days for processing</li>
                                        <li>Receipts will be sent to your student email address</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">View Payment History</h5>
                                </div>
                                <div class="card-body">
                                    <p>You can view your payment history and download receipts through the student portal:</p>
                                    <ol>
                                        <li>Log in to the <a href="https://portal.zanvarsity.ac.tz" target="_blank">Student Portal</a></li>
                                        <li>Navigate to "My Finances"</li>
                                        <li>Select "Payment History"</li>
                                        <li>View or download your receipts</li>
                                    </ol>
                                    <a href="https://portal.zanvarsity.ac.tz" class="btn btn-outline-success mt-2" target="_blank">Access Student Portal</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="section">
                    <h3>Contact Information</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="contact-box">
                                <h4>Finance Office</h4>
                                <address>
                                    <p><i class="fa fa-building"></i> Administration Building, Ground Floor<br>
                                    Zanvarsity University<br>
                                    P.O. Box 12345<br>
                                    Dar es Salaam, Tanzania</p>
                                    <p><i class="fa fa-phone"></i> +255 22 123 4567<br>
                                    <i class="fa fa-envelope"></i> <a href="mailto:finance@zanvarsity.ac.tz">finance@zanvarsity.ac.tz</a><br>
                                    <i class="fa fa-clock-o"></i> Monday - Friday: 8:00 AM - 4:00 PM</p>
                                </address>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="contact-box">
                                <h4>Student Accounts</h4>
                                <address>
                                    <p><i class="fa fa-building"></i> Student Services Center<br>
                                    Zanvarsity University<br>
                                    P.O. Box 12345<br>
                                    Dar es Salaam, Tanzania</p>
                                    <p><i class="fa fa-phone"></i> +255 22 123 4568<br>
                                    <i class="fa fa-envelope"></i> <a href="mailto:student.accounts@zanvarsity.ac.tz">student.accounts@zanvarsity.ac.tz</a><br>
                                    <i class="fa fa-clock-o"></i> Monday - Friday: 8:30 AM - 3:30 PM</p>
                                </address>
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
    margin-bottom: 40px;
    padding: 25px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0 0 15px rgba(0,0,0,0.05);
}

h2 {
    color: #004225;
    margin-bottom: 25px;
    padding-bottom: 10px;
    border-bottom: 2px solid #eaeaea;
}

h3 {
    color: #006633;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eaeaea;
}

h4 {
    color: #004225;
    margin-top: 20px;
    margin-bottom: 15px;
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

.bank-details {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    height: 100%;
    border: 1px solid #e9ecef;
}

.payment-option {
    text-align: center;
    padding: 20px;
    border: 1px solid #e9ecef;
    border-radius: 5px;
    height: 100%;
    transition: all 0.3s ease;
}

.payment-option:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transform: translateY(-5px);
}

.payment-icon {
    color: #004225;
    margin-bottom: 15px;
}

.online-payment {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
}

.card-icons {
    margin-top: 15px;
}

.card-icons i {
    margin: 0 10px;
    color: #555;
}

.benefits-list {
    list-style: none;
    padding-left: 0;
}

.benefits-list li {
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.benefits-list li:last-child {
    border-bottom: none;
}

.contact-box {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    height: 100%;
    border-left: 4px solid #004225;
}

.contact-box i {
    width: 20px;
    text-align: center;
    margin-right: 10px;
    color: #006633;
}

.table th {
    background-color: #004225;
    color: white;
}

.btn-primary {
    background-color: #004225;
    border-color: #004225;
}

.btn-primary:hover {
    background-color: #006633;
    border-color: #006633;
}

@media (max-width: 768px) {
    .payment-option {
        margin-bottom: 20px;
    }
    
    .contact-box {
        margin-bottom: 20px;
    }
}
</style>

<?php include_once 'includes/about_footer.php'; ?>