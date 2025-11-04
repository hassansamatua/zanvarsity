<?php
// Function to get faculty content
getFacultyContent($faculty_id, $content_type = null) {
    global $conn;
    
    $sql = "SELECT * FROM faculty_content WHERE faculty_id = ?";
    $params = [$faculty_id];
    $types = "i";
    
    if ($content_type) {
        $sql .= " AND content_type = ?";
        $params[] = $content_type;
        $types .= "s";
    }
    
    $stmt = $conn->prepare($sql);
    
    if ($content_type) {
        $stmt->bind_param($types, $faculty_id, $content_type);
    } else {
        $stmt->bind_param($types, $faculty_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($content_type) {
        return $result->fetch_assoc();
    } else {
        $content = [];
        while ($row = $result->fetch_assoc()) {
            $content[$row['content_type']] = $row;
        }
        return $content;
    }
}

// Function to save faculty content
function saveFacultyContent($faculty_id, $content_type, $title, $content, $image_path = null, $user_id = null) {
    global $conn;
    
    // Check if content already exists
    $existing = getFacultyContent($faculty_id, $content_type);
    
    if ($existing) {
        // Update existing content
        $sql = "UPDATE faculty_content SET 
                title = ?, 
                content = ?,
                updated_at = NOW()";
                
        $params = [$title, $content];
        $types = "ss";
        
        if ($image_path) {
            $sql .= ", image_path = ?";
            $params[] = $image_path;
            $types .= "s";
        }
        
        $sql .= " WHERE faculty_id = ? AND content_type = ?";
        $params[] = $faculty_id;
        $params[] = $content_type;
        $types .= "is";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
    } else {
        // Insert new content
        $sql = "INSERT INTO faculty_content 
                (faculty_id, content_type, title, content, image_path, created_by, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssi", $faculty_id, $content_type, $title, $content, $image_path, $user_id);
    }
    
    return $stmt->execute();
}

// Function to handle file upload
function uploadFacultyImage($file, $faculty_id, $content_type) {
    $upload_dir = __DIR__ . '/../uploads/faculty_content/';
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique filename
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = 'faculty_' . $faculty_id . '_' . $content_type . '_' . time() . '.' . $file_extension;
    $target_path = $upload_dir . $filename;
    
    // Check file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Only JPG, PNG, and GIF files are allowed.'];
    }
    
    // Check file size (max 2MB)
    if ($file['size'] > 2 * 1024 * 1024) {
        return ['success' => false, 'message' => 'File size must be less than 2MB.'];
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return [
            'success' => true, 
            'path' => '/c/zanvarsity/uploads/faculty_content/' . $filename
        ];
    } else {
        return ['success' => false, 'message' => 'Failed to upload file.'];
    }
}
?>
