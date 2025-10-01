<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set session parameters
$session_name = 'zanvarsity_session';
$secure = isset($_SERVER['HTTPS']);
$httponly = true;

session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => $secure,
    'httponly' => $httponly,
    'samesite' => 'Lax'
]);

session_name($session_name);

// Start or resume session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['set'])) {
        $_SESSION['test_key'] = 'test_value_' . time();
        $message = 'Session variable set: ' . $_SESSION['test_key'];
    } elseif (isset($_POST['check'])) {
        $message = isset($_SESSION['test_key']) 
            ? 'Session variable found: ' . $_SESSION['test_key'] 
            : 'No session variable found';
    } elseif (isset($_POST['destroy'])) {
        session_destroy();
        $message = 'Session destroyed';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Session Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .container { background: #f5f5f5; padding: 20px; border-radius: 5px; margin-top: 20px; }
        pre { background: #fff; padding: 15px; border: 1px solid #ddd; border-radius: 4px; overflow-x: auto; }
        button { padding: 8px 15px; margin: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Session Test Page</h1>
    
    <?php if (!empty($message)): ?>
        <div style="background: #dff0d8; color: #3c763d; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <div class="container">
        <h2>Session Actions</h2>
        <form method="post">
            <button type="submit" name="set">Set Session Variable</button>
            <button type="submit" name="check">Check Session</button>
            <button type="submit" name="destroy">Destroy Session</button>
        </form>
        
        <h3>Session Information</h3>
        <pre>Session ID: <?php echo session_id(); ?>
Session Name: <?php echo session_name(); ?>
Session Status: <?php echo session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Not Active'; ?>

Session Data:
<?php print_r($_SESSION); ?>

Cookies:
<?php print_r($_COOKIE); ?>

Server Info:
<?php 
echo 'PHP Version: ' . phpversion() . "\n";
echo 'Session Save Path: ' . session_save_path() . "\n";
echo 'Cookie Parameters: ' . print_r(session_get_cookie_params(), true);
?></pre>
    </div>
</body>
</html>
