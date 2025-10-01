<?php
// Start session with secure settings
$session_name = 'zanvarsity_session';
$secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
$httponly = true;

// Set session parameters
session_name($session_name);
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => $secure,
    'httponly' => $httponly,
    'samesite' => 'Lax'
]);

// Start or resume session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simple validation
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } else {
        // For testing - accept any non-empty credentials
        $_SESSION['user_id'] = 123;
        $_SESSION['user_name'] = 'Test User';
        $_SESSION['user_role'] = 'user';
        
        // Debug: Log the session data
        error_log('Debug Login - Session data set: ' . print_r($_SESSION, true));
        
        // Redirect to verify page
        header('Location: verify-login.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Login</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; }
        button { padding: 8px 15px; background: #007bff; color: white; border: none; cursor: pointer; }
        .error { color: red; margin: 10px 0; }
        .debug { background: #f5f5f5; padding: 15px; margin-top: 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <h2>Debug Login</h2>
    
    <?php if (isset($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <form method="post" action="debug-login.php">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit">Login</button>
    </form>
    
    <div class="debug">
        <h3>Debug Information</h3>
        <p>Session ID: <?php echo session_id(); ?></p>
        <p>Session Status: <?php echo session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Not Active'; ?></p>
        <p>Session Data: <?php echo htmlspecialchars(print_r($_SESSION, true)); ?></p>
        <p>Cookies: <?php echo htmlspecialchars(print_r($_COOKIE, true)); ?></p>
    </div>
    
    <div style="margin-top: 20px;">
        <p><a href="verify-login.php">Check Login Status</a></p>
        <p><a href="test-login-form.php">Test Login Form</a></p>
        <p><a href="check-session.php">Check Session</a></p>
    </div>
</body>
</html>
