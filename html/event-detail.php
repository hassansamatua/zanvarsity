<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/../includes/database.php';

// Check if event ID is provided
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($event_id <= 0) {
    header('Location: event.php');
    exit();
}

// Initialize variables
$event = [];
$gallery_images = [];

// Start transaction
$conn->begin_transaction();

try {
    // Get event details
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->bind_param('i', $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
    $stmt->close();
    
    if ($event) {
        // Initialize empty gallery images array
        $gallery_images = [];
        
        // First check if event_galleries table exists
        $table_check = $conn->query("SHOW TABLES LIKE 'event_galleries'");
        
        if ($table_check && $table_check->num_rows > 0) {
            // Table exists, try to get gallery images
            $stmt = $conn->prepare("SELECT * FROM event_galleries WHERE event_id = ? ORDER BY is_primary DESC, created_at ASC");
            if ($stmt) {
                $stmt->bind_param('i', $event_id);
                if ($stmt->execute()) {
                    $gallery_result = $stmt->get_result();
                    $gallery_images = $gallery_result->fetch_all(MYSQLI_ASSOC);
                }
                $stmt->close();
            }
        }
        
        // Debug: Check image paths
        $debug_info = [
            'event_id' => $event_id,
            'event_has_image' => !empty($event['image_url']),
            'gallery_images_count' => count($gallery_images),
            'event_image_url' => $event['image_url'] ?? 'Not set',
            'gallery_images' => $gallery_images
        ];
        
        // Process gallery images to ensure proper URLs
        foreach ($gallery_images as &$img) {
            if (strpos($img['image_url'], 'http') !== 0) {
                $img['image_url'] = (strpos($img['image_url'], '/') === 0) ? 
                    rtrim(BASE_URL, '/') . $img['image_url'] : 
                    rtrim(BASE_URL, '/') . '/' . ltrim($img['image_url'], '/');
            }
        }
        unset($img); // Break the reference
        
        // If no gallery images but event has an image_url, use it as primary
        if (empty($gallery_images) && !empty($event['image_url'])) {
            // Ensure the image URL is properly formatted
            $image_url = $event['image_url'];
            
            // Convert relative URLs to absolute
            if (strpos($image_url, 'http') !== 0) {
                $image_url = (strpos($image_url, '/') === 0) ? 
                    rtrim(BASE_URL, '/') . $image_url : 
                    rtrim(BASE_URL, '/') . '/' . ltrim($image_url, '/');
            }
            
            $gallery_images[] = [
                'id' => 0,
                'event_id' => $event_id,
                'image_url' => $image_url,
                'caption' => $event['title'],
                'is_primary' => 1
            ];
        }
    }
    
    $conn->commit();
    
    if (!$event) {
        // Event not found, redirect to events list
        header('Location: event.php?error=not_found');
        exit();
    }
    
    // Format dates
    $start_date = new DateTime($event['start_date']);
    $end_date = !empty($event['end_date']) ? new DateTime($event['end_date']) : null;
    $now = new DateTime();
    
    // Format dates and times
    $formatted_start_date = $start_date->format('F j, Y');
    $formatted_start_time = $start_date->format('g:i A');
    $formatted_end_date = $end_date ? $end_date->format('F j, Y') : '';
    $formatted_end_time = $end_date ? $end_date->format('g:i A') : '';
    
    // Check if event is ongoing
    $is_ongoing = ($start_date <= $now && ($end_date === null || $end_date >= $now));
    $status_class = $is_ongoing ? 'ongoing' : '';
    $status_text = $is_ongoing ? 'Ongoing' : 'Upcoming';
    
    if ($event['status'] === 'cancelled') {
        $status_class = 'cancelled';
        $status_text = 'Cancelled';
    } elseif ($end_date && $now > $end_date) {
        $status_class = 'completed';
        $status_text = 'Completed';
    }
    
    // Set page title
    $page_title = htmlspecialchars($event['title']);
    
} catch (Exception $e) {
    error_log('Error fetching event details: ' . $e->getMessage());
    header('Location: event.php?error=server_error');
    exit();
}

// Include header
include_once 'includes/header.php';
?>

<!-- Page Content -->
<div id="page-content">
    <!-- Breadcrumb -->
    <div class="block">
        <div class="container">
            <div class="block-breadcrumb">
                <a href="index.php">Home</a>
                <a href="event.php">Events</a>
                <span><?php echo htmlspecialchars($event['title']); ?></span>
            </div>
        </div>
    </div>
    <!-- end Breadcrumb -->

    <div class="block">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <article class="event-detail">
                        <!-- Debug Info -->
                        <?php if (isset($_GET['debug'])): ?>
                        <div class="alert alert-info">
                            <h4>Debug Information</h4>
                            <pre><?php echo htmlspecialchars(print_r($debug_info, true)); ?></pre>
                            <p>Current URL: <?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Main Event Image/Gallery -->
                        <div class="event-gallery" style="margin-bottom: 30px;">
                            <?php if (!empty($gallery_images)): ?>
                                <div class="main-image" style="margin-bottom: 15px; border-radius: 6px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                    <img id="main-gallery-image" 
                                         src="<?php echo htmlspecialchars($gallery_images[0]['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($event['title']); ?>" 
                                         style="width: 100%; max-height: 500px; object-fit: cover; cursor: pointer;"
                                         onclick="openLightbox('<?php echo htmlspecialchars($gallery_images[0]['image_url']); ?>', '<?php echo htmlspecialchars($gallery_images[0]['caption'] ?? $event['title']); ?>')">
                                </div>
                                
                                <?php if (count($gallery_images) > 1): ?>
                                <div class="gallery-thumbnails" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px;">
                                    <?php foreach ($gallery_images as $index => $image): ?>
                                        <div class="thumbnail" style="width: 80px; height: 60px; overflow: hidden; border-radius: 4px; border: 2px solid #ddd; cursor: pointer; transition: all 0.3s ease;"
                                             onmouseover="this.style.borderColor='#006400'" 
                                             onmouseout="this.style.borderColor='#ddd'"
                                             onclick="changeMainImage('<?php echo $index; ?>')">
                                            <img src="<?php echo htmlspecialchars($image['image_url']); ?>" 
                                                 alt="<?php echo htmlspecialchars($image['caption'] ?? 'Gallery image ' . ($index + 1)); ?>"
                                                 style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                                
                            <?php elseif (!empty($event['image_url'])): ?>
                                <div class="event-image" style="margin-bottom: 15px; border-radius: 6px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                    <img src="<?php echo htmlspecialchars($event['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($event['title']); ?>" 
                                         style="width: 100%; max-height: 500px; object-fit: cover;">
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <header class="event-header" style="margin-bottom: 30px;">
                            <h1 style="color: #006400; margin-bottom: 15px;">
                                <?php echo htmlspecialchars($event['title']); ?>
                                <?php if ($status_text): ?>
                                    <span class="status-badge" style="font-size: 0.5em; vertical-align: middle; margin-left: 10px; padding: 3px 12px; border-radius: 12px; background-color: #006400; color: white; font-weight: 500;">
                                        <?php echo $status_text; ?>
                                    </span>
                                <?php endif; ?>
                            </h1>
                            
                            <div class="event-meta" style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 20px; color: #555;">
                                <div class="meta-item" style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fa fa-calendar" style="color: #006400;"></i>
                                    <div>
                                        <div style="font-weight: 500; color: #333;">Date</div>
                                        <div><?php echo $formatted_start_date; ?>
                                            <?php if ($formatted_end_date && $formatted_end_date !== $formatted_start_date): ?>
                                                - <?php echo $formatted_end_date; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if (!empty($event['location'])): ?>
                                <div class="meta-item" style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fa fa-map-marker" style="color: #006400;"></i>
                                    <div>
                                        <div style="font-weight: 500; color: #333;">Location</div>
                                        <div><?php echo htmlspecialchars($event['location']); ?></div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($formatted_start_time)): ?>
                                <div class="meta-item" style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fa fa-clock-o" style="color: #006400;"></i>
                                    <div>
                                        <div style="font-weight: 500; color: #333;">Time</div>
                                        <div>
                                            <?php echo $formatted_start_time; ?>
                                            <?php if ($formatted_end_time && $formatted_end_time !== $formatted_start_time): ?>
                                                - <?php echo $formatted_end_time; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </header>
                        
                        <?php if (!empty($event['description'])): ?>
                        <div class="event-description" style="line-height: 1.8; color: #444; margin-bottom: 30px;">
                            <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Additional Gallery Section -->
                        <?php if (count($gallery_images) > 1): ?>
                        <div class="additional-gallery" style="margin-top: 50px;">
                            <h3 style="color: #006400; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e0e0e0;">
                                Event Gallery
                            </h3>
                            <div class="gallery-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                                <?php foreach ($gallery_images as $index => $image): ?>
                                    <div class="gallery-item" style="position: relative; border-radius: 6px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                        <img src="<?php echo htmlspecialchars($image['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($image['caption'] ?? 'Gallery image ' . ($index + 1)); ?>"
                                             style="width: 100%; height: 150px; object-fit: cover; cursor: pointer;"
                                             onclick="openLightbox('<?php echo htmlspecialchars($image['image_url']); ?>', '<?php echo htmlspecialchars($image['caption'] ?? $event['title']); ?>')">
                                        <?php if (!empty($image['caption'])): ?>
                                            <div class="gallery-caption" style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0, 0, 0, 0.7); color: white; padding: 8px; font-size: 0.85em;">
                                                <?php echo htmlspecialchars($image['caption']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Lightbox Modal -->
                        <div id="lightbox" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.9); z-index: 1000; align-items: center; justify-content: center;">
                            <div style="position: relative; max-width: 90%; max-height: 90%;">
                                <span id="close-lightbox" style="position: absolute; top: -40px; right: 0; color: white; font-size: 30px; cursor: pointer;">&times;</span>
                                <img id="lightbox-image" src="" alt="" style="max-width: 100%; max-height: 80vh; display: block; margin: 0 auto;">
                                <div id="lightbox-caption" style="color: white; text-align: center; margin-top: 10px;"></div>
                            </div>
                        </div>
                        
                        <script>
                        // Gallery functionality
                        const galleryImages = <?php echo json_encode($gallery_images); ?>;
                        
                        function changeMainImage(index) {
                            const mainImage = document.getElementById('main-gallery-image');
                            if (galleryImages[index]) {
                                mainImage.src = galleryImages[index].image_url;
                                mainImage.alt = galleryImages[index].caption || '';
                                mainImage.onclick = function() {
                                    openLightbox(galleryImages[index].image_url, galleryImages[index].caption || '');
                                };
                            }
                        }
                        
                        function openLightbox(src, caption) {
                            const lightbox = document.getElementById('lightbox');
                            const lightboxImg = document.getElementById('lightbox-image');
                            const lightboxCaption = document.getElementById('lightbox-caption');
                            
                            lightbox.style.display = 'flex';
                            lightboxImg.src = src;
                            lightboxCaption.textContent = caption || '';
                            document.body.style.overflow = 'hidden';
                        }
                        
                        // Close lightbox when clicking the close button or outside the image
                        document.getElementById('close-lightbox').onclick = closeLightbox;
                        document.getElementById('lightbox').onclick = function(e) {
                            if (e.target === this) {
                                closeLightbox();
                            }
                        };
                        
                        // Close with ESC key
                        document.addEventListener('keydown', function(e) {
                            if (e.key === 'Escape') {
                                closeLightbox();
                            }
                        });
                        
                        function closeLightbox() {
                            document.getElementById('lightbox').style.display = 'none';
                            document.body.style.overflow = 'auto';
                        }
                        </script>
                        
                        <div class="event-actions" style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee;">
                            <a href="event.php" class="btn btn-default" style="margin-right: 10px;">
                                <i class="fa fa-arrow-left"></i> Back to Events
                            </a>
                            <?php if ($is_ongoing): ?>
                                <a href="#" class="btn btn-primary" style="background-color: #006400; border-color: #005500;">
                                    <i class="fa fa-calendar-plus-o"></i> Add to Calendar
                                </a>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>
                
                <div class="col-md-4">
                    <div class="event-sidebar" style="background-color: #f9f9f9; border-radius: 6px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                        <h3 style="color: #006400; margin-top: 0; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e0e0e0;">
                            Event Details
                        </h3>
                        
                        <ul class="event-details-list" style="list-style: none; padding: 0; margin: 0;">
                            <li style="padding: 10px 0; border-bottom: 1px solid #eee; display: flex; align-items: flex-start;">
                                <i class="fa fa-calendar" style="color: #006400; margin-right: 10px; margin-top: 3px;"></i>
                                <div>
                                    <div style="font-weight: 500; color: #333;">Date</div>
                                    <div><?php echo $formatted_start_date; ?>
                                        <?php if ($formatted_end_date && $formatted_end_date !== $formatted_start_date): ?>
                                            - <?php echo $formatted_end_date; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </li>
                            
                            <?php if (!empty($formatted_start_time)): ?>
                            <li style="padding: 10px 0; border-bottom: 1px solid #eee; display: flex; align-items: flex-start;">
                                <i class="fa fa-clock-o" style="color: #006400; margin-right: 10px; margin-top: 3px;"></i>
                                <div>
                                    <div style="font-weight: 500; color: #333;">Time</div>
                                    <div>
                                        <?php echo $formatted_start_time; ?>
                                        <?php if ($formatted_end_time && $formatted_end_time !== $formatted_start_time): ?>
                                            - <?php echo $formatted_end_time; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </li>
                            <?php endif; ?>
                            
                            <?php if (!empty($event['location'])): ?>
                            <li style="padding: 10px 0; border-bottom: 1px solid #eee; display: flex; align-items: flex-start;">
                                <i class="fa fa-map-marker" style="color: #006400; margin-right: 10px; margin-top: 3px;"></i>
                                <div>
                                    <div style="font-weight: 500; color: #333;">Location</div>
                                    <div><?php echo htmlspecialchars($event['location']); ?></div>
                                </div>
                            </li>
                            <?php endif; ?>
                            
                            <li style="padding: 10px 0; display: flex; align-items: flex-start;">
                                <i class="fa fa-info-circle" style="color: #006400; margin-right: 10px; margin-top: 3px;"></i>
                                <div>
                                    <div style="font-weight: 500; color: #333;">Status</div>
                                    <div><?php echo $status_text; ?></div>
                                </div>
                            </li>
                        </ul>
                        
                        <?php if ($is_ongoing): ?>
                        <div class="event-cta" style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #eee;">
                            <a href="#" class="btn btn-primary btn-block" style="background-color: #006400; border-color: #005500; margin-bottom: 10px;">
                                <i class="fa fa-ticket"></i> Register Now
                            </a>
                            <a href="#" class="btn btn-default btn-block">
                                <i class="fa fa-share-alt"></i> Share Event
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($is_ongoing): ?>
                    <div class="event-map" style="margin-top: 30px; background-color: #f9f9f9; border-radius: 6px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                        <h3 style="color: #006400; margin-top: 0; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #e0e0e0;">
                            Location Map
                        </h3>
                        <div style="height: 200px; background-color: #eee; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                            <i class="fa fa-map-o" style="font-size: 40px; color: #999;"></i>
                        </div>
                        <p style="margin-top: 15px; font-size: 0.9em; color: #666; text-align: center;">
                            Map would be displayed here
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once 'includes/footer.php';
?>
