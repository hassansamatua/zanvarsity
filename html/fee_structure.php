<?php
$page_title = 'Fee Structure';
$page_description = 'Detailed fee structure for all programs at Zanvarsity University';
$page_heading = 'Fee Structure';
include_once 'includes/about_header.php';
?>

<!-- Page Content -->
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold text-primary mb-3">Fee Structure</h1>
        <p class="lead text-dark">Access our comprehensive fee structure for the academic year 2025/2026</p>
    </div>

    <div class="row g-4 justify-content-center">
        <!-- Local Students Fee Structure -->
        <div class="col-lg-5 col-md-6">
            <div class="card h-100 border-0 shadow-lg hover-lift">
                <div class="card-header bg-primary text-white py-4">
                    <h2 class="h4 mb-0 text-center">Fee Structure: 2025/2026</h2>
                    <p class="mb-0 text-center">(Tanzanian Shillings - TZS)</p>
                </div>
                <div class="card-body p-4 text-center d-flex flex-column">
                    <div class="display-4 text-primary mb-3">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <h3 class="h5 mb-4 text-dark">Local Students</h3>
                    <p class="text-dark mb-4">Download the complete fee structure for local students for the academic year 2025/2026.</p>
                    <div class="mt-auto d-flex gap-2 justify-content-center">
                        <a href="http://localhost/c/zanvarsity/html/uploads/doc/feeTZ.pdf" 
                           class="btn btn-outline-primary flex-grow-1" 
                           target="_blank"
                           aria-label="View Local Students Fee Structure">
                            <i class="far fa-eye me-2"></i>View PDF
                        </a>
                        <a href="http://localhost/c/zanvarsity/html/uploads/doc/feeTZ.pdf" 
                           class="btn btn-primary flex-grow-1" 
                           download="Zanvarsity_Fee_Structure_2025_2026_TZS.pdf"
                           aria-label="Download Local Students Fee Structure PDF">
                            <i class="fas fa-download me-2"></i>Download
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- International Students Fee Structure -->
        <div class="col-lg-5 col-md-6">
            <div class="card h-100 border-0 shadow-lg hover-lift">
                <div class="card-header bg-success text-white py-4">
                    <h2 class="h4 mb-0 text-center">Fee Structure for Foreign Students</h2>
                    <p class="mb-0 text-center">(US Dollars - USD)</p>
                </div>
                <div class="card-body p-4 text-center d-flex flex-column">
                    <div class="display-4 text-success mb-3">
                        <i class="fas fa-globe-americas"></i>
                    </div>
                    <h3 class="h5 mb-4 text-dark">International Students</h3>
                    <p class="text-dark mb-4">Download the complete fee structure for international students for the academic year 2025/2026.</p>
                    <div class="mt-auto d-flex gap-2 justify-content-center">
                        <a href="http://localhost/c/zanvarsity/html/uploads/doc/feeUSD.pdf" 
                           class="btn btn-outline-success flex-grow-1" 
                           target="_blank"
                           aria-label="View International Students Fee Structure">
                            <i class="far fa-eye me-2"></i>View PDF
                        </a>
                        <a href="http://localhost/c/zanvarsity/html/uploads/doc/feeUSD.pdf" 
                           class="btn btn-success flex-grow-1" 
                           download="Zanvarsity_International_Fee_Structure_2025_2026_USD.pdf"
                           aria-label="Download International Students Fee Structure PDF">
                            <i class="fas fa-download me-2"></i>Download
                        </a>
                    </div>
                </div>
            </div>
           
        </div>
        
    </div>
    
            <?php include_once 'includes/about_footer.php'; ?>
        </div>
                       
<style>
/* Base Styles */
:root {
    --primary-color: #004225;
    --secondary-color: #28a745;
    --text-dark: #2c3e50;
    --text-muted: #6c757d;
    --light-gray: #f8f9fa;
    --white: #ffffff;
    --shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    --transition: all 0.3s ease;
}

body {
    color: var(--text-dark);
    line-height: 1.6;
}

/* Card Styles */
.card {
    border: none;
    transition: var(--transition);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.card-header {
    background-color: var(--primary-color);
    color: var(--white);
    border: none;
    padding: 1.5rem;
}

.card-header h2, .card-header h3 {
    color: var(--white);
    margin: 0;
    font-weight: 600;
}

.card-body {
    padding: 2rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

/* Button Styles */
.btn {
    font-weight: 600;
    letter-spacing: 0.5px;
    padding: 0.6rem 0.75rem;
    border-radius: 0.25rem;
    transition: var(--transition);
    text-transform: uppercase;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
}

.btn i {
    font-size: 0.9em;
}

.btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-primary:hover {
    background-color: #003319;
    border-color: #003319;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.btn-success {
    background-color: var(--secondary-color);
    border-color: var(--secondary-color);
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Hover Effects */
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.15) !important;
}

/* Icons */
.display-4 {
    color: var(--primary-color);
    margin-bottom: 1rem;
}


/* Responsive Adjustments */
@media (max-width: 768px) {
    .card {
        margin-bottom: 1.5rem;
    }
    
    .display-4 {
        font-size: 2.5rem;
    }
    
    .card-body {
        padding: 1.5rem;
    }
}

/* Animation for Icons */
@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
    40% {transform: translateY(-10px);}
    60% {transform: translateY(-5px);}
}

.card:hover .display-4 {
    animation: bounce 1s ease;
}

/* Accessibility Improvements */
:focus {
    outline: 3px solid #4D90FE;
    outline-offset: 2px;
}

/* Fix horizontal scrollbar */
html {
    overflow-x: hidden;
    width: 100%;
    max-width: 100%;
}

body {
    position: relative;
    overflow-x: hidden;
    width: 100%;
    max-width: 100%;
    margin: 0;
    padding: 0;
}

/* Reset container and row */
.container,
.container-fluid {
    max-width: 100% !important;
    padding-right: 15px !important;
    padding-left: 15px !important;
    margin-right: auto !important;
    margin-left: auto !important;
}

.row {
    --bs-gutter-x: 1.5rem;
    margin-right: -0.75rem !important;
    margin-left: -0.75rem !important;
}

/* Print Styles */
@media print {
    .btn {
        display: none;
    }
    
    .card {

    /* Ensure content stays centered but container is full width */
    #page-footer .container,
    #page-footer .container-fluid,
    #footer-top .container,
    #footer-content .container,
    #footer-bottom .container {
        max-width: 100% !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 15px !important;
    }

    /* Remove any horizontal padding from rows and columns */
    #page-footer .row,
    #footer-content .row,
    #footer-bottom .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
#page-footer .container,
#page-footer .container-fluid {
    max-width: 100% !important;
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
}

#page-footer .row {
    margin-left: 0 !important;
    margin-right: 0 !important;
}

#page-footer [class*="col-"] {
    padding-left: 0 !important;
    padding-right: 0 !important;
}
</style>
