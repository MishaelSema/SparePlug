-- SparePlug Database Cleanup Script
-- Run this in your InfinityFree phpMyAdmin to clean up unused tables and sync categories.

-- 1. DROP UNUSED TABLES
-- These tables are likely residuals from other projects or templates and are not used by SparePlug.
DROP TABLE IF EXISTS customer;
DROP TABLE IF EXISTS employees;
DROP TABLE IF EXISTS jobs;
DROP TABLE IF EXISTS subscription_plans;
DROP TABLE IF EXISTS vehicle_makes;
DROP TABLE IF EXISTS vehicle_models;
DROP TABLE IF EXISTS analytics;

-- 2. SYNCHRONIZE CATEGORIES
-- Ensure 'image_url' column exists
ALTER TABLE categories ADD COLUMN IF NOT EXISTS image_url VARCHAR(255) AFTER icon;

-- Remove extra categories and ensure only the core 6 are present to match report/local.
TRUNCATE TABLE categories;

INSERT INTO categories (name, slug, description, icon, image_url) VALUES
('Engine Parts', 'engine-parts', 'Engine components and accessories', 'engine', 'https://images.unsplash.com/photo-1486262715619-01b8c247a323?auto=format&fit=crop&w=600&q=80'),
('Brake System', 'brake-system', 'Brake pads, discs, and related parts', 'brake', 'https://images.unsplash.com/photo-1620803135805-4e76a6e191b4?auto=format&fit=crop&w=600&q=80'),
('Suspension', 'suspension', 'Shocks, struts, and suspension components', 'suspension', 'https://images.unsplash.com/photo-1563720223185-11003d516905?auto=format&fit=crop&w=600&q=80'),
('Electrical', 'electrical', 'Batteries, alternators, and electrical parts', 'electrical', 'https://images.unsplash.com/photo-1510348061851-d34360e7f78c?auto=format&fit=crop&w=600&q=80'),
('Body Parts', 'body-parts', 'Bumpers, mirrors, and body components', 'body', 'https://images.unsplash.com/photo-1503376763036-066120622c74?auto=format&fit=crop&w=600&q=80'),
('Interior', 'interior', 'Seats, dashboards, and interior accessories', 'interior', 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=600&q=80');

-- Note: 'analytics' and 'subscriptions' are kept as they are part of the core SparePlug architecture.
