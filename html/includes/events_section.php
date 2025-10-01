<?php
// Include the events retrieval script
require_once __DIR__ . '/../../includes/get_events.php';

// Get upcoming events
$events = getUpcomingEvents(5);
?>

<div class="col-md-4 col-sm-6">
    <section class="events" id="events">
        <header>
            <h2>Upcoming Events</h2>
        </header>
        <div class="section-content">
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): 
                    // Format date range
                    $start_date = date('M j, Y', strtotime($event['start_date']));
                    $end_date = !empty($event['end_date']) && $event['end_date'] != $event['start_date'] 
                        ? ' - ' . date('M j, Y', strtotime($event['end_date'])) 
                        : '';
                    $date_range = $start_date . $end_date;
                    
                    // Format time if available
                    $time = '';
                    if (!empty($event['start_time'])) {
                        $start_time = date('g:i A', strtotime($event['start_time']));
                        $end_time = !empty($event['end_time']) 
                            ? ' - ' . date('g:i A', strtotime($event['end_time'])) 
                            : '';
                        $time = $start_time . $end_time;
                    }
                ?>
                    <article class="event <?php echo $event['is_featured'] ? 'featured' : ''; ?> status-<?php echo $event['status_class']; ?>">
                        <div class="event-date">
                            <span class="day"><?php echo date('d', strtotime($event['start_date'])); ?></span>
                            <span class="month"><?php echo date('M', strtotime($event['start_date'])); ?></span>
                        </div>
                        <div class="event-content">
                            <h3 class="event-title" title="<?php echo htmlspecialchars($event['title']); ?>">
                                <a href="event-detail.php?id=<?php echo $event['id']; ?>">
                                    <?php echo htmlspecialchars($event['title']); ?>
                                </a>
                                <?php if ($event['is_featured']): ?>
                                    <span class="featured-badge">Featured</span>
                                <?php endif; ?>
                            </h3>
                            <div class="event-meta">
                                <span class="date"><?php echo $date_range; ?></span>
                                <?php if (!empty($time)): ?>
                                    <span class="time"><?php echo $time; ?></span>
                                <?php endif; ?>
                                <?php if (!empty($event['location'])): ?>
                                    <span class="location">
                                        <i class="fa fa-map-marker"></i> <?php echo htmlspecialchars($event['location']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-events">
                    <p>No upcoming events scheduled.</p>
                </div>
            <?php endif; ?>
        </div>
        <a href="events.php" class="view-all">View All Events</a>
    </section>
</div>

<style>
.events {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 15px;
    margin-bottom: 30px;
}

.events h2 {
    color: #2c3e50;
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
    font-size: 18px;
    font-weight: 600;
}

.event {
    display: flex;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
    height: 90px; /* Slightly taller than announcements to accommodate more info */
    overflow: hidden;
}

.event:last-child {
    border-bottom: none;
}

.event.featured {
    background-color: #f0f7ff;
    padding: 12px;
    border-radius: 4px;
    border-left: 3px solid #3498db;
}

.event-date {
    text-align: center;
    margin-right: 12px;
    min-width: 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.event-date .day {
    display: block;
    font-size: 20px;
    font-weight: bold;
    color: #2c3e50;
    line-height: 1;
}

.event-date .month {
    display: block;
    font-size: 11px;
    color: #7f8c8d;
    text-transform: uppercase;
}

.event-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    overflow: hidden;
}

.event-title {
    margin: 0 0 4px 0;
    font-size: 14px;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.event-title a {
    color: #2c3e50;
    text-decoration: none;
}

.event-title a:hover {
    color: #3498db;
}

.event-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    font-size: 11px;
    color: #7f8c8d;
    gap: 8px;
}

.event-meta .date,
.event-meta .time,
.event-meta .location {
    display: flex;
    align-items: center;
    white-space: nowrap;
}

.event-meta i {
    margin-right: 3px;
}

.featured-badge {
    background: #3498db;
    color: #fff;
    font-size: 9px;
    padding: 1px 4px;
    border-radius: 2px;
    margin-left: 6px;
    font-weight: bold;
    text-transform: uppercase;
    vertical-align: middle;
}

.view-all {
    display: inline-block;
    margin-top: 15px;
    color: #3498db;
    text-decoration: none;
    font-weight: 500;
    font-size: 13px;
}

.view-all:hover {
    text-decoration: underline;
}

/* Status indicators */
.status-upcoming .event-date .day,
.status-upcoming .event-date .month {
    color: #3498db;
}

.status-ongoing .event-date .day,
.status-ongoing .event-date .month {
    color: #2ecc71;
    font-weight: bold;
}

.status-past .event-date .day,
.status-past .event-date .month {
    color: #95a5a6;
}

/* Responsive */
@media (max-width: 768px) {
    .event {
        flex-direction: row;
        height: auto;
        min-height: 90px;
    }
    
    .event-date {
        margin-bottom: 0;
    }
}
</style>
