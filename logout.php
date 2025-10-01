<?php
/**
 * Logout Handler for Zanvarsity
 * 
 * This script handles user logout by securely destroying the session
 * and preventing session fixation attacks.
 */

// Include configuration and authentication functions
require_once __DIR__ . '/includes/auth_functions.php';

// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    // Security settings
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    ini_set('session.cookie_samesite', 'Lax');
    
    // Set session name and start session
    session_name('zanvarsity_session');
    session_start();
}

// Log the logout action if user was logged in
if (isset($_SESSION['user_id'], $_SESSION['user_email'])) {
    error_log(sprintf(
        'User logout: ID=%s, Email=%s, IP=%s',
        $_SESSION['user_id'],
        $_SESSION['user_email'],
        $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ));
    
    // Log the activity
    if (function_exists('log_activity')) {
        log_activity(
            $_SESSION['user_id'],
            'logout',
            [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'referer' => $_SERVER['HTTP_REFERER'] ?? 'direct'
            ]
        );
    }
}

// Store the redirect URL (default to login page)
$redirect = '/c/zanvarsity/html/login.php';

// Check for a valid redirect URL in the request
if (isset($_GET['redirect']) && filter_var($_GET['redirect'], FILTER_VALIDATE_URL)) {
    $parsed_url = parse_url($_GET['redirect']);
    // Only allow redirects within our domain
    if (str_ends_with($parsed_url['host'] ?? '', 'zanvarsity.local') || 
        $parsed_url['host'] === 'localhost') {
        $redirect = $_GET['redirect'];
    }
}

// Unset all session variables
$_SESSION = [];

// Clear the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        [
            'expires' => time() - 42000,
            'path' => $params["path"],
            'domain' => $params["domain"],
            'secure' => $params["secure"],
            'httponly' => true,
            'samesite' => 'Lax'
        ]
    );
}

// Clear any existing output buffers
while (ob_get_level() > 0) {
    ob_end_clean();
}

// Completely destroy the session
if (session_destroy()) {
    // Start a new session to prevent session fixation
    session_start();
    
    // Generate a new session ID and delete the old one
    session_regenerate_id(true);
    
    // Set security headers
    header_remove('X-Powered-By');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Prevent caching of the logout page
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Add a small delay to prevent timing attacks
    usleep(rand(100000, 500000)); // 0.1 - 0.5 second delay
    
    // Add a success message to the session
    session_start();
    $_SESSION['success_message'] = 'You have been successfully logged out.';
    
    // Redirect to the login page or the specified redirect URL
    header('Location: ' . $redirect, true, 303);
    exit();
} else {
    // Log the error if session destruction fails
    error_log('Failed to destroy session for user: ' . 
             ($_SESSION['user_email'] ?? 'unknown') . 
             ' from IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    
    // If session destruction fails, try to clear the session data manually
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Force redirect to login page
    header('Location: ' . $redirect . '?error=logout_failed', true, 303);
    exit();
}

// Redirect to login page with success message
header("Location: /c/zanvarsity/html/login.php?logout=success");
exit();
?>
