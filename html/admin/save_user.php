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

// Get form data
$action = $_POST["action"] ?? "";
$userId = $_POST["user_id"] ?? 0;
$firstName = trim($_POST["first_name"] ?? "");
$lastName = trim($_POST["last_name"] ?? "");
$email = filter_var(trim($_POST["email"] ?? ""), FILTER_VALIDATE_EMAIL);
$role = in_array($_POST["role"] ?? "", ["admin", "instructor", "student"]) ? $_POST["role"] : "";
$isActive = isset($_POST["is_active"]) ? 1 : 0;
$password = $_POST["password"] ?? "";

// Validate input
if (empty($firstName) || empty($lastName) || !$email || empty($role)) {
    $response["message"] = "All fields are required";
    echo json_encode($response);
    exit();
}

try {
    // Check if email already exists (for new users or when email is changed)
    if ($action === "add" || $action === "edit") {
        $query = "SELECT id FROM users WHERE email = ? AND id != ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $email, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $response["message"] = "Email already exists";
            echo json_encode($response);
            exit();
        }
    }
    
    // Prepare data for database
    if ($action === "add") {
        // Add new user
        if (empty($password)) {
            $response["message"] = "Password is required for new users";
            echo json_encode($response);
            exit();
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (first_name, last_name, email, password, role, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $firstName, $lastName, $email, $hashedPassword, $role, $isActive);
        $actionMessage = "User added successfully";
        
    } else {
        // Update existing user
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $query = "UPDATE users SET first_name = ?, last_name = ?, email = ?, password = ?, role = ?, is_active = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssssii", $firstName, $lastName, $email, $hashedPassword, $role, $isActive, $userId);
        } else {
            $query = "UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ?, is_active = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssii", $firstName, $lastName, $email, $role, $isActive, $userId);
        }
        $actionMessage = "User updated successfully";
    }
    
    // Execute the query
    if ($stmt->execute()) {
        $response["success"] = true;
        $response["message"] = $actionMessage;
    } else {
        $response["message"] = "Error saving user";
    }
    
} catch (Exception $e) {
    error_log("Error saving user: " . $e->getMessage());
    $response["message"] = "Database error";
}

echo json_encode($response);
?>
