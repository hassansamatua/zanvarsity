<?php
// Start session and check authentication
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'super_admin'])) {
    header("Location: /c/zanvarsity/html/sign-in.php");
    exit();
}

// Include database connection
require_once __DIR__ . '/../includes/database.php';



// Handle form submission for add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $name = trim($_POST['name'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status = isset($_POST['status']) ? 1 : 0;
        
        // Validate input
        $errors = [];
        if (empty($name)) $errors[] = 'Faculty name is required';
        if (empty($code)) $errors[] = 'Faculty code is required';
        
        if (empty($errors)) {
            if ($_POST['action'] === 'add') {
                // Add new faculty
                $stmt = $conn->prepare("INSERT INTO faculty_tbl (faculty_name, abbreviation, created_at) VALUES (?, ?, NOW())");
                $stmt->bind_param("ss", $name, $code);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = 'Faculty added successfully';
                    header("Location: faculties.php");
                    exit();
                } else {
                    $errors[] = 'Failed to add faculty: ' . $conn->error;
                }
            } 
            elseif ($_POST['action'] === 'edit' && isset($_POST['faculty_id'])) {
                // Update existing faculty
                $faculty_id = (int)$_POST['faculty_id'];
                $stmt = $conn->prepare("UPDATE faculty_tbl SET faculty_name = ?, abbreviation = ? WHERE id = ?");
                $stmt->bind_param("ssi", $name, $code, $faculty_id);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = 'Faculty updated successfully';
                    header("Location: faculties.php");
                    exit();
                } else {
                    $errors[] = 'Failed to update faculty: ' . $conn->error;
                }
            }
        }
    }
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $faculty_id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM faculty_tbl WHERE id = ?");
    $stmt->bind_param("i", $faculty_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Faculty deleted successfully';
    } else {
        $_SESSION['error'] = 'Failed to delete faculty: ' . $conn->error;
    }
    header("Location: faculties.php");
    exit();
}

// Get all faculties
$faculties = [];
$query = "SELECT id, faculty_name as name, abbreviation as code, created_at, updated_at FROM faculty_tbl ORDER BY faculty_name ASC";
$result = $conn->query($query);

// Debug information
error_log("SQL Query: " . $query);
if ($result === false) {
    error_log("Query failed: " . $conn->error);
    $_SESSION['error'] = 'Database error: ' . $conn->error;
} else {
    $faculties = $result->fetch_all(MYSQLI_ASSOC);
    error_log("Number of faculties found: " . count($faculties));
    if (empty($faculties)) {
        error_log("No faculties found in faculty_tbl");
    }
}

