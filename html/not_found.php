<?php
// Set the response code to 404
http_response_code(404);
$page_title = 'Page Not Found';
$page_description = 'The page you are looking for could not be found';

// Get the requested URI
$requested_uri = isset($_SERVER['REQUEST_URI']) ? htmlspecialchars($_SERVER['REQUEST_URI']) : 'this page';

// Include header
$header_file = 'includes/about_header.php';
if (file_exists($header_file)) {
    include_once $header_file;
} else {
    // If header doesn't exist, output basic HTML
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . $page_title . ' | Zanzibar University</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 0; background: #f8f9fa; color: #333; }
            .container { max-width: 800px; margin: 0 auto; padding: 40px 20px; }
            .error-content { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); text-align: center; }
            .error-code { font-size: 8rem; font-weight: 700; color: #e9ecef; margin: 0; line-height: 1; }
            h1 { color: #004225; font-size: 2.2rem; margin: 20px 0; }
            p { color: #6c757d; font-size: 1.1rem; margin-bottom: 30px; }
            .btn { display: inline-block; background: #004225; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 4px; font-weight: 600; transition: all 0.3s ease; }
            .btn:hover { background: #003319; }
        </style>
    </head>
    <body>';
}
?>

<!-- 404 Error Content -->
<div class="error-container">
    <div class="container">
        <div class="error-content">
            <div class="error-code">404</div>
            <h1>Page Not Found</h1>
            <p>
                The page <strong><?php echo $requested_uri; ?></strong> you are looking for might have been removed, 
                had its name changed, or is temporarily unavailable.
            </p>
            <p>Here are some helpful links instead:</p>
            <div style="margin-top: 30px;">
                <a href="index.php" class="btn">Go to Homepage</a>
                <a href="javascript:history.back()" class="btn" style="margin-left: 10px; background: #6c757d;">Go Back</a>
            </div>
        </div>
    </div>
    <?php
// Include footer if it exists
$footer_file = 'includes/about_footer.php';
if (file_exists($footer_file)) {
    include_once $footer_file;
} else {
    // Close the HTML tags we opened in the header
    echo '    </body>
    </html>';
}
?>
</div>

<style>
.error-container {
    background-color: #f8f9fa;
    min-height: 100vh;
    padding: 60px 0;
    text-align: center;
}
.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 15px;
}
.error-content {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
    padding: 40px 20px;
}
.error-code {
    font-size: 8rem;
    font-weight: 700;
    color: #e9ecef;
    margin: 0 0 20px 0;
    line-height: 1;
}
h1 {
    color: #004225;
    font-size: 2.2rem;
    margin: 0 0 20px 0;
}
p {
    color: #6c757d;
    font-size: 1.1rem;
    margin-bottom: 20px;
    line-height: 1.6;
}
.btn {
    display: inline-block;
    background-color: #004225;
    color: #fff;
    padding: 12px 30px;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    margin: 5px;
}
.btn:hover {
    background-color: #003319;
    transform: translateY(-2px);
}
</style>


