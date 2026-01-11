-- Migration to add ADMIN user type
-- Run this if you already have an existing database

USE spareplug_db;

-- Modify users table to add 'admin' to user_type ENUM
ALTER TABLE users MODIFY COLUMN user_type ENUM('buyer', 'vendor', 'admin') NOT NULL DEFAULT 'buyer';

-- Insert default admin account (password: admin123)
-- CHANGE THIS PASSWORD in production!
INSERT INTO users (email, password, first_name, last_name, user_type, is_verified, status) VALUES
('admin@spareplug.cm', '$2y$12$Tay/nqztcTh9G9tmWvCM8.8oM0F4Ixao5D.pBivf3px6rCcrSExpW', 'Admin', 'User', 'admin', 1, 'active')
ON DUPLICATE KEY UPDATE user_type='admin';

