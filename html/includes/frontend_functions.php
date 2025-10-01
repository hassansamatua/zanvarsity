<?php
/**
 * Frontend Functions
 * Handles database queries for the frontend
 */

// Include database connection
require_once __DIR__ . '/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Execute a safe query with parameters
 */
function execute_query($sql, $params = [], $param_types = '') {
    global $conn;
    $result = [];
    
    try {
        $stmt = $conn->prepare($sql);
        
        if ($params) {
            $stmt->bind_param($param_types, ...$params);
        }
        
        $stmt->execute();
        $query_result = $stmt->get_result();
        
        if ($query_result) {
            while ($row = $query_result->fetch_assoc()) {
                $result[] = $row;
            }
        }
        
        return $result;
    } catch (Exception $e) {
        error_log("Query Error: " . $e->getMessage() . "\nSQL: " . $sql);
        return [];
    }
}

/**
 * Get carousel slides
 */
function get_carousel_slides($limit = 5) {
    // First try common table names
    $tables_to_try = ['carousel_slides', 'carousel', 'slides', 'banners'];
    $active_field = '';
    $slides = [];
    
    foreach ($tables_to_try as $table) {
        try {
            // Check if table exists and has required fields
            $sql = "SHOW COLUMNS FROM `$table`";
            $columns = execute_query($sql);
            
            if (empty($columns)) continue;
            
            // Determine active field name
            $active_field = in_array('is_active', array_column($columns, 'Field')) ? 'is_active' : '';
            $order_field = in_array('display_order', array_column($columns, 'Field')) ? 'display_order' : 'id';
            
            // Build query
            $sql = "SELECT * FROM `$table`";
            $params = [];
            $param_types = '';
            
            if ($active_field) {
                $sql .= " WHERE $active_field = ?";
                $params[] = 1;
                $param_types .= 'i';
            }
            
            $sql .= " ORDER BY $order_field ASC LIMIT ?";
            $params[] = $limit;
            $param_types .= 'i';
            
            $slides = execute_query($sql, $params, $param_types);
            
            if (!empty($slides)) {
                return $slides;
            }
        } catch (Exception $e) {
            // Table doesn't exist or error occurred, try next one
            continue;
        }
    }
    
    return [];
}

/**
 * Get latest announcements
 */
function get_latest_announcements($limit = 3) {
    $tables_to_try = ['announcements', 'announcement', 'news', 'posts'];
    
    foreach ($tables_to_try as $table) {
        try {
            $sql = "SHOW COLUMNS FROM `$table`";
            $columns = execute_query($sql);
            
            if (empty($columns)) continue;
            
            // Determine field names
            $date_field = in_array('created_at', array_column($columns, 'Field')) ? 'created_at' : 
                         (in_array('date_created', array_column($columns, 'Field')) ? 'date_created' : 'id');
            $status_field = in_array('is_published', array_column($columns, 'Field')) ? 'is_published' : '';
            
            $sql = "SELECT * FROM `$table`";
            $params = [];
            $param_types = '';
            
            if ($status_field) {
                $sql .= " WHERE $status_field = ?";
                $params[] = 1;
                $param_types .= 'i';
            }
            
            $sql .= " ORDER BY $date_field DESC LIMIT ?";
            $params[] = $limit;
            $param_types .= 'i';
            
            $announcements = execute_query($sql, $params, $param_types);
            
            if (!empty($announcements)) {
                return $announcements;
            }
        } catch (Exception $e) {
            continue;
        }
    }
    
    return [];
}

/**
 * Get latest news
 */
function get_latest_news($limit = 3) {
    // Try to get from news table first
    $news = get_latest_announcements($limit);
    
    if (!empty($news)) {
        return $news;
    }
    
    // If no news found in announcements, try other tables
    $tables_to_try = ['news', 'articles', 'posts'];
    
    foreach ($tables_to_try as $table) {
        try {
            $sql = "SHOW COLUMNS FROM `$table`";
            $columns = execute_query($sql);
            
            if (empty($columns)) continue;
            
            // Determine field names
            $date_field = in_array('publish_date', array_column($columns, 'Field')) ? 'publish_date' : 
                         (in_array('date_created', array_column($columns, 'Field')) ? 'date_created' : 'id');
            $status_field = in_array('is_published', array_column($columns, 'Field')) ? 'is_published' : '';
            
            $sql = "SELECT * FROM `$table`";
            $params = [];
            $param_types = '';
            
            if ($status_field) {
                $sql .= " WHERE $status_field = ?";
                $params[] = 1;
                $param_types .= 'i';
            }
            
            $sql .= " ORDER BY $date_field DESC LIMIT ?";
            $params[] = $limit;
            $param_types .= 'i';
            
            $news = execute_query($sql, $params, $param_types);
            
            if (!empty($news)) {
                return $news;
            }
        } catch (Exception $e) {
            continue;
        }
    }
    
    return [];
}

/**
 * Get upcoming events
 */
function get_upcoming_events($limit = 3) {
    $tables_to_try = ['events', 'event', 'calendar_events'];
    $current_date = date('Y-m-d H:i:s');
    
    foreach ($tables_to_try as $table) {
        try {
            $sql = "SHOW COLUMNS FROM `$table`";
            $columns = execute_query($sql);
            
            if (empty($columns)) continue;
            
            // Determine field names
            $start_date_field = in_array('start_date', array_column($columns, 'Field')) ? 'start_date' : 
                               (in_array('event_date', array_column($columns, 'Field')) ? 'event_date' : 'date');
            $end_date_field = in_array('end_date', array_column($columns, 'Field')) ? 'end_date' : $start_date_field;
            $status_field = in_array('is_published', array_column($columns, 'Field')) ? 'is_published' : '';
            
            $sql = "SELECT * FROM `$table`";
            $params = [];
            $param_types = '';
            
            $where = [];
            
            if ($status_field) {
                $where[] = "$status_field = ?";
                $params[] = 1;
                $param_types .= 'i';
            }
            
            $where[] = "($end_date_field IS NULL OR $end_date_field >= ?)";
            $params[] = $current_date;
            $param_types .= 's';
            
            if (!empty($where)) {
                $sql .= " WHERE " . implode(' AND ', $where);
            }
            
            $sql .= " ORDER BY $start_date_field ASC LIMIT ?";
            $params[] = $limit;
            $param_types .= 'i';
            
            $events = execute_query($sql, $params, $param_types);
            
            if (!empty($events)) {
                return $events;
            }
        } catch (Exception $e) {
            continue;
        }
    }
    
    return [];
}
?>
