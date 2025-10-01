<?php
require_once __DIR__ . '/../../../includes/auth_functions.php';
require_once __DIR__ . '/../../../includes/database.php';
require_login();
require_admin();

header('Content-Type: application/json');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid publication ID']);
    exit();
}

$publication_id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM publications WHERE id = ?");
$stmt->bind_param("i", $publication_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Publication not found']);
    exit();
}

echo json_encode($result->fetch_assoc());
?>
