<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));

require_once ROOT_PATH . '/includes/auth_functions.php';
require_once ROOT_PATH . '/includes/database.php';
require_once ROOT_PATH . '/includes/staff_functions.php';

// Initialize response array
$response = ['success' => false, 'message' => ''];

// Check if user is logged in and is admin
require_login();

// Get database connection
$conn = $GLOBALS['conn'] ?? null;

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user_role = $_SESSION['role'];
$user_name = $_SESSION['name'] ?? '';
$user_email = $_SESSION['email'] ?? '';
$error = '';
$success = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid CSRF token';
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'add_staff':
                // Validate required fields
                $required = ['first_name', 'last_name', 'email', 'position'];
                $missing = [];
                $staff_data = [];
                
                foreach ($required as $field) {
                    if (empty(trim($_POST[$field] ?? ''))) {
                        $missing[] = $field;
                    } else {
                        $staff_data[$field] = trim($_POST[$field]);
                    }
                }
                
                if (!empty($missing)) {
                    $error = 'Please fill in all required fields: ' . implode(', ', $missing);
                    break;
                }

                // Handle file upload
                $image_url = '';
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $target_dir = ROOT_PATH . "/uploads/staff/";
                    if (!file_exists($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    
                    $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
                    $new_filename = uniqid() . '.' . $file_extension;
                    $target_file = $target_dir . $new_filename;
                    
                    // Check if image file is an actual image
                    $check = getimagesize($_FILES["image"]["tmp_name"]);
                    if ($check === false) {
                        $error = 'File is not an image.';
                        break;
                    }
                    
                    // Check file size (5MB max)
                    if ($_FILES["image"]["size"] > 5000000) {
                        $error = 'Sorry, your file is too large. Maximum size is 5MB.';
                        break;
                    }
                    
                    // Allow certain file formats
                    $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
                    if (!in_array($file_extension, $allowed_extensions)) {
                        $error = 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.';
                        break;
                    }
                    
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        $image_url = "/uploads/staff/" . $new_filename;
                    } else {
                        $error = 'Sorry, there was an error uploading your file.';
                        break;
                    }
                }
                
                // Prepare staff data
                $staff_data['title'] = $_POST['title'] ?? '';
                $staff_data['department_id'] = $_POST['department_id'] ?? null;
                $staff_data['qualification'] = $_POST['qualification'] ?? '';
                $staff_data['bio'] = $_POST['bio'] ?? '';
                $staff_data['phone'] = $_POST['phone'] ?? '';
                $staff_data['is_teaching'] = isset($_POST['is_teaching']) ? 1 : 0;
                $staff_data['image_url'] = $image_url;
                
                // Insert into database
                $result = addStaff($conn, $staff_data);
                
                if ($result) {
                    $success = 'Staff member added successfully';
                    $_SESSION['success'] = $success;
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    $error = 'Failed to add staff member: ' . ($conn->error ?? 'Unknown error');
                }
                break;
                
            case 'update_staff':
                // Similar to add_staff but with update logic
                // Implementation omitted for brevity
                break;
                
            case 'delete_staff':
                $staff_id = $_POST['staff_id'] ?? 0;
                if ($staff_id) {
                    // Get staff data first to delete the image
                    $staff = getStaffById($conn, $staff_id);
                    
                    if ($staff) {
                        // Delete the staff member
                        $result = deleteStaff($conn, $staff_id);
                        
                        if ($result) {
                            // Delete the associated image if it exists
                            if (!empty($staff['image_url'])) {
                                $image_path = ROOT_PATH . $staff['image_url'];
                                if (file_exists($image_path)) {
                                    unlink($image_path);
                                }
                            }
                            
                            $response = [
                                'success' => true,
                                'message' => 'Staff member deleted successfully'
                            ];
                            header('Content-Type: application/json');
                            echo json_encode($response);
                            exit();
                        }
                    }
                }
                
                $response['message'] = 'Failed to delete staff member';
                header('Content-Type: application/json');
                echo json_encode($response);
                exit();
                
            default:
                $error = 'Invalid action';
        }
    }
}

// Get all staff for display
$staff_members = getAllStaff($conn);

