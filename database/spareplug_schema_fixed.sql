-- SparePlug Database Schema (Corrected)
-- Created by: Ekom Mishael Sema
-- For HND Defense Project

CREATE DATABASE IF NOT EXISTS spareplug_db;
USE spareplug_db;

-- Users table (both buyers and vendors)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    user_type ENUM('buyer', 'vendor', 'admin') NOT NULL DEFAULT 'buyer',
    is_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    status ENUM('active', 'suspended', 'pending') DEFAULT 'active'
);

-- Vendor profiles
CREATE TABLE vendor_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    business_name VARCHAR(255) NOT NULL,
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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Categories
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    parent_id INT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Vehicle makes and models
CREATE TABLE vehicle_makes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    logo VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE vehicle_models (
    id INT PRIMARY KEY AUTO_INCREMENT,
    make_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    year_from INT,
    year_to INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (make_id) REFERENCES vehicle_makes(id) ON DELETE CASCADE
);

-- Products
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vendor_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
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
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);

-- Product inquiries
CREATE TABLE product_inquiries (
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

-- Reviews
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    buyer_id INT NOT NULL,
    vendor_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    title VARCHAR(255),
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

-- Subscriptions
CREATE TABLE subscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vendor_id INT NOT NULL,
    plan_type ENUM('free', 'basic', 'premium') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'XAF',
    billing_cycle ENUM('monthly', 'annual') NOT NULL,
    status ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
    starts_at TIMESTAMP NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    payment_reference VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES vendor_profiles(id) ON DELETE CASCADE
);

-- Analytics data
CREATE TABLE analytics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vendor_id INT NOT NULL,
    metric_type VARCHAR(50) NOT NULL,
    metric_value DECIMAL(10,2) NOT NULL,
    date_recorded DATE NOT NULL,
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES vendor_profiles(id) ON DELETE CASCADE
);

-- Insert sample data
INSERT INTO categories (name, slug, description, icon) VALUES
('Engine Parts', 'engine-parts', 'Engine components and accessories', 'engine'),
('Brake System', 'brake-system', 'Brake pads, discs, and related parts', 'brake'),
('Suspension', 'suspension', 'Shocks, struts, and suspension components', 'suspension'),
('Electrical', 'electrical', 'Batteries, alternators, and electrical parts', 'electrical'),
('Body Parts', 'body-parts', 'Bumpers, mirrors, and body components', 'body'),
('Interior', 'interior', 'Seats, dashboards, and interior accessories', 'interior');

INSERT INTO vehicle_makes (name) VALUES
('Toyota'), ('Honda'), ('Nissan'), ('Ford'), ('BMW'), ('Mercedes'), ('Hyundai'), ('Kia'), ('Peugeot'), ('Renault');

-- Insert sample models for Toyota
INSERT INTO vehicle_models (make_id, name, year_from, year_to) VALUES
(1, 'Corolla', 2000, 2024),
(1, 'Camry', 2000, 2024),
(1, 'RAV4', 2000, 2024),
(1, 'Hilux', 2000, 2024),
(1, 'Prius', 2005, 2024);

-- Insert sample models for Honda
INSERT INTO vehicle_models (make_id, name, year_from, year_to) VALUES
(2, 'Civic', 2000, 2024),
(2, 'Accord', 2000, 2024),
(2, 'CR-V', 2000, 2024),
(2, 'Pilot', 2000, 2024);



