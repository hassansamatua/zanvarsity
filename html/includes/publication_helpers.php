<?php
/**
 * Helper functions for displaying publications
 */

require_once __DIR__ . '/publication_manager.php';

/**
 * Display a grid of publications
 * 
 * @param int $limit Number of publications to show
 * @param bool $featuredOnly Whether to show only featured publications
 */
function displayPublications($limit = 6, $featuredOnly = false) {
    $publicationManager = new PublicationManager();
    $publications = $publicationManager->getPublications($limit, $featuredOnly);
    
    if (empty($publications)) {
        echo '<div class="alert alert-info">No publications found.</div>';
        return;
    }
    
    echo '<div class="row publications-grid">';
    
    foreach ($publications as $pub) {
        echo '<div class="col-md-4 mb-4">';
        echo '  <div class="card h-100 publication-card">';
        
        // Publication image
        if (!empty($pub['image_url'])) {
            echo '    <img src="' . htmlspecialchars($pub['image_url']) . '" ';
            echo '         class="card-img-top" ';
            echo '         alt="' . htmlspecialchars($pub['title']) . '" ';
            echo '         style="height: 200px; object-fit: cover;">';
        } else {
            // Default placeholder image
            echo '    <div class="bg-light text-center py-5" style="height: 200px;">';
            echo '        <i class="fas fa-newspaper fa-4x text-muted mt-4"></i>';
            echo '    </div>';
        }
        
        // Publication content
        echo '    <div class="card-body d-flex flex-column">';
        echo '        <h5 class="card-title">' . htmlspecialchars($pub['title']) . '</h5>';
        
        // Meta information
        echo '        <div class="text-muted small mb-3">';
        echo '            <span class="me-3"><i class="fas fa-user me-1"></i> ' . htmlspecialchars($pub['author']) . '</span>';
        echo '            <span><i class="far fa-calendar-alt me-1"></i> ' . date('M j, Y', strtotime($pub['publication_date'])) . '</span>';
        echo '        </div>';
        
        // Description (truncated)
        $description = strip_tags($pub['description']);
        if (strlen($description) > 150) {
            $description = substr($description, 0, 150) . '...';
        }
        echo '        <p class="card-text flex-grow-1">' . htmlspecialchars($description) . '</p>';
        
        // Action buttons
        echo '        <div class="mt-auto">';
        if (!empty($pub['document_url'])) {
            echo '    <a href="' . htmlspecialchars($pub['document_url']) . '" ';
            echo '       class="btn btn-primary btn-sm me-2" ';
            echo '       target="_blank" ';
            echo '       title="Download document">';
            echo '        <i class="fas fa-download me-1"></i> Download';
            echo '    </a>';
        }
        
        echo '            <a href="publication.php?id=' . (int)$pub['id'] . '" ';
        echo '               class="btn btn-outline-primary btn-sm" ';
        echo '               title="View details">';
        echo '                Read More <i class="fas fa-arrow-right ms-1"></i>';
        echo '            </a>';
        echo '        </div>'; // End action buttons
        
        echo '    </div>'; // End card-body
        echo '  </div>'; // End card
        echo '</div>'; // End col
    }
    
    echo '</div>'; // End row
    
    // Show view all button if there are more publications
    $viewAllUrl = $featuredOnly ? 'publications.php' : 'publications.php?all=1';
    echo '<div class="text-center mt-4">';
    echo '    <a href="' . $viewAllUrl . '" class="btn btn-outline-primary">';
    echo '        ' . ($featuredOnly ? 'View All Publications' : 'View More Publications');
    echo '        <i class="fas fa-arrow-right ms-2"></i>';
    echo '    </a>';
    echo '</div>';
}

/**
 * Display a single publication
 * 
 * @param int $id Publication ID
 */
function displayPublication($id) {
    $publicationManager = new PublicationManager();
    $publication = $publicationManager->getPublicationById($id);
    
    if (!$publication) {
        echo '<div class="alert alert-danger">Publication not found.</div>';
        return;
    }
    
    echo '<article class="publication-detail">';
    
    // Publication header
    echo '<header class="mb-4">';
    echo '    <h1 class="display-5 fw-bold">' . htmlspecialchars($publication['title']) . '</h1>';
    echo '    <div class="text-muted mb-3">';
    echo '        <span class="me-3"><i class="fas fa-user me-1"></i> ' . htmlspecialchars($publication['author']) . '</span>';
    echo '        <span><i class="far fa-calendar-alt me-1"></i> ' . date('F j, Y', strtotime($publication['publication_date'])) . '</span>';
    echo '    </div>';
    echo '</header>';
    
    // Publication image
    if (!empty($publication['image_url'])) {
        echo '<figure class="figure mb-4">';
        echo '    <img src="' . htmlspecialchars($publication['image_url']) . '" ';
        echo '         class="figure-img img-fluid rounded" ';
        echo '         alt="' . htmlspecialchars($publication['title']) . '">';
        echo '</figure>';
    }
    
    // Publication content
    echo '<div class="publication-content mb-4">';
    echo nl2br(htmlspecialchars($publication['description']));
    echo '</div>';
    
    // Document download
    if (!empty($publication['document_url'])) {
        echo '<div class="card mb-4">';
        echo '    <div class="card-body">';
        echo '        <h5 class="card-title">Download Publication</h5>';
        echo '        <p class="card-text">Download the full publication document.</p>';
        echo '        <a href="' . htmlspecialchars($publication['document_url']) . '" ';
        echo '           class="btn btn-primary" ';
        echo '           target="_blank">';
        echo '            <i class="fas fa-download me-2"></i> Download Document';
        echo '        </a>';
        echo '    </div>';
        echo '</div>';
    }
    
    // Back to publications link
    echo '<div class="mt-4">';
    echo '    <a href="publications.php" class="btn btn-outline-secondary">';
    echo '        <i class="fas fa-arrow-left me-2"></i> Back to Publications';
    echo '    </a>';
    echo '</div>';
    
    echo '</article>';
}
?>
