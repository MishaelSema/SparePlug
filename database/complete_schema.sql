-- =====================================================
-- SparePlug - Complete Database Schema
-- For HND Defense Project by Ekom Mishael Sema
-- This file contains ALL tables and sample data needed
-- to run the application on a new PC
-- =====================================================

-- Create database (use existing if already exists)
CREATE DATABASE IF NOT EXISTS spareplug_db;
USE spareplug_db;

-- =====================================================
-- USERS TABLE (buyers, vendors, admin)
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(191) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    user_type ENUM('buyer', 'vendor', 'admin') NOT NULL DEFAULT 'buyer',
    status ENUM('active', 'suspended', 'pending') DEFAULT 'active',
    is_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(191),
    email_verification_token VARCHAR(191) NULL,
    email_verification_expires TIMESTAMP NULL,
    phone_verified BOOLEAN DEFAULT FALSE,
    phone_verification_code VARCHAR(10) NULL,
    phone_verification_expires TIMESTAMP NULL,
    two_factor_enabled BOOLEAN DEFAULT FALSE,
    two_factor_secret VARCHAR(191) NULL,
    google_id VARCHAR(191) NULL,
    password_reset_token VARCHAR(191) NULL,
    password_reset_expires TIMESTAMP NULL,
    last_password_change TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_password_reset (password_reset_token),
    INDEX idx_google_id (google_id)
);

-- =====================================================
-- VENDOR PROFILES TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS vendor_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    business_name VARCHAR(191) NOT NULL,
    business_type ENUM('individual', 'company', 'garage', 'dealer') NOT NULL,
    description TEXT,
    address TEXT,
    city VARCHAR(100),
    region VARCHAR(100),
    operating_hours JSON,
    verification_documents JSON,
    is_verified BOOLEAN DEFAULT FALSE,
    subscription_plan ENUM('free', 'basic', 'premium') DEFAULT 'free',
    subscription_expires TIMESTAMP NULL,
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_sales INT DEFAULT 0,
    logo_url VARCHAR(191) DEFAULT NULL,
    cover_url VARCHAR(191) DEFAULT NULL,
    facebook_url VARCHAR(191) DEFAULT NULL,
    instagram_url VARCHAR(191) DEFAULT NULL,
    twitter_url VARCHAR(191) DEFAULT NULL,
    google_map_url VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =====================================================
-- CATEGORIES TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    image_url VARCHAR(191) DEFAULT NULL,
    parent_id INT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- =====================================================
-- VEHICLE MAKES AND MODELS
-- =====================================================
CREATE TABLE IF NOT EXISTS vehicle_makes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    logo VARCHAR(191),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS vehicle_models (
    id INT PRIMARY KEY AUTO_INCREMENT,
    make_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    year_from INT,
    year_to INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (make_id) REFERENCES vehicle_makes(id) ON DELETE CASCADE
);

-- =====================================================
-- PRODUCTS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vendor_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(191) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'XAF',
    `condition` ENUM('new', 'used', 'refurbished') NOT NULL,
    compatibility JSON,
    images JSON,
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    views_count INT DEFAULT 0,
    inquiries_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES vendor_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    INDEX idx_vendor (vendor_id),
    INDEX idx_category (category_id),
    INDEX idx_featured (is_featured, is_active)
);

-- =====================================================
-- PRODUCT INQUIRIES TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS product_inquiries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    buyer_id INT NOT NULL,
    message TEXT NOT NULL,
    contact_method ENUM('whatsapp', 'call', 'email', 'message') NOT NULL,
    status ENUM('pending', 'responded', 'closed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =====================================================
-- REVIEWS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    buyer_id INT NOT NULL,
    vendor_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    title VARCHAR(191),
    comment TEXT,
    images JSON,
    is_verified BOOLEAN DEFAULT FALSE,
    vendor_response TEXT,
    vendor_response_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (vendor_id) REFERENCES vendor_profiles(id) ON DELETE CASCADE
);

-- =====================================================
-- SUBSCRIPTIONS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS subscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vendor_id INT NOT NULL,
    plan_type ENUM('free', 'basic', 'premium') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'XAF',
    billing_cycle ENUM('monthly', 'annual') NOT NULL,
    status ENUM('active', 'pending', 'expired', 'cancelled') DEFAULT 'pending',
    starts_at TIMESTAMP NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    payment_reference VARCHAR(191),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES vendor_profiles(id) ON DELETE CASCADE
);

-- =====================================================
-- SUBSCRIPTION PLANS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS subscription_plans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    billing_cycle ENUM('monthly', 'annual') DEFAULT 'monthly',
    features JSON,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- ANALYTICS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS analytics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vendor_id INT NOT NULL,
    metric_type VARCHAR(50) NOT NULL,
    metric_value DECIMAL(10,2) NOT NULL,
    date_recorded DATE NOT NULL,
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES vendor_profiles(id) ON DELETE CASCADE
);

