<?php
// Include the announcements retrieval script
require_once __DIR__ . '/../../includes/get_announcements.php';
?>

<div class="col-md-4 col-sm-6">
    <section class="events small" id="events-small">
        <header>
            <h2>Announcements</h2>
            <a href="#" class="link-calendar">View All</a>
        </header>
        <div class="section-content">
            <?php
            // Counter to track the iteration for adding specific classes
            $counter = 0;
            
            // Display each announcement
            foreach ($announcements as $announcement) {
                // Determine the class based on the counter
                $class = '';
                if ($counter === 0) {
                    $class = 'nearest';
                } elseif ($counter === 1) {
                    $class = 'nearest-second';
                }
                
                // Format the date
                $month = formatMonth($announcement['announcement_date']);
                $day = formatDay($announcement['announcement_date']);
                
                // Output the announcement HTML
                echo '<article class="event ' . $class . '">';
                echo '    <figure class="date">';
                echo '        <div class="month">' . $month . '</div>';
                echo '        <div class="day">' . $day . '</div>';
                echo '    </figure>';
                echo '    <aside>';
                echo '        <header>';
                echo '            <a href="event-detail.php?id=' . $announcement['id'] . '">' . htmlspecialchars($announcement['title']) . '</a>';
                echo '        </header>';
                if (!empty($announcement['location'])) {
                    echo '        <div class="additional-info' . ($counter === 1 ? ' clearfix' : '') . '">' . htmlspecialchars($announcement['location']) . '</div>';
                }
                echo '    </aside>';
                echo '</article>';
                
                $counter++;
            }
            
            // If no announcements found, show a default message
            if (empty($announcements)) {
                echo '<p>No announcements available at the moment.</p>';
            }
            ?>
        </div>
        <!-- /.section-content -->
    </section>
    <!-- /.events-small -->
</div>
