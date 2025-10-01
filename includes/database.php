<?php
/**
 * Database Connection and Initialization
 * 
 * This file handles the database connection and ensures all required tables exist.
 * It's included by other PHP files that need database access.
 */

// Only proceed if we're not already in the middle of including config.php
if (!defined('DB_HOST')) {
    $config_file = __DIR__ . '/../config.php';
    if (!file_exists($config_file)) {
        die('Config file not found at: ' . realpath($config_file));
    }
    require_once $config_file;
}

// Function to establish database connection
function getDbConnection() {
    static $conn = null;
    
    if ($conn === null) {
        // If we get here, we need to create a new connection
        $host = defined('DB_HOST') ? DB_HOST : 'localhost';
        $user = defined('DB_USER') ? DB_USER : 'root';
        $pass = defined('DB_PASS') ? DB_PASS : '';
        $dbname = defined('DB_NAME') ? DB_NAME : 'zanvarsity_db';
        
        // Create connection without selecting database first
        $conn = new mysqli($host, $user, $pass);
        
        if ($conn->connect_error) {
            error_log("Database connection failed: " . $conn->connect_error);
            die("Database connection failed. Please try again later.");
        }
        
        // Set charset to ensure proper encoding
        $conn->set_charset("utf8mb4");
        
        // Create database if not exists
        $sql = "CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if (!$conn->query($sql)) {
            die("Error creating database: " . $conn->error);
        }
        
        // Select the database
        if (!$conn->select_db($dbname)) {
            die("Error selecting database: " . $conn->error);
        }
    }
    
    return $conn;
}

// Get database connection
$conn = getDbConnection();

// Function to execute a query and handle errors
function executeQuery($conn, $sql, $errorMessage) {
    $result = $conn->query($sql);
    if ($result === FALSE) {
        error_log("Query failed: $sql - " . $conn->error);
        die("$errorMessage: " . $conn->error);
    }
    return $result;
}

// Set charset to ensure proper encoding
$conn->set_charset("utf8mb4");

// Create users table if not exists
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    country VARCHAR(50),
    postal_code VARCHAR(20),
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other', 'prefer_not_to_say') DEFAULT 'prefer_not_to_say',
    profile_image VARCHAR(255),
    bio TEXT,
    role ENUM('super_admin', 'admin', 'instructor', 'student', 'staff', 'parent') DEFAULT 'student',
    department VARCHAR(100),
    student_id VARCHAR(50) UNIQUE,
    staff_id VARCHAR(50) UNIQUE,
    is_active BOOLEAN DEFAULT TRUE,
    is_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(255),
    reset_token VARCHAR(255),
    reset_token_expires DATETIME,
    last_login DATETIME,
    last_ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
executeQuery($conn, $sql, "Error creating users table");

// Create events table
$sql = "CREATE TABLE IF NOT EXISTS events (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    event_date DATETIME NOT NULL,
    location VARCHAR(255),
    image_url VARCHAR(255),
    created_by INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
)";
executeQuery($conn, $sql, "Error creating events table");

// Create news table
$sql = "CREATE TABLE IF NOT EXISTS news (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image_url VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    published_at DATETIME,
    created_by INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
)";
executeQuery($conn, $sql, "Error creating news table");

// Create announcements table
$sql = "CREATE TABLE IF NOT EXISTS announcements (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    is_important BOOLEAN DEFAULT FALSE,
    created_by INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
)";
executeQuery($conn, $sql, "Error creating announcements table");

// Create downloads table
$sql = "CREATE TABLE IF NOT EXISTS downloads (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    file_url VARCHAR(255) NOT NULL,
    file_size INT(11),
    file_type VARCHAR(100),
    download_count INT(11) DEFAULT 0,
    category VARCHAR(100),
    created_by INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
)";
executeQuery($conn, $sql, "Error creating downloads table");

// Create faculties table
$sql = "CREATE TABLE IF NOT EXISTS faculties (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    dean_name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
executeQuery($conn, $sql, "Error creating faculties table");

// Create departments table
$sql = "CREATE TABLE IF NOT EXISTS departments (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    faculty_id INT(11) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    head_of_department VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculties(id) ON DELETE CASCADE
)";
executeQuery($conn, $sql, "Error creating departments table");

// Create courses table
// Check if events table exists
$table_check = $conn->query("SHOW TABLES LIKE 'events'");

if ($table_check === false) {
    error_log("Error checking if events table exists: " . $conn->error);
} elseif ($table_check->num_rows == 0) {
    // Only create the table if it doesn't exist
    $sql = "
    SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
    SET time_zone = '+00:00';

    CREATE TABLE `events` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(255) NOT NULL,
      `description` text,
      `start_date` datetime NOT NULL,
      `end_date` datetime DEFAULT NULL,
      `location` varchar(255) DEFAULT NULL,
      `image_url` varchar(255) DEFAULT NULL,
      `status` enum('upcoming','ongoing','completed','cancelled') NOT NULL DEFAULT 'upcoming',
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    // Split the SQL into individual queries
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($queries as $query) {
        if (!empty($query)) {
            if ($conn->query($query) === FALSE) {
                error_log("Error executing query: " . $conn->error . "\nQuery: " . $query);
            }
        }
    }
}

$sql = "CREATE TABLE IF NOT EXISTS courses (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    department_id INT(11) NOT NULL,
    course_code VARCHAR(20) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    credit_hours INT(2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
)";
executeQuery($conn, $sql, "Error creating courses table");

// Create staff table
$sql = "CREATE TABLE IF NOT EXISTS staff (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    department_id INT(11),
    position VARCHAR(100),
    bio TEXT,
    image_url VARCHAR(255),
    is_teaching BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
)";
executeQuery($conn, $sql, "Error creating staff table");

// Create facilities table
$sql = "CREATE TABLE IF NOT EXISTS facilities (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type ENUM('library', 'cafeteria', 'sports', 'laboratory', 'other') NOT NULL,
    description TEXT,
    location VARCHAR(255),
    operating_hours TEXT,
    contact_info VARCHAR(255),
    image_url VARCHAR(255),
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
executeQuery($conn, $sql, "Error creating facilities table");

// Create student_organizations table
$sql = "CREATE TABLE IF NOT EXISTS student_organizations (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    founded_date DATE,
    president_name VARCHAR(100),
    email VARCHAR(100),
    faculty_advisor_id INT(11),
    logo_url VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_advisor_id) REFERENCES staff(id) ON DELETE SET NULL
)";
executeQuery($conn, $sql, "Error creating student_organizations table");

// Create user_logs table
$sql = "CREATE TABLE IF NOT EXISTS user_logs (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    action VARCHAR(50) NOT NULL,
    table_name VARCHAR(50),
    record_id INT(11),
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
executeQuery($conn, $sql, "Error creating user_logs table");

// Hash the admin password
$admin_email = 'hanscovd5@gmail.com';
$admin_password = password_hash('admin123', PASSWORD_DEFAULT);

// Check if admin user exists, if not create one
$sql = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $sql = "INSERT INTO users (email, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $admin_email, $admin_password);
    
    if ($stmt->execute() === FALSE) {
        die("Error creating admin user: " . $conn->error);
    }
}

$stmt->close();
?>
