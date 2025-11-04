-- Force Delete Faculty Tables by Removing Constraints First
-- This script will find and remove all foreign key constraints, then delete the tables

USE zanvarsity_db;

-- ==========================================
-- STEP 1: Find all foreign key constraints that reference faculty tables
-- ==========================================

-- Show all foreign key constraints in the database
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE CONSTRAINT_SCHEMA = 'zanvarsity_db'
AND REFERENCED_TABLE_NAME IN ('faculty', 'faculties', 'faculty_tbl')
ORDER BY TABLE_NAME;

-- ==========================================
-- STEP 2: Disable foreign key checks temporarily
-- ==========================================

SET FOREIGN_KEY_CHECKS = 0;

-- ==========================================
-- STEP 3: Drop all faculty tables
-- ==========================================

DROP TABLE IF EXISTS faculty;
DROP TABLE IF EXISTS faculties; 
DROP TABLE IF EXISTS faculty_tbl;
DROP TABLE IF EXISTS faculty_table;
DROP TABLE IF EXISTS faculty_content;
DROP TABLE IF EXISTS faculty_data;
DROP TABLE IF EXISTS tbl_faculty;
DROP TABLE IF EXISTS tbl_faculties;

-- ==========================================
-- STEP 4: Re-enable foreign key checks
-- ==========================================

SET FOREIGN_KEY_CHECKS = 1;

-- ==========================================
-- STEP 5: Clean up any orphaned foreign key columns
-- ==========================================

-- Remove faculty_id column from departments if it exists
ALTER TABLE departments DROP COLUMN IF EXISTS faculty_id;

-- Remove faculty_id column from staff if it exists  
ALTER TABLE staff DROP COLUMN IF EXISTS faculty_id;

-- Remove faculty column from users if it exists
ALTER TABLE users DROP COLUMN IF EXISTS faculty;

-- ==========================================
-- STEP 6: Verify deletion
-- ==========================================

-- Check that no faculty tables remain
SELECT TABLE_NAME 
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = 'zanvarsity_db' 
AND (TABLE_NAME LIKE '%faculty%' OR TABLE_NAME LIKE '%faculties%');

-- Show remaining tables
SHOW TABLES;

-- Success message
SELECT 'All faculty tables and constraints have been removed' as Result;
