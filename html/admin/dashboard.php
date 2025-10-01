<?php
require_once '../../includes/auth_functions.php';
require_login();

// Check if user has admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    error_log("Access denied. User role: " . ($_SESSION['role'] ?? 'not set'));
    header("Location: /zanvarsity/html/403.php");
    exit();
}

// Include database connection
require_once '../../includes/database.php';

// Get counts for dashboard
$counts = [
    'users' => $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'],
    'news' => $conn->query("SELECT COUNT(*) as count FROM news")->fetch_assoc()['count'],
    'events' => $conn->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'],
    'announcements' => $conn->query("SELECT COUNT(*) as count FROM announcements")->fetch_assoc()['count'],
    'faculties' => $conn->query("SELECT COUNT(*) as count FROM faculties")->fetch_assoc()['count'],
    'departments' => $conn->query("SELECT COUNT(*) as count FROM departments")->fetch_assoc()['count'],
    'courses' => $conn->query("SELECT COUNT(*) as count FROM courses")->fetch_assoc()['count'],
    'staff' => $conn->query("SELECT COUNT(*) as count FROM staff")->fetch_assoc()['count'],
    'facilities' => $conn->query("SELECT COUNT(*) as count FROM facilities")->fetch_assoc()['count'],
    'organizations' => $conn->query("SELECT COUNT(*) as count FROM student_organizations")->fetch_assoc()['count'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Zanvarsity</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.1/css/boxicons.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
            color: white;
            transition: all 0.3s;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: #34495e;
            color: white;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        .main-content {
            padding: 20px;
        }
        .stat-card {
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            color: white;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .bg-primary { background: #3498db; }
        .bg-success { background: #2ecc71; }
        .bg-warning { background: #f39c12; }
        .bg-danger { background: #e74c3c; }
        .bg-info { background: #1abc9c; }
        .bg-secondary { background: #7f8c8d; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="d-flex flex-column p-3">
                    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                        <span class="fs-4">Zanvarsity</span>
                    </a>
                    <hr>
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="dashboard.php" class="nav-link active">
                                <i class='bx bxs-dashboard'></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="users.php" class="nav-link">
                                <i class='bx bxs-user'></i> Users
                            </a>
                        </li>
                        <li>
                            <a href="#contentSubmenu" data-bs-toggle="collapse" class="nav-link">
                                <i class='bx bxs-news'></i> Content
                            </a>
                            <ul class="collapse nav flex-column ms-3" id="contentSubmenu">
                                <li><a href="news.php" class="nav-link">News</a></li>
                                <li><a href="events.php" class="nav-link">Events</a></li>
                                <li><a href="announcements.php" class="nav-link">Announcements</a></li>
                                <li><a href="downloads.php" class="nav-link">Downloads</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#academicsSubmenu" data-bs-toggle="collapse" class="nav-link">
                                <i class='bx bxs-graduation'></i> Academics
                            </a>
                            <ul class="collapse nav flex-column ms-3" id="academicsSubmenu">
                                <li><a href="faculties.php" class="nav-link">Faculties</a></li>
                                <li><a href="departments.php" class="nav-link">Departments</a></li>
                                <li><a href="courses.php" class="nav-link">Courses</a></li>
                                <li><a href="staff.php" class="nav-link">Staff</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#campusSubmenu" data-bs-toggle="collapse" class="nav-link">
                                <i class='bx bxs-building-house'></i> Campus
                            </a>
                            <ul class="collapse nav flex-column ms-3" id="campusSubmenu">
                                <li><a href="facilities.php" class="nav-link">Facilities</a></li>
                                <li><a href="organizations.php" class="nav-link">Student Organizations</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="settings.php" class="nav-link">
                                <i class='bx bxs-cog'></i> Settings
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class='bx bxs-user-circle fs-4 me-2'></i>
                            <strong><?php echo htmlspecialchars($_SESSION['user_email']); ?></strong>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                            <li><a class="dropdown-item" href="<?php echo dirname(dirname($_SERVER['PHP_SELF'])); ?>/profile.php"><i class="fa fa-user"></i> My Profile</a></li>
                            <li><a class="dropdown-item" href="<?php echo dirname(dirname($_SERVER['PHP_SELF'])); ?>/change-password.php"><i class="fa fa-key"></i> Change Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo dirname(dirname($_SERVER['PHP_SELF'])); ?>/logout.php" onclick="return confirm('Are you sure you want to log out?');"><i class="fa fa-sign-out"></i> Log Out</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 ms-sm-auto px-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                            <i class='bx bxs-calendar-alt'></i> This week
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-card bg-primary">
                            <h5>Users</h5>
                            <h2><?php echo $counts['users']; ?></h2>
                            <a href="users.php" class="text-white">View all <i class='bx bx-chevron-right'></i></a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card bg-success">
                            <h5>News</h5>
                            <h2><?php echo $counts['news']; ?></h2>
                            <a href="news.php" class="text-white">Manage <i class='bx bx-chevron-right'></i></a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card bg-warning">
                            <h5>Events</h5>
                            <h2><?php echo $counts['events']; ?></h2>
                            <a href="events.php" class="text-white">Manage <i class='bx bx-chevron-right'></i></a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card bg-danger">
                            <h5>Announcements</h5>
                            <h2><?php echo $counts['announcements']; ?></h2>
                            <a href="announcements.php" class="text-white">Manage <i class='bx bx-chevron-right'></i></a>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <a href="news.php?action=add" class="btn btn-outline-primary w-100 mb-2">
                                            <i class='bx bx-plus'></i> Add News
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="events.php?action=add" class="btn btn-outline-success w-100 mb-2">
                                            <i class='bx bx-calendar-plus'></i> Add Event
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="announcements.php?action=add" class="btn btn-outline-warning w-100 mb-2">
                                            <i class='bx bx-megaphone'></i> Add Announcement
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="users.php?action=add" class="btn btn-outline-info w-100 mb-2">
                                            <i class='bx bx-user-plus'></i> Add User
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Activity</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <?php
                                    $recent_activity = $conn->query("
                                        SELECT * FROM user_logs 
                                        ORDER BY created_at DESC 
                                        LIMIT 5
                                    ")->fetch_all(MYSQLI_ASSOC);
                                    
                                    if (count($recent_activity) > 0) {
                                        foreach ($recent_activity as $activity) {
                                            $user = $conn->query("SELECT email FROM users WHERE id = {$activity['user_id']}")->fetch_assoc();
                                            echo "<div class='list-group-item list-group-item-action'>";
                                            echo "<div class='d-flex w-100 justify-content-between'>";
                                            echo "<h6 class='mb-1'>{$activity['action']}</h6>";
                                            echo "<small>" . date('M d, H:i', strtotime($activity['created_at'])) . "</small>";
                                            echo "</div>";
                                            echo "<p class='mb-1'>By: {$user['email']}</p>";
                                            if ($activity['table_name']) {
                                                echo "<small>Table: {$activity['table_name']}" . 
                                                     ($activity['record_id'] ? " (ID: {$activity['record_id']})" : "") . "</small>";
                                            }
                                            echo "</div>";
                                        }
                                    } else {
                                        echo "<p class='text-muted text-center my-3'>No recent activity</p>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">System Statistics</h5>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary">Daily</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary active">Weekly</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary">Monthly</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="statisticsChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Statistics Chart
        var ctx = document.getElementById('statisticsChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                datasets: [
                    {
                        label: 'Users',
                        data: [12, 19, 3, 5, 2, 3, 8],
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Page Views',
                        data: [8, 15, 7, 12, 9, 14, 11],
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false,
                        text: 'Monthly Statistics'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
