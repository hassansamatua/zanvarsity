<?php
// Display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set a custom error log file in the uploads directory
$logFile = __DIR__ . '/../uploads/upload_errors.log';
ini_set('error_log', $logFile);

// Log a test message
error_log("Test error message at " . date('Y-m-d H:i:s'));

$message = '';
$uploadedFile = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetDir = __DIR__ . '/../uploads/carousel/';
    $targetFile = $targetDir . basename($_FILES['fileToUpload']['name']);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Check if file already exists
    if (file_exists($targetFile)) {
        $message = "Sorry, file already exists.";
        $uploadOk = 0;
    }
    
    // Check file size (5MB max)
    if ($_FILES['fileToUpload']['size'] > 5000000) {
        $message = "Sorry, your file is too large. Maximum size is 5MB.";
        $uploadOk = 0;
    }
    
    // Allow certain file formats
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowedTypes)) {
        $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $message = "Sorry, your file was not uploaded. " . $message;
    } else {
        if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $targetFile)) {
            $message = "The file ". htmlspecialchars(basename($_FILES['fileToUpload']['name'])). " has been uploaded.";
            $uploadedFile = $targetFile;
            
            // Log the successful upload
            error_log("File uploaded successfully: " . $targetFile);
        } else {
            $message = "Sorry, there was an error uploading your file.";
            error_log("Error uploading file: " . print_r(error_get_last(), true));
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>File Upload Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .message { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background-color: #dff0d8; color: #3c763d; border: 1px solid #d6e9c6; }
        .error { background-color: #f2dede; color: #a94442; border: 1px solid #ebccd1; }
        .file-list { margin-top: 20px; }
        .file-list table { width: 100%; border-collapse: collapse; }
        .file-list th, .file-list td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .file-list th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>File Upload Test</h1>
        
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'error') !== false ? 'error' : 'success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <form action="" method="post" enctype="multipart/form-data">
            <div>
                <label for="fileToUpload">Select file to upload:</label>
                <input type="file" name="fileToUpload" id="fileToUpload" required>
            </div>
            <div style="margin-top: 10px;">
                <input type="submit" value="Upload File" name="submit">
            </div>
        </form>
        
        <?php if ($uploadedFile): ?>
            <div class="file-preview" style="margin-top: 20px;">
                <h3>Uploaded File:</h3>
                <img src="<?php echo str_replace(__DIR__ . '/..', '', $uploadedFile); ?>" alt="Uploaded Image" style="max-width: 100%; max-height: 300px;">
            </div>
        <?php endif; ?>
        
        <div class="file-list">
            <h3>Existing Files in Uploads/Carousel:</h3>
            <?php
            $files = glob(__DIR__ . '/../uploads/carousel/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
            if (count($files) > 0): ?>
                <table>
                    <tr>
                        <th>File Name</th>
                        <th>Size</th>
                        <th>Last Modified</th>
                        <th>Preview</th>
                    </tr>
                    <?php foreach ($files as $file): 
                        $fileUrl = str_replace(__DIR__ . '/..', '', $file);
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars(basename($file)); ?></td>
                            <td><?php echo number_format(filesize($file) / 1024, 2); ?> KB</td>
                            <td><?php echo date('Y-m-d H:i:s', filemtime($file)); ?></td>
                            <td><img src="<?php echo $fileUrl; ?>" alt="Preview" style="max-width: 100px; max-height: 100px;"></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No files found in the uploads directory.</p>
            <?php endif; ?>
        </div>
        
        <div class="error-log" style="margin-top: 30px; background: #f5f5f5; padding: 10px; border: 1px solid #ddd;">
            <h3>Error Log (<?php echo $logFile; ?>)</h3>
            <?php 
            if (file_exists($logFile)) {
                echo '<pre>' . htmlspecialchars(file_get_contents($logFile)) . '</pre>';
            } else {
                echo '<p>Error log file not found or is empty.</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>
