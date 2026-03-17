-- Seed Data for Betonio API
-- Insert sample student records into the users table

USE ipt_db;

-- Clear existing data (optional - comment out if you want to preserve existing records)
-- TRUNCATE TABLE users;

-- Insert sample student records
INSERT INTO users (firstname, lastname, contact, school_idnum, email) VALUES
('Juan', 'Dela Cruz', '09123456789', 'STU001', 'juan.delacruz@school.edu'),
('Maria', 'Garcia', '09234567890', 'STU002', 'maria.garcia@school.edu'),
('Miguel', 'Santos', '09345678901', 'STU003', 'miguel.santos@school.edu'),
('Ana', 'Reyes', '09456789012', 'STU004', 'ana.reyes@school.edu'),
('Carlos', 'Morales', '09567890123', 'STU005', 'carlos.morales@school.edu'),
('Rosa', 'Fernandez', '09678901234', 'STU006', 'rosa.fernandez@school.edu'),
('Luis', 'Mendoza', '09789012345', 'STU007', 'luis.mendoza@school.edu'),
('Sofia', 'Ramos', '09890123456', 'STU008', 'sofia.ramos@school.edu'),
('Pedro', 'Cruz', '09901234567', 'STU009', 'pedro.cruz@school.edu'),
('Isabella', 'Flores', '09012345678', 'STU010', 'isabella.flores@school.edu');

-- Verify inserted data
SELECT * FROM users;
