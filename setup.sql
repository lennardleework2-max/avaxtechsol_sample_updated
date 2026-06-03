-- Database Setup Script for Employee Management System
-- Run this script in Adminer or MySQL client

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS avaxtechsol_sample;
USE avaxtechsol_sample;

-- Create mf_users table
CREATE TABLE IF NOT EXISTS mf_users (
    recid INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create mf_employees table
CREATE TABLE IF NOT EXISTS mf_employees (
    recid INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(20) NOT NULL UNIQUE,
    employee_name VARCHAR(255) NOT NULL,
    salary DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create index for employee_id searches
CREATE INDEX idx_employee_id ON mf_employees(employee_id);
CREATE INDEX idx_employee_name ON mf_employees(employee_name);

-- Insert default admin user (password: password)
-- The hashed password below is for 'password' using PHP's password_hash with PASSWORD_DEFAULT
INSERT INTO mf_users (username, password) VALUES
('admin', '$2y$10$8sWTmDGOwEwlW/DkmKmqfO79lfZYQAw6z4mvyHRaJSXZrJxQhaqZO')
ON DUPLICATE KEY UPDATE username = username;

-- Insert sample employees (optional)
INSERT INTO mf_employees (employee_id, employee_name, salary) VALUES
('EMP-0001', 'John Doe', 50000.00),
('EMP-0002', 'Jane Smith', 55000.00),
('EMP-0003', 'Bob Johnson', 45000.00),
('EMP-0004', 'Alice Williams', 60000.00),
('EMP-0005', 'Charlie Brown', 52000.00)
ON DUPLICATE KEY UPDATE employee_id = employee_id;
