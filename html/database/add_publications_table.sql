-- First, let's check what columns actually exist
SET @dbname = DATABASE();
SET @tablename = 'publications';

-- Create a temporary table to store the column information
CREATE TEMPORARY TABLE IF NOT EXISTS temp_columns (
    column_name VARCHAR(100),
    column_type TEXT,
    is_nullable VARCHAR(3),
    column_default TEXT,
    column_comment TEXT
);

-- Get the current table structure
SET @sql = CONCAT('INSERT INTO temp_columns SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?');
PREPARE stmt FROM @sql;
EXECUTE stmt USING @dbname, @tablename;
DEALLOCATE PREPARE stmt;

-- Start building the ALTER TABLE statement
SET @alter_sql = 'ALTER TABLE `publications` ';

-- Add columns that don't exist yet
IF NOT EXISTS (SELECT 1 FROM temp_columns WHERE column_name = 'Author') THEN
    SET @alter_sql = CONCAT(@alter_sql, 'ADD COLUMN `Author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `title`, ');
END IF;

IF NOT EXISTS (SELECT 1 FROM temp_columns WHERE column_name = 'abstract') THEN
    SET @alter_sql = CONCAT(@alter_sql, 'ADD COLUMN `abstract` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, ');
END IF;

IF NOT EXISTS (SELECT 1 FROM temp_columns WHERE column_name = 'datePublished') THEN
    SET @alter_sql = CONCAT(@alter_sql, 'ADD COLUMN `datePublished` date DEFAULT NULL, ');
END IF;

IF NOT EXISTS (SELECT 1 FROM temp_columns WHERE column_name = 'publicationCategory') THEN
    SET @alter_sql = CONCAT(@alter_sql, 'ADD COLUMN `publicationCategory` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT \'Journal Articles\', ');
END IF;

-- Add API-related columns
IF NOT EXISTS (SELECT 1 FROM temp_columns WHERE column_name = 'status_code') THEN
    SET @alter_sql = CONCAT(@alter_sql, 'ADD COLUMN `status_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT \'200\' AFTER `link`, ');
END IF;

IF NOT EXISTS (SELECT 1 FROM temp_columns WHERE column_name = 'status_desc') THEN
    SET @alter_sql = CONCAT(@alter_sql, 'ADD COLUMN `status_desc` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT \'Success\' AFTER `status_code`, ');
END IF;

IF NOT EXISTS (SELECT 1 FROM temp_columns WHERE column_name = 'api_id') THEN
    SET @alter_sql = CONCAT(@alter_sql, 'ADD COLUMN `api_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `status_desc`, ');
END IF;

-- Remove trailing comma and space if we added any columns
IF @alter_sql != 'ALTER TABLE `publications` ' THEN
    SET @alter_sql = LEFT(@alter_sql, LENGTH(@alter_sql) - 2);
    
    -- Execute the ALTER TABLE statement
    PREPARE stmt FROM @alter_sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    
    SELECT 'Table structure updated successfully' AS message;
ELSE
    SELECT 'No changes needed - table structure is already up to date' AS message;
END IF;

-- Add indexes (with error handling)
SET @index_sql = '';

-- Add indexes only if they don't exist
IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'publications' AND INDEX_NAME = 'idx_api_id') THEN
    SET @index_sql = CONCAT(@index_sql, 'ADD UNIQUE INDEX `idx_api_id` (`api_id`), ');
END IF;

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'publications' AND INDEX_NAME = 'idx_date_published') THEN
    SET @index_sql = CONCAT(@index_sql, 'ADD INDEX `idx_date_published` (`datePublished`), ');
END IF;

IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'publications' AND INDEX_NAME = 'idx_publication_category') THEN
    SET @index_sql = CONCAT(@index_sql, 'ADD INDEX `idx_publication_category` (`publicationCategory`), ');
END IF;

-- Execute index creation if needed
IF @index_sql != '' THEN
    SET @index_sql = CONCAT('ALTER TABLE `publications` ', LEFT(@index_sql, LENGTH(@index_sql) - 2));
    PREPARE stmt FROM @index_sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    
    SELECT CONCAT('Added indexes: ', @index_sql) AS message;
END IF;

-- Clean up
DROP TEMPORARY TABLE IF EXISTS temp_columns;

-- Optional: Clean up old columns if they exist and you're sure they're not needed
-- ALTER TABLE `publications`
-- DROP COLUMN IF EXISTS `image_url`,
-- DROP COLUMN IF EXISTS `document_url`,
-- DROP COLUMN IF EXISTS `is_featured`;