// Get departments for dropdown
$departments = [];
$dept_result = $conn->query("SELECT id, name FROM departments ORDER BY name");
if ($dept_result && $dept_result->num_rows > 0) {
    while ($row = $dept_result->fetch_assoc()) {
        $departments[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Manage Staff - Zanzibar University</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="/zanvarsity/html/assets/img/favicon.ico">
    
    <!-- Load jQuery first -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI/tZ1ZqTlJfMkQzB6Xj3lF9z2BvPdl4yf0Hk0=" crossorigin="anonymous"></script>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css"/>
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Preload critical scripts -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" as="script">
    <link rel="preload" href="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js" as="script">
    <link rel="preload" href="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js" as="script">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js" as="script">
    
    <!-- Custom CSS -->
    <style>
        /* Custom styles for staff management */
        .staff-avatar {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .qualification-badge {
            font-size: 0.75rem;
            margin-right: 0.25rem;
            margin-bottom: 0.25rem;
        }
        
        .department-badge {
            background-color: #e9ecef;
            color: #495057;
        }
        
        /* Responsive table */
        .table-responsive {
            overflow-x: auto;
        }
        
        /* Card styling */
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.25rem;
        }
        
        .card-title {
            margin-bottom: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        /* Button styles */
        .btn-add {
            background-color: #198754;
            border-color: #198754;
        }
        
        .btn-add:hover {
            background-color: #157347;
            border-color: #146c43;
        }
        
        /* Modal styles */
        .modal-header {
            background-color: #198754;
            color: white;
        }
        
        .modal-title {
            font-weight: 600;
        }
        
        /* Form styles */
        .form-label {
            font-weight: 500;
        }
        
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        
        /* Status badges */
        .status-active {
            background-color: #198754;
        }
        
        .status-inactive {
            background-color: #6c757d;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .btn-add {
                width: 100%;
                margin-top: 0.5rem;
            }
        }
    </style>
</head>
<!-- Wrapper -->
<!-- Wrapper -->
<div class="wrapper">
    <!-- Header -->
    <div class="navigation-wrapper">
        <div class="secondary-navigation-wrapper">
            <div class="container">
                <div class="navigation">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <nav>
                                    <ul class="nav nav-pills nav-top">
                                        <li class="nav-item">
                                            <a href="/zanvarsity/html/index.html" class="nav-link"><i class="fa fa-home"></i> Home</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/zanvarsity/html/admin/dashboard.php" class="nav-link">Dashboard</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/zanvarsity/html/admin/manage_content.php" class="nav-link">Content Management</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/zanvarsity/html/admin/manage_staff.php" class="nav-link active">Staff Management</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/zanvarsity/html/admin/users.php" class="nav-link">User Management</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/zanvarsity/html/admin/logout.php" class="nav-link">Logout</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Page Content -->
    <div id="page-content">
        <div class="container">
            <div class="row">
                <!-- Breadcrumb -->
                <div class="container">
                    <ol class="breadcrumb">
                        <li><a href="/zanvarsity/html/index.html">Home</a></li>
                        <li><a href="/zanvarsity/html/admin/dashboard.php">Admin</a></li>
                        <li class="active">Staff Management</li>
                    </ol>
                </div>
                
                <!-- Sidebar -->
                <aside class="col-md-3 col-sm-4">
                    <div class="sidebar">
                        <div class="sidebar-inner">
                            <div class="widget">
                                <h3 class="widget-title">Admin Menu</h3>
                                <ul class="nav nav-pills nav-stacked">
                                    <li><a href="/zanvarsity/html/admin/dashboard.php"><i class='bx bxs-dashboard me-2'></i> Dashboard</a></li>
                                    <li><a href="/zanvarsity/html/admin/manage_content.php"><i class='bx bxs-grid me-2'></i> Content Management</a></li>
                                    <li class="active"><a href="/zanvarsity/html/admin/manage_staff.php"><i class='bx bxs-user-detail me-2'></i> Staff Management</a></li>
                                    <li><a href="/zanvarsity/html/admin/users.php"><i class='bx bxs-user-account me-2'></i> User Management</a></li>
                                    <li><a href="/zanvarsity/html/admin/manage_publications.php"><i class='bx bxs-book me-2'></i> Publications</a></li>
                                    <li><a href="/zanvarsity/html/admin/settings.php"><i class='bx bxs-cog me-2'></i> Settings</a></li>
                                    <li><a href="/zanvarsity/html/admin/logout.php"><i class='bx bx-log-out me-2'></i> Logout</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </aside>
                
                <!-- Main Content -->
                <div class="col-md-9 col-sm-8">
                    <section class="block">
                        <div class="page-title">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h2><i class='bx bxs-user-detail me-2'></i>Manage Staff</h2>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                                    <i class='bx bx-plus me-1'></i> Add Staff
                                </button>
                            </div>    
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            
                            <?php if (!empty($success)): ?>
                                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card shadow-sm">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle" id="staffTable" style="width:100%">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-4">Photo</th>
                                                <th>Name</th>
                                                <th>Position</th>
                                                <th>Department</th>
                                                <th>Email</th>
                                                <th>Status</th>
                                                <th class="text-end pe-4">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($staff_members)): ?>
                                                <?php foreach ($staff_members as $staff): ?>
                                                    <tr>
                                                        <td class="ps-4">
                                                            <img src="<?php echo !empty($staff['image_url']) ? htmlspecialchars($staff['image_url']) : '/assets/img/default-avatar.jpg'; ?>" 
                                                                 alt="<?php echo htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']); ?>" 
                                                                 class="staff-avatar">
                                                        </td>
                                                        <td>
                                                            <?php 
                                                                echo htmlspecialchars(($staff['title'] ? $staff['title'] . ' ' : '') . 
                                                                    $staff['first_name'] . ' ' . $staff['last_name']); 
                                                            ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($staff['position'] ?? 'N/A'); ?></td>
                                                        <td>
                                                            <?php if (!empty($staff['department_name'])): ?>
                                                                <span class="badge department-badge">
                                                                    <?php echo htmlspecialchars($staff['department_name']); ?>
                                                                </span>
                                                            <?php else: ?>
                                                                N/A
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($staff['email'] ?? 'N/A'); ?></td>
                                                        <td>
                                                            <span class="badge status-<?php echo ($staff['is_active'] ?? 1) ? 'active' : 'inactive'; ?>">
                                                                <?php echo ($staff['is_active'] ?? 1) ? 'Active' : 'Inactive'; ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-end pe-4">
                                                            <button class="btn btn-sm btn-info view-staff" 
                                                                    data-id="<?php echo $staff['id']; ?>"
                                                                    data-bs-toggle="tooltip" 
                                                                    title="View">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-warning edit-staff" 
                                                                    data-id="<?php echo $staff['id']; ?>"
                                                                    data-bs-toggle="tooltip" 
                                                                    title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-danger delete-staff" 
                                                                    data-id="<?php echo $staff['id']; ?>"
                                                                    data-name="<?php echo htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']); ?>"
                                                                    data-bs-toggle="tooltip" 
                                                                    title="Delete">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="8" class="text-center py-5">
                                                        <div class="text-muted">
                                                            <i class="fas fa-users fa-3x mb-3"></i>
                                                            <h4>No Staff Members Found</h4>
                                                            <p>Get started by adding a new staff member.</p>
                                                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                                                                <i class="fas fa-plus me-1"></i> Add Staff
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <!-- End Main Content -->
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <?php 
    // Prevent footer scripts from loading since we're including our own
    define('SKIP_FOOTER_SCRIPTS', true);
    include_once ROOT_PATH . '/html/includes/footer.php'; 
    ?>
</div>
<!-- End Wrapper -->

<!-- Add Staff Modal -->
<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1" role="dialog" aria-labelledby="addStaffModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h2 class="modal-title h5" id="addStaffModalLabel">Add New Staff Member</h2>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addStaffForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_staff">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <div class="image-upload-container">
                                <img id="staffImagePreview" src="/zanvarsity/html/assets/img/default-avatar.svg" 
                                     class="img-thumbnail mb-2" style="width: 200px; height: 200px; object-fit: cover;"
                                     alt="Staff Image">
                                <div class="d-grid gap-2">
                                    <input type="file" class="form-control d-none" id="staffImage" name="image" accept="image/*">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('staffImage').click()">
                                        <i class="fas fa-upload me-1"></i> Upload Photo
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" id="removeImage" style="display: none;">
                                        <i class="fas fa-trash-alt me-1"></i> Remove
                                    </button>
                                </div>
                                <small class="text-muted">Max size: 5MB (JPG, PNG, GIF)</small>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <select class="form-select" id="title" name="title">
                                        <option value="">Select Title</option>
                                        <option value="Prof.">Prof.</option>
                                        <option value="Dr.">Dr.</option>
                                        <option value="Mr.">Mr.</option>
                                        <option value="Mrs.">Mrs.</option>
                                        <option value="Ms.">Ms.</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label required-field">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label required-field">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label required-field">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="phone" name="phone">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="position" class="form-label required-field">Position</label>
                                    <input type="text" class="form-control" id="position" name="position" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="department_id" class="form-label">Department</label>
                                    <select class="form-select" id="department_id" name="department_id">
                                        <option value="">Select Department</option>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?php echo $dept['id']; ?>">
                                                <?php echo htmlspecialchars($dept['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="qualification" class="form-label">Qualifications</label>
                                    <input type="text" class="form-control" id="qualification" name="qualification" 
                                           placeholder="e.g., PhD in Computer Science, MSc in IT">
                                    <small class="text-muted">Separate multiple qualifications with commas</small>
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_teaching" name="is_teaching" value="1" checked>
                                        <label class="form-check-label" for="is_teaching">
                                            Teaching Staff
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <label for="bio" class="form-label">Bio/Description</label>
                                    <textarea class="form-control" id="bio" name="bio" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Staff Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Staff Modal -->
<!-- View Staff Modal -->
<div class="modal fade" id="viewStaffModal" tabindex="-1" role="dialog" aria-labelledby="viewStaffModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h2 class="modal-title h5" id="viewStaffModalLabel">Staff Details</h2>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="staffDetails">
                <!-- Staff details will be loaded here via AJAX -->
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading staff details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Staff Modal -->
<!-- Edit Staff Modal -->
<div class="modal fade" id="editStaffModal" tabindex="-1" role="dialog" aria-labelledby="editStaffModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h2 class="modal-title h5" id="editStaffModalLabel">Edit Staff Member</h2>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editStaffForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_staff">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="staff_id" id="edit_staff_id">
                
                <div class="modal-body" id="editStaffFormContent">
                    <!-- Form content will be loaded here via AJAX -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading staff data...</p>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Staff</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="staffNameToDelete"></strong>?</p>
                <p class="text-danger">This action cannot be undone. All data related to this staff member will be permanently removed.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline-block;">
                    <input type="hidden" name="action" value="delete_staff">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="staff_id" id="delete_staff_id">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Load all scripts at the end of the body for better performance -->
<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

<!-- Dependency Checker -->
<script>
// Check for required libraries
function checkDependencies() {
    // Define required libraries and their status
    const required = {
        'jQuery': typeof jQuery !== 'undefined',
        'Bootstrap': typeof bootstrap !== 'undefined',
        'DataTable': typeof $.fn.DataTable !== 'undefined',
        'SweetAlert2': typeof Swal !== 'undefined'
    };

    // Check each required library
    let allLoaded = true;
    for (const [lib, loaded] of Object.entries(required)) {
        if (!loaded) {
            console.error(`❌ ${lib} is not loaded!`);
            allLoaded = false;
        } else {
            console.log(`✅ ${lib} is loaded`);
        }
    }

    if (allLoaded) {
        console.log('✅ All required libraries are loaded');
    } else {
        console.error('❌ Some required libraries are missing');
        
        // Show error message to user if critical libraries are missing
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error Loading Page',
                text: 'Some required resources failed to load. Please refresh the page and try again.',
                confirmButtonText: 'Refresh',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.reload();
                }
            });
        }
    }

    return allLoaded;
}

