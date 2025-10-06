-- Add faculty_id column to staff table
ALTER TABLE staff
ADD COLUMN faculty_id INT NULL AFTER department_id,
ADD CONSTRAINT fk_staff_faculty
FOREIGN KEY (faculty_id) REFERENCES faculty(id)
ON DELETE SET NULL;

-- Update staff records with faculty IDs based on department_id
-- Assuming department_id 1 is Medicine, 2 is Science & Technology, etc.
-- Adjust these mappings based on your actual department IDs
UPDATE staff 
SET faculty_id = 1 
WHERE department_id = 1; -- Medicine

UPDATE staff 
SET faculty_id = 2 
WHERE department_id = 2; -- Science & Technology

UPDATE staff 
SET faculty_id = 3 
WHERE department_id = 3; -- Business & Economics

UPDATE staff 
SET faculty_id = 4 
WHERE department_id = 4; -- Arts & Social Sciences

-- For any staff without a faculty assignment
UPDATE staff 
SET faculty_id = 1 
WHERE faculty_id IS NULL; -- Default to Medicine if no faculty assigned

-- Optional: Make the faculty_id column NOT NULL after all records have been updated
-- ALTER TABLE staff MODIFY COLUMN faculty_id INT NOT NULL;
