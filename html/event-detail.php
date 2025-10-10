<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/../includes/database.php';

// Set page title and description
$page_title = 'Event Details';
$page_description = 'View detailed information about the event';
$page_heading = 'Event Details';

// Include about header
include_once 'includes/about_header.php';

// Define base URL
$base_url = rtrim(str_replace('/c/zanvarsity', '', $_SERVER['REQUEST_URI']), '/');
$base_url = str_replace(basename($_SERVER['REQUEST_URI']), '', $base_url);
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/c/zanvarsity' . $base_url;

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

?>

<!-- Page Content -->
<div class="container py-5">
    <!-- Breadcrumb -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="event.php">Events</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($event['title']); ?></li>
            </ol>
        </nav>
    </div>
    
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
                                    <?php 
                                $main_image = $gallery_images[0]['image_url'];
                                
                                // Initialize debug info
                                $debug_info = [];
                                $debug_info[] = "Original path: " . $main_image;
                                
                                // Define base paths
                                $base_path = '/c/zanvarsity';
                                $uploads_path = $base_path . '/uploads/events/';
                                $assets_path = $base_path . '/assets/images/';
                                
                                // If it's already a full URL, use it as is
                                if (strpos($main_image, 'http') === 0) {
                                    $debug_info[] = "Using full URL as is";
                                } 
                                // Handle relative paths
                                else {
                                    // Remove any protocol and domain if present
                                    $main_image = preg_replace('|^https?://[^/]+|', '', $main_image);
                                    $debug_info[] = "After removing protocol: " . $main_image;
                                    
                                    // Normalize slashes
                                    $main_image = str_replace('\\', '/', $main_image);
                                    $main_image = ltrim($main_image, '/');
                                    
                                    // Check if file exists in the uploads directory first
                                    $filename = basename($main_image);
                                    $possible_paths = [
                                        $uploads_path . $filename,
                                        $base_path . '/' . $main_image,
                                        $assets_path . 'placeholder.jpg'
                                    ];
                                    
                                    $found = false;
                                    foreach ($possible_paths as $path) {
                                        $full_path = $_SERVER['DOCUMENT_ROOT'] . $path;
                                        $debug_info[] = "Checking path: " . $full_path;
                                        
                                        if (file_exists($full_path)) {
                                            $main_image = $path;
                                            $debug_info[] = "Found image at: " . $path;
                                            $found = true;
                                            break;
                                        }
                                    }
                                    
                                    if (!$found) {
                                        $debug_info[] = "Image not found, using placeholder";
                                        $main_image = $assets_path . 'placeholder.jpg';
                                        
                                        // Create placeholder if it doesn't exist
                                        $placeholder_path = $_SERVER['DOCUMENT_ROOT'] . $main_image;
                                        if (!file_exists($placeholder_path)) {
                                            // Create a simple placeholder image
                                            $placeholder = imagecreatetruecolor(800, 600);
                                            $bg_color = imagecolorallocate($placeholder, 240, 240, 240);
                                            $text_color = imagecolorallocate($placeholder, 150, 150, 150);
                                            
                                            imagefill($placeholder, 0, 0, $bg_color);
                                            $text = 'Image Not Found';
                                            $font_size = 5;
                                            $text_width = imagefontwidth($font_size) * strlen($text);
                                            $text_height = imagefontheight($font_size);
                                            $x = (800 - $text_width) / 2;
                                            $y = (600 - $text_height) / 2;
                                            
                                            imagestring($placeholder, $font_size, $x, $y, $text, $text_color);
                                            
                                            // Create directories if they don't exist
                                            if (!is_dir(dirname($placeholder_path))) {
                                                mkdir(dirname($placeholder_path), 0755, true);
                                            }
                                            
                                            // Save the placeholder image
                                            imagejpeg($placeholder, $placeholder_path, 80);
                                            imagedestroy($placeholder);
                                            $debug_info[] = "Created placeholder image at: " . $placeholder_path;
                                        }
                                    }
                                }
                                
                                // Output debug info if debug mode is on
                                if (isset($_GET['debug'])) {
                                    echo '<div class="alert alert-warning"><pre>' . implode("\n", $debug_info) . '</pre></div>';
                                }
                                ?>
                                <?php 
                                // Ensure the image URL is properly encoded
                                $main_image_src = htmlspecialchars($main_image);
                                $main_image_alt = htmlspecialchars($event['title']);
                                $main_image_title = htmlspecialchars($gallery_images[0]['caption'] ?? $event['title']);
                                ?>
                                <?php
                                // Ensure the image path is properly formatted
                                $main_image_src = $main_image;
                                
                                // If it's a relative path, make sure it starts with /
                                if (strpos($main_image_src, '/') !== 0 && strpos($main_image_src, 'http') !== 0) {
                                    $main_image_src = '/' . $main_image_src;
                                }
                                
                                // Make sure the path doesn't contain double slashes
                                $main_image_src = preg_replace('#/+#', '/', $main_image_src);
                                
                                // If the image is in the uploads directory but the path is incorrect
                                if (strpos($main_image_src, '/uploads/') !== false && !file_exists($_SERVER['DOCUMENT_ROOT'] . $main_image_src)) {
                                    $filename = basename($main_image_src);
                                    $correct_path = '/c/zanvarsity/uploads/events/' . $filename;
                                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $correct_path)) {
                                        $main_image_src = $correct_path;
                                    }
                                }
                                
                                // Add timestamp to prevent caching issues
                                $main_image_src .= (strpos($main_image_src, '?') === false ? '?' : '&') . 't=' . time();
                                ?>
                                <img id="main-gallery-image" 
                                     src="<?php echo htmlspecialchars($main_image_src); ?>" 
                                     alt="<?php echo htmlspecialchars($main_image_alt); ?>" 
                                     title="<?php echo htmlspecialchars($main_image_title); ?>"
                                     class="img-fluid rounded shadow"
                                     style="width: 100%; max-height: 500px; object-fit: cover; cursor: pointer;"
                                     onerror="this.onerror=null; this.src='/c/zanvarsity/assets/images/placeholder.jpg?t=<?php echo time(); ?>';"
                                     onclick="openLightbox('<?php echo $main_image_src; ?>', '<?php echo $main_image_title; ?>')">
                                </div>
                                
                                <?php if (count($gallery_images) > 1): ?>
                                <div class="gallery-thumbnails d-flex flex-wrap gap-2 mt-3">
                                    <?php foreach ($gallery_images as $index => $image): 
                                        $thumb_image = $image['image_url'];
                                        
                                        // If it's already a full URL, use it as is
                                        if (strpos($thumb_image, 'http') === 0) {
                                            // Do nothing, use as is
                                        } 
                                        // Handle relative paths
                                        else {
                                            // Remove any protocol and domain if present
                                            $thumb_image = preg_replace('|^https?://[^/]+|', '', $thumb_image);
                                            
                                            // Remove any duplicate /c/zanvarsity/ segments
                                            $thumb_image = preg_replace('|(/c/zanvarsity)+|', '/c/zanvarsity', $thumb_image);
                                            
                                            // If it doesn't start with /c/zanvarsity, add it
                                            if (strpos($thumb_image, '/c/zanvarsity') !== 0) {
                                                $thumb_image = '/c/zanvarsity' . (strpos($thumb_image, '/') === 0 ? '' : '/') . ltrim($thumb_image, '/');
                                            }
                                            
                                            // Check if file exists, if not try with just the filename in uploads/events/
                                            $image_path = $_SERVER['DOCUMENT_ROOT'] . $thumb_image;
                                            if (!file_exists($image_path)) {
                                                $filename = basename($thumb_image);
                                                $alt_path = $_SERVER['DOCUMENT_ROOT'] . '/c/zanvarsity/uploads/events/' . $filename;
                                                if (file_exists($alt_path)) {
                                                    $thumb_image = '/c/zanvarsity/uploads/events/' . $filename;
                                                }
                                            }
                                        }
                                    ?>
                                        <div class="thumbnail position-relative" 
                                             style="width: 80px; height: 60px; overflow: hidden; border-radius: 4px; border: 2px solid #dee2e6; cursor: pointer; transition: all 0.3s ease;"
                                             onmouseover="this.style.borderColor='#006400'" 
                                             onmouseout="this.style.borderColor='#dee2e6'"
                                             onclick="changeMainImage('<?php echo $index; ?>')">
                                            <img src="<?php echo htmlspecialchars($thumb_image); ?>" 
                                                 alt="<?php echo htmlspecialchars($image['caption'] ?? 'Gallery image ' . ($index + 1)); ?>"
                                                 class="w-100 h-100"
                                                 style="object-fit: cover;">
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
                    <!-- Sidebar content here -->
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
        </div> <!-- End of col-md-8 -->
        
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Event Details</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-calendar-alt text-primary me-2 mt-1"></i>
                                <div>
                                    <h6 class="mb-0">Date & Time</h6>
                                    <p class="mb-0">
                                        <?php 
                                        echo $start_date->format('F j, Y');
                                        if ($end_date && $end_date->format('Y-m-d') !== $start_date->format('Y-m-d')) {
                                            echo ' - ' . $end_date->format('F j, Y');
                                        }
                                        ?>
                                        <br>
                                        <?php 
                                        echo $start_date->format('g:i A');
                                        if ($end_date) {
                                            echo ' - ' . $end_date->format('g:i A');
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </li>
                        <?php if (!empty($event['location'])): ?>
                        <li class="mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-map-marker-alt text-primary me-2 mt-1"></i>
                                <div>
                                    <h6 class="mb-0">Location</h6>
                                    <p class="mb-0"><?php echo htmlspecialchars($event['location']); ?></p>
                                </div>
                            </div>
                        </li>
                        <?php endif; ?>
                        <?php if (!empty($event['registration_link'])): ?>
                        <li class="text-center mt-4">
                            <a href="<?php echo htmlspecialchars($event['registration_link']); ?>" 
                               class="btn btn-primary w-100" 
                               target="_blank">
                                <i class="fas fa-user-plus me-2"></i>Register Now
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            
            <?php if (!empty($event['map_embed_code'])): ?>
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Location Map</h5>
                </div>
                <div class="card-body p-0">
                    <div class="ratio ratio-16x9">
                        <?php echo $event['map_embed_code']; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div> <!-- End of col-md-4 -->
    </div> <!-- End of row -->
</div> <!-- End of container -->

<!-- Lightbox Modal -->
<div class="modal fade" id="imageLightbox" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lightboxTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="lightboxImage" src="" class="img-fluid" alt="">
            </div>
        </div>
    </div>
</div>

<script>
// Function to open lightbox with image
function openLightbox(imageSrc, title) {
    const lightbox = new bootstrap.Modal(document.getElementById('imageLightbox'));
    const lightboxImg = document.getElementById('lightboxImage');
    const lightboxTitle = document.getElementById('lightboxTitle');
    
    lightboxImg.src = imageSrc;
    lightboxImg.alt = title;
    lightboxTitle.textContent = title || '';
    
    lightbox.show();
}

// Function to change main image when thumbnail is clicked
function changeMainImage(index) {
    const gallery = <?php echo json_encode($gallery_images); ?>;
    const mainImg = document.getElementById('main-gallery-image');
    
    if (gallery[index]) {
        let imgSrc = gallery[index].image_url;
        if (imgSrc.indexOf('http') !== 0) {
            imgSrc = '/c/zanvarsity/' + imgSrc.replace(/^\//, '');
        }
        mainImg.src = imgSrc;
        mainImg.alt = gallery[index].caption || gallery[index].title || 'Event Image';
        
        // Update onclick to open lightbox with the new image
        mainImg.onclick = function() {
            openLightbox(imgSrc, gallery[index].caption || gallery[index].title || '');
        };
    }
}
</script>

<?php
// Include about footer
include_once 'includes/about_footer.php';
?>
