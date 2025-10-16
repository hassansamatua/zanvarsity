<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define root path - point to zanvarsity directory
define('ROOT_PATH', dirname(dirname(dirname(__DIR__))));

// Include necessary files
require_once ROOT_PATH . '/zanvarsity/includes/auth_functions.php';
require_once ROOT_PATH . '/zanvarsity/includes/database.php';

// Check if user is logged in
require_login();

// Get user information from session
$user_id = $_SESSION['user_id'] ?? null;
$user_email = $_SESSION['email'] ?? 'Guest';
$user_name = $_SESSION['first_name'] ?? 'User';
$user_role = $_SESSION['role'] ?? 'student';
$is_admin = in_array($user_role, ['admin', 'super_admin']);
$is_dean = ($user_role === 'dean');

// Verify admin access
if (!$is_admin) {
    header('HTTP/1.0 403 Forbidden');
    die('Access Denied');
}

// Handle User Management Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_admin) {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_user':
            $first_name = trim($_POST['first_name'] ?? '');
            $last_name = trim($_POST['last_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'admin';
            $faculty = $_POST['faculty'] ?? null;
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            // Validate faculty for dean role
            if ($role === 'dean' && empty($faculty)) {
                $_SESSION['error'] = "Faculty selection is required for Dean role.";
                header("Location: users.php");
                exit();
            }
            
            // Basic validation
            if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
                $_SESSION['error'] = "All fields are required.";
                header("Location: users.php");
                exit();
            }
            
            // Check if email already exists
            $check_sql = "SELECT id FROM users WHERE email = ?";
            if ($stmt = $conn->prepare($check_sql)) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $_SESSION['error'] = "Email already exists.";
                    header("Location: users.php");
                    exit();
                }
                $stmt->close();
            }
            
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $sql = "INSERT INTO users (first_name, last_name, email, password, role, faculty, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ssssssi", $first_name, $last_name, $email, $hashed_password, $role, $faculty, $is_active);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = "User added successfully!";
                } else {
                    $_SESSION['error'] = "Error adding user: " . $conn->error;
                }
                $stmt->close();
            } else {
                $_SESSION['error'] = "Database error: " . $conn->error;
            }
            
            header("Location: users.php");
            exit();
            
        case 'update_role':
            $user_id_to_update = intval($_POST['user_id'] ?? 0);
            $new_role = $_POST['new_role'] ?? 'student';
            
            // Prevent changing your own role
            if ($user_id_to_update === $_SESSION['user_id']) {
                $_SESSION['error'] = "You cannot change your own role.";
                header("Location: users.php");
                exit();
            }
            
            $valid_roles = ['admin', 'dean'];
            if (in_array($new_role, $valid_roles)) {
                $sql = "UPDATE users SET role = ? WHERE id = ?";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("si", $new_role, $user_id_to_update);
                    if ($stmt->execute()) {
                        $_SESSION['success'] = "User role updated successfully!";
                    } else {
                        $_SESSION['error'] = "Error updating user role: " . $conn->error;
                    }
                    $stmt->close();
                }
            }
            
            header("Location: users.php");
            exit();
            
        case 'update_user':
            $user_id_to_update = intval($_POST['user_id'] ?? 0);
            $first_name = trim($_POST['first_name'] ?? '');
            $last_name = trim($_POST['last_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = $_POST['role'] ?? 'admin';
            $faculty = $_POST['faculty'] ?? null;
            $status = isset($_POST['status']) ? 1 : 0;
            
            // Validate faculty for dean role
            if ($role === 'dean' && empty($faculty)) {
                $_SESSION['error'] = "Faculty selection is required for Dean role.";
                header("Location: users.php");
                exit();
            }
            
            // Debug logging
            error_log("Update user attempt - ID: $user_id_to_update, Name: $first_name $last_name, Email: $email");
            
            // Basic validation
            if (empty($first_name) || empty($last_name) || empty($email)) {
                $_SESSION['error'] = "First name, last name, and email are required.";
                error_log("Update user failed - validation error: missing required fields");
                header("Location: users.php");
                exit();
            }
            
            if ($user_id_to_update <= 0) {
                $_SESSION['error'] = "Invalid user ID.";
                error_log("Update user failed - invalid user ID: $user_id_to_update");
                header("Location: users.php");
                exit();
            }
            
            // Check if email already exists for other users
            $check_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
            if ($stmt = $conn->prepare($check_sql)) {
                $stmt->bind_param("si", $email, $user_id_to_update);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $_SESSION['error'] = "Email already exists for another user.";
                    header("Location: users.php");
                    exit();
                }
                $stmt->close();
            }
            
            // Update user
            $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ?, faculty = ?, status = ?, updated_at = NOW() WHERE id = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("sssssii", $first_name, $last_name, $email, $role, $faculty, $status, $user_id_to_update);
                if ($stmt->execute()) {
                    $_SESSION['success'] = "User updated successfully!";
                } else {
                    $_SESSION['error'] = "Error updating user: " . $conn->error;
                }
                $stmt->close();
            } else {
                $_SESSION['error'] = "Database error: " . $conn->error;
            }
            
            header("Location: users.php");
            exit();
            
        case 'delete_user':
            $user_id_to_delete = intval($_POST['user_id'] ?? 0);
            
            // Debug logging
            error_log("Delete user attempt - ID: $user_id_to_delete, Current user: " . $_SESSION['user_id']);
            
            // Validate user ID
            if ($user_id_to_delete <= 0) {
                $_SESSION['error'] = "Invalid user ID.";
                error_log("Delete user failed - invalid user ID: $user_id_to_delete");
                header("Location: users.php");
                exit();
            }
            
            // Prevent deleting yourself
            if ($user_id_to_delete === $_SESSION['user_id']) {
                $_SESSION['error'] = "You cannot delete your own account.";
                error_log("Delete user failed - attempting to delete own account");
                header("Location: users.php");
                exit();
            }
            
            $sql = "DELETE FROM users WHERE id = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $user_id_to_delete);
                if ($stmt->execute()) {
                    $_SESSION['success'] = "User deleted successfully!";
                } else {
                    $_SESSION['error'] = "Error deleting user: " . $conn->error;
                }
                $stmt->close();
            }
            
            header("Location: users.php");
            exit();
    }
}

