-- Migration: Update users table to store student information
-- Run this script against ipt_db to apply the schema changes

USE ipt_db;

-- Step 1: Drop the old users table (this removes existing data)
DROP TABLE IF EXISTS users;

-- Step 2: Recreate users table with student info fields
CREATE TABLE users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    firstname   VARCHAR(100) NOT NULL,
    lastname    VARCHAR(100) NOT NULL,
    contact     VARCHAR(20)  NOT NULL,
    school_idnum VARCHAR(50) NOT NULL UNIQUE,
    email       VARCHAR(255) NOT NULL UNIQUE,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Step 3: Seed sample student records
INSERT INTO users (firstname, lastname, contact, school_idnum, email) VALUES
    ('Juan',   'Dela Cruz', '09171234567', '2021-00001', 'juan.delacruz@school.edu'),
    ('Maria',  'Santos',    '09181234567', '2021-00002', 'maria.santos@school.edu'),
    ('Carlos', 'Reyes',     '09191234567', '2022-00003', 'carlos.reyes@school.edu'),
    ('Ana',    'Garcia',    '09201234567', '2022-00004', 'ana.garcia@school.edu'),
    ('Pedro',  'Lim',       '09211234567', '2023-00005', 'pedro.lim@school.edu');
