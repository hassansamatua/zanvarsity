<?php
/**
 * Application Configuration
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Set secure session parameters
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    $httponly = true;
    $samesite = 'Lax';
    
    // Set session cookie parameters
    $cookieParams = [
        'lifetime' => 86400, // 24 hours
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => $secure,
        'httponly' => $httponly,
        'samesite' => $samesite
    ];
    
    session_set_cookie_params($cookieParams);
    session_name('zanvarsity_session');
    session_start();
}

// Base URL (without trailing slash)
$protocol = $secure ? 'https://' : 'http://';
$base_url = $protocol . $_SERVER['HTTP_HOST'] . '/c/zanvarsity';
$assets_url = $base_url . '/assets';

define('BASE_URL', $base_url);
define('ASSETS_URL', $assets_url);
define('SITE_URL', $base_url . '/html');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'zanvarsity');

// Session configuration
define('SESSION_NAME', 'zanvarsity_session');
define('SESSION_LIFETIME', 86400); // 24 hours

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Africa/Dar_es_Salaam');

// Set include path
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__);

// Set base URL in session for use in templates
$_SESSION['base_url'] = $base_url;
$_SESSION['assets_url'] = $assets_url;
