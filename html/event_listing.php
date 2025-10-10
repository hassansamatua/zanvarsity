<?php
$page_title = 'Upcoming Events';
$page_description = 'View our upcoming events and activities at Zanvarsity University';
include_once 'includes/header.php';

// Get events from the API endpoint
$eventsJson = file_get_contents('http://' . $_SERVER['HTTP_HOST'] . '/c/zanvarsity/html/events.php');
$events = json_decode($eventsJson, true);

// Function to format date
function formatEventDate($dateString) {
    $date = new DateTime($dateString);
    return $date->format('F j, Y');
}
?>

<div class="container py-5">
    <h1 class="text-center mb-5">Upcoming Events</h1>
    
    <div class="row">
        <?php 
        if (!empty($events)) {
            foreach ($events as $event): 
                $eventDate = new DateTime($event['start']);
                $today = new DateTime();
                $isPastEvent = $eventDate < $today;
        ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 <?php echo $isPastEvent ? 'bg-light' : 'border-primary'; ?>">
                    <div class="card-header <?php echo $isPastEvent ? 'bg-secondary text-white' : 'bg-primary text-white'; ?>">
                        <h5 class="card-title mb-0"><?php echo htmlspecialchars($event['title']); ?></h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <i class="far fa-calendar-alt me-2"></i> 
                            <?php echo formatEventDate($event['start']); ?>
                        </p>
                        <?php if (!$isPastEvent): ?>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="<?php echo htmlspecialchars($event['url']); ?>" class="btn btn-primary btn-sm">
                                    View Details
                                </a>
                                <?php 
                                $daysUntil = $today->diff($eventDate)->days;
                                if ($daysUntil === 0) {
                                    echo '<span class="badge bg-warning text-dark">Today</span>';
                                } elseif ($daysUntil === 1) {
                                    echo '<span class="badge bg-info text-dark">Tomorrow</span>';
                                } elseif ($daysUntil <= 7) {
                                    echo '<span class="badge bg-success">In ' . $daysUntil . ' days</span>';
                                }
                                ?>
                            </div>
                        <?php else: ?>
                            <span class="badge bg-secondary">Past Event</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php 
            endforeach; 
        } else {
            echo '<div class="col-12 text-center"><p>No upcoming events at the moment. Please check back later.</p></div>';
        }
        ?>
    </div>
</div>

<style>
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .card-text {
        flex-grow: 1;
    }
    .badge {
        font-size: 0.8rem;
        padding: 0.35em 0.65em;
    }
</style>

<?php include_once 'includes/footer.php'; ?>
