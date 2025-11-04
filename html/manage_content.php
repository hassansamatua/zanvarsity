<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define root path
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__DIR__)));
}

// Include necessary files
require_once ROOT_PATH . '/zanvarsity/includes/auth_functions.php';
require_once ROOT_PATH . '/zanvarsity/includes/database.php';

// Check if user is logged in and has admin/dean privileges
require_login();

// Get user information from session
$user_id = $_SESSION['user_id'] ?? null;
$user_email = $_SESSION['email'] ?? 'Guest';
$user_name = $_SESSION['first_name'] ?? 'User';
$user_role = $_SESSION['role'] ?? 'student';
$is_admin = in_array($user_role, ['admin', 'super_admin']);
$is_dean = ($user_role === 'dean');

if (!($is_admin || $is_dean)) {
    $_SESSION['error'] = "You don't have permission to access this page.";
    header("Location: index.php");
    exit();
}

// Set page title
$page_title = 'Manage Content | Zanvarsity';

// Include header
include 'includes/about_header.php';
?>

<style>
  /* Sidebar Styles */
  .sidebar {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    padding: 0;
    margin-bottom: 30px;
    overflow: hidden;
  }

  .profile-card {
    padding: 20px;
    text-align: center;
    background: #f8f9fa;
    border-bottom: 1px solid #eee;
  }

  .profile-image {
    width: 120px;
    height: 120px;
    margin: 0 auto 15px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid #fff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  }

  .profile-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .profile-name {
    margin: 10px 0 5px;
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
  }

  .profile-role {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 0;
  }

  .sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
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
    transition: all 0.3s ease;
    position: relative;
  }

  .sidebar-menu a i {
    width: 20px;
    margin-right: 10px;
    text-align: center;
  }

  .sidebar-menu a:hover,
  .sidebar-menu a.active {
    background: #f8f9fa;
    color: #014421;
    padding-left: 25px;
  }

  /* Responsive adjustments */
  @media (max-width: 991px) {
    .sidebar {
      margin-bottom: 30px;
    }
  }

  /* Dashboard Styles */
  .dashboard-container {
    padding: 30px 0;
  }

  .dashboard-content {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    padding: 25px;
  }

  .dashboard-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    padding: 20px;
    margin-bottom: 20px;
    height: 100%;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  }

  .card-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
  }
</style>

