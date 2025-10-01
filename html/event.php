<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set page title
$page_title = "All Events";

// Include header
include_once 'includes/header.php';

// Include database connection
require_once __DIR__ . '/../includes/database.php';
?>

<!-- Page Content -->
<div id="page-content">
    <!-- Breadcrumb -->
    <div class="block">
        <div class="container">
            <div class="block-breadcrumb">
                <a href="index.php">Home</a>
                <span>Events</span>
            </div>
        </div>
    </div>
    <!-- end Breadcrumb -->

    <div class="block">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <header class="page-title">
                        <h1>All Events</h1>
                    </header>
                    
                    <div class="events-list">
                        <?php
                        try {
                            // Get all upcoming and ongoing events
                            $sql = "SELECT id, title, description, start_date, end_date, location, status, image_url 
                                    FROM events 
                                    WHERE status IN ('upcoming', 'ongoing')
                                    ORDER BY start_date ASC";
                            
                            $result = $conn->query($sql);
                            
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    // Process event data
                                    $start_date = new DateTime($row['start_date']);
                                    $end_date = !empty($row['end_date']) ? new DateTime($row['end_date']) : null;
                                    $now = new DateTime();
                                    
                                    // Format dates
                                    $formatted_date = $start_date->format('F j, Y');
                                    $formatted_time = $start_date->format('g:i A');
                                    $end_time = $end_date ? $end_date->format('g:i A') : '';
                                    
                                    // Check if event is ongoing
                                    $is_ongoing = ($start_date <= $now && ($end_date === null || $end_date >= $now));
                                    $status_class = $is_ongoing ? 'ongoing' : '';
                                    $status_text = $is_ongoing ? 'Ongoing' : '';
                                    
                                    // Handle image URL
                                    $image_url = '';
                                    if (!empty($row['image_url'])) {
                                        $image_url = (strpos($row['image_url'], 'http') === 0) ? 
                                            $row['image_url'] : 
                                            rtrim(BASE_URL, '/') . '/' . ltrim($row['image_url'], '/');
                                    } else {
                                        // Fallback to a placeholder image if no image is set
                                        $image_url = rtrim(BASE_URL, '/') . '/assets/img/event-placeholder.jpg';
                                    }
                                    
                                    // Set article style
                                    $article_style = "border-left: 4px solid #006400; 
                                        padding: 15px 20px; 
                                        margin: 0 0 20px 0; 
                                        border-radius: 0 6px 6px 0;
                                        box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
                                        background-color: " . ($is_ongoing ? '#e8f5e9' : '#f8f9fa') . ";
                                        max-width: 100%;
                                        overflow: hidden;";
                                    
                                    // Debugging
                                    error_log('Processing Event:');
                                    error_log('- ID: ' . $row['id']);
                                    error_log('- Title: ' . $row['title']);
                                    error_log('- Image URL: ' . $image_url);
                                    error_log('- Status: ' . $row['status']);
                                    error_log('- Start Date: ' . $row['start_date']);
                                    error_log('- Location: ' . ($row['location'] ?? 'Not specified'));
                                    
                                    if ($row['status'] === 'cancelled') {
                                        $status_class = 'cancelled';
                                        $status_text = 'Cancelled';
                                    }
                                    ?>
                                    <article class="event-item <?php echo htmlspecialchars($status_class); ?>" 
                                             style="<?php echo $article_style; ?>">
                                        <div class="row">
                                            <?php if (!empty($row['image_url'])): ?>
                                            <div class="col-md-3">
                                                <div class="event-image" style="width: 100%; height: 180px; overflow: hidden; border-radius: 4px; background-color: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                                                    <a href="event-detail.php?id=<?php echo $row['id']; ?>" style="display: block; width: 100%; height: 100%;">
                                                        <img src="<?php echo htmlspecialchars($image_url); ?>" 
                                                             style="width: 100%; height: 100%; object-fit: cover;"
                                                             alt="<?php echo htmlspecialchars($row['title']); ?>">
                                                    </a>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <div class="<?php echo !empty($row['image_url']) ? 'col-md-9' : 'col-md-12'; ?>">
                                                <?php if ($status_text): ?>
                                                    <span class="status-badge" style="float: right; padding: 3px 10px; border-radius: 12px; font-size: 0.8em; background-color: #006400; color: white; font-weight: 500; margin-bottom: 10px;">
                                                        <?php echo $status_text; ?>
                                                    </span>
                                                <?php endif; ?>
                                                
                                                <h3 class="event-title" style="color: #004d00; font-weight: 600; margin: 0 0 10px 0; font-size: 1.4em;">
                                                    <a href="event-detail.php?id=<?php echo $row['id']; ?>" style="color: #006400; text-decoration: none; transition: color 0.3s ease;">
                                                        <?php echo htmlspecialchars($row['title']); ?>
                                                    </a>
                                                </h3>
                                                
                                                <div class="event-date" style="color: #006400; font-size: 0.95em; margin-bottom: 8px; font-weight: 500; display: flex; align-items: center; flex-wrap: wrap; gap: 15px;">
                                                    <span><i class="fa fa-calendar"></i> <?php echo $formatted_date; ?></span>
                                                    <?php if (!empty($formatted_time)): ?>
                                                        <span><i class="fa fa-clock-o"></i> 
                                                            <?php 
                                                            echo $formatted_time;
                                                            if (!empty($end_time) && $end_time !== $formatted_time) {
                                                                echo ' - ' . $end_time;
                                                            }
                                                            ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($row['location'])): ?>
                                                        <span><i class="fa fa-map-marker"></i> <?php echo htmlspecialchars($row['location']); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <?php if (!empty($row['description'])): ?>
                                                    <div class="event-description" style="margin-bottom: 15px; color: #333;">
                                                        <?php 
                                                        $description = strip_tags($row['description']);
                                                        echo strlen($description) > 200 ? substr($description, 0, 200) . '...' : $description;
                                                        ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="event-actions">
                                                    <a href="event-detail.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary" style="background-color: #006400; border-color: #005500;">
                                                        View Details <i class="fa fa-arrow-right"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                    <?php
                                }
                            } else {
                                echo '<div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> No events found. Please check back later.
                                </div>';
                            }
                        } catch (Exception $e) {
                            error_log('Error fetching events: ' . $e->getMessage());
                            echo '<div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i> An error occurred while loading events. Please try again later.
                            </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once 'includes/footer.php';
?>
