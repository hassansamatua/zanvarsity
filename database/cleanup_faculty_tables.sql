-- Database Cleanup Script for Faculty Tables
-- This script will consolidate and clean up multiple faculty tables

-- First, let's check what faculty tables exist and their structure
-- Run these queries first to see what you have:
-- SHOW TABLES LIKE '%faculty%';
-- DESCRIBE faculty;
-- DESCRIBE faculties;
-- DESCRIBE faculty_tbl;

-- ==========================================
-- STEP 1: Create/Update the main faculties table (standardized)
-- ==========================================

-- First, check if faculties table exists and create it if not
CREATE TABLE IF NOT EXISTS faculties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add missing columns if they don't exist
ALTER TABLE faculties 
ADD COLUMN IF NOT EXISTS code VARCHAR(20) NULL AFTER name,
ADD COLUMN IF NOT EXISTS description TEXT NULL AFTER code,
ADD COLUMN IF NOT EXISTS dean_id INT NULL AFTER description,
ADD COLUMN IF NOT EXISTS established_year YEAR NULL AFTER dean_id,
ADD COLUMN IF NOT EXISTS contact_email VARCHAR(100) NULL AFTER established_year,
ADD COLUMN IF NOT EXISTS contact_phone VARCHAR(20) NULL AFTER contact_email,
ADD COLUMN IF NOT EXISTS building VARCHAR(100) NULL AFTER contact_phone,
ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT TRUE AFTER building;

-- Add constraints if they don't exist
ALTER TABLE faculties 
ADD CONSTRAINT IF NOT EXISTS unique_faculty_name UNIQUE (name),
ADD CONSTRAINT IF NOT EXISTS unique_faculty_code UNIQUE (code);

-- Add foreign key if it doesn't exist (this will fail silently if users table doesn't have 'id' column)
-- ALTER TABLE faculties ADD CONSTRAINT fk_faculty_dean FOREIGN KEY (dean_id) REFERENCES users(id) ON DELETE SET NULL;

-- ==========================================
-- STEP 2: Insert standard faculty data
-- ==========================================

INSERT IGNORE INTO faculties (name, code, description, established_year, is_active) VALUES
('Faculty of Business Administration', 'FBA', 'Faculty of Business Administration', 2010, TRUE),
('Faculty of Law and Shariah', 'FLS', 'Faculty of Law and Shariah', 2010, TRUE),
('Faculty of Arts and Social Sciences', 'FASS', 'Faculty of Arts and Social Sciences', 2010, TRUE),
('Faculty of Engineering', 'FOE', 'Faculty of Engineering', 2010, TRUE),
('Faculty of Health and Allied Sciences', 'FOHAS', 'Faculty of Health and Allied Sciences', 2012, TRUE),
('Faculty of Science', 'FOS', 'Faculty of Science', 2015, TRUE),
('Institute of Postgraduate Studies and Research', 'IPGSR', 'Institute of Postgraduate Studies and Research', 2018, TRUE),
('Institute of Islamic Banking and Finance', 'IIBF', 'Institute of Islamic Banking and Finance', 2020, TRUE),
('Institute of Continuing Education', 'ICE', 'Institute of Continuing Education', 2020, TRUE);

-- ==========================================
-- STEP 3: Migrate data from old tables (if they exist)
-- ==========================================

-- Migrate from 'faculty' table if it exists
INSERT IGNORE INTO faculties (name, code, description, created_at)
SELECT 
    COALESCE(name, 'Unknown Faculty') as name,
    COALESCE(code, CONCAT('FAC', id)) as code,
    COALESCE(description, '') as description,
    COALESCE(created_at, NOW()) as created_at
FROM faculty 
WHERE EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'faculty' AND table_schema = DATABASE());

-- Migrate from 'faculty_tbl' table if it exists
INSERT IGNORE INTO faculties (name, code, description, created_at)
SELECT 
    COALESCE(faculty_name, name, 'Unknown Faculty') as name,
    COALESCE(faculty_code, code, CONCAT('FAC', id)) as code,
    COALESCE(description, '') as description,
    COALESCE(created_at, NOW()) as created_at
FROM faculty_tbl 
WHERE EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'faculty_tbl' AND table_schema = DATABASE());

-- ==========================================
-- STEP 4: Update related tables to use the new faculties table
-- ==========================================

-- Update users table to reference faculties instead of faculty
UPDATE users u
JOIN faculties f ON u.faculty = f.code
SET u.faculty = f.code
WHERE u.faculty IS NOT NULL;

-- Update departments table if it has faculty references
ALTER TABLE departments 
ADD COLUMN IF NOT EXISTS faculty_id INT NULL AFTER department_id,
ADD CONSTRAINT fk_dept_faculty FOREIGN KEY (faculty_id) REFERENCES faculties(id) ON DELETE SET NULL;

-- Map departments to faculties
UPDATE departments d
JOIN faculties f ON (
    (d.name LIKE '%Engineering%' AND f.code = 'ENG') OR
    (d.name LIKE '%Computer%' AND f.code = 'SCI') OR
    (d.name LIKE '%Science%' AND f.code = 'SCI') OR
    (d.name LIKE '%Business%' AND f.code = 'BUS') OR
    (d.name LIKE '%Arts%' AND f.code = 'ARTS') OR
    (d.name LIKE '%Medicine%' AND f.code = 'MED') OR
    (d.name LIKE '%Law%' AND f.code = 'LAW') OR
    (d.name LIKE '%Education%' AND f.code = 'EDU')
)
SET d.faculty_id = f.id;

-- Update staff table if it exists
UPDATE staff s
JOIN departments d ON s.department_id = d.department_id
SET s.faculty_id = d.faculty_id
WHERE EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'staff' AND table_schema = DATABASE())
AND d.faculty_id IS NOT NULL;

-- ==========================================
-- STEP 5: Update faculty_content table to use the new structure
-- ==========================================

-- Update faculty_content to reference faculties by code
UPDATE faculty_content fc
JOIN faculties f ON fc.faculty = f.code
SET fc.faculty = f.code
WHERE EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'faculty_content' AND table_schema = DATABASE());

-- ==========================================
-- STEP 6: Drop old faculty tables (ONLY AFTER CONFIRMING DATA IS MIGRATED)
-- ==========================================

-- IMPORTANT: Only run these DROP statements after confirming all data has been migrated
-- and you've backed up your database!

-- DROP TABLE IF EXISTS faculty;
-- DROP TABLE IF EXISTS faculty_tbl;

-- ==========================================
-- STEP 7: Create indexes for better performance
-- ==========================================

CREATE INDEX idx_faculties_code ON faculties(code);
CREATE INDEX idx_faculties_active ON faculties(is_active);
CREATE INDEX idx_departments_faculty ON departments(faculty_id);

-- ==========================================
-- VERIFICATION QUERIES
-- ==========================================

-- Run these to verify the cleanup worked:
-- SELECT * FROM faculties;
-- SELECT COUNT(*) as faculty_count FROM faculties;
-- SELECT d.name as department, f.name as faculty FROM departments d LEFT JOIN faculties f ON d.faculty_id = f.id;

-- Show all tables with 'faculty' in the name:
-- SHOW TABLES LIKE '%faculty%';
