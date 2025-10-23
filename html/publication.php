<?php
require_once 'includes/config.php';
require_once 'includes/db_connect.php';

// Set headers for JSON response
header('Content-Type: application/json');

// API endpoints
define('API_BASE_URL', 'https://www.zumis.ac.tz/academic/data/api/');
define('API_ENDPOINTS', [
    'all' => 'getAllPublications',
    'filtered' => 'getFilteredPublications'
]);

// Function to fetch publications from ZUMIS API
function fetchPublicationsFromAPI($endpoint = 'all') {
    $apiUrl = API_BASE_URL . (API_ENDPOINTS[$endpoint] ?? $endpoint);
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false, // For development only, use proper SSL in production
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'Cache-Control: no-cache',
            'User-Agent: Zanvarsity-Publications/1.0'
        ]
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if (!empty($error)) {
        return ['error' => 'API request failed: ' . $error];
    }
    
    if ($httpCode !== 200) {
        return ['error' => 'API request failed with HTTP code: ' . $httpCode];
    }
    
    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['error' => 'Failed to parse API response: ' . json_last_error_msg()];
    }
    
    if (!is_array($data) || empty($data)) {
        return ['error' => 'Empty or invalid API response'];
    }
    
    return $data;
}

// Function to save publications to database
function savePublicationsToDB($pdo, $publications) {
    if (!is_array($publications) || empty($publications)) {
        return 0;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO publications 
        (title, authors, abstract, publication_date, publication_category, link, 
         status_code, status_desc, api_id, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ON DUPLICATE KEY UPDATE
        title = VALUES(title),
        authors = VALUES(authors),
        abstract = VALUES(abstract),
        publication_date = VALUES(publication_date),
        publication_category = VALUES(publication_category),
        link = VALUES(link),
        status_code = VALUES(status_code),
        status_desc = VALUES(status_desc),
        updated_at = NOW()"
    );
    
    $count = 0;
    foreach ($publications as $pub) {
        // Skip if status is not success (if status code is available)
        if (isset($pub['StatusCode']) && $pub['StatusCode'] !== '200') {
            continue;
        }
        
        // Map ZUMIS API response fields to database fields
        $publicationDate = null;
        if (!empty($pub['datePublished'])) {
            $date = strtotime($pub['datePublished']);
            $publicationDate = $date ? date('Y-m-d', $date) : null;
        }
        
        $mappedPub = [
            'title' => $pub['title'] ?? 'No title',
            'authors' => $pub['Author'] ?? 'Unknown',
            'abstract' => $pub['description'] ?? $pub['abstract'] ?? null,
            'publication_date' => $publicationDate,
            'publication_category' => $pub['publicationCategory'] ?? 'Journal Articles',
            'link' => $pub['link'] ?? null,
            'status_code' => $pub['StatusCode'] ?? '200',
            'status_desc' => $pub['StatusDesc'] ?? 'Success',
            'api_id' => md5(($pub['title'] ?? '') . ($pub['Author'] ?? '') . ($pub['datePublished'] ?? ''))
        ];
        
        try {
            $stmt->execute([
                $mappedPub['title'],
                $mappedPub['authors'],
                $mappedPub['abstract'],
                $mappedPub['publication_date'],
                $mappedPub['publication_category'],
                $mappedPub['link'],
                $mappedPub['status_code'],
                $mappedPub['status_desc'],
                $mappedPub['api_id']
            ]);
            $count++;
        } catch (PDOException $e) {
            // Log error but continue with other records
            error_log("Error saving publication: " . $e->getMessage());
            continue;
        }
    }
    
    return $count;
}

// Function to get publications from database
function getPublicationsFromDB($pdo, $limit = 50, $offset = 0) {
    $stmt = $pdo->prepare(
        "SELECT * FROM publications 
        ORDER BY publication_date DESC 
        LIMIT :limit OFFSET :offset"
    );
    
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

try {
    // Get parameters
    $refresh = isset($_GET['refresh']) && $_GET['refresh'] == 1;
    $endpoint = isset($_GET['endpoint']) && in_array($_GET['endpoint'], ['all', 'filtered']) 
        ? $_GET['endpoint'] 
        : 'all';
    
    $publications = [];
    $message = '';
    
    if ($refresh) {
        // Fetch from API
        $apiData = fetchPublicationsFromAPI($endpoint);
        
        if (isset($apiData['error'])) {
            throw new Exception($apiData['error']);
        }
        
        // Save to database
        $count = savePublicationsToDB($pdo, $apiData);
        $publications = $apiData;
        $message = "Successfully fetched and saved $count publications from $endpoint API endpoint.";
    } else {
        // Get from database
        $publications = getPublicationsFromDB($pdo);
        $message = 'Displaying publications from database. ';
        $message .= $refresh ? 'Use ?refresh=1 to update from API.' : '';
    }
    
    // Output the publications as JSON
    $response = [
        'status' => 'success',
        'message' => $message,
        'count' => count($publications),
        'publications' => $publications,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    if ($refresh) {
        $response['api_endpoint'] = API_BASE_URL . API_ENDPOINTS[$endpoint];
    }
    
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Error: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
}
?>
