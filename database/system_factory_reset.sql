-- SparePlug System Factory Reset Script
-- WARNING: This will permanently delete all Products, Vendors, Reviews, and Users (except Admins).

-- 1. DISABLE FOREIGN KEY CHECKS
SET FOREIGN_KEY_CHECKS = 0;

-- 2. CLEAR TRANSACTIONAL DATA
TRUNCATE TABLE reviews;
TRUNCATE TABLE product_inquiries;
TRUNCATE TABLE notifications;
TRUNCATE TABLE login_logs;
TRUNCATE TABLE social_logins;
TRUNCATE TABLE password_resets;
TRUNCATE TABLE analytics;

-- 3. CLEAR CORE DATA
TRUNCATE TABLE products;
TRUNCATE TABLE vendor_profiles;

-- 4. CLEAR USERS (Except Admins)
-- We keep any user whose type is 'admin' so you don't get locked out.
DELETE FROM users WHERE user_type != 'admin';

-- 5. RESET AUTO-INCREMENT COUNTERS (Optional but cleaner)
ALTER TABLE products AUTO_INCREMENT = 1;
ALTER TABLE vendor_profiles AUTO_INCREMENT = 1;
ALTER TABLE users AUTO_INCREMENT = 2; -- Assumes Admin is ID 1

-- 6. RE-ENABLE FOREIGN KEY CHECKS
SET FOREIGN_KEY_CHECKS = 1;

-- 7. CATEGORY SYNC (Optional: Ensures you have the clean 6 categories)
TRUNCATE TABLE categories;
INSERT INTO categories (name, slug, description, icon, image_url) VALUES
('Engine Parts', 'engine-parts', 'Engine components and accessories', 'engine', 'https://images.unsplash.com/photo-1486262715619-01b8c247a323?auto=format&fit=crop&w=600&q=80'),
('Brake System', 'brake-system', 'Brake pads, discs, and related parts', 'brake', 'https://images.unsplash.com/photo-1620803135805-4e76a6e191b4?auto=format&fit=crop&w=600&q=80'),
('Suspension', 'suspension', 'Shocks, struts, and suspension components', 'suspension', 'https://images.unsplash.com/photo-1563720223185-11003d516905?auto=format&fit=crop&w=600&q=80'),
('Electrical', 'electrical', 'Batteries, alternators, and electrical parts', 'electrical', 'https://images.unsplash.com/photo-1510348061851-d34360e7f78c?auto=format&fit=crop&w=600&q=80'),
('Body Parts', 'body-parts', 'Bumpers, mirrors, and body components', 'body', 'https://images.unsplash.com/photo-1503376763036-066120622c74?auto=format&fit=crop&w=600&q=80'),
('Interior', 'interior', 'Seats, dashboards, and interior accessories', 'interior', 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=600&q=80');

-- END OF RESET
