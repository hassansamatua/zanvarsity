<?php
// Start session
session_start();

// Simple login check
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Simple validation
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } else {
        // For testing purposes, accept any non-empty credentials
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = 'Test User';
        
        // Redirect to verify page
        header('Location: verify-login.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Login</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="email"], input[type="password"] { width: 100%; padding: 8px; }
        button { padding: 8px 15px; background: #007bff; color: white; border: none; cursor: pointer; }
        .error { color: red; margin-top: 10px; }
    </style>
</head>
<body>
    <h2>Test Login Form</h2>
    
    <?php if (isset($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <form method="post" action="test-login-form.php">
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
    
    <div style="margin-top: 20px;">
        <p>Test this form to see if basic login works.</p>
        <p>After login, you'll be redirected to verify-login.php</p>
    </div>
    
    <div style="margin-top: 30px; padding: 15px; background: #f0f0f0; border-radius: 5px;">
        <h3>Debug Info:</h3>
        <p>Session ID: <?php echo session_id(); ?></p>
        <p>Session Status: <?php echo session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Not Active'; ?></p>
        <p>Session Data: <?php print_r($_SESSION); ?></p>
    </div>
</body>
</html>