// Get user data from database
if (isset($conn) && $user_id) {
    $query = "SELECT id, email, first_name, last_name, phone, bio, profile_image, role FROM users WHERE id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($user = $result->fetch_assoc()) {
                // Update session data from database
                if (!empty($user['first_name'])) {
                    $user_name = $user['first_name'];
                    $_SESSION['first_name'] = $user_name;
                }
                if (!empty($user['role'])) {
                    $user_role = $user['role'];
                    $_SESSION['role'] = $user_role;
                    $is_admin = in_array($user_role, ['admin', 'super_admin']);
                }
                if (!empty($user['profile_image'])) {
                    $_SESSION['profile_image'] = $user['profile_image'];
                }
            }
        }
        $stmt->close();
    }
}

// Set page title and heading
$page_title = 'User Management | Zanvarsity';
$page_heading = 'User Management';

// Include header
include '../includes/header.php';
?>

<!-- Additional CSS for Bootstrap and FontAwesome -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!-- Fallback for FontAwesome -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

<!-- Custom styles for the dashboard -->
<style>
  /* Base font styling to match my-account.php */
  body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    line-height: 1.6;
    color: #333;
    background-color: #f8f9fa;
    font-size: 14px;
  }
  
  /* Typography consistency */
  h1, h2, h3, h4, h5, h6 {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    font-weight: 600;
    line-height: 1.2;
    color: #333;
  }
  
  .card-header h5 {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0;
  }
  
  .table {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    font-size: 14px;
  }
  
  .btn {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    font-size: 14px;
    font-weight: 500;
  }
  
  .form-control, .form-select {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    font-size: 14px;
  }
  
  .form-label {
    font-weight: 500;
    color: #333;
  }
  
  /* Dashboard Styles */
  .dashboard-container {
    padding: 40px 0;
    background: #f8f9fa;
    min-height: calc(100vh - 200px);
  }

  .dashboard-header {
    margin-bottom: 30px;
    text-align: center;
  }

  .dashboard-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 25px;
    margin-bottom: 30px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
  }

  /* Sidebar Styles */
  .sidebar {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    margin-bottom: 30px;
  }

  .profile-card {
    text-align: center;
    padding: 25px 15px;
    background: linear-gradient(135deg, #014421 0%, #027333 100%);
    color: white;
  }

  .profile-image {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    border: 4px solid rgba(255, 255, 255, 0.2);
    margin: 0 auto 15px;
    overflow: hidden;
  }

  .profile-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .profile-name {
    font-size: 1.3rem;
    font-weight: 600;
    margin: 10px 0 5px;
  }

  .profile-role {
    font-size: 0.9rem;
    opacity: 0.9;
    text-transform: capitalize;
  }

  .sidebar-menu {
    padding: 0;
    margin: 0;
    list-style: none;
  }

  .sidebar-menu li {
    border-bottom: 1px solid #f0f0f0;
  }

  .sidebar-menu li:last-child {
    border-bottom: none;
  }

  .sidebar-menu a {
    display: block;
    padding: 12px 20px;
    color: #555;
    text-decoration: none;
    transition: all 0.3s;
  }

  .sidebar-menu a:hover,
  .sidebar-menu a.active {
    background: #f8f9fa;
    color: #014421;
    padding-left: 25px;
  }

  .sidebar-menu i {
    width: 20px;
    margin-right: 10px;
    text-align: center;
  }

  /* Responsive adjustments */
  @media (max-width: 991px) {
    .sidebar {
      margin-bottom: 30px;
    }
  }
  
  /* Modal fallback styles */
  .modal.show {
    display: block !important;
    opacity: 1;
  }
  
  .modal {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1055;
    width: 100%;
    height: 100%;
    overflow-x: hidden;
    overflow-y: auto;
    outline: 0;
  }
  
  .modal-dialog {
    position: relative;
    width: auto;
    margin: 1.75rem auto;
    max-width: 500px;
  }
  
  .modal-content {
    position: relative;
    display: flex;
    flex-direction: column;
    width: 100%;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid rgba(0,0,0,.2);
    border-radius: 0.3rem;
    box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,.5);
  }
  
  .modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1050;
    width: 100vw;
    height: 100vh;
    background-color: #000;
  }
  
  .modal-backdrop.show {
    opacity: 0.5;
  }
  
  .modal-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 1rem 1rem;
    border-bottom: 1px solid #dee2e6;
    border-top-left-radius: calc(0.3rem - 1px);
    border-top-right-radius: calc(0.3rem - 1px);
  }
  
  .modal-body {
    position: relative;
    flex: 1 1 auto;
    padding: 1rem;
  }
  
  .modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 0.75rem;
    border-top: 1px solid #dee2e6;
    border-bottom-right-radius: calc(0.3rem - 1px);
    border-bottom-left-radius: calc(0.3rem - 1px);
  }
  
  .btn-close {
    box-sizing: content-box;
    width: 1em;
    height: 1em;
    padding: 0.25em 0.25em;
    color: #000;
    background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='m.235.677l15.09 15.09a.5.5 0 0 0 .707-.707L.942.03a.5.5 0 0 0-.707.647z'/%3e%3cpath d='m15.265.677l-15.09 15.09a.5.5 0 0 0 .707.707L15.97.97a.5.5 0 0 0-.707-.647z'/%3e%3c/svg%3e") center/1em auto no-repeat;
    border: 0;
    border-radius: 0.25rem;
    opacity: 0.5;
  }
  
  .btn-close:hover {
    color: #000;
    text-decoration: none;
    opacity: 0.75;
  }
  
  /* Profile image styling */
  .profile-image {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    position: relative;
    overflow: hidden;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }
  
  .profile-image img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    object-position: center;
  }
  
  /* Table profile images */
  .table td {
    vertical-align: middle;
    text-align: center;
  }
  
  .table td img {
    width: 40px !important;
    height: 40px !important;
    border-radius: 50%;
    object-fit: cover;
    object-position: center;
    border: 2px solid #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
  }
  
  /* Ensure consistent sizing for fallback avatars */
  .table td > div {
    margin: 0 auto;
  }
