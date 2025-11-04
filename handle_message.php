<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
require_once __DIR__ . '/includes/auth_functions.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/message_functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: /c/zanvarsity/html/sign-in.php');
    exit();
}

// Get current user info
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
        !hash_equals((string)$_SESSION['csrf_token'], (string)$_POST['csrf_token'])) {
        $_SESSION['error'] = 'Invalid security token. Please try again.';
        header('Location: /c/zanvarsity/html/my-account.php?tab=messages');
        exit();
    }

    // Validate required fields
    $errors = [];
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $recipient_id = $_POST['recipient_id'] ?? null;
    $is_broadcast = isset($_POST['is_broadcast']) && $_POST['is_broadcast'] === '1';

    if (empty($subject)) {
        $errors[] = 'Subject is required';
    }
    
    if (empty($message)) {
        $errors[] = 'Message is required';
    }
    
    // Validate recipient if not a broadcast
    if (!$is_broadcast && empty($recipient_id)) {
        $errors[] = 'Please select a recipient or choose to send to all users';
    }
    
    // Check permissions
    if ($user_role === 'student' && !$is_broadcast) {
        $errors[] = 'You do not have permission to send direct messages';
    }
    
    // If no errors, send the message
    if (empty($errors)) {
        $result = send_message(
            $user_id,
            $is_broadcast ? null : $recipient_id,
            $subject,
            $message,
            $conn
        );
        
        if ($result === true) {
            $_SESSION['success'] = 'Message sent successfully!';
        } else {
            $_SESSION['error'] = 'Failed to send message: ' . $result;
        }
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
    }
    
    // Redirect back to messages
    header('Location: /c/zanvarsity/html/my-account.php?tab=messages');
    exit();
}

// If not a POST request, redirect to messages
header('Location: /c/zanvarsity/html/my-account.php?tab=messages');
exit();
?>