// Run checks when the page is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded');
    checkDependencies();
});

// Also check when window is fully loaded
window.addEventListener('load', function() {
    console.log('Window fully loaded');
    
    // Initialize the page
    try {
        // First check dependencies
        if (!checkDependencies()) {
            console.error('Not all dependencies are loaded on window load');
            return;
        }
        
        // Then initialize the page
        initializePage();
    } catch (error) {
        console.error('Error during page initialization:', error);
        showError('An error occurred while initializing the page. Please refresh and try again.');
    }
    
    // One final check after a short delay
    setTimeout(function() {
        if (!checkDependencies()) {
            console.error('Not all dependencies are loaded after timeout');
        } else {
            console.log('All dependencies verified after timeout');
        }
    }, 1000);
});
</script>

<!-- Fix for default avatar -->
<script>
    // Set default avatar path
    const defaultAvatar = '/zanvarsity/html/assets/img/default-avatar.svg';
</script>

<script>
// Function to initialize DataTable
function initializeDataTable() {
    console.log('Initializing DataTable...');
    
    // Check if jQuery is available
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded. Cannot initialize DataTable.');
        showError('jQuery is not loaded. Please refresh the page.');
        return null;
    }
    
    // Check if DataTable is available
    if (typeof $.fn.DataTable === 'undefined') {
        console.error('DataTable is not loaded. Check if jQuery DataTable script is included.');
        showError('DataTable library is not loaded. Please refresh the page.');
        return null;
    }
    
    try {
        // Check if table exists
        const $table = $('#staffTable');
        if ($table.length === 0) {
            console.error('Table with ID "staffTable" not found');
            return null;
        }
        
        // Initialize DataTable with error handling
        return $table.DataTable({
            responsive: true,
            columnDefs: [
                { 
                    orderable: false, 
                    targets: [0, 6], // Disable sorting on photo and actions columns
                    searchable: false // Disable search on photo and actions columns
                },
                { 
                    className: 'align-middle', 
                    targets: '_all' // Center align all cells
                }
            ],
            order: [[1, 'asc']], // Sort by name by default
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search staff...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ staff members",
                infoEmpty: "No staff members found",
                infoFiltered: "(filtered from _MAX_ total staff)",
                zeroRecords: "No matching staff members found",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            initComplete: function() {
                console.log('DataTable initialization complete');
            },
            error: function(error) {
                console.error('DataTables error:', error);
                // Show user-friendly error message
                $('#staffTable').html('<div class="alert alert-danger">An error occurred while loading the staff data. Please refresh the page and try again.</div>');
            },
            drawCallback: function() {
                // Reinitialize tooltips after table redraw
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        });
    } catch (error) {
        console.error('Error initializing DataTable:', error);
        showError('Failed to initialize the data table. Please refresh the page.');
        return null;
    }
}

