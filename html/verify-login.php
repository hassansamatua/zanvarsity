<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session with secure settings
$session_name = 'zanvarsity_session';
$secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
$httponly = true;
$samesite = 'Lax';

// Set session parameters
session_set_cookie_params([
    'lifetime' => 86400, // 1 day
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => $secure,
    'httponly' => $httponly,
    'samesite' => $samesite
]);

// Set session name
session_name($session_name);

// Start or resume session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Verification</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .success { color: #3c763d; background: #dff0d8; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .error { color: #a94442; background: #f2dede; padding: 15px; margin: 10px 0; border-radius: 4px; }
        pre { background: #f5f5f5; padding: 15px; border: 1px solid #ddd; border-radius: 4px; overflow-x: auto; }
        .btn { display: inline-block; padding: 8px 15px; margin: 5px; text-decoration: none; border-radius: 4px; }
        .btn-primary { background: #337ab7; color: white; }
        .btn-danger { background: #d9534f; color: white; }
    </style>
</head>
<body>
    <h1>Login Verification</h1>
    
    <?php if ($isLoggedIn): ?>
        <div class="success">
            <h3>✓ You are logged in!</h3>
            <p>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>!</p>
            <a href="logout.php" class="btn btn-danger">Logout</a>
            <a href="my-account.php" class="btn btn-primary">Go to My Account</a>
        </div>
    <?php else: ?>
        <div class="error">
            <h3>✗ You are not logged in</h3>
            <p>Please login to continue.</p>
            <a href="login.php" class="btn btn-primary">Go to Login</a>
        </div>
    <?php endif; ?>
    
    <h2>Session Information</h2>
    <pre>Session ID: <?php echo session_id(); ?>
Session Name: <?php echo session_name(); ?>
Session Status: <?php echo session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Not Active'; ?>

Session Data:
<?php print_r($_SESSION); ?>

Cookies:
<?php print_r($_COOKIE); ?>

Server Info:
PHP Version: <?php echo phpversion(); ?>
Session Save Path: <?php echo session_save_path(); ?>
Is Writable: <?php echo is_writable(session_save_path()) ? 'Yes' : 'No'; ?>

Request Headers:
<?php 
$headers = [];
foreach (getallheaders() as $name => $value) {
    $headers[] = "$name: $value";
}
echo implode("\n", $headers);
?></pre>

    <h2>Test Links</h2>
    <p>
        <a href="login.php" class="btn btn-primary">Test Login Page</a>
        <a href="my-account.php" class="btn btn-primary">Test My Account</a>
        <a href="check-session.php" class="btn btn-primary">Check Session</a>
    </p>
</body>
</html>