// Get faculty data for editing
$edit_faculty = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $faculty_id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM faculty_tbl WHERE id = ?");
    $stmt->bind_param("i", $faculty_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_faculty = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en-US" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Theme Starz">

    <link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Action buttons styling */
        .btn-action {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border-radius: 4px;
            transition: all 0.2s ease-in-out;
        }
        
        .btn-action i {
            font-size: 14px;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .btn-edit {
            background-color: #4e73df;
            color: white;
            border: 1px solid #4e73df;
        }
        
        .btn-edit:hover {
            background-color: #2e59d9;
            color: white;
        }
        
        .btn-delete {
            background-color: #e74a3b;
            color: white;
            border: 1px solid #e74a3b;
        }
        
        .btn-delete:hover {
            background-color: #d62c1a;
            color: white;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
        }
    </style>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/selectize.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/vanillabox/vanillabox.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/style.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/green-theme.css" type="text/css">

    <title>My Account - Zanvarsity</title>
</head>

<body class="page-sub-page page-my-account">
<!-- Wrapper -->
<div class="wrapper">
<!-- Header -->
<div class="navigation-wrapper">
    <div class="secondary-navigation-wrapper">
        <div class="container">
            <div class="navigation-contact pull-left">
                <i class="fa fa-phone"></i> Call Us: <span class="opacity-70">+255 123 456 789</span>
            </div>
            <ul class="secondary-navigation list-unstyled pull-right">
                <li><a href="#tab-profile" data-toggle="tab"><i class="fa fa-user"></i>My Profile</a></li>
                <li><a href="#tab-my-courses" data-toggle="tab">My Courses</a></li>
                <li><a href="#tab-change-password" data-toggle="tab">Change Password</a></li>
                <li><a href="<?php echo dirname(dirname($_SERVER['PHP_SELF'])); ?>/logout.php" onclick="return confirm('Are you sure you want to log out?');"><i class="fa fa-sign-out"></i> Log Out</a></li>
            </ul>
        </div>
    </div><!-- /.secondary-navigation -->
    <div class="primary-navigation-wrapper">
        <header class="navbar" id="top" role="banner">
            <div class="container">
                <div class="navbar-header">
                    <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <div class="navbar-brand nav" id="brand">
                        <a href="/zanvarsity/html/index.html">
                            <img src="/zanvarsity/html/assets/img/logo.png" alt="Zanvarsity" class="logo">
                        </a>
                    </div>
                </div>
                <nav class="collapse navbar-collapse bs-navbar-collapse navbar-right" role="navigation">
                    <ul class="nav navbar-nav">
                        <li><a href="../index.php">Home</a></li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'courses.php' ? 'active' : ''; ?>">
                            <a href="../courses.php">Courses</a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'events.php' ? 'active' : ''; ?>">
                            <a href="../event.php">Events</a>
                        </li>
                        <?php if ($is_admin): ?>
                        <li>
                            <a href="../admin/dashboard.php">Admin</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav><!-- /.navbar collapse-->
            </div><!-- /.container -->
        </header><!-- /.navbar -->
    </div><!-- /.primary-navigation -->
    <div class="background">
        <img src="../assets/img/logo11.png"  alt="background">
    </div>
</div>
<!-- end Header -->

<!-- Breadcrumb -->
<div class="container">
    <ol class="breadcrumb">
        <li><a href="/zanvarsity/html/index.html">Home</a></li>
        <li class="active">My Account</li>
    </ol>
</div>
<!-- end Breadcrumb -->

<!-- Page Content -->
<div id="page-content">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <aside class="col-md-3 col-sm-4">
                <div class="sidebar">
                    <div class="sidebar-inner">
                        <div class="sidebar-widget">
                            <div class="user-avatar">
                                <div style="width: 100px; height: 100px; margin: 0 auto 15px; background-color: #4caf50; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; font-weight: bold; text-transform: uppercase; text-align: center; padding: 5px; line-height: 1.2;">
                                    <?php 
                                    $role_display = '';
                                    switch($user_role) {
                                        case 'super_admin':
                                            $role_display = 'Super Admin';
                                            break;
                                        case 'admin':
                                            $role_display = 'Admin';
                                            break;
                                        case 'instructor':
                                            $role_display = 'Instructor';
                                            break;
                                        case 'student':
                                            $role_display = 'Student';
                                            break;
                                        case 'staff':
                                            $role_display = 'Staff';
                                            break;
                                        case 'parent':
                                            $role_display = 'Parent';
                                            break;
                                        default:
                                            $role_display = 'User';
                                    }
                                    echo $role_display;
                                    ?>
                                </div>
                                <div class="text-center">
                                    <h4><?php echo htmlspecialchars($user_name); ?></h4>
                                    <span class="label label-primary"><?php echo $role_display; ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="sidebar-widget">
                            <ul class="nav nav-pills nav-stacked nav-dashboard">
                                <li class="active"><a href="my-account.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                                <?php if (in_array($user_role, ['super_admin', 'admin'])): ?>
                                <li><a href="admin/users.php"><i class="fa fa-users"></i> Manage Users</a></li>
                                <li><a href="admin/contents.php"><i class="fa fa-file-text"></i> Manage Contents</a></li>
                                <?php endif; ?>
                                <li><a href="my-courses.php"><i class="fa fa-book"></i> My Courses</a></li>
                                <li><a href="my-profile.php"><i class="fa fa-user"></i> My Profile</a></li>
                                <li><a href="settings.php"><i class="fa fa-cog"></i> Settings</a></li>
                                <?php if (in_array($user_role, ['instructor', 'admin', 'super_admin'])): ?>
                                <li><a href="instructor/"><i class="fa fa-graduation-cap"></i> Instructor Panel</a></li>
                                <?php endif; ?>
                                <li class="divider"></li>
                                <li><a href="/zanvarsity/html/logout.php" onclick="return confirm('Are you sure you want to log out?')"><i class="fa fa-sign-out"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </aside>

     <div class="container-fluid">
    <!-- Page header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?php echo $page_title; ?></h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#facultyModal">
            <i class="fas fa-plus"></i> Add New Faculty
        </button>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['success']; 
            unset($_SESSION['success']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Faculties Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Faculties List</h6>
            <div>
                <a href="?debug=1" class="btn btn-sm btn-outline-info">Show Debug Info</a>
                <a href="manage_faculties.php" class="btn btn-sm btn-outline-secondary">Hide Debug</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="facultiesTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="20%">Faculty Name</th>
                            <th width="10%">Code</th>
                            <th width="15%">Created At</th>
                            <th width="15%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($faculties)): ?>
                            <?php foreach ($faculties as $index => $faculty): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($faculty['name']); ?></td>
                                    <td><?php echo htmlspecialchars($faculty['code']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($faculty['created_at'])); ?></td>
                                    <td class="text-center">
                                        <div class="action-buttons">
                                            <a href="?action=edit&id=<?php echo $faculty['id']; ?>" 
                                               class="btn-action btn-edit" 
                                               title="Edit"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn-action btn-delete delete-faculty" 
                                                    data-id="<?php echo $faculty['id']; ?>" 
                                                    data-name="<?php echo htmlspecialchars($faculty['name']); ?>"
                                                    title="Delete"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No faculties found. Add your first faculty using the button above.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php if (isset($_GET['debug'])): ?>
    <div class="card shadow mb-4 mt-4">
        <div class="card-header bg-info text-white">
            <h6 class="m-0 font-weight-bold">Debug Information</h6>
        </div>
        <div class="card-body">
            <h5>Database Connection:</h5>
            <pre><?php 
                echo "Server: {$servername}\n";
                echo "Database: {$dbname}\n";
                echo "Connected: " . ($conn ? 'Yes' : 'No') . "\n";
                if ($conn) {
                    echo "Server version: " . $conn->server_info . "\n";
                    echo "Error: " . $conn->error . "\n";
                }
            ?></pre>
            
            <h5 class="mt-4">Query Results:</h5>
            <pre>Query: <?php echo htmlspecialchars($query); ?>
Number of faculties: <?php echo count($faculties); ?>

<?php 
if (!empty($faculties)) {
    echo "First faculty: \n";
    print_r($faculties[0]);
} else {
    // Check if table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'faculty_tbl'");
    $table_exists = $table_check && $table_check->num_rows > 0;
    echo "Table 'faculty_tbl' exists: " . ($table_exists ? 'Yes' : 'No') . "\n";
    
    if ($table_exists) {
        // Get table structure
        $result = $conn->query("DESCRIBE faculty_tbl");
        if ($result) {
            echo "\nTable structure:\n";
            while ($row = $result->fetch_assoc()) {
                echo "{$row['Field']} ({$row['Type']}) " . ($row['Null'] == 'YES' ? 'NULL' : 'NOT NULL') . "\n";
            }
        }
    }
}
?></pre>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the faculty: <strong id="facultyName"></strong>?</p>
                    <p class="text-danger"><small>This action cannot be undone.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Faculty Modal -->
<div class="modal fade" id="facultyModal" tabindex="-1" aria-labelledby="facultyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="facultyForm" method="POST" action="">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="facultyModalLabel">
                        <?php echo isset($edit_faculty) ? 'Edit Faculty' : 'Add New Faculty'; ?>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Error messages will be shown here -->
                    <div id="formError" class="alert alert-danger d-none"></div>
                    
                    <input type="hidden" name="action" value="<?php echo isset($edit_faculty) ? 'edit' : 'add'; ?>">
                    <?php if (isset($edit_faculty)): ?>
                        <input type="hidden" name="faculty_id" value="<?php echo $edit_faculty['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Faculty Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required 
                               value="<?php echo isset($edit_faculty) ? htmlspecialchars($edit_faculty['name']) : ''; ?>"
                               placeholder="Enter faculty name" autofocus>
                        <div class="invalid-feedback">Please provide a faculty name.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="code" required 
                               value="<?php echo isset($edit_faculty) ? htmlspecialchars($edit_faculty['code']) : ''; ?>"
                               placeholder="e.g., FST, FBE" maxlength="10">
                        <div class="invalid-feedback">Please provide a faculty code/abbreviation.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="2"
                                 placeholder="Optional description"><?php 
                            echo isset($edit_faculty) ? htmlspecialchars($edit_faculty['description']) : ''; 
                        ?></textarea>
                    </div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1"
                            <?php echo (isset($edit_faculty) && $edit_faculty['status']) || !isset($edit_faculty) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="status">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveFacultyBtn">
                        <i class="fas fa-save me-1"></i>
                        <?php echo isset($edit_faculty) ? 'Update' : 'Save'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="facultyName"></strong>? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>
<style>
    /* Sidebar Styles */
    .sidebar {
        background: #fff;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        overflow: hidden;
    }
    
    .sidebar-widget {
        padding: 20px;
    }
    
    .sidebar-widget:not(:last-child) {
        border-bottom: 1px solid #eee;
    }
    
    .user-avatar {
        text-align: center;
        padding: 20px 0;
    }
    
    .user-avatar h4 {
        margin: 10px 0 5px;
        font-weight: 600;
    }
    
    .label {
        display: inline-block;
        padding: 3px 8px;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.5;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 3px;
    }
    
    .label-primary {
        background-color: #4caf50;
    }
    
    /* Navigation */
    .nav-dashboard {
        margin: 0 -20px;
    }
    
    .nav-dashboard > li > a {
        padding: 12px 20px;
        color: #555;
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
    }
    
    .nav-dashboard > li > a:hover,
    .nav-dashboard > li.active > a {
        background-color: #f8f9fa;
        color: #4caf50;
        border-left-color: #4caf50;
    }
    
    .nav-dashboard > li > a i {
        margin-right: 8px;
        width: 20px;
        text-align: center;
    }
    
    .nav-dashboard > li.divider {
        height: 1px;
        margin: 9px 0;
        overflow: hidden;
        background-color: #e5e5e5;
    }
    
    /* Dashboard Content */
    .feature-box {
        background: #fff;
        padding: 20px;
        border-radius: 4px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
    }
    
    .feature-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .feature-box-icon {
        margin-bottom: 15px;
    }
    
    .feature-box-icon i {
        font-size: 36px;
    }
    
    .feature-box h3 {
        font-size: 16px;
        font-weight: 600;
        margin: 0 0 10px;
    }
    
    /* Responsive */
    @media (max-width: 991px) {
        .sidebar {
            margin-bottom: 30px;
        }
    }
    
    @media (max-width: 767px) {
        .feature-box {
            margin-bottom: 20px;
        }
    }
</style>

<!-- Add DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css"/>

<!-- Include jQuery first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Then include DataTables -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<!-- Then Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Wait for the document to be fully loaded
// Minimal DataTable initialization
$(document).ready(function() {
    console.log('Document ready, starting DataTable initialization...');
    
    // First, verify the table exists
    var $table = $('#facultiesTable');
    if ($table.length === 0) {
        console.error('Error: Table with ID "facultiesTable" not found');
        return;
    }
    
    // Log table structure for debugging
    console.log('Table found with', $table.find('thead th').length, 'header columns and', 
                $table.find('tbody tr').length, 'data rows');
    
    // Initialize with minimal configuration
    try {
        var dataTable = $table.DataTable({
            "paging": false,
            "searching": false,
            "ordering": false,
            "info": false,
            "autoWidth": false,
            "responsive": false,
            "retrieve": true
        });
        
        console.log('DataTable initialized successfully with minimal configuration');
        
        // If minimal config works, add more features
        if (dataTable) {
            dataTable.destroy();
            
            // Reinitialize with more features
            $table.DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "columnDefs": [
                    { "orderable": false, "targets": [0, 4] } // Only 5 columns (0-4)
                ],
                "order": [[1, 'asc']], // Sort by Faculty Name by default
                "language": {
                    "emptyTable": "No faculties found. Add your first faculty using the button above.",
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "No entries to show",
                    "infoFiltered": "(filtered from _MAX_ total entries)"
                },
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });
            
            console.log('DataTable reinitialized with full features');
        }
    } catch (e) {
        console.error('Error initializing DataTable:', e);
    }
    
        // Handle form submission with AJAX
    $(document).on('submit', '#facultyForm', function(e) {
        e.preventDefault();
        
        // Reset previous states
        $('.is-invalid').removeClass('is-invalid');
        $('#formError').addClass('d-none').empty();
        
        // Get form data
        const $form = $(this);
        const formAction = $form.attr('action') || 'manage_faculties.php';
        const formMethod = $form.attr('method') || 'POST';
        const $submitBtn = $form.find('button[type="submit"]');
        const originalBtnText = $submitBtn.html();
        
        // Get form values
        const name = $('#name').val().trim();
        const code = $('#code').val().trim();
        const description = $('#description').val().trim();
        const status = $('#status').is(':checked') ? 1 : 0;
        const action = $('input[name="action"]').val();
        const facultyId = $('input[name="faculty_id"]').val();
        
        // Validate form
        let isValid = true;
        if (!name) {
            $('#name').addClass('is-invalid');
            isValid = false;
        }
        
        if (!code) {
            $('#code').addClass('is-invalid');
            isValid = false;
        }
        
        if (!isValid) {
            return false;
        }
        
        // Show loading state
        $submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...').prop('disabled', true);
        
        // Prepare form data
        const formData = new FormData();
        formData.append('action', action);
        formData.append('name', name);
        formData.append('code', code);
        formData.append('description', description);
        formData.append('status', status);
        
        // Add faculty_id for edit mode
        if (facultyId) {
            formData.append('faculty_id', facultyId);
        }
        
        // Send AJAX request
        $.ajax({
            url: formAction,
            type: formMethod,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response && response.success) {
                    // Show success message
                    const successMsg = formData.action === 'add' ? 'Faculty added successfully!' : 'Faculty updated successfully!';
                    
                    // Show success alert
                    const alertHtml = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i> ${successMsg}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    
                    // Add alert before the card
                    $('.card').first().before(alertHtml);
                    
                    // Close modal and reload the page after a short delay
                    $('#facultyModal').modal('hide');
                    
                    // Reload the page after a short delay to show the success message
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    // Show error message
                    let errorMsg = 'An error occurred. Please try again.';
                    
                    if (response && response.message) {
                        errorMsg = response.message;
                    } else if (response && response.errors) {
                        // Handle validation errors
                        let errorList = '<ul class="mb-0">';
                        $.each(response.errors, function(field, message) {
                            errorList += `<li>${message}</li>`;
                            $(`#${field}`).addClass('is-invalid');
                        });
                        errorList += '</ul>';
                        errorMsg = errorList;
                    }
                    
                    $('#formError').html(errorMsg).removeClass('d-none');
                    
                    // Scroll to the error message
                    $('html, body').animate({
                        scrollTop: $('#formError').offset().top - 100
                    }, 500);
                }
                
                // Re-enable the submit button
                $submitBtn.html(originalBtnText).prop('disabled', false);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                $('#formError').html('An error occurred while processing your request. Please try again.').removeClass('d-none');
                $submitBtn.html(originalBtnText).prop('disabled', false);
            }
        });
    });
    
    // Handle delete button click using event delegation
    $(document).on('click', '.delete-faculty', function(e) {
        e.preventDefault();
        var $button = $(this);
        var facultyId = $button.data('id');
        var facultyName = $button.data('name');
        
        // Update modal content
        $('#facultyName').text(facultyName);
        
        // Store the button for later use
        var $deleteModal = $('#deleteModal');
        
        // Update confirm button action
        $('#confirmDelete').off('click').on('click', function(e) {
            e.preventDefault();
            
            // Show loading state
            var $confirmBtn = $(this);
            var originalText = $confirmBtn.html();
            $confirmBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...').prop('disabled', true);
            
            // Send delete request
            $.ajax({
                url: 'manage_faculties.php',
                type: 'GET',
                data: {
                    action: 'delete',
                    id: facultyId
                },
                success: function(response) {
                    // Show success message
                    $deleteModal.modal('hide');
                    
                    // Reload the page to see changes
                    window.location.reload();
                },
                error: function(xhr, status, error) {
                    // Show error message
                    alert('Error deleting faculty: ' + error);
                    $confirmBtn.html(originalText).prop('disabled', false);
                }
            });
        });
        // Show the modal
        $deleteModal.modal('show');
    });
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Handle modal hidden event to reset form
    $('#facultyModal').on('hidden.bs.modal', function () {
        $('#facultyForm')[0].reset();
        $('#facultyForm input[name="action"]').val('add');
        $('#facultyForm input[name="faculty_id"]').remove();
        $('#facultyModalLabel').text('Add New Faculty');
    });

    // Show edit modal if there's an edit action in the URL
    <?php if (isset($edit_faculty)): ?>
    $(document).ready(function() {
        // Set form values for editing
        if (typeof $edit_faculty !== 'undefined') {
            $('#name').val('<?php echo addslashes($edit_faculty['faculty_name']); ?>');
            $('#code').val('<?php echo addslashes($edit_faculty['abbreviation']); ?>');
            $('#description').val('<?php echo addslashes($edit_faculty['description']); ?>');
            $('#status').prop('checked', <?php echo $edit_faculty['status'] ? 'true' : 'false'; ?>);
            
            // Update form action and method
            $('#facultyForm').attr('action', '?action=edit&id=<?php echo $edit_faculty['id']; ?>');
            
            // Show the modal
            var facultyModal = new bootstrap.Modal(document.getElementById('facultyModal'));
            facultyModal.show();
        }
    });
    <?php endif; ?>
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<?php
// Include footer
include __DIR__ . '/includes/footer.php';
?>
