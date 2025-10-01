<?php
/**
 * Authentication and Security Functions
 * 
 * This file contains functions for user authentication, session management,
 * and security-related operations.
 */

// Load configuration
require_once __DIR__ . '/../config.php';

// Include database connection
require_once __DIR__ . '/database.php';

// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    // Session security settings
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    ini_set('session.cookie_samesite', 'Lax');
    
    // Set session name and start session
    session_name('zanvarsity_session');
    session_start();
    
    // Regenerate session ID to prevent session fixation
    if (empty($_SESSION['last_activity'])) {
        session_regenerate_id(true);
    }
}

// Error reporting - only show errors in development
if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Function to sanitize user input
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    return $data;
}

// Function to validate email
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Function to generate a secure token
function generate_token($length = 32) {
    if (!function_exists('random_bytes')) {
        throw new RuntimeException('random_bytes function not available');
    }
    return bin2hex(random_bytes($length));
}

// Function to hash password
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

// Function to verify password
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

// Function to check password strength
function is_strong_password($password) {
    // At least 8 characters, 1 uppercase, 1 lowercase, 1 number, 1 special character
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s]).{8,}$/', $password);
}

/**
 * Authenticate user with email and password
 * 
 * @param string $email User's email address
 * @param string $password User's password (plain text)
 * @return array|bool User data array on success, false on failure
 */
function authenticate_user($email, $password) {
    global $conn;
    
    // Validate input
    if (empty($email) || empty($password)) {
        error_log("Authentication failed: Empty email or password");
        return false;
    }
    
    // Sanitize and validate email
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error_log("Authentication failed: Invalid email format - " . $email);
        return false;
    }
    
    // Prepare SQL with additional security checks
    $sql = "SELECT id, email, password, role, first_name, last_name, status, 
                   failed_login_attempts, last_failed_login, account_locked_until
            FROM users 
            WHERE email = ? 
            LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Prepare failed: " . ($conn->error ?? 'Unknown error'));
        return false;
    }
    
    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return false;
    }
    
    $result = $stmt->get_result();
    
    // Check if user exists
    if ($result->num_rows === 0) {
        // Log failed login attempt with IP
        error_log(sprintf(
            'Failed login: No user found with email %s from IP %s',
            $email,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ));
        return false;
    }
    
    // Get user data
    $user = $result->fetch_assoc();
    
    // Check if account is locked
    if (!empty($user['account_locked_until']) && strtotime($user['account_locked_until']) > time()) {
        error_log(sprintf(
            'Account locked for user %s until %s',
            $email,
            $user['account_locked_until']
        ));
        return false;
    }
    
    // Check if account is active
    if ($user['status'] != 1) {
        error_log(sprintf(
            'Login attempt for inactive account: %s (status: %d)',
            $email,
            $user['status']
        ));
        return false;
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        // Update failed login attempts
        $failed_attempts = $user['failed_login_attempts'] + 1;
        $lock_until = null;
        
        // Lock account after 5 failed attempts for 15 minutes
        if ($failed_attempts >= 5) {
            $lock_until = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            error_log(sprintf(
                'Account locked for user %s due to too many failed attempts',
                $email
            ));
        }
        
        // Update failed login attempts in database
        $update_sql = "UPDATE users SET 
                      failed_login_attempts = ?, 
                      last_failed_login = NOW(),
                      account_locked_until = ?
                      WHERE email = ?";
        
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("iss", $failed_attempts, $lock_until, $email);
        $update_stmt->execute();
        
        error_log(sprintf(
            'Invalid password for user %s (attempt %d) from IP %s',
            $email,
            $failed_attempts,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ));
        
        return false;
    }
    
    // Reset failed login attempts on successful login
    if ($user['failed_login_attempts'] > 0 || $user['account_locked_until'] !== null) {
        $reset_sql = "UPDATE users SET 
                     failed_login_attempts = 0, 
                     account_locked_until = NULL
                     WHERE id = ?";
        
        $reset_stmt = $conn->prepare($reset_sql);
        $reset_stmt->bind_param("i", $user['id']);
        $reset_stmt->execute();
    }
    
    // Update last login time
    $update_sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $user['id']);
    $update_stmt->execute();
    
    // Log successful login
    error_log(sprintf(
        'User %s logged in successfully from IP %s',
        $email,
        $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ));
    
    // Remove sensitive data before returning
    unset($user['password']);
    unset($user['failed_login_attempts']);
    unset($user['last_failed_login']);
    unset($user['account_locked_until']);
    
    return $user;
}

