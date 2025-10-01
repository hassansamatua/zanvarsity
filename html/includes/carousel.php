<?php
// Include database connection
require_once __DIR__ . '/../../includes/database.php';

// Function to get active carousel items
function getCarouselItems($conn) {
    try {
        $query = "SELECT * FROM carousel WHERE status = 'active' ORDER BY display_order ASC";
        $result = $conn->query($query);
        
        $items = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        return $items;
    } catch (Exception $e) {
        error_log("Error in getCarouselItems: " . $e->getMessage());
        return [];
    }
}

// Get database connection
$conn = getDbConnection();

// Get carousel items
$carouselItems = getCarouselItems($conn);
?>

<!-- Carousel Section -->
<div class="carousel-container">
    <div id="imageCarousel" class="carousel slide" data-ride="carousel">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            <?php foreach ($carouselItems as $index => $item): ?>
                <li data-target="#imageCarousel" data-slide-to="<?php echo $index; ?>" <?php echo $index === 0 ? 'class="active"' : ''; ?>></li>
            <?php endforeach; ?>
        </ol>

        <!-- Wrapper for slides -->
        <div class="carousel-inner">
            <?php foreach ($carouselItems as $index => $item): ?>
                <div class="item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    <?php if (!empty($item['title']) || !empty($item['description'])): ?>
                        <div class="carousel-caption">
                            <?php if (!empty($item['title'])): ?>
                                <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                            <?php endif; ?>
                            <?php if (!empty($item['description'])): ?>
                                <p><?php echo htmlspecialchars($item['description']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Controls -->
        <a class="left carousel-control" href="#imageCarousel" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#imageCarousel" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</div>

<!-- Add some basic styling -->
<style>
.carousel-container {
    margin: 20px auto;
    max-width: 1200px;
    width: 100%;
}
.carousel-inner > .item > img {
    width: 100%;
    height: auto;
}
.carousel-caption {
    background-color: rgba(0, 0, 0, 0.5);
    padding: 10px;
    border-radius: 5px;
}
.carousel-caption h3, .carousel-caption p {
    color: #fff;
}
</style>

<!-- Add jQuery and Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<script>
// Initialize carousel
$(document).ready(function(){
    $('.carousel').carousel({
        interval: 5000, // Change slide every 5 seconds
        pause: 'hover', // Pause on hover
        wrap: true // Continuously cycle through slides
    });
});
</script>
