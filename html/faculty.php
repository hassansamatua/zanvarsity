<?php
// Start session and include database connection
session_start();
require_once 'includes/config.php';

// Debug output
echo "<pre style='background: #f8f9fa; padding: 15px; border: 1px solid #ddd; margin: 10px;'>";
echo "GET Parameters: "; print_r($_GET);
echo "Database Connection: " . ($conn ? "Connected" : "Failed") . "\n";

// Check if faculty ID is provided
$faculty_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
echo "Faculty ID: " . $faculty_id . "\n";

// Initialize variables
$faculty = null;
$departments = [];
$error = '';

if ($faculty_id > 0) {
    try {
        // Get faculty details
        $stmt = $conn->prepare("SELECT * FROM faculty_tbl WHERE id = ?");
        $stmt->bind_param("i", $faculty_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $faculty = $result->fetch_assoc();
            
            // Get departments for this faculty
            try {
                $dept_stmt = $conn->prepare("SHOW TABLES LIKE 'department_tbl'");
                $dept_stmt->execute();
                $table_exists = $dept_stmt->get_result()->num_rows > 0;
                
                if ($table_exists) {
                    $dept_stmt = $conn->prepare("SELECT * FROM department_tbl WHERE faculty_id = ? ORDER BY name ASC");
                    if ($dept_stmt) {
                        $dept_stmt->bind_param("i", $faculty_id);
                        $dept_stmt->execute();
                        $dept_result = $dept_stmt->get_result();
                        
                        if ($dept_result && $dept_result->num_rows > 0) {
                            $departments = $dept_result->fetch_all(MYSQLI_ASSOC);
                        }
                    }
                } else {
                    // Table doesn't exist, we'll create it later
                    $error = 'Departments feature is not yet configured. Please contact the administrator.';
                }
            } catch (Exception $e) {
                // Silently handle the error for now
                error_log('Department query error: ' . $e->getMessage());
            }
        } else {
            $error = 'Faculty not found or inactive.';
        }
    } catch (Exception $e) {
        $error = 'Error fetching faculty information: ' . $e->getMessage();
    }
} else {
    $error = 'Invalid faculty ID provided.';
}

// Set page title
$page_title = isset($faculty['faculty_name']) ? $faculty['faculty_name'] . ' - ' . SITE_NAME : 'Faculty - ' . SITE_NAME;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .faculty-header {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('assets/images/faculty-bg.jpg') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 100px 0;
            margin-bottom: 50px;
        }
        .faculty-about {
            margin-bottom: 50px;
        }
        .department-card {
            transition: transform 0.3s ease;
            margin-bottom: 20px;
            height: 100%;
        }
        .department-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .department-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'includes/about_header.php'; ?>

    <?php if (!empty($error)): ?>
        <div class="container mt-5">
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
                <a href="index.php" class="alert-link">Return to Home</a>
            </div>
        </div>
    <?php elseif ($faculty): ?>
        <!-- Faculty Header -->
        <header class="faculty-header text-center">
            <div class="container">
                <h1 class="display-4 fw-bold"><?php echo htmlspecialchars($faculty['faculty_name']); ?></h1>
                <p class="lead"><?php echo htmlspecialchars($faculty['abbreviation']); ?></p>
                <?php if (!empty($faculty['dean_of_faculty'])): ?>
                    <p class="h5">Dean: <?php echo htmlspecialchars($faculty['dean_of_faculty']); ?></p>
                <?php endif; ?>
                <?php if (!empty($faculty['slogan'])): ?>
                    <p class="fst-italic">"<?php echo htmlspecialchars($faculty['slogan']); ?>"</p>
                <?php endif; ?>
            </div>
        </header>

        <div class="container">
            <!-- Welcome Message -->
            <?php if (!empty($faculty['welcome_message'])): ?>
                <div class="alert alert-info">
                    <h4 class="alert-heading">Welcome Message</h4>
                    <p><?php echo nl2br(htmlspecialchars($faculty['welcome_message'])); ?></p>
                </div>
            <?php endif; ?>

            <!-- About Section -->
            <section class="faculty-about">
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <h2 class="mb-4">About the Faculty</h2>
                        <div class="mb-4">
                            <?php 
                            $description = !empty($faculty['faculty_background']) ? 
                                $faculty['faculty_background'] : 
                                'No description available.';
                            echo nl2br(htmlspecialchars($description));
                            ?>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Vision & Mission -->
            <div class="row mb-5">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3 class="card-title"><i class="fas fa-eye me-2"></i>Our Vision</h3>
                            <p class="card-text">
                                <?php echo !empty($faculty['vision']) ? 
                                    nl2br(htmlspecialchars($faculty['vision'])) : 
                                    'Vision statement will be updated soon.'; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3 class="card-title"><i class="fas fa-bullseye me-2"></i>Our Mission</h3>
                            <p class="card-text">
                                <?php echo !empty($faculty['mission']) ? 
                                    nl2br(htmlspecialchars($faculty['mission'])) : 
                                    'Mission statement will be updated soon.'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Departments -->
            <section class="departments mb-5">
                <h2 class="text-center mb-4">Our Departments</h2>
                <?php if (!empty($departments)): ?>
                    <div class="row">
                        <?php foreach ($departments as $dept): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card department-card">
                                    <div class="card-body text-center">
                                        <div class="department-icon">
                                            <i class="fas fa-university"></i>
                                        </div>
                                        <h3 class="h5"><?php echo htmlspecialchars($dept['name']); ?></h3>
                                        <?php if (!empty($dept['description'])): ?>
                                            <p class="card-text">
                                                <?php echo nl2br(htmlspecialchars(mb_strimwidth($dept['description'], 0, 150, '...'))); ?>
                                            </p>
                                        <?php endif; ?>
                                        <a href="department.php?id=<?php echo $dept['id']; ?>" class="btn btn-outline-primary">
                                            Learn More <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php elseif (isset($error) && strpos($error, 'Departments feature') !== false): ?>
                    <div class="alert alert-info">
                        <p>Department information is not yet available. Please check back later.</p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <p>No departments found for this faculty.</p>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    <?php endif; ?>

    <!-- Footer -->
    <?php include 'includes/about_footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
