<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define root path - point to zanvarsity directory
define('ROOT_PATH', dirname(dirname(dirname(__DIR__))));

// Set page title
$page_title = 'User Management';
$page_description = 'Manage system users and their permissions';

// Include necessary files
require_once ROOT_PATH . '/zanvarsity/includes/auth_functions.php';
require_once ROOT_PATH . '/zanvarsity/includes/database.php';

// Check if user is logged in
require_login();

// Get user information from session
$user_id = $_SESSION['user_id'] ?? null;
$user_email = $_SESSION['email'] ?? 'Guest';
