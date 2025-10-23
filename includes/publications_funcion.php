<?php
// File: includes/publications_functions.php

function fetchAndSavePublications() {
    global $conn;
    
    $apiUrl = "https://www.zumis.ac.tz/academic/data/api/getAllPublications";
    
    // Fetch from API
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER => ['Accept: application/json']
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        return ['success' => false, 'message' => 'Failed to fetch from API'];
    }
    
    $publications = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['success' => false, 'message' => 'Invalid API response'];
    }
    
    // Prepare statement
    $stmt = $conn->prepare("
        INSERT INTO publications 
        (title, author, description, publication_date, image_url, document_url, status_code, status_desc, api_id, created_at, updated_at)
        VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ON DUPLICATE KEY UPDATE
        title = VALUES(title),
        author = VALUES(author),
        description = VALUES(description),
        publication_date = VALUES(publication_date),
        image_url = VALUES(image_url),
        document_url = VALUES(document_url),
        updated_at = NOW()
    ");
    
    $count = 0;
    $conn->begin_transaction();
    
    try {
        foreach ($publications as $pub) {
            $data = [
                'title' => $pub['title'] ?? 'No title',
                'author' => $pub['author'] ?? 'Unknown',
                'description' => $pub['description'] ?? $pub['abstract'] ?? null,
                'publication_date' => !empty($pub['datePublished']) ? date('Y-m-d', strtotime($pub['datePublished'])) : date('Y-m-d'),
                'image_url' => $pub['imageUrl'] ?? $pub['thumbnailUrl'] ?? null,
                'document_url' => $pub['documentUrl'] ?? $pub['fileUrl'] ?? null,
                'status_code' => $pub['status'] ?? '200',
                'status_desc' => 'Imported from API',
                'api_id' => $pub['id'] ?? uniqid('pub_', true)
            ];
            
            $stmt->bind_param(
                'sssssssss',
                $data['title'],
                $data['author'],
                $data['description'],
                $data['publication_date'],
                $data['image_url'],
                $data['document_url'],
                $data['status_code'],
                $data['status_desc'],
                $data['api_id']
            );
            
            $stmt->execute();
            $count++;
        }
        
        $conn->commit();
        return ['success' => true, 'count' => $count];
        
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function displayPublications($limit = 6, $featuredOnly = false) {
    global $conn;
    
    $query = "SELECT * FROM publications 
              WHERE status_code = '200' " . 
              ($featuredOnly ? "AND is_featured = 1 " : "") .
              "ORDER BY publication_date DESC 
               LIMIT ?";
               
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo '<div class="publications-grid">';
        while ($pub = $result->fetch_assoc()) {
            echo '<div class="publication-card">';
            if (!empty($pub['image_url'])) {
                echo '<img src="' . htmlspecialchars($pub['image_url']) . '" alt="' . htmlspecialchars($pub['title']) . '" class="pub-image">';
            } else {
                echo '<div class="pub-image-placeholder"></div>';
            }
            echo '<div class="pub-content">';
            echo '<h3 class="pub-title">' . htmlspecialchars($pub['title']) . '</h3>';
            echo '<div class="pub-meta">';
            echo '<span class="pub-author"><i class="fas fa-user"></i> ' . htmlspecialchars($pub['author']) . '</span>';
            echo '<span class="pub-date"><i class="far fa-calendar"></i> ' . date('M j, Y', strtotime($pub['publication_date'])) . '</span>';
            echo '</div>'; // .pub-meta
            echo '<p class="pub-desc">' . 
                 htmlspecialchars(substr($pub['description'], 0, 150)) . 
                 (strlen($pub['description']) > 150 ? '...' : '') . '</p>';
            if (!empty($pub['document_url'])) {
                echo '<a href="' . htmlspecialchars($pub['document_url']) . '" class="pub-link" target="_blank">Read More <i class="fas fa-arrow-right"></i></a>';
            }
            echo '</div>'; // .pub-content
            echo '</div>'; // .publication-card
        }
        echo '</div>'; // .publications-grid
    } else {
        echo '<div class="no-publications">No publications found. <a href="javascript:location.reload()">Try again</a></div>';
    }
}