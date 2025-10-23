<?php
/**
 * Login Handler for Zanvarsity
 * 
 * This script handles user authentication, validates credentials,
 * and manages user sessions.
 */

// Include necessary files first
require_once __DIR__ . '/includes/auth_functions.php';
require_once __DIR__ . '/includes/database.php';

// Set default redirect URL
$redirect_url = '/c/zanvarsity/html/my-account.php';

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ensure session is started and has a CSRF token
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Generate CSRF token if not exists
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    // Verify CSRF token with detailed error logging
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
        !hash_equals((string)$_SESSION['csrf_token'], (string)$_POST['csrf_token'])) {
        
        error_log('CSRF token validation failed. ' . 
                 'Session token: ' . ($_SESSION['csrf_token'] ?? 'not set') . ', ' .
                 'POST token: ' . ($_POST['csrf_token'] ?? 'not set') . ', ' . 
                 'Session ID: ' . (session_id() ?: 'no session'));
        
        // Regenerate CSRF token for next attempt
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        
        // Redirect back to login with error
        header('Location: /c/zanvarsity/html/sign-in.php?error=invalid_csrf');
        exit();
    }
    // Validate required fields
    if (empty($_POST['email']) || empty($_POST['password'])) {
        header("Location: /c/zanvarsity/html/login.php?error=empty_fields");
        exit();
    }
    
    // Sanitize and validate input
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: /c/zanvarsity/html/login.php?error=invalid_email&email=" . urlencode($email));
        exit();
    }
    
    
        // Debug: Log login attempt
    error_log("Login attempt for email: " . $email);
    
    // Attempt to authenticate user
    $user = authenticate_user($email, $password);
    
    if ($user) {
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email']; // Make sure this matches the session key used in my-account.php
        
        // Set user role with proper validation
        $allowed_roles = ['student', 'instructor', 'admin', 'super_admin', 'dean'];
        $_SESSION['role'] = in_array(strtolower($user['role'] ?? 'student'), $allowed_roles) 
            ? strtolower($user['role']) 
            : 'student';
            
        // Set user's name and other profile information
        $_SESSION['first_name'] = isset($user['first_name']) ? htmlspecialchars($user['first_name'], ENT_QUOTES, 'UTF-8') : '';
        $_SESSION['last_name'] = isset($user['last_name']) ? htmlspecialchars($user['last_name'], ENT_QUOTES, 'UTF-8') : '';
        
        // Set profile image if available
        if (!empty($user['profile_image'])) {
            $_SESSION['profile_image'] = $user['profile_image'];
        } else {
            // Default avatar if no profile image is set
            $_SESSION['profile_image'] = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiB2aWV3Qm94PSIwIDAgMTAwIDEwMCI+PHJlY3Qgd2lkdGg9IjEwMCIgaGVpZ2h0PSIxMDAiIGZpbGw9IiNkZGQiLz48dGV4dCB4PSI1MCIgeT0iNTAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgYWxpZ25tZW50LWJhc2VsaW5lPSJtaWRkbGUiIGZpbGw9IiYjNjY2OyI+QXZhdGFyPC90ZXh0Pjwvc3ZnPg';
        }
        
        // Set last activity time
        $_SESSION['last_activity'] = time();
        if (!empty($_SESSION['last_name'])) {
            $_SESSION['full_name'] = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
        } else {
            $_SESSION['full_name'] = $_SESSION['first_name'];
        }
        
        // Set last login time
        $_SESSION['last_login'] = time();
        
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        // Log the successful login
        error_log(sprintf(
            'User login: ID=%s, Email=%s, Role=%s, IP=%s',
            $user['id'],
            $user['email'],
            $_SESSION['role'],
            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ));
        // Generate new CSRF token after successful login
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        
        // Log successful login
        error_log("User logged in successfully: " . $user['email']);
        
        // Always redirect to my-account.php first
        $redirect = '/c/zanvarsity/html/my-account.php';
        
        // Check for remember me option
        if (isset($_POST['remember']) && $_POST['remember'] === 'on') {
            // Set a long-lived cookie (30 days)
            $token = bin2hex(random_bytes(32));
            $expires = time() + (30 * 24 * 60 * 60); // 30 days
            
            // Store token in database (you'll need to implement this)
            // save_remember_token($user['id'], $token, $expires);
            
            // Set secure cookie
            setcookie(
                'remember_token',
                $token,
                [
                    'expires' => $expires,
                    'path' => '/',
                    'domain' => $_SERVER['HTTP_HOST'],
                    'secure' => isset($_SERVER['HTTPS']),
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]
            );
        }
        
        // Redirect to the appropriate page
        header('Location: ' . $redirect);
        exit();
    } else {
        // Authentication failed
        error_log(sprintf(
            'Failed login attempt for email: %s from IP: %s',
            $email,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ));
        
        // Random delay to prevent timing attacks
        usleep(rand(200000, 1000000)); // 0.2 - 1 second delay
        
        header("Location: /c/zanvarsity/html/sign-in.php?error=invalid_credentials&email=" . urlencode($email));
        exit();
    }
} else {
    // If not a POST request, redirect to login page
    header("Location: /c/zanvarsity/html/sign-in.php");
    exit();
}
?>
