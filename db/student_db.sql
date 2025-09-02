-- Sample database with records for Student Registration System
-- Import this into MySQL/MariaDB to get pre-populated data

-- Create database (safe if it already exists)
CREATE DATABASE IF NOT EXISTS student_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE student_db;

-- Table schema (matches database.php)
CREATE TABLE IF NOT EXISTS student_records (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  department VARCHAR(50) NOT NULL,
  matric_number VARCHAR(20) NOT NULL UNIQUE,
  registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample rows (dummy data)
INSERT INTO student_records (full_name, email, department, matric_number) VALUES
('John Doe', 'john@example.com', 'Computer Science', 'CSC/2025/001'),
('Jane Smith', 'jane@example.com', 'Mathematics', 'MTH/2025/002'),
('Peter Okoye', 'peter.okoye@example.com', 'Physics', 'PHY/2025/003');