// Function to show error message
function showError(message) {
    console.error(message);
    
    // Try to show error message using SweetAlert2 if available
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
            confirmButtonText: 'OK'
        });
    } else {
        // Fallback to alert if SweetAlert2 is not available
        alert('Error: ' + message);
    }
    
    // Also show error in the UI if possible
    const errorContainer = document.createElement('div');
    errorContainer.className = 'alert alert-danger mt-3';
    errorContainer.innerHTML = `
        <i class="fas fa-exclamation-triangle me-2"></i>
        ${message}
        <button type="button" class="btn btn-sm btn-outline-danger ms-3" onclick="window.location.reload()">
            <i class="fas fa-sync-alt me-1"></i> Refresh Page
        </button>
    `;
    
    const container = document.querySelector('.container') || document.body;
    if (container) {
        container.prepend(errorContainer);
    }
}

// Initialize the page when everything is ready
function initializePage() {
    console.log('Initializing page...');
    
    // First check if all dependencies are loaded
    if (!checkDependencies()) {
        console.error('Not all dependencies are loaded');
        return;
    }
    
    // Initialize DataTable
    const dataTable = initializeDataTable();
    
    if (dataTable) {
        console.log('Page initialization complete');
        
        // Handle success/error messages
        <?php if (!empty($_SESSION['success'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '<?php echo addslashes($_SESSION['success']); ?>',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (!empty($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo addslashes($_SESSION['error']); ?>',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        // Image preview for add form
        $('#staffImage').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#staffImagePreview').attr('src', e.target.result);
                    $('#removeImage').show();
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Remove image in add form
        $('#removeImage').click(function(e) {
            e.preventDefault();
            $('#staffImage').val('');
            $('#staffImagePreview').attr('src', '/assets/img/default-avatar.jpg');
            $(this).hide();
        });
        
        // View staff details
        $(document).on('click', '.view-staff', function() {
            const staffId = $(this).data('id');
            const modal = new bootstrap.Modal(document.getElementById('viewStaffModal'));
            
            // Show loading state
            $('#staffDetails').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading staff details...</p>
                </div>
            `);
            
            // Show modal
            modal.show();
            
            // Load staff details via AJAX
            $.ajax({
                url: 'ajax/get_staff.php',
                type: 'GET',
                data: { id: staffId },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        const staff = response.data;
                        let html = `
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <img src="${staff.image_url || '/assets/img/default-avatar.jpg'}" 
                                         class="img-thumbnail mb-3" style="width: 200px; height: 200px; object-fit: cover;">
                                    <h4>${staff.title ? staff.title + ' ' : ''}${staff.first_name} ${staff.last_name}</h4>
                                    <p class="text-muted">${staff.position || 'Staff Member'}</p>
                                    
                                    <div class="d-grid gap-2">
                                        ${staff.email ? `<a href="mailto:${staff.email}" class="btn btn-outline-primary">
                                            <i class="fas fa-envelope me-1"></i> Email
                                        </a>` : ''}
                                        ${staff.phone ? `<a href="tel:${staff.phone}" class="btn btn-outline-secondary">
                                            <i class="fas fa-phone me-1"></i> Call
                                        </a>` : ''}
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h5 class="border-bottom pb-2 mb-3">Profile Information</h5>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <p><strong>Full Name:</strong><br>
                                            ${staff.title ? staff.title + ' ' : ''}${staff.first_name} ${staff.last_name}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Email:</strong><br>
                                            ${staff.email || 'N/A'}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Phone:</strong><br>
                                            ${staff.phone || 'N/A'}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Department:</strong><br>
                                            ${staff.department_name || 'N/A'}</p>
                                        </div>
                                        <div class="col-12">
                                            <p><strong>Position:</strong><br>
                                            ${staff.position || 'N/A'}</p>
                                        </div>
                                        <div class="col-12">
                                            <p><strong>Qualifications:</strong><br>
                                            ${staff.qualification ? staff.qualification.split(',').map(q => 
                                                `<span class="badge bg-primary me-1">${q.trim()}</span>`
                                            ).join('') : 'N/A'}</p>
                                        </div>
                                        ${staff.bio ? `
                                        <div class="col-12">
                                            <p><strong>Bio:</strong></p>
                                            <div class="border p-3 rounded bg-light">
                                                ${staff.bio.replace(/\n/g, '<br>')}
                                            </div>
                                        </div>` : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        $('#staffDetails').html(html);
                    } else {
                        $('#staffDetails').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                ${response.message || 'Failed to load staff details. Please try again.'}
                            </div>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching staff details:', error);
                    $('#staffDetails').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            An error occurred while loading staff details. Please try again.
                        </div>
                    `);
                }
            });
        });
        
        // Edit staff
        $(document).on('click', '.edit-staff', function() {
            const staffId = $(this).data('id');
            const modal = new bootstrap.Modal(document.getElementById('editStaffModal'));
            
            // Show loading state
            $('#editStaffFormContent').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading staff data...</p>
                </div>
            `);
            
            // Show modal
            modal.show();
            
            // Load staff data via AJAX
            $.ajax({
                url: 'ajax/get_staff.php',
                type: 'GET',
                data: { id: staffId },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        const staff = response.data;
                        
                        // Generate form HTML
                        let html = `
                            <div class="row">
                                <div class="col-md-4 text-center mb-3">
                                    <div class="image-upload-container">
                                        <img id="editStaffImagePreview" 
                                             src="${staff.image_url || '/assets/img/default-avatar.jpg'}" 
                                             class="img-thumbnail mb-2" 
                                             style="width: 200px; height: 200px; object-fit: cover;">
                                        <div class="d-grid gap-2">
                                            <input type="file" class="form-control d-none" id="editStaffImage" name="image" accept="image/*">
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('editStaffImage').click()">
                                                <i class="fas fa-upload me-1"></i> Change Photo
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" id="editRemoveImage" ${!staff.image_url ? 'style="display: none;"' : ''}>
                                                <i class="fas fa-trash-alt me-1"></i> Remove
                                            </button>
                                        </div>
                                        <input type="hidden" name="current_image" value="${staff.image_url || ''}">
                                        <small class="text-muted">Max size: 5MB (JPG, PNG, GIF)</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_title" class="form-label">Title</label>
                                            <select class="form-select" id="edit_title" name="title">
                                                <option value="">Select Title</option>
                                                <option value="Prof." ${staff.title === 'Prof.' ? 'selected' : ''}>Prof.</option>
                                                <option value="Dr." ${staff.title === 'Dr.' ? 'selected' : ''}>Dr.</option>
                                                <option value="Mr." ${staff.title === 'Mr.' ? 'selected' : ''}>Mr.</option>
                                                <option value="Mrs." ${staff.title === 'Mrs.' ? 'selected' : ''}>Mrs.</option>
                                                <option value="Ms." ${staff.title === 'Ms.' ? 'selected' : ''}>Ms.</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_first_name" class="form-label required-field">First Name</label>
                                            <input type="text" class="form-control" id="edit_first_name" name="first_name" 
                                                   value="${staff.first_name || ''}" required>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_last_name" class="form-label required-field">Last Name</label>
                                            <input type="text" class="form-control" id="edit_last_name" name="last_name" 
                                                   value="${staff.last_name || ''}" required>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_email" class="form-label required-field">Email</label>
                                            <input type="email" class="form-control" id="edit_email" name="email" 
                                                   value="${staff.email || ''}" required>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_phone" class="form-label">Phone</label>
                                            <input type="tel" class="form-control" id="edit_phone" name="phone" 
                                                   value="${staff.phone || ''}">
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_position" class="form-label required-field">Position</label>
                                            <input type="text" class="form-control" id="edit_position" name="position" 
                                                   value="${staff.position || ''}" required>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_department_id" class="form-label">Department</label>
                                            <select class="form-select" id="edit_department_id" name="department_id">
                                                <option value="">Select Department</option>
                                                <?php foreach ($departments as $dept): ?>
                                                    <option value="<?php echo $dept['id']; ?>" 
                                                        ${<?php echo $dept['id']; ?> === parseInt('${staff.department_id || 0}') ? 'selected' : ''}>
                                                        <?php echo addslashes($dept['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="edit_qualification" class="form-label">Qualifications</label>
                                            <input type="text" class="form-control" id="edit_qualification" name="qualification" 
                                                   value="${staff.qualification || ''}" 
                                                   placeholder="e.g., PhD in Computer Science, MSc in IT">
                                            <small class="text-muted">Separate multiple qualifications with commas</small>
                                        </div>
                                        
                                        <div class="col-12 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="edit_is_teaching" 
                                                       name="is_teaching" value="1" ${staff.is_teaching ? 'checked' : ''}>
                                                <label class="form-check-label" for="edit_is_teaching">
                                                    Teaching Staff
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-12 mb-3">
                                            <label for="edit_bio" class="form-label">Bio/Description</label>
                                            <textarea class="form-control" id="edit_bio" name="bio" rows="3">${staff.bio || ''}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        $('#editStaffFormContent').html(html);
                        $('#edit_staff_id').val(staffId);
                        
                        // Initialize image preview for edit form
                        $('#editStaffImage').change(function() {
                            const file = this.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    $('#editStaffImagePreview').attr('src', e.target.result);
                                    $('#editRemoveImage').show();
                                }
                                reader.readAsDataURL(file);
                            }
                        });
                        
                        // Remove image in edit form
                        $('#editRemoveImage').click(function(e) {
                            e.preventDefault();
                            $('#editStaffImage').val('');
                            $('#editStaffImagePreview').attr('src', '/assets/img/default-avatar.jpg');
                            $(this).hide();
                            $('input[name="current_image"]').val('');
                        });
                        
                    } else {
                        $('#editStaffFormContent').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                ${response.message || 'Failed to load staff data. Please try again.'}
                            </div>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching staff data:', error);
                    $('#editStaffFormContent').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            An error occurred while loading staff data. Please try again.
                        </div>
                    `);
                }
            });
        });
        
        // Delete staff
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        
        $(document).on('click', '.delete-staff', function() {
            const staffId = $(this).data('id');
            const staffName = $(this).data('name');
            
            $('#staffNameToDelete').text(staffName);
            $('#delete_staff_id').val(staffId);
            
            deleteModal.show();
        });
        
        // Handle delete form submission
        $('#deleteForm').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const formData = form.serialize();
            const button = form.find('button[type="submit"]');
            const originalText = button.html();
            
            // Show loading state
            button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...');
            
            // Submit form via AJAX
            $.ajax({
                url: 'ajax/delete_staff.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message || 'Staff member deleted successfully',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        
                        // Close modal
                        deleteModal.hide();
                        
                        // Reload the page to update the table
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to delete staff member',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 5000
                        });
                        
                        // Reset button
                        button.prop('disabled', false).html(originalText);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting staff:', error);
                    
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while deleting the staff member. Please try again.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000
                    });
                    
                    // Reset button
                    button.prop('disabled', false).html(originalText);
                }
            });
        });
        
        // Handle add staff form submission
        $('#addStaffForm').on('submit', function(e) {
            e.preventDefault();
            
            // Check if all required libraries are loaded
            if (!checkDependencies()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Some required libraries failed to load. Please refresh the page and try again.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000
                });
                return false;
            }
            
            const form = $(this);
            const formData = new FormData(this);
            const button = form.find('button[type="submit"]');
            const originalText = button.html();
            
            // Show loading state
            button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
            
            // Log form data for debugging
            console.log('Form data:', Object.fromEntries(formData.entries()));
            
            // Submit form via AJAX
            $.ajax({
                url: 'ajax/save_staff.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Server response:', response);
                    
                    if (response && response.success) {
                        // Show success message and reload the page
                        window.location.href = 'manage_staff.php?success=' + encodeURIComponent(response.message || 'Staff member added successfully');
                    } else {
                        // Show error message with debug info if available
                        const errorMsg = response && response.message 
                            ? response.message 
                            : 'Failed to add staff member';
                            
                        const debugInfo = response && response.debug 
                            ? '\n\nDebug: ' + JSON.stringify(response.debug, null, 2) 
                            : '';
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg + debugInfo,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: true,
                            timer: 10000
                        });
                        
                        // Reset button
                        button.prop('disabled', false).html(originalText);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error
                    });
                    
                    let errorMessage = 'An error occurred while adding the staff member. ';
                    
                    try {
                        const response = xhr.responseJSON || JSON.parse(xhr.responseText);
                        if (response && response.message) {
                            errorMessage = response.message;
                        }
                        if (response && response.debug) {
                            errorMessage += '\n\nDebug: ' + JSON.stringify(response.debug, null, 2);
                        }
                    } catch (e) {
                        errorMessage += 'Please check the console for more details.';
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: true,
                        timer: 10000
                    });
                    
                    // Reset button
                    button.prop('disabled', false).html(originalText);
                }
            });
            
            return false;
        });
        
        // Handle any uncaught errors in the DataTable
        $(document).on('error', function(e) {
            console.error('Unhandled error in DataTable:', e);
        });
        
        // Handle edit staff form submission
        $(document).on('submit', '#editStaffForm', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const formData = new FormData(this);
            const button = form.find('button[type="submit"]');
            const originalText = button.html();
            
            // Show loading state
            button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
            
            // Submit form via AJAX
            $.ajax({
                url: 'ajax/update_staff.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Show success message and reload the page
                        window.location.href = 'manage_staff.php?success=' + encodeURIComponent(response.message || 'Staff member updated successfully');
                    } else {
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to update staff member',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 5000
                        });
                        
                        // Reset button
                        button.prop('disabled', false).html(originalText);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error updating staff:', error);
                    
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating the staff member. Please try again.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000
                    });
                    
                    // Reset button
                    button.prop('disabled', false).html(originalText);
                }
            });
        });
    });
</script>

</body>
</html>
