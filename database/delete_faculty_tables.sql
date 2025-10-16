-- Delete Faculty Tables Script
-- This script will drop the faculty-related tables

-- ==========================================
-- WARNING: This will permanently delete all data in these tables!
-- Make sure you have a backup before running this script.
-- ==========================================

-- First, check what tables exist:
-- SHOW TABLES LIKE '%faculty%';
-- SHOW TABLES LIKE '%faculties%';

-- ==========================================
-- STEP 1: Drop foreign key constraints first (if they exist)
-- ==========================================

-- Drop any foreign key constraints that reference these tables
-- You might need to adjust these based on your actual constraint names

-- Drop constraints from departments table if they exist
ALTER TABLE departments DROP FOREIGN KEY IF EXISTS fk_dept_faculty;
ALTER TABLE departments DROP FOREIGN KEY IF EXISTS fk_department_faculty;

-- Drop constraints from staff table if they exist  
ALTER TABLE staff DROP FOREIGN KEY IF EXISTS fk_staff_faculty;

-- Drop constraints from any content tables if they exist
ALTER TABLE academic_content DROP FOREIGN KEY IF EXISTS fk_content_faculty;
ALTER TABLE faculty_content DROP FOREIGN KEY IF EXISTS fk_faculty_content_faculty;

-- ==========================================
-- STEP 2: Drop the tables
-- ==========================================

-- Drop faculties table
DROP TABLE IF EXISTS faculties;

-- Drop faculty table  
DROP TABLE IF EXISTS faculty;

-- Drop faculty_tbl table if it exists
DROP TABLE IF EXISTS faculty_tbl;

-- Drop faculty_content table if it exists (since we're using academic_content now)
DROP TABLE IF EXISTS faculty_content;

-- ==========================================
-- STEP 3: Verification
-- ==========================================

-- Check that the tables are gone:
-- SHOW TABLES LIKE '%faculty%';

-- ==========================================
-- STEP 4: Clean up any remaining references (optional)
-- ==========================================

-- Remove faculty_id column from departments if it exists and is no longer needed
-- ALTER TABLE departments DROP COLUMN IF EXISTS faculty_id;

-- Remove faculty_id column from staff if it exists and is no longer needed  
-- ALTER TABLE staff DROP COLUMN IF EXISTS faculty_id;

-- Remove faculty column from users if it exists and is no longer needed
-- ALTER TABLE users DROP COLUMN IF EXISTS faculty;

-- ==========================================
-- NOTES:
-- ==========================================
-- After running this script, you should:
-- 1. Run the step_by_step_faculty_fix.sql to recreate the proper structure
-- 2. Run the create_content_system_safe.sql to set up the content system
-- 3. Update your application code to use the new structure
