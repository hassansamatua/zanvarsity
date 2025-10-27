<?php
// Set session configuration before starting
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_path', '/');

// Set session name and start session
session_name('zanvarsity_session');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug: Log session status
error_log('my-account.php - Session ID: ' . (session_id() ?: 'none'));
error_log('my-account.php - Session status: ' . session_status());
error_log('my-account.php - Session data: ' . print_r($_SESSION, true));

// Define base path
$base_path = '/c/zanvarsity/html';

// Include necessary files
require_once __DIR__ . '/../includes/auth_functions.php';
require_once __DIR__ . '/../includes/database.php';

// Add JavaScript functions at the top level to ensure they're available globally
echo '<script>
// User Management Functions - Moved to bottom of file
</script>';

// Check if user is logged in
if (!is_logged_in()) {
    // Get the current URL for redirection after login
    $current_url = $_SERVER['REQUEST_URI'];
    
    // Debug: Log the redirect attempt
    error_log('my-account.php - User not logged in, redirecting to login');
    error_log('my-account.php - Current URL: ' . $current_url);
    
    // Build login URL with redirect
    $login_url = $base_path . '/register-sign-in.php?error=login_required';
    
    // Only add redirect parameter if not already going to login page
    if (strpos($current_url, 'register-sign-in.php') === false) {
        $login_url .= '&redirect=' . urlencode($current_url);
    }
    
    // Clear any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Set cache control headers
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    
    // Perform the redirect
    header('Location: ' . $login_url, true, 302);
    exit();
}

// Get user information from session
$user_id = $_SESSION['user_id'] ?? null;
$user_email = $_SESSION['user_email'] ?? 'Guest';
$user_name = $_SESSION['first_name'] ?? 'User';
$user_role = $_SESSION['user_role'] ?? 'student';

// Debug: Log user info
error_log('my-account.php - User Info:');
error_log('- User ID: ' . $user_id);
error_log('- Email: ' . $user_email);
error_log('- Name: ' . $user_name);
error_log('- Role: ' . $user_role);
$is_admin = in_array($user_role, ['admin', 'super_admin']);
$is_dean = ($user_role === 'dean');

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile']) && $user_id) {
    // Sanitize input
    $first_name = trim(htmlspecialchars($_POST['first_name'] ?? ''));
    $last_name = trim(htmlspecialchars($_POST['last_name'] ?? ''));
    $phone = trim(htmlspecialchars($_POST['phone'] ?? ''));
    $bio = trim(htmlspecialchars($_POST['bio'] ?? ''));
    
    // Validate required fields
    if (empty($first_name) || empty($last_name)) {
        $_SESSION['error'] = "First name and last name are required.";
        header("Location: my-account.php?tab=profile");
        exit();
    }
    
    // Get current user data
    $user = [];
    $query = "SELECT profile_image FROM users WHERE id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
    }
    
    // Handle file upload
    $default_avatar = '/c/zanvarsity/html/assets/img/avatar-placeholder.png';
    $profile_image = !empty($user['profile_image']) ? $user['profile_image'] : $default_avatar;
    
    // Define upload directories
    $base_upload_dir = __DIR__ . '/uploads/profiles/';
    $web_upload_dir = '/c/zanvarsity/html/uploads/profiles/';
    
    // Ensure the uploads directory exists with proper permissions
    if (!file_exists($base_upload_dir)) {
        if (!mkdir($base_upload_dir, 0755, true)) {
            error_log("Failed to create directory: " . $base_upload_dir);
            $_SESSION['error'] = "Failed to create upload directory";
        } else {
            // Set directory permissions
            chmod($base_upload_dir, 0755);
            // Create an index.html file to prevent directory listing
            file_put_contents($base_upload_dir . 'index.html', '<!-- Directory access forbidden -->');
        }
    }
    
    // Check if remove profile image is checked
    if (isset($_POST['remove_profile_image']) && $_POST['remove_profile_image'] == '1') {
        // Remove the current profile image
        if (!empty($user['profile_image']) && $user['profile_image'] !== $default_avatar) {
            $old_image_path = str_replace('/c/zanvarsity/html', $_SERVER['DOCUMENT_ROOT'], $user['profile_image']);
            if (file_exists($old_image_path)) {
                @unlink($old_image_path);
            }
            $profile_image = $default_avatar;
            $update_image = true;
        }
    } 
    // Handle file upload if a new file was provided
    elseif (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        // Debug log
        error_log("Starting file upload process");
        error_log("Uploaded file info: " . print_r($_FILES['profile_image'], true));
        
        // Ensure directories exist
        if (!file_exists($base_upload_dir)) {
            mkdir($base_upload_dir, 0755, true);
        }
        
        error_log("Base upload dir: $base_upload_dir");
        error_log("Web upload dir: $web_upload_dir");
        
        // Create upload directory if it doesn't exist
        if (!file_exists($base_upload_dir)) {
            if (!mkdir($base_upload_dir, 0755, true)) {
                error_log("Failed to create directory: " . $base_upload_dir);
                $_SESSION['error'] = "Failed to create upload directory";
                header("Location: my-account.php?tab=profile");
                exit();
            }
            // Set directory permissions
            chmod($base_upload_dir, 0755);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            // Delete old profile image if it exists and is not the default avatar
            if (!empty($user['profile_image']) && $user['profile_image'] !== $default_avatar) {
                $old_image_path = strpos($user['profile_image'], $web_upload_dir) === 0 ? 
                    str_replace($web_upload_dir, $base_upload_dir, $user['profile_image']) : 
                    $base_upload_dir . basename($user['profile_image']);
                
                if (file_exists($old_image_path) && is_writable($old_image_path)) {
                    @unlink($old_image_path);
                }
            }
            
            // Generate a unique filename
            $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $file_extension;
            $target_path = $base_upload_dir . $new_filename;
            $web_path = $web_upload_dir . $new_filename;
            
            // Debug information
            error_log("Attempting to move uploaded file to: " . $target_path);
            error_log("Temporary file: " . $_FILES['profile_image']['tmp_name']);
            error_log("File size: " . $_FILES['profile_image']['size']);
            
            error_log("Attempting to move uploaded file to: $target_path");
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_path)) {
                error_log("File moved successfully to: $target_path");
                
                // Verify the file was actually moved
                if (file_exists($target_path)) {
                    error_log("Target file exists, setting permissions");
                    // Set proper permissions
                    if (chmod($target_path, 0644)) {
                        error_log("File permissions set successfully");
                    } else {
                        error_log("Failed to set file permissions");
                    }
                    
                    // Store the full web path in the database
                    $profile_image = $web_path;
                    
                    // Log the path for debugging
                    error_log("Profile image path to save in DB: $profile_image");
                    error_log("Target file exists: " . (file_exists($target_path) ? 'Yes' : 'No'));
                    error_log("File size: " . filesize($target_path) . " bytes");
                    
                    // Update the user's profile image in the database
                    $update_sql = "UPDATE users SET profile_image = ? WHERE id = ?";
                    if ($update_stmt = $conn->prepare($update_sql)) {
                        $update_stmt->bind_param("si", $profile_image, $user_id);
                        if ($update_stmt->execute()) {
                            error_log("Successfully updated profile image in database");
                            
                            // Update the current user's profile image in session
                            if (isset($_SESSION['user'])) {
                                $_SESSION['user']['profile_image'] = $profile_image;
                            }
                            
                            // Also update the $user array for immediate display
                            $user['profile_image'] = $profile_image;
                            
                            // Set success message
                            $_SESSION['success'] = "Profile picture updated successfully!";
                        } else {
                            error_log("Failed to update profile image in database: " . $update_stmt->error);
                            $_SESSION['error'] = "Failed to update profile image in database: " . $update_stmt->error;
                            header("Location: my-account.php?tab=profile");
                            exit();
                        }
                        $update_stmt->close();
                    } else {
                        $error = $conn->error;
                        error_log("Failed to prepare update statement: $error");
                        $_SESSION['error'] = "Database error: $error";
                        header("Location: my-account.php?tab=profile");
                        exit();
                    }
                } else {
                    error_log("File move succeeded but target file doesn't exist: " . $target_path);
                    $_SESSION['error'] = "Failed to save uploaded file";
                    header("Location: my-account.php?tab=profile");
                    exit();
                }
            }
        } else {
            $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.";
            header("Location: my-account.php?tab=profile");
            exit();
        }
    }
    
    // Update user in database
    $sql = "UPDATE users SET 
            first_name = ?,
            last_name = ?,
            phone = ?,
            bio = ?,
            profile_image = ?
            WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssssi", $first_name, $last_name, $phone, $bio, $profile_image, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Profile updated successfully!";
            // Update session data
            $_SESSION['first_name'] = $first_name;
            $_SESSION['profile_image'] = $profile_image;
            // Refresh the page
            header("Location: my-account.php?tab=profile");
            exit();
        } else {
            $_SESSION['error'] = "Error updating profile: " . $conn->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Database error: " . $conn->error;
    }
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
            $role = $_POST['role'] ?? 'student';
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            // Basic validation
            if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
                $_SESSION['error'] = "All fields are required.";
                header("Location: my-account.php?tab=manage-users");
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
                    header("Location: my-account.php?tab=manage-users");
                    exit();
                }
                $stmt->close();
            }
            
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $sql = "INSERT INTO users (first_name, last_name, email, password, role, is_active, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("sssssi", $first_name, $last_name, $email, $hashed_password, $role, $is_active);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = "User added successfully!";
                } else {
                    $_SESSION['error'] = "Error adding user: " . $conn->error;
                }
                $stmt->close();
            } else {
                $_SESSION['error'] = "Database error: " . $conn->error;
            }
            
            header("Location: my-account.php?tab=manage-users");
            exit();
            
        case 'update_role':
            $user_id_to_update = intval($_POST['user_id'] ?? 0);
            $new_role = $_POST['new_role'] ?? 'student';
            
            // Prevent changing your own role
            if ($user_id_to_update === $_SESSION['user_id']) {
                $_SESSION['error'] = "You cannot change your own role.";
                header("Location: my-account.php?tab=manage-users");
                exit();
            }
            
            $valid_roles = ['student', 'lecturer', 'admin'];
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
            
            header("Location: my-account.php?tab=manage-users");
            exit();
            
        case 'delete_user':
            $user_id_to_delete = intval($_POST['user_id'] ?? 0);
            
            // Prevent deleting yourself
            if ($user_id_to_delete === $_SESSION['user_id']) {
                $_SESSION['error'] = "You cannot delete your own account.";
                header("Location: my-account.php?tab=manage-users");
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
            
            header("Location: my-account.php?tab=manage-users");
            exit();
    }
}

