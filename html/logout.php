<?php
/**
 * Logout Handler
 * 
 * This script handles user logout by destroying the session and related cookies.
 * It also provides protection against session fixation attacks.
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Set secure session parameters
    $session_name = 'zanvarsity_session';
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    $httponly = true;
    $samesite = 'Lax';
    
    // Set session cookie parameters
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params([
        'lifetime' => 0,  // Until the browser is closed
        'path' => '/',
        'domain' => $cookieParams['domain'],
        'secure' => $secure,
        'httponly' => $httponly,
        'samesite' => $samesite
    ]);
    
    session_name($session_name);
    session_start();
}

// Store the redirect URL if provided (only allow relative URLs for security)
$redirect = '/';
if (isset($_GET['redirect'])) {
    $redirect = filter_var($_GET['redirect'], FILTER_SANITIZE_URL);
    // Only allow relative URLs
    if (strpos($redirect, 'http') === 0 || strpos($redirect, '//') === 0) {
        $redirect = '/';
    }
    // Ensure the redirect is within our application
    if (strpos($redirect, '/c/zanvarsity/') !== 0) {
        $redirect = '/';
    }
}

// Unset all session variables
$_SESSION = [];

// If it's desired to kill the session, also delete the session cookie
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
            'httponly' => $params["httponly"],
            'samesite' => $params['samesite'] ?? 'Lax'
        ]
    );
}

// Finally, destroy the session
session_destroy();

// Clear any auth cookies (if used)
setcookie('remember_me', '', [
    'expires' => time() - 3600,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => $secure,
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Clear browser cache
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Redirect to login page with a success message
header('Location: /c/zanvarsity/html/login.php?logout=1&redirect=' . urlencode($redirect));
exit();
