-- Seed Data for Betonio API
-- Insert sample student records into the users table

USE ipt_db;

-- Clear existing data (optional - comment out if you want to preserve existing records)
-- TRUNCATE TABLE users;

-- Note: the `password` column is required by the schema; we seed a common
-- bcrypt hash for the plaintext `password` to satisfy NOT NULL.
-- Hash used below: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

INSERT INTO users (firstname, lastname, contact, school_idnum, email, password) VALUES
('Juan', 'Dela Cruz', '09123456789', 'STU001', 'juan.delacruz@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Maria', 'Garcia', '09234567890', 'STU002', 'maria.garcia@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Miguel', 'Santos', '09345678901', 'STU003', 'miguel.santos@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Ana', 'Reyes', '09456789012', 'STU004', 'ana.reyes@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Carlos', 'Morales', '09567890123', 'STU005', 'carlos.morales@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Rosa', 'Fernandez', '09678901234', 'STU006', 'rosa.fernandez@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Luis', 'Mendoza', '09789012345', 'STU007', 'luis.mendoza@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Sofia', 'Ramos', '09890123456', 'STU008', 'sofia.ramos@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Pedro', 'Cruz', '09901234567', 'STU009', 'pedro.cruz@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Isabella', 'Flores', '09012345678', 'STU010', 'isabella.flores@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Verify inserted data
SELECT * FROM users;
