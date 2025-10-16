-- Update user roles and add faculty support
-- Run this SQL script to update the database structure

-- Add faculty column to users table if it doesn't exist
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS faculty VARCHAR(50) NULL AFTER role;

-- Create academic_content table for managing faculty and institute content
CREATE TABLE IF NOT EXISTS academic_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unit_code VARCHAR(20) NOT NULL,
    unit_type ENUM('faculty', 'institute') NOT NULL,
    content_type ENUM('welcome', 'vision', 'mission', 'about') NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_unit_content (unit_code, content_type),
    INDEX idx_unit_type (unit_type),
    INDEX idx_unit_code (unit_code)
);

-- Update existing user roles (optional - only if you want to migrate existing data)
-- UPDATE users SET role = 'admin' WHERE role = 'super_admin';
-- UPDATE users SET role = 'dean' WHERE role = 'lecturer';

-- Insert default content for faculties and institutes
INSERT IGNORE INTO academic_content (unit_code, unit_type, content_type, title, content, status, created_by) VALUES
-- Faculty content (faculties have departments)
('FBA', 'faculty', 'welcome', 'Welcome to Faculty of Business Administration', 'Welcome to the Faculty of Business Administration. We are committed to excellence in business education and developing future leaders.', 'published', 1),
('FBA', 'faculty', 'vision', 'Our Vision', 'To be a leading faculty in business education and entrepreneurship development.', 'published', 1),
('FBA', 'faculty', 'mission', 'Our Mission', 'To provide quality business education and develop ethical business leaders.', 'published', 1),
('FBA', 'faculty', 'about', 'About Us', 'The Faculty of Business Administration prepares students for the dynamic world of business and commerce.', 'published', 1),

('FLS', 'faculty', 'welcome', 'Welcome to Faculty of Law and Shariah', 'Welcome to the Faculty of Law and Shariah. Justice through legal excellence and Islamic jurisprudence.', 'published', 1),
('FLS', 'faculty', 'vision', 'Our Vision', 'To promote justice through comprehensive legal education and Islamic law.', 'published', 1),
('FLS', 'faculty', 'mission', 'Our Mission', 'To provide excellent legal education combining conventional and Islamic law.', 'published', 1),
('FLS', 'faculty', 'about', 'About Us', 'The Faculty of Law and Shariah is dedicated to legal education and Islamic jurisprudence.', 'published', 1),

('FASS', 'faculty', 'welcome', 'Welcome to Faculty of Arts and Social Sciences', 'Welcome to the Faculty of Arts and Social Sciences. Explore human culture, society, and creative expression.', 'published', 1),
('FASS', 'faculty', 'vision', 'Our Vision', 'To foster critical thinking and cultural understanding in arts and social sciences.', 'published', 1),
('FASS', 'faculty', 'mission', 'Our Mission', 'To provide comprehensive education in arts, humanities, and social sciences.', 'published', 1),
('FASS', 'faculty', 'about', 'About Us', 'The Faculty of Arts and Social Sciences celebrates human creativity and social understanding.', 'published', 1),

('FOE', 'faculty', 'welcome', 'Welcome to Faculty of Engineering', 'Welcome to the Faculty of Engineering. Innovation through engineering excellence and technological advancement.', 'published', 1),
('FOE', 'faculty', 'vision', 'Our Vision', 'To be a leading faculty in engineering education and technological innovation.', 'published', 1),
('FOE', 'faculty', 'mission', 'Our Mission', 'To provide quality engineering education and conduct cutting-edge research.', 'published', 1),
('FOE', 'faculty', 'about', 'About Us', 'The Faculty of Engineering has been at the forefront of technological advancement and innovation.', 'published', 1),

('FOHAS', 'faculty', 'welcome', 'Welcome to Faculty of Health and Allied Sciences', 'Welcome to the Faculty of Health and Allied Sciences. Advancing health through education and research.', 'published', 1),
('FOHAS', 'faculty', 'vision', 'Our Vision', 'To advance healthcare through comprehensive health sciences education.', 'published', 1),
('FOHAS', 'faculty', 'mission', 'Our Mission', 'To train competent healthcare professionals and advance health sciences knowledge.', 'published', 1),
('FOHAS', 'faculty', 'about', 'About Us', 'The Faculty of Health and Allied Sciences is committed to excellence in health education and research.', 'published', 1),

('FOS', 'faculty', 'welcome', 'Welcome to Faculty of Science', 'Welcome to the Faculty of Science. Discover the wonders of scientific exploration and innovation.', 'published', 1),
('FOS', 'faculty', 'vision', 'Our Vision', 'To advance scientific knowledge and understanding through research and education.', 'published', 1),
('FOS', 'faculty', 'mission', 'Our Mission', 'To provide excellent science education and research opportunities.', 'published', 1),
('FOS', 'faculty', 'about', 'About Us', 'The Faculty of Science is dedicated to scientific excellence and discovery.', 'published', 1),

-- Institute content (institutes have no departments)
('IPGSR', 'institute', 'welcome', 'Welcome to Institute of Postgraduate Studies and Research', 'Welcome to the Institute of Postgraduate Studies and Research. Excellence in advanced education and research.', 'published', 1),
('IPGSR', 'institute', 'vision', 'Our Vision', 'To be a center of excellence in postgraduate education and research.', 'published', 1),
('IPGSR', 'institute', 'mission', 'Our Mission', 'To provide high-quality postgraduate programs and promote research excellence.', 'published', 1),
('IPGSR', 'institute', 'about', 'About Us', 'The Institute of Postgraduate Studies and Research coordinates advanced degree programs and research activities.', 'published', 1),

('IIBF', 'institute', 'welcome', 'Welcome to Institute of Islamic Banking and Finance', 'Welcome to the Institute of Islamic Banking and Finance. Excellence in Shariah-compliant financial education.', 'published', 1),
('IIBF', 'institute', 'vision', 'Our Vision', 'To be a leading institute in Islamic banking and finance education.', 'published', 1),
('IIBF', 'institute', 'mission', 'Our Mission', 'To provide comprehensive education in Islamic banking and finance principles.', 'published', 1),
('IIBF', 'institute', 'about', 'About Us', 'The Institute of Islamic Banking and Finance specializes in Shariah-compliant financial education and research.', 'published', 1),

('ICE', 'institute', 'welcome', 'Welcome to Institute of Continuing Education', 'Welcome to the Institute of Continuing Education. Lifelong learning and professional development.', 'published', 1),
('ICE', 'institute', 'vision', 'Our Vision', 'To promote lifelong learning and professional development for all.', 'published', 1),
('ICE', 'institute', 'mission', 'Our Mission', 'To provide flexible and accessible continuing education programs.', 'published', 1),
('ICE', 'institute', 'about', 'About Us', 'The Institute of Continuing Education offers flexible learning opportunities for professional and personal development.', 'published', 1);
