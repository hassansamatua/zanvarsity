<?php
// Include the database connection file
require_once __DIR__ . '/includes/db_connect.php';

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully to database: " . DB_NAME . "<br><br>";

// Check if announcements table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'announcements'");
if ($tableCheck->num_rows == 0) {
    die("Error: The 'announcements' table does not exist in the database.");
}
echo "Announcements table exists.<br><br>";

// Count total announcements
$countResult = $conn->query("SELECT COUNT(*) as total FROM announcements");
$totalAnnouncements = $countResult->fetch_assoc()['total'];
echo "Total announcements in database: " . $totalAnnouncements . "<br><br>";

// Show sample data (first 5 records)
$result = $conn->query("SELECT * FROM announcements ORDER BY start_date DESC LIMIT 5");

if ($result->num_rows > 0) {
    echo "<h3>Sample Announcements (most recent 5):</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Title</th><th>Start Date</th><th>End Date</th><th>Status</th><th>Important</th></tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . htmlspecialchars($row["title"]) . "</td>";
        echo "<td>" . $row["start_date"] . "</td>";
        echo "<td>" . $row["end_date"] . "</td>";
        echo "<td>" . $row["status"] . "</td>";
        echo "<td>" . ($row["is_important"] ? 'Yes' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No announcements found in the database.";
}

$conn->close();
?>
