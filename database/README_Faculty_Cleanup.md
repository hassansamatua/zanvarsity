# Faculty Tables Cleanup Instructions

## Problem
You have multiple faculty-related tables (`faculty`, `faculties`, `faculty_tbl`) which is causing confusion and potential data inconsistency.

## Solution
I've created a comprehensive cleanup solution with the following files:

### 1. `cleanup_faculty_tables.sql`
This script will:
- Create a standardized `faculties` table
- Migrate data from existing faculty tables
- Update related tables to use the new structure
- Provide verification queries

### 2. `update_user_roles.sql` (Updated)
This script will:
- Add faculty column to users table
- Create faculty_content table for dean content management
- Insert default content for all faculties

## Step-by-Step Instructions

### Step 1: Backup Your Database
```sql
-- Create a backup before running any scripts
mysqldump -u your_username -p zanvarsity > zanvarsity_backup_$(date +%Y%m%d).sql
```

### Step 2: Check Current Faculty Tables
Run this query to see what faculty tables you currently have:
```sql
SHOW TABLES LIKE '%faculty%';
```

### Step 3: Examine Table Structures
For each table found, check its structure:
```sql
DESCRIBE faculty;
DESCRIBE faculties;
DESCRIBE faculty_tbl;
```

### Step 4: Run Cleanup Script
Execute the cleanup script:
```sql
SOURCE /path/to/cleanup_faculty_tables.sql;
```

### Step 5: Run User Roles Update
Execute the user roles update:
```sql
SOURCE /path/to/update_user_roles.sql;
```

### Step 6: Verify Results
```sql
-- Check the new faculties table
SELECT * FROM faculties;

-- Check faculty content
SELECT * FROM faculty_content LIMIT 10;

-- Verify relationships
SELECT d.name as department, f.name as faculty 
FROM departments d 
LEFT JOIN faculties f ON d.faculty_id = f.id;
```

### Step 7: Clean Up Old Tables (ONLY AFTER VERIFICATION)
Once you've verified all data is properly migrated:
```sql
DROP TABLE IF EXISTS faculty;
DROP TABLE IF EXISTS faculty_tbl;
-- Keep only the 'faculties' table
```

## Final Table Structure

After cleanup, you should have:

### `faculties` table (KEEP THIS ONE)
- Standardized faculty information
- Uses codes: ENG, SCI, ARTS, BUS, MED, LAW, EDU
- Proper relationships with other tables

### `faculty_content` table (NEW)
- Stores dean-managed content (welcome, vision, mission, about)
- Links to faculties table via faculty_code

## Tables to Delete (After Migration)
- `faculty` (if exists)
- `faculty_tbl` (if exists)

## Tables to Keep
- `faculties` (standardized)
- `faculty_content` (new for dean content)

## Verification Checklist
- [ ] All faculty data migrated to `faculties` table
- [ ] Departments linked to faculties via `faculty_id`
- [ ] Staff linked to faculties (if staff table exists)
- [ ] Users table has `faculty` column for dean assignments
- [ ] Faculty content populated for all faculties
- [ ] Old faculty tables can be safely dropped

## Need Help?
If you encounter any issues:
1. Check the error messages carefully
2. Verify your table names match the script expectations
3. Make sure you have proper database permissions
4. Don't drop old tables until you've verified the migration worked
