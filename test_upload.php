<?php
// Display error log path
echo "Error Log: " . ini_get('error_log') . "<br>";

// Test file upload handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['test_file'])) {
    $upload_dir = __DIR__ . '/uploads/test/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $target_file = $upload_dir . basename($_FILES['test_file']['name']);
    
    if (move_uploaded_file($_FILES['test_file']['tmp_name'], $target_file)) {
        echo "File uploaded successfully to: " . $target_file . "<br>";
        echo "File size: " . filesize($target_file) . " bytes<br>";
    } else {
        echo "Error uploading file. Error: " . $_FILES['test_file']['error'] . "<br>";
        echo "Upload directory exists: " . (is_dir($upload_dir) ? 'Yes' : 'No') . "<br>";
        echo "Upload directory writable: " . (is_writable($upload_dir) ? 'Yes' : 'No') . "<br>";
    }
}
?>

<h2>Test File Upload</h2>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="test_file">
    <button type="submit">Upload Test File</button>
</form>
