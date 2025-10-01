<?php
// Include the announcements retrieval script
require_once __DIR__ . '/../../includes/get_announcements.php';
?>

<section class="announcements" id="announcements">
    <div class="section-title">
        <h2>Announcements</h2>
    </div>
        <div class="section-content">
            <?php if (!empty($announcements)): ?>
                <?php foreach ($announcements as $announcement): 
                    // Format date range
                    $start_date = date('M j, Y', strtotime($announcement['start_date']));
                    $end_date = !empty($announcement['end_date']) ? ' - ' . date('M j, Y', strtotime($announcement['end_date'])) : '';
                    $date_range = $start_date . $end_date;
                    
                    // Truncate description if too long
                    $description = strlen($announcement['description']) > 100 
                        ? substr($announcement['description'], 0, 100) . '...' 
                        : $announcement['description'];
                ?>
                    <article class="announcement <?php echo $announcement['is_important'] ? 'important' : ''; ?>">
                        <figure class="date">
                            <div class="day"><?php echo date('d', strtotime($announcement['start_date'])); ?></div>
                            <div class="month"><?php echo date('M', strtotime($announcement['start_date'])); ?></div>
                        </figure>
                        <div class="announcement-content">
                            <h3 class="announcement-title">
                                <a href="announcement-detail.php?id=<?php echo $announcement['id']; ?>">
                                    <?php echo htmlspecialchars($announcement['title']); ?>
                                </a>
                                <?php if ($announcement['is_important']): ?>
                                    <span class="important-badge">Important</span>
                                <?php endif; ?>
                            </h3>
                            <div class="announcement-meta">
                                <span class="date"><i class="fa fa-calendar"></i> <?php echo $date_range; ?></span>
                                <span class="status status-<?php echo $announcement['status_class']; ?>">
                                    <?php echo ucfirst($announcement['status']); ?>
                                </span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-announcements">
                    <p>No announcements at the moment. Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>
        <div class="text-right">
            <a href="announcements.php" class="view-all">View All Announcements <i class="fa fa-arrow-right"></i></a>
        </div>
    </section>

<style>
.announcements {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 20px;
    margin-bottom: 30px;
}

.section-title {
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f0f0f0;
}

.section-title h2 {
    color: #2c3e50;
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.announcement {
    display: flex;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.3s ease;
}

.announcement:hover {
    background-color: #f9f9f9;
    padding-left: 5px;
}

.announcement:last-child {
    border-bottom: none;
}

.announcement.important {
    background-color: #fff8e1;
    padding: 15px;
    border-radius: 4px;
    border-left: 3px solid #ffc107;
}

figure.date {
    -webkit-font-smoothing: antialiased;
    font-family: "Montserrat", "Arial", sans-serif;
    font-size: 85%;
    display: block;
    margin: 0;
    color: #fff;
    float: left;
    overflow: hidden;
    height: 50px;
    width: 50px;
    background-color: #012951;
    text-align: center;
    border-radius: 4px;
    margin-right: 15px;
}

figure.date .day {
    display: block;
    font-size: 20px;
    font-weight: bold;
    line-height: 1.2;
    padding-top: 2px;
}

figure.date .month {
    display: block;
    font-size: 11px;
    text-transform: uppercase;
    line-height: 1;
    padding-bottom: 5px;
}

.announcement-content {
    flex: 1;
}

.announcement-title {
    margin: 0 0 5px 0;
    font-size: 16px;
}

.announcement-title a {
    color: #2c3e50;
    text-decoration: none;
}

.announcement-title a:hover {
    color: #3498db;
}

.announcement-meta {
    font-size: 12px;
    color: #7f8c8d;
    margin-bottom: 5px;
}

.announcement-meta .date {
    margin-right: 10px;
}

.announcement-meta .status {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 10px;
    text-transform: uppercase;
    color: white;
}

.announcement-description {
    margin: 5px 0 0 0;
    color: #555;
    font-size: 13px;
    line-height: 1.5;
}

.important-badge {
    background: #ffc107;
    color: #000;
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 3px;
    margin-left: 8px;
    font-weight: bold;
    text-transform: uppercase;
}

.view-all {
    display: inline-block;
    margin-top: 15px;
    color: #3498db;
    text-decoration: none;
    font-weight: 500;
    font-size: 13px;
    transition: all 0.3s ease;
}

.view-all i {
    margin-left: 5px;
    transition: transform 0.3s ease;
}

.view-all:hover i {
    transform: translateX(3px);
}

.view-all:hover {
    text-decoration: underline;
}

/* Status colors */
.status-active { background-color: #2ecc71; }
.status-pending { background-color: #f39c12; }
.status-expired { background-color: #95a5a6; }
.status-cancelled { background-color: #e74c3c; }

/* Responsive */
@media (max-width: 768px) {
    .announcement {
        flex-direction: column;
    }
    
    .announcement-date {
        margin-bottom: 10px;
        text-align: left;
    }
}
        .event-image img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }
        .no-events {
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 4px;
            text-align: center;
            color: #7f8c8d;
        }
    </style>
</div>