/**
 * Check if user is logged in and session is valid
 * 
 * @return bool True if user is logged in, false otherwise
 */
function is_logged_in() {
    // Check if session is active and user is authenticated
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return false;
    }
    
    // Check for session timeout (30 minutes of inactivity)
    $timeout = 1800; // 30 minutes in seconds
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
        // Session has expired
        session_unset();
        session_destroy();
        return false;
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
    
    // Check if user is authenticated
    if (!isset($_SESSION['user_id'], $_SESSION['user_email'])) {
        return false;
    }
    
    // Additional security: Verify session against database
    if (defined('VERIFY_SESSION_AGAINST_DB') && VERIFY_SESSION_AGAINST_DB === true) {
        return verify_session_integrity();
    }
    
    return true;
}

/**
 * Verify session integrity by checking against database
 * 
 * @return bool True if session is valid, false otherwise
 */
function verify_session_integrity() {
    global $conn;
    
    if (!isset($_SESSION['user_id'], $_SESSION['session_token'])) {
        return false;
    }
    
    $user_id = (int)$_SESSION['user_id'];
    $session_token = $_SESSION['session_token'];
    
    $sql = "SELECT id FROM user_sessions 
            WHERE user_id = ? AND session_token = ? AND expires_at > NOW()
            LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Session verification prepare failed: " . $conn->error);
        return false;
    }
    
    $stmt->bind_param("is", $user_id, $session_token);
    if (!$stmt->execute()) {
        error_log("Session verification execute failed: " . $stmt->error);
        return false;
    }
    
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Function to require login
function require_login() {
    if (!is_logged_in()) {
        $login_url = '/c/zanvarsity/html/login.php?error=login_required';
        header("Location: $login_url");
        exit();
    }
}

// Function to redirect if already logged in
function redirect_if_logged_in($location = '/c/zanvarsity/html/my-account.php') {
    if (is_logged_in()) {
        header("Location: $location");
        exit();
    }
}

// Function to check if user is admin
function is_admin() {
    if (!is_logged_in()) {
        error_log('User not logged in - cannot be admin');
        return false;
    }
    
    // Debug log the session data
    error_log('Session data in is_admin(): ' . print_r($_SESSION, true));
    
    // Check if user has admin role in session
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        error_log('User is admin');
        return true;
    }
    
    error_log('User is not an admin. Role: ' . ($_SESSION['role'] ?? 'not set'));
    return false;
}

/**
 * Require admin access
 * Redirects to login if not logged in, or to home page if not admin
 */
function require_admin() {
    require_login(); // First make sure user is logged in
    
    if (!is_admin()) {
        $_SESSION['error'] = 'You do not have permission to access this page';
        header('Location: /zanvarsity/html/my-account.php');
        exit();
    }
}

// Security-related session initialization
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Set last activity time for session timeout
if (!isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time();
}

// Regenerate session ID periodically to prevent session fixation
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
    // Regenerate session ID every 30 minutes
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// Function to validate CSRF token
function validate_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Function to get CSRF token HTML input
function csrf_token_field() {
    return '<input type="hidden" name="csrf_token" value="' . 
           htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') . 
           '">';
}

// Function to securely destroy session
function destroy_session() {
    // Unset all session variables
    $_SESSION = [];
    
    // Delete the session cookie
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
}

// Function to safely redirect
function safe_redirect($url, $status_code = 302) {
    // Remove any existing output
    if (headers_sent()) {
        echo "<script>window.location.href='$url';</script>";
        exit();
    }
    
    // Sanitize URL
    $url = filter_var($url, FILTER_SANITIZE_URL);
    
    // Set header and exit
    header("Location: $url", true, $status_code);
    exit();
}

// Function to log user activity
function log_activity($user_id, $action, $details = []) {
    global $conn;
    
    $sql = "INSERT INTO user_activity_log 
            (user_id, action, ip_address, user_agent, details, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Failed to prepare activity log statement: " . $conn->error);
        return false;
    }
    
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $details_json = !empty($details) ? json_encode($details) : null;
    
    $stmt->bind_param("issss", $user_id, $action, $ip_address, $user_agent, $details_json);
    
    if (!$stmt->execute()) {
        error_log("Failed to log activity: " . $stmt->error);
        return false;
    }
    
    return true;
}
?>
