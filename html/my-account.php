<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug: Check what tab is being requested
$current_tab = $_GET['tab'] ?? 'dashboard';
error_log("My-account.php - Current tab: " . $current_tab);

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

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile']) && $user_id) {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $bio = $_POST['bio'] ?? '';
    
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
    $profile_image = $user['profile_image'] ?? 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMTAwIDEwMCI+PHJlY3Qgd2lkdGg9IjEwMCIgaGVpZ2h0PSIxMDAiIGZpbGw9IiNkZGQiLz48dGV4dCB4PSI1MCIgeT0iNTAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgYWxpZ25tZW50LWJhc2VsaW5lPSJtaWRkbGUiIGZpbGw9IiYjNjY2OyI+QXZhdGFyPC90ZXh0Pjwvc3ZnPg';
    
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/uploads/profiles/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $file_extension;
            $target_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_path)) {
                $profile_image = 'uploads/profiles/' . $new_filename;
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
             <img src="<?php echo !empty($user['profile_image']) ? $user['profile_image'] : 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMTAwIDEwMCI+PHJlY3Qgd2lkdGg9IjEwMCIgaGVpZ2h0PSIxMDAiIGZpbGw9IiNkZGQiLz48dGV4dCB4PSI1MCIgeT0iNTAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgYWxpZ25tZW50LWJhc2VsaW5lPSJtaWRkbGUiIGZpbGw9IiYjNjY2OyI+QXZhdGFyPC90ZXh0Pjwvc3ZnPg'; ?>" alt="Profile Image" id="sidebar-profile-img">
            </div>
            <h3 class="profile-name"><?php echo htmlspecialchars($user_name); ?></h3>
            <div class="profile-role"><?php echo ucfirst(htmlspecialchars($user_role)); ?></div>
          </div>
          <ul class="sidebar-menu">
            <li><a href="?tab=dashboard" class="<?php echo (!isset($_GET['tab']) || $_GET['tab'] === 'dashboard') ? 'active' : ''; ?>"><i class="fa fa-tachometer"></i> Dashboard</a></li>
            <li><a href="?tab=profile" class="<?php echo (isset($_GET['tab']) && $_GET['tab'] === 'profile') ? 'active' : ''; ?>"><i class="fa fa-user"></i> My Profile</a></li>
            <?php if ($is_admin): ?>
            <li><a href="admin/users.php" class="<?php echo (basename(dirname($_SERVER['PHP_SELF'])) === 'admin' && basename($_SERVER['PHP_SELF']) === 'users.php') ? 'active' : ''; ?>"><i class="fa fa-users"></i> Manage Users</a></li>
            <?php endif; ?>
            <?php if ($is_dean): ?>
            <li><a href="?tab=faculty-content" class="<?php echo (isset($_GET['tab']) && $_GET['tab'] === 'faculty-content') ? 'active' : ''; ?>"><i class="fa fa-graduation-cap"></i> Faculty Content</a></li>
            <?php endif; ?>
            <li><a href="?tab=messages" class="<?php echo (isset($_GET['tab']) && $_GET['tab'] === 'messages') ? 'active' : ''; ?>"><i class="fa fa-envelope"></i> Messages</a></li>
            <li><a href="?tab=settings" class="<?php echo (isset($_GET['tab']) && $_GET['tab'] === 'settings') ? 'active' : ''; ?>"><i class="fa fa-cog"></i> Settings</a></li>
            <?php if ($is_admin): ?>
            <li><a href="/c/zanvarsity/html/admin/admin-panel.php"><i class="fa fa-lock"></i> Admin Panel</a></li>
            <?php endif; ?>
            <li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
          </ul>
        </div>
      </div>

      <!-- Main Content -->
      <div class="col-lg-9">
        <?php if (isset($_GET['tab']) && $_GET['tab'] === 'manage-users' && $is_admin): ?>
          <!-- Manage Users Section -->
          <div class="manage-users">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h2><i class="fa fa-users-cog"></i> Manage Users</h2>
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
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
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sql = "SELECT id, first_name, last_name, email, role, is_active FROM users ORDER BY created_at DESC";
                      $result = $conn->query($sql);
                      
                      if ($result && $result->num_rows > 0) {
                          while ($user = $result->fetch_assoc()) {
                              $is_current_user = ($user['id'] == $_SESSION['user_id']);
                              ?>
                              <tr>
                                <td>#<?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                  <?php if ($is_current_user): ?>
                                    <?php echo ucfirst($user['role']); ?>
                                  <?php else: ?>
                                    <form method="post" class="d-inline" onchange="this.submit()">
                                      <input type="hidden" name="action" value="update_role">
                                      <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                      <select name="new_role" class="form-select form-select-sm" style="width: auto;">
                                        <option value="student" <?php echo $user['role'] === 'student' ? 'selected' : ''; ?>>Student</option>
                                        <option value="lecturer" <?php echo $user['role'] === 'lecturer' ? 'selected' : ''; ?>>Lecturer</option>
                                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                      </select>
                                    </form>
                                  <?php endif; ?>
                                </td>
                                <td>
                                  <span class="badge <?php echo $user['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                    <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                  </span>
                                </td>
                                <td>
                                  <div class="btn-group">
                                    <a href="?tab=edit-user&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-info" title="Edit">
                                      <i class="fa fa-edit"></i>
                                    </a>
                                    <?php if (!$is_current_user): ?>
                                      <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
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
                          echo '<tr><td colspan="6" class="text-center">No users found</td></tr>';
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
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                      <select class="form-select" id="role" name="role" required>
                        <option value="student">Student</option>
                        <option value="lecturer">Lecturer</option>
                        <option value="admin">Admin</option>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

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
                    <div class="profile-image-large mb-3">
                      <img src="<?php echo !empty($user['profile_image']) ? $user['profile_image'] : 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMTAwIDEwMCI+PHJlY3Qgd2lkdGg9IjEwMCIgaGVpZ2h0PSIxMDAiIGZpbGw9IiNkZGQiLz48dGV4dCB4PSI1MCIgeT0iNTAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgYWxpZ25tZW50LWJhc2VsaW5lPSJtaWRkbGUiIGZpbGw9IiYjNjY2OyI+QXZhdGFyPC90ZXh0Pjwvc3ZnPg'; ?>" alt="Profile Image" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
                    </div>
                    <button class="btn btn-primary">Change Photo</button>
                  </div>
                  <div class="col-md-8">
                    <form>
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <label class="form-label">First Name</label>
                          <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                          <label class="form-label">Last Name</label>
                          <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" readonly>
                        </div>
                        <div class="col-md-12 mb-3">
                          <label class="form-label">Email</label>
                          <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                          <label class="form-label">Role</label>
                          <input type="text" class="form-control" value="<?php echo ucfirst(htmlspecialchars($user_role)); ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                          <label class="form-label">Phone</label>
                          <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?>" readonly>
                        </div>
                      </div>
                      <button type="button" class="btn btn-success">Edit Profile</button>
                    </form>
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
<!-- JavaScript -->
<script src="assets/js/jquery-2.1.0.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<!-- Popper.js is required for Bootstrap tooltips -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<!-- Initialize Bootstrap tooltips -->
<script>
  // Make sure jQuery is loaded before executing scripts
  jQuery(document).ready(function($) {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Add active class to current nav item
    const currentPage = window.location.pathname.split('/').pop() || 'index.php';
    $('.sidebar-menu a').each(function() {
      if ($(this).attr('href') === currentPage) {
        $(this).addClass('active');
      }
    });

    // Tab navigation is handled by PHP URL parameters, not JavaScript
    // The sidebar navigation works with page reloads using ?tab=name parameters
    
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
      }
    });
  });
</script>
<script>
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
</script>
</body>
</html>
<?php endif; ?>