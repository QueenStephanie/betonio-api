
CREATE DATABASE IF NOT EXISTS ipt_db;


USE ipt_db;


CREATE TABLE IF NOT EXISTS users (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    firstname    VARCHAR(100) NOT NULL,
    lastname     VARCHAR(100) NOT NULL,
    contact      VARCHAR(20)  NOT NULL,
    school_idnum VARCHAR(50)  NOT NULL UNIQUE,
    email        VARCHAR(255) NOT NULL UNIQUE,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
