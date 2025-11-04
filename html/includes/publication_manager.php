<?php
require_once __DIR__ . '/db_connect.php';

class PublicationManager {
    private $conn;
    private $lastUpdateFile;
    private $cacheDuration = 3600; // 1 hour in seconds
    
    public function __construct() {
        $this->conn = getZanvarsityDbConnection();
        $this->lastUpdateFile = __DIR__ . '/../cache/publications_last_update.txt';
        $this->ensureTableExists();
    }
    
    private function ensureTableExists() {
        $sql = "CREATE TABLE IF NOT EXISTS publications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            author VARCHAR(255) DEFAULT 'ZUMI Staff',
            description TEXT,
            publication_date DATE,
            status_code VARCHAR(10) DEFAULT '200',
            status_desc VARCHAR(255) DEFAULT 'Active',
            api_id VARCHAR(100) UNIQUE,
            is_featured BOOLEAN DEFAULT 0,
            image_url VARCHAR(512),
            document_url VARCHAR(512),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            last_fetched_at TIMESTAMP NULL,
            INDEX idx_api_id (api_id),
            INDEX idx_publication_date (publication_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $this->conn->exec($sql);
    }
    
    public function shouldUpdateFromApi() {
        if (!file_exists($this->lastUpdateFile)) {
            return true;
        }
        
        $lastUpdate = (int)file_get_contents($this->lastUpdateFile);
        return (time() - $lastUpdate) > $this->cacheDuration;
    }
    
    public function updatePublicationsFromApi() {
        $apiUrl = "https://www.zumis.ac.tz/academic/data/api/getAllPublications";
        
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
            error_log("Failed to fetch publications from API. HTTP Code: $httpCode");
            return false;
        }
        
        $publications = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Invalid JSON response from API: " . json_last_error_msg());
            return false;
        }
        
        if (!is_array($publications)) {
            error_log("Unexpected API response format");
            return false;
        }
        
        $this->conn->beginTransaction();
        
        try {
            // Mark all existing publications as inactive
            $stmt = $this->conn->prepare("UPDATE publications SET status_code = '404' WHERE status_code = '200'");
            $stmt->execute();
            
            // Prepare insert/update statement
            $stmt = $this->conn->prepare("
                INSERT INTO publications 
                (title, author, description, publication_date, status_code, status_desc, api_id, image_url, document_url, last_fetched_at)
                VALUES 
                (:title, :author, :description, :publication_date, '200', 'Active', :api_id, :image_url, :document_url, NOW())
                ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                author = VALUES(author),
                description = VALUES(description),
                publication_date = VALUES(publication_date),
                status_code = '200',
                status_desc = 'Active',
                image_url = VALUES(image_url),
                document_url = VALUES(document_url),
                last_fetched_at = NOW()
            ");
            
            $count = 0;
            foreach ($publications as $pub) {
                if (!is_array($pub)) continue;
                
                $data = [
                    ':title' => $pub['title'] ?? 'Untitled Publication',
                    ':author' => $pub['author'] ?? 'ZUMI Staff',
                    ':description' => $pub['description'] ?? $pub['abstract'] ?? 'No description available.',
                    ':publication_date' => !empty($pub['date']) ? date('Y-m-d', strtotime($pub['date'])) : date('Y-m-d'),
                    ':api_id' => $pub['id'] ?? 'pub_' . md5(serialize($pub)),
                    ':image_url' => $pub['image_url'] ?? null,
                    ':document_url' => $pub['document_url'] ?? $pub['file_url'] ?? null
                ];
                
                $stmt->execute($data);
                $count++;
            }
            
            $this->conn->commit();
            file_put_contents($this->lastUpdateFile, time());
            return $count;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error updating publications: " . $e->getMessage());
            return false;
        }
    }
    
    public function getPublications($limit = 6, $featuredOnly = false) {
        try {
            $query = "SELECT * FROM publications 
                     WHERE status_code = '200' " . 
                     ($featuredOnly ? "AND is_featured = 1 " : "") .
                     "ORDER BY publication_date DESC 
                     LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log('Error getting publications: ' . $e->getMessage());
            return [];
        }
    }
    
    public function getPublicationById($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM publications WHERE id = :id AND status_code = '200'");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error getting publication: ' . $e->getMessage());
            return null;
        }
    }
}

// Initialize the publication manager
$publicationManager = new PublicationManager();

// Check if we need to update from API (only if it's been more than 1 hour since last update)
if ($publicationManager->shouldUpdateFromApi()) {
    // Run in background to avoid slowing down the page load
    $scriptPath = __DIR__ . '/../cron/update_publications.php';
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        pclose(popen("start /B php $scriptPath > NUL", 'r'));
    } else {
        exec("php $scriptPath > /dev/null 2>&1 &");
    }
}
?>
