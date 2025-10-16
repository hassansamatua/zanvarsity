<div class="admin-dashboard">
    <h2><i class="fa fa-tachometer"></i> Admin Dashboard</h2>
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
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
                    <a href="?tab=users" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fa fa-book"></i>
                </div>
                <div class="stat-info">
                    <h3>
                        <?php 
                        $sql = "SELECT COUNT(*) as count FROM courses";
                        $result = $conn->query($sql);
                        echo $result ? $result->fetch_assoc()['count'] : '0';
                        ?>
                    </h3>
                    <p>Total Courses</p>
                    <a href="?tab=content" class="btn btn-sm btn-outline-success">Manage</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fa fa-history"></i>
                </div>
                <div class="stat-info">
                    <h3>
                        <?php 
                        // This would need a proper activities table
                        $sql = "SELECT COUNT(DISTINCT user_id) as count FROM user_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                        $result = $conn->query($sql);
                        echo $result ? $result->fetch_assoc()['count'] . ' active' : '0';
                        ?>
                    </h3>
                    <p>Active Users (7 days)</p>
                    <a href="?tab=activities" class="btn btn-sm btn-outline-info">View Activities</a>
                </div>
            </div>
        </div>
    </div>

    <div class="recent-activities mt-4">
        <div class="card">
            <div class="card-header">
                <h4><i class="fa fa-clock"></i> Recent Activities</h4>
            </div>
            <div class="card-body">
                <?php
                // This is a placeholder - you'll need to implement the actual activity logging
                echo '<p class="text-muted">Recent user activities will be displayed here.</p>';
                ?>
            </div>
        </div>
    </div>
</div>
