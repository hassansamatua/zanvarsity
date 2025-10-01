<?php
// Include database connection
require_once __DIR__ . '/../includes/db_connect.php';

// Fetch upcoming events from database
try {
    $today = date('Y-m-d');
    $eventsQuery = "SELECT * FROM event 
                   WHERE start_date > ? 
                   ORDER BY start_date ASC 
                   LIMIT 3";
    
    $hasEvents = false;
    if ($stmt = $conn->prepare($eventsQuery)) {
        $stmt->bind_param('s', $today);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            while ($event = $result->fetch_assoc()) {
                $eventDate = !empty($event['start_date']) ? date('m-d-Y', strtotime($event['start_date'])) : date('m-d-Y');
                $eventTitle = !empty($event['title']) ? htmlspecialchars($event['title']) : 'New Event';
                $eventLink = !empty($event['url']) ? htmlspecialchars($event['url']) : '#';
                $hasEvents = true;
                ?>
                <article>
                    <figure class="date">
                        <i class="fa fa-calendar"></i><?php echo $eventDate; ?>
                    </figure>
                    <header>
                        <a href="<?php echo $eventLink; ?>"><?php echo $eventTitle; ?></a>
                    </header>
                </article>
                <!-- /article -->
                <?php
            }
        }
        $stmt->close();
    }

    if (!$hasEvents) {
        ?>
        <article>
            <figure class="date">
                <i class="fa fa-calendar"></i><?php echo date('m-d-Y'); ?>
            </figure>
            <header>
                <a href="#">No upcoming events scheduled</a>
            </header>
        </article>
        <?php
    }
} catch (Exception $e) {
    error_log('Events query error: ' . $e->getMessage());
    ?>
    <article>
        <figure class="date">
            <i class="fa fa-calendar"><?php echo date('m-d-Y'); ?></i>
        </figure>
        <header>
            <a href="#">Error loading events</a>
        </header>
    </article>
    <?php
}
?>
