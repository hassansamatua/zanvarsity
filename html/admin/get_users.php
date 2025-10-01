<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/auth_functions.php';

// Verify admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

// Verify CSRF token for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

// Get request parameters
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? $conn->real_escape_string($_POST['search']['value']) : '';
$orderColumn = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir = isset($_POST['order'][0]['dir']) ? strtoupper($_POST['order'][0]['dir']) : 'DESC';

// Column mapping
$columns = [
    0 => 'id',
    1 => 'first_name',
    2 => 'email',
    3 => 'role',
    4 => 'status',
    5 => 'last_login'
];

// Order by
$orderBy = $columns[$orderColumn] . ' ' . $orderDir;

// Build the query
$query = "SELECT id, first_name, last_name, email, role, status, last_login, created_at 
          FROM users 
          WHERE 1=1";

// Add search condition
if (!empty($search)) {
    $query .= " AND (first_name LIKE '%$search%' 
                   OR last_name LIKE '%$search%' 
                   OR email LIKE '%$search%' 
                   OR role LIKE '%$search%')";
}

// Get total records
$totalRecords = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];

// Get filtered count
$filteredQuery = str_replace("SELECT id, first_name, last_name, email, role, status, last_login, created_at", 
                            "SELECT COUNT(*) as count", $query);
$filteredResult = $conn->query($filteredQuery);
$filteredRecords = $filteredResult ? $filteredResult->fetch_assoc()['count'] : 0;

// Add order by and limit
$query .= " ORDER BY $orderBy LIMIT $start, $length";

// Execute query
$result = $conn->query($query);

// Prepare data
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'id' => $row['id'],
        'first_name' => $row['first_name'],
        'last_name' => $row['last_name'],
        'email' => $row['email'],
        'role' => ucfirst($row['role']),
        'status' => $row['status'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>',
        'last_login' => $row['last_login'] ? date('Y-m-d H:i:s', strtotime($row['last_login'])) : 'Never',
        'created_at' => date('Y-m-d', strtotime($row['created_at']))
    ];
}

// Return JSON response
echo json_encode([
    'draw' => $draw,
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $filteredRecords,
    'data' => $data
]);

// Close connection
$conn->close();
?>
