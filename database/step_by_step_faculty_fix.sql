-- Step-by-Step Faculty Table Fix
-- Run each section separately to avoid errors

-- ==========================================
-- STEP 1: Check what you currently have
-- ==========================================
-- Run these queries first to see your current situation:

-- SHOW TABLES LIKE '%faculty%';
-- DESCRIBE faculties;

-- ==========================================
-- STEP 2: Add missing columns to existing faculties table
-- ==========================================

-- Add code column if it doesn't exist
ALTER TABLE faculties ADD COLUMN code VARCHAR(20) NULL AFTER name;

-- Add description column if it doesn't exist  
ALTER TABLE faculties ADD COLUMN description TEXT NULL AFTER code;

-- Add dean_id column if it doesn't exist
ALTER TABLE faculties ADD COLUMN dean_id INT NULL AFTER description;

-- Add established_year column if it doesn't exist
ALTER TABLE faculties ADD COLUMN established_year YEAR NULL AFTER dean_id;

-- Add contact_email column if it doesn't exist
ALTER TABLE faculties ADD COLUMN contact_email VARCHAR(100) NULL AFTER established_year;

-- Add contact_phone column if it doesn't exist
ALTER TABLE faculties ADD COLUMN contact_phone VARCHAR(20) NULL AFTER contact_email;

-- Add building column if it doesn't exist
ALTER TABLE faculties ADD COLUMN building VARCHAR(100) NULL AFTER contact_phone;

-- Add is_active column if it doesn't exist
ALTER TABLE faculties ADD COLUMN is_active BOOLEAN DEFAULT TRUE AFTER building;

-- ==========================================
-- STEP 3: Insert standard faculty data
-- ==========================================

-- Add type column to distinguish faculties from institutes
ALTER TABLE faculties ADD COLUMN type ENUM('faculty', 'institute') DEFAULT 'faculty' AFTER code;

INSERT IGNORE INTO faculties (name, code, type, description, established_year, is_active) VALUES
-- Faculties (have departments)
('Faculty of Business Administration', 'FBA', 'faculty', 'Faculty of Business Administration', 2010, TRUE),
('Faculty of Law and Shariah', 'FLS', 'faculty', 'Faculty of Law and Shariah', 2010, TRUE),
('Faculty of Arts and Social Sciences', 'FASS', 'faculty', 'Faculty of Arts and Social Sciences', 2010, TRUE),
('Faculty of Engineering', 'FOE', 'faculty', 'Faculty of Engineering', 2010, TRUE),
('Faculty of Health and Allied Sciences', 'FOHAS', 'faculty', 'Faculty of Health and Allied Sciences', 2012, TRUE),
('Faculty of Science', 'FOS', 'faculty', 'Faculty of Science', 2015, TRUE),

-- Institutes (no departments)
('Institute of Postgraduate Studies and Research', 'IPGSR', 'institute', 'Institute of Postgraduate Studies and Research', 2018, TRUE),
('Institute of Islamic Banking and Finance', 'IIBF', 'institute', 'Institute of Islamic Banking and Finance', 2020, TRUE),
('Institute of Continuing Education', 'ICE', 'institute', 'Institute of Continuing Education', 2020, TRUE);

-- ==========================================
-- STEP 4: Update existing records with codes (if any exist without codes)
-- ==========================================

UPDATE faculties SET code = 'FBA' WHERE name LIKE '%Business Administration%' AND code IS NULL;
UPDATE faculties SET code = 'FLS' WHERE name LIKE '%Law and Shariah%' AND code IS NULL;
UPDATE faculties SET code = 'FASS' WHERE name LIKE '%Arts and Social Sciences%' AND code IS NULL;
UPDATE faculties SET code = 'FOE' WHERE name LIKE '%Engineering%' AND code IS NULL;
UPDATE faculties SET code = 'FOHAS' WHERE name LIKE '%Health and Allied Sciences%' AND code IS NULL;
UPDATE faculties SET code = 'FOS' WHERE name LIKE '%Science%' AND code IS NULL;
UPDATE faculties SET code = 'IPGSR' WHERE name LIKE '%Postgraduate Studies%' AND code IS NULL;
UPDATE faculties SET code = 'IIBF' WHERE name LIKE '%Islamic Banking%' AND code IS NULL;
UPDATE faculties SET code = 'ICE' WHERE name LIKE '%Continuing Education%' AND code IS NULL;

-- ==========================================
-- STEP 5: Add unique constraints
-- ==========================================

-- Add unique constraint on name (ignore error if already exists)
-- ALTER TABLE faculties ADD CONSTRAINT unique_faculty_name UNIQUE (name);

-- Add unique constraint on code (ignore error if already exists)
-- ALTER TABLE faculties ADD CONSTRAINT unique_faculty_code UNIQUE (code);

-- ==========================================
-- STEP 6: Verify the results
-- ==========================================

-- Run this to check your faculties table:
-- SELECT * FROM faculties;

-- ==========================================
-- STEP 7: Clean up old tables (ONLY AFTER VERIFICATION)
-- ==========================================

-- ==========================================
-- STEP 8: Update departments to link only to faculties (not institutes)
-- ==========================================

-- Update departments table to link only to faculties
UPDATE departments d
JOIN faculties f ON (
    (d.name LIKE '%Business%' AND f.code = 'FBA' AND f.type = 'faculty') OR
    (d.name LIKE '%Law%' AND f.code = 'FLS' AND f.type = 'faculty') OR
    (d.name LIKE '%Arts%' AND f.code = 'FASS' AND f.type = 'faculty') OR
    (d.name LIKE '%Engineering%' AND f.code = 'FOE' AND f.type = 'faculty') OR
    (d.name LIKE '%Health%' AND f.code = 'FOHAS' AND f.type = 'faculty') OR
    (d.name LIKE '%Science%' AND f.code = 'FOS' AND f.type = 'faculty')
)
SET d.faculty_id = f.id
WHERE f.type = 'faculty';

-- ==========================================
-- STEP 9: Create institutes table for non-departmental institutes
-- ==========================================

CREATE TABLE IF NOT EXISTS institutes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    code VARCHAR(20) NOT NULL UNIQUE,
    description TEXT,
    director_id INT NULL,
    established_year YEAR NULL,
    contact_email VARCHAR(100) NULL,
    contact_phone VARCHAR(20) NULL,
    building VARCHAR(100) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (director_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Migrate institutes from faculties table to institutes table
INSERT IGNORE INTO institutes (name, code, description, established_year, is_active, created_at)
SELECT name, code, description, established_year, is_active, created_at
FROM faculties 
WHERE type = 'institute';

-- ==========================================
-- STEP 10: Clean up (OPTIONAL - only after verification)
-- ==========================================

-- Remove institutes from faculties table (only run after verification)
-- DELETE FROM faculties WHERE type = 'institute';

-- Drop old faculty tables (only run after verification)
-- DROP TABLE IF EXISTS faculty;
-- DROP TABLE IF EXISTS faculty_tbl;
