-- Create staff table if it doesn't exist
CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    title VARCHAR(20) NOT NULL,
    academic_title VARCHAR(50) DEFAULT NULL,
    qualification TEXT,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    department_id INT,
    position VARCHAR(100) NOT NULL,
    bio TEXT,
    image_url VARCHAR(255) DEFAULT 'default_doctor.jpg',
    is_teaching BOOLEAN DEFAULT TRUE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(department_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample staff data
INSERT INTO staff (first_name, last_name, title, academic_title, qualification, email, phone, department_id, position, bio, image_url, is_teaching) VALUES
-- Medical Doctors
('Michael', 'Johnson', 'Dr.', 'MD', 'MBBS, MD (Internal Medicine)', 'michael.johnson@zanvarsity.edu', '+255712345678', 1, 'Senior Medical Officer', 'Dr. Michael Johnson is a seasoned medical professional with over 15 years of experience in internal medicine. He specializes in tropical diseases and has published numerous research papers in international journals.', 'dr_michael_johnson.jpg', FALSE),

('Grace', 'Wangari', 'Dr.', 'PhD', 'MBChB, MMED (Pediatrics), PhD (Public Health)', 'grace.wangari@zanvarsity.edu', '+255712345679', 1, 'Head of Pediatrics', 'Dr. Grace Wangari is a pediatric specialist with extensive experience in child healthcare. She is passionate about community health and has led several public health initiatives in East Africa.', 'dr_grace_wangari.jpg', TRUE),

('Ali', 'Ibrahim', 'Dr.', 'MD', 'MBBS, MD (Cardiology)', 'ali.ibrahim@zanvarsity.edu', '+255712345680', 1, 'Cardiologist', 'Dr. Ali Ibrahim is a renowned cardiologist with special interest in preventive cardiology. He has been instrumental in setting up the cardiac care unit at our university hospital.', 'dr_ali_ibrahim.jpg', TRUE),

-- Academic Staff
('Sarah', 'Mohammed', 'Prof.', 'PhD', 'BSc, MSc, PhD (Computer Science)', 'sarah.mohammed@zanvarsity.edu', '+255712345681', 1, 'Professor of Computer Science', 'Professor Sarah Mohammed is an expert in artificial intelligence and machine learning. She has over 20 years of teaching and research experience in prestigious institutions worldwide.', 'prof_sarah_mohammed.jpg', TRUE),

('James', 'Kamau', 'Dr.', 'PhD', 'BSc, MSc, PhD (Business Administration)', 'james.kamau@zanvarsity.edu', '+255712345682', 2, 'Associate Professor', 'Dr. James Kamau specializes in strategic management and organizational behavior. He consults for several multinational corporations in East Africa.', 'dr_james_kamau.jpg', TRUE),

-- Administrative Staff
('Amina', 'Rashid', 'Ms.', 'MBA', 'BBA, MBA (Human Resource Management)', 'amina.rashid@zanvarsity.edu', '+255712345683', 3, 'HR Manager', 'Amina Rashid heads the Human Resources department with over 10 years of experience in academic administration and staff development.', 'amina_rashid.jpg', FALSE),

('David', 'Omondi', 'Mr.', 'MSc', 'BSc, MSc (Finance)', 'david.omondi@zanvarsity.edu', '+255712345684', 4, 'Finance Director', 'David Omondi oversees the financial operations of the university, bringing in 12 years of experience in financial management in the education sector.', 'david_omondi.jpg', FALSE);

-- Update the users table to link with staff where applicable
-- This assumes you have a staff_id column in the users table
-- If not, you would need to add it first:
-- ALTER TABLE users ADD COLUMN staff_id INT NULL AFTER user_id;
-- ALTER TABLE users ADD FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE SET NULL;

-- Then update the users table to link with staff
-- UPDATE users u
-- JOIN staff s ON u.email = s.email
-- SET u.staff_id = s.id;
