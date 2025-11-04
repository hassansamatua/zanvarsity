<?php
// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include configuration and authentication functions
require_once __DIR__ . '/includes/auth_functions.php';

// Unset all session variables
$_SESSION = array();

// Clear the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Clear any other custom session-related cookies
setcookie('remember_me', '', time() - 3600, '/');

// Redirect to login page with a success message
$redirect_url = '/c/zanvarsity/html/sign-in.php?logged_out=1';
header('Location: ' . $redirect_url);
exit();