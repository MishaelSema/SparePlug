-- Authentication System Schema Updates
-- Run this to add authentication features to existing database

USE spareplug_db;

-- Add authentication fields to users table
ALTER TABLE users 
ADD COLUMN phone_verified BOOLEAN DEFAULT FALSE AFTER phone,
ADD COLUMN phone_verification_code VARCHAR(10) NULL AFTER phone_verified,
ADD COLUMN phone_verification_expires TIMESTAMP NULL AFTER phone_verification_code,
ADD COLUMN two_factor_enabled BOOLEAN DEFAULT FALSE AFTER is_verified,
ADD COLUMN two_factor_secret VARCHAR(255) NULL AFTER two_factor_enabled,
ADD COLUMN google_id VARCHAR(255) NULL AFTER two_factor_secret,
ADD COLUMN password_reset_token VARCHAR(255) NULL AFTER google_id,
ADD COLUMN password_reset_expires TIMESTAMP NULL AFTER password_reset_token,
ADD COLUMN last_password_change TIMESTAMP NULL AFTER password_reset_expires,
ADD COLUMN email_verification_token VARCHAR(255) NULL AFTER verification_token,
ADD COLUMN email_verification_expires TIMESTAMP NULL AFTER email_verification_token,
ADD INDEX idx_email_verification (email_verification_token),
ADD INDEX idx_password_reset (password_reset_token),
ADD INDEX idx_google_id (google_id);

-- Create password_resets table for tracking password reset attempts
CREATE TABLE IF NOT EXISTS password_resets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    used BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_expires (expires_at)
);

-- Create social_logins table for tracking social authentication
CREATE TABLE IF NOT EXISTS social_logins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    provider ENUM('google') NOT NULL,
    provider_id VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_provider (provider, provider_id)
);

-- Create two_factor_codes table for storing temporary 2FA codes
CREATE TABLE IF NOT EXISTS two_factor_codes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    code VARCHAR(10) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_expires (user_id, expires_at)
);

-- Create email_verifications table for tracking email verification
CREATE TABLE IF NOT EXISTS email_verifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    verified BOOLEAN DEFAULT FALSE,
    verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token)
);

-- Create phone_verifications table for tracking phone verification
CREATE TABLE IF NOT EXISTS phone_verifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    code VARCHAR(10) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    verified BOOLEAN DEFAULT FALSE,
    verified_at TIMESTAMP NULL,
    attempts INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_phone_code (phone, code),
    INDEX idx_expires (expires_at)
);

-- Create login_logs table for security tracking
CREATE TABLE IF NOT EXISTS login_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    email VARCHAR(255),
    ip_address VARCHAR(45),
    user_agent TEXT,
    success BOOLEAN DEFAULT FALSE,
    failure_reason VARCHAR(255),
    two_factor_used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_created (user_id, created_at),
    INDEX idx_email (email),
    INDEX idx_ip (ip_address)
);