<div class="dashboard-container">
  <div class="container">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-lg-3">
        <div class="sidebar">
          <div class="profile-card">
            <div class="profile-image">
              <img src="<?php echo !empty($_SESSION['profile_image']) ? $_SESSION['profile_image'] : 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMTAwIDEwMCI+PHJlY3Qgd2lkdGg9IjEwMCIgaGVpZ2h0PSIxMDAiIGZpbGw9IiNkZGQiLz48dGV4dCB4PSI1MCIgeT0iNTAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgYWxpZ25tZW50LWJhc2VsaW5lPSJtaWRkbGUiIGZpbGw9IiYjNjY2OyI+QXZhdGFyPC90ZXh0Pjwvc3ZnPg'; ?>" alt="Profile Image" id="sidebar-profile-img">
            </div>
            <h3 class="profile-name"><?php echo htmlspecialchars($user_name); ?></h3>
            <div class="profile-role"><?php echo ucfirst(htmlspecialchars($user_role)); ?></div>
          </div>
          <ul class="sidebar-menu">
            <li><a href="my-account.php?tab=dashboard"><i class="fa fa-tachometer"></i> Dashboard</a></li>
            <li><a href="my-account.php?tab=profile"><i class="fa fa-user"></i> My Profile</a></li>
            <?php if ($is_admin): ?>
            <li><a href="admin/users.php"><i class="fa fa-users"></i> Manage Users</a></li>
            <?php endif; ?>
            <?php if ($is_dean): ?>
            <li><a href="my-account.php?tab=faculty-content"><i class="fa fa-graduation-cap"></i> Faculty Content</a></li>
            <?php endif; ?>
            <li><a href="my-account.php?tab=messages"><i class="fa fa-envelope"></i> Messages</a></li>
            <li><a href="my-account.php?tab=settings"><i class="fa fa-cog"></i> Settings</a></li>
            <li class="active"><a href="manage_content.php"><i class="fa fa-edit"></i> Manage Content</a></li>
            <li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
          </ul>
        </div>
      </div>
      
      <!-- Main Content -->
      <div class="col-lg-9">
        <section class="block">
          <div class="page-title">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h2><i class='bx bxs-dashboard me-2'></i>Content Management Dashboard</h2>
            </div>
          </div>

          <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <?php endif; ?>
          
          <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <?php endif; ?>

          <div class="row">
            <!-- Events Management -->
            <div class="col-md-4 col-sm-6 mb-4">
              <a href="manage_events.php" class="text-decoration-none">
                <div class="dashboard-card card h-100">
                  <div class="card-body text-center">
                    <div class="card-icon text-primary">
                      <i class='bx bx-calendar-event'></i>
                    </div>
                    <h4 class="card-title">Manage Events</h4>
                    <p class="card-text text-muted">Create, edit, and manage university events and activities</p>
                  </div>
                </div>
              </a>
            </div>

            <!-- Announcements Management -->
            <div class="col-md-4 col-sm-6 mb-4">
              <a href="manage_announcements.php" class="text-decoration-none">
                <div class="dashboard-card card h-100">
                  <div class="card-body text-center">
                    <div class="card-icon text-success">
                      <i class='bx bx-bullhorn'></i>
                    </div>
                    <h4 class="card-title">Manage Announcements</h4>
                    <p class="card-text text-muted">Post and manage important university announcements</p>
                  </div>
                </div>
              </a>
            </div>

            <!-- Publications Management -->
            <div class="col-md-4 col-sm-6 mb-4">
              <a href="manage_publications.php" class="text-decoration-none">
                <div class="dashboard-card card h-100">
                  <div class="card-body text-center">
                    <div class="card-icon text-info">
                      <i class='bx bx-book-open'></i>
                    </div>
                    <h4 class="card-title">Manage Publications</h4>
                    <p class="card-text text-muted">Manage research papers and publications</p>
                  </div>
                </div>
              </a>
            </div>

            <!-- Staff Management -->
            <div class="col-md-4 col-sm-6 mb-4">
              <a href="manage_staff.php" class="text-decoration-none">
                <div class="dashboard-card card h-100">
                  <div class="card-body text-center">
                    <div class="card-icon text-warning">
                      <i class='bx bx-user-pin'></i>
                    </div>
                    <h4 class="card-title">Manage Staff</h4>
                    <p class="card-text text-muted">Manage university staff members and their profiles</p>
                  </div>
                </div>
              </a>
            </div>

            <!-- VC Notice Management -->
            <div class="col-md-4 col-sm-6 mb-4">
              <a href="manage_vc_notice.php" class="text-decoration-none">
                <div class="dashboard-card card h-100">
                  <div class="card-body text-center">
                    <div class="card-icon text-danger">
                      <i class='bx bx-news'></i>
                    </div>
                    <h4 class="card-title">VC Notices</h4>
                    <p class="card-text text-muted">Manage notices from the Vice Chancellor's office</p>
                  </div>
                </div>
              </a>
            </div>

            <!-- Faculty Management -->
            <div class="col-md-4 col-sm-6 mb-4">
              <a href="manage_faculties.php" class="text-decoration-none">
                <div class="dashboard-card card h-100">
                  <div class="card-body text-center">
                    <div class="card-icon text-primary">
                      <i class='bx bx-buildings'></i>
                    </div>
                    <h4 class="card-title">Manage Faculties</h4>
                    <p class="card-text text-muted">Manage university faculties and departments</p>
                  </div>
                </div>
              </a>
            </div>

            <!-- Downloads Management -->
            <div class="col-md-4 col-sm-6 mb-4">
              <a href="manage_downloads.php" class="text-decoration-none">
                <div class="dashboard-card card h-100">
                  <div class="card-body text-center">
                    <div class="card-icon text-success">
                      <i class='bx bx-download'></i>
                    </div>
                    <h4 class="card-title">Manage Downloads</h4>
                    <p class="card-text text-muted">Manage downloadable files and resources</p>
                  </div>
                </div>
              </a>
            </div>

            <!-- Departments Management -->
            <div class="col-md-4 col-sm-6 mb-4">
              <a href="manage_departments.php" class="text-decoration-none">
                <div class="dashboard-card card h-100">
                  <div class="card-body text-center">
                    <div class="card-icon text-info">
                      <i class='bx bx-list-ul'></i>
                    </div>
                    <h4 class="card-title">Manage Departments</h4>
                    <p class="card-text text-muted">Manage academic departments and programs</p>
                  </div>
                </div>
              </a>
            </div>

            <!-- Programmes Management -->
            <div class="col-md-4 col-sm-6 mb-4">
              <a href="manage_programmes.php" class="text-decoration-none">
                <div class="dashboard-card card h-100">
                  <div class="card-body text-center">
                    <div class="card-icon text-warning">
                      <i class='bx bx-book-content'></i>
                    </div>
                    <h4 class="card-title">Manage Programmes</h4>
                    <p class="card-text text-muted">Manage academic programs and courses</p>
                  </div>
                </div>
              </a>
            </div>
                </div>
              </div>
            </div>

            <!-- Announcements -->
            <div class="col-md-4 col-sm-6">
              <div class="dashboard-card card h-100">
                <div class="card-body text-center">
                  <div class="card-icon">
                    <i class='bx bx-megaphone'></i>
                  </div>
                  <h5 class="card-title">Announcements</h5>
                  <p class="card-text">Create and manage important university announcements.</p>
                </div>
                <div class="card-footer text-center">
                  <a href="admin/manage_announcements.php" class="btn btn-success btn-sm">
                    <i class='bx bx-edit me-1'></i> Manage Announcements
                  </a>
                </div>
              </div>
            </div>

            <!-- Publications -->
            <div class="col-md-4 col-sm-6">
              <div class="dashboard-card card h-100">
                <div class="card-body text-center">
                  <div class="card-icon">
                    <i class='bx bx-book-open'></i>
                  </div>
                  <h5 class="card-title">Publications</h5>
                  <p class="card-text">Manage research papers, articles, and publications.</p>
                </div>
                <div class="card-footer text-center">
                  <a href="admin/manage_publications.php" class="btn btn-success btn-sm">
                    <i class='bx bx-edit me-1'></i> Manage Publications
                  </a>
                </div>
              </div>
            </div>

            <!-- Downloads -->
            <div class="col-md-4 col-sm-6">
              <div class="dashboard-card card h-100">
                <div class="card-body text-center">
                  <div class="card-icon">
                    <i class='bx bx-download'></i>
                  </div>
                  <h5 class="card-title">Downloads</h5>
                  <p class="card-text">Manage downloadable resources and documents.</p>
                </div>
                <div class="card-footer text-center">
                  <a href="admin/manage_downloads.php" class="btn btn-success btn-sm">
                    <i class='bx bx-edit me-1'></i> Manage Downloads
                  </a>
                </div>
              </div>
            </div>

            <!-- Staff Management -->
            <div class="col-md-4 col-sm-6">
              <div class="dashboard-card card h-100">
                <div class="card-body text-center">
                  <div class="card-icon">
                    <i class='bx bx-group'></i>
                  </div>
                  <h3 class="card-title">Manage Staff</h3>
                  <p class="card-text">Add, edit, or remove staff members and manage their details.</p>
                </div>
                <div class="card-footer text-center">
                  <a href="admin/manage_staff.php" class="btn btn-success btn-sm">
                    <i class='bx bx-edit me-1'></i> Manage Staff
                  </a>
                </div>
              </div>
            </div>

            <!-- VC Notice -->
            <div class="col-md-4 col-sm-6">
              <div class="dashboard-card card h-100">
                <div class="card-body text-center">
                  <div class="card-icon">
                    <i class='bx bx-book-open'></i>
                  </div>
                  <h5 class="card-title">Vc Notice</h5>
                  <p class="card-text">Manage Vice Chancellor's messages and notices.</p>
                </div>
                <div class="card-footer text-center">
                  <a href="admin/manage_vc_notice.php" class="btn btn-success btn-sm">
                    <i class='bx bx-edit me-1'></i> Manage Vc Notice
                  </a>
                </div>
              </div>
            </div>

            <!-- Carousel Images -->
            <div class="col-md-4 col-sm-6">
              <div class="dashboard-card card h-100">
                <div class="card-body text-center">
                  <div class="card-icon">
                    <i class='bx bx-slideshow'></i>
                  </div>
                  <h5 class="card-title">Carousel Images</h5>
                  <p class="card-text">Manage homepage slider/carousel images.</p>
                </div>
                <div class="card-footer text-center">
                  <a href="admin/manage_courasel.php" class="btn btn-success btn-sm">
                    <i class='bx bx-edit me-1'></i> Manage Carousel
                  </a>
                </div>
              </div>
            </div>

            <!-- Faculties -->
            <div class="col-md-4 col-sm-6">
              <div class="dashboard-card card h-100">
                <div class="card-body text-center">
                  <div class="card-icon">
                    <i class='bx bx-building-house'></i>
                  </div>
                  <h5 class="card-title">Faculties</h5>
                  <p class="card-text">Manage university faculties and departments.</p>
                </div>
                <div class="card-footer text-center">
                  <a href="admin/manage_faculties.php" class="btn btn-success btn-sm">
                    <i class='bx bx-edit me-1'></i> Manage Faculties
                  </a>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
</div>

<?php
// Include footer
include 'includes/about_footer.php';
?>

<!-- JavaScript -->
<script src="assets/js/jquery-2.1.0.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

<script>
// Initialize tooltips
jQuery(document).ready(function($) {
    $('[data-toggle="tooltip"]').tooltip();
    
    // Add active class to current nav item
    $('.sidebar-menu a').each(function() {
        if (this.href === window.location.href) {
            $(this).addClass('active');
        }
    });
});
</script>
