<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));

// Include necessary files
require_once ROOT_PATH . '/includes/auth_functions.php';
require_once ROOT_PATH . '/includes/database.php';

// Check if user is logged in and is admin
require_login();

// Use the global database connection
global $conn;

// Initialize variables
$backgroundinfo = '';
$ownership_accredition = '';
$establishment_faculties = '';
$university_membership = '';
$memoranda_understanding = '';
$success = '';
$error = '';

// Check for success or error messages from other scripts
if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    $error = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid CSRF token';
    } else {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        
        // Validate input
        if (empty($title) || empty($content)) {
            $error = 'Title and content are required';
        } else {
            try {
                // Check if record exists
                $stmt = $conn->prepare("SELECT id FROM background_info LIMIT 1");
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    // Update existing record
                    $stmt = $conn->prepare("UPDATE background_info SET title = ?, content = ?, updated_at = NOW()");
                    $stmt->bind_param("ss", $title, $content);
                } else {
                    // Insert new record
                    $stmt = $conn->prepare("INSERT INTO background_info (title, content) VALUES (?, ?)");
                    $stmt->bind_param("ss", $title, $content);
                }
                
                if ($stmt->execute()) {
                    $success = 'Background information updated successfully!';
                } else {
                    $error = 'Error saving background information: ' . $conn->error;
                }
                $stmt->close();
            } catch (Exception $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

// Fetch current background information
try {
    $result = $conn->query("SELECT * FROM background_info LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $bg_info = $result->fetch_assoc();
        $backgroundinfo = $bg_info['backgroundinfo'] ?? '';
        $ownership_accredition = $bg_info['ownership_accredition'] ?? '';
        $establishment_faculties = $bg_info['establishment_faculties'] ?? '';
        $university_membership = $bg_info['university_membership'] ?? '';
        $memoranda_understanding = $bg_info['memoranda_understanding'] ?? '';
    }
} catch (Exception $e) {
    $error = 'Error loading background information: ' . $e->getMessage();
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<?php
// Set the page title
$page_title = 'Manage Background Information';

// Include the admin header
require_once 'includes/header.php';
?>
<body class="admin-dashboard">
<div class="wrapper">
    <div class="navigation-wrapper">
        <div class="secondary-navigation-wrapper">
            <div class="container">
                <div class="navigation">
                    <div class="container">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class='bx bx-error-circle me-2'></i> <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class='bx bx-check-circle me-2'></i> <?php echo $success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-end mb-4">
                            <a href="update_background_info.php" class="btn btn-info btn-sm me-2" onclick="return confirm('Are you sure you want to update the background information with the official content from the website? This will overwrite any existing content.');">
                                <i class='bx bx-refresh me-1'></i> Update from Official Website
                            </a>
                        </div>
                        <div class="navbar-header">
                                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-navigation" aria-expanded="false" aria-controls="navbar">
                                            <span class="sr-only">Toggle navigation</span>
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                    </div>
                                    <?php include('includes/admin_navigation.php'); ?>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end Header -->

    <!-- Page Content -->
    <div id="page-content">
        <div class="container">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-md-3 col-sm-4">
                    <?php include('includes/admin_sidebar.php'); ?>
                </div>
                <!-- end Sidebar -->
                
                <!-- Main Content -->
                <div class="col-md-9 col-sm-8">
                    <section class="block">
                        <div class="page-title">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h2><i class='bx bx-info-circle me-2'></i>Manage Background Information</h2>
                                <div class="d-flex">
                                    <a href="update_background_info.php" class="btn btn-info btn-sm me-2" onclick="return confirm('Are you sure you want to update the background information with the official content from the website? This will overwrite any existing content.');">
                                        <i class='bx bx-refresh me-1'></i> Update from Official Website
                                    </a>
                                    <a href="manage_content.php" class="btn btn-secondary btn-sm">
                                        <i class='bx bx-arrow-back me-1'></i> Back to Dashboard
                                    </a>
                                </div>
                                </a>
                            </div>
                        </div>
                        
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $success; ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $error; ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card">
                            <div class="card-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    
                                    <div class="form-group mb-4">
                                        <label for="backgroundinfo" class="form-label fw-bold">Background Information</label>
                                        <textarea class="form-control summernote" id="backgroundinfo" name="backgroundinfo" rows="10" required><?php echo htmlspecialchars($backgroundinfo); ?></textarea>
                                    </div>
                                    
                                    <div class="form-group mb-4">
                                        <label for="ownership_accredition" class="form-label fw-bold">Ownership & Accreditation</label>
                                        <textarea class="form-control summernote" id="ownership_accredition" name="ownership_accredition" rows="10" required><?php echo htmlspecialchars($ownership_accredition); ?></textarea>
                                    </div>
                                    
                                    <div class="form-group mb-4">
                                        <label for="establishment_faculties" class="form-label fw-bold">Establishment of Academic Faculties</label>
                                        <textarea class="form-control summernote" id="establishment_faculties" name="establishment_faculties" rows="10" required><?php echo htmlspecialchars($establishment_faculties); ?></textarea>
                                    </div>
                                    
                                    <div class="form-group mb-4">
                                        <label for="university_membership" class="form-label fw-bold">University Membership</label>
                                        <textarea class="form-control summernote" id="university_membership" name="university_membership" rows="10" required><?php echo htmlspecialchars($university_membership); ?></textarea>
                                    </div>
                                    
                                    <div class="form-group mb-4">
                                        <label for="memoranda_understanding" class="form-label fw-bold">Memoranda of Understanding</label>
                                        <textarea class="form-control summernote" id="memoranda_understanding" name="memoranda_understanding" rows="10" required><?php echo htmlspecialchars($memoranda_understanding); ?></textarea>
                                    </div>
                                    
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class='bx bx-save me-1'></i> Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
                <!-- end Main Content -->
            </div>
        </div>
    </div>
    <!-- end Page Content -->
</div>
<!-- end Wrapper -->

<!-- Initialize Summernote -->
<script>
$(document).ready(function() {
    // Initialize all summernote editors
    $('.summernote').each(function() {
        $(this).summernote({
            height: 250,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onInit: function() {
                    // Add a custom class to the editor
                    $(this).next('.note-editor').addClass('mb-4');
                }
            }
        });
    });
    
    // Handle form submission
    $('form').on('submit', function() {
        // Update textareas with Summernote content before form submission
        $('.summernote').each(function() {
            var content = $(this).summernote('code');
            $(this).val(content);
        });
    });

    // Close alert when close button is clicked
    $('.alert .btn-close').on('click', function() {
        $(this).closest('.alert').fadeOut();
    });
    
    // Auto-hide success messages after 5 seconds
    setTimeout(function() {
        $('.alert-success').fadeOut();
    }, 5000);
});
</script>

<?php
// Include the admin footer
require_once 'includes/footer.php';
?>
