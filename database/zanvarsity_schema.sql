-- ZAN University Database Schema
-- Created: 2025-10-01

-- Drop database if exists and create a new one
DROP DATABASE IF EXISTS zanvarsity;
CREATE DATABASE zanvarsity;
USE zanvarsity;

-- Users Table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role ENUM('admin', 'staff', 'student', 'faculty') NOT NULL,
    profile_image VARCHAR(255) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login DATETIME DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Departments Table
CREATE TABLE departments (
    department_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(10) UNIQUE NOT NULL,
    description TEXT,
    head_of_department_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (head_of_department_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Programs Table
CREATE TABLE programs (
    program_id INT AUTO_INCREMENT PRIMARY KEY,
    department_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(10) UNIQUE NOT NULL,
    description TEXT,
    duration_years INT NOT NULL DEFAULT 4,
    degree_type ENUM('Certificate', 'Diploma', 'Bachelor', 'Master', 'PhD') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(department_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Courses Table
CREATE TABLE courses (
    course_id INT AUTO_INCREMENT PRIMARY KEY,
    program_id INT NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    credit_hours INT NOT NULL,
    semester INT NOT NULL,
    is_core BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (program_id) REFERENCES programs(program_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Student Enrollment
CREATE TABLE student_enrollments (
    enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    program_id INT NOT NULL,
    enrollment_date DATE NOT NULL,
    graduation_date DATE,
    status ENUM('active', 'graduated', 'withdrawn', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (program_id) REFERENCES programs(program_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Course Registration
CREATE TABLE course_registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    enrollment_id INT NOT NULL,
    course_id INT NOT NULL,
    semester INT NOT NULL,
    academic_year VARCHAR(10) NOT NULL,
    registration_date DATE NOT NULL,
    status ENUM('registered', 'dropped', 'completed', 'failed') DEFAULT 'registered',
    grade VARCHAR(2) DEFAULT NULL,
    points DECIMAL(3,2) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (enrollment_id) REFERENCES student_enrollments(enrollment_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Events Table
CREATE TABLE events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    location VARCHAR(255),
    image_url VARCHAR(255) DEFAULT NULL,
    event_type ENUM('academic', 'social', 'sports', 'workshop', 'conference') NOT NULL,
    status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- News Table
CREATE TABLE news (
    news_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image_url VARCHAR(255) DEFAULT NULL,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    published_at DATETIME DEFAULT NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Gallery Table
CREATE TABLE gallery (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT,
    image_url VARCHAR(255) NOT NULL,
    caption VARCHAR(255) DEFAULT NULL,
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Notifications Table
CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    link VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Settings Table
CREATE TABLE settings (
    setting_id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_group VARCHAR(50) DEFAULT 'general',
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- SAMPLE DATA INSERTION
-- =============================================

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password_hash, email, first_name, last_name, role, is_active) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@zanvarsity.edu', 'System', 'Administrator', 'admin', TRUE);

-- Insert sample departments
INSERT INTO departments (name, code, description) VALUES
('Computer Science', 'CS', 'Department of Computer Science and Information Technology'),
('Business Administration', 'BUS', 'Department of Business and Management'),
('Engineering', 'ENG', 'Department of Engineering and Technology'),
('Arts and Humanities', 'ARTS', 'Department of Arts and Humanities');

-- Insert sample programs
INSERT INTO programs (department_id, name, code, description, duration_years, degree_type) VALUES
(1, 'Bachelor of Science in Computer Science', 'BSCS', 'Undergraduate program in Computer Science', 4, 'Bachelor'),
(1, 'Master of Science in Data Science', 'MSDS', 'Graduate program in Data Science', 2, 'Master'),
(2, 'Bachelor of Business Administration', 'BBA', 'Undergraduate program in Business Administration', 4, 'Bachelor'),
(3, 'Bachelor of Engineering in Civil', 'BECIV', 'Undergraduate program in Civil Engineering', 4, 'Bachelor');

-- Insert sample courses
INSERT INTO courses (program_id, code, name, description, credit_hours, semester, is_core) VALUES
(1, 'CS101', 'Introduction to Programming', 'Fundamentals of programming using Python', 3, 1, TRUE),
(1, 'CS201', 'Data Structures', 'Study of fundamental data structures', 4, 2, TRUE),
(2, 'DS501', 'Machine Learning', 'Introduction to machine learning algorithms', 3, 1, TRUE),
(3, 'BUS101', 'Principles of Management', 'Introduction to management principles', 3, 1, TRUE);

-- Insert sample events
INSERT INTO events (title, description, start_date, end_date, location, event_type, status, created_by) VALUES
('Orientation Day 2025', 'Welcome event for new students', '2025-10-15 09:00:00', '2025-10-15 16:00:00', 'Main Auditorium', 'academic', 'upcoming', 1),
('Annual Science Fair', 'Showcase of student projects', '2025-11-20 10:00:00', '2025-11-22 18:00:00', 'Science Building', 'academic', 'upcoming', 1),
('Sports Week', 'Annual sports competition', '2025-12-01 08:00:00', '2025-12-05 17:00:00', 'University Stadium', 'sports', 'upcoming', 1);

-- Insert sample news
INSERT INTO news (title, content, status, published_at, created_by) VALUES
('New Research Center Opened', 'The university has opened a new research center for artificial intelligence.', 'published', NOW(), 1),
('Upcoming Semester Registration', 'Registration for the next semester begins on November 1st.', 'published', NOW(), 1);

-- Insert sample settings
INSERT INTO settings (setting_key, setting_value, setting_group, is_public) VALUES
('site_name', 'ZAN University', 'general', TRUE),
('site_description', 'Excellence in Education', 'general', TRUE),
('contact_email', 'info@zanvarsity.edu', 'contact', TRUE),
('maintenance_mode', '0', 'system', FALSE);

-- Add indexes for better performance
CREATE INDEX idx_events_dates ON events(start_date, end_date);
CREATE INDEX idx_news_status ON news(status, published_at);
CREATE INDEX idx_users_email ON users(email);

-- Create a view for active students
CREATE VIEW active_students AS
SELECT u.user_id, u.first_name, u.last_name, u.email, p.name AS program, d.name AS department
FROM users u
JOIN student_enrollments se ON u.user_id = se.student_id
JOIN programs p ON se.program_id = p.program_id
JOIN departments d ON p.department_id = d.department_id
WHERE se.status = 'active' AND u.role = 'student';

-- Create a view for upcoming events
CREATE VIEW upcoming_events AS
SELECT event_id, title, description, start_date, end_date, location, event_type
FROM events
WHERE status = 'upcoming' AND start_date >= NOW()
ORDER BY start_date ASC;
