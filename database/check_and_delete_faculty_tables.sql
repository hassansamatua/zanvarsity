-- Check and Delete All Faculty Tables from zanvarsity_db
-- This script will show you exactly what exists and then delete it

-- ==========================================
-- STEP 1: Check what faculty tables exist in zanvarsity_db
-- ==========================================

USE zanvarsity_db;

-- Show all tables that contain 'faculty' in the name
SELECT TABLE_NAME 
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = 'zanvarsity_db' 
AND TABLE_NAME LIKE '%faculty%';

-- Show all tables that contain 'faculties' in the name  
SELECT TABLE_NAME 
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = 'zanvarsity_db' 
AND TABLE_NAME LIKE '%faculties%';

-- Show ALL tables in the database to see the full picture
SHOW TABLES;

-- ==========================================
-- STEP 2: Delete all faculty-related tables
-- ==========================================

-- Drop all possible faculty table variations
DROP TABLE IF EXISTS faculty;
DROP TABLE IF EXISTS faculties; 
DROP TABLE IF EXISTS faculty_tbl;
DROP TABLE IF EXISTS faculty_table;
DROP TABLE IF EXISTS faculty_content;
DROP TABLE IF EXISTS faculty_data;
DROP TABLE IF EXISTS tbl_faculty;
DROP TABLE IF EXISTS tbl_faculties;

-- ==========================================
-- STEP 3: Verify deletion
-- ==========================================

-- Check that no faculty tables remain
SELECT TABLE_NAME 
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = 'zanvarsity_db' 
AND (TABLE_NAME LIKE '%faculty%' OR TABLE_NAME LIKE '%faculties%');

-- Show remaining tables
SHOW TABLES;

-- Success message
SELECT 'All faculty tables have been deleted from zanvarsity_db' as Result;
