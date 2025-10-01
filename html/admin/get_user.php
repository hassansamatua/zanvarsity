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

// Get user ID from request
$userId = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$userId) {
    echo json_encode(["success" => false, "message" => "Invalid user ID"]);
    exit();
}

try {
    // Prepare and execute query
    $query = "SELECT id, first_name, last_name, email, role, is_active FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "User not found"]);
        exit();
    }
    
    $user = $result->fetch_assoc();
    echo json_encode(["success" => true, "data" => $user]);
    
} catch (Exception $e) {
    error_log("Error fetching user: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Database error"]);
}
?>