// Set page title and heading
$page_title = 'My Account | Zanvarsity';
$page_heading = 'My Dashboard';

// Include header
include 'includes/about_header.php';

// Get user information from session
$user_id = $_SESSION['user_id'] ?? null;
$user_email = $_SESSION['email'] ?? 'Guest';
$user_name = $_SESSION['first_name'] ?? 'User';
$user_role = $_SESSION['role'] ?? 'student';
$is_admin = in_array($user_role, ['admin', 'super_admin']);

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

// Set active tab
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';

// Get user stats from database
$stats = [];
if (isset($conn)) {
    // Add your stats queries here
    // Example: $stats['total_courses'] = get_course_count($conn, $user_id);
}

// Add custom styles for the dashboard
?>
<style>
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

  .stat-card {
    text-align: center;
    padding: 20px;
    border-radius: 8px;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
  }

  .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  }

  .stat-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
    color: #014421;
  }

  .stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
    margin: 10px 0;
  }

  .stat-label {
    color: #666;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
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
                // Define default avatar as data URI
                $default_avatar = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMTAwIDEwMCI+PHJlY3Qgd2lkdGg9IjEwMCIgaGVpZ2h0PSIxMDAiIGZpbGw9IiNkZGQiLz48dGV4dCB4PSI1MCIgeT0iNTAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgYWxpZ25tZW50LWJhc2VsaW5lPSJtaWRkbGUiIGZpbGw9IiYjNjY2OyI+QXZhdGFyPC90ZXh0Pjwvc3ZnPg';
                
                // Initialize profile image with default
                $profile_img = $default_avatar;
                
                // If user has a profile image set
                if (!empty($user['profile_image'])) {
                    $user_image = $user['profile_image'];
                    
                    // Check if the image exists at the given path
                    $server_path = str_replace('/c/zanvarsity/html', $_SERVER['DOCUMENT_ROOT'], $user_image);
                    
                    if (file_exists($server_path)) {
                        $profile_img = $user_image . '?v=' . filemtime($server_path);
                    } else {
                        // Try alternative path resolution
                        $alt_path = __DIR__ . str_replace('/c/zanvarsity/html', '', $user_image);
                        if (file_exists($alt_path)) {
                            $profile_img = $user_image . '?v=' . filemtime($alt_path);
                        } else {
                            // Fall back to default avatar if image not found
                            error_log("Profile image not found: " . $server_path);
                            $profile_img = $default_avatar;
                        }
                    }
                        
                    // Ensure the path is accessible from the web root
                    if (strpos($profile_img, 'http') !== 0 && strpos($profile_img, 'data:image/') !== 0) {
                        // If the path doesn't start with /c/zanvarsity/html, add it
                        if (strpos($profile_img, '/c/zanvarsity/html/') !== 0) {
                            $profile_img = '/c/zanvarsity/html' . $profile_img;
                        }
                        
                        // Add cache buster
                        $profile_img .= (strpos($profile_img, '?') === false ? '?' : '&') . 'v=' . time();
                    }
                }
                ?>
                <img src="<?php echo htmlspecialchars($profile_img); ?>" 
                     alt="Profile Image" 
                     id="sidebar-profile-img" 
                     style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;" 
                     onerror="this.src='<?php echo htmlspecialchars($default_avatar); ?>';">
            </div>
            <h3 class="profile-name"><?php echo htmlspecialchars($user_name); ?></h3>
            <div class="profile-role"><?php echo ucfirst(htmlspecialchars($user_role)); ?></div>
          </div>
          <ul class="sidebar-menu">
            <li><a href="?tab=dashboard" class="<?php echo (!isset($_GET['tab']) || $_GET['tab'] === 'dashboard') ? 'active' : ''; ?>"><i class="fa fa-tachometer"></i> Dashboard</a></li>
            <li><a href="?tab=profile" class="<?php echo (isset($_GET['tab']) && $_GET['tab'] === 'profile') ? 'active' : ''; ?>"><i class="fa fa-user"></i> My Profile</a></li>
            <?php if ($is_admin): ?>
            <li><a href="?tab=manage-users" class="<?php echo (isset($_GET['tab']) && $_GET['tab'] === 'manage-users') ? 'active' : ''; ?>"><i class="fa fa-users"></i> Manage Users</a></li>
            <li><a href="admin/users.php" class="<?php echo (basename(dirname($_SERVER['PHP_SELF'])) === 'admin' && basename($_SERVER['PHP_SELF']) === 'users.php') ? 'active' : ''; ?>"><i class="fa fa-users"></i> Manage Users</a></li>
            <?php endif; ?>
            <?php if ($is_dean): ?>
            <li><a href="?tab=faculty-content" class="<?php echo (isset($_GET['tab']) && $_GET['tab'] === 'faculty-content') ? 'active' : ''; ?>"><i class="fa fa-graduation-cap"></i> Faculty Content</a></li>
            <?php endif; ?>
            <li><a href="?tab=messages" class="<?php echo (isset($_GET['tab']) && $_GET['tab'] === 'messages') ? 'active' : ''; ?>"><i class="fa fa-envelope"></i> Messages</a></li>
            <li><a href="?tab=settings" class="<?php echo (isset($_GET['tab']) && $_GET['tab'] === 'settings') ? 'active' : ''; ?>"><i class="fa fa-cog"></i> Settings</a></li>
            <?php if ($is_admin): ?>
            <li><a href="manage_content.php"><i class="fa fa-edit"></i> Manage Content</a></li>
            <?php endif; ?>
            <li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
          </ul>
        </div>
      </div>

      <!-- Main Content -->
      <div class="col-lg-9">
        <?php if (isset($_GET['tab']) && $_GET['tab'] === 'manage-content' && ($is_admin || $is_dean)): ?>
          <script>
            // Direct redirect to manage_content.php
            window.location.href = 'manage_content.php';
          </script>

        <?php elseif (isset($_GET['tab']) && $_GET['tab'] === 'profile'): ?>
          <!-- Profile Section -->
          <div class="profile-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h2><i class="fa fa-user"></i> My Profile</h2>
            </div>
            
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-4 text-center">
                    <form method="POST" action="" enctype="multipart/form-data" id="profileForm" onsubmit="return validateForm()">
                      <input type="hidden" name="update_profile" value="1">
                      <?php echo csrf_token_field(); ?>
                      <div class="profile-image-large mb-3">
                        <?php 
                        // Define default avatar as data URI
                        $default_avatar = '/c/zanvarsity/html/assets/img/avatar-placeholder.png';
                        
                        // Initialize profile image with default
                        $profile_img = $default_avatar;
                        
                        // If user has a profile image set
                        if (!empty($user['profile_image'])) {
                            $user_image = $user['profile_image'];
                            
                            // Check if the image exists at the given path
                            $server_path = str_replace('/c/zanvarsity/html', $_SERVER['DOCUMENT_ROOT'], $user_image);
                            
                            if (file_exists($server_path)) {
                                $profile_img = $user_image . '?v=' . filemtime($server_path);
                            } else {
                                // Try alternative path resolution
                                $alt_path = __DIR__ . str_replace('/c/zanvarsity/html', '', $user_image);
                                if (file_exists($alt_path)) {
                                    $profile_img = $user_image . '?v=' . filemtime($alt_path);
                                } else {
                                    // Fall back to default avatar if image not found
                                    error_log("Profile image not found: " . $server_path);
                                    $profile_img = $default_avatar;
                                }
                            }
                            
                            // Ensure the path is accessible from the web root
                            if (strpos($profile_img, 'http') !== 0 && strpos($profile_img, 'data:image/') !== 0) {
                                // If the path doesn't start with /c/zanvarsity/html, add it
                                if (strpos($profile_img, '/c/zanvarsity/html/') !== 0) {
                                    $profile_img = '/c/zanvarsity/html' . ltrim($profile_img, '/');
                                }
                                
                                // Add cache buster if not already present
                                if (strpos($profile_img, '?') === false) {
                                    $profile_img .= '?v=' . time();
                                }
                            }
                        }
                        ?>
                        <img id="profile-preview" 
                             src="<?php echo htmlspecialchars($profile_img); ?>" 
                             alt="Profile Image" 
                             style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;" 
                             onerror="this.src='<?php echo htmlspecialchars($default_avatar); ?>';">
                      </div>
                      <div class="mb-3">
                        <label for="profile_image" class="form-label">Change Profile Picture</label>
                        <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/jpeg, image/png, image/gif" onchange="previewImage(this)">
                        <div class="form-text">Max file size: 2MB. Allowed formats: JPG, PNG, GIF</div>
                        <?php if (!empty($user['profile_image']) && $user['profile_image'] !== $default_avatar): ?>
                        <div class="form-check mt-2">
                          <input class="form-check-input" type="checkbox" name="remove_profile_image" id="remove_profile_image" value="1">
                          <label class="form-check-label" for="remove_profile_image">
                            Remove profile picture
                          </label>
                        </div>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="col-md-8">
                      <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                      <?php endif; ?>
                      <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                      <?php endif; ?>
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <label class="form-label">First Name</label>
                          <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                          <label class="form-label">Last Name</label>
                          <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-12 mb-3">
                          <label class="form-label">Email</label>
                          <input type="email" class="form-control" value="<?php echo htmlspecialchars($user_email); ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                          <label class="form-label">Role</label>
                          <input type="text" class="form-control" value="<?php echo ucfirst(htmlspecialchars($user_role)); ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                          <label class="form-label">Phone</label>
                          <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                      </div>
                      <div class="mb-3">
                        <label for="bio" class="form-label">Bio</label>
                        <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                      </div>
                      
                      <button type="submit" class="btn btn-primary">Save Changes</button>
                      
                      <script>
                      // Profile picture preview
                      document.getElementById('profile_image')?.addEventListener('change', function(e) {
                          const file = e.target.files[0];
                          if (file) {
                              // Check file size (max 2MB)
                              if (file.size > 2 * 1024 * 1024) {
                                  alert('File size must be less than 2MB');
                                  this.value = '';
                                  return;
                              }
                              
                              // Check file type
                              const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                              if (!validTypes.includes(file.type)) {
                                  alert('Only JPG, PNG, and GIF files are allowed');
                                  this.value = '';
                                  return;
                              }
                              
                              // Create preview
                              const reader = new FileReader();
                              reader.onload = function(e) {
                                  // Update main profile preview
                                  const preview = document.getElementById('profile-preview');
                                  if (preview) {
                                      preview.src = e.target.result;
                                  }
                                  
                                  // Update sidebar image
                                  const sidebarImg = document.querySelector('.profile-image img');
                                  if (sidebarImg) {
                                      sidebarImg.src = e.target.result;
                                  }
                              };
                              reader.readAsDataURL(file);
                          }
                      });
                      
                      // Form validation
                      function validateForm() {
                          const firstName = document.getElementById('first_name')?.value.trim();
                          const lastName = document.getElementById('last_name')?.value.trim();
                          const fileInput = document.getElementById('profile_image');
                          const removeCheckbox = document.getElementById('remove_profile_image');
                          
                          // Basic validation
                          if (!firstName || !lastName) {
                              alert('First name and last name are required');
                              return false;
                          }
                          
                          // Show loading state
                          const submitBtn = document.querySelector('button[type="submit"]');
                          if (submitBtn) {
                              submitBtn.disabled = true;
                              submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';
                          }
                          
                          // File validation if a file is selected
                          if (fileInput.files.length > 0 && !removeCheckbox?.checked) {
                              const file = fileInput.files[0];
                              const fileSize = file.size / 1024 / 1024; // in MB
                              const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                              
                              if (fileSize > 2) {
                                  alert('File size must be less than 2MB');
                                  if (submitBtn) submitBtn.disabled = false;
                                  return false;
                              }
                              
                              if (!validTypes.includes(file.type)) {
                                  alert('Only JPG, PNG, and GIF files are allowed');
                                  if (submitBtn) submitBtn.disabled = false;
                                  return false;
                              }
                          }
                          
                          return true;
                      }
                      
                      function previewImage(input) {
                          const preview = document.getElementById('profile-preview');
                          const file = input.files[0];
                          
                          if (file) {
                              const reader = new FileReader();
                              
                              reader.onload = function(e) {
                                  preview.src = e.target.result;
                              }
                              
                              reader.readAsDataURL(file);
                          }
                      }
                      </script>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
                  </div>
                </div>
              </div>
            </div>
          </div>

        <?php elseif (isset($_GET['tab']) && $_GET['tab'] === 'messages'): ?>
          <!-- Messages Section -->
          <div class="messages-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h2><i class="fa fa-envelope"></i> Messages</h2>
              <button class="btn btn-primary"><i class="fa fa-plus"></i> New Message</button>
            </div>
            
            <div class="card">
              <div class="card-body">
                <div class="text-center py-5">
                  <i class="fa fa-envelope-o fa-3x text-muted mb-3"></i>
                  <h4>No Messages</h4>
                  <p class="text-muted">You don't have any messages yet.</p>
                </div>
              </div>
            </div>
          </div>

        <?php elseif (isset($_GET['tab']) && $_GET['tab'] === 'settings'): ?>
          <!-- Settings Section -->
          <div class="settings-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h2><i class="fa fa-cog"></i> Settings</h2>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="card">
                  <div class="card-header">
                    <h5>Account Settings</h5>
                  </div>
                  <div class="card-body">
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                      <label class="form-check-label" for="emailNotifications">
                        Email Notifications
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="checkbox" id="smsNotifications">
                      <label class="form-check-label" for="smsNotifications">
                        SMS Notifications
                      </label>
                    </div>
                    <button class="btn btn-success">Save Settings</button>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card">
                  <div class="card-header">
                    <h5>Security</h5>
                  </div>
                  <div class="card-body">
                    <button class="btn btn-warning mb-2 d-block">Change Password</button>
                    <button class="btn btn-info mb-2 d-block">Two-Factor Authentication</button>
                    <button class="btn btn-secondary d-block">Download Data</button>
                  </div>
                </div>
              </div>
            </div>
          </div>

        <?php elseif (isset($_GET['tab']) && $_GET['tab'] === 'manage-users' && $is_admin): ?>
          <!-- Manage Users Section -->
          <div class="manage-users-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h2><i class="fa fa-users"></i> Manage Users</h2>
              <button type="button" class="btn btn-primary" id="addNewUserBtn">
                <i class="fa fa-plus"></i> Add New User
              </button>
            </div>
            
            <?php if (isset($_SESSION['error'])): ?>
              <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
              <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            
            <div class="card">
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      // Get all users except the current admin
                      $users = [];
                      $query = "SELECT id, first_name, last_name, email, role, status FROM users WHERE id != ? ORDER BY role, first_name";
                      if ($stmt = $conn->prepare($query)) {
                          $stmt->bind_param("i", $user_id);
                          if ($stmt->execute()) {
                              $result = $stmt->get_result();
                              while ($row = $result->fetch_assoc()) {
                                  $users[] = $row;
                              }
                          }
                          $stmt->close();
                      }
                      
                      foreach ($users as $user): 
                          $full_name = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
                          $email = htmlspecialchars($user['email']);
                          $role = ucfirst($user['role']);
                          $status = $user['status'] ? 'Active' : 'Inactive';
                          $status_class = $user['status'] ? 'success' : 'secondary';
                      ?>
                      <tr>
                        <td><?php echo $full_name; ?></td>
                        <td><?php echo $email; ?></td>
                        <td><?php echo $role; ?></td>
                        <td><span class="badge bg-<?php echo $status_class; ?>"><?php echo $status; ?></span></td>
                        <td class="text-nowrap">
                          <button type="button" class="btn btn-sm btn-outline-primary me-2 edit-user-btn" 
                            data-id="<?php echo $user['id']; ?>"
                            data-firstname="<?php echo htmlspecialchars($user['first_name'], ENT_QUOTES); ?>"
                            data-lastname="<?php echo htmlspecialchars($user['last_name'], ENT_QUOTES); ?>"
                            data-email="<?php echo htmlspecialchars($user['email'], ENT_QUOTES); ?>"
                            data-role="<?php echo htmlspecialchars($user['role'], ENT_QUOTES); ?>"
                            data-status="<?php echo $user['status'] ? '1' : '0'; ?>"
                            title="Edit User">
                            <i class="fa fa-edit"></i> Edit
                          </button>
                          <button class="btn btn-sm btn-outline-danger delete-user-btn" 
                            data-id="<?php echo $user['id']; ?>"
                            data-name="<?php echo htmlspecialchars($full_name, ENT_QUOTES); ?>"
                            title="Delete User">
                            <i class="fa fa-trash"></i> Delete
                          </button>
                        </td>
                      </tr>
                      <?php endforeach; ?>
                      <?php if (empty($users)): ?>
                      <tr>
                        <td colspan="5" class="text-center">No users found.</td>
                      </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            
            <!-- Add/Edit User Modal -->
            <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form id="userForm" method="POST" action="">
                    <input type="hidden" name="action" id="userAction" value="">
                    <input type="hidden" name="user_id" id="userId" value="">
                    
                    <div class="modal-header">
                      <h5 class="modal-title" id="userModalTitle">Add New User</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstName" name="first_name" required>
                      </div>
                      <div class="mb-3">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="last_name" required>
                      </div>
                      <div class="mb-3">
                        <label for="userEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="userEmail" name="email" required>
                      </div>
                      <div class="mb-3">
                        <label for="userRole" class="form-label">Role</label>
                        <select class="form-select" id="userRole" name="role" required>
                          <option value="student">Student</option>
                          <option value="lecturer">Lecturer</option>
                          <option value="dean">Dean</option>
                          <option value="admin">Administrator</option>
                        </select>
                      </div>
                      <div class="mb-3">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="userStatus" name="is_active" value="1" checked>
                          <label class="form-check-label" for="userStatus">Active</label>
                        </div>
                      </div>
                      <div id="passwordFields">
                        <div class="mb-3">
                          <label for="userPassword" class="form-label">Password</label>
                          <input type="password" class="form-control" id="userPassword" name="password" autocomplete="new-password">
                          <div class="form-text">Leave blank to keep current password (when editing)</div>
                        </div>
                        <div class="mb-3">
                          <label for="confirmPassword" class="form-label">Confirm Password</label>
                          <input type="password" class="form-control" id="confirmPassword" name="confirm_password" autocomplete="new-password">
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-primary">Save Changes</button>
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
                    <p>Are you sure you want to delete <strong id="deleteUserName"></strong>?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                  </div>
                  <div class="modal-footer">
                    <form id="deleteForm" method="POST" action="">
                      <?php echo csrf_token_field(); ?>
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="user_id" id="deleteUserId" value="">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-danger">Delete User</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>

        <?php elseif (isset($_GET['tab']) && $_GET['tab'] === 'faculty-content' && $is_dean): ?>
          <!-- Faculty Content Management Section -->
          <div class="faculty-content-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h2><i class="fa fa-graduation-cap"></i> Faculty Content Management</h2>
              <button class="btn btn-primary" onclick="showAddContentModal()"><i class="fa fa-plus"></i> Add Content</button>
            </div>
            
            <div class="row">
              <!-- Welcome Message Card -->
              <div class="col-md-6 mb-4">
                <div class="card">
                  <div class="card-header">
                    <h5><i class="fa fa-home"></i> Welcome Message</h5>
                  </div>
                  <div class="card-body">
                    <p class="text-muted">Manage the welcome message for your faculty page.</p>
                    <button class="btn btn-primary btn-sm" onclick="editContent('welcome')">
                      <i class="fa fa-edit"></i> Edit Welcome Message
                    </button>
                  </div>
                </div>
              </div>
              
              <!-- Vision Card -->
              <div class="col-md-6 mb-4">
                <div class="card">
                  <div class="card-header">
                    <h5><i class="fa fa-eye"></i> Vision Statement</h5>
                  </div>
                  <div class="card-body">
                    <p class="text-muted">Manage your faculty's vision statement.</p>
                    <button class="btn btn-primary btn-sm" onclick="editContent('vision')">
                      <i class="fa fa-edit"></i> Edit Vision
                    </button>
                  </div>
                </div>
              </div>
              
              <!-- Mission Card -->
              <div class="col-md-6 mb-4">
                <div class="card">
                  <div class="card-header">
                    <h5><i class="fa fa-bullseye"></i> Mission Statement</h5>
                  </div>
                  <div class="card-body">
                    <p class="text-muted">Manage your faculty's mission statement.</p>
                    <button class="btn btn-primary btn-sm" onclick="editContent('mission')">
                      <i class="fa fa-edit"></i> Edit Mission
                    </button>
                  </div>
                </div>
              </div>
              
              <!-- About Card -->
              <div class="col-md-6 mb-4">
                <div class="card">
                  <div class="card-header">
                    <h5><i class="fa fa-info-circle"></i> About Faculty</h5>
                  </div>
                  <div class="card-body">
                    <p class="text-muted">Manage general information about your faculty.</p>
                    <button class="btn btn-primary btn-sm" onclick="editContent('about')">
                      <i class="fa fa-edit"></i> Edit About
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Recent Content -->
            <div class="card">
              <div class="card-header">
                <h5>Recent Content Updates</h5>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Content Type</th>
                        <th>Last Updated</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td><i class="fa fa-home"></i> Welcome Message</td>
                        <td>2 days ago</td>
                        <td><span class="badge bg-success">Published</span></td>
                        <td>
                          <button class="btn btn-sm btn-info" onclick="editContent('welcome')">
                            <i class="fa fa-edit"></i>
                          </button>
                        </td>
                      </tr>
                      <tr>
                        <td><i class="fa fa-eye"></i> Vision Statement</td>
                        <td>1 week ago</td>
                        <td><span class="badge bg-success">Published</span></td>
                        <td>
                          <button class="btn btn-sm btn-info" onclick="editContent('vision')">
                            <i class="fa fa-edit"></i>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

        <?php elseif (isset($_GET['tab']) && $_GET['tab'] === 'dashboard'): ?>
          <!-- Dashboard Section -->
          <div class="dashboard-header">
            <h1>Welcome back, <?php echo htmlspecialchars($user_name); ?>!</h1>
            <p>Here's what's happening with your account today.</p>
          </div>

          <!-- Statistics Cards -->
          <div class="row">
            <!-- Announcements Card -->
            <div class="col-md-3 mb-4">
              <div class="stat-card">
                <div class="stat-icon">
                  <i class="fa fa-bullhorn"></i>
                </div>
                <div class="stat-info">
                  <h3>
                  <?php 
                    $sql = "SELECT COUNT(*) as count FROM publications";
                    $result = $conn->query($sql);
                    echo $result ? $result->fetch_assoc()['count'] : '0';
                    ?>
                </h3>
                <p>Publications</p>
              </div>
            </div>
          </div>

          <!-- Events Card -->
          <div class="col-md-3 mb-4">
            <div class="stat-card">
              <div class="stat-icon">
                <i class="fa fa-calendar"></i>
              </div>
              <div class="stat-info">
                <h3>
                  <?php 
                    $sql = "SELECT COUNT(*) as count FROM events 
                            WHERE _date >= CURDATE()";
                    $result = $conn->query($sql);
                    echo $result ? $result->fetch_assoc()['count'] : '0';
                    ?>
                </h3>
                <p>Upcoming Events</p>
              </div>
            </div>
          </div>

          <!-- News Card -->
          <div class="col-md-3 mb-4">
            <div class="stat-card">
              <div class="stat-icon">
                <i class="fa fa-newspaper-o"></i>
              </div>
              <div class="stat-info">
                <h3>
                  <?php 
                    $sql = "SELECT COUNT(*) as count FROM news 
                            WHERE status = 'published'";
                    $result = $conn->query($sql);
                    echo $result ? $result->fetch_assoc()['count'] : '0';
                    ?>
                </h3>
                <p>News Articles</p>
              </div>
            </div>
          </div>

          <!-- Users Card (Admin only) -->
          <?php if ($is_admin): ?>
          <div class="col-md-3 mb-4">
            <div class="stat-card">
              <div class="stat-icon">
                <i class="fa fa-users"></i>
              </div>
              <div class="stat-info">
                <h3>
                  <?php 
                    $sql = "SELECT COUNT(*) as count FROM users";
                    $result = $conn->query($sql);
                    echo $result ? $result->fetch_assoc()['count'] : '0';
                    ?>
                </h3>
                <p>Total Users</p>
              </div>
            </div>
          </div>
          <?php endif; ?>
        </div>

        <!-- Recent Activity -->
        <div class="row mt-4">
          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <h5>Recent Activity</h5>
              </div>
              <div class="card-body">
                <div class="text-center py-4">
                  <i class="fa fa-clock-o fa-2x text-muted mb-3"></i>
                  <p class="text-muted">No recent activity to display.</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                <h5>Quick Actions</h5>
              </div>
              <div class="card-body">
                <a href="?tab=profile" class="btn btn-primary btn-block mb-2">
                  <i class="fa fa-user"></i> Edit Profile
                </a>
                <?php if ($is_admin): ?>
                <a href="admin/users.php" class="btn btn-success btn-block mb-2">
                  <i class="fa fa-users"></i> Manage Users
                </a>
                <?php endif; ?>
                <a href="?tab=settings" class="btn btn-info btn-block">
                  <i class="fa fa-cog"></i> Settings
                </a>
              </div>
            </div>
          </div>
        </div>

        <?php else: // Default fallback ?>
          <div class="dashboard-header">
            <h1>Welcome back, <?php echo htmlspecialchars($user_name); ?>!</h1>
            <p>Here's what's happening with your account today.</p>
          </div>

          <!-- Statistics Cards -->
          <div class="row">
            <!-- Announcements Card -->
            <div class="col-md-3 mb-4">
              <div class="stat-card">
                <div class="stat-icon">
                  <i class="fa fa-bullhorn"></i>
                </div>
                <div class="stat-info">
                  <h3>
                  <?php 
                    $sql = "SELECT COUNT(*) as count FROM publications";
                    $result = $conn->query($sql);
                    echo $result ? $result->fetch_assoc()['count'] : '0';
                    ?>
                </h3>
                <p>Publications</p>
              </div>
            </div>
          </div>

          <!-- Events Card -->
          <div class="col-md-3 mb-4">
            <div class="stat-card">
              <div class="stat-icon">
                <i class="fa fa-calendar"></i>
              </div>
              <div class="stat-info">
                <h3>
                  <?php 
                    $sql = "SELECT COUNT(*) as count FROM events 
                            WHERE _date >= CURDATE()";
                    $result = $conn->query($sql);
                    echo $result ? $result->fetch_assoc()['count'] : '0';
                    ?>
                </h3>
                <p>Upcoming Events</p>
              </div>
            </div>
          </div>
        </div>
        <!-- Recent Activity -->
        <div class="dashboard-card">
          <h3>Recent Activity</h3>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Activity</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Today</td>
                  <td>Completed "Introduction to Web Development"</td>
                  <td><span class="badge bg-success">Completed</span></td>
                </tr>
                <tr>
                  <td>Yesterday</td>
                  <td>Started "Advanced JavaScript" course</td>
                  <td><span class="badge bg-primary">In Progress</span></td>
                </tr>
                <tr>
                  <td>2 days ago</td>
                  <td>Submitted assignment for "Database Design"</td>
                  <td><span class="badge bg-warning">Pending</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Profile Section -->
        <div class="dashboard-card" id="profile-section" style="display: <?php echo $active_tab === 'profile' ? 'block' : 'none'; ?>">
          <h2 class="mb-4">My Profile</h2>
          
          <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
          <?php endif; ?>
          
          <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
          <?php endif; ?>

          <form action="my-account.php?tab=profile" method="POST" enctype="multipart/form-data">
            <div class="row">
              <div class="col-md-4 text-center mb-4">
                <div class="profile-image-container mb-3" style="width: 200px; height: 200px; margin: 0 auto; border-radius: 50%; overflow: hidden; border: 5px solid #f0f0f0;">
                  <img id="profile-preview" src="<?php echo !empty($user['profile_image']) ? $user['profile_image'] : 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMTAwIDEwMCI+PHJlY3Qgd2lkdGg9IjEwMCIgaGVpZ2h0PSIxMDAiIGZpbGw9IiNkZGQiLz48dGV4dCB4PSI1MCIgeT0iNTAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgYWxpZ25tZW50LWJhc2VsaW5lPSJtaWRkbGUiIGZpbGw9IiYjNjY2OyI+QXZhdGFyPC90ZXh0Pjwvc3ZnPg'; ?>" alt="Profile Image" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="form-group">
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" id="profile_image" name="profile_image" accept="image/*" onchange="previewImage(this)">
                    <label class="custom-file-label" for="profile_image">Choose file</label>
                  </div>
                  <small class="form-text text-muted">Max file size: 2MB. Allowed formats: JPG, PNG, GIF</small>
                </div>
              </div>
              
              <div class="col-md-8">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="first_name">First Name</label>
                      <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="last_name">Last Name</label>
                      <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>">
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="email">Email Address</label>
                  <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" disabled>
                  <small class="form-text text-muted">Contact support to change your email address</small>
                </div>
                
                <div class="form-group">
                  <label for="phone">Phone Number</label>
                  <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                  <label for="bio">Bio</label>
                  <textarea class="form-control" id="bio" name="bio" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>
                
                <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
              </div>
            </div>
          </form>
        </div>

        <script>
        function previewImage(input) {
          const preview = document.getElementById('profile-preview');
          const sidebarImg = document.getElementById('sidebar-profile-img');
          const file = input.files[0];
          const reader = new FileReader();
          
          reader.onloadend = function() {
            preview.src = reader.result;
            if (sidebarImg) {
              sidebarImg.src = reader.result;
            }
          }
          
          if (file) {
            reader.readAsDataURL(file);
          } else {
const defaultImg = '<?php echo !empty($user['profile_image']) ? $user['profile_image'] : 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMTAwIDEwMCI+PHJlY3Qgd2lkdGg9IjEwMCIgaGVpZ2h0PSIxMDAiIGZpbGw9IiNkZGQiLz48dGV4dCB4PSI1MCIgeT0iNTAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgYWxpZ25tZW50LWJhc2VsaW5lPSJtaWRkbGUiIGZpbGw9IiYjNjY2OyI+QXZhdGFyPC90ZXh0Pjwvc3ZnPg'; ?>';
            preview.src = defaultImg;
            if (sidebarImg) {
              sidebarImg.src = defaultImg;
            }
          }
        }
        
        // Tab navigation is now handled by PHP URL parameters, not JavaScript hash navigation
        </script>
      </div>
    </div>
  </div>
</div>
<div class="container-fluid p-0">
  <?php include 'includes/about_footer.php'; ?>
</div>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- User Management JavaScript -->
<script>
// Make sure these functions are available globally
window.editUser = function(id, firstName, lastName, email, role, status) {
    try {
        console.log('editUser called with:', {id, firstName, lastName, email, role, status});
        const modalElement = document.getElementById('userModal');
        if (!modalElement) {
            console.error('User modal element not found');
            return false;
        }
        
        const modal = new bootstrap.Modal(modalElement);
        
        // Set form action and title
        const title = id ? 'Edit User' : 'Add New User';
        document.getElementById('userModalTitle').textContent = title;
        document.getElementById('userAction').value = id ? 'update' : 'add';
        document.getElementById('userId').value = id || '';
        
        // Fill the form
        document.getElementById('firstName').value = firstName || '';
        document.getElementById('lastName').value = lastName || '';
        document.getElementById('userEmail').value = email || '';
        document.getElementById('userRole').value = role || 'student';
        document.getElementById('userStatus').checked = status == 1;
        
        // Show/hide password fields
        const passwordFields = document.getElementById('passwordFields');
        if (passwordFields) {
            passwordFields.style.display = id ? 'none' : 'block';
        }
        
        // Show the modal
        console.log('Showing user modal');
        modal.show();
    } catch (error) {
        console.error('Error in editUser:', error);
        alert('An error occurred while loading the user form. Please check the console for details.');
    }
    return false;
};

window.confirmDelete = function(userId, userName) {
    console.log('confirmDelete called with:', {userId, userName});
    try {
        const modalElement = document.getElementById('deleteModal');
        if (!modalElement) {
            console.error('Delete modal element not found');
            return false;
        }
        
        const modal = new bootstrap.Modal(modalElement);
        const nameElement = document.getElementById('deleteUserName');
        const idElement = document.getElementById('deleteUserId');
        
        if (!nameElement || !idElement) {
            console.error('Required elements not found in delete modal');
            return false;
        }
        
        nameElement.textContent = userName;
        idElement.value = userId;
        console.log('Showing delete confirmation modal');
        modal.show();
    } catch (error) {
        console.error('Error in confirmDelete:', error);
        if (confirm('Are you sure you want to delete user: ' + userName + '?')) {
            // Fallback if modal fails
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = window.location.href.split('?')[0];
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'delete';
            
            const userIdInput = document.createElement('input');
            userIdInput.type = 'hidden';
            userIdInput.name = 'user_id';
            userIdInput.value = userId;
            
            // Add CSRF token if exists
            const csrfToken = document.querySelector('input[name="csrf_token"]');
            
            form.appendChild(actionInput);
            form.appendChild(userIdInput);
            if (csrfToken) {
                form.appendChild(csrfToken.cloneNode());
            }
            
            document.body.appendChild(form);
            form.submit();
        }
    }
    return false;
};

// Initialize when document is ready
$(document).ready(function() {
    console.log('Document ready');
    
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Handle tab-based navigation
    const urlParams = new URLSearchParams(window.location.search);
    const currentTab = urlParams.get('tab') || 'dashboard';
    console.log('Setting up sidebar active states for tab:', currentTab);
    
    // Remove all active classes first
    $('.sidebar-menu a').removeClass('active');
    
    // Add active class to current tab
    $(`.sidebar-menu a[href*="tab=${currentTab}"]`).addClass('active');
    
    // If no tab-specific link found, activate dashboard
    if ($('.sidebar-menu a.active').length === 0) {
        $('.sidebar-menu a[href*="tab=dashboard"]').addClass('active');
    }
    
    // Handle form submission
    const userForm = document.getElementById('userForm');
    if (userForm) {
        userForm.addEventListener('submit', function(e) {
            const firstName = document.getElementById('firstName')?.value?.trim();
            const lastName = document.getElementById('lastName')?.value?.trim();
            const email = document.getElementById('userEmail')?.value?.trim();
            const password = document.getElementById('userPassword')?.value;
            const confirmPassword = document.getElementById('confirmPassword')?.value;
            const isAdd = document.getElementById('userAction')?.value === 'add';
            
            if (!firstName || !lastName || !email) {
                e.preventDefault();
                alert('Please fill in all required fields');
                return false;
            }
            
            if (isAdd && (!password || !confirmPassword)) {
                e.preventDefault();
                alert('Please enter and confirm the password for new users');
                return false;
            }
            
            if (password && password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match');
                return false;
            }
            
            if (isAdd && password && password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long');
                return false;
            }
            
            return true;
        });
    }
    
    // Initialize add user button
    const addUserBtn = document.querySelector('.btn-primary[onclick*="addUser"], .btn-primary[onclick*="editUser"]');
    if (addUserBtn) {
        addUserBtn.addEventListener('click', function(e) {
            e.preventDefault();
            editUser(0, '', '', '', 'student', 1);
        });
    }
    
    // Handle smooth scrolling for anchor links
    $('a[href^="#"]').not('[href^="#tab-"]').on('click', function(e) {
        const href = $(this).attr('href');
        if (href === '#' || href.startsWith('#')) {
            e.preventDefault();
            const $target = $(href);
            if ($target.length) {
                $('html, body').stop().animate({
                    'scrollTop': $target.offset().top
                }, 900, 'swing');
        return false;
    });

    // Handle edit user button clicks
    $(document).on('click', '.edit-user-btn', function() {
        const $btn = $(this);
        editUser(
            $btn.data('id'),
            $btn.data('firstname'),
            $btn.data('lastname'),
            $btn.data('email'),
            $btn.data('role'),
            $btn.data('status')
        );
        return false;
    });

    // Handle delete user button clicks
    $(document).on('click', '.delete-user-btn', function() {
        const $btn = $(this);
        confirmDelete($btn.data('id'), $btn.data('name'));
        return false;
    });
    
    // Initialize the delete form handler
    const deleteForm = document.getElementById('deleteForm');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to delete this user?')) {
                e.preventDefault();
                return false;
            }
            return true;
        });
    }
    
    // Faculty content management functions
    window.editContent = function(contentType) {
        alert('Edit ' + contentType + ' content functionality will be implemented here.');
        // This would open a modal or redirect to an edit page
        // Example: window.location.href = '?tab=edit-content&type=' + contentType;
    };
    
    window.showAddContentModal = function() {
        alert('Add new content functionality will be implemented here.');
        // This would show a modal for adding new content types
    };
});

// Initialize when document is ready
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Handle tab-based navigation instead of page-based
    const urlParams = new URLSearchParams(window.location.search);
    const currentTab = urlParams.get('tab') || 'dashboard';
    console.log('Setting up sidebar active states for tab:', currentTab);
    
    // Remove all active classes first
    $('.sidebar-menu a').removeClass('active');
    
    // Add active class to current tab
    $('.sidebar-menu a[href="?tab=' + currentTab + '"]').addClass('active');
    
    // If no tab-specific link found, activate dashboard
    if ($('.sidebar-menu a.active').length === 0) {
      $('.sidebar-menu a[href="?tab=dashboard"]').addClass('active');
    }

    // Handle sidebar navigation
    console.log('My-account.php loaded, current URL:', window.location.href);
    
    // Add click handlers to sidebar links
    const sidebarLinks = document.querySelectorAll('.sidebar-menu a');
    sidebarLinks.forEach(function(link) {
      link.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        console.log('Sidebar link clicked:', href);
        
        // If it's a tab link (starts with ?tab=), let it navigate normally
        if (href && href.startsWith('?tab=')) {
          console.log('Navigating to tab:', href);
          // Let the default navigation happen
          return true;
        }
      });
    });
    
    // Active class management is now handled above with jQuery

    // Add smooth scrolling (but not for tab links)
    $('a[href^="#"]').on('click', function(e) {
      e.preventDefault();
      const target = this.getAttribute('href');
      if (target === '#') return;
      
      const $target = $(target);
      $('html, body').stop().animate({
        'scrollTop': $target.offset().top
      }, 900, 'swing', function() {
        window.location.hash = target;
      });
    });
});

// Handle form submission
const userForm = document.getElementById('userForm');
if (userForm) {
    userForm.addEventListener('submit', function(e) {
        // Basic form validation
        const firstName = document.getElementById('firstName').value.trim();
        const lastName = document.getElementById('lastName').value.trim();
        const email = document.getElementById('userEmail').value.trim();
        const password = document.getElementById('userPassword')?.value;
        const confirmPassword = document.getElementById('confirmPassword')?.value;
        const isAdd = document.getElementById('userAction').value === 'add';
        
        if (!firstName || !lastName || !email) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return false;
        }
        
        if (isAdd && (!password || !confirmPassword)) {
            e.preventDefault();
            alert('Please enter and confirm the password for new users');
            return false;
        }
        
        if (password && password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match');
            return false;
        }
        
        // Check password strength for new users
        if (isAdd && password && password.length < 8) {
            e.preventDefault();
            alert('Password must be at least 8 characters long');
            return false;
        }
        
        return true;
    });
}

// Faculty content management functions
function editContent(contentType) {
    alert('Edit ' + contentType + ' content functionality will be implemented here.');
    // This would open a modal or redirect to an edit page
    // Example: window.location.href = '?tab=edit-content&type=' + contentType;
}

function showAddContentModal() {
    alert('Add new content functionality will be implemented here.');
    // This would show a modal for adding new content types
}

// Handle smooth scrolling for anchor links
$('a[href^="#"]').on('click', function(e) {
  const href = $(this).attr('href');
  if (href === '#') return;
  
  e.preventDefault();
  const target = href.split('?')[0];
  const $target = $(target);
  if ($target.length) {
    $('html, body').stop().animate({
      'scrollTop': $target.offset().top
    }, 900, 'swing', function() {
      window.location.hash = target;
    });
          
    window.location.hash = target;
  });
}

// User Management Functions
window.editUser = function(id, firstName = '', lastName = '', email = '', role = 'student', status = 0) {
    try {
        console.log('editUser called with:', {id, firstName, lastName, email, role, status});
        const modalElement = document.getElementById('userModal');
        if (!modalElement) {
            console.error('Error: Could not find user modal element');
            return false;
        }

        const modal = new bootstrap.Modal(modalElement);
        
        // Set form action and title
        const title = id ? 'Edit User' : 'Add New User';
        document.getElementById('userModalTitle').textContent = title;
        document.getElementById('userAction').value = id ? 'update' : 'add';
        document.getElementById('userId').value = id || '';
        
        // Fill the form
        document.getElementById('firstName').value = firstName || '';
        document.getElementById('lastName').value = lastName || '';
        document.getElementById('userEmail').value = email || '';
        document.getElementById('userRole').value = role || 'student';
        document.getElementById('userStatus').checked = status == 1;
        
        // Show/hide password fields
        const passwordFields = document.getElementById('passwordFields');
        if (passwordFields) {
            passwordFields.style.display = id ? 'none' : 'block';
        }
        
        // Show the modal
        console.log('Showing user modal...');
        modal.show();
        return false;
    } catch (error) {
        console.error('Error in editUser:', error);
        alert('Error initializing user form. Please try again.');
        return false;
    }
};

 function confirmDelete(userId, userName) {
    console.log('=== confirmDelete called ===');
    console.log('userId:', userId, 'userName:', userName);
    
    // Check if Bootstrap is loaded
    if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
        console.error('Bootstrap Modal not found!');
        alert('Error: Required libraries not loaded. Please refresh the page.');
        return false;
    }
    
    // Get modal element
    const deleteModal = document.getElementById('deleteModal');
    console.log('deleteModal element:', deleteModal);
    
    if (!deleteModal) {
        console.error('Error: Could not find delete modal element');
        alert('Error: Could not initialize delete confirmation. Please try again.');
        return false;
    }
    
    // Initialize modal
    try {
        const modal = new bootstrap.Modal(deleteModal);
        console.log('Modal initialized:', modal);
        
        // Set user data
        const nameElement = document.getElementById('deleteUserName');
        const idElement = document.getElementById('deleteUserId');
        console.log('Name element:', nameElement, 'ID element:', idElement);
        
        if (nameElement) {
            nameElement.textContent = userName;
            console.log('Set user name to:', userName);
        } else {
            console.warn('Could not find deleteUserName element');
        }
        
        if (idElement) {
            idElement.value = userId;
            console.log('Set user ID to:', userId);
        } else {
            console.warn('Could not find deleteUserId element');
        }
        
        // Show modal
        console.log('Showing modal...');
        modal.show();
        console.log('Modal should be visible now');
        
        // Add debug event listeners
        deleteModal.addEventListener('shown.bs.modal', function() {
            console.log('Modal shown event fired');
        });
        
        return false;
        
    } catch (error) {
        console.error('Error in confirmDelete:', error);
        console.error('Error details:', {
            name: error.name,
            message: error.message,
            stack: error.stack
        });
        
        // Fallback confirmation
        if (confirm('Error showing delete confirmation. Are you sure you want to delete user: ' + userName + '?')) {
            console.log('User confirmed deletion via fallback');
            
            // Create form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = window.location.href.split('?')[0];
            
            // Add CSRF token if exists
            const csrfToken = document.querySelector('input[name="csrf_token"]');
            if (csrfToken) {
                const csrfClone = csrfToken.cloneNode();
                csrfClone.name = 'csrf_token';
                form.appendChild(csrfClone);
            }
            
            // Add action
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'delete';
            
            // Add user ID
            const userIdInput = document.createElement('input');
            userIdInput.type = 'hidden';
            userIdInput.name = 'user_id';
            userIdInput.value = userId;
            
            console.log('Form data prepared:', {
                action: 'delete',
                user_id: userId,
                hasCsrf: !!csrfToken
            });
            
            // Submit form
            form.appendChild(actionInput);
            form.appendChild(userIdInput);
            document.body.appendChild(form);
            console.log('Submitting delete form...');
            form.submit();
        }
        return false;
    }
}
</script>
</body>
</html>
<?php endif; ?>