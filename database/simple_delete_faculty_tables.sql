-- Simple Faculty Tables Deletion
-- Only drops tables that exist, ignores errors for non-existent tables

-- Check what faculty tables currently exist
SHOW TABLES LIKE '%faculty%';

-- Drop the faculty tables (ignore errors if they don't exist)
DROP TABLE IF EXISTS faculties;
DROP TABLE IF EXISTS faculty;
DROP TABLE IF EXISTS faculty_tbl;
DROP TABLE IF EXISTS faculty_content;

-- Verify they're gone
SHOW TABLES LIKE '%faculty%';

-- Success message
SELECT 'Faculty tables deleted successfully' as Status;
