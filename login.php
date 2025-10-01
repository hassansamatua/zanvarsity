<?php
/**
 * Login Handler for Zanvarsity
 * 
 * This script handles user authentication, validates credentials,
 * and manages user sessions.
 */

// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    session_start();
}

// Include necessary files
require_once __DIR__ . '/includes/auth_functions.php';
require_once __DIR__ . '/includes/database.php';

// Set default redirect URL
$redirect_url = '/c/zanvarsity/html/my-account.php';

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        error_log('CSRF token validation failed');
        header('Location: /c/zanvarsity/html/login.php?error=invalid_csrf');
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
        // Authentication successful - set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        
        // Set user role with proper validation
        $allowed_roles = ['student', 'instructor', 'admin', 'super_admin'];
        $_SESSION['role'] = in_array(strtolower($user['role'] ?? 'student'), $allowed_roles) 
            ? strtolower($user['role']) 
            : 'student';
        
        // Set user's name if available
        if (isset($user['first_name'])) {
            $_SESSION['first_name'] = htmlspecialchars($user['first_name'], ENT_QUOTES, 'UTF-8');
            if (isset($user['last_name'])) {
                $_SESSION['last_name'] = htmlspecialchars($user['last_name'], ENT_QUOTES, 'UTF-8');
                $_SESSION['full_name'] = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
            } else {
                $_SESSION['full_name'] = $_SESSION['first_name'];
            }
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
        
        // Clear the CSRF token after successful login
        unset($_SESSION['csrf_token']);
        
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
        
        header("Location: /c/zanvarsity/html/login.php?error=invalid_credentials&email=" . urlencode($email));
        exit();
    }
} else {
    // If not a POST request, redirect to login page
    header("Location: /c/zanvarsity/html/login.php");
    exit();
}
?>
