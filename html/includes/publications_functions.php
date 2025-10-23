<?php
// File: includes/publications_functions.php
require_once __DIR__ . '/db_connect.php';

function fetchAndSavePublications() {
    $conn = getZanvarsityDbConnection();
    
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
        return ['success' => false, 'message' => 'Invalid API response: ' . json_last_error_msg()];
    }
    
    // First, log the API response for debugging
    error_log("API Response: " . print_r($publications, true));
    
    // Prepare statement with named parameters for PDO
    $sql = "
        INSERT INTO publications 
        (title, author, description, publication_date, status_code, status_desc, api_id, created_at, updated_at)
        VALUES 
        (:title, :author, :description, :publication_date, :status_code, :status_desc, :api_id, NOW(), NOW())
        ON DUPLICATE KEY UPDATE
        title = VALUES(title),
        author = VALUES(author),
        description = VALUES(description),
        publication_date = VALUES(publication_date),
        status_code = VALUES(status_code),
        status_desc = VALUES(status_desc),
        updated_at = NOW()
    ";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        return ['success' => false, 'message' => 'Prepare failed: ' . $conn->error];
    }
    
    $count = 0;
    
    try {
        // Start transaction
        $conn->beginTransaction();
        if (!is_array($publications)) {
            throw new Exception('Invalid publications data format: ' . gettype($publications));
        }
        
        foreach ($publications as $index => $pub) {
            // Skip if not an array or empty
            if (!is_array($pub)) {
                error_log("Skipping non-array publication at index $index");
                continue;
            }
            
            // Debug: Log the current publication data
            error_log("Processing publication: " . print_r($pub, true));
            
            // Handle different possible field names in the API response
            $title = '';
            if (isset($pub['title']) && is_string($pub['title'])) {
                $title = $pub['title'];
            } elseif (isset($pub['name'])) {
                $title = $pub['name'];
            } else {
                $title = 'Untitled Publication ' . ($index + 1);
            }
            
            $data = [
                'title' => $title,
                'author' => $pub['author'] ?? 'ZUMI Staff',
                'description' => $pub['description'] ?? $pub['abstract'] ?? 'No description available.',
                'publication_date' => !empty($pub['date']) ? date('Y-m-d', strtotime($pub['date'])) : date('Y-m-d'),
                'status_code' => '200',
                'status_desc' => 'Imported from ZUMI API',
                'api_id' => $pub['id'] ?? 'zumi_pub_' . ($index + 1)
            ];
            
            // Debug: Log the prepared data
            error_log("Prepared data: " . print_r($data, true));
            
            // Bind parameters using PDO's bindValue
            $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
            $stmt->bindValue(':author', $data['author'], PDO::PARAM_STR);
            $stmt->bindValue(':description', $data['description'], PDO::PARAM_STR);
            $stmt->bindValue(':publication_date', $data['publication_date'], PDO::PARAM_STR);
            $stmt->bindValue(':status_code', $data['status_code'], PDO::PARAM_STR);
            $stmt->bindValue(':status_desc', $data['status_desc'], PDO::PARAM_STR);
            $stmt->bindValue(':api_id', $data['api_id'], PDO::PARAM_STR);
            
            if (!$stmt->execute()) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception('Execute failed: ' . ($errorInfo[2] ?? 'Unknown error'));
            }
            $count++;
        }
        
        $conn->commit();
        return ['success' => true, 'count' => $count];
        
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        return ['success' => false, 'message' => $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine()];
    }
}

function displayPublications($limit = 6, $featuredOnly = false) {
    try {
        $conn = getZanvarsityDbConnection();
        
        $query = "SELECT * FROM publications 
                WHERE status_code = '200' " . 
                ($featuredOnly ? "AND is_featured = 1 " : "") .
                "ORDER BY publication_date DESC 
                LIMIT :limit";
                
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($publications) > 0) {
            echo '<div class="publications-grid">';
            foreach ($publications as $pub) {
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
    } catch (Exception $e) {
        error_log('Error in displayPublications: ' . $e->getMessage());
        echo '<div class="error">Error loading publications. Please try again later.</div>';
    }
}