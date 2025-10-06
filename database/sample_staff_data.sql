-- Sample Staff Data for ZAN University
-- This file contains sample data for staff members

-- First, let's ensure we're using the correct database
USE zanvarsity;

-- Insert sample staff members
-- Password for all sample accounts is 'password123' (hashed using bcrypt)
INSERT INTO users (username, password_hash, email, first_name, last_name, role, phone, address, is_active) VALUES
-- Medical Staff (Doctors with title 'DR')
('dr.johnson', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'johnson@zanvarsity.edu', 'Dr. Michael', 'Johnson', 'staff', '+255712345678', '123 University Ave, Zanzibar', TRUE),
('dr.wangari', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'wangari@zanvarsity.edu', 'Dr. Grace', 'Wangari', 'staff', '+255712345679', '456 Health Center Rd, Zanzibar', TRUE),
('dr.ibrahim', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ibrahim@zanvarsity.edu', 'Dr. Ali', 'Ibrahim', 'staff', '+255712345680', '789 Medical Center, Zanzibar', TRUE),

-- Administrative Staff
('sarah.m', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sarah@zanvarsity.edu', 'Sarah', 'Mohammed', 'staff', '+255712345681', '101 Admin Building, Zanzibar', TRUE),
('james.k', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'james@zanvarsity.edu', 'James', 'Kamau', 'staff', '+255712345682', '202 Admin Building, Zanzibar', TRUE);

-- Update the staff members with their titles and departments
-- Note: In a real scenario, you might want to create a separate staff_profiles table for this information
-- For now, we'll add this information to the address field as a temporary solution

-- For the doctors, we'll update their profile information
UPDATE users 
SET 
    address = CONCAT('Medical Department, ', address),
    profile_image = 'doctor_', user_id, '.jpg'
WHERE username LIKE 'dr.%';

-- For other staff, we'll add their department to the address
UPDATE users 
SET 
    address = CONCAT('Administration Department, ', address),
    profile_image = 'staff_', user_id, '.jpg'
WHERE role = 'staff' AND username NOT LIKE 'dr.%';

-- Add some sample profile images (these would need to exist in your filesystem)
-- Note: You would need to create these image files in your assets directory
-- For example: /assets/img/staff/doctor_2.jpg, /assets/img/staff/doctor_3.jpg, etc.

-- Create sample staff detail pages (you would need to create these PHP files)
-- For example: /staff-detail.php?id=2, /staff-detail.php?id=3, etc.

-- Create sample staff listing page with filter for doctors
-- This would be handled by your staff.php page with ?filter=dr parameter
