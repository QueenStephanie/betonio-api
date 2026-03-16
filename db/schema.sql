-- Database Schema for Betonio API
-- Database: ipt_db

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS ipt_db;
USE ipt_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    contact VARCHAR(20) NOT NULL,
    school_idnum VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_school_idnum (school_idnum)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional: Create an index on created_at if you plan to query by date
CREATE INDEX idx_created_at ON users(created_at DESC);
