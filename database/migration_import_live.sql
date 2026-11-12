-- =====================================================
-- IMPORT LIVE DATABASE ONLY
-- Follow these steps exactly
-- =====================================================

-- STEP 1: Clear database (run in SQL tab)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS email_verifications;
DROP TABLE IF EXISTS login_logs;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS product_inquiries;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS social_logins;
DROP TABLE IF EXISTS subscriptions;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS vehicle_makes;
DROP TABLE IF EXISTS vendor_profiles;
SET FOREIGN_KEY_CHECKS = 1;

-- STEP 2: Import your LIVE database file
-- Go to Import tab in phpMyAdmin
-- Choose: if0_40751516_spareplug.sql
-- Click Go

-- STEP 3: After import, run these fixes (if needed)
-- ALTER TABLE users MODIFY status ENUM('active', 'suspended', 'pending', 'inactive') DEFAULT 'active';
-- UPDATE users SET status = 'active' WHERE status = 'inactive';
