<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get session save path
$sessionPath = session_save_path();
$isWritable = is_writable($sessionPath);
$sessionFiles = [];

// List session files if directory is readable
if (is_dir($sessionPath) && is_readable($sessionPath)) {
    $sessionFiles = array_diff(scandir($sessionPath), ['.', '..']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Session Directory Check</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .success { color: #3c763d; background: #dff0d8; padding: 10px; margin: 10px 0; border-radius: 4px; }
        .error { color: #a94442; background: #f2dede; padding: 10px; margin: 10px 0; border-radius: 4px; }
        pre { background: #f5f5f5; padding: 15px; border: 1px solid #ddd; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Session Directory Check</h1>
    
    <h2>Session Information</h2>
    <pre>PHP Version: <?php echo phpversion(); ?>
Session Save Path: <?php echo $sessionPath; ?>
Is Writable: <?php echo $isWritable ? 'Yes' : 'No'; ?>
Session Status: <?php echo session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Not Active'; ?>
Session ID: <?php echo session_id(); ?></pre>

    <h2>Session Directory Contents</h2>
    <?php if (!empty($sessionFiles)): ?>
        <p>Found <?php echo count($sessionFiles); ?> session files:</p>
        <pre><?php print_r($sessionFiles); ?></pre>
    <?php else: ?>
        <p>No session files found or directory is not readable.</p>
    <?php endif; ?>

    <h2>PHP Info</h2>
    <p><a href="phpinfo.php">View phpinfo()</a></p>
    
    <h2>Test Session</h2>
    <p><a href="check-session.php">Test Session Handling</a></p>
</body>
</html>
