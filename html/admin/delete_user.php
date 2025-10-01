<?php
// Start session
session_start();

// Check if user is logged in and has admin privileges
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "admin") {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit();
}

// Include database connection
require_once "../../includes/db.php";

// Initialize response array
$response = ["success" => false, "message" => ""];

// Get user ID from request
$userId = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$userId) {
    $response["message"] = "Invalid user ID";
    echo json_encode($response);
    exit();
}

// Prevent deleting the current user
if ($userId == $_SESSION["user_id"]) {
    $response["message"] = "You cannot delete your own account";
    echo json_encode($response);
    exit();
}

try {
    // Prepare and execute delete query
    $query = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        $response["success"] = true;
        $response["message"] = "User deleted successfully";
    } else {
        $response["message"] = "Error deleting user";
    }
    
} catch (Exception $e) {
    error_log("Error deleting user: " . $e->getMessage());
    $response["message"] = "Database error";
}

echo json_encode($response);
?>
