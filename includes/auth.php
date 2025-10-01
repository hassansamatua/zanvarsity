<?php
/**
 * Authentication and Authorization Functions
 */

// Start secure session
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // Set secure session parameters
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
        ini_set('session.cookie_samesite', 'Lax');
        
        session_name('zanvarsity_session');
        session_start();
    }
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Check if user has admin role
function isAdmin() {
    return isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'super_admin']);
}

// Require login
function requireLogin($redirect = '/c/zanvarsity/html/login.php') {
    if (!isLoggedIn()) {
        // Store the current URL for redirection after login
        if (!empty($_SERVER['REQUEST_URI'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        }
        
        header('Location: ' . $redirect);
        exit();
    }
}

// Require admin access
function requireAdmin($redirect = '/c/zanvarsity/html/my-account.php') {
    requireLogin();
    
    if (!isAdmin()) {
        $_SESSION['error'] = 'You do not have permission to access this page.';
        header('Location: ' . $redirect);
        exit();
    }
}

// Redirect if already logged in
function redirectIfLoggedIn($location = '/c/zanvarsity/html/my-account.php') {
    if (isLoggedIn()) {
        header('Location: ' . $location);
        exit();
    }
}

// Set error message
function setError($message) {
    $_SESSION['error'] = $message;
}

// Get and clear error message
function getError() {
    $message = $_SESSION['error'] ?? '';
    unset($_SESSION['error']);
    return $message;
}

// Set success message
function setSuccess($message) {
    $_SESSION['success'] = $message;
}

// Get and clear success message
function getSuccess() {
    $message = $_SESSION['success'] ?? '';
    unset($_SESSION['success']);
    return $message;
}

// Initialize authentication
startSecureSession();
