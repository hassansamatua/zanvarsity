<?php
// Include database connection
require_once __DIR__ . '/../../includes/db_connect.php';

try {
    // Query to fetch staff with title 'DR' and is_active = 1
    $query = "SELECT * FROM staff WHERE title = 'DR' AND is_active = 1 ORDER BY name ASC LIMIT 6";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        while ($doctor = $result->fetch_assoc()) {
            // Set default image if not available
            $image = !empty($doctor['image']) ? 'admin/uploads/staff/' . $doctor['image'] : 'assets/img/doctor.jpg';
            ?>
            <article class="doctor-thumbnail">
                <figure class="doctor-image">
                    <a href="staff-detail.php?id=<?php echo $doctor['id']; ?>">
                        <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($doctor['name']); ?>">
                    </a>
                </figure>
                <aside>
                    <header>
                        <a href="staff-detail.php?id=<?php echo $doctor['id']; ?>"><?php echo htmlspecialchars($doctor['name']); ?></a>
                        <div class="divider"></div>
                        <figure class="doctor-description">
                            <?php echo !empty($doctor['department']) ? htmlspecialchars($doctor['department']) : 'Medical Staff'; ?>
                        </figure>
                    </header>
                    <a href="staff-detail.php?id=<?php echo $doctor['id']; ?>" class="show-profile">View Profile</a>
                </aside>
            </article>
            <!-- /.doctor-thumbnail -->
            <?php
        }
    } else {
        echo '<p class="text-center">No Doctors found.</p>';
    }
} catch (Exception $e) {
    echo '<p class="text-center">Error loading doctors. Please try again later.</p>';
    error_log('Error in doctors section: ' . $e->getMessage());
}
?>
<a href="staff.php?filter=dr" class="read-more stick-to-bottom">All Doctors</a>
