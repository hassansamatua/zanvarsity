<?php
// Include the events retrieval script
require_once __DIR__ . '/../../includes/get_events.php';

// Get upcoming events (limit to 3 for the sidebar)
$events = getUpcomingEvents(3);
?>

<section class="news-small" id="upcoming-events">
    <header>
        <h2>Upcoming Events</h2>
    </header>
    <div class="section-content">
        <?php if (!empty($events)): ?>
            <?php foreach ($events as $event): 
                $event_date = date('m-d-Y', strtotime($event['start_date']));
                $event_title = htmlspecialchars($event['title']);
            ?>
                <article>
                    <figure class="date">
                        <i class="fa fa-calendar"></i><?php echo $event_date; ?>
                    </figure>
                    <header>
                        <a href="event-detail.php?id=<?php echo $event['id']; ?>">
                            <?php echo $event_title; ?>
                        </a>
                    </header>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Fallback content if no events are found -->
            <article>
                <figure class="date">
                    <i class="fa fa-calendar"></i>08-24-2014
                </figure>
                <header>
                    <a href="#">U-M School of Public Health, Detroit partners aim to improve air quality in the city</a>
                </header>
            </article>
            <article>
                <figure class="date">
                    <i class="fa fa-calendar"></i>08-24-2014
                </figure>
                <header>
                    <a href="#">At 50, Center for the Education of Women celebrates a wider mission</a>
                </header>
            </article>
            <article>
                <figure class="date">
                    <i class="fa fa-calendar"></i>08-24-2014
                </figure>
                <header>
                    <a href="#">Three U-Michigan scientists receive Sloan fellowships</a>
                </header>
            </article>
        <?php endif; ?>
    </div>
    <!-- /.section-content -->
    <a href="events.php" class="read-more stick-to-bottom">All Events</a>
</section>
<!-- /.news-small -->
