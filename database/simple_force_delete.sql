-- Simple Force Delete Faculty Tables
-- Skip the constraint checking and just force delete

USE zanvarsity_db;

-- Disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- Drop all faculty tables
DROP TABLE IF EXISTS faculty;
DROP TABLE IF EXISTS faculties; 
DROP TABLE IF EXISTS faculty_tbl;
DROP TABLE IF EXISTS faculty_table;
DROP TABLE IF EXISTS faculty_content;
DROP TABLE IF EXISTS faculty_data;
DROP TABLE IF EXISTS tbl_faculty;
DROP TABLE IF EXISTS tbl_faculties;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Verify deletion
SHOW TABLES LIKE '%faculty%';

-- Success message
SELECT 'Faculty tables deleted successfully' as Status;
