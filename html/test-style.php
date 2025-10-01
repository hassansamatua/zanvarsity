<?php
// Start session
session_start();

// Set base URL for assets
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$base_path = trim(dirname($_SERVER['PHP_SELF']), '/\\');

// Construct base URL without double slashes
$base_url = "$protocol://$host";
if (!empty($base_path)) {
    $base_url .= '/' . $base_path;
}

// Ensure base_url ends with a slash
$base_url = rtrim($base_url, '/') . '/';

// Store base URL in session for header
$_SESSION['base_url'] = $base_url;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Style Test</title>
    
    <!-- Core CSS -->
    <link href="<?php echo $base_url; ?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>assets/css/font-awesome.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>assets/css/style.css" rel="stylesheet">
    
    <style>
        .test-box {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
        .debug-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-family: monospace;
            white-space: pre;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="page-header">
                    <h1>Style Test Page</h1>
                    <p class="lead">This page tests if the CSS files are loading correctly.</p>
                </div>
                
                <div class="test-box">
                    <h3>Test Box</h3>
                    <p>This is a test box to check if styles are applied.</p>
                    <button class="btn btn-primary">Test Button</button>
                </div>
                
                <div class="debug-info">
                    <strong>Debug Information:</strong>
                    Base URL: <?php echo htmlspecialchars($base_url); ?>
                    
                    <?php
                    $css_files = [
                        'bootstrap.min.css' => $base_url . 'assets/bootstrap/css/bootstrap.min.css',
                        'font-awesome.css' => $base_url . 'assets/css/font-awesome.css',
                        'style.css' => $base_url . 'assets/css/style.css'
                    ];
                    
                    echo "\n\nCSS Files:";
                    foreach ($css_files as $name => $url) {
                        $exists = @file_get_contents($url) !== false;
                        echo "\n- $name: " . ($exists ? '✅ Found' : '❌ Not Found') . " at $url";
                    }
                    ?>
                </div>
                
                <div class="alert alert-info">
                    <h4>If styles are not working:</h4>
                    <ol>
                        <li>Check if the CSS files exist at the specified paths</li>
                        <li>Check the browser's developer console for 404 errors</li>
                        <li>Verify file permissions in the assets directory</li>
                    </ol>
                </div>
                
                <p><a href="login.php" class="btn btn-default">Back to Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>
