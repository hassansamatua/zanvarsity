<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/publications_functions.php';

// Set default page title
$pageTitle = "Publications | Zanzibar University";
$page_description = "Browse our latest publications, research papers, and academic works from Zanzibar University.";

// Get search term if any
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get all publications with optional search filter
$publications = [];
try {
    $conn = getZanvarsityDbConnection();
    
    // Base query
    $sql = "SELECT * FROM publications WHERE status_code = 200";
    $params = [];
    
    // Add search condition if search term exists
    if (!empty($search)) {
        $sql .= " AND (title LIKE :search OR author LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    // Add ordering
    $sql .= " ORDER BY publication_date DESC";
    
    // Prepare and execute the query
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debug: Log the number of publications found
    error_log("Number of publications found: " . count($publications));
} catch (Exception $e) {
    $error = "Error loading publications. Please try again later.";
    error_log("Database error: " . $e->getMessage());
}

// Include the about header
include __DIR__ . '/includes/about_header.php';
?>

<style>
        /* Publications Page Specific Styles */
        .publication-card {
            height: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 30px;
        }
        
        .publication-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .publication-image {
            height: 200px;
            overflow: hidden;
        }
        
        .publication-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .publication-card:hover .publication-image img {
            transform: scale(1.05);
        }
        
        .publication-date {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .publication-title {
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
            color: #004225;
            font-weight: 600;
        }
        
        .publication-excerpt {
            color: #555;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .read-more-btn {
            background-color: #004225;
            border-color: #003319;
            color: white;
            padding: 8px 20px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .read-more-btn:hover {
            background-color: #003319;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .page-header {
            background-color: #f8f9fa;
            padding: 60px 0 30px;
            margin-bottom: 40px;
            background-image: linear-gradient(rgba(0, 66, 37, 0.8), rgba(0, 66, 37, 0.8)), url('assets/img/academic-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
        }
        
        .page-header h1 {
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .breadcrumb {
            background: none;
            justify-content: center;
            padding: 0;
        }
        
        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }
        
        .breadcrumb-item.active {
            color: white;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            color: rgba(255, 255, 255, 0.5);
        }
        
        .no-publications {
            text-align: center;
            padding: 50px 0;
            color: #6c757d;
        }
        
        /* Search form styles */
        .search-form .form-control {
            border: 1px solid #004225;
            border-radius: 4px 0 0 4px;
            height: 50px;
            font-size: 1rem;
        }
        
        .search-form .btn {
            height: 50px;
            padding: 0 20px;
            border-radius: 0 4px 4px 0;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .search-form .btn:hover {
            background-color: #003319 !important;
            border-color: #002211 !important;
        }
        
        .search-form .form-control:focus {
            border-color: #004225;
            box-shadow: 0 0 0 0.25rem rgba(0, 66, 37, 0.25);
        }
        
        /* Sidebar Styles */
        .sidebar-widget {
            background: #fff;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        }
        
        .widget-title {
            color: #004225;
            font-size: 1.5rem;
            font-weight: 600;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            margin-bottom: 20px;
        }
        
        .list-group-item {
            border: none;
            border-left: 3px solid transparent;
            margin-bottom: 5px;
            border-radius: 4px !important;
            transition: all 0.3s ease;
            padding: 12px 15px;
            color: #333;
        }
        
        .list-group-item:hover {
            background-color: #f8f9fa;
            border-left-color: #004225;
            color: #004225;
        }
        
        .list-group-item i {
            width: 20px;
            text-align: center;
            color: #004225;
        }
        
        @media (max-width: 991.98px) {
            .sidebar-widget {
                margin-top: 40px;
            }
        }
    </style>
</head>

<body class="page-publications">
    <!-- Page Title Section -->
    <section class="page-title" style="background-color: #f8f9fa; padding: 60px 0 30px; margin-bottom: 40px; border-bottom: 1px solid #eaeaea;">
        <div class="container">
            <header>
                <h1>Our Publications</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Publications</li>
                    </ol>
                </nav>
            </header>
        </div>
    </section>

    <!-- Main Content Section -->
    <section class="main-content py-5">
        <div class="container">
            <div class="row">
                <!-- Main Content Column -->
                <div class="col-lg-8">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <!-- Search Filter -->
            <div class="row mb-4">
                <div class="col-md-8 mx-auto">
                    <form method="GET" action="" class="search-form">
                        <div class="input-group">
                            <input type="text" 
                                   name="search" 
                                   class="form-control form-control-lg" 
                                   placeholder="Search by title or author..." 
                                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                                   aria-label="Search publications">
                            <button class="btn btn-primary" type="submit" style="background-color: #004225; border-color: #003319;">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                                <a href="?" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            
            <?php if (empty($publications)): ?>
                <div class="no-publications text-center py-5 my-5">
                    <i class="fas fa-book-open fa-4x mb-4" style="color: #004225;"></i>
                    <h2 class="mb-3">No Publications Available</h2>
                    <p class="lead">Check back later for our latest publications.</p>
                    <a href="index.php" class="btn btn-primary mt-3" style="background-color: #004225; border-color: #003319;">
                        <i class="fas fa-arrow-left me-2"></i> Back to Home
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($publications as $pub): 
                        // Format publication date
                        $pubDate = 'Date not available';
                        if (!empty($pub['publication_date']) && $pub['publication_date'] !== '0000-00-00') {
                            try {
                                $dateObj = new DateTime($pub['publication_date']);
                                $pubDate = $dateObj->format('F j, Y');
                            } catch (Exception $e) {
                                error_log("Error formatting date for publication ID {$pub['id']}: " . $e->getMessage());
                            }
                        }
                        
                        // Get first image from content if available
                        $imageUrl = 'assets/img/publication-placeholder.jpg';
                        if (!empty($pub['image_url'])) {
                            $imageUrl = $pub['image_url'];
                        }
                    ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card publication-card h-100">
                                <div class="publication-image">
                                    <img src="<?php echo htmlspecialchars($imageUrl); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($pub['title']); ?>">
                                </div>
                                <div class="card-body">
                                    <div class="publication-date">
                                        <i class="far fa-calendar-alt me-1"></i> <?php echo $pubDate; ?>
                                    </div>
                                    <h3 class="publication-title">
                                        <?php echo htmlspecialchars($pub['title']); ?>
                                    </h3>
                                    <?php if (!empty($pub['author'])): ?>
                                        <p class="text-muted small mb-2">
                                            <i class="fas fa-user-edit me-1"></i> 
                                            <?php echo htmlspecialchars($pub['author']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if (!empty($pub['description'])): ?>
                                        <p class="publication-excerpt">
                                            <?php echo htmlspecialchars(substr($pub['description'], 0, 150)) . '...'; ?>
                                        </p>
                                    <?php endif; ?>
                                    <a href="publication-details.php?id=<?php echo $pub['id']; ?>" class="read-more-btn">
                                        Read More <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Sidebar Column -->
                <div class="col-lg-4">
                    <div class="sidebar-widget mb-5">
                        <h3 class="widget-title mb-4">Campus Life</h3>
                        <div class="list-group">
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-running me-3"></i>
                                <span>Athletics & Recreation</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-users me-3"></i>
                                <span>Clubs & Extra-curricular Activities</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-heartbeat me-3"></i>
                                <span>Health & Wellness</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-home me-3"></i>
                                <span>Housing & Residence</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-palette me-3"></i>
                                <span>Arts & Culture</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-laptop me-3"></i>
                                <span>Student IT Services</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-envelope me-3"></i>
                                <span>Newsletter</span>
                            </a>
                        </div>
                        
                        <div class="card mt-4 border-0 shadow-sm">
                            <div class="card-body">
                                <p class="card-text">
                                    <small class="text-muted">
                                        Ut tincidunt, quam in tincidunt vestibulum, turpis ipsum porttitor nisi, et fermentum augue lit eu neque. 
                                        In at tempor dolor, sit amet dictum lacus. Praesent porta orci eget laoreet ultrices.
                                    </small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Sidebar Column -->
                
            </div>
        </div>
    </section>

    <!-- Include the about footer -->
    <?php 
    // Set flag to prevent double page content div in footer
    $hide_page_content_div = true;
    include __DIR__ . '/includes/about_footer.php'; 
    ?>

    <!-- Additional JavaScript for publications -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Add smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    });
    </script>
</body>
</html>
<?php endif; ?>