-- =====================================================
-- NOTIFICATIONS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_read (user_id, is_read)
);

-- =====================================================
-- AUTHENTICATION TABLES
-- =====================================================
CREATE TABLE IF NOT EXISTS login_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    email VARCHAR(191),
    ip_address VARCHAR(45),
    user_agent TEXT,
    success BOOLEAN DEFAULT FALSE,
    failure_reason VARCHAR(255),
    two_factor_used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_created (user_id, created_at),
    INDEX idx_email (email)
);

CREATE TABLE IF NOT EXISTS social_logins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    provider VARCHAR(50),
    provider_id VARCHAR(191),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS two_factor_codes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    code VARCHAR(10),
    expires_at TIMESTAMP,
    used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_expires (user_id, expires_at)
);

CREATE TABLE IF NOT EXISTS email_verifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    email VARCHAR(191),
    token VARCHAR(191),
    expires_at TIMESTAMP,
    verified BOOLEAN DEFAULT FALSE,
    verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token)
);

CREATE TABLE IF NOT EXISTS phone_verifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    phone VARCHAR(20),
    code VARCHAR(10),
    expires_at TIMESTAMP,
    verified BOOLEAN DEFAULT FALSE,
    verified_at TIMESTAMP NULL,
    attempts INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_phone_code (phone, code)
);

CREATE TABLE IF NOT EXISTS password_resets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(191),
    expires_at TIMESTAMP,
    used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_expires (expires_at)
);

-- =====================================================
-- SAMPLE DATA - DEFAULT ADMIN
-- =====================================================
INSERT INTO users (email, password, first_name, last_name, user_type, is_verified, status) 
VALUES ('admin@spareplug.cm', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'admin', 1, 'active')
ON DUPLICATE KEY UPDATE email=email;

-- =====================================================
-- SAMPLE DATA - CATEGORIES
-- =====================================================
INSERT INTO categories (name, slug, description, icon) VALUES
('Engine Parts', 'engine-parts', 'Engine components and accessories', 'engine'),
('Brake System', 'brake-system', 'Brake pads, discs, and related parts', 'brake'),
('Suspension', 'suspension', 'Shocks, struts, and suspension components', 'suspension'),
('Electrical', 'electrical', 'Batteries, alternators, and electrical parts', 'electrical'),
('Body Parts', 'body-parts', 'Bumpers, mirrors, and body components', 'body'),
('Interior', 'interior', 'Seats, dashboards, and interior accessories', 'interior'),
('Transmission', 'transmission', 'Transmission parts and components', 'transmission'),
('Exhaust System', 'exhaust-system', 'Mufflers, catalytic converters, and exhaust parts', 'exhaust'),
('Cooling System', 'cooling-system', 'Radiators, water pumps, and cooling components', 'cooling')
ON DUPLICATE KEY UPDATE name=name;

-- =====================================================
-- SAMPLE DATA - VEHICLE MAKES
-- =====================================================
INSERT INTO vehicle_makes (name) VALUES
('Toyota'), ('Honda'), ('Nissan'), ('Ford'), ('BMW'), ('Mercedes'), ('Hyundai'), ('Kia'), ('Peugeot'), ('Renault')
ON DUPLICATE KEY UPDATE name=name;

-- =====================================================
-- SAMPLE DATA - VEHICLE MODELS
-- =====================================================
INSERT INTO vehicle_models (make_id, name, year_from, year_to) VALUES
(1, 'Corolla', 2000, 2024),
(1, 'Camry', 2000, 2024),
(1, 'RAV4', 2000, 2024),
(1, 'Hilux', 2000, 2024),
(1, 'Prius', 2005, 2024),
(2, 'Civic', 2000, 2024),
(2, 'Accord', 2000, 2024),
(2, 'CR-V', 2000, 2024),
(2, 'Pilot', 2000, 2024),
(3, 'Altima', 2000, 2024),
(3, 'Sentra', 2000, 2024),
(3, 'Pathfinder', 2000, 2024),
(4, 'Focus', 2000, 2024),
(4, 'Fiesta', 2000, 2024),
(4, 'Explorer', 2000, 2024)
ON DUPLICATE KEY UPDATE name=name;

-- =====================================================
-- SAMPLE DATA - SUBSCRIPTION PLANS
-- =====================================================
INSERT INTO subscription_plans (name, price, billing_cycle, features, is_active) VALUES
('Free', 0, 'monthly', JSON_ARRAY('10 product listings', 'Basic profile', 'Email support'), 1),
('Basic', 15000, 'monthly', JSON_ARRAY('50 product listings', 'Featured products', 'Verified badge', 'Priority support'), 1),
('Premium', 35000, 'monthly', JSON_ARRAY('Unlimited listings', 'Top of search results', 'Analytics dashboard', 'WhatsApp direct link', 'Verified badge', '24/7 support'), 1)
ON DUPLICATE KEY UPDATE name=name;

-- =====================================================
-- COMPLETE - DATABASE READY
-- =====================================================