</style>

<!-- Main Content -->
<div class="dashboard-container">
  <div class="container">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-lg-3">
        <div class="sidebar">
          <div class="profile-card">
            <div class="profile-image">
             <?php 
             $profile_image = !empty($user['profile_image']) ? $user['profile_image'] : '';
             
             if ($profile_image) {
                 // Debug: Show what we're working with
                 echo "<!-- Debug Sidebar: Profile image from DB: " . htmlspecialchars($profile_image) . " -->";
                 
                 // Clean the profile image path - same logic as table
                 $clean_filename = $profile_image;
                 
                 // Remove file:// protocol if present
                 if (strpos($clean_filename, 'file://') === 0) {
                     $clean_filename = substr($clean_filename, 7); // Remove 'file://'
                 }
                 
                 // Remove full Windows paths
                 if (strpos($clean_filename, 'C:/') === 0 || strpos($clean_filename, 'C:\\') === 0) {
                     $clean_filename = basename($clean_filename);
                 } else {
                     $clean_filename = basename($clean_filename);
                 }
                 
                 // Since we're in admin folder, we need to go up one level to access uploads
                 $web_path = '../uploads/profiles/' . $clean_filename;
                 $server_path = $_SERVER['DOCUMENT_ROOT'] . '/c/zanvarsity/html/uploads/profiles/' . $clean_filename;
                 
                 echo "<!-- Debug Sidebar: Clean filename: " . htmlspecialchars($clean_filename) . " -->";
                 echo "<!-- Debug Sidebar: Web path: " . htmlspecialchars($web_path) . " -->";
                 echo "<!-- Debug Sidebar: File exists: " . (file_exists($server_path) ? 'YES' : 'NO') . " -->";
                 
                 // Skip placeholder or invalid filenames
                 if ($clean_filename && $clean_filename !== 'avatar-placeholder.png' && $clean_filename !== 'placeholder.png') {
                     echo '<img src="' . htmlspecialchars($web_path) . '" alt="Profile Image" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">';
                 } else {
                     echo "<!-- Debug Sidebar: Skipping placeholder filename: " . htmlspecialchars($clean_filename) . " -->";
                 }
             }
             
             // Fallback avatar (always present, hidden if image loads successfully)
             $show_avatar = !$profile_image || (isset($clean_filename) && ($clean_filename === 'avatar-placeholder.png' || $clean_filename === 'placeholder.png'));
             echo '<div style="width: 80px; height: 80px; background-color: #4caf50; border-radius: 50%; display: ' . ($show_avatar ? 'flex' : 'none') . '; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: bold; margin: 0 auto;">' . strtoupper(substr($user_name, 0, 1)) . '</div>';
             ?>
            </div>
            <h3 class="profile-name"><?php echo htmlspecialchars($user_name); ?></h3>
            <div class="profile-role"><?php echo ucfirst(htmlspecialchars($user_role)); ?></div>
          </div>
          <ul class="sidebar-menu">
            <li><a href="../my-account.php?tab=dashboard"><i class="fa fa-tachometer"></i> Dashboard</a></li>
            <li><a href="../my-account.php?tab=profile"><i class="fa fa-user"></i> My Profile</a></li>
            <li><a href="users.php" class="active"><i class="fa fa-users-cog"></i> Manage Users</a></li>
            <li><a href="../my-account.php?tab=messages"><i class="fa fa-envelope"></i> Messages</a></li>
            <li><a href="../my-account.php?tab=settings"><i class="fa fa-cog"></i> Settings</a></li>
            <li><a href="../logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
          </ul>
        </div>
      </div>

      <!-- Main Content -->
      <div class="col-lg-9">
        <!-- Manage Users Section -->
        <div class="manage-users">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fa fa-users-cog"></i> Manage Users</h2>
            <button type="button" class="btn btn-primary" onclick="showAddUserModal()">
              <i class="fa fa-plus"></i> Add New User
            </button>
          </div>

          <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
          <?php endif; ?>
          
          <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
          <?php endif; ?>

          <div class="card">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead class="table-light">
                    <tr>
                      <th>ID</th>
                      <th>Profile</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Role</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $sql = "SELECT id, first_name, last_name, email, role, status, profile_image FROM users ORDER BY created_at DESC";
                    $result = $conn->query($sql);
                    
                    // Debug: Check if query was successful
                    if ($result === false) {
                        echo '<tr><td colspan="7" class="text-center text-danger">Database Error: ' . htmlspecialchars($conn->error) . '</td></tr>';
                    } elseif ($result->num_rows > 0) {
                        while ($user_row = $result->fetch_assoc()) {
                            $is_current_user = ($user_row['id'] == $_SESSION['user_id']);
                            ?>
                            <tr>
                              <td>#<?php echo $user_row['id']; ?></td>
                              <td>
                                <?php 
                                $profile_image = !empty($user_row['profile_image']) ? $user_row['profile_image'] : '';
                                if ($profile_image) {
                                    // Debug: Show what we're working with
                                    echo "<!-- Debug User ID " . $user_row['id'] . ": Profile image from DB: " . htmlspecialchars($profile_image) . " -->";
                                    
                                    // Clean the profile image path - remove any file:// or full paths
                                    $clean_filename = $profile_image;
                                    
                                    // Remove file:// protocol if present
                                    if (strpos($clean_filename, 'file://') === 0) {
                                        $clean_filename = substr($clean_filename, 7); // Remove 'file://'
                                    }
                                    
                                    // Remove full Windows paths
                                    if (strpos($clean_filename, 'C:/') === 0 || strpos($clean_filename, 'C:\\') === 0) {
                                        $clean_filename = basename($clean_filename);
                                    } else {
                                        $clean_filename = basename($clean_filename);
                                    }
                                    
                                    // Since we're in admin folder, we need to go up one level to access uploads
                                    $web_path = '../uploads/profiles/' . $clean_filename;
                                    $server_path = $_SERVER['DOCUMENT_ROOT'] . '/c/zanvarsity/html/uploads/profiles/' . $clean_filename;
                                    
                                    echo "<!-- Debug: Clean filename: " . htmlspecialchars($clean_filename) . " -->";
                                    echo "<!-- Debug: Web path: " . htmlspecialchars($web_path) . " -->";
                                    echo "<!-- Debug: Server path: " . htmlspecialchars($server_path) . " -->";
                                    echo "<!-- Debug: File exists: " . (file_exists($server_path) ? 'YES' : 'NO') . " -->";
                                    
                                    // Skip placeholder or invalid filenames
                                    if ($clean_filename && $clean_filename !== 'avatar-placeholder.png' && $clean_filename !== 'placeholder.png') {
                                        // Always try to display the image with the correct web path
                                        echo '<img src="' . htmlspecialchars($web_path) . '" alt="Profile" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">';
                                    } else {
                                        echo "<!-- Debug: Skipping placeholder filename: " . htmlspecialchars($clean_filename) . " -->";
                                    }
                                }
                                
                                // Fallback avatar (always present, hidden if image loads successfully)
                                $show_avatar = !$profile_image || (isset($clean_filename) && ($clean_filename === 'avatar-placeholder.png' || $clean_filename === 'placeholder.png'));
                                echo '<div style="width: 40px; height: 40px; background-color: #4caf50; border-radius: 50%; display: ' . ($show_avatar ? 'flex' : 'none') . '; align-items: center; justify-content: center; color: white; font-size: 14px; font-weight: bold; margin: 0 auto;">' . strtoupper(substr($user_row['first_name'], 0, 1)) . '</div>';
                                ?>
                              </td>
                              <td><?php echo htmlspecialchars($user_row['first_name'] . ' ' . $user_row['last_name']); ?></td>
                              <td><?php echo htmlspecialchars($user_row['email']); ?></td>
                              <td>
                                <?php if ($is_current_user): ?>
                                  <?php echo ucfirst($user_row['role']); ?>
                                <?php else: ?>
                                  <form method="post" class="d-inline" onchange="this.submit()">
                                    <input type="hidden" name="action" value="update_role">
                                    <input type="hidden" name="user_id" value="<?php echo $user_row['id']; ?>">
                                    <select name="new_role" class="form-select form-select-sm" style="width: auto;">
                                      <option value="admin" <?php echo $user_row['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                      <option value="dean" <?php echo $user_row['role'] === 'dean' ? 'selected' : ''; ?>>Dean</option>
                                    </select>
                                  </form>
                                <?php endif; ?>
                              </td>
                              <td>
                                <span class="badge <?php echo $user_row['status'] ? 'bg-success' : 'bg-secondary'; ?>">
                                  <?php echo $user_row['status'] ? 'Active' : 'Inactive'; ?>
                                </span>
                              </td>
                              <td>
                                <div class="btn-group">
                                  <button type="button" class="btn btn-sm btn-info" title="Edit" 
                                          onclick="editUser(<?php echo $user_row['id']; ?>, '<?php echo htmlspecialchars($user_row['first_name']); ?>', '<?php echo htmlspecialchars($user_row['last_name']); ?>', '<?php echo htmlspecialchars($user_row['email']); ?>', '<?php echo $user_row['role']; ?>', <?php echo $user_row['status']; ?>)">
                                    <i class="fa fa-edit"></i>
                                  </button>
                                  <?php if (!$is_current_user): ?>
                                    <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                      <input type="hidden" name="action" value="delete_user">
                                      <input type="hidden" name="user_id" value="<?php echo $user_row['id']; ?>">
                                      <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fa fa-trash"></i>
                                      </button>
                                    </form>
                                  <?php endif; ?>
                                </div>
                              </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo '<tr><td colspan="7" class="text-center">No users found</td></tr>';
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Add User Modal -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close" onclick="closeModal('addUserModal')" aria-label="Close"></button>
              </div>
              <form method="post" action="">
                <div class="modal-body">
                  <input type="hidden" name="action" value="add_user">
                  <div class="mb-3">
                    <label for="firstName" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="firstName" name="first_name" required>
                  </div>
                  <div class="mb-3">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="lastName" name="last_name" required>
                  </div>
                  <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                  </div>
                  <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                  </div>
                  <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" required onchange="toggleFacultyField()">
                      <option value="">Select Role</option>
                      <option value="admin">Admin</option>
                      <option value="dean">Dean</option>
                    </select>
                  </div>
                  <div class="mb-3" id="facultyField" style="display: none;">
                    <label for="faculty" class="form-label">Faculty</label>
                    <select class="form-select" id="faculty" name="faculty">
                      <option value="">Select Faculty</option>
                      <option value="FBA">Faculty of Business Administration</option>
                      <option value="FLS">Faculty of Law and Shariah</option>
                      <option value="FASS">Faculty of Arts and Social Sciences</option>
                      <option value="FOE">Faculty of Engineering</option>
                      <option value="FOHAS">Faculty of Health and Allied Sciences</option>
                      <option value="FOS">Faculty of Science</option>
                      <option value="IPGSR">Institute of Postgraduate Studies and Research</option>
                      <option value="IIBF">Institute of Islamic Banking and Finance</option>
                      <option value="ICE">Institute of Continuing Education</option>
                    </select>
                  </div>
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1" checked>
                    <label class="form-check-label" for="isActive">
                      Active
                    </label>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" onclick="closeModal('addUserModal')">Cancel</button>
                  <button type="submit" class="btn btn-primary">Add User</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <!-- end Add User Modal -->

        <!-- Edit User Modal -->
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" onclick="closeModal('editUserModal')" aria-label="Close"></button>
              </div>
              <form method="post" action="" id="editUserForm">
                <div class="modal-body">
                  <input type="hidden" name="action" value="update_user">
                  <input type="hidden" name="user_id" id="edit_user_id">
                  <div class="mb-3">
                    <label for="editFirstName" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="editFirstName" name="first_name" required>
                  </div>
                  <div class="mb-3">
                    <label for="editLastName" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="editLastName" name="last_name" required>
                  </div>
                  <div class="mb-3">
                    <label for="editEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="editEmail" name="email" required>
                  </div>
                  <div class="mb-3">
                    <label for="editRole" class="form-label">Role</label>
                    <select class="form-select" id="editRole" name="role" required onchange="toggleEditFacultyField()">
                      <option value="admin">Admin</option>
                      <option value="dean">Dean</option>
                    </select>
                  </div>
                  <div class="mb-3" id="editFacultyField" style="display: none;">
                    <label for="editFaculty" class="form-label">Faculty</label>
                    <select class="form-select" id="editFaculty" name="faculty">
                      <option value="">Select Faculty</option>
                      <option value="FBA">Faculty of Business Administration</option>
                      <option value="FLS">Faculty of Law and Shariah</option>
                      <option value="FASS">Faculty of Arts and Social Sciences</option>
                      <option value="FOE">Faculty of Engineering</option>
                      <option value="FOHAS">Faculty of Health and Allied Sciences</option>
                      <option value="FOS">Faculty of Science</option>
                      <option value="IPGSR">Institute of Postgraduate Studies and Research</option>
                      <option value="IIBF">Institute of Islamic Banking and Finance</option>
                      <option value="ICE">Institute of Continuing Education</option>
                    </select>
                  </div>
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="editIsActive" name="status" value="1">
                    <label class="form-check-label" for="editIsActive">
                      Active
                    </label>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" onclick="closeModal('editUserModal')">Cancel</button>
                  <button type="submit" class="btn btn-primary">Update User</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <!-- end Edit User Modal -->
      </div>
    </div>
  </div>
</div>

<!-- jQuery for modal functionality -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap CSS only, no JS to avoid conflicts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom Modal Implementation -->
<script>
// Simple modal functions
function showModal(modalId) {
    console.log('Showing modal:', modalId);
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        
        // Add backdrop
        let backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'modal-backdrop';
        backdrop.onclick = function() { hideModal(modalId); };
        document.body.appendChild(backdrop);
        
        console.log('Modal should be visible now');
    } else {
        console.error('Modal not found:', modalId);
    }
}

function hideModal(modalId) {
    console.log('Hiding modal:', modalId);
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        document.body.style.overflow = '';
        
        // Remove backdrop
        const backdrop = document.getElementById('modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    }
}
</script>

<!-- JavaScript for Edit User functionality -->
<script>
function editUser(userId, firstName, lastName, email, role, status) {
    console.log('editUser called with:', {userId, firstName, lastName, email, role, status});
    
    // Populate the edit modal with user data
    document.getElementById('edit_user_id').value = userId;
    document.getElementById('editFirstName').value = firstName || '';
    document.getElementById('editLastName').value = lastName || '';
    document.getElementById('editEmail').value = email || '';
    document.getElementById('editRole').value = role || 'student';
    document.getElementById('editIsActive').checked = status == 1;
    
    // Show the modal using our simple function
    showModal('editUserModal');
}

// Function to show Add User modal
// Function to toggle faculty field based on role selection
function toggleFacultyField() {
    const roleSelect = document.getElementById('role');
    const facultyField = document.getElementById('facultyField');
    
    if (roleSelect.value === 'dean') {
        facultyField.style.display = 'block';
        document.getElementById('faculty').required = true;
    } else {
        facultyField.style.display = 'none';
        document.getElementById('faculty').required = false;
        document.getElementById('faculty').value = '';
    }
}

// Function to toggle faculty field in edit modal
function toggleEditFacultyField() {
    const roleSelect = document.getElementById('editRole');
    const facultyField = document.getElementById('editFacultyField');
    
    if (roleSelect.value === 'dean') {
        facultyField.style.display = 'block';
        document.getElementById('editFaculty').required = true;
    } else {
        facultyField.style.display = 'none';
        document.getElementById('editFaculty').required = false;
        document.getElementById('editFaculty').value = '';
    }
}

function showAddUserModal() {
    console.log('showAddUserModal called');
    
    // Clear form fields
    document.getElementById('firstName').value = '';
    document.getElementById('lastName').value = '';
    document.getElementById('email').value = '';
    document.getElementById('password').value = '';
    document.getElementById('role').value = '';
    document.getElementById('faculty').value = '';
    document.getElementById('isActive').checked = true;
    
    // Hide faculty field initially
    document.getElementById('facultyField').style.display = 'none';
    document.getElementById('faculty').required = false;
    
    // Show the modal using our simple function
    showModal('addUserModal');
}

// Function to close modal
function closeModal(modalId) {
    hideModal(modalId);
}

// Handle form submissions
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up event listeners');
    
    // Handle profile image loading errors gracefully
    document.addEventListener('error', function(e) {
        if (e.target.tagName === 'IMG' && e.target.alt === 'Profile Image') {
            console.log('Profile image failed to load:', e.target.src);
            // The onerror attribute in the img tag will handle the fallback
        }
    }, true);
    
    // Test if modals exist
    const addModal = document.getElementById('addUserModal');
    const editModal = document.getElementById('editUserModal');
    console.log('Add modal found:', !!addModal);
    console.log('Edit modal found:', !!editModal);
    
    // Add click handlers for buttons as backup
    const addButton = document.querySelector('button[onclick="showAddUserModal()"]');
    if (addButton) {
        console.log('Add button found, adding backup click handler');
        addButton.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Add button clicked via event listener');
            showAddUserModal();
        });
    }
    
    // Test Bootstrap modal functionality
    if (typeof bootstrap !== 'undefined' && addModal) {
        console.log('Testing Bootstrap modal creation...');
        try {
            const testModal = new bootstrap.Modal(addModal);
            console.log('Bootstrap modal creation successful');
        } catch (e) {
            console.error('Bootstrap modal creation failed:', e);
        }
    }
    // Add user form
    const addUserForm = document.querySelector('#addUserModal form');
    if (addUserForm) {
        addUserForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long.');
                return false;
            }
        });
    }
    
    // Edit user form
    const editUserForm = document.getElementById('editUserForm');
    if (editUserForm) {
        editUserForm.addEventListener('submit', function(e) {
            const firstName = document.getElementById('editFirstName').value.trim();
            const lastName = document.getElementById('editLastName').value.trim();
            const email = document.getElementById('editEmail').value.trim();
            
            if (!firstName || !lastName) {
                e.preventDefault();
                alert('First name and last name are required.');
                return false;
            }
            
            if (!email || !email.includes('@') || !email.includes('.')) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Updating...';
            }
        });
    }
    
    // Delete confirmation with better UX
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-danger') && e.target.closest('form[onsubmit*="confirm"]')) {
            const form = e.target.closest('form');
            e.preventDefault();
            
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                // Show loading state
                const deleteBtn = e.target.closest('.btn-danger');
                if (deleteBtn) {
                    deleteBtn.disabled = true;
                    deleteBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
                }
                form.submit();
            }
        }
    });
});
</script>

<?php
// Include footer
include '../includes/footer.php';
?>
