<?php
// Database configuration
require_once __DIR__ . '/includes/db_connect.php';

// SQL to create table
$sql = "
CREATE TABLE IF NOT EXISTS `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `location` varchar(255) DEFAULT NULL,
  `announcement_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_announcement_date` (`announcement_date`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data if table is empty
INSERT INTO `announcements` (`title`, `description`, `location`, `announcement_date`, `is_active`) 
SELECT 'Welcome to ZANVARSITY', 'We are excited to welcome you to our university', 'Main Campus', CURDATE(), 1
WHERE NOT EXISTS (SELECT 1 FROM `announcements` LIMIT 1);
";

// Execute multi-query
if ($conn->multi_query($sql)) {
    echo "Announcements table created/verified successfully. <a href='/zanvarsity/html/'>Go to Homepage</a>";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
