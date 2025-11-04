<?php
// Set session configuration before starting session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_path', '/');

// Set session name and start session
session_name('zanvarsity_session');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include configuration and authentication functions
require_once __DIR__ . '/../includes/auth_functions.php';

// Debug: Log session status and variables
error_log('Session ID: ' . session_id());
error_log('Session Status: ' . session_status());
error_log('Session Data: ' . print_r($_SESSION, true));

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        header('Location: /c/zanvarsity/html/register-sign-in.php?error=invalid_csrf');
        exit();
    }

    // Get form data
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    // Validate input
    if (empty($email) || empty($password)) {
        header('Location: /c/zanvarsity/html/register-sign-in.php?error=empty_fields&email=' . urlencode($email));
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: /c/zanvarsity/html/register-sign-in.php?error=invalid_email');
        exit();
    }

    // Authenticate user
    $user = authenticate_user($email, $password);

    if ($user) {
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'] ?? 'user';
        $_SESSION['last_activity'] = time();
        
        // Debug: Log successful login
        error_log('User logged in: ' . $user['email']);
        error_log('Session after login: ' . print_r($_SESSION, true));
        
        // Force write and close session to ensure session data is saved
        session_write_close();
        
        // Regenerate session ID again after setting variables
        session_start();
        session_regenerate_id(true);

        // Set remember me cookie if requested
        if ($remember) {
            $token = generate_token();
            $expires = time() + (86400 * 30); // 30 days
            setcookie('remember_token', $token, $expires, '/', '', isset($_SERVER['HTTPS']), true);
            
            // Store token in database (you'll need to implement this)
            // save_remember_token($user['id'], $token, $expires);
        }
        
        // Redirect to dashboard or previous page
        $redirect = isset($_POST['redirect']) ? filter_var($_POST['redirect'], FILTER_SANITIZE_URL) : '/c/zanvarsity/html/my-account.php';
        
        // Ensure the redirect is within our domain for security
        $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
        $allowed_domains = ['/c/zanvarsity', '/zanvarsity'];
        
        $is_valid_redirect = false;
        foreach ($allowed_domains as $domain) {
            if (strpos($redirect, $domain) === 0) {
                $is_valid_redirect = true;
                break;
            }
        }
        
        if (!$is_valid_redirect) {
            $redirect = '/c/zanvarsity/html/my-account.php';
        }
        
        // Debug: Log redirect URL
        error_log('Redirecting to: ' . $redirect);
        
        // Clear output buffers and set headers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Perform the redirect
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Location: ' . $redirect, true, 302);
        exit();
    } else {
        // Authentication failed
        header('Location: /c/zanvarsity/html/register-sign-in.php?error=invalid_credentials&email=' . urlencode($email));
        exit();
    }
} else {
    // Not a POST request, redirect to login
    header('Location: /c/zanvarsity/html/register-sign-in.php');
    exit();
}
?>
